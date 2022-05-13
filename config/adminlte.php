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
        [
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => true,
        ],
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'text' => 'Dashboard',
            'route' => 'login',
            'title' => 'Dashboard',
            'can' =>  ['Admin']

        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
        ],
        [
            'text'    => 'Master',
            'can' =>  ['Admin'],
            'submenu' => [
                [
                    'text' => 'Company Master',
                    'url' => 'company',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'far fa-fw fa-file',
                ],

                [
                    'text' => 'Region Master ',
                    'url'  => 'admin/mws_regions',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
                [
                    'text' => 'Credentials Master',
                    'url'  => 'admin/credentials',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
                [
                    'text' => 'Currency Master',
                    'url'  => 'admin/currencys',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
                [
                    'text' => 'Asin Master',
                    'url'  => 'asin-master',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
                [
                    'text' => 'Roles',
                    'url' => 'admin/rolespermissions',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin']
                ],
                [
                    'text' => 'User Master',
                    'icon' => 'far fa-fw fa-user',
                    'url'  => 'admin/user_list',
                    'can'  => ['Admin'],
                    // 'submenu' => [
                    //     [
                    //         'text' => 'Admin Management',
                    //         'url'  => 'admin/user_list',
                    //         'icon' => 'far fa-fw fa-user',
                    //         'can' =>  ['Admin'],
                    //     ],
                    //     [
                    //         'text' => 'Catalog Management',
                    //         'url'  => 'admin/catalog_user',
                    //         'icon' => 'far fa-fw fa-user',
                    //         'can' =>  ['Admin']
                    //     ]
                    // ],
                ],

            ],
        ],

        [
            'text'    => 'Catalog',
            'can' =>   ['Admin', 'Catalog Manager'],
            'submenu' => [

                [
                    'text' => 'Universal Textiles',
                    'url'  => 'textiles',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
                [

                    'text' => 'PMS Amazon',
                    'url'  => 'product/amazon_com',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin'],
                ],
                [

                    'text' => 'Other Amazon.com',
                    'url'  => 'other-product/amazon_com',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Catalog Manager'],
                ],
                [

                    'text' => 'Other Amazon.IN',
                    'url'  => 'other-product/amazon_in',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Catalog Manager'],
                ],
                [

                    'text' => 'fragrancenet.com',
                    'url'  => '../../fragrancenet.com',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin']
                ],
            ],
        ],

        [
            'text'    => 'Inventory',
            'can' =>   ['Admin', 'Inventory'],
            'submenu' => [

                [
                    'text' => 'Master',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Inventory'],
                    'submenu' => [

                        [
                            'text' => 'Warehouse',
                            'url'  => 'inventory/warehouses',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory'],
                            // 'submenu' => [

                            //     [

                            //         'text' => 'Manage',
                            //         'url'  => 'inventory/warehouses',
                            //         'icon' => 'far fa-fw fa-file',
                            //         'can' =>  ['Admin', 'Inventory']
                            //     ],
                            // ],

                        ],
                        [

                            'text' => 'Rack Master',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory'],
                            'submenu' => [
                                [
                                    'text' => 'Racks',
                                    'url'  => 'inventory/racks',
                                    'icon' => 'far fa-fw fa-file',
                                    'can' =>  ['Admin', 'Inventory']
                                ],
                                [
                                    'text' => 'Shelves',
                                    'url'  => 'inventory/shelves',
                                    'icon' => 'far fa-fw fa-file',
                                    'can' =>  ['Admin', 'Inventory']

                                ],
                                [
                                    'text' => 'Bins',
                                    'url'  => 'inventory/bins',
                                    'icon' => 'far fa-fw fa-file',
                                    'can' =>  ['Admin', 'Inventory']

                                ],

                            ],
                        ],
                        [
                            'text' => 'Dispose',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin'],
                            'submenu' => [

                                [
                                    'text' => ' Dispose reason',
                                    'url'  => 'inventory/disposes',
                                    'icon' => 'far fa-fw fa-file',
                                    'can' =>  ['Admin'],
                                ],
                            ],
                        ],

                    ],
                ],



                [
                    'text' => 'Inward ',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Inventory'],
                    'submenu' => [
                        [
                            'text' => 'Source',
                            'url'  => 'inventory/sources',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory'],
                        ],
                        [
                            'text' => 'Shipment',
                            'url'  => 'inventory/shipments',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Inwarding',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],

                    ],
                ],

                [
                    'text' => 'Outward ',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Inventory'],
                    'submenu' => [
                        [
                            'text' => 'Destination',
                            'url'  => 'inventory/destinations',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory'],

                        ],
                        [
                            'text' => 'Shipment',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Outwarding',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],

                    ],
                ],
                [
                    'text' => 'Reports',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Inventory'],
                    'submenu' => [
                        [
                            'text' => 'Daily',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Weekly',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Monthly',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                    ],
                ],
                [
                    'text' => 'System',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin', 'Inventory'],
                    'submenu' => [
                        [
                            'text' => 'Option',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Email',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                        [
                            'text' => 'Config',
                            'url'  => 'inventory/features',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin', 'Inventory']
                        ],
                    ],
                ],

            ],
        ],
        [
            'text' => 'Orders',
            'can' =>  ['Admin'],
            'submenu' => [
                [
                    'text' => 'Order',
                    'url' => 'orders/list',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',

                ],
                [
                    'text' => 'Orders Details',
                    'url' => 'orders/details ',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',


                ],
                [
                    'text' => 'Orders Item Details',
                    'url' => 'orders/item-details ',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',


                ],
            ],
        ],


        [
            'text' => 'B2C Ship',
            'can' =>  ['Admin', 'B2CShip'],
            'submenu' => [
                [
                    'text' => 'Details',
                    'can' => ['Admin', 'B2CShip'],
                    'submenu' => [
                        [
                            'text' => 'Ship Tracking Status',
                            'icon' => 'far fa-fw fa-file',
                            'can' => ['Admin', 'B2CShip'],
                            'url' => 'b2cship/tracking_status/details'
                        ],
                        [
                            'text' => 'Micro Status Missing',
                            'url' => 'b2cship/micro_status_missing_report',
                            'can' => ['Admin', 'B2CShip'],
                            'icon' => 'far fa-fw fa-file',
                        ]
                    ]

                ],
                [
                    'text' => 'Report',
                    'can' => ['Admin', 'B2CShip'],
                    'submenu' => [
                        [
                            'text' => 'KYC Status',
                            'url' => 'b2cship/kyc',
                            'can' => ['Admin', 'B2CShip'],
                            'icon' => 'far fa-fw fa-file',

                        ],

                        [
                            'text' => 'Booking Status',
                            'url' => 'b2cship/booking',
                            'can' => ['Admin', 'B2CShip'],
                            'icon' => 'far fa-fw fa-file',
                        ],
                        [
                            'text' => 'Micro Status',
                            'url' => 'b2cship/micro_status_report',
                            'can' => ['Admin', 'B2CShip'],
                            'icon' => 'far fa-fw fa-file',
                        ],
                    ]
                ],
                [
                    'text' => 'Bombino',
                    'can' => ['Admin', 'B2CShip'],
                    'submenu' => [
                        [
                            'text' => 'Packet Activities',
                            'url' => 'bombion/packet-activities',
                            'can' => ['Admin', 'B2CShip'],
                            'icon' => 'far fa-fw fa-file',

                        ]
                    ]
                ],
            ],
        ],
        [
            'text' => 'BOE Manage',
            'can' =>  ['Admin', 'Account'],
            'submenu' => [
                [
                    'text' => 'Manage',
                    'url' => 'BOE/index',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'far fa-fw fa-file',

                ],
                [
                    'text' => 'Report',
                    'url' => 'BOE/report',
                    'can' => ['Admin', 'Account'],
                    'icon' => 'far fa-fw fa-file',

                ],
            ],
        ],

        // [
        //     'text' => 'Download Files',
        //     'url'  => 'file_downloads',
        //     'icon' => 'far fa-fw fa-file',
        //     'can' =>  ['Admin']
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
