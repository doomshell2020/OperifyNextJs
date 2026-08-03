<?php //hello sanjay
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

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

include '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
include '../vendor/phpmailer/phpmailer/src/Exception.php';
include '../vendor/phpmailer/phpmailer/src/SMTP.php';
include '../vendor/phpmailer/phpmailer/src/OAuth.php';

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

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        //$this->loadComponent('Csrf');
        $this->loadComponent('Flash');
        $this->set('BASE_URL', Configure::read('BASE_URL'));

        if ($this->request->data['email'] && $this->request->data['password']) {
            //  if ($this->request->data['email']) {

            $this->loadComponent('Auth', [
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'email', 'password' => 'password'],
                    ],
                ],

            ]);
        } else {
            $this->loadComponent('Auth', [
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'mobile', 'password' => 'password'],
                    ],
                ],

            ]);
        }



        $role_id = $this->request->session()->read('Auth.User.role_id');
        $community = $this->request->session()->read('Auth.User.db');

        $company_id = $this->request->session()->read('Auth.User.c_id');
        if ($role_id != 101 && $role_id != null) {
            ConnectionManager::drop('default');
            ConnectionManager::drop($community);
            ConnectionManager::config($community, [
                'className' => 'Cake\Database\Connection',
                'driver' => 'Cake\Database\Driver\Mysql',
                'persistent' => false,
                'host' => DBHOSTNAME,
                'username' => MYSQLUSERNAME,
                'password' => MYSQLPASSWORD,
                'database' => $community,
                'encoding' => 'utf8mb4',
                'timezone' => 'UTC',
                'cacheMetadata' => true,
            ]);

            ConnectionManager::get($community);
            \Cake\Datasource\ConnectionManager::alias($community, 'default');
            date_default_timezone_set('Asia/Kolkata');
        }
    }

    public function connection($dbs)
    {
        //    echo $dbs; die;
        ConnectionManager::config($dbs, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => DBHOSTNAME,
            'username' => MYSQLUSERNAME,
            'password' => MYSQLPASSWORD,
            'database' => $dbs,
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ]);
        ConnectionManager::drop('default');
        ConnectionManager::get($dbs);
        \Cake\Datasource\ConnectionManager::alias($dbs, 'default');
    }

    public $paginate = ['limit' => 50];

    public function sitesetting($name = null)
    {
        // pr($name);
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('Template');
        $this->loadModel('Users');
        $board = $this->request->session()->read('Auth.User.board');
        $sitesettingdetail = $this->Sitesettings->find('all')->first();
        $sitesetting = $this->SitesettingsDetails->find('all')->where(['board_id' => $board])->first();
        if (empty($sitesetting)) {
            $sitesetting = $this->SitesettingsDetails->find('all')->first();
        }
        $sitesetting['sitetitle'] = $sitesettingdetail['site_title'];
        $sitesetting['sign'] = $sitesetting['sign'];
        $id = $sitesettingdetail[$name];
        // pr($sitesettingdetail);die;
        $this->set(compact('sitesetting'));
        $det = $this->Template->find('all')->where(['id' => $id])->first();
        $this->set(compact('det'));
    }


    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */

    public function beforeRender(Event $event)
    {
        if (
            !array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    public function is_LoggedIn()
    {
        $logged_in = false;
        if ($this->request->session()->read('Auth.User.role_id')) {

            $logged_in = true;
        }
        return $logged_in;
    }

    public function academicyear()
    {
        $this->loadModel('AcademicYear');
        $acd = $this->AcademicYear->find('list', ['keyField' => 'academicyear', 'valueField' => 'academicyear'])->toarray();
        return $acd;
    }

    public function currentacademicyear()
    {
        $this->loadModel('Users');
        $user = $this->Users->find('all')->where(['Users.role_id IN ' => ['1', '105']])->first();
        return $user['academic_year'];
    }

    public function check_permission()
    {
        $controller = $this->request->params['controller'];
        $role = $this->request->session()->read('Auth.User.role_id');
        if ($role == 0) {
            return true;
        }

        if ($controller == 'Mobile') {
            return true;
        }

        if ($controller == 'Logins') {
            return true;
        }

        if ($controller == 'PermissionModules') {
            return true;
        }

        $this->loadModel('PermissionModules');
        $this->loadModel('Permissions');
        $userTable = TableRegistry::get('PermissionModules');
        $exists = $userTable->exists(['action' => $this->request->params['action'], 'controller' => $this->request->params['controller']]);
        if ($exists) {
            $permissionModulesid = $this->PermissionModules->find('all')->where(['action' => $this->request->params['action'], 'controller' => $this->request->params['controller']])->order(['id' => 'DESC'])->first();

            $userTable2 = TableRegistry::get('Permissions');
            $exists2 = $userTable2->exists(['module_id' => $permissionModulesid['id'], 'role_id' => $this->request->session()->read('Auth.User.role_id')]);
            if ($exists2) {
            } else {
                $this->request->data['module_id'] = $permissionModulesid['id'];
                $this->request->data['role_id'] = $this->request->session()->read('Auth.User.role_id');
                $permission = $this->Permissions->newEntity();
                $permissionmodules3 = $this->Permissions->patchEntity($permission, $this->request->data);
                $this->Permissions->save($permissionmodules3);
            }
        } else {

            $this->request->data['action'] = $this->request->params['action'];
            $this->request->data['controller'] = $this->request->params['controller'];
            $permissionmodules = $this->PermissionModules->newEntity();

            $previousduefees = $this->PermissionModules->patchEntity($permissionmodules, $this->request->data);
            $this->PermissionModules->save($previousduefees);
            $permissionModules = $this->PermissionModules->find('all')->order(['id' => 'DESC'])->first();

            $this->request->data['module_id'] = $permissionModules['id'];
            $this->request->data['role_id'] = $this->request->session()->read('Auth.User.role_id');

            $permission = $this->Permissions->newEntity();
            $permissionmodules = $this->Permissions->patchEntity($permission, $this->request->data);
            $this->Permissions->save($permissionmodules);
        }
    }

    public function checkfinalpermission()
    {
        $this->loadModel('PermissionModules');
        $this->loadModel('Permissions');
        if ($this->is_LoggedIn()) {
            $role = $this->request->session()->read('Auth.User.role_id');
        } else {
            $role = 0;
        }

        $controller = $this->request->params['controller'];
        $action = $this->request->params['action'];

        if ($role == 0) {
            return true;
        }

        if ($controller == 'Mobile') {
            return true;
        }

        if ($controller == 'Logins') {
            return true;
        }

        if ($action == 'login') {
            return true;
        }

        if ($action == 'logout') {
            return true;
        }

        $action = $this->request->params['action'];

        if ($action == 'errorfound' || $action == 'admin_syncronize_actions') {
            return true;
        }

        $moduleId = $this->PermissionModules->find('all')->where(['action' => $this->request->params['action'], 'controller' => $this->request->params['controller']])->order(['id' => 'DESC'])->first();

        //if($moduleId['id']=='') return true;

        $modulePermission = $this->Permissions->find('all')->select(['permission'])->where(['module_id' => $moduleId['id'], 'role_id' => $this->request->session()->read('Auth.User.role_id')])->order(['id' => 'DESC'])->first();
        // pr($modulePermission); die;

        if ($modulePermission['permission'] == 'Y') {
            return true;
        } else {

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '5' || $rolepresent == '8') {

                return $this->redirect(['controller' => 'Studentfees', 'action' => 'view']);
            } else if ($rolepresent == '6') {

                return $this->redirect(['controller' => 'Employeeattendance', 'action' => 'rolesubstitute']);
            } else if ($rolepresent == '7') {

                return $this->redirect(['controller' => 'Issuebooks', 'action' => 'index']);
            } else if ($rolepresent == '4') {

                return $this->redirect(['controller' => 'Studentexamresult', 'action' => 'examcontrolview']);
            } else if ($rolepresent == '13') {

                return $this->redirect(['controller' => 'Studentexamresult', 'action' => 'examcontrolview']);
            } else if ($rolepresent == '3') {
                $this->loadModel('ClasstimeTabs');
                $this->loadModel('Classections');
                $tid = $this->request->session()->read('Auth.User.tech_id');
                $csec = $this->ClasstimeTabs->find('all')->where(['FIND_IN_SET(\'' . $tid . '\',ClasstimeTabs.employee_id)'])->toarray();

                foreach ($csec as $key => $value) {
                    $csection = $this->Classections->find('all')->contain(['Classes', 'Sections'])->where(['Classections.id' => $value['class_id']])->order(['Classections.id' => 'ASC'])->first();
                    $classs[] = $csection['class_id'];
                }

                $cls = array_unique($classs);
                $klp = array('18', '19', '1', '2', '3', '4', '6');
                $rt = array_intersect($cls, $klp);

                if (!empty($rt)) {
                    return $this->redirect(['controller' => 'Primarycentral', 'action' => 'primaryexamcontrolview']);
                } else {

                    return $this->redirect(['controller' => 'Studentexamresult', 'action' => 'examcontrolviewsubject']);
                }
            } else if ($rolepresent == '12') {

                return $this->redirect(['controller' => 'Primarycentral', 'action' => 'primaryindex']);
            } else if ($rolepresent == '1') {

                return $this->redirect(['controller' => 'dashboards', 'action' => 'index']);
            } else {

                return $this->redirect(['controller' => 'Error', 'action' => 'errorfound']);
            }
        }
    }

    public function getHTML($url, $timeout)
    {
        $ch = curl_init($url); // initialize curl with given url
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
        return @curl_exec($ch);
    }

    public function getbranchdetail()
    {
        $dbname = $this->request->data['dbname'];
        $conn = ConnectionManager::get('default');
        // if ($dbname == "tirupati") {
        //     $role_id = "105";
        //     $NTSQL = "SELECT * FROM $dbname.`users` WHERE `role_id`= '$role_id'";
        // } else {
        //     $role_id = "6";
        // }

        $role_id = "6";
        $NTSQL = "SELECT * FROM $dbname.`users` WHERE `role_id`= '$role_id'";

        $user_data = $conn->execute($NTSQL)->fetchAll('assoc');
        $rec_data['email'] = $user_data[0]['email'];
        $rec_data['confirm_pass'] = $user_data[0]['confirm_pass'];
        echo json_encode($rec_data);
        die;
    }

    public function erpLogin()
    {
        if ($this->request->is('post')) {
            $db = $this->request->data['dbname'];
            $user_db = $this->request->session()->read('Auth.User.db');
            $user_id = '105';
            $_SESSION['checked_by'] = $user_id;
            if (!empty($db) && $db != $user_db) {
                ConnectionManager::config($db, [
                    'className' => 'Cake\Database\Connection',
                    'driver' => 'Cake\Database\Driver\Mysql',
                    'persistent' => false,
                    'host' => DBHOSTNAME,
                    // 'port'=>'3306',
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
                // $user = $this->Auth->identify();
                $user = $this->Users->find()->where(['role_id' => 105])->first();
                // $user_db = $this->request->session()->read('Auth.User.db');
                // pr($user_db);die;
            }
            if ($user) {
                $this->Auth->setUser($user);
            }
            // pr($user);die;
            echo json_encode($user);
            die;
        }
    }

    public function financialyear()
    {
        $year = date('Y');
        $nextyear = date('y');
        $month = date('m');
        if ($month < 4) {
            $year = $year - 1;
        } else {
            $year = $year;
        }
        if ($month < 4) {
            $nextyear = $nextyear - 1;
        } else {
            $nextyear = $nextyear + 1;
        }
        $aca_year = $year . "-" . $nextyear;
        return $aca_year; //die;
    }

    public function whatsappmsg($to = null, $message = null, $instance_id = null, $whatsapp_token = null)
    {
        // $instance_id = 'instance30027';
        // $token = 'hwg41vila1ybdcd9';

        $instance_id = 'instance29351';
        $token = 'urpbsza8p1hiwk3j';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ultramsg.com/$instance_id/messages/chat",

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "token=$token&to=$to&body=$message&priority=10&referenceId=",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // if ($err) {
        //  echo "cURL Error #:" . $err; die;
        // } else { 
        //   echo $response; die;
        // }
    }


    //new super admin msg api  
    public function supperadminwhatsapp($to = null, $message = null)
    {
        $instance_id = 'instance30027';
        $token = 'hwg41vila1ybdcd9';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ultramsg.com/$instance_id/messages/chat",

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "token=$token&to=$to&body=$message&priority=10&referenceId=",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // if ($err) {
        //  echo "cURL Error #:" . $err; die;
        // } else { 
        //   echo $response; die;
        // }
    }

    ///------------------------Url hold-----------------------------//
    public function beforeFilter(Event $event)
    {
        $user = $this->Auth->user(); // Assuming you are using CakePHP's Auth component
        $user_id = $user['id'];

        // Check user permissions
        $controller = $this->request->param('controller');
        $action = $this->request->param('action');


        if ($controller == 'Logins' && $action == 'logout') {
            return $this->redirect('/logins');
        }
        // else if ($user_id) {

        //     $isAllowed = $this->checkUserPermissions($controller, $action, $user_id);

        //     if (!$isAllowed && $user) {
        //         return $this->redirect(['controller' => 'dashboards', 'action' => 'overview']);
        //         $this->Flash->error(__('You are not authorized'));
        //     }
        // }
    }

    public function checkUserPermissions($controller, $action, $user_id)
    {
        $this->loadModel('PermissionModules');
        if ($controller == 'Dashboards' && $action == 'overview') {
            return true;
        }

        $search_action = [
            'viewcontractdetail',
            'viewcontractdetailspdf',
            'editaddbom',
            'edit',
            'delete',
            'searchitem',
            'search',
            'add',
            'viewdesignsheet',
            'viewdesignsheetpdf',
            'indentitems',
            'viewpodetail',
            'viewpodetailspdf',
            'deliverynote',
            'grnexcel',
            'viewgrndetail',
            'searchpodetail',
            'productionorderexcel',
            'addproductionorders',
            'getcontract',
            'getcontractfinished',
            'finisheditems',
            'viewproductiondetails',
            'delete',
            'deletedailysheet',
            'dailysheetexcel',
            'viewindentpodetail',
            'viewindentpopdf',
            'indentpoexcel',
            'getdesignsheetdetails',
            'getname',
            'vendorsreport',
            'view',
            'viewpdf',
            'viewitemexcel',
            'getitemdetail',
            'maintenancestatus',
            'status',
            'getdailysheet',
            'getproductionorder',
            'getproductprocess',
            'viewproductionpdf',
            'checkprocesscompletion',
            'gettax',
            'getvendorname',
            'getitemname',
            'posummaryreport',
            'summaryexcel',
            'getfinisheditemname',
            'deletedata',
            'getitemsname',
            'checkdeliverynote',
            'purchaseorderitems',
            'viewitemdetail',
            'viewitemdetailpdf',
            'updategrnquery',
            'addsupplier',
            'viewreverseindent',
            'viewreverseindentpdf',
            'excel',
            'getmachinereading',
            'checkpono',
            'searchstockregister',
            'searchstock',
            'detailedexcel',
            'dailystockexcel',
            'getbomfinshedproduct',
            'checkdesignsheetitem',
            'viewexpenditure',
            'viewreverse',
            'getbillno',
            'getgrndetails',
            'getcompletepo',
            'getcategoryindent',
            'viewdetail',
            'deliveryreport',
            'productcomparisonreport',
            'printdeliveryschedule',
            'getbranchdetail',
            'erp_login',
            'printallpo',
            'revised'
        ];
        if (in_array($action, $search_action)) {
            return true;
        }
        $search_controller = ['Permissionmodules', 'Users'];

        if (in_array($controller, $search_controller)) {
            return true;
        }

        $moduleId = $this->PermissionModules->find('all')
            ->where([
                'PermissionModules.controller' => $controller,
                'PermissionModules.action' => $action,
                'PermissionModules.user_id' => $user_id
            ])
            ->order(['id' => 'DESC'])
            ->first();
        return ($moduleId !== null);
    }




    public function sendNotification($token, $title, $message)
    {


        $serviceAccountFile = WWW_ROOT . 'operify-cad4a-d47af56c4f8a.json';
        $accessToken = $this->getGoogleAccessToken($serviceAccountFile);
        $url = 'https://fcm.googleapis.com/v1/projects/operify-cad4a/messages:send';

        $this->loadModel('Users');
        // $user = $this->Users->find('all')->where(['id' => $userId])->first();
        // $token = $user['token'];
        // $token = 'fgiF6ceARVedYrA0ywIHb_:APA91bGB9VKAgzFk6DkxV_SNpgjFAjfVrzxWqzbluglE2BDICfjAzkR5WOqg2scQ6Cc20BAE4N2ZcGBBRtWVqFLcaN-F1mRlIE6HE2oe_HEyWebsxb009ML0VDC5Z_ZCZJ1ACA4HxGeU';

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
                'data' => [
                    'key1' => 'value1',
                    'key2' => 'value2',
                ],
            ],
        ];

        $jsonMessage = json_encode($message);

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonMessage);

        $response = curl_exec($ch);

        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }

        curl_close($ch);

        // echo $response;
    }
    private function getGoogleAccessToken($serviceAccountFile)
    {
        $tokenUri = 'https://oauth2.googleapis.com/token';
        $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);

        $jwtHeader = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $jwtClaimSet = base64_encode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $tokenUri,
            'exp' => time() + 3600,
            'iat' => time(),
        ]));

        $signature = '';
        openssl_sign($jwtHeader . '.' . $jwtClaimSet, $signature, $serviceAccount['private_key'], 'sha256WithRSAEncryption');
        $jwt = $jwtHeader . '.' . $jwtClaimSet . '.' . base64_encode($signature);

        $postFields = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response['access_token'])) {
            return $response['access_token'];
        } else {
            die('Error fetching OAuth 2.0 token: ' . $result);
        }
    }


    public function send_email($to, $subject, $message, $filename = null)
    {


        $mail = new \PHPMailer(True);
        $mail->isSMTP();
        // $mail->SMTPDebug = \SMTP::DEBUG_SERVER;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'contact@doomshell.com';
        $mail->Password = 'zxrz ypql enpw npxo';
        $mail->SMTPSecure = \PHPMailer::ENCRYPTION_STARTTLS;

        //  $mail->tls = true;
        $mail->Port = 587;
        $mail->setFrom('contact@doomshell.com', 'Operify');

        // echo $to; die;
        if ($filename != "") {
            $mail->addAttachment($filename);
        }
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        // $test= $mail->send();
        // pr($test); die;
        if (!$mail->send()) {
            return 0;
        }
        return 1;
        // exit;

    }

    // for block emojis
    public function removeEmojis($text)
    {
        return preg_replace('/[\x{1F600}-\x{1F64F}' .
            '\x{1F300}-\x{1F5FF}' .
            '\x{1F680}-\x{1F6FF}' .
            '\x{2600}-\x{26FF}' .
            '\x{2700}-\x{27BF}]++/u', '', $text);
    }
}
