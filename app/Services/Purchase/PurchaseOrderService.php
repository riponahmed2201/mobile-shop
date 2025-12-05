<?php

namespace App\Services\Purchase;

use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\Supplier;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PurchaseOrderService
{
    /**
     * Get all purchase orders for the current tenant
     */
    public function getPurchaseOrdersForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return PurchaseOrder::with(['supplier', 'createdBy', 'items.product'])
                ->forTenant($tenantId)
                ->orderBy('po_date', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching purchase orders for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get purchase orders query for DataTables
     */
    public function getPurchaseOrdersQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return PurchaseOrder::with(['supplier', 'createdBy', 'items.product'])
                ->forTenant($tenantId);
        } catch (Exception $e) {
            Log::error('Error creating purchase orders query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get products for dropdown (for PO creation)
     */
    public function getProductsForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching products for PO dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate PO number
     */
    public function generatePoNumber(?int $tenantId = null): string
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $year = date('Y');
            $month = date('m');

            // Get the last PO number for this tenant this month
            $lastPo = PurchaseOrder::forTenant($tenantId)
                ->whereYear('po_date', $year)
                ->whereMonth('po_date', $month)
                ->orderBy('po_number', 'desc')
                ->first();

            if ($lastPo) {
                // Extract the sequential number from the last PO
                $parts = explode('-', $lastPo->po_number);
                $sequential = intval(end($parts)) + 1;
            } else {
                $sequential = 1;
            }

            return sprintf('PO-%s%s-%04d', $year, $month, $sequential);
        } catch (Exception $e) {
            Log::error('Error generating PO number', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new purchase order
     */
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        try {
            DB::beginTransaction();

            // Set tenant_id if not provided
            if (!isset($data['tenant_id'])) {
                $data['tenant_id'] = $this->getTenantId();
            }

            // Set created_by if not provided and user is authenticated
            if (!isset($data['created_by']) && auth()->check()) {
                $data['created_by'] = auth()->id();
            }

            // Generate PO number if not provided
            if (!isset($data['po_number']) || empty($data['po_number'])) {
                $data['po_number'] = $this->generatePoNumber($data['tenant_id']);
            }

            // Validate that we have items
            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('At least one item must be added to the purchase order.');
            }

            // Validate supplier belongs to tenant
            $supplier = Supplier::forTenant($data['tenant_id'])
                ->where('id', $data['supplier_id'])
                ->first();

            if (!$supplier) {
                throw new Exception('Supplier not found or does not belong to your tenant.');
            }

            // Calculate totals and validate items
            $totalAmount = 0;
            $validatedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('tenant_id', $data['tenant_id'])
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }

                if ($item['quantity'] <= 0) {
                    throw new Exception('Quantity must be greater than 0 for all items.');
                }

                if ($item['unit_price'] < 0) {
                    throw new Exception('Unit price cannot be negative.');
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $itemTotal;

                $validatedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $itemTotal,
                    'received_quantity' => 0,
                ];
            }

            // Set calculated fields
            $data['total_amount'] = $totalAmount;
            $data['due_amount'] = $totalAmount;

            // Create the purchase order
            $purchaseOrder = PurchaseOrder::create($data);

            // Create purchase order items
            $purchaseOrder->items()->createMany($validatedItems);

            DB::commit();

            return $purchaseOrder->fresh(['supplier', 'createdBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating purchase order', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a purchase order
     */
    public function updatePurchaseOrder(PurchaseOrder $purchaseOrder, array $data): PurchaseOrder
    {
        try {
            DB::beginTransaction();

            // Only allow updates if status is DRAFT
            if ($purchaseOrder->order_status !== 'DRAFT') {
                throw new Exception('Only draft purchase orders can be edited.');
            }

            // Validate that we have items
            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('At least one item must be added to the purchase order.');
            }

            // Validate supplier belongs to tenant
            $supplier = Supplier::forTenant($purchaseOrder->tenant_id)
                ->where('id', $data['supplier_id'])
                ->first();

            if (!$supplier) {
                throw new Exception('Supplier not found or does not belong to your tenant.');
            }

            // Calculate totals and validate items
            $totalAmount = 0;
            $validatedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('tenant_id', $purchaseOrder->tenant_id)
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }

                if ($item['quantity'] <= 0) {
                    throw new Exception('Quantity must be greater than 0 for all items.');
                }

                if ($item['unit_price'] < 0) {
                    throw new Exception('Unit price cannot be negative.');
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $itemTotal;

                $validatedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $itemTotal,
                    'received_quantity' => 0,
                ];
            }

            // Update calculated fields
            $data['total_amount'] = $totalAmount;
            $data['due_amount'] = $totalAmount - $purchaseOrder->paid_amount;

            // Update the purchase order
            $purchaseOrder->update($data);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();
            $purchaseOrder->items()->createMany($validatedItems);

            DB::commit();

            return $purchaseOrder->fresh(['supplier', 'createdBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase order', [
                'po_id' => $purchaseOrder->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update purchase order status
     */
    public function updatePurchaseOrderStatus(PurchaseOrder $purchaseOrder, string $status): PurchaseOrder
    {
        try {
            DB::beginTransaction();

            $oldStatus = $purchaseOrder->order_status;

            if ($status === 'CONFIRMED') {
                if ($purchaseOrder->order_status !== 'DRAFT') {
                    throw new Exception('Only draft orders can be confirmed.');
                }
                $purchaseOrder->order_status = 'CONFIRMED';
            } elseif ($status === 'RECEIVED') {
                if (!in_array($purchaseOrder->order_status, ['CONFIRMED', 'DRAFT'])) {
                    throw new Exception('Only confirmed or draft orders can be marked as received.');
                }
                $purchaseOrder->order_status = 'RECEIVED';

                // Update product stock levels and received quantities
                foreach ($purchaseOrder->items as $item) {
                    $item->update(['received_quantity' => $item->quantity]);
                    $item->product->increment('current_stock', $item->quantity);
                }
            } elseif ($status === 'CANCELLED') {
                if (in_array($purchaseOrder->order_status, ['RECEIVED'])) {
                    throw new Exception('Received orders cannot be cancelled.');
                }
                $purchaseOrder->order_status = 'CANCELLED';
            }

            $purchaseOrder->save();

            DB::commit();

            return $purchaseOrder->fresh(['supplier', 'createdBy', 'items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase order status', [
                'po_id' => $purchaseOrder->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a purchase order
     */
    public function deletePurchaseOrder(PurchaseOrder $purchaseOrder): bool
    {
        try {
            DB::beginTransaction();

            // Only allow deletion if status is DRAFT
            if ($purchaseOrder->order_status !== 'DRAFT') {
                throw new Exception('Only draft purchase orders can be deleted.');
            }

            // Delete associated items first (cascade should handle this, but being explicit)
            $purchaseOrder->items()->delete();

            // Delete the purchase order
            $purchaseOrder->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting purchase order', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get purchase order statistics
     */
    public function getPurchaseOrderStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $stats = PurchaseOrder::forTenant($tenantId)
                ->selectRaw('order_status, COUNT(*) as count, SUM(total_amount) as total_value')
                ->groupBy('order_status')
                ->pluck('count', 'order_status')
                ->toArray();

            $paymentStats = PurchaseOrder::forTenant($tenantId)
                ->selectRaw('payment_status, COUNT(*) as count, SUM(due_amount) as total_due')
                ->groupBy('payment_status')
                ->pluck('total_due', 'payment_status')
                ->toArray();

            $totalValue = PurchaseOrder::forTenant($tenantId)
                ->where('order_status', '!=', 'CANCELLED')
                ->sum('total_amount');

            $totalDue = PurchaseOrder::forTenant($tenantId)
                ->where('order_status', '!=', 'CANCELLED')
                ->sum('due_amount');

            $recentOrders = PurchaseOrder::forTenant($tenantId)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            return [
                'total_orders' => array_sum($stats),
                'total_value' => $totalValue,
                'total_due' => $totalDue,
                'draft_count' => $stats['DRAFT'] ?? 0,
                'confirmed_count' => $stats['CONFIRMED'] ?? 0,
                'received_count' => $stats['RECEIVED'] ?? 0,
                'cancelled_count' => $stats['CANCELLED'] ?? 0,
                'pending_payment' => $paymentStats['PENDING'] ?? 0,
                'partial_payment' => $paymentStats['PARTIAL'] ?? 0,
                'paid_orders' => $paymentStats['PAID'] ?? 0,
                'recent_orders' => $recentOrders,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching purchase order statistics', [
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