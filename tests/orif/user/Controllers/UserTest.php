<?php
namespace User\Controllers;
use CodeIgniter\Test\CIUnitTestCase;

class UserTest extends CIUnitTestCase{
    public function testUserConnexionDeconnexion(){
        $client =\Config\Services::curlrequest();
        unlink(WRITEPATH.'curldebug.txt');
        //make authntication request
        $postResponse=$client->request('POST','https://localhost/ci_packbase_v4/public/user/auth/login',[
            'verify'=>false,
            'form_params'=>[
                'username'=>'admin',
                'password'=>'OrifInfo2009',
                'btn_login'=>'Se connecter'
                ]
            ,'cookie'=>WRITEPATH.'COOKIESAVER.txt','debug'=>WRITEPATH.'curldebug.txt']);
        //get page after login        
        $getresponse=$client->request('GET','https://localhost/ci_packbase_v4/public/',[
            'verify'=>false,'cookie'=>WRITEPATH.'COOKIESAVER.txt','debug'=>WRITEPATH.'curldebug.txt']);
        //see if administration button is displayed on the page    
        $this->assertTrue((boolval(strpos($getresponse->getBody(),'<a href="https://localhost/ci_packbase_v4/public/user/admin/list_user" >Administration</a>'))));
        //try to logout
        $getresponse=$client->request('GET','https://localhost/ci_packbase_v4/public/user/auth/logout',[
            'verify'=>false,'cookie'=>WRITEPATH.'COOKIESAVER.txt']);
        //get page after logout    
        $getresponse=$client->request('GET','https://localhost/ci_packbase_v4/public/',[
            'verify'=>false,'cookie'=>WRITEPATH.'COOKIESAVER.txt','debug'=>WRITEPATH.'curldebug.txt']); 

       //see if client is disconnected after logout
       $this->assertTrue((boolval(strpos($getresponse->getBody(),'<a href="https://localhost/ci_packbase_v4/public/user/auth/login" >Se connecter</a>'))));
   
        unlink(WRITEPATH.'COOKIESAVER.txt');
        
    }
}


?>