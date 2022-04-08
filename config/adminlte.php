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

    'logo' => '<b>Mosh Catalog Manager</b>',
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
    'layout_fixed_footer' => null,
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
            'text'    => 'Catalog',
            'can' =>   ['Admin', 'Catalog Manager'],
            'submenu' => [
                [
                    'text'    => 'Master',
                    'icon' => 'far fa-fw fa-file',
                    'can' =>  ['Admin'],
                    'submenu' => [

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
                            'can'  => ['Admin'],
                            'submenu' => [
                                [
                                    'text' => 'Admin Management',
                                    'url'  => 'admin/user_list',
                                    'icon' => 'far fa-fw fa-user',
                                    'can' =>  ['Admin'],
                                ],
                                [
                                    'text' => 'Catalog Management',
                                    'url'  => 'admin/catalog_user',
                                    'icon' => 'far fa-fw fa-user',
                                    'can' =>  ['Admin']
                                ]
                            ],
                        ],

                    ],
                ],
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
            'can' =>   ['Admin'],
            'submenu' => [
                [

                    'text' => 'Master',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin'],
                    'submenu' => [

                        [
                            'text' => 'Roles',
                            'url'  => 'Inventory/Roles/Index',
                            'icon' => 'fas fa-user',
                            'can' =>  ['Admin']
                         
                        ],

                        [
                            'text' => 'Users',
                            'url'  => 'Inventory/Master/Users/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']

                        ],
                        [
                            'text' => 'Racks',
                            'url'  => 'Inventory/Master/Racks/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']

                        ],
                        [
                            'text' => 'Company',
                            'url'  => 'Inventory/master/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']

                        ],
                        [
                            'text' => 'Source',
                            'url'  => 'Inventory/master/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin'],
                            'submenu' => [
                                [
                                      'text' => 'Amazon',
                                      'url'  => 'Inventory/master/Index',
                                      'icon' => 'far fa-fw fa-file',
                                      'can' =>  ['Admin']
                                  ],
                                  [
                                      'text' => 'Maxcon',
                                      'url'  => 'Inventory/master/Index',
                                      'icon' => 'far fa-fw fa-file',
                                      'can' =>  ['Admin']
                                  ],
                                  [
                                      'text' => 'CStuart',
                                      'url'  => 'Inventory/master/Index',
                                      'icon' => 'far fa-fw fa-file',
                                      'can' =>  ['Admin']
                                  ],
  
                          ],

                        ],
                        [
                            'text' => 'Destination',
                            'url'  => 'Inventory/master/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin'],
                            'submenu'=> [
                                [
                                    'text' => 'Amazon Order',
                                    'url'  => 'Inventory/master/Index',
                                    'icon' => 'far fa-fw fa-file',
                                    'can' =>  ['Admin']
                                ],
                                
                                
                            ],

                        ],
                    ],
                ],
                [
                    'text' => 'Stock Master',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Inwarding',
                            'url'  => 'Inventory/Stock/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Outwarding',
                            'url'  => 'Inventory/Stock/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Disposing',
                            'url'  => 'Inventory/Stock/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Adjustment',
                            'url'  => 'Inventory/Stock/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                    ],
                ],
                [
                    'text' => 'Reporting',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Daily/Weekly/Monthly',
                            'url'  => 'Inventory/Reporting/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Inwarding V/S Outwarding',
                            'url'  => 'Inventory/Reporting/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Dispose',
                            'url'  => 'Inventory/Reporting/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Aging Report',
                            'url'  => 'Inventory/Reporting/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                    ],
                ],
                [
                    'text' => 'Features',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Excel Import/Export',
                            'url'  => 'Inventory/Features/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'Email',
                            'url'  => 'Inventory/Features/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                    ],
                ],
                [
                    'text' => 'System',
                    'icon' => 'fas fa-user',
                    'can' =>  ['Admin'],
                    'submenu' => [
                        [
                            'text' => 'Global',
                            'url'  => 'Inventory/System/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
                        ],
                        [
                            'text' => 'System',
                            'url'  => 'Inventory/System/Index',
                            'icon' => 'far fa-fw fa-file',
                            'can' =>  ['Admin']
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
            'can' =>  ['Admin'],
            'submenu' => [
                [
                    'text' => 'KYC',
                    'url' => 'B2cship/kyc',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',

                ],
                [
                    'text' => 'Status Details',
                    'icon' => 'far fa-fw fa-file',
                    'can' => ['Admin'],
                    'url' => 'B2cship/tracking_status/details'
                ],
                [
                    'text' => 'Booking Status',
                    'url' => 'B2cship/booking',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',
                ]
            ],
        ],
        [
            'text' => 'PDF Upload',
            'can' =>  ['Admin'],
            'submenu' => [
                [
                    'text' => 'BOE',
                    'url' => 'BOE/index',
                    'can' => ['Admin'],
                    'icon' => 'far fa-fw fa-file',

                ],
                [
                    'text' => 'View Uploded PDF',
                    'url' => 'BOE/uplod',
                    'can' => ['Admin'],
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
