<?php
// sanjay
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

class LoginsController extends AppController
{
    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->loadComponent('Cookie');
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['newindex', 'index', 'logout', 'forgot', 'forgetcpass', 'setpass']);
    }



    // new index page after login page submit.......
    public function newindex($db = null)
    {
        $user = $this->Auth->user();
        // if user already login
        if ($user) {
            $db = $this->request->session()->read('Auth.User.db');
            if ($db != DB_NAME) {
                $role = $this->request->session()->read('Auth.User.role_id');
                $this->request->session()->write('role', $role);
                $this->request->session()->write('redirectUrl', $this->referer());
                $this->Auth->logout();
                return $this->redirect('/logins');
            }
        }

        $this->loadModel('Schools');
        $schoolstatus = $this->Schools->find('all')->where(['school_database' => $db])->first();
        if ($schoolstatus['status'] == 'N') {
            echo '<img src="' . SITE_URL . 'img/awserror.png">';
            die;
        }
        // pr($schoolstatus);die;
        // To set db for login
        if (empty($db)) {
            $db = DB_NAME;
        }
        $this->set(compact('db'));

        // if user already login
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $user_db = $this->request->session()->read('Auth.User.db');
        // pr($user_db);die;


        if ($rolepresent == '6') { // branchAdmin
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
        } else if ($rolepresent == '105') { // ErpHead
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
        } else if ($rolepresent == '101') { // SuperAdmin
            return $this->redirect(['controller' => 'admin/Permission', 'action' => 'index']);
        }

        $this->viewBuilder()->layout('login');


        // after form submit
        if ($this->request->is('post')) {

            $conn = ConnectionManager::get('default');
            $mobile = $this->request->data['mobile'];
            $password = $this->request->data['password'];
            $db_names = DB_NAME;
            $user_detail = "SELECT * FROM `$db_names`.`users` where mobile='$mobile' and confirm_pass='$password'";
            $run_data = $conn->execute($user_detail)->fetch('assoc');
            $db = $run_data['db'];
            // pr($db);die;
            if (!empty($db) && $db != 'logins' && $db != $user_db) {
                ConnectionManager::config($db, [
                    'className' => 'Cake\Database\Connection',
                    'driver' => 'Cake\Database\Driver\Mysql',
                    'persistent' => false,
                    'host' => DBHOSTNAME,
                    'username' => MYSQLUSERNAME,
                    'password' => MYSQLPASSWORD,
                    'database' => $db,
                    'encoding' => 'utf8mb4',
                    'timezone' => 'UTC',
                    'cacheMetadata' => true,
                ]);
                ConnectionManager::drop('default');
                ConnectionManager::get($db);
                \Cake\Datasource\ConnectionManager::alias($db, 'default');
                date_default_timezone_set('Asia/Kolkata');
                $this->loadModel('Users');


                // for check duplicate mobile no
                $user_count_check = $this->Users->find('all')->where(['mobile' => $mobile])->count();
                //  pr($user_count_check);die;
                if ($user_count_check > 1) {
                    $this->Flash->error(__('Mobile number is already exist in other role and company. Contact to administrator'));
                    $this->Auth->logout();
                    return $this->redirect(['controller' => 'logins', 'action' => 'index']);
                }
            }


            $user = $this->Auth->identify();

            if ($user) {
                $this->Auth->setUser($user);

                if ($this->request->data['remember_me'] == 1) {
                    $this->Cookie->write('remember_me', $this->request->data['remember_me'], true, '1 month');
                    $this->Cookie->write('email', $this->request->data['email'], true, '1 month');
                    $this->Cookie->write('password', $this->request->data['password'], true, '1 month');
                } else {
                    $this->Cookie->write('remember_me', '', false, 1000);
                    $this->Cookie->write('email', '', false, 1000);
                    $this->Cookie->write('password', '', false, 1000);
                }


                $rolepresent = $this->request->session()->read('Auth.User.role_id');
                // pr($rolepresent);die;
                if ($rolepresent == '1') { // single branch Admin
                    return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
                } else if ($rolepresent == '6') { // Branch Admin
                    return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
                } else if ($rolepresent == '18') { // store Head
                    return $this->redirect(['controller' => 'admin/stockregister', 'action' => 'dailystock']);
                } else if ($rolepresent == '21') { // Store Staff
                    return $this->redirect(['controller' => 'admin/stockregister/', 'action' => 'dailystock']);
                } else if ($rolepresent == '22') { // Production Staff
                    return $this->redirect(['controller' => 'admin/production/', 'action' => 'productionorders']);
                } else if ($rolepresent == '23') { // Maintenance Staff
                    return $this->redirect(['controller' => 'admin/maintenance/', 'action' => 'index']);
                } else if ($rolepresent == '25') { // EMD Staff
                    return $this->redirect(['controller' => 'admin/emd/', 'action' => 'index']);
                } else if ($rolepresent == '26') { // paymentmanage Staff
                    return $this->redirect(['controller' => 'admin/paymentmanager/', 'action' => 'index']);
                } else if ($rolepresent == '101') { // SuperAdmin
                    return $this->redirect(['controller' => 'admin/Permission', 'action' => 'index']);
                } else if ($rolepresent == '102') { // Production Head
                    return $this->redirect(['controller' => 'admin/production/', 'action' => 'productionorders']);
                } else if ($rolepresent == '104') { // Maintenance Head
                    return $this->redirect(['controller' => 'admin/maintenance/', 'action' => 'index']);
                } else if ($rolepresent == '105') { // Center Head
                    return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
                } else if ($rolepresent == '106') { // Inspection Head
                    return $this->redirect(['controller' => 'admin/Inspection/', 'action' => 'index']);
                } else if ($rolepresent == '107') { // Inspection Staff
                    return $this->redirect(['controller' => 'admin/Inspection/', 'action' => 'index']);
                } else if ($rolepresent == '111') { // Indent Master
                    return $this->redirect(['controller' => 'admin/Indentpo/', 'action' => 'index']);
                } else {
                    $this->Flash->error(__('Invalid mobile no. or password, try again'));
                    $this->Auth->logout();
                    return $this->redirect(['controller' => 'logins', 'action' => 'index']);
                }

                $this->set('user', $user['user_name']);

                $roleidpast = $this->request->session()->read('role');

                if ($roleidpast == $rolepresent) {
                    return $this->redirect($this->request->session()->read('redirectUrl'));
                    $this->request->session()->delete('redirectUrl');
                    $this->request->session()->delete('role');
                } else {
                    return $this->redirect(['controller' => 'admin/dashboards', 'action' => 'index']);
                    $this->request->session()->delete('redirectUrl');
                    $this->request->session()->delete('role');
                }
            } else {
                $this->Flash->error(__('Invalid mobile no. or password, try again'));
                return $this->redirect($this->referer());
            }
        }

        $remember_me = $this->Cookie->read('remember_me');
        $email = $this->Cookie->read('email');
        $password = $this->Cookie->read('password');
        $this->set(compact('email', 'password', 'remember_me'));
    }




    // Index Page
    public function index($db = null)
    {
        $user = $this->Auth->user();
        // pr($user);die;
        // if user already login
        if ($user) {
            $db = $this->request->session()->read('Auth.User.db');
            if ($db != DB_NAME) {
                $role = $this->request->session()->read('Auth.User.role_id');
                $this->request->session()->write('role', $role);
                $this->request->session()->write('redirectUrl', $this->referer());
                $this->Auth->logout();
                return $this->redirect('/logins');
            }
        }

        $this->loadModel('Schools');
        $schoolstatus = $this->Schools->find('all')->where(['school_database' => $db])->first();
        if ($schoolstatus['status'] == 'N') {
            echo '<img src="' . SITE_URL . 'img/awserror.png">';
            die;
        }

        // To set db for login
        if (empty($db)) {
            $db = DB_NAME;
        }
        $this->set(compact('db'));

        // if user already login
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $user_db = $this->request->session()->read('Auth.User.db');



        if ($rolepresent == '6') { // branchAdmin
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'overview']);
        } else if ($rolepresent == '105') { // ErpHead
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'headbranch']);
        } else if ($rolepresent == '101') { // SuperAdmin
            return $this->redirect(['controller' => 'admin/Permission', 'action' => 'index']);
        } else if ($rolepresent == '1') { // single branch  Admin
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'headbranch']);
        } else if ($rolepresent == '18') { // Store Head
            return $this->redirect(['controller' => 'admin/Permission', 'action' => 'index']);
        } else if ($rolepresent == '105') { // ErpHead
            return $this->redirect(['controller' => 'admin/Dashboards/', 'action' => 'headbranch']);
        } else if ($rolepresent == '101') { // SuperAdmin
            return $this->redirect(['controller' => 'admin/Permission', 'action' => 'index']);
        }

        $this->viewBuilder()->layout('login');

        $email = $this->Cookie->read('email');
        $password = $this->Cookie->read('password');
        $this->set(compact('email', 'password', 'remember_me'));
    }



    // public function logout()
    // {
    //     $db = $this->request->session()->read('Auth.User.db');
    //     $role = $this->request->session()->read('Auth.User.role_id');
    //     // echo $role; die;
    //     $this->request->session()->write('role', $role);
    //     $this->request->session()->write('redirectUrl', $this->referer());
    //     $this->request->session()->destroy();
    //     $this->Auth->logout();
    //     return $this->redirect('/logins');
    // }


    public function logout()
    {
        $role = $this->request->session()->read('Auth.User.role_id');
        $redirectUrl = $this->referer();
        $this->request->session()->destroy();
        $this->Auth->logout();
        return $this->redirect('/logins');
    }

    public function forgot()
    {
        $this->viewBuilder()->layout('login');
        $email = $this->request->data['email'];
        $userDatas = $this->Users->find('all')->where(['email' => $email])->first();

        if (isset($userDatas) && !empty($userDatas)) {

            $fkey = rand(1, 10000);
            $conn = ConnectionManager::get('default');
            $detail = 'UPDATE `users` SET `fkey` ="' . $fkey . '" WHERE `users`.`email` ="' . $email . '"';
            $results = $conn->execute($detail);
            $mid = base64_encode(base64_encode($fkey));
            $url = SITE_URL . "logins/forgetcpass/" . $mid;
            $subject = 'Forgot Password';
            //set header
            $from = 'admin@idsprime.com';
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: <' . $from . '>' . "\r\n";
            //set message
            $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Mail</title>
            </head><body style="margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif">
            <div style=" text-align:left; font-size:15px; margin:40px 0;">
            <b><a style=" color:#2586a2;"  href="' . $url . '">Click here to reset your password</a></b>
            </div>
            ';

            echo mail($email, $subject, $message, $headers);
            $this->Flash->success(__('Your has been Updated on your Email ID.'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__("This Email ID doesn't exist in database"));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function forgetcpass()
    {
        $this->viewBuilder()->layout('forgetcpass');
    }

    public function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
