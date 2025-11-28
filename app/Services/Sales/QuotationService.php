<?php

namespace App\Services\Sales;

use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationItem;
use App\Models\Sales\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class QuotationService
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Get quotations query for DataTables
     */
    public function getQuotationsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Quotation::where('tenant_id', $tenantId)
                ->with(['customer', 'creator']);
        } catch (Exception $e) {
            Log::error('Error creating quotations query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique quotation number
     */
    public function generateQuotationNumber(int $tenantId): string
    {
        $prefix = 'QUO';
        $date = date('Ymd');
        
        $lastQuotation = Quotation::where('tenant_id', $tenantId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuotation && $lastQuotation->quotation_number) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new quotation
     */
    public function createQuotation(array $data, array $items): Quotation
    {
        DB::beginTransaction();
        
        try {
            $tenantId = $this->getTenantId();
            
            // Generate quotation number
            if (empty($data['quotation_number'])) {
                $data['quotation_number'] = $this->generateQuotationNumber($tenantId);
            }

            // Calculate totals
            $totals = $this->calculateTotals($items, $data['discount_amount'] ?? 0, $data['tax_amount'] ?? 0);

            // Create quotation
            $quotation = Quotation::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'subtotal' => $totals['subtotal'],
                'total_amount' => $totals['total_amount'],
                'status' => $data['status'] ?? 'DRAFT',
                'created_by' => auth()->id(),
            ]));

            // Create quotation items
            foreach ($items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_price' => $item['total_price'],
                ]);
            }

            DB::commit();

            Log::info('Quotation created successfully', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number
            ]);

            return $quotation->load('items', 'customer');
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating quotation', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing quotation
     */
    public function updateQuotation(Quotation $quotation, array $data, array $items): Quotation
    {
        DB::beginTransaction();
        
        try {
            // Cannot update converted quotations
            if ($quotation->status === 'CONVERTED') {
                throw new Exception('Cannot update a converted quotation.');
            }

            // Calculate totals
            $totals = $this->calculateTotals($items, $data['discount_amount'] ?? 0, $data['tax_amount'] ?? 0);

            // Update quotation
            $quotation->update(array_merge($data, [
                'subtotal' => $totals['subtotal'],
                'total_amount' => $totals['total_amount'],
            ]));

            // Delete existing items
            $quotation->items()->delete();

            // Create new items
            foreach ($items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_price' => $item['total_price'],
                ]);
            }

            DB::commit();

            Log::info('Quotation updated successfully', [
                'quotation_id' => $quotation->id
            ]);

            return $quotation->fresh()->load('items', 'customer');
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating quotation', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Convert quotation to sale
     */
    public function convertToSale(Quotation $quotation, array $saleData): Sale
    {
        DB::beginTransaction();
        
        try {
            if ($quotation->status === 'CONVERTED') {
                throw new Exception('Quotation has already been converted to a sale.');
            }

            // Prepare sale items from quotation items
            $saleItems = [];
            foreach ($quotation->items as $item) {
                $saleItems[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount_amount' => $item->discount_amount,
                    'total_price' => $item->total_price,
                ];
            }

            // Create sale
            $sale = $this->saleService->createSale(array_merge([
                'customer_id' => $quotation->customer_id,
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'tax_amount' => $quotation->tax_amount,
                'total_amount' => $quotation->total_amount,
            ], $saleData), $saleItems);

            // Update quotation status
            $quotation->update([
                'status' => 'CONVERTED',
                'converted_to_sale_id' => $sale->id,
            ]);

            DB::commit();

            Log::info('Quotation converted to sale', [
                'quotation_id' => $quotation->id,
                'sale_id' => $sale->id
            ]);

            return $sale;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error converting quotation to sale', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a quotation
     */
    public function deleteQuotation(Quotation $quotation): bool
    {
        try {
            if ($quotation->status === 'CONVERTED') {
                throw new Exception('Cannot delete a converted quotation.');
            }

            $deleted = $quotation->delete();

            if ($deleted) {
                Log::info('Quotation deleted successfully', [
                    'quotation_id' => $quotation->id
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting quotation', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate quotation totals
     */
    private function calculateTotals(array $items, float $discountAmount = 0, float $taxAmount = 0): array
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
