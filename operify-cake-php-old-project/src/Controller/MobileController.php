<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Firebase\JWT\JWT;
use Cake\Event\Event;
use Cake\Utility\Security;
use RNCryptor\RNCryptor\Decryptor;
use RNCryptor\RNCryptor\Encryptor;
use App\Controller\Component\WhatsAppComponent;

class MobileController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Cookie');
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.

        $this->Auth->allow([
            'login',
            'dashboard',
            'grn',
            'allgrn',
            'purchaseorder',
            'vendor',
            'allvendors',
            'indent',
            'reverse',
            'getreverse',
            'production',
            'contract',
            'getcontract',
            'vendorsreport',
            'searchvendorname',
            'searchcontractname',
            'searchcontract',
            'searchpurchaseorder',
            'searchgrn',
            'searchproduction',
            'searchindent',
            'searchreverse',
            'searchvendorsreport',
            'searchvendor',
            'stock',
            'allpo',
            'uploadToken',
            'grnpopup',
            'contractpdf',
            'productionorderpdf',
            'reversepdf',
            'indentpdf',
            'purchaseorderpdf',
            'maintenance',
            'searchstock',
            'searchitemname',
            'versioncheck',
            'getcompanylogo',
            'companynames',
            'updateToken',
        ]);
    }




    public function db($dbs)
    {
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


        $this->loadModel('Users');
        $this->loadModel('Device');
        $this->loadModel('Contracts');
        $this->loadModel('Device');
        $this->loadModel('Production');
        $this->loadModel('Maintenance');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Productionorder');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('InspectionReport');
        $this->loadModel('Vendor');
        $this->loadModel('Po_delivery_note');
        $this->loadModel('Indentpo');
        $this->loadModel('Reverseindent');
        $this->loadModel('Additem');
        $this->loadModel('Payments');
        $this->loadModel('Stockregister');
        $this->loadModel('Machinemaster');
        $this->loadModel('Taxmaster');
    }



    //----------------------------- Login API --------------------------------
    public function login()
    {
        $this->autoRender = false;

        $mobile = $this->request->data['mobile'];
        $otp = $this->request->data['password'];
        $device_id = $this->request->data['device_id'];

        if (empty($mobile) || empty($otp)) {
            $response["success"] = false;
            $response["message"] = "Invalid Paramenters";
        }

        $user = $this->Auth->identify();
        if (!empty($user)) {
            $response["success"] = true;
            if ($user['role_id'] != '105') {
                $response["success"] = false;
                $response["message"] = "You are not authorise.";
            }

            $data["mobile"] = $mobile;

            $this->loadModel('Schools');
            $school = $this->Schools->find('all')->where(['Schools.id' => $user['c_id']])->first();
            $company_name = $school['school_name'];
            $data["erpID"] = $user['db'];
            $data['company_name'] = $company_name;

            $dbname = $user['db'];
            $userId = 1;
            $this->db($dbname);

            $checkdeviced = $this->Device->find('all')->where(['Device.device_id' => $device_id])->first();

            if (empty($checkdeviced)) {
                $newentity['device_id'] = $device_id;
                $userDevice = $this->Device->patchEntity($this->Device->newEntity(), $newentity);
                if ($device = $this->Device->save($userDevice)) {
                    $lstid = $device->id;
                }
            } else {
                $lstid = $checkdeviced['id'];
            }

            $conn = ConnectionManager::get('default');
            $details = 'UPDATE ' . $dbname . '.`users` SET `device_id` ="' . $lstid . '" WHERE `id` = "' . $userId . '"';
            $conn->execute($details);

            $data['userId'] = $user['id'];
            $data['device_id'] = $device_id;

            $filename2 = 'https://operify.in/images/' . $user['db'] . 'logo.png';
            if ($filename2) {
                $data["logo"] = 'https://operify.in/images/logo.png';
            } else {
                $data["logo"] = 'https://operify.in/images/operifylogo.png';
            }

            $data['authToken'] = JWT::encode(
                [
                    'sub' => $user['id'],
                    'exp' => time() + 604800,
                ],
                Security::salt()
            );
            // $cryptor = new Encryptor;
            // $response['output'] = $cryptor->encrypt(json_encode($data), DECRYPT); 
            $response['output'] = $data;
        } else {
            $response["success"] = false;
            $response["message"] = "Invalid User Type";
        }

        echo json_encode($response);
        return;
    }

    // to connect different database
    public function companynames()
    {
        $this->autoRender = false;
        $mobile = $this->request->data['erpID'];

        $company = [
            'tirupati_tppl' => 'TIRUPATI PLASTOMATICS PVT. LTD.',
            'tirupati_kcpl' => 'KANHHA CABLES PVT. LTD.',
            'tirupati_kcpl2' => ' M/s KANHHA CABLES PVT. LTD.',
            'tirupati_ebm' => 'EMB PLASTOMATICS PVT. LTD.'
        ];

        $response["success"] = true;
        $response["message"] = "Company Name Fetched successfully.";
        $response['companyName'] = $company;
        echo json_encode($response);
        return;
    }

    /******************************************************** Use To Create Payload  *********************************************/
    // public function prepareRequestBody()
    // {
    //     $data = json_encode($this->request->data);
    //     $cryptor = new Encryptor;
    //     $base64Encrypted = $cryptor->encrypt($data, DECRYPT);
    //     $response['payload'] = $base64Encrypted;
    //     $this->response->type('application/json');
    //     $this->response->body(json_encode($response));
    //     return $this->response;
    // }

    /******************************************************** Data increption  *********************************************/
    // public function dPayloadEncrypt()
    // {
    //     $data = $this->request->data['encode'];
    //     $cryptor = new Decryptor;
    //     $plaintext = $cryptor->decrypt($data, DECRYPT);
    //     print_r($plaintext);
    //     die;
    // }
    /******************************************************** Create a payload for website security  *********************************************/


    // public function dPayload($data)
    // {
    //     $base64Encrypted = $data->payload;
    //     $cryptor = new Decryptor;
    //     $plaintext = $cryptor->decrypt($base64Encrypted, DECRYPT);
    //     $postData = json_decode($plaintext);
    //     return $postData;
    // }


    /********************************************************  Create api of upload Token  *********************************************/
    public function uploadToken()
    {
        $response = array();
        $this->autoRender = false;
        if ($this->request->is("post")) {
            $token = $this->request->data["token"];
            $device_id = $this->request->data["uniqueDeviceId"];
            $erpID = $this->request->data["erpID"];

            if (empty($erpID) || empty($token) || empty($device_id)) {
                $response["success"] = false;
                $response["message"] = "Invalid Parameter";
            } else {
                $this->db($erpID);
                $deviceDetails = $this->Device->find('all')->where(['Device.device_id' => $device_id])->first();
                if (empty($deviceDetails)) {
                    $newentity['token'] = $token;
                    $newentity['device_id'] = $device_id;
                    $userDevice = $this->Device->patchEntity($this->Device->newEntity(), $newentity);
                    $device = $this->Device->save($userDevice);
                    $response["success"] = true;
                    $response["message"] = "Token upload success";
                } else {
                    $deviceDetails->device_id = $device_id;
                    $deviceDetails->token = $token;
                    $device = $this->Device->save($deviceDetails);
                    $response["success"] = true;
                    $response["message"] = "Token updated success";
                }
            }
        }
        echo json_encode($response);
        return;
    }

    // /********************************************************  Create api of update token  *********************************************/
    // public function updateToken()
    // {
    //     $response = array();
    //     $this->autoRender = false;
    //     $jsonData = $this->request->input('json_decode');
    //     if (empty($jsonData)) {
    //         $response["success"] = 0;
    //         $response["message"] = "Invalid Json Data";
    //         echo json_encode($response);
    //         return;
    //     } else {
    //         $jsonData = $this->request->input('json_decode');
    //         $postData = $this->dPayload($jsonData);

    //         $token = $postData->token;
    //         $erpID = $postData->erpID;

    //         if (empty($erpID) || empty($token)) {
    //             $response["success"] = false;
    //             $response["message"] = "Invalid Parameter";
    //         }

    //         $this->db($erpID);
    //         $this->loadmodel('Users');

    //         $userid = 1;
    //         $user = $this->Users->get($userid);
    //         $user->token = $token;
    //         if ($this->Users->save($user)) {
    //             $response["success"] = true;
    //             $response["message"] = "Token upload success";
    //         } else {
    //             $response["success"] = false;
    //             $response["message"] = "Token upload failed";
    //         }
    //     }
    //     echo json_encode($response);
    //     return;
    // }
    /********************************************************  Create api of versioncheck  *********************************************/
    function versioncheck()
    {

        $this->autoRender = false;
        $android_build_no = $this->request->data['android_build_no'];
        $ios_build_no = $this->request->data['ios_build_no'];
        $erpID = $this->request->data['erpID'];
        $response = array();
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
        }

        $this->db($erpID);
        $this->loadmodel('Users');
        $findbuild = $this->Users->find()->where(['role_id' => 105])->first();
        if (!empty($android_build_no) || !empty($ios_build_no)) {
            if ($android_build_no) {
                if ($findbuild['android_build_no'] == $android_build_no) {
                    $response['success'] = true;
                    $response['app_url'] = null;
                    $response['message'] = 'Build No. Match';
                } else {
                    $response['success'] = false;
                    $response['app_url'] = 'https://play.google.com/store/apps/details?id=com.doomshell.the_operify';
                    $response['message'] = 'Please update your app.';
                }
            }
            if ($ios_build_no) {
                if ($findbuild['ios_build_no'] == $ios_build_no) {
                    $response['success'] = true;
                    $response['app_url_ios'] = null;
                    $response['message'] = 'Build No. Match';
                } else {
                    $response['success'] = false;
                    $response['app_url_ios'] = 'https://apps.apple.com/in/app/the-operify/id6670391874';
                    $response['message'] = 'Please update your app.';
                }
            }
        } else {
            $response["success"] = false;
            $response["message"] = "Invalid Parameters";
        }
        echo json_encode($response);
        return;
    }

    /********************************************************  To show data on Dashboard  *********************************************/

    public function dashboard()
    {
        $this->autoRender = false;
        $response = [];
        $user_id = trim($this->request->data['user_id']);
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }

        $this->db($erpID);

        $response["success"] = true;
        $response["message"] = "Data fetched successfully.";
        $currentdate = date('Y-m-d');
        $lastweek = date('Y-m-d', strtotime('-7 days'));
        $monthdate = date('Y-m-01');
        if (date("Y-m-d") >= date("Y-04-01")) {
            $financialyear = date("Y-04-01");
        } else {
            $financialyear = date("Y-04-01", strtotime("-1 year"));
        }

        //--------------------------------- Header Data -------------------------
        // contract header
        $response["contractHeader"] = array();
        $contract['contractcount'] = $this->Contracts->find('all')->where(['DATE(Contracts.added_time) >=' => $financialyear])->count();

        $contract['todaycontractcount'] = $this->Production->find('all')->where(['DATE(Production.production_date)' => $currentdate])->group('contract_id')->count();
        $contract['weekcontractcount'] = $this->Production->find('all')->where(['DATE(Production.production_date) >' => $lastweek])->group('contract_id')->count();
        $contract['monthcontractcount'] = $this->Production->find('all')->where(['DATE(Production.production_date) >=' => $monthdate])->group('contract_id')->count();
        array_push($response["contractHeader"], $contract);

        // po header
        $response["poHeader"] = array();
        $po['purchasenordercount'] = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >=' => $financialyear])->count();
        $po['todaypurchasenordercount'] = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time)' => $currentdate])->count();
        $po['weekpurchasenordercount'] = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >' => $lastweek])->count();
        $po['monthpurchasenordercount'] = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >=' => $monthdate])->count();
        array_push($response["poHeader"], $po);

        // grn header
        $response["grnHeader"] = array();
        $grnheader['totalgrncount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >=' => $financialyear])->count();
        $grnheader['todaylgrncount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date)' => $currentdate])->count();
        $grnheader['weeklgrncount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >' => $lastweek])->count();
        $grnheader['monthgrncount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >=' => $monthdate])->count();
        array_push($response["grnHeader"], $grnheader);

        // supplier header
        $response["supplierHeader"] = array();
        $supplier['suppliercount'] = $this->Vendor->find('all')->where(['Vendor.status' => 'Y'])->count();
        $supplier['todaylsuppliercount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date)' => $currentdate])->group('vendor_id')->count();
        $supplier['weeklsuppliercount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >' => $lastweek])->group('vendor_id')->count();
        $supplier['monthsuppliercount'] = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >=' => $monthdate])->group('vendor_id')->count();
        array_push($response["supplierHeader"], $supplier);

        // maintenance header
        $response["maintenanceHeader"] = array();
        $maintenance['maintenancecount'] = $this->Maintenance->find('all')->where([['DATE(Maintenance.datefrom) >=' => $financialyear]])->count();
        $maintenance['todaymaintenancecount'] = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y', 'DATE(Maintenance.datefrom)' => $currentdate])->count();
        $maintenance['weekmaintenancecount'] = $this->Maintenance->find('all')->where([['DATE(Maintenance.datefrom) >' => $lastweek]])->count();
        $maintenance['monthmaintenancecount'] = $this->Maintenance->find('all')->where(['DATE(Maintenance.datefrom) >=' => $monthdate])->count();
        array_push($response["maintenanceHeader"], $maintenance);


        // -------------------------------- Tables Data ------------------------------------------------
        // po table
        $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->limit(5)->toarray();
        $response["poDetails"] = array();
        foreach ($podata as $value) {
            $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

            if ($value['is_revised'] > 0) {
                $purchaseorder['poid'] = $value['purchaseorder_id'] . ' R-' . $value['is_revised'];
            } else {
                $purchaseorder['poid'] = $value['purchaseorder_id'];
            }
            $purchaseorder['purchaseorder_id'] = $value['purchaseorder_id'];
            $purchaseorder['po_primary'] = $value['id'];
            $purchaseorder['is_revised'] = $value['is_revised'];
            $purchaseorder['date'] = date('d-m-Y', strtotime($value['added_time']));
            $purchaseorder['supplier'] = $vendors['name'];
            $purchaseorder['qty'] = $value['total_qty'];
            $purchaseorder['amount'] = $value['total_amt'];
            $purchaseorder['delivery'] = date('d-m-Y', strtotime($value['delivery_date']));
            array_push($response["poDetails"], $purchaseorder);
        }

        // grn table
        $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC'])->limit(5)->toarray();
        $response["grnDetails"] = array();
        foreach ($goodsreceived as $value) {
            $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
            $grn['grnno'] = $value['id'];
            $grn['poid'] = $value['purchaseorder_id'];
            $grn['date'] = date('d-m-Y', strtotime($value['inwarddate']));
            $grn['billDate'] = date('d-m-Y', strtotime($value['bill_date']));
            $grn['supplier'] = $vendors['name'];
            $grn['amount'] = $value['total_amt'];
            array_push($response["grnDetails"], $grn);
        }

        // indent table
        $indentpoid = $this->Indentpo->find('all')->order(['Indentpo.id' => 'DESC'])->limit(5)->toarray();
        $response["indentDetails"] = array();
        $i = 1;
        foreach ($indentpoid as $value) {
            $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
            $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();
            $indent['contract_id'] = $contractname['id'];
            $indent['indent_id'] = $value['indent_id'];
            $indent['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
            $indent['product'] = $product['item_name'];
            $indent['issued_name'] = $value['issued_name'];
            $indent['date'] = date('d-m-Y', strtotime($value['created']));
            array_push($response["indentDetails"], $indent);
            $i++;
        }

        // reverse table
        $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.id' => 'DESC'])->limit(5)->toarray();
        $response["reverseDetails"] = array();
        $i = 1;
        foreach ($reverseindentid as $value) {
            $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
            $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();
            $reverse['contract_id'] = $contractname['id'];
            $reverse['reverse_id'] = $value['reverse_id'];
            $reverse['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
            $reverse['product'] = $product['item_name'];
            $reverse['received_name'] = $value['received_name'];
            $reverse['date'] = date('d-m-Y', strtotime($value['created']));
            array_push($response["reverseDetails"], $reverse);
            $i++;
        }

        // production table
        $productionorder = $this->Productionorder->find('all')->order(['Productionorder.po_id' => 'Desc'])->limit(5)->toarray();
        $response["productionDetails"] = array();
        foreach ($productionorder as $value) {
            $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
            $product = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();
            $checkdailysheet = $this->Production->find()->where(['Production.po_id' => $value['po_id'], 'Production.productprocess_id' => 8])->order(['Production.id' => 'DESC'])->toarray();
            $quantity = '';
            foreach ($checkdailysheet as $prepqty) {
                $quantity += $prepqty['production_shift_a'] + $prepqty['production_shift_b'];
            }
            $production['contract_id'] = $contractname['id'];
            $production['po_id'] = $value['po_id'];
            $production['date'] = date('d-m-Y', strtotime($value['issuedate']));
            $production['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
            $production['product'] = $product['item_name'];
            $production['plannedqty'] = number_format((float) $value['plannedqty'], 2, '.', '');
            $production['preparedqty'] = number_format((float) $quantity ? $quantity : 0, 2, '.', '');
            array_push($response["productionDetails"], $production);
        }
        echo json_encode($response);
    }

    /********************************************************  To show data of Purchaseorder  *********************************************/
    public function purchaseorder()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->toarray();
        if (isset($podata) && !empty($podata)) {
            $response["success"] = true;
            $response["message"] = "PurchaseOrder Details fetched successfully.";
            $response["poDetails"] = array();
            foreach ($podata as $value) {
                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
                if ($value['is_revised'] > 0) {
                    $purchaseorder['poid'] = $value['purchaseorder_id'] . ' R-' . $value['is_revised'];
                } else {
                    $purchaseorder['poid'] = $value['purchaseorder_id'];
                }
                $purchaseorder['purchaseorder_id'] = $value['purchaseorder_id'];
                $purchaseorder['po_primary'] = $value['id'];
                $purchaseorder['is_revised'] = $value['is_revised'];
                $purchaseorder['date'] = date('d-m-Y', strtotime($value['added_time']));
                $purchaseorder['supplier'] = $vendors['name'];
                $purchaseorder['qty'] = $value['total_qty'];
                $purchaseorder['amount'] = $value['total_amt'];
                $purchaseorder['delivery'] = date('d-m-Y', strtotime($value['delivery_date']));
                array_push($response["poDetails"], $purchaseorder);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No PurchaseOrder data found.";
        }
        echo json_encode($response);
    }

    /********************************************************  To show data of Goods Received  *********************************************/
    public function grn()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC'])->toarray();
        if (isset($goodsreceived) && !empty($goodsreceived)) {
            $response["success"] = true;
            $response["message"] = "GRN Details fetched successfully.";
            $response["grnDetails"] = array();
            foreach ($goodsreceived as $value) {
                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
                $grn['grnno'] = $value['id'];
                $grn['poid'] = $value['purchaseorder_id'];
                $grn['date'] = date('d-m-Y', strtotime($value['inwarddate']));
                $grn['billDate'] = date('d-m-Y', strtotime($value['bill_date']));
                $grn['supplier'] = $vendors['name'];
                $grn['amount'] = $value['total_amt'];
                array_push($response["grnDetails"], $grn);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No GRN data found.";
        }
        echo json_encode($response);
    }

    /********************************************************  To show data of Vendor Details  *********************************************/
    public function vendor()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $vendor_id = $this->Vendor->find('all')->order(['Vendor.name' => 'ASC'])->first();
        $payments = $this->Payments->find('all')->where(['Payments.vendor_id' => $vendor_id['id'], 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC'])->toarray();

        if (isset($payments) && !empty($payments)) {
            $response["success"] = true;
            $response["message"] = "Vendors Details fetched successfully.";
            $response["vendor_id"] = $vendor_id['id'];
            $response["name"] = $vendor_id['name'];
            $response["vendorDetails"] = array();
            foreach ($payments as $value) {

                if ($value['store_type'] == '1') {
                    $description = 'Bill No. ' . $value['bill_no'] . ' With ' . $value['remark'];
                } else {
                    $description = 'Recipt No. ' . $value['receipt_no'] . ' With ' . $value['remark'];
                }

                if ($value['store_type'] == '1') {
                    $credit = number_format((float) $value['total_amt'], 2, '.', '');
                } else {
                    $credit = '-';
                }

                if ($value['store_type'] == '2') {
                    $debit = number_format((float) $value['total_amt'], 2, '.', '');
                } else {
                    $debit = '-';
                }

                if ($value['store_type'] == '1') {
                    $curbalance = $curbalance + $value['total_amt'];
                } else {
                    $curbalance = $curbalance - $value['total_amt'];
                }

                $vendor['date'] = date("d-m-Y", strtotime($value['bill_date']));
                $vendor['description'] = $description;
                $vendor['cr_amt'] = $credit;
                $vendor['de_amt'] = $debit;
                $vendor['balance'] = $curbalance;
                array_push($response["vendorDetails"], $vendor);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Vendors data found.";
        }
        echo json_encode($response);
    }

    /********************************************************  Made by ashish all vendor **************************  *********************************************/
    public function allvendors()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $vendorsreport = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        if (isset($vendorsreport) && !empty($vendorsreport)) {
            $response["success"] = true;
            $response["message"] = "Vendors Track fetched successfully.";
            $response["vendorTrack"] = array();
            foreach ($vendorsreport as $value) {
                array_push($response["vendorTrack"], $value);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Vendors Track data foend.";
        }
        echo json_encode($response);
    }


    /*************************************** Api made by Aashish Tiwari //************************/

    public function allpo()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameters";
            echo json_encode($response);
            return;
        }

        $this->db($erpID);
        $podeta = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->toArray();

        if (!empty($podeta)) {
            $response["success"] = true;
            $response["message"] = "Purchase Order details fetched successfully!";
            $response["podetails"] = [];

            foreach ($podeta as $value) {
                $po_Detail = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $value['purchaseorder_id']])->toArray();

                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

                $purchaseorder = [];

                if ($value['is_revised'] > 0) {
                    $purchaseorder['poid'] = $value['purchaseorder_id'] . 'R-' . $value['is_revised'];
                } else {
                    $purchaseorder['poid'] = $value['purchaseorder_id'];
                }

                $purchaseorder['purchaseorder_id'] = $value['purchaseorder_id'];
                $purchaseorder['po_primary'] = $value['id'];
                $purchaseorder['is_revised'] = $value['is_revised'];
                $purchaseorder['date'] = date('d-m-Y', strtotime($value['added_time']));
                $purchaseorder['delivery'] = date('d-m-Y', strtotime($value['delivery_date']));
                $purchaseorder['supplier'] = $vendors['name'];
                $purchaseorder['qty'] = $value['total_qty'];
                $purchaseorder['total_amount'] = $value['total_amt'];

                $product_details = [];

                foreach ($po_Detail as $p_detail) {
                    $itemname = $this->Additem->find('all')->where(['Additem.id' => $p_detail['item_id']])->first();
                    $delivery = $this->Po_delivery_note->find('all')->where(['Po_delivery_note.item_id' => $p_detail['item_id']])->first();

                    $product = [];
                    $product['product_name'] = $itemname['item_name'];
                    $product['order_qty'] = $p_detail['item_qty'];
                    $product['pending_qty'] = $p_detail['item_qty'];
                    $product['rate'] = $p_detail['item_amt'];
                    $product['price'] = $p_detail['item_qty'] * $p_detail['item_amt'];
                    $product['tax'] = '18%';
                    $product['tax_amount'] = $p_detail['item_tax_amt'];
                    $product['item_total_amount'] = $p_detail['item_total_amount'];
                    $product['delivery date'] = $delivery['delivery_date'];
                    $product['delivery note'] = $delivery['delivery_note'];

                    $product_details[] = $product;
                }

                $purchaseorder['product_details'] = $product_details;
                array_push($response["podetails"], $purchaseorder);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No PurchaseOrder data found.";
        }

        echo json_encode($response);
    }




    /********************************************************  To show data of Vendorsreport  *********************************************/

    public function vendorsreport()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        $vendorsreport = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        if (isset($vendorsreport) && !empty($vendorsreport)) {
            $response["success"] = true;
            $response["message"] = "Vendors Track fetched successfully.";
            $response["vendorTrack"] = array();
            foreach ($vendorsreport as $value) {
                $vendor = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
                $vendortarck['date'] = date("d-m-Y", strtotime($value['inwarddate']));
                $vendortarck['grn_no'] = $value['id'];
                $vendortarck['po_no'] = $value['purchaseorder_id'];
                $vendortarck['bill_no'] = $value['bill_no'];
                $vendortarck['vendor'] = $vendor['name'];
                $vendortarck['amount'] = number_format((float) $value['total_amt'], 2, '.', '');
                array_push($response["vendorTrack"], $vendortarck);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Vendors Track data foend.";
        }
        echo json_encode($response);
    }


    /********************************************************  To show data of Indent Details  *********************************************/
    public function indent()
    {

        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $indentpoid = $this->Indentpo->find('all')->order(['Indentpo.id' => 'DESC'])->toarray();
        if (isset($indentpoid) && !empty($indentpoid)) {
            $response["success"] = true;
            $response["message"] = "Indent Details fetched successfully.";
            $response["indentDetails"] = array();
            $i = 1;
            foreach ($indentpoid as $value) {
                $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();
                $indent['contract_id'] = $contractname['id'];
                $indent['indent_id'] = $value['indent_id'];
                $indent['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                $indent['product'] = $product['item_name'];
                $indent['issued_name'] = $value['issued_name'];
                $indent['date'] = date('d-m-Y', strtotime($value['created']));
                array_push($response["indentDetails"], $indent);
                $i++;
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Indent data found.";
        }
        echo json_encode($response);
    }

    /************************************** made by Aashish Reverse Indent Details ************************************/
    public function getreverse()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.id' => 'DESC'])->toarray();
        if (isset($reverseindentid) && !empty($reverseindentid)) {
            $response["success"] = true;
            $response["message"] = "Reverse Details fetched successfully.";
            $response["reverseDetails"] = array();
            foreach ($reverseindentid as $value) {
                $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();
                $reverse['contract_id'] = $contractname['id'];
                $reverse['reverse_id'] = $value['reverse_id'];
                $reverse['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                $reverse['product'] = $product['item_name'];
                $reverse['received_name'] = $value['received_name'];
                $reverse['date'] = date('d-m-Y', strtotime($value['created']));
                array_push($response["reverseDetails"], $reverse);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Reverse data found.";
        }
        echo json_encode($response);
    }

    /************************************** To show data of Reverse Indent Details ************************************/
    public function reverse()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.id' => 'DESC'])->toarray();
        if (isset($reverseindentid) && !empty($reverseindentid)) {
            $response["success"] = true;
            $response["message"] = "Reverse Details fetched successfully.";
            $response["reverseDetails"] = array();
            $i = 1;
            foreach ($reverseindentid as $value) {
                $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();
                $reverse['contract_id'] = $contractname['id'];
                $reverse['reverse_id'] = $value['reverse_id'];
                $reverse['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                $reverse['product'] = $product['item_name'];
                $reverse['received_name'] = $value['received_name'];
                $reverse['date'] = date('d-m-Y', strtotime($value['created']));
                array_push($response["reverseDetails"], $reverse);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Reverse data found.";
        }
        echo json_encode($response);
    }


    /********************************************************  To show data of ProductionOrder             *********************************************/
    public function production()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $productionorder = $this->Productionorder->find('all')->order(['Productionorder.po_id' => 'Desc'])->toarray();
        if (isset($productionorder) && !empty($productionorder)) {
            $response["success"] = true;
            $response["message"] = "Production Details fetched successfully.";
            $response["productionDetails"] = array();
            foreach ($productionorder as $value) {
                $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();
                $checkdailysheet = $this->Production->find()->where(['Production.po_id' => $value['po_id'], 'Production.productprocess_id' => 8])->order(['Production.id' => 'DESC'])->toarray();
                $quantity = '';
                foreach ($checkdailysheet as $prepqty) {
                    $quantity += $prepqty['production_shift_a'] + $prepqty['production_shift_b'];
                }
                $production['contract_id'] = $contractname['id'];
                $production['po_id'] = $value['po_id'];
                $production['date'] = date('d-m-Y', strtotime($value['issuedate']));
                $production['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                $production['product'] = $product['item_name'];
                $production['plannedqty'] = number_format((float) $value['plannedqty'], 2, '.', '');
                $production['preparedqty'] = number_format((float) $quantity ? $quantity : 0, 2, '.', '');
                array_push($response["productionDetails"], $production);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Production data found.";
        }
        echo json_encode($response);
    }

    /********************************************************  To show data of Stock  *********************************************/
    public function stock()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $itemId = $this->Additem->find('all')->where(['Additem.id' => 271])->order(['Additem.item_name' => 'ASC'])->first();
        $datefrom = '2023-11-29';
        $dateto2 = date('Y-m-d');

        $date_from = strtotime($datefrom);
        $date_to = strtotime($dateto2);

        $response["success"] = true;
        $response["message"] = "Item Stock fetched successfully.";
        $response["item_id"] = $itemId['id'];
        $response["item_name"] = $itemId['item_name'];

        $response["stockDetails"] = array();
        for ($i = $date_from; $i <= $date_to; $i += 86400) {
            $totalquant = 0;

            $totalreceived = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date <' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
            $totalissued = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.issue_date <' => date("Y-m-d", $i), 'Stockregister.item_id' => $itemId['id'], 'Stockregister.store_type IN' => ['2,4']])->first();
            $currentStock = $totalreceived['sum'] - $totalissued['sum'];
            $currentStock = $currentStock ? $currentStock : '0';

            $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['1']])->first();
            $grnStock = $grnStock['sum'] ? $grnStock['sum'] : '0';

            $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['2']])->first();
            $indentStock = $indentStock['sum'] ? $indentStock['sum'] : '0';

            $reverseStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['3']])->first();
            $reverseStock = $reverseStock['sum'] ? $reverseStock['sum'] : '0';

            $returnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['4']])->first();
            $returnStock = $returnStock['sum'] ? $returnStock['sum'] : '0';

            $totalquant = $currentStock + $grnStock - $indentStock + $reverseStock - $returnStock;

            if ($grnStock == 0 && $indentStock == 0 && $reverseStock == 0 && $returnStock == 0) {
                continue;
            }
            $stockvalue['date'] = date("d-m-Y", $i);
            $stockvalue['opening_stock'] = $currentStock;
            $stockvalue['received_stock'] = $grnStock;
            $stockvalue['issued_stock'] = $indentStock;
            $stockvalue['reverse_stock'] = $reverseStock;
            $stockvalue['return_stock'] = $returnStock;
            $stockvalue['closing_stock'] = $totalquant;

            array_push($response["stockDetails"], $stockvalue);
        }

        echo json_encode($response);
    }


    /********************************************************  To search Stock  *********************************************/
    public function searchstock()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $item_id = $this->request->data['item_id'];
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        $itemId = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
        $date_from = strtotime($datefrom);
        $date_to = strtotime($dateto2);
        $response["success"] = true;
        $response["message"] = "Item Stock fetched successfully.";
        $response["item_id"] = $itemId['id'];
        $response["item_name"] = $itemId['item_name'];

        $response["stockDetails"] = array();
        for ($i = $date_from; $i <= $date_to; $i += 86400) {
            $totalquant = 0;

            $totalreceived = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date <' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
            $totalissued = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.issue_date <' => date("Y-m-d", $i), 'Stockregister.item_id' => $itemId['id'], 'Stockregister.store_type IN' => ['2,4']])->first();
            $currentStock = $totalreceived['sum'] - $totalissued['sum'];
            $currentStock = $currentStock ? $currentStock : '0';

            $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['1']])->first();
            $grnStock = $grnStock['sum'] ? $grnStock['sum'] : '0';

            $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['2']])->first();
            $indentStock = $indentStock['sum'] ? $indentStock['sum'] : '0';

            $reverseStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['3']])->first();
            $reverseStock = $reverseStock['sum'] ? $reverseStock['sum'] : '0';

            $returnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $itemId['id'], 'Stockregister.issue_date ' => date("Y-m-d", $i), 'Stockregister.store_type IN' => ['4']])->first();
            $returnStock = $returnStock['sum'] ? $returnStock['sum'] : '0';

            $totalquant = $currentStock + $grnStock - $indentStock + $reverseStock - $returnStock;

            if ($grnStock == 0 && $indentStock == 0 && $reverseStock == 0 && $returnStock == 0) {
                continue;
            }
            $stockvalue['date'] = date("d-m-Y", $i);
            $stockvalue['opening_stock'] = $currentStock;
            $stockvalue['received_stock'] = $grnStock;
            $stockvalue['issued_stock'] = $indentStock;
            $stockvalue['reverse_stock'] = $reverseStock;
            $stockvalue['return_stock'] = $returnStock;
            $stockvalue['closing_stock'] = $totalquant;

            array_push($response["stockDetails"], $stockvalue);
        }
        echo json_encode($response);
    }


    /********************************************************  To Show Contract Details  *********************************************/
    public function getcontract()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        $contractname = $this->Contracts->find('all')->order(['Contracts.id' => 'Desc'])->toarray();
        if (isset($contractname) && !empty($contractname)) {
            $response["success"] = true;
            $response["message"] = "Contract Details fetched successfully.";
            $response["contractDetails"] = array();
            foreach ($contractname as $value) {
                $contract['id'] = $value['id'];
                $contract['supplier_id'] = $value['supplier_id'];
                $contract['title'] = $value['title'];
                $contract['workorder'] = $value['workorder'];
                $contract['cost'] = $value['cost'];
                $contract['description'] = $value['description'];
                $contract['start-date'] = date('d-m-Y', strtotime($value['contract_start_date']));
                $contract['end-date'] = date('d-m-Y', strtotime($value['contract_end_date']));
                $contract['added_time'] = date('H:i:s', strtotime($value['added_time']));

                array_push($response["contractDetails"], $contract);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Contract data fetched.";
        }
        echo json_encode($response);
    }



    public function contract()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        $contractname = $this->Contracts->find('all')->order(['Contracts.id' => 'Desc'])->toarray();
        if (isset($contractname) && !empty($contractname)) {
            $response["success"] = true;
            $response["message"] = "Contract Details fetched successfully.";
            $response["contractDetails"] = array();
            foreach ($contractname as $value) {
                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['supplier_id']])->first();
                $contract['contract_id'] = $value['id'];
                $contract['title'] = $value['title'] . '(' . $value['workorder'] . ')';
                $contract['supplier'] = $vendors['name'];
                $contract['cost'] = $value['cost'];
                $contract['date'] = date('d-m-Y', strtotime($value['issuedate']));
                array_push($response["contractDetails"], $contract);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Contract data fetched.";
        }
        echo json_encode($response);
    }


    /********************************************************  To search vendorName  *********************************************/
    public function searchvendorname()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $vendorname = trim($this->request->data['vendorname']);

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($vendorname == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Vendor Name.";
        } else {
            $vendors = $this->Vendor->find('all')->where(['Vendor.name LIKE ' => '%' . $vendorname . '%'])->toarray();
            if (!empty($vendors)) {
                $response["success"] = true;
                $response["message"] = "Vendors Name fetched successfully.";
                $response["vendorName"] = array();
                foreach ($vendors as $value) {
                    $searchven['vendor_id'] = $value['id'];
                    $searchven['name'] = $value['name'];
                    array_push($response["vendorName"], $searchven);
                }
            } else {
                $response['status'] = false;
                $response['message'] = "Vendor Not found.";
            }
        }
        echo json_encode($response);
    }


    /********************************************************  To search itemName  *********************************************/
    public function searchitemname()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        $item_name = $this->request->data['item_name'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($item_name == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Item Name.";
        } else {

            $items = $this->Additem->find('all')->where(['Additem.item_name LIKE ' => '%' . $item_name . '%'])->toarray();
            if (!empty($items)) {
                $response["success"] = true;
                $response["message"] = "Item Name fetched successfully.";
                $response["itemName"] = array();
                foreach ($items as $value) {
                    $searchitem['item_id'] = $value['id'];
                    $searchitem['item_name'] = $value['item_name'];
                    array_push($response["itemName"], $searchitem);
                }
            } else {
                $response['status'] = false;
                $response['message'] = "Item Not found.";
            }
        }
        echo json_encode($response);
    }

    /********************************************************  To search ContractName  *********************************************/

    public function searchcontractname()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];
        $workorderno = $this->request->data['workorderno'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($workorderno == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Workorder No.";
        } else {
            $contractname = $this->Contracts->find('all')
                ->where([
                    'Contracts.status' => 'Y',
                    'OR' => [
                        ['Contracts.workorder LIKE' => '%' . $workorderno . '%'],
                        ['Contracts.title LIKE' => '%' . $workorderno . '%'],
                    ]
                ])
                ->toArray();
            if (!empty($contractname)) {
                $response["success"] = true;
                $response["message"] = "Contract fetched successfully.";
                $response["contractName"] = array();
                foreach ($contractname as $value) {
                    $searchcon['contract_id'] = $value['id'];
                    $searchcon['name'] = $value['title'] . '(' . $value['workorder'] . ')';
                    array_push($response["contractName"], $searchcon);
                }
            } else {
                $response['status'] = false;
                $response['message'] = "Contract Not found.";
            }
        }
        echo json_encode($response);
    }

    /******************************************************** To search Contract  *********************************************/
    public function searchcontract()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $contract_id = $this->request->data['contract_id'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($contract_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Workorder No.";
        } else {
            $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $contract_id])->first();
            if (!empty($contractname)) {
                $response["success"] = true;
                $response["message"] = "Contract fetched successfully.";
                $response["contractDetails"] = array();

                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $contractname['supplier_id']])->first();

                $contract['contract_id'] = $contractname['id'];
                $contract['title'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                $contract['supplier'] = $vendors['name'];
                $contract['cost'] = $contractname['cost'];
                $contract['date'] = date('d-m-Y', strtotime($contractname['issuedate']));
                array_push($response["contractDetails"], $contract);
            } else {
                $response['status'] = false;
                $response['message'] = "Contract Not found.";
            }
        }
        echo json_encode($response);
    }

    //******************************************************** searchpurchaseorder API ********************************************************
    public function searchpurchaseorder()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $po_id = $this->request->data['po_id'];
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($po_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter PO No.";
        } else {
            $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $po_id])->order(['Purchaseorder.id' => 'DESC'])->toarray();
            if (!empty($podata)) {
                $response["success"] = true;
                $response["message"] = "PurchaseOrder Details fetched successfully.";
                $response["purchaseDetails"] = array();
                foreach ($podata as $value) {
                    $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

                    if ($value['is_revised'] > 0) {
                        $purchase['poid'] = $value['purchaseorder_id'] . ' R-' . $value['is_revised'];
                    } else {
                        $purchase['poid'] = $value['purchaseorder_id'];
                    }

                    $purchase['date'] = date('d-m-Y', strtotime($value['added_time']));
                    $purchase['supplier'] = $vendors['name'];
                    $purchase['qty'] = $value['total_qty'];
                    $purchase['amount'] = $value['total_amt'];
                    $purchase['delivery'] = date('d-m-Y', strtotime($value['delivery_date']));
                    array_push($response["purchaseDetails"], $purchase);
                }
            } else {
                $response['status'] = false;
                $response['message'] = "PurchaseOrder Not found.";
            }
        }
        echo json_encode($response);
    }

    //******************************************************** searchgrn API ********************************************************
    public function searchgrn()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $po_id = $this->request->data['po_id'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($po_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter PO No.";
        } else {
            $goodsdata = $this->Goodsreceived->find('all')->where(['Goodsreceived.purchaseorder_id' => $po_id])->order(['Goodsreceived.id' => 'Desc'])->toarray();
            if (!empty($goodsdata)) {
                $response["success"] = true;
                $response["message"] = "GRN Details fetched successfully.";
                $response["grnreceiveDetails"] = array();

                foreach ($goodsdata as $value) {
                    $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

                    $searchgrn['grnno'] = $value['id'];
                    $searchgrn['poid'] = $value['purchaseorder_id'];
                    $searchgrn['date'] = date('d-m-Y', strtotime($value['inwarddate']));
                    $searchgrn['billDate'] = date('d-m-Y', strtotime($value['bill_date']));
                    $searchgrn['supplier'] = $vendors['name'];
                    $searchgrn['amount'] = $value['total_amt'];
                    array_push($response["grnreceiveDetails"], $searchgrn);
                }
            } else {
                $response['status'] = false;
                $response['message'] = "GRN Not found.";
            }
        }
        echo json_encode($response);
    }

    //******************************************************** searchProduction API ********************************************************
    public function searchproduction()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $contract_id = $this->request->data['contract_id'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($contract_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Workorder No.";
        } else {
            $production = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contract_id])->order(['Productionorder.po_id' => 'Desc'])->toarray();

            if (isset($production) && !empty($production)) {
                $response["success"] = true;
                $response["message"] = "Production Details fetched successfully.";
                $response["searchProduction"] = array();
                foreach ($production as $value) {
                    $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                    $product = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();

                    $checkdailysheet = $this->Production->find()->where(['Production.po_id' => $value['po_id'], 'Production.productprocess_id' => 8])->order(['Production.id' => 'DESC'])->toarray();

                    $quantity = '';
                    foreach ($checkdailysheet as $prepqty) {
                        $quantity += $prepqty['production_shift_a'] + $prepqty['production_shift_b'];
                    }

                    $searchpro['contract_id'] = $contractname['id'];
                    $searchpro['po_id'] = $value['po_id'];
                    $searchpro['date'] = date('d-m-Y', strtotime($value['issuedate']));
                    $searchpro['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                    $searchpro['product'] = $product['item_name'];
                    $searchpro['plannedqty'] = number_format((float) $value['plannedqty'], 2, '.', '');
                    $searchpro['preparedqty'] = number_format((float) $quantity ? $quantity : 0, 2, '.', '');
                    array_push($response["searchProduction"], $searchpro);
                }
            } else {
                $response["success"] = false;
                $response["message"] = "No Production data fetched.";
            }
        }
        echo json_encode($response);
    }

    //********************************************************  searchIndent API  ********************************************************
    public function searchindent()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $contract_id = $this->request->data['contract_id'];


        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($contract_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Workorder No.";
        } else {
            $indent = $this->Indentpo->find('all')->where(['Indentpo.contract_id' => $contract_id])->order(['Indentpo.id' => 'Desc'])->toarray();
            if (isset($indent) && !empty($indent)) {
                $response["success"] = true;
                $response["message"] = "Indent Details fetched successfully.";
                $response["searchIndent"] = array();
                $i = 1;
                foreach ($indent as $value) {
                    $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                    $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();

                    $searchindent['contract_id'] = $contractname['id'];
                    $searchindent['indent_id'] = $value['indent_id'];
                    $searchindent['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                    $searchindent['product'] = $product['item_name'];
                    $searchindent['issued_name'] = $value['issued_name'];
                    $searchindent['date'] = date('d-m-Y', strtotime($value['created']));
                    array_push($response["searchIndent"], $searchindent);
                    $i++;
                }
            } else {
                $response["success"] = false;
                $response["message"] = "No Indent data fetched.";
            }
        }
        echo json_encode($response);
    }

    //**************************************************  searchReverse API **************************************************
    public function searchreverse()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $contract_id = $this->request->data['contract_id'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($contract_id == '') {
            $response['status'] = false;
            $response['message'] = "Please Enter Workorder No.";
        } else {
            $reverse = $this->Reverseindent->find('all')->where(['Reverseindent.contract_id' => $contract_id])->order(['Reverseindent.id' => 'Desc'])->toarray();
            if (isset($reverse) && !empty($reverse)) {
                $response["success"] = true;
                $response["message"] = " Reverse Details fetched successfully.";
                $response["searchReverse"] = array();

                foreach ($reverse as $value) {
                    $contractname = $this->Contracts->find('all')->where(['Contracts.id' => $value['contract_id']])->first();
                    $product = $this->Additem->find('all')->where(['Additem.id' => $value['finishedproduct_id']])->first();

                    $searchreverse['contract_id'] = $contractname['id'];
                    $searchreverse['reverse_id'] = $value['reverse_id'];
                    $searchreverse['contact_name'] = $contractname['title'] . '(' . $contractname['workorder'] . ')';
                    $searchreverse['product'] = $product['item_name'];
                    $searchreverse['received_name'] = $value['received_name'];
                    $searchreverse['date'] = date('d-m-Y', strtotime($value['created']));
                    array_push($response["searchReverse"], $searchreverse);
                    $i++;
                }
            } else {
                $response["success"] = false;
                $response["message"] = "No Reverse data fetched.";
            }
        }
        echo json_encode($response);
    }

    //**************************************************  searchvendorsreport API **************************************************
    public function searchvendorsreport()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $vendor_id = $this->request->data['vendor_id'];
        $date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
        $date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);


        if ($vendor_id == '' || $date_from == '' || $date_to == '') {
            $response['status'] = false;
            $response['message'] = "All feilds are required";
        } else {
            $cond1 = [];
            $cond1['Goodsreceived.vendor_id'] = $vendor_id;
            $cond1['DATE(Goodsreceived.inwarddate) >='] = $date_from;
            $cond1['DATE(Goodsreceived.inwarddate) <='] = $date_to;

            $vendorsreport = $this->Goodsreceived->find('all')->where([$cond1])->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();

            if (isset($vendorsreport) && !empty($vendorsreport)) {
                $response["success"] = true;
                $response["message"] = "Vendors Data fetched successfully.";
                $response["vendorsData"] = array();
                foreach ($vendorsreport as $value) {
                    $vendr = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
                    $vendortarck['date'] = date("d-m-Y", strtotime($value['inwarddate']));
                    $vendortarck['grn_no'] = $value['id'];
                    $vendortarck['po_no'] = $value['purchaseorder_id'];
                    $vendortarck['bill_no'] = $value['bill_no'];
                    $vendortarck['vendor'] = $vendr['name'];
                    $vendortarck['amount'] = number_format((float) $value['total_amt'], 2, '.', '');
                    array_push($response["vendorsData"], $vendortarck);
                }
            } else {
                $response["success"] = false;
                $response["message"] = "No Vendors Data data found.";
            }
        }

        echo json_encode($response);
    }

    //************************************************** searchvendor API **************************************************
    public function searchvendor()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $vendor_id = $this->request->data['vendor_id'];
        $date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
        $date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($vendor_id == '' || $date_from == '' || $date_to == '') {
            $response['status'] = false;
            $response['message'] = "All feilds are required";
        } else {
            $cond2 = [];
            $cond2['Payments.vendor_id'] = $vendor_id;
            $cond2['DATE(Payments.bill_date) >='] = $date_from;
            $cond2['DATE(Payments.bill_date) <='] = $date_to;

            $vendorpayments = $this->Payments->find('all')->where([$cond2, 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC'])->toarray();

            $creditamt = $this->Payments->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.bill_date <' => $date_from, 'Payments.store_type IN' => ['1']])->first();
            $debitamt = $this->Payments->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.bill_date <' => $date_from, 'Payments.status' => 'Y', 'Payments.store_type IN' => ['2']])->first();

            $amount = $creditamt['sum'] - $debitamt['sum'];
            $curbalance = $amount ? $amount : '0';

            if (isset($vendorpayments) && !empty($vendorpayments)) {
                $response["success"] = true;
                $response["message"] = "Vendors Details fetched successfully.";
                $response["vendorDetails"] = array();
                foreach ($vendorpayments as $value) {

                    if ($value['store_type'] == '1') {
                        $description = 'Bill No. ' . $value['bill_no'] . ' With ' . $value['remark'];
                    } else {
                        $description = 'Recipt No. ' . $value['receipt_no'] . ' With ' . $value['remark'];
                    }

                    if ($value['store_type'] == '1') {
                        $credit = number_format((float) $value['total_amt'], 2, '.', '');
                    } else {
                        $credit = '-';
                    }

                    if ($value['store_type'] == '2') {
                        $debit = number_format((float) $value['total_amt'], 2, '.', '');
                    } else {
                        $debit = '-';
                    }

                    if ($value['store_type'] == '1') {
                        $curbalance = $curbalance + $value['total_amt'];
                    } else {
                        $curbalance = $curbalance - $value['total_amt'];
                    }

                    $vendor['date'] = date("d-m-Y", strtotime($value['bill_date']));
                    $vendor['description'] = $description;
                    $vendor['cr_amt'] = $credit;
                    $vendor['de_amt'] = $debit;
                    $vendor['balance'] = $curbalance;
                    array_push($response["vendorDetails"], $vendor);
                }
            } else {
                $response["success"] = false;
                $response["message"] = "No Vendors Data data found.";
            }
        }

        echo json_encode($response);
    }


    //**************************************************  GRN Popup API  **************************************************
    public function grnpopup()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $goodsId = $this->request->data['goodsID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($goodsId) {
            $response["success"] = true;
            $response["message"] = "GRN Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'goodsreceived/view/' . $goodsId . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter GRN No.";
        }
        echo json_encode($response);
    }

    //************************************************** purchaseorderpdf API **************************************************
    public function purchaseorderpdf()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $purchaseorder_id = trim($this->request->data['purchaseorder_id']);
        $po_primary = trim($this->request->data['po_primary']);
        $is_revised = trim($this->request->data['is_revised']);

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($purchaseorder_id) {
            $response["success"] = true;
            $response["message"] = "Purchase Order Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'purchaseorder/view/' . $purchaseorder_id . '/' . $is_revised . '/' . $po_primary . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter PO No.";
        }
        echo json_encode($response);
    }

    //**************************************************  Indet pdf API  **************************************************
    public function indentpdf()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $indentId = trim($this->request->data['indent_id']);

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($indentId) {
            $response["success"] = true;
            $response["message"] = "Indent Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'indentpo/viewindentpopdf/' . $indentId . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter Indent No.";
        }
        echo json_encode($response);
    }

    //**************************************************  reversepdf API  **************************************************
    public function reversepdf()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $reverseId = trim($this->request->data['reverse_id']);
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);
        if ($reverseId) {
            $response["success"] = true;
            $response["message"] = "Reverse Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'reverseindent/viewreverseindentpdf/' . $reverseId . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter Reverse No.";
        }
        echo json_encode($response);
    }

    //**************************************************  productionorderpdf  API  **************************************************
    public function productionorderpdf()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $productionId = trim($this->request->data['production_id']);

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($productionId) {
            $response["success"] = true;
            $response["message"] = "Production Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'production/viewproductionpdf/' . $productionId . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter Production No.";
        }
        echo json_encode($response);
    }

    //************************************************** contractpdf  API  **************************************************
    public function contractpdf()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];
        $contractId = trim($this->request->data['contract_id']);
        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        if ($contractId) {
            $response["success"] = true;
            $response["message"] = "Contract Details fetched successfully.";
            $response["pdfLink"] = ADMIN_URL . 'production/viewcontractdetailspdf/' . $contractId . '/' . $erpID;
        } else {
            $response["success"] = false;
            $response["message"] = "Please Enter Contract No.";
        }
        echo json_encode($response);
    }

    //************************************************** Maintenance API **************************************************
    public function maintenance()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }
        $this->db($erpID);

        $maintenance = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'DESC'])->toarray();
        if (isset($maintenance) && !empty($maintenance)) {
            $response["success"] = true;
            $response["message"] = "Maintenance Details fetched successfully.";
            $response["maintenanceDetails"] = array();
            foreach ($maintenance as $value) {
                $machine_name = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $value['machine_id']])->first();
                $main['date'] = date('d-m-Y', strtotime($value['datefrom']));
                $main['machine'] = $machine_name['machine_name'];
                $main['breakdown'] = $value['breakdown_type'];
                $main['time'] = $value['total_time'];
                $main['assign_to'] = $value['assigned_to'];
                $main['status'] = $value['maintenance_status'];
                array_push($response["maintenanceDetails"], $main);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No GRN data fetched.";
        }
        echo json_encode($response);
    }

    // get company logo by erp id
    public function getcompanylogo()
    {
        $this->autoRender = false;
        $response = [];

        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }

        $filename2 = WWW_ROOT . 'images/' . $erpID . 'logo.png';
        if (file_exists($filename2)) {
            $data["logo"] = SITE_URL . 'images/' . $erpID . 'logo.png';
        } else {
            $data["logo"] = SITE_URL . 'images/operifylogo.png';
        }

        $response["success"] = true;
        $response["message"] = "Logo fetched suceesfully.";
        $response['output'] = $data;
        echo json_encode($response);
        return;
    }




    /******************************************** *   all api made by Aashish *****************************************/

    public function allgrn()
    {
        $this->autoRender = false;
        $response = [];
        $erpID = $this->request->data['erpID'];

        if (empty($erpID)) {
            $response["success"] = false;
            $response["message"] = "Invalid Parameter";
            echo json_encode($response);
            return;
        }

        $this->db($erpID);

        $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC'])->toArray();

        if (!empty($goodsreceived)) {
            $response["success"] = true;
            $response["message"] = "GRN Details fetched successfully.";
            $response["grnDetails"] = [];

            foreach ($goodsreceived as $value) {

                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

                $grn = [];
                $grn['grnno'] = $value['id'];
                $grn['poid'] = $value['purchaseorder_id'];
                $grn['date'] = date('d-m-Y', strtotime($value['inwarddate']));
                $grn['billDate'] = date('d-m-Y', strtotime($value['bill_date']));
                $grn['supplier'] = $vendors['name'];
                $grn['amount'] = $value['total_amt'];

                $po_Detail = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $value['purchaseorder_id']])->toArray();

                $product_details = [];
                foreach ($po_Detail as $p_detail) {

                    $itemname = $this->Additem->find('all')->where(['Additem.id' => $p_detail['item_id']])->first();

                    $product = [];
                    $product['product_name'] = $itemname['item_name'];
                    $product['order_qty'] = $p_detail['item_qty'];
                    $product['pending_qty'] = $p_detail['item_qty'];
                    $product['rate'] = $p_detail['item_amt'];
                    $product['price'] = $p_detail['item_qty'] * $p_detail['item_amt'];
                    $product['tax'] = '18%';
                    $product['tax_amount'] = $p_detail['item_tax_amt'];
                    $product['item_total_amount'] = $p_detail['item_total_amount'];

                    $product_details[] = $product;
                }

                $grn['product_details'] = $product_details;

                array_push($response["grnDetails"], $grn);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No GRN data found.";
        }

        echo json_encode($response);
    }
}
