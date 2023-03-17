<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
     */

    'title' => 'Catalog Manager',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
     */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    |
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
     */

    'logo' => '<b>M C M</b>',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Mosh Ecom',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
     */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
     */

    'layout_topnav' => true,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => false,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
     */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
     */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container-fluid',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
     */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
     */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
     */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
     */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
     */

    'menu' => [
        // Navbar items:
        // [
        //     'type'         => 'navbar-search',
        //     'text'         => 'search',
        //     'topnav_right' => true,
        // ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => false,
        ],

        // Sidebar items:
        [
            'text' => 'Dashboard',
            'route' => 'login',
            'title' => 'Dashboard',
            'can' => ['Admin'],

        ],
        [
            'text' => 'blog',
            'url' => 'admin/blog',
            'can' => 'manage-blog',
        ],
        [
            'text' => 'Master',
            'can' => ['Admin', 'Account'],
            'submenu' => [
                [
                    'text' => 'Roles',
                    'url' => 'admin/rolespermissions',
                    'icon' => 'far fa fa-file-text-o',
                    'can' => ['Admin'],
                ],
                [
                    'text' => 'Users',
                    'icon' => 'far fa fa-users',
                    'url' => 'admin/user_list',
                    'can' => ['Admin'],
                ],
                [
                    'text' => 'Geo',
                    'url' => 'admin/geo',
                    'icon' => 'fas fa-globe-asia',
                    'can' => ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Country',
                            'url' => 'admin/geo/country',
                            'icon' => 'far fa fa-globe',
                            'can' => ['Admin'],
                        ],
                        [
                            'text' => 'State',
                            'url' => 'admin/geo/state',
                            'icon' => 'far fa fa-flag',
                            'can' => ['Admin'],
                        ],
                        [
                            'text' => 'City',
                            'url' => 'admin/geo/city',
                            'icon' => 'far fa fa-building-o',
                            'can' => ['Admin'],
                        ],
                    ],
                ],
                [
                    'text' => 'Company',
                    'url' => 'company',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'far fa fa-building-o',
                ],
                [
                    'text' => 'Stores',
                    'url' => 'admin/stores',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'fas fa-store ',
                ],
                [
                    'text' => 'Rate',
                    'url' => 'admin/rate-master',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-money',
                ],
                [
                    'text' => 'BuyBox',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-archive',
                    'submenu' => [
                        [
                            'text' => 'Region Master ',
                            'url' => 'admin/mws_regions',
                            'icon' => 'far fa fa-map-marker',
                            'can' => ['Admin'],
                        ],
                        [
                            'text' => 'Credentials Master',
                            'url' => 'admin/credentials',
                            'icon' => 'far fa-id-card',
                            'can' => ['Admin'],
                        ],
                        [
                            'text' => 'Currency Master',
                            'url' => 'admin/currencys',
                            'icon' => 'far fa fa-money',
                            'can' => ['Admin'],
                        ],
                        [
                            'text' => 'Credentials Management',
                            'url' => 'admin/creds/manage',
                            'icon' => 'far fa-id-card',
                            'can' => ['Admin'],
                        ],
                    ],
                ],
                [
                    'text' => 'System',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'far fa fa-desktop',
                    'submenu' => [
                        [
                            'text' => 'Setting',
                            'url' => 'admin/system-setting',
                            'can' => ['Admin', 'Account'],
                            'icon' => 'far fa fa-cog',
                        ],
                        [
                            'text' => 'File Management',
                            'url' => 'admin/file-management',
                            'can' => ['Admin'],
                            'icon' => 'far fa-fw fa-file',
                        ],
                        [
                            'text' => 'Process Management',
                            'url' => 'admin/process-management',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-spinner',
                        ],
                        [
                            'text' => 'Jobs Management',
                            'url' => 'admin/job-management',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-tasks',
                        ],

                        [
                            'text' => 'Scheduler Management',
                            'url' => 'admin/scheduler/management',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-calendar',
                        ],
                        [
                            'text' => 'DB back-up Management',
                            'url' => 'admin/backup/management',
                            'can' => ['Admin'],
                            'icon' => 'fas fa-hdd',
                        ],
                    ],
                ],

            ],
        ],

        [
            'text' => 'MV2',
            'can' => ['Admin'],
            'submenu' => [
                [
                    'text' => 'Master',
                    'can' => ['Admin'],
                    'icon' => 'far far fa fa-cog',
                    'submenu' => [

                        [
                            'text' => 'Roles',
                            'url' => 'v2/master/roles',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-file-text-o',

                        ],

                        [
                            'text' => 'Departments',
                            'url' => 'v2/master/departments',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-sitemap',

                        ],

                        [
                            'text' => 'Company',
                            'url' => 'v2/master/company',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-building-o',

                        ],

                        [
                            'text' => 'Users',
                            'url' => 'v2/master/users',
                            'can' => ['Admin'],
                            'icon' => 'far fa-fw fa-user',

                        ],

                        [
                            'text' => 'Store',
                            'url' => 'store',
                            'can' => ['Admin'],
                            'icon' => 'fas fa-store',
                            'submenu' => [
                                [
                                    'text' => 'Currency',
                                    'url' => 'v2/master/store/currency',
                                    'icon' => 'far fa fa-money',
                                    'can' => ['Admin'],
                                ],
                                [
                                    'text' => 'Regions',
                                    'url' => 'v2/master/store/regions',
                                    'icon' => 'far fa fa-map-marker',
                                    'can' => ['Admin'],
                                ],
                                [
                                    'text' => 'Credentials',
                                    'url' => 'v2/master/store/credentials',
                                    'icon' => 'far fa-id-card',
                                    'can' => ['Admin'],
                                ],

                            ],

                        ],

                        [
                            'text' => 'Geo',
                            'url' => 'geo',
                            'can' => ['Admin'],
                            'icon' => 'fas fa-globe-asia',
                            'submenu' => [
                                [
                                    'text' => 'Country',
                                    'url' => 'v2/master/geo/country',
                                    'icon' => 'far fa fa-globe',
                                    'can' => ['Admin'],
                                ],
                                [
                                    'text' => 'State',
                                    'url' => 'v2/master/geo/state',
                                    'icon' => 'far fa fa-flag',
                                    'can' => ['Admin'],
                                ],
                                [
                                    'text' => 'City',
                                    'url' => 'v2/master/geo/city',
                                    'icon' => 'far fa fa-building-o',
                                    'can' => ['Admin'],
                                ],
                            ],

                        ],
                    ],

                ],
            ],
        ],

        [
            'text' => 'Catalog',
            'can' => ['Admin', 'Catalog'],
            'submenu' => [
                [
                    'text' => 'Dashboard',
                    'url' => 'catalog/dashboard',
                    'icon' => 'fa fa-dashboard',
                    'can' => ['Admin', 'Catalog'],
                ],

                [
                    'text' => 'Asin Master',
                    'icon' => 'far fa fa-database',
                    'can' => ['Admin', 'Catalog'],
                    'submenu' => [
                        [
                            'text' => 'Asin Source',
                            'url' => 'catalog/asin-source',
                            'icon' => 'far fa fa-sitemap',
                            'can' => ['Admin', 'Catalog'],
                        ],

                        [
                            'text' => 'Asin Destination',
                            'url' => 'catalog/asin-destination',
                            'icon' => 'far fa fa-globe',
                            'can' => ['Admin', 'Catalog'],
                        ],
                    ],
                ],

                /*
                [
                'text' => 'Universal Textiles',
                'url'  => 'textiles',
                'icon' => 'far fa-fw fa-file',
                'can' =>  ['Admin']
                ],
                 */

                [

                    'text' => 'Mosh Amazon Catalog',
                    'icon' => 'far fa fa-database',
                    'can' => ['Admin', 'Catalog'],
                    'submenu' => [
                        [
                            'text' => 'Only Price Export',
                            'url' => 'catalog/product',
                            'icon' => 'far fa fa-tag',
                            'can' => ['Admin', 'Catalog'],
                        ],

                        [
                            'text' => 'Price Export With Catalog Details',
                            'url' => 'catalog/export-with-price',
                            'icon' => 'far fa fa-tags',
                            'can' => ['Admin', 'Catalog'],
                        ],
                    ],
                ],

                /*
                [
                'text' => 'Other Amazon.com',
                'url'  => 'other-product/amazon_com',
                'icon' => 'far fa-fw fa-file',
                'can' =>  ['Admin', 'Catalog'],
                ],
                [
                'text' => 'Other Amazon.IN',
                'url'  => 'other-product/amazon_in',
                'icon' => 'far fa-fw fa-file',
                'can' =>  ['Admin', 'Catalog'],
                ],
                 */

                /*
                [
                'text' => 'fragrancenet.com',
                'url'  => '../../fragrancenet.com',
                'icon' => 'far fa-fw fa-file',
                'can' =>  ['Admin']
                ],
                 */

                [

                    'text' => 'Catalog Exchange Rate',
                    'url' => 'catalog/exchange-rate',
                    'icon' => 'far fa fa-exchange',
                    'can' => ['Admin'],
                ],

                [

                    'text' => 'Buy-Box Operation',
                    'icon' => 'far fa fa-archive',
                    'can' => ['Admin', 'Catalog'],
                    'submenu' => [
                        [
                            'text' => 'Buy-Box Import',
                            'url' => 'catalog/buybox/import',
                            'icon' => 'far fa fa-cloud-download',
                            'can' => ['Admin', 'Catalog'],
                        ],

                        [
                            'text' => 'Buy-Box Export',
                            'url' => 'catalog/buybox/export',
                            'icon' => 'far fa fa-external-link',
                            'can' => ['Admin', 'Catalog'],
                        ],
                    ],
                ],
                [

                    'text' => 'Buy-Box Stores',
                    'icon' => 'far fa fa-shopping-cart',
                    'can' => ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Latency Update',
                            'url' => 'buybox/stores',
                            'icon' => 'far fa fa-clock-o',
                            'can' => ['Admin'],
                        ],
                    ],
                ],
            ],
        ],

        [
            'text' => 'Orders',
            'can' => ['Admin', 'Catalog', 'Inventory', 'BOE', 'KYC'],
            'submenu' => [
                [
                    'text' => 'Dashboard',
                    'can' => ['Admin'],
                    'icon' => 'fa fa-dashboard',
                    'submenu' => [
                        [
                            'text' => 'Order Details',
                            'url' => 'orders/dashboard',
                            'can' => ['Admin'],
                            'icon' => 'fa fa fa-file-text-o ',
                        ],
                        [
                            'text' => 'Order Item Details',
                            'url' => 'orders/item/dashboard',
                            'can' => ['Admin'],
                            'icon' => 'fa fa fa-cube',
                        ],
                    ],
                ],
                [
                    'text' => 'Order Details',
                    'can' => ['Admin'],
                    'icon' => 'fa fa-search',
                    'url' => "orders/details/list",

                ],
                [
                    'text' => 'AWS Order Dashboard',
                    'can' => ['Admin'],
                    'icon' => 'fa fa-dashboard',
                    'url' => "orders/aws/dashboard",

                ],
                [
                    'text' => 'Order Statistics',
                    'can' => ['Admin'],
                    'icon' => ' fa fa-check-circle-o',
                    'url' => "orders/statistics",

                ],
                [
                    'text' => 'Zoho Price Missing',
                    'can' => ['Admin', 'Catalog'],
                    'icon' => 'fa fa-minus-circle',
                    'submenu' =>
                    [
                        [
                            'text' => 'Price Missing',
                            'url' => "orders/missing/price",
                            'can' => ['Admin', 'Catalog'],
                            'icon' => 'fa fa-minus-circle',
                        ],
                        [
                            'text' => 'Price Updated',
                            'url' => "orders/missing/price/updated",
                            'can' => ['Admin', 'Catalog'],
                            'icon' => ' fa fa-check-circle-o',
                        ],
                        [
                            'text' => 'Zoho Dump',
                            'url' => "orders/missing/force/dump/view",
                            'can' => ['Admin', 'Catalog'],
                            'icon' => 'fa fa-plus-circle',
                        ],
                    ],
                ],
            ],
        ],

        [
            'text' => 'Operations',
            'can' => ['Admin', 'Inventory', 'BOE', 'KYC', 'B2CShip'],
            'submenu' => [
                [
                    'text' => 'Inventory',
                    'can' => ['Admin', 'Inventory'],
                    'icon' => 'far fa fa-list-alt',
                    'submenu' => [

                        [
                            'text' => 'Master',
                            'icon' => 'far fa fa-cog',
                            'can' => ['Admin', 'Inventory'],
                            'submenu' => [
                                [
                                    'text' => 'Warehouse',
                                    'url' => 'inventory/warehouses',
                                    'icon' => 'far fa fa-home',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                                [

                                    'text' => 'Rack',
                                    'icon' => 'far fa fa-table',
                                    'can' => ['Admin', 'Inventory'],
                                    'submenu' => [
                                        [
                                            'text' => 'Racks',
                                            'url' => 'inventory/racks',
                                            'icon' => 'far fa fa-cubes',
                                            'can' => ['Admin', 'Inventory'],
                                        ],
                                        [
                                            'text' => 'Shelves',
                                            'url' => 'inventory/shelves',
                                            'icon' => 'far fa fa-archive',
                                            'can' => ['Admin', 'Inventory'],

                                        ],
                                        [
                                            'text' => 'Bins',
                                            'url' => 'inventory/bins',
                                            'icon' => 'far fa fa-cube',
                                            'can' => ['Admin', 'Inventory'],

                                        ],

                                    ],
                                ],
                                [
                                    'text' => 'Tags',
                                    'icon' => 'far fa fa-tag',
                                    'url' => 'inventory/tags',
                                    'can' => ['Admin', 'Inventory'],

                                ],
                                [
                                    'text' => 'Dispose',
                                    'icon' => 'far fa fa-ban',
                                    'url' => 'inventory/features',
                                    'can' => ['Admin', 'Inventory'],

                                ],
                            ],
                        ],

                        [
                            'text' => 'Vendor (Source & Desitinaion)',
                            'url' => 'inventory/vendors',
                            'icon' => 'far fa fa-sitemap',
                            'can' => ['Admin', 'Inventory'],
                        ],

                        [
                            'text' => 'Stock Master',
                            'url' => 'inventory/stocks',
                            'icon' => 'far fa fa-line-chart',
                            'can' => ['Admin', 'Inventory'],
                            'submenu' => [
                                [
                                    'text' => 'Inventory Stocks',
                                    'url' => 'inventory/stocks',
                                    'icon' => 'far fa fa-check-square-o',
                                    'can' => ['Admin', 'Inventory'],
                                ],

                                [
                                    'text' => 'Inward ',
                                    'icon' => 'far fa fa-plus-square-o',
                                    'url' => 'inventory/shipments',
                                    'can' => ['Admin', 'Inventory'],
                                ],

                                [
                                    'text' => 'Outward ',
                                    'icon' => 'far fa fa-minus-square-o',
                                    'url' => 'inventory/outwardings',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                            ],
                        ],
                        [
                            'text' => 'Reports',
                            'icon' => 'far fa fa-pie-chart',
                            'can' => ['Admin', 'Inventory'],
                            'submenu' => [
                                [
                                    'text' => 'Daily',
                                    'url' => 'inventory/reports/daily',
                                    'icon' => 'far fa fa-calendar',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                                [
                                    'text' => 'Weekly',
                                    'url' => 'inventory/reports/weekly',
                                    'icon' => 'far fa fa-calendar',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                                [
                                    'text' => 'Monthly',
                                    'url' => 'inventory/reports/monthly',
                                    'icon' => 'far fa fa-calendar-o',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                            ],
                        ],
                        [
                            'text' => 'System',
                            'icon' => 'far fa fa-desktop',
                            'can' => ['Admin', 'Inventory'],
                            'submenu' => [
                                [
                                    'text' => 'Option',
                                    'url' => 'inventory/features',
                                    'icon' => 'far fa fa-th-large',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                                [
                                    'text' => 'Email',
                                    'url' => 'inventory/features',
                                    'icon' => 'far fa fa-envelope-o',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                                [
                                    'text' => 'Config',
                                    'url' => 'inventory/features',
                                    'icon' => 'far fa fa-cogs',
                                    'can' => ['Admin', 'Inventory'],
                                ],
                            ],
                        ],

                    ],
                ],
                [
                    'text' => 'Invoice',
                    'url' => 'invoice/manage',
                    'icon' => 'far fa fa-file-o',
                    'can' => ['Admin', 'Inventory'],
                ],
                [
                    'text' => 'Label',
                    'url' => 'label/manage',
                    'icon' => 'far fa fa-tag',
                    'can' => ['Admin', 'Inventory'],
                ],
                [
                    'text' => 'BOE',
                    'can' => ['Admin', 'BOE'],
                    'icon' => 'far fa fa-university',
                    'submenu' => [
                        [
                            'text' => 'Manage',
                            'url' => 'BOE/index',
                            'can' => ['Admin', 'BOE'],
                            'icon' => 'far fa fa-server',

                        ],
                        [
                            'text' => 'Report',
                            'url' => 'BOE/report',
                            'can' => ['Admin', 'BOE'],
                            'icon' => 'far fa fa-area-chart',

                        ],
                    ],
                ],
                [
                    'text' => 'Bulk Amazon Invoice Upload',
                    'url' => 'amazon/invoice',
                    'icon' => 'far fa fa-bullseye',
                    'can' => ['Admin', 'KYC'],
                ],
                [
                    'text' => 'B2C Ship',
                    'can' => ['Admin', 'B2CShip'],
                    'icon' => 'far fa fa-truck',
                    'submenu' => [
                        [
                            'text' => 'Dashboard',
                            'icon' => 'far fa fa-bar-chart',
                            'can' => ['Admin', 'B2CShip'],
                            'url' => 'b2cship/dashboard',
                        ],
                        [
                            'text' => 'Monitor',
                            'icon' => 'far fa fa-search',
                            'can' => ['Admin', 'B2CShip'],
                            'url' => 'b2cship/monitor',
                        ],
                        [
                            'text' => 'Details',
                            'icon' => 'far fa fa-file-text-o',
                            'can' => ['Admin', 'B2CShip'],
                            'submenu' => [
                                [
                                    'text' => 'Ship Tracking Status',
                                    'icon' => 'far fa fa-map-marker',
                                    'can' => ['Admin', 'B2CShip'],
                                    'url' => 'b2cship/tracking_status/details',
                                ],
                                [
                                    'text' => 'Micro Status Missing',
                                    'url' => 'b2cship/micro_status_missing_report',
                                    'can' => ['Admin', 'B2CShip'],
                                    'icon' => 'far far fa fa-envelope-o',
                                ],
                            ],

                        ],
                        [
                            'text' => 'Report',
                            'icon' => 'far fa fa-pie-chart',
                            'can' => ['Admin', 'B2CShip'],
                            'submenu' => [
                                [
                                    'text' => 'KYC Status',
                                    'url' => 'b2cship/kyc',
                                    'can' => ['Admin', 'B2CShip'],
                                    'icon' => 'far fa fa-calendar',

                                ],

                                [
                                    'text' => 'Booking Status',
                                    'url' => 'b2cship/booking',
                                    'can' => ['Admin', 'B2CShip'],
                                    'icon' => 'far fa fa-check-circle-o',
                                ],
                                [
                                    'text' => 'Micro Status',
                                    'url' => 'b2cship/micro_status_report',
                                    'can' => ['Admin', 'B2CShip'],
                                    'icon' => 'far fa fa-paper-plane',
                                ],
                            ],
                        ],
                        [
                            'text' => 'Bombino',
                            'icon' => 'far fa fa-plane',
                            'can' => ['Admin', 'B2CShip'],
                            'submenu' => [
                                [
                                    'text' => 'Packet Activities',
                                    'url' => 'bombion/packet-activities',
                                    'can' => ['Admin', 'B2CShip'],
                                    'icon' => 'far fa fa-cube ',

                                ],
                            ],
                        ],

                    ],
                ],
            ],

        ],

        [
            'text' => 'Seller Central',
            'can' => ['Admin', 'Seller'],
            'submenu' => [
                [
                    'text' => 'Asin Master',
                    'url' => 'seller/asin-master',
                    'can' => ['Admin', 'Seller'],
                    'icon' => 'far fa fa-cog',

                ],
                // [
                //     'text' => 'Catalog Details',
                //     'url' => 'seller/catalog',
                //     'can' => ['Admin', 'Seller'],
                //     'icon' => 'far fa-fw fa-file',
                // ],
                [
                    'text' => 'Asin Details',
                    'url' => 'seller/price/details',
                    'can' => ['Admin', 'Seller'],
                    'icon' => 'far  fa fa-cogs',
                ],
                [
                    'text' => 'Seller Invoice',
                    'url' => 'seller/invoice',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-user',
                ],
                [
                    'text' => ' stores',
                    'can' => ['Admin'],
                    'icon' => 'fas fa-store',
                    'submenu' =>
                    [
                        [
                            'text' => 'Price listing',
                            'url' => 'stores/listing/price',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-list',
                        ],
                        [
                            'text' => 'Availability',
                            'url' => 'stores/listing/availability',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-list'
                        ],
                        [
                            'text' => 'Price Updated',
                            'url' => 'stores/price/updated',
                            'can' => ['Admin'],
                            'icon' => 'fa fa-refresh',
                        ],
                    ],
                ],
            ],
        ],

        [
            'text' => 'Ship & Track',
            'can' => ['Admin'],
            'submenu' => [
                [
                    'text' => 'Courier Master',
                    'icon' => 'far  fa fa-cogs',
                    'can' => ['Admin'],

                    'submenu' =>
                    [
                        [
                            'text' => 'Courier Partner',
                            'url' => 'shipntrack/courier',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-archive',
                        ],
                        [
                            'text' => 'Booking Master',
                            'url' => 'shipntrack/booking',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-plane',
                        ],
                        [
                            'text' => 'Courier Status Master',
                            'url' => 'shipntrack/status',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-cog',
                        ],
                    ],




                ],

                [
                    'text' => 'Forwarder Mapping',
                    'url' => 'shipntrack/forwarder',
                    'icon' => 'far fa fa-map-marker',
                    'can' => ['Admin'],
                ],
                [
                    'text' => 'Tracking',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-bar-chart',
                    'submenu' =>
                    [
                        [
                            'text' => 'SMSA Tracking',
                            'url' => 'shipntrack/smsa',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-barcode',
                        ],
                        [
                            'text' => 'Bombino Tracking',
                            'url' => 'shipntrack/bombino',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-plane',
                        ],
                    ],
                ],


                [
                    'text' => 'Tracking Event',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-thumb-tack',
                    'submenu' =>
                    [
                        [
                            'text' => 'Tracking Event Master',
                            'url' => 'shipntrack/event-master',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-cog',
                        ],
                        [
                            'text' => 'Tracking Event Mapping',
                            'url' => 'shipntrack/event-mapping',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-map-marker',
                        ],
                        [
                            'text' => 'Stop Tracking',
                            'url' => 'shipntrack/stopTracking',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-ban',
                        ],
                        [
                            // 'text'  =>  'Tracking Listing',
                            // 'url'   =>  'shipntrack/trackingList',
                            // 'can'   =>  ['Admin'],
                        ],
                    ],
                ],
            ],
        ],

        [
            'text' => 'Cliqnshop',
            'can' => ['Admin', 'Cliqnshop'],
            'submenu' => [
                [
                    'text' => 'Catalog',
                    'can' => ['Admin', 'Cliqnshop'],
                    'icon' => 'fa fa-yelp',
                    'submenu' => [
                        [
                            'text' => 'Asin Importer',
                            'url' => 'catalog/index',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'far fa-fw fa-file',

                        ],
                        [
                            'text' => 'Keyword',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-google-wallet',
                            'submenu' => [
                                [
                                    'text' => 'Search Log',
                                    'url' => 'cliqnshop/keyword/log',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-cloud-download',
                                ],
                                [
                                    'text' => 'Banned Keywords',
                                    'url' => 'cliqnshop/keyword/ban',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-ban',
                                ],
                            ]
                        ],
                        [
                            'text' => 'Category',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-list-ul',
                            'submenu' => [
                                            [
                                                'text' => 'Banned Categories',
                                                'url' => 'cliqnshop/category',
                                                'can' => ['Admin', 'Cliqnshop'],
                                                'icon' => 'fa fa-ban',
                                            ],
                                        ]
                        ],
                        [
                            'text' => 'brand',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-list-ul',
                            'submenu' => [
                                            [
                                                'text' => 'Banned Brands',
                                                'url' => 'cliqnshop/brand/ban',
                                                'can' => ['Admin', 'Cliqnshop'],
                                                'icon' => 'fa fa-ban',
                                            ],
                               
                            ]
                        ]


                    ]
                ],
                [
                    'text' => 'Customer ',
                    'can' => ['Admin', 'Cliqnshop'],
                    'icon' => 'fa fa-users',
                    'submenu' => [
                        [
                            'text' => 'Orders Details',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-shopping-cart',
                            'submenu' => [
                                [
                                    'text' => 'Orders Pending',
                                    'url' => 'business/orders/details',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-clock-o',
                                ],
                                [
                                    'text' => 'Orders Booked',
                                    'url' => 'business/booked/details',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-check',
                                ],
                                [
                                    'text' => 'Orders Confirmation',
                                    'url' => 'business/orders/confirm',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-check-circle-o',
                                ],
                                [
                                    'text' => 'Shipment Notification',
                                    'url' => 'business/ship/confirmation',
                                    'can' => ['Admin', 'Cliqnshop'],
                                    'icon' => 'fa fa-bell',
                                ],
                            ],

                        ],
                        [
                            'text' => 'KYC Details',
                            'url' => 'cliqnshop/kyc',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-file',
                        ],
                        [
                            'text' => 'Contact List',
                            'url' => 'cliqnshop/contact',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-link',
                        ],

                    ]
                ],
                [
                    'text' => 'Home Section',
                    'can' => ['Admin', 'Cliqnshop'],
                    'icon' => 'fa fa-home',
                    'submenu' => [
                        [
                            'text' => 'Top Selling Section',
                            'url' => 'cliqnshop/brand',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-arrow-up',
                        ],
                        [
                            'text' => '1 Banner Section',
                            'url' => 'cliqnshop/one_banners',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-image',
                        ],
                        [
                            'text' => '2 Banner Section',
                            'url' => 'cliqnshop/two_banners',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-image',
                        ],
                        [
                            'text' => '3 Banner Section',
                            'url' => 'cliqnshop/banner',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-image',
                        ],
                        [
                            'text' => 'trending brands section',
                            'url' => 'cliqnshop/trending',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-th',
                        ],
                        [
                            'text' => 'promo Banner Section',
                            'url' => 'cliqnshop/promo',
                            'can' => ['Admin', 'Cliqnshop'],
                            'icon' => 'fa fa-arrow-up',
                        ],

                    ],
                ],
                [
                    'text' => 'Site Information',
                    'url' => 'cliqnshop/footercontent',
                    'can' => ['Admin', 'Cliqnshop'],
                    'icon' => 'fas fa-chalkboard',
                ],
                [
                    'text' => 'Static-Pages-Content',
                    'url' => 'cliqnshop/staticpagecontent',
                    'can' => ['Admin', 'Cliqnshop'],
                    'icon' => 'fas fa-file-word-o',
                ],

            ],
        ],

        [
            'text' => 'AWS-POC',
            'can' => ['Admin', 'POC'],
            'submenu' => [
                [
                    'text' => 'Search Product ',
                    'can' => ['Admin', 'POC'],
                    'icon' => 'far fa fa-search',
                    'submenu' => [
                        [
                            'text' => 'Search Product Request',
                            'url' => 'business/search/products',
                            'can' => ['Admin', 'POC'],
                            'icon' => 'far fa fa-search-plus',

                        ],
                        [
                            'text' => 'Product Request',
                            'url' => 'business/products/request',
                            'can' => ['Admin', 'POC'],
                            'icon' => 'far fa fa-paper-plane-o',

                        ],
                        [
                            'text' => 'Search Offers Request',
                            'url' => 'business/offers',
                            'can' => ['Admin', 'POC'],
                            'icon' => 'far fa fa-tag',

                        ],
                        [
                            'text' => 'Get Products By Asins',
                            'url' => 'business/byasins',
                            'can' => ['Admin', 'POC'],
                            'icon' => 'far fa fa-wrench',

                        ],
                    ],
                ],

                [
                    'text' => 'Order API',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-shopping-cart',
                    'submenu' => [
                        [
                            'text' => 'Order Test',
                            'url' => 'business/orders',
                            'can' => ['Admin'],
                            'icon' => 'far fa fa-check-square-o',

                        ],
                        // [
                        //     'text' => 'Order Details',
                        //     'url' => 'business/orders/details',
                        //     'can' => ['Admin'],
                        //     'icon' => 'far fa-fw fa-file',

                        // ]
                    ],

                ],
                [
                    'text' => 'B-API Catalog Details',
                    'url' => 'business/details',
                    'can' => ['Admin'],
                    'icon' => 'far fa fa-server',

                ],
            ],
        ],

        // [
        //     'text' => 'OMS',
        //     'can' => ['Admin'],
        //     'submenu' => [
        //         [
        //             'text' => 'Master',
        //             'can' => ['Admin'],
        //             'icon' => 'far fa fa-cogs',
        //             'submenu' => [

        //                 [
        //                     'text' => 'Status Master',
        //                     'url' => 'v2/oms',
        //                     'can' => ['Admin'],
        //                     'icon' => 'far fa fa-adjust',

        //                 ],
        //             ]

        //         ],
        //     ],
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
     */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
     */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
     */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
     */

    'livewire' => false,
];
