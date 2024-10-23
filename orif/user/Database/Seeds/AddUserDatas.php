<?php


namespace User\Database\Seeds;


class AddUserDatas extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $data = [
            ['fk_user_type'=>1,'username'=>'admin','email'=>'admin@test.com','password'=>'$2y$10$uvix1BJUvu.acqlaNlsmieKlgWuYA7/MfwdHeSFjwvdeqIZtO/Utm'],
            ['fk_user_type'=>2,'username'=>'user','email'=>'user@test.com','password'=>'$2y$10$hrihONbH2MT.eh6MGkSZ5ekADbCGFBUGHz5YsXIFI3W4QPh.2N16W']
        ];
        foreach($data as $row)
        $this->db->table('user')->insert($row);
    }
}