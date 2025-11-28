<?php

namespace App\Services\Sales;

use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
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
            
            $deleted = $sale->delete();

            if ($deleted) {
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
