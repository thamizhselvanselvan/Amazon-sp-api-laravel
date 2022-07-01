<?php

use Illuminate\Support\Str;

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
            'prefix' => env('INVENTORY_DB_PREFIX') . '_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
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
            'prefix' => env('CATALOG_DB_PREFIX') . '_',
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
            'prefix' => env('ORDER_DB_PREFIX') . '_',
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
            'prefix' => env('SELLER_DB_PREFIX') . '_',
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
                'host' => [],
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
