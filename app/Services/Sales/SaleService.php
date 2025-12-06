<?php

namespace App\Services\Sales;

use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SaleService
{
    /**
     * Get sales query for DataTables
     */
    public function getSalesQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Sale::where('tenant_id', $tenantId)
                ->with(['customer', 'soldBy']);
        } catch (Exception $e) {
            Log::error('Error creating sales query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber(int $tenantId): string
    {
        $prefix = 'INV';
        $date = date('Ymd');

        $lastSale = Sale::where('tenant_id', $tenantId)
            ->whereDate('created_at', today())
            ->whereNotNull('invoice_number')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale && $lastSale->invoice_number) {
            $lastNumber = (int) substr($lastSale->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new sale
     */
    public function createSale(array $data, array $items): Sale
    {
        DB::beginTransaction();

        try {
            $tenantId = $this->getTenantId();

            // Create or find customer if customer_name and customer_phone are provided
            if (!empty($data['customer_name']) && !empty($data['customer_phone'])) {
                $customer = $this->createOrFindCustomer($tenantId, $data['customer_name'], $data['customer_phone']);
                $data['customer_id'] = $customer->id;
            }

            // Generate invoice number if not provided
            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = $this->generateInvoiceNumber($tenantId);
            }

            // Calculate totals
            $totals = $this->calculateTotals($items, $data['discount_amount'] ?? 0, $data['tax_amount'] ?? 0);

            // Create sale
            $sale = Sale::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'subtotal' => $totals['subtotal'],
                'total_amount' => $totals['total_amount'],
                'due_amount' => $totals['total_amount'] - ($data['paid_amount'] ?? 0),
                'payment_status' => $this->determinePaymentStatus($totals['total_amount'], $data['paid_amount'] ?? 0),
                'sold_by' => auth()->id(),
            ]));

            // Create sale items
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'imei_id' => $item['imei_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_price' => $item['total_price'],
                    'warranty_months' => $item['warranty_months'] ?? 0,
                    'warranty_expiry_date' => $item['warranty_expiry_date'] ?? null,
                ]);
            }

            // Create EMI plan if sale type is EMI
            if (isset($data['sale_type']) && $data['sale_type'] === 'EMI') {
                $this->createEmiPlanForSale($sale, $data);
            }

            // Update customer total purchases and last purchase date
            if ($sale->customer_id) {
                $this->updateCustomerPurchaseStats($sale->customer_id, $sale->total_amount, $sale->sale_date);
            }

            DB::commit();

            Log::info('Sale created successfully', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'tenant_id' => $tenantId
            ]);

            return $sale->load('items', 'customer');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating sale', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing sale
     */
    public function updateSale(Sale $sale, array $data, array $items): Sale
    {
        DB::beginTransaction();

        try {
            $tenantId = $this->getTenantId();
            
            // Store old values before update
            $oldCustomerId = $sale->customer_id;
            $oldTotalAmount = $sale->total_amount;

            // Create or find customer if customer_name and customer_phone are provided
            if (!empty($data['customer_name']) && !empty($data['customer_phone'])) {
                $customer = $this->createOrFindCustomer($tenantId, $data['customer_name'], $data['customer_phone']);
                $data['customer_id'] = $customer->id;
            }

            // Calculate totals
            $totals = $this->calculateTotals($items, $data['discount_amount'] ?? 0, $data['tax_amount'] ?? 0);

            // Update sale
            $sale->update(array_merge($data, [
                'subtotal' => $totals['subtotal'],
                'total_amount' => $totals['total_amount'],
                'due_amount' => $totals['total_amount'] - ($data['paid_amount'] ?? 0),
                'payment_status' => $this->determinePaymentStatus($totals['total_amount'], $data['paid_amount'] ?? 0),
            ]));

            // Delete existing items
            $sale->items()->delete();

            // Create new items
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'imei_id' => $item['imei_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_price' => $item['total_price'],
                    'warranty_months' => $item['warranty_months'] ?? 0,
                    'warranty_expiry_date' => $item['warranty_expiry_date'] ?? null,
                ]);
            }

            // Update customer purchase stats
            if ($oldCustomerId && $oldCustomerId == $sale->customer_id) {
                // Same customer - adjust the difference
                $difference = $sale->total_amount - $oldTotalAmount;
                $this->updateCustomerPurchaseStats($sale->customer_id, $difference, $sale->sale_date);
            } else {
                // Customer changed
                if ($oldCustomerId) {
                    // Deduct from old customer
                    $this->updateCustomerPurchaseStats($oldCustomerId, -$oldTotalAmount, null);
                }
                if ($sale->customer_id) {
                    // Add to new customer
                    $this->updateCustomerPurchaseStats($sale->customer_id, $sale->total_amount, $sale->sale_date);
                }
            }

            DB::commit();

            Log::info('Sale updated successfully', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);

            return $sale->fresh()->load('items', 'customer');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error updating sale', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a sale
     */
    public function deleteSale(Sale $sale): bool
    {
        try {
            // Check if sale has returns
            if ($sale->returns()->exists()) {
                throw new Exception('Cannot delete sale with existing returns.');
            }

            // Check if sale has EMI plan
            if ($sale->emiPlan()->exists()) {
                throw new Exception('Cannot delete sale with active EMI plan.');
            }

            $saleId = $sale->id;
            $invoiceNumber = $sale->invoice_number;
            $customerId = $sale->customer_id;
            $totalAmount = $sale->total_amount;

            $deleted = $sale->delete();

            if ($deleted) {
                // Deduct from customer total purchases
                if ($customerId) {
                    $this->updateCustomerPurchaseStats($customerId, -$totalAmount, null);
                }
                
                Log::info('Sale deleted successfully', [
                    'sale_id' => $saleId,
                    'invoice_number' => $invoiceNumber
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting sale', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate sale totals
     */
    public function calculateTotals(array $items, float $discountAmount = 0, float $taxAmount = 0): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['total_price'];
        }

        $totalAmount = $subtotal - $discountAmount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Determine payment status
     */
    private function determinePaymentStatus(float $totalAmount, float $paidAmount): string
    {
        if ($paidAmount >= $totalAmount) {
            return 'PAID';
        } elseif ($paidAmount > 0) {
            return 'PARTIAL';
        } else {
            return 'UNPAID';
        }
    }

    /**
     * Create EMI plan for a sale
     */
    private function createEmiPlanForSale(Sale $sale, array $data): void
    {
        try {
            $emiService = app(EmiService::class);

            // Default EMI parameters (can be customized based on requirements)
            $emiData = [
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'total_amount' => $sale->total_amount,
                'down_payment' => $data['paid_amount'] ?? 0, // Use paid amount as down payment
                'number_of_installments' => $data['number_of_installments'] ?? 12, // Default 12 months
                'interest_rate' => $data['interest_rate'] ?? 0, // Default 0% interest
                'start_date' => $data['emi_start_date'] ?? now()->addMonth(), // Start next month
                'status' => 'ACTIVE',
            ];

            // Calculate installment amount
            $remainingAmount = $emiData['total_amount'] - $emiData['down_payment'];
            $emiData['installment_amount'] = $remainingAmount / $emiData['number_of_installments'];

            $emiService->createEmiPlan($emiData);

            Log::info('EMI plan created for sale', [
                'sale_id' => $sale->id,
                'emi_data' => $emiData
            ]);
        } catch (Exception $e) {
            Log::error('Error creating EMI plan for sale', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception, just log it so sale creation doesn't fail
        }
    }

    /**
     * Generate unique customer code
     */
    private function generateCustomerCode(int $tenantId): string
    {
        $prefix = 'CUST';
        $date = date('ym'); // Year and Month (e.g., 2512 for Dec 2025)

        $lastCustomer = Customer::where('tenant_id', $tenantId)
            ->whereNotNull('customer_code')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer && $lastCustomer->customer_code) {
            // Extract the last 4 digits from the customer code
            $lastNumber = (int) substr($lastCustomer->customer_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create or find customer by name and phone
     */
    private function createOrFindCustomer(int $tenantId, string $name, string $phone): Customer
    {
        try {
            // Try to find existing customer by phone number
            $customer = Customer::where('tenant_id', $tenantId)
                ->where('mobile_primary', $phone)
                ->first();

            if ($customer) {
                Log::info('Found existing customer', [
                    'customer_id' => $customer->id,
                    'phone' => $phone
                ]);
                return $customer;
            }

            // Generate customer code
            $customerCode = $this->generateCustomerCode($tenantId);

            // Create new customer
            $customer = Customer::create([
                'tenant_id' => $tenantId,
                'customer_code' => $customerCode,
                'full_name' => $name,
                'mobile_primary' => $phone,
                'customer_type' => 'NEW',
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            Log::info('Created new customer', [
                'customer_id' => $customer->id,
                'customer_code' => $customerCode,
                'name' => $name,
                'phone' => $phone
            ]);

            return $customer;
        } catch (Exception $e) {
            Log::error('Error creating/finding customer', [
                'name' => $name,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update customer purchase statistics
     */
    private function updateCustomerPurchaseStats(int $customerId, float $amount, $saleDate = null): void
    {
        try {
            $customer = Customer::find($customerId);
            
            if ($customer) {
                // Update total purchases
                $customer->total_purchases = ($customer->total_purchases ?? 0) + $amount;
                
                // Update last purchase date if provided and it's newer
                if ($saleDate) {
                    $saleDateTime = is_string($saleDate) ? \Carbon\Carbon::parse($saleDate) : $saleDate;
                    if (!$customer->last_purchase_date || $saleDateTime->greaterThan($customer->last_purchase_date)) {
                        $customer->last_purchase_date = $saleDateTime;
                    }
                }
                
                $customer->save();
                
                Log::info('Customer purchase stats updated', [
                    'customer_id' => $customerId,
                    'amount_change' => $amount,
                    'new_total' => $customer->total_purchases
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error updating customer purchase stats', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception to avoid breaking the sale transaction
        }
    }

    /**
     * Get current tenant ID
     */
    private function getTenantId(): int
    {
        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }
        
        return 1;
    }
}
