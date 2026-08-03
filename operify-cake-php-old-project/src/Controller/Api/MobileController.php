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

class MobileController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $dbs = "tpplerp";
        ConnectionManager::config($dbs, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'tpplerp',
            'password' => 'tpplerp@23~',
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
        $this->loadModel('Production');
        $this->loadModel('Maintenance');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Productionorder');
        $this->loadModel('InspectionReport');
        $this->loadModel('Vendor');
        $this->loadModel('Indentpo');
        $this->loadModel('Reverseindent');
        $this->loadModel('Additem');
        $this->loadModel('Payments');
        $this->loadModel('Stockregister');
        $this->loadModel('Machinemaster');
        $this->loadModel('Taxmaster');

        parent::beforeFilter($event);
        $this->loadComponent('Cookie');
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow([
            'login',
            'grn',
            'purchaseorder',
            'vendor',
            'indent',
            'reverse',
            'dashboard',
            'production',
            'contract',
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
            'sendNotification',
            'getGoogleAccessToken'
        ]);
    }


    //----------------------------- Login API --------------------------------
    public function login()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $username = trim($this->request->data['mobile']);
            $password = trim($this->request->data['password']);
            $device_id = trim($this->request->data['device_id']);

            $response = array();

            if ($username == '' || $password == '') {
                $response['status'] = false;
                $response['message'] = "Mobile No. or Password cannot be blank.";
            } else {
                $user = $this->Users->find('all')->where(['mobile' => $username])->first();
                if (!empty($user)) {
                    if ($user['confirm_pass'] == $password) {
                        $checkdeviced = $this->Device->find('all')->where(['Device.device_id' => $device_id])->first();
                        if (empty($checkdeviced)) {
                            $newentity['device_id'] = $device_id;
                            $userDevice = $this->Device->patchEntity($this->Device->newEntity(), $newentity);
                            if ($device = $this->Device->save($userDevice)) {
                                $lstid = $device->id;
                                $user->device_id = $lstid;
                                $this->Users->save($user);
                            }
                        } else {
                            $user->device_id = $checkdeviced['id'];
                            $this->Users->save($user);
                        }

                        $response['status'] = true;
                        $response['message'] = "Logged in Sucessfully.";
                        $response['userId'] = $user['id'];
                        $response['device_id'] = $device_id;

                        $response['authtoken'] = JWT::encode(
                            [
                                'sub' => $user['id'],
                                'exp' => time() + 2592000,
                            ],
                            Security::salt()
                        );
                        // $cryptor = new Encryptor;
                        // $response['output'] = $cryptor->encrypt(json_encode($data), DECRYPT);

                    } else {
                        $response['status'] = false;
                        $response['message'] = "Password does not match.";
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = "Mobile Number does not exist.";
                }
            }
            echo json_encode($response);
        }
    }
    //------------------------------------------ Create api of upload Token  ----------------------------/
    public function uploadToken()
    {
        $this->autoRender = false;
        if ($this->request->is("post")) {
            $token = $this->request->data["token"];
            $device_id = $this->request->data["uniqueDeviceId"];

            if (empty($token) || empty($device_id)) {
                $response["success"] = false;
                $response["message"] = "Invalid Parameter";
            } else {
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
            echo json_encode($response);
            return;
        }

    }
    //------------------------------------------ Create api of versioncheck  ----------------------------/
    function versioncheck()
    {
        $this->autoRender = false;
        $findbuild = $this->Users->find()->where(['role_id' => 1])->first();

        if ($this->request->is('post')) {
            $android_build_no = $this->request->data['android_build_no'];
            $ios_build_no = $this->request->data['ios_build_no'];

            $response = array();
            if ($android_build_no) {
                if ($findbuild['android_build_no'] == $android_build_no) {
                    $response['success'] = true;
                    $response['app_url'] = null;
                    $response['message'] = 'Build No. Match';
                } else {
                    $response['success'] = false;
                    $response['app_url'] = 'https://play.google.com/store/apps/details?id=com.tppl&hl=en&gl=US&pli=1';
                    $response['message'] = 'Please update your app.';
                }
            }

            if ($ios_build_no) {
                if ($findbuild['ios_build_no'] == $ios_build_no) {
                    $response['success'] = true;
                    $response['app_url'] = null;
                    $response['message'] = 'Build No. Match';
                } else {
                    $response['success'] = false;
                    $response['app_url'] = 'https://testflight.apple.com/join/tjYNRxpZ';
                    $response['message'] = 'Please update your app.';
                }
            }
            echo json_encode($response);
            return;
        }
    }
    //----------------------------- Dashboard API --------------------------------

    public function dashboard()
    {
        $this->autoRender = false;


        $user_id = trim($this->request->data['user_id']);
        $user = $this->Users->find()->contain(['Device'])->where(['Users.id' => $user_id])->first();
        // pr($user);die;

        $response["success"] = true;
        $response["message"] = "Data fetched successfully.";
        $response["device_id"] = $user['device']['device_id'];

        $currentdate = date('Y-m-d');
        $lastweek = date('Y-m-d', strtotime('-7 days'));
        $monthdate = date('Y-m-01');
        $financialyear = date('Y-04-01', strtotime('-1 year'));

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
        $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.purchaseorder_id' => 'DESC', 'Purchaseorder.id' => 'DESC'])->limit(5)->toarray();
        $response["poDetails"] = array();
        foreach ($podata as $value) {
            $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

            $purchaseorder['purchaseorder_id'] = $value['purchaseorder_id'];
            $purchaseorder['po_primary'] = $value['id'];
            $purchaseorder['is_revised'] = $value['is_revised'];

            if ($value['is_revised'] > 0) {
                $purchaseorder['poid'] = $value['purchaseorder_id'] . ' R-' . $value['is_revised'];
            } else {
                $purchaseorder['poid'] = $value['purchaseorder_id'];
            }

            // $purchaseorder['poid'] = $value['purchaseorder_id'];
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


    //----------------------------- PurchaseOrder API --------------------------------
    public function purchaseorder()
    {
        $this->autoRender = false;
        $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.purchaseorder_id' => 'DESC', 'Purchaseorder.id' => 'DESC'])->toarray();
        if (isset($podata) && !empty($podata)) {
            $response["success"] = true;
            $response["message"] = "PurchaseOrder Details fetched successfully.";
            $response["poDetails"] = array();
            foreach ($podata as $value) {
                $vendors = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();

                $purchaseorder['purchaseorder_id'] = $value['purchaseorder_id'];
                $purchaseorder['po_primary'] = $value['id'];
                $purchaseorder['is_revised'] = $value['is_revised'];

                if ($value['is_revised'] > 0) {
                    $purchaseorder['poid'] = $value['purchaseorder_id'] . ' R-' . $value['is_revised'];
                } else {
                    $purchaseorder['poid'] = $value['purchaseorder_id'];
                }


                $purchaseorder['date'] = date('d-m-Y', strtotime($value['added_time']));
                $purchaseorder['supplier'] = $vendors['name'];
                $purchaseorder['qty'] = $value['total_qty'];
                $purchaseorder['amount'] = $value['total_amt'];
                $purchaseorder['delivery'] = date('d-m-Y', strtotime($value['delivery_date']));
                array_push($response["poDetails"], $purchaseorder);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No PurchaseOrder data fetched.";
        }
        echo json_encode($response);
    }


    //----------------------------- GRN API --------------------------------
    public function grn()
    {
        $this->autoRender = false;
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
            $response["message"] = "No GRN data fetched.";
        }
        echo json_encode($response);
    }

    //----------------------------- Vendor API --------------------------------
    public function vendor()
    {
        $this->autoRender = false;
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
            $response["message"] = "No Vendors data fetched.";
        }
        echo json_encode($response);
    }

    //----------------------------- Vendorsreport API --------------------------------
    public function vendorsreport()
    {
        $this->autoRender = false;

        $vendorsreport = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        if (isset($vendorsreport) && !empty($vendorsreport)) {
            $response["success"] = true;
            $response["message"] = "Vendors Track fetched successfully.";
            $response["vendorTrack"] = array();
            foreach ($vendorsreport as $value) {
                $vendr = $this->Vendor->find('all')->where(['Vendor.id' => $value['vendor_id']])->first();
                $vendortarck['date'] = date("d-m-Y", strtotime($value['inwarddate']));
                $vendortarck['grn_no'] = $value['id'];
                $vendortarck['po_no'] = $value['purchaseorder_id'];
                $vendortarck['bill_no'] = $value['bill_no'];
                $vendortarck['vendor'] = $vendr['name'];
                $vendortarck['amount'] = number_format((float) $value['total_amt'], 2, '.', '');
                array_push($response["vendorTrack"], $vendortarck);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "No Vendors Track data fetched.";
        }
        echo json_encode($response);
    }


    //----------------------------- Indent API --------------------------------
    public function indent()
    {
        $this->autoRender = false;
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
            $response["message"] = "No Indent data fetched.";
        }
        echo json_encode($response);
    }

    //----------------------------- Reverse API --------------------------------
    public function reverse()
    {
        $this->autoRender = false;
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
            $response["message"] = "No Reverse data fetched.";
        }
        echo json_encode($response);
    }



    //----------------------------- ProductionOrder API --------------------------------
    public function production()
    {
        $this->autoRender = false;
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
            $response["message"] = "No Production data fetched.";
        }
        echo json_encode($response);
    }

    // //----------------------------- Stock API --------------------------------
    public function stock()
    {
        $this->autoRender = false;
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

    // //----------------------------- search Stock API --------------------------------
    public function searchstock()
    {
        $this->autoRender = false;

        $item_id = $this->request->data['item_id'];
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));

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

    //----------------------------- Contract API --------------------------------
    public function contract()
    {
        $this->autoRender = false;
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


    //----------------------------- SearchvendorName API --------------------------------
    public function searchvendorname()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $vendorname = trim($this->request->data['vendorname']);

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
    }


    //----------------------------- searchitemname API --------------------------------
    public function searchitemname()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $item_name = trim($this->request->data['item_name']);

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
    }

    //----------------------------- Searchcontract API --------------------------------

    public function searchcontractname()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $workorderno = trim($this->request->data['workorderno']);

            if ($workorderno == '') {
                $response['status'] = false;
                $response['message'] = "Please Enter Workorder No.";
            } else {
                $contractname = $this->Contracts->find('all')->where(['Contracts.workorder LIKE ' => '%' . $workorderno . '%'])->toarray();
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
    }

    //----------------------------- Searchcontract Name API --------------------------------
    public function searchcontract()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $contract_id = trim($this->request->data['contract_id']);

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
    }

    //----------------------------- searchpurchaseorder API --------------------------------
    public function searchpurchaseorder()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $po_id = trim($this->request->data['po_id']);

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
    }

    //----------------------------- searchgrn API --------------------------------
    public function searchgrn()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $po_id = trim($this->request->data['po_id']);

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
    }

    //----------------------------- searchProduction API --------------------------------
    public function searchproduction()
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $contract_id = trim($this->request->data['contract_id']);

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
    }

    //----------------------------- searchIndent API --------------------------------
    public function searchindent()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $contract_id = trim($this->request->data['contract_id']);
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
    }

    //----------------------------- searchReverse API --------------------------------
    public function searchreverse()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $contract_id = trim($this->request->data['contract_id']);
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
    }

    //----------------------------- searchvendorsreport API --------------------------------
    public function searchvendorsreport()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $vendor_id = trim($this->request->data['vendor_id']);
            $date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

            if ($vendor_id == '' || $this->request->data['date_from'] == '' || $this->request->data['date_to'] == '') {
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
    }

    //----------------------------- searchvendor API --------------------------------
    public function searchvendor()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $vendor_id = trim($this->request->data['vendor_id']);
            $date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

            if ($vendor_id == '' || $this->request->data['date_from'] == '' || $this->request->data['date_to'] == '') {
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
    }


    //-------------------------------------------GRN Popup API---------------------
    public function grnpopup()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $gooodsId = trim($this->request->data['goods_id']);
            if ($gooodsId) {
                $response["success"] = true;
                $response["message"] = "GRN Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'goodsreceived/view/' . $gooodsId;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter GRN No.";
            }
            echo json_encode($response);
        }
    }

    //-------------------------------------------purchaseorderpdf API---------------------
    public function purchaseorderpdf()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $purchaseorder_id = trim($this->request->data['purchaseorder_id']);
            $po_primary = trim($this->request->data['po_primary']);
            $is_revised = trim($this->request->data['is_revised']);


            if ($purchaseorder_id) {
                $response["success"] = true;
                $response["message"] = "Purchase Order Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'purchaseorder/view/' . $purchaseorder_id . '/' . $is_revised . '/' . $po_primary;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter PO No.";
            }
            echo json_encode($response);
        }
    }

    //-------------------------------------------Indet pdf API---------------------
    public function indentpdf()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $indentId = trim($this->request->data['indent_id']);
            if ($indentId) {
                $response["success"] = true;
                $response["message"] = "Indent Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'indentpo/viewindentpopdf/' . $indentId;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter Indent No.";
            }
            echo json_encode($response);
        }
    }

    //-------------------------------------------reversepdf API---------------------
    public function reversepdf()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $reverseId = trim($this->request->data['reverse_id']);
            if ($reverseId) {
                $response["success"] = true;
                $response["message"] = "Reverse Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'reverseindent/viewreverseindentpdf/' . $reverseId;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter Reverse No.";
            }
            echo json_encode($response);
        }
    }

    //-------------------------------------------productionorderpdf  API---------------------
    public function productionorderpdf()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $productionId = trim($this->request->data['production_id']);
            if ($productionId) {
                $response["success"] = true;
                $response["message"] = "Production Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'production/viewproductionpdf/' . $productionId;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter Production No.";
            }
            echo json_encode($response);
        }
    }

    //-------------------------------------------contractpdf  API---------------------
    public function contractpdf()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $contractId = trim($this->request->data['contract_id']);
            if ($contractId) {
                $response["success"] = true;
                $response["message"] = "Contract Details fetched successfully.";
                $response["pdfLink"] = ADMIN_URL . 'production/viewcontractdetailspdf/' . $contractId;
            } else {
                $response["success"] = false;
                $response["message"] = "Please Enter Contract No.";
            }
            echo json_encode($response);
        }
    }

    //----------------------------- Maintenance API --------------------------------
    public function maintenance()
    {
        $this->autoRender = false;
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


    public function sendNotification($token, $title, $message)
    {
        // pr($userId);die;
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
}
