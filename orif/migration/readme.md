# Migration module #

This module provides functionnality to execute migration files or rollback migration files from the user interface.

It's strongly recommended to delete this module after having executed all desired migrations.

## Installing in an existing project ##
1. Copy orif/migration folder to your project
2. Edit app/Config/Autoload.php file to add the "Migration" namespace :
```php
public $psr4 = [
        APP_NAMESPACE => APPPATH, // For custom app namespace
        'Config'      => APPPATH . 'Config',
        /* ... your other namespaces ... */
        'Migration'   => ROOTPATH.'orif/migration',
    ];
```

3. Edit app/Config/Filters.php to use the "MigrationFilter" :
```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
/* ... other filters to use ... */
use Migration\Filters\MigrationFilter;

class Filters extends BaseConfig
{
    public array $aliases = [
        /* ... other aliases ... */
        'migration'     => MigrationFilter::class,
    ];

    public array $globals = [
        'before' => [
            /* ... your other 'before' filters ... */
            'migration',
        ],
        'after' => [
            /* ... your 'after' filters */
        ],
    ];

    /* ... rest of the class ... */
}
```

## Version 4.0 ##

### 4.0 ###

- First release of this module
