<?php

namespace App\Services\Inventory;

use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    /**
     * Get all stock transfers for the current tenant
     */
    public function getStockTransfersForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return StockTransfer::with(['transferredBy', 'items.product'])
                ->forTenant($tenantId)
                ->orderBy('transfer_date', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching stock transfers for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get stock transfers query for DataTables
     */
    public function getStockTransfersQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return StockTransfer::with(['transferredBy', 'items.product'])
                ->forTenant($tenantId);
        } catch (Exception $e) {
            Log::error('Error creating stock transfers query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get products for dropdown
     */
    public function getProductsForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '>', 0)
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching products for stock transfer dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new stock transfer
     */
    public function createStockTransfer(array $data): StockTransfer
    {
        try {
            DB::beginTransaction();

            // Set tenant_id if not provided
            if (!isset($data['tenant_id'])) {
                $data['tenant_id'] = $this->getTenantId();
            }

            // Set transferred_by if not provided and user is authenticated
            if (!isset($data['transferred_by']) && auth()->check()) {
                $data['transferred_by'] = auth()->id();
            }

            // Validate that we have items
            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('At least one item must be added to the transfer.');
            }

            // Validate stock availability for each item
            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('tenant_id', $data['tenant_id'])
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }

                if ($product->current_stock < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$product->product_name}. Available: {$product->current_stock}, Requested: {$item['quantity']}");
                }
            }

            // Create the transfer
            $transfer = StockTransfer::create($data);

            // Create transfer items
            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = new StockTransferItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'received_quantity' => 0,
                ]);
            }
            $transfer->items()->saveMany($items);

            // Update product stock levels (decrease from source)
            foreach ($transfer->items as $item) {
                $item->product->decrement('current_stock', $item->quantity);
            }

            DB::commit();

            return $transfer->fresh(['transferredBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating stock transfer', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a stock transfer
     */
    public function updateStockTransfer(StockTransfer $transfer, array $data): StockTransfer
    {
        try {
            DB::beginTransaction();

            // Only allow updates if status is PENDING
            if ($transfer->status !== 'PENDING') {
                throw new Exception('Only pending transfers can be edited.');
            }

            // Validate that we have items
            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('At least one item must be added to the transfer.');
            }

            // Validate stock availability for each item (accounting for already reserved stock)
            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('tenant_id', $transfer->tenant_id)
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }

                // Get current reserved quantity for this transfer
                $currentReserved = $transfer->items()->where('product_id', $item['product_id'])->sum('quantity');

                // Calculate available stock (current stock + what was reserved for this transfer)
                $availableStock = $product->current_stock + $currentReserved;

                if ($availableStock < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$product->product_name}. Available: {$availableStock}, Requested: {$item['quantity']}");
                }
            }

            // Update the transfer
            $transfer->update($data);

            // Delete existing items and restore stock
            foreach ($transfer->items as $item) {
                $item->product->increment('current_stock', $item->quantity);
                $item->delete();
            }

            // Create new transfer items
            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = new StockTransferItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'received_quantity' => 0,
                ]);
            }
            $transfer->items()->saveMany($items);

            // Update product stock levels again
            foreach ($transfer->items as $item) {
                $item->product->decrement('current_stock', $item->quantity);
            }

            DB::commit();

            return $transfer->fresh(['transferredBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating stock transfer', [
                'transfer_id' => $transfer->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update transfer status
     */
    public function updateTransferStatus(StockTransfer $transfer, string $status, array $receivedQuantities = []): StockTransfer
    {
        try {
            DB::beginTransaction();

            $oldStatus = $transfer->status;

            if ($status === 'COMPLETED') {
                // For completed transfers, we might want to handle received quantities
                // This is a simplified version - in a real system, you'd track received quantities per item
                $transfer->status = 'COMPLETED';
            } elseif ($status === 'IN_TRANSIT') {
                $transfer->status = 'IN_TRANSIT';
            } elseif ($status === 'CANCELLED') {
                // Cancelled transfers should restore stock
                foreach ($transfer->items as $item) {
                    $item->product->increment('current_stock', $item->quantity - $item->received_quantity);
                }
                $transfer->status = 'CANCELLED';
            }

            $transfer->save();

            DB::commit();

            return $transfer->fresh(['transferredBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating transfer status', [
                'transfer_id' => $transfer->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a stock transfer
     */
    public function deleteStockTransfer(StockTransfer $transfer): bool
    {
        try {
            DB::beginTransaction();

            // Only allow deletion if status is PENDING
            if ($transfer->status !== 'PENDING') {
                throw new Exception('Only pending transfers can be deleted.');
            }

            // Restore stock levels
            foreach ($transfer->items as $item) {
                $item->product->increment('current_stock', $item->quantity);
            }

            // Delete transfer (cascade will delete items)
            $transfer->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting stock transfer', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get stock transfer statistics
     */
    public function getStockTransferStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $stats = StockTransfer::forTenant($tenantId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $totalTransfers = array_sum($stats);
            $totalItems = StockTransferItem::whereHas('stockTransfer', function($query) use ($tenantId) {
                $query->forTenant($tenantId);
            })->sum('quantity');

            $recentTransfers = StockTransfer::forTenant($tenantId)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            return [
                'total_transfers' => $totalTransfers,
                'total_items' => $totalItems,
                'pending_count' => $stats['PENDING'] ?? 0,
                'in_transit_count' => $stats['IN_TRANSIT'] ?? 0,
                'completed_count' => $stats['COMPLETED'] ?? 0,
                'cancelled_count' => $stats['CANCELLED'] ?? 0,
                'recent_transfers' => $recentTransfers,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching stock transfer statistics', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get transfer locations (for dropdown)
     */
    public function getTransferLocations(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $fromLocations = StockTransfer::forTenant($tenantId)
                ->whereNotNull('from_location')
                ->distinct()
                ->pluck('from_location')
                ->toArray();

            $toLocations = StockTransfer::forTenant($tenantId)
                ->whereNotNull('to_location')
                ->distinct()
                ->pluck('to_location')
                ->toArray();

            $allLocations = array_unique(array_merge($fromLocations, $toLocations));

            return array_combine($allLocations, $allLocations);
        } catch (Exception $e) {
            Log::error('Error fetching transfer locations', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
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

        // Fallback for testing if no auth
        return 1;
    }
}
