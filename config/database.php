<?php

use Illuminate\Support\Str;
use App\Models\Admin\Backup;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    'bombino_account_id' => env('BOMBINO_ACCOUNT_ID'),
    'bombino_user_id' => env('BOMBINO_USER_ID'),
    'bombino_password' => env('BOMBINO_PASSWORD'),

    'smsa_password' => env('SMSA_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'web' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('WEB_DB_HOST', '127.0.0.1'),
            'port' => env('WEB_DB_PORT', '3306'),
            'database' => env('WEB_DB_DATABASE', 'forge'),
            'username' => env('WEB_DB_USERNAME', 'forge'),
            'password' => env('WEB_DB_PASSWORD', ''),
            'unix_socket' => env('WEB_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('WEB_DB_PREFIX') . '_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'dump' => [
                'dump_binary_path' => env('MYSQL_DUMP_BINARY_PATH', '/usr/bin/'),
                'excludeTables' => [
                    'sp_asin_masters',
                    'sp_failed_jobs',
                    'sp_jobs',
                    'sp_model_has_permissions',
                    'sp_other_catalog_asins',
                    'sp_rate_masters',
                    'sp_universal_textiles',
                ],
                'useSingleTransaction' => true,
            ],
        ],

        'inventory' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('INVENTORY_DB_HOST', '127.0.0.1'),
            'port' => env('INVENTORY_DB_PORT', '3306'),
            'database' => env('INVENTORY_DB_DATABASE', 'forge'),
            'username' => env('INVENTORY_DB_USERNAME', 'forge'),
            'password' => env('INVENTORY_DB_PASSWORD', ''),
            'unix_socket' => env('INVENTORY_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('INVENTORY_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
          
            'dump' => [
                'dump_binary_path' => env('MYSQL_DUMP_BINARY_PATH', '/usr/bin/'),
                'excludeTables' => [
                    'in_catalogs',
                    'in_states',
                    'in_countries',
                    'in_cities',
                ],
                'useSingleTransaction' => true,
            ],
        ],

        'catalog' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('CATALOG_DB_HOST', '127.0.0.1'),
            'port' => env('CATALOG_DB_PORT', '3306'),
            'database' => env('CATALOG_DB_DATABASE', 'forge'),
            'username' => env('CATALOG_DB_USERNAME', 'forge'),
            'password' => env('CATALOG_DB_PASSWORD', ''),
            'unix_socket' => env('CATALOG_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('CATALOG_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'order' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ORDER_DB_HOST', '127.0.0.1'),
            'port' => env('ORDER_DB_PORT', '3306'),
            'database' => env('ORDER_DB_DATABASE', 'forge'),
            'username' => env('ORDER_DB_USERNAME', 'forge'),
            'password' => env('ORDER_DB_PASSWORD', ''),
            'unix_socket' => env('ORDER_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('ORDER_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'order_no_prefix' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ORDER_DB_NO_PREFIX_HOST', '127.0.0.1'),
            'port' => env('ORDER_DB_NO_PREFIX_PORT', '3306'),
            'database' => env('ORDER_DB_NO_PREFIX_DATABASE', 'forge'),
            'username' => env('ORDER_DB_NO_PREFIX_USERNAME', 'forge'),
            'password' => env('ORDER_DB_NO_PREFIX_PASSWORD', ''),
            'unix_socket' => env('ORDER_DB_NO_PREFIX_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('ORDER_DB_NO_PREFIX_PREFIX') . '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'seller' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('SELLER_DB_HOST', '127.0.0.1'),
            'port' => env('SELLER_DB_PORT', '3306'),
            'database' => env('SELLER_DB_DATABASE', 'forge'),
            'username' => env('SELLER_DB_USERNAME', 'forge'),
            'password' => env('SELLER_DB_PASSWORD', ''),
            'unix_socket' => env('SELLER_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('SELLER_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'buybox' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('BB_DB_HOST', '127.0.0.1'),
            'port' => env('BB_DB_PORT', '3306'),
            'database' => env('BB_DB_DATABASE', 'forge'),
            'username' => env('BB_DB_USERNAME', 'forge'),
            'password' => env('BB_DB_PASSWORD', ''),
            'unix_socket' => env('BB_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('BB_DB_PREFIX') . '_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'aws' => [
            'driver' => 'mysql',
            'read' => [
                'host' => [
                    env('AWS_DB_HOST', '127.0.0.1')
                ],
            ],
            'write' => [
                'host' => [
                    env('AWS_DB_HOST', '127.0.0.1')
                ],
            ],
            'port' => env('AWS_DB_PORT', '3306'),
            'database' => env('AWS_DB_DATABASE', ''),
            'username' => env('AWS_DB_USERNAME', ''),
            'password' => env('AWS_DB_PASSWORD', ''),
            'unix_socket' => env('AWS_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('AWS_DB_PREFIX', ''),
            'prefix_indexes' => false,
            'strict' => false,
            'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',
        ],

        'b2cship' => [
            'driver' => 'sqlsrv',
            'read' => [
                'host' => [
                    env('MSSQL_DB_HOST', '127.0.0.1')
                ],
            ],
            'write' => [
                'host' => [],
            ],
            'port' => env('MSSQL_DB_PORT', '3306'),
            'database' => env('MSSQL_DB_DATABASE', ''),
            'username' => env('MSSQL_DB_USERNAME', ''),
            'password' => env('MSSQL_DB_PASSWORD', ''),
            'charset' => 'utf8',
        ],

        'shipntracking' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('SNT_DB_HOST', '127.0.0.1'),
            'port' => env('SNT_DB_PORT', '3306'),
            'database' => env('SNT_DB_DATABASE', 'forge'),
            'username' => env('SNT_DB_USERNAME', 'forge'),
            'password' => env('SNT_DB_PASSWORD', ''),
            'unix_socket' => env('SNT_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('SNT_DB_PREFIX') . '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'business' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('BUSINESS_ORDER_DB_HOST', '127.0.0.1'),
            'port' => env('BUSINESS_ORDER_DB_PORT', '3306'),
            'database' => env('BUSINESS_ORDER_DB_DATABASE', 'forge'),
            'username' => env('BUSINESS_ORDER_DB_USERNAME', 'forge'),
            'password' => env('BUSINESS_ORDER_DB_PASSWORD', ''),
            'unix_socket' => env('BUSINESS_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('BUSINESS_ORDER_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mongodb' => [
            'driver' => 'mongodb',
            'host' => env('MONGO_DB_HOST', 'localhost'),
            'port' => env('MONGO_DB_PORT', 27017),
            'database' => env('MONGO_DB_DATABASE', 'zoho'),
            'username' => env('MONGO_DB_USERNAME'),
            'password' => env('MONGO_DB_PASSWORD'),
            'options' => [
                // here you can pass more settings to the Mongo Driver Manager
                // see https://www.php.net/manual/en/mongodb-driver-manager.construct.php under "Uri Options" for a list of complete parameters that you can use

                // 'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'), // required with Mongo 3+
            ],
        ],

        'cliqnshop' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('CLIQNSHOP_DB_HOST', '127.0.0.1'),
            'port' => env('CLIQNSHOP_DB_PORT', '3306'),
            'database' => env('CLIQNSHOP_DB_DATABASE', 'forge'),
            'username' => env('CLIQNSHOP_DB_USERNAME', 'forge'),
            'password' => env('CLIQNSHOP_DB_PASSWORD', ''),
            'unix_socket' => env('CLIQNSHOP_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('CLIQNSHOP_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'buybox_stores' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('BUYBOX_STORES_DB_HOST', '127.0.0.1'),
            'port' => env('BUYBOX_STORES_DB_PORT', '3306'),
            'database' => env('BUYBOX_STORES_DB_DATABASE', 'forge'),
            'username' => env('BUYBOX_STORES_DB_USERNAME', 'forge'),
            'password' => env('BUYBOX_STORES_DB_PASSWORD', ''),
            'unix_socket' => env('BUYBOX_STORES_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('BUYBOX_STORES_DB_PREFIX'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'oms' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('OMS_DB_HOST', '127.0.0.1'),
            'port' => env('OMS_DB_PORT', '3306'),
            'database' => env('OMS_DB_DATABASE', 'forge'),
            'username' => env('OMS_DB_USERNAME', 'forge'),
            'password' => env('OMS_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

        'queue' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_QUEUE_DB', '2'),
        ],

        'session' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_SESSION_DB', '3'),
        ],

        'horizon' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_SESSION_DB', '3'),
            'options' => [
                'prefix' => ''
            ]
        ],

    ],

];
