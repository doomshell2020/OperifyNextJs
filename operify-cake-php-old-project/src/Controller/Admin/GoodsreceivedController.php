<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'Firebase.php';
require_once 'Push.php';
class GoodsreceivedController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'view'
        ]);
    }

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Vendor');

        if ($this->request->session()->read('openfess_recipt3')) {
            $ids3 = $this->request->session()->read('openfess_recipt3');
            $ids4 = $this->request->session()->read('openfess_recipt4');
            $this->set(compact('ids3'));
            $this->set(compact('ids4'));
            $this->request->session()->delete('openfess_recipt3');
            $this->request->session()->delete('openfess_recipt4');
        }


        $req_data = $_GET;
        $vendor_id = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));
        $purchaseorder_id = $req_data['purchaseorder_id'];

        $apk = [];
        if (!empty($purchaseorder_id)) {
            $apk['Goodsreceived.purchaseorder_id'] = $purchaseorder_id;
        }
        if (!empty($vendor_id)) {
            $apk['Goodsreceived.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) <='] = $dateto2;
        }


        if ($req_data) {
            $goodsreceived = $this->Goodsreceived->find('all')->where([$apk])->order(['Goodsreceived.id' => 'DESC']);
        } else {
            $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC']);
        }
        $goodsreceived = $this->paginate($goodsreceived)->toarray();
        $this->set(compact('goodsreceived'));

        $supplier = $this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
        $this->set('company', $supplier);
    }


    //add goodreceived function
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Itemname');
        $this->loadModel('Suppliers');
        $this->loadModel('Stockregister');
        $this->loadModel('Goodsreceived');
        $this->loadModel('StockAvailable');
        $this->loadModel('Payments');
        $this->loadModel('Podeliverynote');
        $this->loadModel('Vendors');
        $this->loadModel('InspectionGrn');
        $this->loadModel('Users');
        $this->loadModel('Goodsreceived');

        $inspectionPoIds = $this->InspectionGrn->find()
            ->select(['po_id'])
            ->distinct(['po_id'])
            ->toArray();


        $poIdList = array_column($inspectionPoIds, 'po_id');

        $grPoIds = $this->Goodsreceived->find()
            ->select(['purchaseorder_id'])
            ->distinct(['purchaseorder_id'])
            ->where([
                'purchaseorder_id IN' => $poIdList
            ])
            ->toArray();

        $grPoIdList = array_column($grPoIds, 'purchaseorder_id');

        // Step 3: Get po_ids that are in InspectionGrn but not in Goodsreceived at all
        $poWithoutGR = array_diff($poIdList, $grPoIdList);

        // Step 4: Get InspectionGrn entries for those po_ids
        // $InspectionGrn = $this->InspectionGrn->find('list', [
        //     'keyField' => 'inspection_id',
        //     'valueField' => 'inspection_id'
        // ])
        //     ->where([
        //         'InspectionGrn.status' => 'Y'
        //     ])
        //     ->order(['InspectionGrn.inspection_id' => 'desc'])
        //     ->toArray();
        $InspectionGrn = $this->InspectionGrn->find('list', [
            'keyField' => 'inspection_id',
            'valueField' => function ($row) {
                return $row['inspection_id'] . ' (PO ID: ' . $row['po_id'] . ')';
            }
        ])
            ->where([
                'InspectionGrn.status' => 'Y'
            ])
            ->order(['InspectionGrn.inspection_id' => 'desc'])
            ->toArray();
        $this->set('InspectionGrn', $InspectionGrn);



        // $inspectionPoIds = $this->InspectionGrn->find()
        //     ->select(['po_id'])
        //     ->distinct(['po_id'])
        //     ->where(['status' => 'N'])
        //     ->toArray();
        // // pr($inspectionPoIds);exit;
        // $poIdList = array_column($inspectionPoIds, 'po_id');
        // $pendingPoIds = [];

        // foreach ($poIdList as $poId) {
        //     $lastPo = $this->Purchaseorder->find()
        //         ->where(['purchaseorder_id' => $poId])
        //         ->order(['id' => 'DESC'])
        //         ->first();

        //     $totalOrdered = $lastPo ? $lastPo->total_qty : 0;

        //     $totalReceived =  $this->Goodsreceived->find()
        //         ->where(['purchaseorder_id' => $poId])
        //         ->sumOf('total_qty');

        //     if ($totalReceived < $totalOrdered) {
        //         $pendingPoIds[] = $poId;
        //     }
        // }

        // $InspectionGrn = $this->InspectionGrn->find('list', [
        //     'keyField' => 'inspection_id',
        //     'valueField' => 'inspection_id'
        // ])
        //     ->where(['status' => 'N'])
        //     ->order(['InspectionGrn.inspection_id' => 'desc'])
        //     ->toArray();

        // $this->set('InspectionGrn', $InspectionGrn);





        $goodsrecived = $this->Goodsreceived->newEntity();

        if ($this->request->is(['post'])) {

            $po_idd = $this->request->data['purchaseorder_id'];
            if ($this->request->data['delivery_status']) {
                // foreach ($this->request->data['delivery_status'] as $key => $val) {
                $delivery_date = $this->request->data['delivery_status'];
                $connsss = ConnectionManager::get('default');
                $dbname = $this->request->session()->read('Auth.User.db');
                $status_update = "UPDATE $dbname.`po_delivery_note` SET `status`='N' WHERE `delivery_date`='$delivery_date' and `po_id`='$po_idd'";
                $connsss->execute($status_update);
                // }
            }
            $count = count($this->request->data['pitemname']);
            $count -= 1;
            $grn_total_qty = $this->Goodsreceived->find()
                ->select([
                    'total_qty_sum' => $this->Goodsreceived->find()->func()->sum('total_qty')
                ])
                ->where(['purchaseorder_id' => $this->request->data['purchaseorder_id']])
                ->first();

            // pr($grn_total_qty);exit;
            // if ($this->request->data['tqty'] == 0) {
            //     $this->Flash->error(__("Kindly update Received Quantity For Purchase Order Items! "));
            //     return $this->redirect(['controller' => 'goodsreceived', 'action' => 'add']);
            // }
            $taxx = 0;
            $qtyy = 0;
            $totall = 0;
            for ($i = 0; $i <= $count; $i++) {
                if (isset($this->request->data['pitemquantity'][$i]) != 0) {

                    $newsrentity['po_id'] = $this->request->data['purchaseorder_id'];

                    $newsrentity['indent_id'] = $this->request->data['indent_id'][$i];
                    $newsrentity['item_id'] = $this->request->data['pitemname'][$i];
                    $newsrentity['quantity'] = $this->request->data['pitemquantity'][$i];
                    $newsrentity['rate'] = $this->request->data['pitemrate'][$i];
                    $newsrentity['cost_price'] = $this->request->data['pitemamount'][$i];
                    $newsrentity['amount'] = $this->request->data['totalamount'][$i];
                    $newsrentity['tax_id'] = $this->request->data['tax_id'][$i];
                    $newsrentity['tax'] = $this->request->data['pitemtax'][$i];
                    $taxx += $this->request->data['pitemtax'][$i];
                    $qtyy += $this->request->data['pitemquantity'][$i];
                    $pendingqtyy += $this->request->data['pendingqty'][$i];
                    $totall += $this->request->data['totalamount'][$i];
                    $newsrentity['central_store_id'] = '0';
                    $newsrentity['central_store_type'] = '0';
                    $newsrentity['store_id'] = '0';
                    $newsrentity['store_type'] = '1';
                    $newsrentity['store_quantity'] = '0';
                    $newsrentity['student_id'] = '0';
                    $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $this->request->data['pitemname'][$i]])->first();
                    $stock_data_id = $item_data['id'];
                    $conn = ConnectionManager::get('default');
                    $stock_back = $item_data['stock_available'] + $ponewsr['quantity'];
                    $db = $this->request->session()->read('Auth.User.db');
                    $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";
                    $conn->execute($stock_update);
                }
            }

            $poerder['total_qty'] = floatval($qtyy);
            $poerder['total_tax'] = $taxx;
            $poerder['total_amt'] = $totall;
            $poerder['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
            $poerder['vendor_id'] = $this->request->data['vendor_id'];
            $purchaseorder = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $this->request->data['purchaseorder_id'], 'Purchaseorder.status !=' => 'N'])->order(['Purchaseorder.id' => 'DESC'])->first();
            $total_qty = floatval($purchaseorder['total_qty']);
            $totalrecqty = floatval($qtyy + $pendingqtyy);

            $total_qty = trim($total_qty);
            $totalrecqty = trim($totalrecqty);
            $total_qty = round(floatval($total_qty), 2);
            $totalrecqty = round(floatval($totalrecqty), 2);
            // $grn_total = $qtyy + $grn_total_qty['total_qty_sum'];
            // if ($total_qty === $grn_total) {
            //     $poerder['status'] = 'C';
            // } else {
            //     $poerder['status'] = 'O';
            // }
            $poid = $this->request->data['purchaseorder_id'];
            // $postatus = $poerder['status'];
            $poerder['inwarddate'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
            $poerder['bill_date'] = date('Y-m-d', strtotime($this->request->data['bill_date']));
            $poerder['freight'] = 0;
            $poerder['bill_no'] = $this->request->data['bill_no'];
            $poerder['remark'] = $this->removeEmojis($this->request->data['remark']);
            $newpo = $this->Goodsreceived->patchEntity($goodsrecived, $poerder);



            if ($poresustnew = $this->Goodsreceived->save($newpo)) {



                $lstid = $poresustnew->id;

                $vendor = $this->Vendors->find('all')->where(['Vendors.id' => $this->request->data['vendor_id']])->first();
                $goodsId = $lstid;
                $poId = $this->request->data['purchaseorder_id'];
                $vendoName = $vendor['name'];
                $billDate = date('d-m-Y', strtotime($this->request->data['bill_date']));
                $billNo = $this->request->data['bill_no'];
                $date = date('d-m-Y');

                $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
                foreach ($device_details as $key => $value) {
                    $deviceToken = $value['device']['token'];
                    $tokens[] = $deviceToken;
                }

                $message = 'A New GRN(' . $goodsId . ') is Added Dated:' . $date . ' Against PO:' . $poId . ' From Supplier:' . $vendoName . ' with bill Dated:' . $billDate . ' and Bill No:' . $billNo . '.';
                $push = new \Push(
                    'GRN',
                    $message
                );
                $firebase = new \Firebase();
                $mPushNotification = $push->getPush();
                $title = 'GRN';
                foreach ($tokens as $tok) {
                    $this->sendNotification($tok, $title, $message);
                }
                // $test = $firebase->send($tokens, $mPushNotification);
                // pr($test);die;

                $item['inwarddate'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
                $item['bill_date'] = date('Y-m-d', strtotime($this->request->data['bill_date']));
                $item['bill_no'] = $this->request->data['bill_no'];
                $item['vendor_id'] = $this->request->data['vendor_id'];
                $item['total_amt'] = $totall;
                $item['remark'] = $this->removeEmojis($this->request->data['remark']);
                $item['store_type'] = '1';
                $item['goods_id'] = $lstid;
                $pnewdetail = $this->Payments->patchEntity($this->Payments->newEntity(), $item);
                $resustnew = $this->Payments->save($pnewdetail);


                for ($i = 0; $i <= $count; $i++) {
                    if ($this->request->data['pitemquantity'][$i] != 0) {
                        $newsr = $this->Stockregister->newEntity();
                        $purchaseid = $this->Purchaseorder->find('all')->select(['id', 'delivery_date'])->where(['Purchaseorder.purchaseorder_id' => $this->request->data['purchaseorder_id']])->order(['Purchaseorder.id' => 'DESC'])->first();
                        $newsrentity['delivery_schedule_id'] = $this->request->data['delivery_schedule_id'][$i];
                        $newsrentity['purchaseorder_id'] = $purchaseid['id'];
                        $newsrentity['po_id'] = $this->request->data['purchaseorder_id'];
                        $newsrentity['goods_id'] = $lstid;
                        $newsrentity['vendor_id'] = $this->request->data['vendor_id'];
                        $newsrentity['indent_id'] = $this->request->data['indent_id'][$i];
                        $newsrentity['item_id'] = $this->request->data['pitemname'][$i];
                        $newsrentity['quantity'] = $this->request->data['pitemquantity'][$i];
                        $newsrentity['rate'] = $this->request->data['pitemrate'][$i];
                        $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
                        $newsrentity['delivery_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
                        $newsrentity['cost_price'] = $this->request->data['pitemamount'][$i];
                        $newsrentity['amount'] = $this->request->data['totalamount'][$i];
                        $newsrentity['tax_id'] = $this->request->data['tax_id'][$i];
                        $newsrentity['tax'] = $this->request->data['pitemtax'][$i];
                        $newsrentity['central_store_id'] = '0';
                        $newsrentity['central_store_type'] = '0';
                        $newsrentity['store_id'] = '0';

                        $newsrentity['store_type'] = '1';
                        $newsrentity['store_quantity'] = '0';
                        $newsrentity['student_id'] = '0';

                        $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);

                        $ponewsr = $this->Stockregister->save($podetail);
                        $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $this->request->data['pitemname'][$i]])->first();
                        $stock_data_id = $item_data['id'];
                        $conn = ConnectionManager::get('default');
                        $stock_back = $item_data['stock_available'] + $this->request->data['pitemquantity'][$i];

                        $stock_update = "UPDATE `st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";
                        $conn->execute($stock_update);

                        // sanjay code
                        $total_item_qty = $this->Stockregister
                            ->find()
                            ->where(['Stockregister.item_id' => $this->request->data['pitemname'][$i], 'Stockregister.po_id' => $this->request->data['purchaseorder_id']])
                            ->select(['sum_qty' => $this->Stockregister->find()->func()->sum('quantity')])
                            ->first();
                    }
                }

                if ($this->request->data['inspection_id']) {
                    $conn = ConnectionManager::get('default');
                    $inspectionId = $this->request->data['inspection_id'];
                    $inspectionupdate = "UPDATE `grn_inspection` SET `status`='N' WHERE `inspection_id`='$inspectionId'";
                    $conn->execute($inspectionupdate);
                }



                //-----------------sanjay code checke---------------------------//


                $purchaseorder = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $this->request->data['purchaseorder_id'], 'Purchaseorder.status !=' => 'N'])->order(['Purchaseorder.id' => 'DESC'])->first();


                if ($purchaseorder['total_qty'] == $total_item_qty['sum_qty']) {
                    $poerder['status'] = 'C';
                } else {
                    $poerder['status'] = 'O';
                }


                $postatus = $poerder['status'];
                $connection = ConnectionManager::get('default');
                $postatusupdate = "UPDATE `st_purchaseorder` SET `postatus`='$postatus' WHERE `purchaseorder_id`='$poid'";
                $connection->execute($postatusupdate);

                // save in st_goodsreceive
                $connection = ConnectionManager::get('default');
                $savestatusingrn = "UPDATE `st_goodsreceive` SET `status`='$postatus' WHERE `purchaseorder_id`='$poid'";
                $connection->execute($savestatusingrn);


                //----------------------------------------------------------//








                $this->Flash->success(__('The Goods Received Note created Successfully'));
                $this->request->session()->delete('openfess_recipt4');
                $this->request->session()->delete('openfess_recipt3');
                $this->request->session()->write('openfess_recipt3', $newpurchaseordertemp);
                $this->request->session()->write('openfess_recipt4', 0);
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    // view function
    public function view($id = null, $erpID = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendors');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->viewBuilder()->layout('ajax');
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();


        $dbname = $this->request->session()->read('Auth.User.db');
        if (empty($dbname)) {
            $this->connection($erpID);
            $connss = ConnectionManager::get($erpID);

            $site = $connss->execute("SELECT * FROM `sitesettings` limit 1");
            $sitesetting = $site->fetch('assoc');
            $sitedetail = $connss->execute("SELECT * FROM `sitesettings_details` where `status` = 'Y' limit 1");
            $site_details = $sitedetail->fetch('assoc');

            $studentrfidsd = $connss->execute("SELECT * FROM `st_goodsreceive` where `id`='" . $id . "' order by id desc limit 1");
            $users = $studentrfidsd->fetch('assoc');
            $vendor_id = $users['vendor_id'];
            $vendor = $connss->execute("SELECT * FROM `vendors` where `id`='" . $vendor_id . "' order by id desc limit 1");
            $sup = $vendor->fetch('assoc');
            $stock = $connss->execute("SELECT * FROM `st_stock_register` where `goods_id`='" . $id . "'  AND `status` != 'N' AND `store_type` = '1' ");
            $puritems = $stock->fetchAll('assoc');
        } else {
            $users = $this->Goodsreceived->find('all')->where(['Goodsreceived.id' => $id])->order(['Goodsreceived.id' => 'DESC'])->first();
            $sup = $this->Vendors->find('all')->select(['name', 'contact_no', 'email'])->where(['Vendors.id' => $users['vendor_id']])->first();
            $puritems = $this->Stockregister->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Stockregister.goods_id' => $id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '1'])->toarray();
        }
        $this->set(compact('sitesetting', 'site_details'));
        $this->set(compact('users', 'sup', 'puritems', 'co'));
        $this->response->type('pdf');
    }

    public function viewgrndetail($id = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendors');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->viewBuilder()->layout('ajax');
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact('sitesetting', 'site_details'));
        $users = $this->Goodsreceived->find('all')->where(['Goodsreceived.id' => $id])->order(['Goodsreceived.id' => 'DESC'])->first();
        $sup = $this->Vendors->find('all')->select(['name', 'contact_no', 'email'])->where(['Vendors.id' => $users['vendor_id']])->first();
        $puritems = $this->Stockregister->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Stockregister.goods_id' => $id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '1'])->toarray();
        $this->set(compact('users', 'sup', 'puritems', 'co'));
    }




    public function getpurchaseorderid()
    {
        $this->loadModel('Purchaseorder');
        $stsearch = $this->request->data['fetch'];
        $searchst = $this->Vendor->find('all')->select(['name', 'id'])->where(['Vendor.name LIKE' => '%' . $stsearch . '%'])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretail(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['name'] . '</a></li>';
        }
        die;
    }

    // good recieve function
    public function goodsrecieve($id = null)
    {
        $this->loadModel('Indent');
        $this->loadModel('Indenttemp');
        $this->viewBuilder()->layout('ajax');
        $users = $this->Indent->find('all')->contain(['Itemcategory', 'Companymaster', 'Itemname', 'Measurementunit'])->where(['Indent.indent_id' => $id])->toarray();
        $this->set(compact('users'));
        $this->response->type('pdf');
    }


    // searching function
    public function searchitem()
    {
        $this->loadModel('Goodsreceived');
        $req_data = $_GET;
        $vendor_id = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));
        $purchaseorder_id = $req_data['purchaseorder_id'];

        $apk = [];
        if (!empty($purchaseorder_id)) {
            $apk['Goodsreceived.purchaseorder_id'] = $purchaseorder_id;
        }
        if (!empty($vendor_id)) {
            $apk['Goodsreceived.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) <='] = $dateto2;
        }

        $this->request->session()->write('apk', $apk);
        $goodsreceived = $this->Goodsreceived->find('all')->where([$apk])->order(['Goodsreceived.id' => 'DESC']);
        $goodsreceived = $this->paginate($goodsreceived)->toarray();
        $this->set(compact('goodsreceived'));
    }



    public function vendorsreport()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Goodsreceived');

        $req_data = $_GET;
        $suppliername = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (isset($suppliername) && $suppliername != '') {
            $cond['Goodsreceived.vendor_id'] = $suppliername;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Goodsreceived.inwarddate) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Goodsreceived.inwarddate) <=' => $dateto2];
            $cond[] = $contra;
        }

        if ($req_data) {
            $goodsreceived = $this->Goodsreceived->find('all')->where([$cond])->order(['Goodsreceived.inwarddate' => 'DESC']);
        } else {
            $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC']);
        }

        $goodsreceived = $this->paginate($goodsreceived)->toarray();
        $this->set(compact('goodsreceived'));
    }


    public function searchstock()
    {
        $this->loadModel('Goodsreceived');

        $req_data = $_GET;
        $suppliername = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (isset($suppliername) && $suppliername != '') {
            $cond['Goodsreceived.vendor_id'] = $suppliername;
        }

        if ($datefrom !== '1970-01-01') {
            $cond['DATE(Goodsreceived.inwarddate) >='] = $datefrom;
        }

        if ($dateto2 !== '1970-01-01') {
            $cond['DATE(Goodsreceived.inwarddate) <='] = $dateto2;
        }

        $this->request->session()->write('cond', $cond);
        $goodsreceived = $this->Goodsreceived->find('all')->where([$cond])->order(['Goodsreceived.inwarddate' => 'DESC']);
        $goodsreceived = $this->paginate($goodsreceived)->toarray();
        $this->set(compact('goodsreceived'));
    }

    public function viewpdf()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Goodsreceived');

        $req_data = $this->request->session()->read('cond');
        if (isset($req_data)) {
            $goodsreceived = $this->Goodsreceived->find('all')->where([$req_data])->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
            $this->set(compact('goodsreceived', 'req_data'));
            $this->request->session()->delete('cond');
        } else {
            $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
            $this->set(compact('goodsreceived'));
        }
    }
    public function summaryexcel()
    {
        $this->loadModel('Goodsreceived');
        $where = $this->request->session()->read('cond');
        if (isset($where)) {
            $goodsreceived = $this->Goodsreceived->find('all')->where([$where])->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
            $this->request->session()->delete('cond');
        } else {
            $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        }
        $this->set(compact('goodsreceived'));
    }

    public function grnexcel()
    {
        $this->loadModel('Goodsreceived');
        $where = $this->request->session()->read('apk');
        if (isset($where)) {
            $goodsreceived = $this->Goodsreceived->find('all')->where([$where])->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        }
        $this->set(compact('goodsreceived'));
    }

    public function getbillno()
    {
        $this->loadModel('Goodsreceived');
        $vendor_id = $this->request->data['vendor_id'];
        $goodsreceived = $this->Goodsreceived->find('all')->where(['Goodsreceived.vendor_id' => $vendor_id])->order(['Goodsreceived.inwarddate' => 'DESC'])->toarray();
        echo json_encode($goodsreceived);
        die;
    }

    public function getgrndetails()
    {
        $this->loadModel('Goodsreceived');
        $this->loadModel('Stockregister');
        $this->loadModel('Taxmaster');

        $goods_id = $this->request->data['goods_id'];
        $goodsreceived = $this->Goodsreceived->find('all')->where(['Goodsreceived.id' => $goods_id])->order(['Goodsreceived.inwarddate' => 'DESC'])->first();
        $response = array();
        $response['billDate'] = date('d-m-Y', strtotime($goodsreceived['bill_date']));
        $stockitems = $this->Stockregister->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Stockregister.goods_id' => $goods_id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '1'])->toarray();
        $response['stockitems'] = array();
        $response['po_id'] = $stockitems[0]['po_id'];
        foreach ($stockitems as $value) {
            $taxpercant = $this->Taxmaster->find('all')->where(['Taxmaster.id' => $value['tax_id']])->first();
            $stockitems = $this->Stockregister->find('all')->select(['quantity' => $this->Stockregister->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.goods_id' => $goods_id, 'Stockregister.item_id' => $value['item_id'], 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '4'])->first();

            $itemname['item_id'] = $value['item_id'];
            $itemname['itemname'] = $value['additem']['item_name'];
            $itemname['return_qty'] = number_format((float) 0, 2, '.', '');
            $itemname['recived_qty'] = number_format((float) $value['quantity'], 2, '.', '');
            $itemname['total_return_qty'] = number_format((float) $stockitems['quantity'], 2, '.', '');
            $itemname['rate'] = number_format((float) $value['rate'], 2, '.', '');
            $itemname['cost_price'] = number_format((float) 0, 2, '.', '');
            $itemname['taxrate'] = $taxpercant['tax'];
            $itemname['taxamount'] = number_format((float) 0, 2, '.', '');
            $itemname['total'] = number_format((float) 0, 2, '.', '');
            array_push($response["stockitems"], $itemname);
        }

        echo json_encode($response);
        die;
    }

    public function grninspection()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('InspectionGrn');
        $this->loadModel('Vendor');

        if ($this->request->session()->read('openfess_recipt3')) {
            $ids3 = $this->request->session()->read('openfess_recipt3');
            $ids4 = $this->request->session()->read('openfess_recipt4');
            $this->set(compact('ids3'));
            $this->set(compact('ids4'));
            $this->request->session()->delete('openfess_recipt3');
            $this->request->session()->delete('openfess_recipt4');
        }


        $req_data = $_GET;
        $vendor_id = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));
        $purchaseorder_id = $req_data['purchaseorder_id'];

        $apk = [];
        if (!empty($purchaseorder_id)) {
            $apk['Goodsreceived.purchaseorder_id'] = $purchaseorder_id;
        }
        if (!empty($vendor_id)) {
            $apk['Goodsreceived.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Goodsreceived.inwarddate) <='] = $dateto2;
        }


        if ($req_data) {
            $goodsreceived = $this->InspectionGrn->find('all')->where([$apk])->order(['InspectionGrn.id' => 'DESC']);
        } else {
            $goodsreceived = $this->InspectionGrn->find('all')->order(['InspectionGrn.id' => 'DESC']);
        }
        $goodsreceived = $this->paginate($goodsreceived)->toarray();
        $this->set(compact('goodsreceived'));

        $supplier = $this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
        $this->set('company', $supplier);
    }

    //add goodreceived function
    public function add_inspection_grn()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Itemname');
        $this->loadModel('Suppliers');
        $this->loadModel('Stockregister');
        $this->loadModel('Goodsreceived');
        $this->loadModel('StockAvailable');
        $this->loadModel('Payments');
        $this->loadModel('Podeliverynote');
        $this->loadModel('Vendors');
        $this->loadModel('InspectionGrn');
        $this->loadModel('InspectionGrnDetails');




        $InspectionGrn = $this->InspectionGrn->find('list', [
            'keyField' => 'po_id',
            'valueField' => 'po_id'
        ])->toArray();


        // $purchaseorderid = $this->Purchaseorder->find('list', [
        //     'keyField' => 'purchaseorder_id',
        //     'valueField' => 'purchaseorder_id'
        // ])->where([
        //     'Purchaseorder.postatus !=' => 'C',
        //     // 'Purchaseorder.purchaseorder_id NOT IN' => array_keys($InspectionGrn)
        // ])->order(['Purchaseorder.id' => 'desc'])->toArray();


        $subquery = $this->Purchaseorder->find()
            ->select([
                'max_id' => $this->Purchaseorder->find()
                    ->func()
                    ->max('id')
            ])
            ->group('purchaseorder_id');

        $latestIds = [];
        foreach ($subquery as $row) {
            $latestIds[] = $row->max_id;
        }

        $latestPOs = $this->Purchaseorder->find()
            ->select(['purchaseorder_id'])
            ->where([
                'Purchaseorder.id IN' => $latestIds,
                'Purchaseorder.postatus !=' => 'C'
            ])
            ->order(['Purchaseorder.id' => 'DESC'])
            ->distinct(['Purchaseorder.purchaseorder_id'])
            ->toArray();

        $purchaseorderid = collection($latestPOs)
            ->combine('purchaseorder_id', 'purchaseorder_id')
            ->toArray();

        $inspectionNo = $this->InspectionGrn->find('all')->order(['id' => 'desc'])->first();

        $inspectionNo = ($inspectionNo) ? ($inspectionNo['inspection_id'] + 1) : 1001;
        $this->set(compact('purchaseorderid', 'inspectionNo'));


        $goodsrecived = $this->InspectionGrn->newEntity();


        if ($this->request->is(['post'])) {


            $count = count($this->request->data['pitemname']);
            $count -= 1;

            $taxx = 0;
            $qtyy = 0;
            $totall = 0;
            for ($i = 0; $i <= $count; $i++) {

                if (isset($this->request->data['pitemquantity'][$i]) != 0) {
                    $taxx += $this->request->data['pitemtax'][$i];
                    $qtyy += $this->request->data['pitemquantity'][$i];
                    $totall += $this->request->data['totalamount'][$i];
                }
            }

            $poerder['total_qty'] = floatval($qtyy);
            $poerder['total_tax'] = $taxx;
            $poerder['total_amt'] = $totall;
            $poerder['po_id'] = $this->request->data['purchaseorder_id'];
            $poerder['inspection_id'] = $this->request->data['inspection_no'];
            $poerder['vendor_id'] = $this->request->data['vendor_id'];
            $poerder['inwarddate'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
            $poerder['bill_date'] = date('Y-m-d', strtotime($this->request->data['bill_date']));
            $poerder['freight'] = $this->request->data['freight'];
            $poerder['bill_no'] = $this->request->data['bill_no'];
            $poerder['remark'] = $this->removeEmojis($this->request->data['remark']);

            $newpo = $this->InspectionGrn->patchEntity($goodsrecived, $poerder);



            if ($this->InspectionGrn->save($newpo)) {
                for ($i = 0; $i <= $count; $i++) {
                    if ($this->request->data['pitemquantity'][$i] != 0) {
                        $newsr = $this->InspectionGrnDetails->newEntity();


                        // $find_delivery_schedule_id = $this->Podeliverynote->find('all')->order(['id' => 'desc'])->first();


                        $newsrentity['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
                        $newsrentity['inspection_id'] = $this->request->data['inspection_no'];;
                        $newsrentity['vendor_id'] = $this->request->data['vendor_id'];
                        $newsrentity['delivery_schedule_id'] = $this->request->data['delivery_schedule_id'][$i];
                        $newsrentity['item_id'] = $this->request->data['pitemname'][$i];
                        $newsrentity['quantity'] = $this->request->data['pitemquantity'][$i];
                        $newsrentity['rate'] = $this->request->data['pitemrate'][$i];
                        $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
                        $newsrentity['delivery_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
                        $newsrentity['cost_price'] = $this->request->data['pitemamount'][$i];
                        $newsrentity['amount'] = $this->request->data['totalamount'][$i];
                        $newsrentity['tax_id'] = $this->request->data['tax_id'][$i];
                        $newsrentity['tax'] = $this->request->data['pitemtax'][$i];
                        // $newsrentity['tax'] = $this->request->data['pitemtax'][$i];


                        $podetail = $this->InspectionGrnDetails->patchEntity($newsr, $newsrentity);
                        $ponewsr = $this->InspectionGrnDetails->save($podetail);
                    }
                }
                // exit;
                $this->Flash->success(__('The Goods Received Note created Successfully'));
                return $this->redirect(['action' => 'grninspection']);
            }
        }
    }


    public function grninspectionexcel()
    {
        $this->loadModel('InspectionGrn');
        $InspectionGrn = $this->InspectionGrn->find('all')->order(['InspectionGrn.id' => 'DESC'])->toarray();
        $this->set(compact('InspectionGrn'));
    }



    public function purchaseorderitems()
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Itemcategory');
        $this->loadModel('Itemlocation');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Taxmaster');
        $this->loadModel('Stockregister');
        $this->loadModel('Vendorbillto');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('InspectionGrn');
        $this->loadModel('InspectionGrnDetails');



        $purchaseorder = $this->InspectionGrn->find('all')->where(['inspection_id' => $this->request->data['id']])->first();
        $poprimary_id = $purchaseorder['id'];
        $this->set('purchaseorder', $purchaseorder);


        $searchst = $this->Vendorbillto->find('all')->contain(['States'])->where(['Vendorbillto.vendor_id' => $purchaseorder['vendor_id']])->first();
        $iid = array();

        if ($searchst['state_id'] != 33) {
            $iid[] = 9;
        } else if ($searchst['state_id'] == 33) {
            $iid[] = 8;
        }

        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax_name'])->where(['Taxmaster.status' => 'Y', 'Taxmaster.parent' => '0', 'Taxmaster.id NOT IN' => $iid])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('tax', $tax);
        // $revisecount = $this->Stockregister->find('all')->select(['is_revised'])->where(['Stockregister.po_id' => $this->request->data['id'], 'Stockregister.purchaseorder_id' => $purchaseorder['id'], 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '4'])->order(['Stockregister.id' => 'Desc'])->order(['Stockregister.id' => 'DESC'])->first();

        $stockitems = $this->InspectionGrnDetails->find('all')->contain(['Additem', 'Taxmaster'])->where(['InspectionGrnDetails.inspection_id' => $this->request->data['id']])->toarray();

        $this->set(compact('stockitems'));

        $this->set('poid', $this->request->data['id']);
        $this->set('purchaseorder_id', $purchaseorder['id']);
        $itemname = $this->Additem->find('list', ['keyField' => 'id', 'valueField' => 'item_name'])->where(['Additem.status' => 'Y'])->order(['Additem.id' => 'asc'])->toarray();
        $this->set('itemname', $itemname);
    }
}
