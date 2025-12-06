<aside id="layout-menu" class="layout-menu menu-vertical menu">

    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16 3L3 9v6c0 8.284 5.373 15.202 12.844 17.645L16 33l.156-.355C23.627 30.202 29 23.284 29 15V9l-13-6z"
                            fill="currentColor" opacity="0.2" />
                        <path d="M16 7c-4.418 0-8 3.582-8 8v10l8 3 8-3V15c0-4.418-3.582-8-8-8z" fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('app.name', 'MobileShop') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-dashboard"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <!-- Sales & Orders -->
        <li class="menu-item {{ request()->routeIs('sales.*') || request()->routeIs('quotations.*') || request()->routeIs('returns.*') || request()->routeIs('emi.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-shopping-cart"></i>
                <div data-i18n="Sales & Orders">Sales & Orders</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                    <a href="{{ route('sales.create') }}" class="menu-link">
                        <div data-i18n="New Sale / POS">New Sale / POS</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                    <a href="{{ route('sales.index') }}" class="menu-link">
                        <div data-i18n="All Sales">All Sales</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                    <a href="{{ route('quotations.index') }}" class="menu-link">
                        <div data-i18n="Quotations">Quotations</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                    <a href="{{ route('returns.index') }}" class="menu-link">
                        <div data-i18n="Returns & Refunds">Returns & Refunds</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('emi.*') ? 'active' : '' }}">
                    <a href="{{ route('emi.index') }}" class="menu-link">
                        <div data-i18n="EMI/Installments">EMI/Installments</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Customers -->
        <li class="menu-item {{ request()->routeIs('customers.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('customers.index') ? 'active' : '' }}">
                    <a href="{{ route('customers.index') }}" class="menu-link">
                        <div data-i18n="All Customers">All Customers</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                    <a href="{{ route('customers.create') }}" class="menu-link">
                        <div data-i18n="Add New Customer">Add New Customer</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('customer-groups.*') ? 'active' : '' }}">
                    <a href="{{ route('customer-groups.index') }}" class="menu-link">
                        <div data-i18n="Customer Groups">Customer Groups</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ request()->routeIs('loyalty.*') ? 'active' : '' }}">
                    <a href="{{ route('loyalty.index') }}" class="menu-link">
                        <div data-i18n="Loyalty Program">Loyalty Program</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                    <a href="{{ route('feedback.index') }}" class="menu-link">
                        <div data-i18n="Customer Feedback">Customer Feedback</div>
                    </a>
                </li> --}}
            </ul>
        </li>

        <!-- Inventory -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-package"></i>
                <div data-i18n="Inventory">Inventory</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <a href="{{ route('products.index') }}" class="menu-link">
                        <div data-i18n="All Products">All Products</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('products.create') }}" class="menu-link">
                        <div data-i18n="Add Product">Add Product</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <a href="{{ route('brands.index') }}" class="menu-link">
                        <div data-i18n="Brands">Brands</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}" class="menu-link">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('imei.*') ? 'active' : '' }}">
                    <a href="{{ route('imei.index') }}" class="menu-link">
                        <div data-i18n="IMEI Tracking">IMEI Tracking</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}">
                    <a href="{{ route('stock-adjustments.index') }}" class="menu-link">
                        <div data-i18n="Stock Adjustment">Stock Adjustment</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}">
                    <a href="{{ route('stock-transfers.index') }}" class="menu-link">
                        <div data-i18n="Stock Transfer">Stock Transfer</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('low-stock.*') ? 'active' : '' }}">
                    <a href="{{ route('low-stock.index') }}" class="menu-link">
                        <div data-i18n="Low Stock Alert">Low Stock Alert</div>
                        <div class="badge text-bg-danger rounded-pill ms-auto" id="low-stock-badge">0</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Purchases -->
        <li class="menu-item {{ request()->routeIs('suppliers.*') || request()->routeIs('purchase-orders.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-truck-delivery"></i>
                <div data-i18n="Purchases">Purchases</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('purchase-orders.index') ? 'active' : '' }}">
                    <a href="{{ route('purchase-orders.index') }}" class="menu-link">
                        <div data-i18n="Purchase Orders">Purchase Orders</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('purchase-orders.create') ? 'active' : '' }}">
                    <a href="{{ route('purchase-orders.create') }}" class="menu-link">
                        <div data-i18n="Create PO">Create PO</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                    <a href="{{ route('suppliers.index') }}" class="menu-link">
                        <div data-i18n="Suppliers">Suppliers</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('purchase-orders.index', ['status' => 'RECEIVED']) }}" class="menu-link">
                        <div data-i18n="Received Orders">Received Orders</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Repair Service -->
        {{-- <li class="menu-item {{ request()->routeIs('repairs.*') || request()->routeIs('repair-parts.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-tool"></i>
                <div data-i18n="Repair Service">Repair Service</div>
            </a>
            <ul class="menu-sub">
                <!-- Overview/Dashboard -->
                <li class="menu-item {{ request()->routeIs('repairs.index') && !request('status') ? 'active' : '' }}">
                    <a href="{{ route('repairs.index') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-dashboard me-2"></i>
                        <div data-i18n="Overview">Overview</div>
                    </a>
                </li>

                <!-- Quick Actions -->
                <li class="menu-item {{ request()->routeIs('repairs.create') ? 'active' : '' }}">
                    <a href="{{ route('repairs.create') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-plus me-2"></i>
                        <div data-i18n="New Ticket">New Ticket</div>
                    </a>
                </li>

                <!-- Status Filters -->
                <li class="menu-item {{ request('status') === 'RECEIVED' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'RECEIVED']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-inbox me-2"></i>
                        <div data-i18n="Received">Received</div>
                    </a>
                </li>
                <li class="menu-item {{ request('status') === 'DIAGNOSED' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'DIAGNOSED']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-stethoscope me-2"></i>
                        <div data-i18n="Diagnosed">Diagnosed</div>
                    </a>
                </li>
                <li class="menu-item {{ request('status') === 'IN_PROGRESS' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'IN_PROGRESS']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-clock me-2"></i>
                        <div data-i18n="In Progress">In Progress</div>
                        <div class="badge text-bg-info rounded-pill ms-auto" id="in-progress-count">0</div>
                    </a>
                </li>
                <li class="menu-item {{ request('status') === 'PARTS_PENDING' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'PARTS_PENDING']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-package me-2"></i>
                        <div data-i18n="Parts Pending">Parts Pending</div>
                    </a>
                </li>
                <li class="menu-item {{ request('status') === 'READY' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'READY']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-check-circle me-2"></i>
                        <div data-i18n="Ready for Delivery">Ready for Delivery</div>
                        <div class="badge text-bg-success rounded-pill ms-auto" id="ready-count">0</div>
                    </a>
                </li>
                <li class="menu-item {{ request('status') === 'DELIVERED' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['status' => 'DELIVERED']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-truck-delivery me-2"></i>
                        <div data-i18n="Delivered">Delivered</div>
                    </a>
                </li>

                <!-- Special Filters -->
                <li class="menu-item {{ request('warranty_repair') === '1' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['warranty_repair' => '1']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-shield-check me-2"></i>
                        <div data-i18n="Warranty Claims">Warranty Claims</div>
                    </a>
                </li>
                <li class="menu-item {{ request('priority') === 'URGENT' ? 'active' : '' }}">
                    <a href="{{ route('repairs.index', ['priority' => 'URGENT']) }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-alert-triangle me-2"></i>
                        <div data-i18n="Urgent">Urgent</div>
                        <div class="badge text-bg-danger rounded-pill ms-auto" id="urgent-count">0</div>
                    </a>
                </li>

                <!-- Parts Management -->
                <li class="menu-item {{ request()->routeIs('repair-parts.*') ? 'active' : '' }}">
                    <a href="{{ route('repair-parts.index') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-settings me-2"></i>
                        <div data-i18n="Repair Parts">Repair Parts</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        <!-- Apps & Pages -->
        {{-- <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Marketing & Communication">Marketing & Communication</span>
        </li> --}}

        <!-- Marketing -->
        {{-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                <div data-i18n="Marketing">Marketing</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="marketing-sms-campaigns.html" class="menu-link">
                        <div data-i18n="SMS Campaigns">SMS Campaigns</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-send-sms.html" class="menu-link">
                        <div data-i18n="Send SMS">Send SMS</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-templates.html" class="menu-link">
                        <div data-i18n="SMS Templates">SMS Templates</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-sms-logs.html" class="menu-link">
                        <div data-i18n="SMS Logs">SMS Logs</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-offers.html" class="menu-link">
                        <div data-i18n="Offers & Discounts">Offers & Discounts</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-notifications.html" class="menu-link">
                        <div data-i18n="Notifications">Notifications</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="marketing-call-logs.html" class="menu-link">
                        <div data-i18n="Call Logs">Call Logs</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        <!-- Finance Section -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Finance & Reports">Finance & Reports</span>
        </li>

        <!-- Finance -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-currency-dollar"></i>
                <div data-i18n="Finance">Finance</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="finance-cash-book.html" class="menu-link">
                        <div data-i18n="Cash Book">Cash Book</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="finance-expenses.html" class="menu-link">
                        <div data-i18n="Expenses">Expenses</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="finance-expense-categories.html" class="menu-link">
                        <div data-i18n="Expense Categories">Expense Categories</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="finance-profit-loss.html" class="menu-link">
                        <div data-i18n="Profit & Loss">Profit & Loss</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="finance-collections.html" class="menu-link">
                        <div data-i18n="Payment Collection">Payment Collection</div>
                        <div class="badge text-bg-warning rounded-pill ms-auto">15</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="finance-supplier-payments.html" class="menu-link">
                        <div data-i18n="Supplier Payments">Supplier Payments</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Reports -->
        <li class="menu-item {{ request()->routeIs('reports.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                <div data-i18n="Reports">Reports</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                    <a href="{{ route('reports.sales') }}" class="menu-link">
                        <div data-i18n="Sales Report">Sales Report</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                    <a href="{{ route('reports.inventory') }}" class="menu-link">
                        <div data-i18n="Inventory Report">Inventory Report</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('reports.customers') ? 'active' : '' }}">
                    <a href="{{ route('reports.customers') }}" class="menu-link">
                        <div data-i18n="Customer Report">Customer Report</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                    <a href="{{ route('reports.financial') }}" class="menu-link">
                        <div data-i18n="Financial Report">Financial Report</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('reports.product-performance') ? 'active' : '' }}">
                    <a href="{{ route('reports.product-performance') }}" class="menu-link">
                        <div data-i18n="Product Performance">Product Performance</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="reports-staff-performance.html" class="menu-link">
                        <div data-i18n="Staff Performance">Staff Performance</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="reports-repair.html" class="menu-link">
                        <div data-i18n="Repair Reports">Repair Reports</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="reports-custom.html" class="menu-link">
                        <div data-i18n="Custom Reports">Custom Reports</div>
                    </a>
                </li> --}}
            </ul>
        </li>

        <!-- HR & Management -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="HR & Management">HR & Management</span>
        </li>

        <!-- HR & Staff -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-user-circle"></i>
                <div data-i18n="HR & Staff">HR & Staff</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="hr-employees.html" class="menu-link">
                        <div data-i18n="Employees">Employees</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="hr-attendance.html" class="menu-link">
                        <div data-i18n="Attendance">Attendance</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="hr-leave-requests.html" class="menu-link">
                        <div data-i18n="Leave Requests">Leave Requests</div>
                        <div class="badge text-bg-warning rounded-pill ms-auto">2</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="hr-roles.html" class="menu-link">
                        <div data-i18n="Roles & Permissions">Roles & Permissions</div>
                    </a>
                </li> --}}
            </ul>
        </li>

        <!-- Settings Section -->
        {{-- <li class="menu-header small">
            <span class="menu-header-text" data-i18n="System Configuration">System Configuration</span>
        </li> --}}

        <!-- Settings -->
        {{-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-settings"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="settings-profile.html" class="menu-link">
                        <div data-i18n="Shop Profile">Shop Profile</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-subscription.html" class="menu-link">
                        <div data-i18n="Subscription">Subscription</div>
                        <div class="badge text-bg-primary rounded-pill ms-auto">Pro</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-general.html" class="menu-link">
                        <div data-i18n="General Settings">General Settings</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-invoice.html" class="menu-link">
                        <div data-i18n="Invoice Settings">Invoice Settings</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-sms.html" class="menu-link">
                        <div data-i18n="SMS Configuration">SMS Configuration</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-payment-methods.html" class="menu-link">
                        <div data-i18n="Payment Methods">Payment Methods</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-tax.html" class="menu-link">
                        <div data-i18n="Tax Settings">Tax Settings</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-integrations.html" class="menu-link">
                        <div data-i18n="API Integrations">API Integrations</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-backup.html" class="menu-link">
                        <div data-i18n="Backup & Export">Backup & Export</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings-audit-logs.html" class="menu-link">
                        <div data-i18n="Audit Logs">Audit Logs</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        <!-- Help & Support -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-help"></i>
                <div data-i18n="Help & Support">Help & Support</div>
            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item">
                    <a href="help-docs.html" class="menu-link">
                        <div data-i18n="Documentation">Documentation</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="help-tutorials.html" class="menu-link">
                        <div data-i18n="Video Tutorials">Video Tutorials</div>
                    </a>
                </li> --}}
                <li class="menu-item">
                    <a href="help-support.html" class="menu-link">
                        <div data-i18n="Contact Support">Contact Support</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="help-changelog.html" class="menu-link">
                        <div data-i18n="What's New">What's New</div>
                    </a>
                </li>
            </ul>
        </li>

    </ul>

</aside>

<!-- Quick Action Button (Floating) -->
<div class="buy-now">
    <a href="{{ route('sales.create') }}" class="btn btn-danger btn-buy-now">
        <i class="ti tabler-plus"></i> New Sale
    </a>
</div>
