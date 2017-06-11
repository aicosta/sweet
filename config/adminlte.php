<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The default title of your admin panel, this goes into the title tag
    | of your page. You can override it per page with the title section.
    | You can optionally also specify a title prefix and/or postfix.
    |
    */

    'title' => '',

    'title_prefix' => 'Fullhub Sweet - ',

    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | This logo is displayed at the upper left corner of your admin panel.
    | You can use basic HTML here if you want. The logo has also a mini
    | variant, used for the mini side bar. Make it 3 letters or so
    |
    */

    'logo' => '<b>SW</b>EET',

    'logo_mini' => '<b>S</b>WT',

    /*
    |--------------------------------------------------------------------------
    | Skin Color
    |--------------------------------------------------------------------------
    |
    | Choose a skin color for your admin panel. The available skin colors:
    | blue, black, purple, yellow, red, and green. Each skin also has a
    | ligth variant: blue-light, purple-light, purple-light, etc.
    |
    */

    'skin' => 'purple',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Choose a layout for your admin panel. The available layout options:
    | null, 'boxed', 'fixed', 'top-nav'. null is the default, top-nav
    | removes the sidebar and places your menu in the top navbar
    |
    */

    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Collapse Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we choose and option to be able to start with a collapsed side
    | bar. To adjust your sidebar layout simply set this  either true
    | this is compatible with layouts except top-nav layout option
    |
    */

    'collapse_sidebar' => false,

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Register here your dashboard, logout, login and register URLs. The
    | logout URL automatically sends a POST request in Laravel 5.3 or higher.
    | You can set the request to a GET or POST with logout_method.
    | Set register_url to null if you don't want a register link.
    |
    */

    'dashboard_url' => '/home',

    'logout_url' => 'logout',

    'logout_method' => null,

    'login_url' => 'login',

    'register_url' => 'register',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Specify your menu items to display in the left sidebar. Each menu item
    | should have a text and and a URL. You can also specify an icon from
    | Font Awesome. A string instead of an array represents a header in sidebar
    | layout. The 'can' is a filter on Laravel's built in Gate functionality.
    |
    */

    'menu' => [
        'Menu Principal',
        [
            'text'    => 'Dashboard',
            'icon'    => 'dashboard',
            'url'  => '/home',
        ],
        [
            'text'    => 'Fornecedores',
            'icon'    => 'industry',
            'submenu' => [
                [
                    'text' => 'Buscar Fornecedores',
                    'url'  => '#',
                ],
                [
                    'text' => 'Cradastrar Fornecedor',
                    'icon' => 'plus',
                    'url'  => '#',
                ],
            ],
        ],
        [
            'text'    => 'Produtos',
            'icon'    => 'external-link',
            'submenu' => [
                [
                    'text' => 'Buscar Produtos',
                    'url'  => '/products',
                ],
                [
                    'text' => 'Cradastrar Produto',
                    'icon' => 'plus',
                    'url'  => '/products/create',
                ],
                [
                    'text' => 'Ações em massa',
                    'icon' => 'crosshairs',
                    'url'  => '/products/batch',
                ],
            ],
        ],
        [
            'text'    => 'Controle de estoque',
            'icon'    => 'dot-circle-o',
            'submenu' => [
                [
                    'text' => 'Relatório Geral',
                    'url'  => '/stock/list',
                ],
                [
                    'text' => 'Entrada / Saída manual',
                    'url'  => '/stock/create',
                ]
            ],
        ],
        [
            'text'    => 'Pedidos',
            'icon'    => 'dropbox',
            'submenu' => [
                [
                    'text' => 'Buscar Pedidos',
                    'url'  => '/orders',
                ],
                [
                    'text' => 'Pedidos Por Status',
                    'url'  => '/orders/byStatus/',
                ],
                [
                    'text' => 'Comprar ou faturar?',
                    'url'  => '/orders/buy-or-invoice/1',
                ],
                [
                    'text' => 'Gerador de Relatório',
                    'url'  => '/orders/list',
                ],
                [
                    'text' => 'Liberar Faturas Por NFF',
                    'url'  => '/nff',
                ],

            ],
        ],
        [
            'text'    => 'Faturamento',
            'icon'    => 'building-o',
            'submenu' => [
                [
                    'text' => 'Faturas em Aberto',
                    'url'  => '/invoice',
                ]
            ],
        ],
        [
            'text'    => 'Etiquetas',
            'icon'    => 'truck',
            'submenu' => [
                [
                    'text' => 'Emitir Etiquetas',
                    'url'  => '/tags',
                ],
                [
                    'text' => 'Pedidos Mercado Envios',
                    'url'  => '/tags/me2',
                ]
            ],
        ],
        [
            'text'    => 'Marketplaces',
            'icon'    => 'truck',
            'submenu' => [
                [
                    'text' => 'Mobly',
                    'url'  => '/tags',
                ]
            ],
        ],
        [
            'text'    => 'Outras Funcionalidades',
            'icon'    => 'asterisk',
            'submenu' => [
                [
                    'text' => 'Transportadoras',
                    'icon'    => 'truck',
                    'url'  => '/tags',
                ],
                [
                    'text' => 'Leitor de XMLs',
                    'icon'    => 'truck',
                    'url'  => '#',
                ]
            ],
        ]
        /*'LABELS',
        [
            'text'       => 'Important',
            'icon_color' => 'red',
        ],
        [
            'text'       => 'Warning',
            'icon_color' => 'yellow',
        ],
        [
            'text'       => 'Information',
            'icon_color' => 'aqua',
        ],*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Choose what filters you want to include for rendering the menu.
    | You can add your own filters to this array after you've created them.
    | You can comment out the GateFilter if you don't want to use Laravel's
    | built in Gate functionality
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Choose which JavaScript plugins should be included. At this moment,
    | only DataTables is supported as a plugin. Set the value to true
    | to include the JavaScript file from a CDN via a script tag.
    |
    */

    'plugins' => [
        'datatables' => true,
    ],
];
