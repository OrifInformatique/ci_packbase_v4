<?php
namespace Migration\Config;

/** 
 * Configuration for the Migration module
 */
class MigrationConfig extends \CodeIgniter\Config\BaseConfig
{
    // The hash of the password to access the migration page
    public $migrationpass='$2y$10$iKUKZXR.9wIMF.4Kc3hpgeknFJTNUZJr/5rk6ZcCVz2YiVdkz4Tsq';

    // The path to the writable files to store the migration status
    public $writablePath=ROOTPATH.'orif/migration/Writable';

    // The codes to represent the migration status
    public $migrate_status_not_migrated=0;
    public $migrate_status_migrated=1;
    public $migrate_status_removed=2;
}