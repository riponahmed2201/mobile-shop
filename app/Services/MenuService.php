<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Get menu items with badge counts
     */
    public function getMenuItems()
    {
        return Cache::remember('sidebar_menu_' . auth()->id(), 60, function () {
            $menuData = config('menu.sidebar');

            // Add dynamic badge counts
            return $this->addBadgeCounts($menuData);
        });
    }

    /**
     * Add dynamic badge counts to menu
     */
    private function addBadgeCounts($menuItems)
    {
        foreach ($menuItems as &$item) {
            // Add badges based on item ID
            if (isset($item['id'])) {
                $badge = $this->getBadgeCount($item['id']);
                if ($badge) {
                    $item['badge'] = $badge;
                }
            }

            // Process submenu items
            if (isset($item['submenu'])) {
                foreach ($item['submenu'] as &$subitem) {
                    if (isset($subitem['id'])) {
                        $badge = $this->getBadgeCount($subitem['id']);
                        if ($badge) {
                            $subitem['badge'] = $badge;
                        }
                    }
                }
            }
        }

        return $menuItems;
    }

    /**
     * Get badge count for specific menu item
     */
    private function getBadgeCount($itemId)
    {
        $tenantId = auth()->user()->tenant_id;

        return match ($itemId) {
            'emi' => [
                'count' => \App\Models\EmiInstallment::where('tenant_id', $tenantId)
                    ->where('status', 'PENDING')
                    ->where('due_date', '<=', now()->addDays(7))
                    ->count(),
                'type' => 'warning'
            ],
            'low_stock' => [
                'count' => \App\Models\Product::where('tenant_id', $tenantId)
                    ->whereColumn('current_stock', '<=', 'min_stock_level')
                    ->count(),
                'type' => 'danger'
            ],
            'repair_in_progress' => [
                'count' => \App\Models\RepairTicket::where('tenant_id', $tenantId)
                    ->where('status', 'IN_PROGRESS')
                    ->count(),
                'type' => 'info'
            ],
            'repair_ready' => [
                'count' => \App\Models\RepairTicket::where('tenant_id', $tenantId)
                    ->where('status', 'READY')
                    ->count(),
                'type' => 'success'
            ],
            'payment_collection' => [
                'count' => \App\Models\Sale::where('tenant_id', $tenantId)
                    ->where('payment_status', '!=', 'PAID')
                    ->where('due_amount', '>', 0)
                    ->count(),
                'type' => 'warning'
            ],
            'leave_requests' => [
                'count' => \App\Models\LeaveRequest::where('tenant_id', $tenantId)
                    ->where('status', 'PENDING')
                    ->count(),
                'type' => 'warning'
            ],
            default => null
        };
    }

    /**
     * Clear menu cache
     */
    public function clearCache($userId = null)
    {
        if ($userId) {
            Cache::forget('sidebar_menu_' . $userId);
        } else {
            Cache::flush();
        }
    }
}
