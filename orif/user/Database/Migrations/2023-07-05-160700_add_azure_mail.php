<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAzureMail extends Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();

        $fields = [
            'azure_mail' =>[
                'type'              => 'VARCHAR',
                'constraint'        => '100',
                'null'              => true,
                'default'           => null,
                
                // Where to place the field
                'after'             => 'email',
            ],
        ];
        $forge->addColumn('user', $fields);
    }

    public function down()
    {
        // $this->forge->dropTable('user');
    }
}