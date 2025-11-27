<?php

return [
    'sidebar' => [
        [
            'id' => 'dashboard',
            'label' => 'Dashboard',
            'icon' => 'tabler-smart-home',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'permissions' => ['dashboard.view']
        ],

        // Sales & Orders
        [
            'id' => 'sales',
            'label' => 'Sales & Orders',
            'icon' => 'tabler-shopping-cart',
            'active' => 'sales/*',
            'permissions' => ['sales.view', 'sales.create'],
            'submenu' => [
                [
                    'label' => 'New Sale / POS',
                    'route' => 'sales.create',
                    'active' => 'sales/create',
                    'permissions' => ['sales.create']
                ],
                [
                    'label' => 'All Sales',
                    'route' => 'sales.index',
                    'active' => 'sales',
                    'permissions' => ['sales.view']
                ],
                [
                    'label' => 'Quotations',
                    'route' => 'quotations.index',
                    'active' => 'quotations*',
                    'permissions' => ['sales.view']
                ],
                [
                    'label' => 'Returns & Refunds',
                    'route' => 'returns.index',
                    'active' => 'returns*',
                    'permissions' => ['sales.view']
                ],
                [
                    'id' => 'emi',
                    'label' => 'EMI/Installments',
                    'route' => 'emi.index',
                    'active' => 'emi*',
                    'permissions' => ['sales.view']
                ]
            ]
        ],

        // Customers
        [
            'id' => 'customers',
            'label' => 'Customers',
            'icon' => 'tabler-users',
            'active' => 'customers/*',
            'permissions' => ['customers.view', 'customers.create'],
            'submenu' => [
                [
                    'label' => 'All Customers',
                    'route' => 'customers.index',
                    'active' => 'customers',
                    'permissions' => ['customers.view']
                ],
                [
                    'label' => 'Add New Customer',
                    'route' => 'customers.create',
                    'active' => 'customers/create',
                    'permissions' => ['customers.create']
                ],
                [
                    'label' => 'Customer Groups',
                    'route' => 'customers.groups',
                    'active' => 'customers/groups',
                    'permissions' => ['customers.view']
                ],
                [
                    'label' => 'Loyalty Program',
                    'route' => 'loyalty.index',
                    'active' => 'loyalty*',
                    'permissions' => ['customers.view']
                ],
                [
                    'label' => 'Customer Feedback',
                    'route' => 'feedback.index',
                    'active' => 'feedback*',
                    'permissions' => ['customers.view']
                ]
            ]
        ],

        // Inventory
        [
            'id' => 'inventory',
            'label' => 'Inventory',
            'icon' => 'tabler-package',
            'active' => 'inventory/*',
            'permissions' => ['inventory.view', 'inventory.create'],
            'submenu' => [
                [
                    'label' => 'All Products',
                    'route' => 'products.index',
                    'active' => 'products',
                    'permissions' => ['inventory.view']
                ],
                [
                    'label' => 'Add Product',
                    'route' => 'products.create',
                    'active' => 'products/create',
                    'permissions' => ['inventory.create']
                ],
                [
                    'label' => 'Brands',
                    'route' => 'brands.index',
                    'active' => 'brands*',
                    'permissions' => ['inventory.view']
                ],
                [
                    'label' => 'Categories',
                    'route' => 'categories.index',
                    'active' => 'categories*',
                    'permissions' => ['inventory.view']
                ],
                [
                    'label' => 'IMEI Tracking',
                    'route' => 'imei.index',
                    'active' => 'imei*',
                    'permissions' => ['inventory.view']
                ],
                [
                    'label' => 'Stock Adjustment',
                    'route' => 'stock.adjustment',
                    'active' => 'stock/adjustment*',
                    'permissions' => ['inventory.edit']
                ],
                [
                    'label' => 'Stock Transfer',
                    'route' => 'stock.transfer',
                    'active' => 'stock/transfer*',
                    'permissions' => ['inventory.edit']
                ],
                [
                    'id' => 'low_stock',
                    'label' => 'Low Stock Alert',
                    'route' => 'stock.low',
                    'active' => 'stock/low',
                    'permissions' => ['inventory.view']
                ]
            ]
        ],

        // Purchases
        [
            'id' => 'purchases',
            'label' => 'Purchases',
            'icon' => 'tabler-truck-delivery',
            'active' => 'purchases/*',
            'permissions' => ['purchases.view', 'purchases.create'],
            'submenu' => [
                [
                    'label' => 'Purchase Orders',
                    'route' => 'purchases.index',
                    'active' => 'purchases',
                    'permissions' => ['purchases.view']
                ],
                [
                    'label' => 'Create PO',
                    'route' => 'purchases.create',
                    'active' => 'purchases/create',
                    'permissions' => ['purchases.create']
                ],
                [
                    'label' => 'Suppliers',
                    'route' => 'suppliers.index',
                    'active' => 'suppliers*',
                    'permissions' => ['purchases.view']
                ],
                [
                    'label' => 'Received Orders',
                    'route' => 'purchases.received',
                    'active' => 'purchases/received',
                    'permissions' => ['purchases.view']
                ]
            ]
        ],

        // Repair Service
        [
            'id' => 'repair',
            'label' => 'Repair Service',
            'icon' => 'tabler-tool',
            'active' => 'repair/*',
            'permissions' => ['repair.view', 'repair.create'],
            'submenu' => [
                [
                    'label' => 'Repair Tickets',
                    'route' => 'repair.index',
                    'active' => 'repair',
                    'permissions' => ['repair.view']
                ],
                [
                    'label' => 'New Ticket',
                    'route' => 'repair.create',
                    'active' => 'repair/create',
                    'permissions' => ['repair.create']
                ],
                [
                    'id' => 'repair_in_progress',
                    'label' => 'In Progress',
                    'route' => 'repair.in-progress',
                    'active' => 'repair/in-progress',
                    'permissions' => ['repair.view']
                ],
                [
                    'id' => 'repair_ready',
                    'label' => 'Ready for Delivery',
                    'route' => 'repair.ready',
                    'active' => 'repair/ready',
                    'permissions' => ['repair.view']
                ],
                [
                    'label' => 'Warranty Claims',
                    'route' => 'warranty.index',
                    'active' => 'warranty*',
                    'permissions' => ['repair.view']
                ]
            ]
        ],

        // Header: Marketing & Communication
        [
            'type' => 'header',
            'label' => 'Marketing & Communication'
        ],

        // Marketing
        [
            'id' => 'marketing',
            'label' => 'Marketing',
            'icon' => 'tabler-speakerphone',
            'active' => 'marketing/*',
            'permissions' => ['marketing.view', 'marketing.create'],
            'submenu' => [
                [
                    'label' => 'SMS Campaigns',
                    'route' => 'sms.campaigns',
                    'active' => 'marketing/sms*',
                    'permissions' => ['marketing.view']
                ],
                [
                    'label' => 'Send SMS',
                    'route' => 'sms.send',
                    'active' => 'marketing/send-sms',
                    'permissions' => ['marketing.create']
                ],
                [
                    'label' => 'SMS Templates',
                    'route' => 'sms.templates',
                    'active' => 'marketing/templates*',
                    'permissions' => ['marketing.view']
                ],
                [
                    'label' => 'Offers & Discounts',
                    'route' => 'offers.index',
                    'active' => 'offers*',
                    'permissions' => ['marketing.view']
                ]
            ]
        ],

        // Header: Finance & Reports
        [
            'type' => 'header',
            'label' => 'Finance & Reports'
        ],

        // Finance
        [
            'id' => 'finance',
            'label' => 'Finance',
            'icon' => 'tabler-currency-dollar',
            'active' => 'finance/*',
            'permissions' => ['finance.view'],
            'submenu' => [
                [
                    'label' => 'Cash Book',
                    'route' => 'finance.cashbook',
                    'active' => 'finance/cash-book',
                    'permissions' => ['finance.view']
                ],
                [
                    'label' => 'Expenses',
                    'route' => 'expenses.index',
                    'active' => 'expenses*',
                    'permissions' => ['finance.view']
                ],
                [
                    'label' => 'Profit & Loss',
                    'route' => 'finance.profit-loss',
                    'active' => 'finance/profit-loss',
                    'permissions' => ['finance.view']
                ],
                [
                    'id' => 'payment_collection',
                    'label' => 'Payment Collection',
                    'route' => 'finance.collections',
                    'active' => 'finance/collections',
                    'permissions' => ['finance.view']
                ]
            ]
        ],

        // Reports
        [
            'id' => 'reports',
            'label' => 'Reports',
            'icon' => 'tabler-chart-bar',
            'active' => 'reports/*',
            'permissions' => ['reports.view'],
            'submenu' => [
                [
                    'label' => 'Sales Report',
                    'route' => 'reports.sales',
                    'active' => 'reports/sales',
                    'permissions' => ['reports.view']
                ],
                [
                    'label' => 'Inventory Report',
                    'route' => 'reports.inventory',
                    'active' => 'reports/inventory',
                    'permissions' => ['reports.view']
                ],
                [
                    'label' => 'Customer Report',
                    'route' => 'reports.customers',
                    'active' => 'reports/customers',
                    'permissions' => ['reports.view']
                ],
                [
                    'label' => 'Financial Report',
                    'route' => 'reports.financial',
                    'active' => 'reports/financial',
                    'permissions' => ['reports.view']
                ]
            ]
        ],

        // Header: HR & Management
        [
            'type' => 'header',
            'label' => 'HR & Management'
        ],

        // HR & Staff
        [
            'id' => 'hr',
            'label' => 'HR & Staff',
            'icon' => 'tabler-user-circle',
            'active' => 'hr/*',
            'permissions' => ['hr.view'],
            'submenu' => [
                [
                    'label' => 'Employees',
                    'route' => 'employees.index',
                    'active' => 'employees*',
                    'permissions' => ['hr.view']
                ],
                [
                    'label' => 'Attendance',
                    'route' => 'attendance.index',
                    'active' => 'attendance*',
                    'permissions' => ['hr.view']
                ],
                [
                    'id' => 'leave_requests',
                    'label' => 'Leave Requests',
                    'route' => 'leave.index',
                    'active' => 'leave*',
                    'permissions' => ['hr.view']
                ],
                [
                    'label' => 'Roles & Permissions',
                    'route' => 'roles.index',
                    'active' => 'roles*',
                    'permissions' => ['hr.edit']
                ]
            ]
        ],

        // Header: System Configuration
        [
            'type' => 'header',
            'label' => 'System Configuration'
        ],

        // Settings
        [
            'id' => 'settings',
            'label' => 'Settings',
            'icon' => 'tabler-settings',
            'active' => 'settings/*',
            'permissions' => ['settings.view'],
            'submenu' => [
                [
                    'label' => 'Shop Profile',
                    'route' => 'settings.profile',
                    'active' => 'settings/profile',
                    'permissions' => ['settings.edit']
                ],
                [
                    'label' => 'Subscription',
                    'route' => 'settings.subscription',
                    'active' => 'settings/subscription',
                    'permissions' => ['settings.view'],
                    'badge' => [
                        'text' => 'Pro',
                        'type' => 'primary'
                    ]
                ],
                [
                    'label' => 'General Settings',
                    'route' => 'settings.general',
                    'active' => 'settings/general',
                    'permissions' => ['settings.edit']
                ],
                [
                    'label' => 'Invoice Settings',
                    'route' => 'settings.invoice',
                    'active' => 'settings/invoice',
                    'permissions' => ['settings.edit']
                ],
                [
                    'label' => 'SMS Configuration',
                    'route' => 'settings.sms',
                    'active' => 'settings/sms',
                    'permissions' => ['settings.edit']
                ],
                [
                    'label' => 'API Integrations',
                    'route' => 'settings.integrations',
                    'active' => 'settings/integrations',
                    'permissions' => ['settings.edit']
                ],
                [
                    'label' => 'Backup & Export',
                    'route' => 'settings.backup',
                    'active' => 'settings/backup',
                    'permissions' => ['settings.view']
                ],
                [
                    'label' => 'Audit Logs',
                    'route' => 'settings.audit',
                    'active' => 'settings/audit',
                    'permissions' => ['settings.view']
                ]
            ]
        ],

        // Help & Support
        [
            'id' => 'help',
            'label' => 'Help & Support',
            'icon' => 'tabler-help',
            'active' => 'help/*',
            'permissions' => [],
            'submenu' => [
                [
                    'label' => 'Documentation',
                    'route' => 'help.docs',
                    'active' => 'help/docs',
                    'permissions' => []
                ],
                [
                    'label' => 'Video Tutorials',
                    'route' => 'help.tutorials',
                    'active' => 'help/tutorials',
                    'permissions' => []
                ],
                [
                    'label' => 'Contact Support',
                    'route' => 'help.support',
                    'active' => 'help/support',
                    'permissions' => []
                ]
            ]
        ]
    ]
];
