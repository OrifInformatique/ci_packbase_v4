<?php

namespace User\Database\Migrations;

class AddResetPassword extends \CodeIgniter\Database\Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();

        $fields = [
            'reset_password' => [
                'type'              => 'TINYINT',
                'default'           => false, // `0` in DB
                
                // Where to place the field
                'after'             => 'password',
            ],
        ];
        $forge->addColumn('user', $fields);
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        // $this->forge->dropTable('user');
        $this->forge->dropColumn('user', 'reset_password'); // to drop one single column
    }
}