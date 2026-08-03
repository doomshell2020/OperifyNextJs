<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;
include ROOT . DS . "vendor" . DS . "watimage/" . DS . "Watimage.php";
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
      //  pr($this->request->data); die;
        if ($this->request->data['mobile'] && $this->request->data['password']) {
           // echo "test"; die;
            $this->loadComponent('Auth', [  
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'mobile', 'password' => 'password'],
                    ],
                ],
    
            ]);
        }else{ 
        $this->loadComponent('Auth', [
            'storage' => 'Memory',
            'authenticate' => [
                'Form' => [
                    'scope' => ['Users.status' => 'Y'],
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'parameter' => 'token',
                    'userModel' => 'Users',
                    'fields' => [
                        'id' => 'userId',
                    ],
                    'queryDatasource' => true,
                ],
            ],
            'unauthorizedRedirect' => false,
            'checkAuthIn' => 'Controller.initialize',
        ]);
    }
    }

    public function beforeFilter(Event $event)
        {    
            parent::beforeFilter($event);
            $this->loadComponent('Cookie');
            $this->Auth->allow(['move_images','upload_images']);
            
        }
    
    
    
        public function move_images($k='',$folder=null)
        {   
       
          if(count($k['name'])==1){
       
            $filename=$k['name'];
            $ext=  end(explode('.', $filename));
            $name = md5(time($filename));
            $rnd=mt_rand();
            $imagename=trim($name.$rnd.$i.'.'.$ext," ");
            $img_name = $folder;
            $temp= $k['tmp_name'];
         //  pr($img_name); die;
            if(move_uploaded_file($temp,$img_name))
            {
              $kk[]=$imagename;
            }
          }
         return $kk;
      }      
       // product resize image
    public function upload_images($k = '',$sizeArray = '', $path,$phats)
    {
     
           $wm = new \Watimage();
           $wm->setImage($phats);
           $wm->setQuality(80);
           $wm->resize(array('type' => 'resize', 'size' => $sizeArray));
           $wm->generate($path . $k);   
    //pr($wm); die;
    }

}
