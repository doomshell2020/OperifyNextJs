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
class PurchaseorderController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
        $this->Auth->allow([
            'view'
        ]);
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');

        $reqdata = $_GET;
        $vendor_id = $reqdata['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $status = $reqdata['status'];
        $purchaseorder_id = $reqdata['purchaseorder_id'];
        $apk = [];

        if (!empty($purchaseorder_id)) {
            $apk['Purchaseorder.purchaseorder_id'] = $purchaseorder_id;
        }
        if (!empty($vendor_id)) {
            $apk['Purchaseorder.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {
            if ($reqdata['type'] == 'deli') {
                $apk['DATE(Purchaseorder.delivery_date) >='] = $datefrom;
                $apk['DATE(Purchaseorder.delivery_date) <='] = $dateto2;
            } else {
                $apk['DATE(Purchaseorder.added_time) >='] = $datefrom;
                $apk['DATE(Purchaseorder.added_time) <='] = $dateto2;
            }
        }
        if (!empty($status)) {
            $apk['Purchaseorder.postatus'] = $status;
        }

        if ($reqdata) {
            $allpodata = $this->Purchaseorder->find()->where([$apk])->order(['Purchaseorder.id' => 'DESC'])->toarray();
        } else {
            $allpodata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC']);
        }

        $podata = [];
        foreach ($allpodata as $value) {
            // to check is it last revised or not
            $podata1 = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $value['purchaseorder_id']])->order(['Purchaseorder.id' => 'DESC'])->first();
            if ($value['id'] != $podata1['id']) {
                continue;
            } else {
                $podata[] = $value;
            }
        }

        if ($reqdata['type'] == 'deli') {
            usort($podata, function ($a, $b) {
                return strtotime($b['delivery_date']) - strtotime($a['delivery_date']);
            });
        }

        $paginatedData = $this->paginateArray($podata, 50);
        $this->set('podata', $paginatedData['data']);
        $this->set('paging', $paginatedData['paging']);
    }

    // public function add()
    // {
    //     $this->viewBuilder()->layout('admin');
    //     $this->loadModel('Purchaseorder');
    //     $this->loadModel('Purchaseordertemp');
    //     $this->loadModel('Purchaseorderitem');
    //     $this->loadModel('Itemcategory');
    //     $this->loadModel('Companymaster');
    //     $this->loadModel('Additem');
    //     $this->loadModel('Vendors');
    //     $this->loadModel('Stockregister');
    //     $this->loadModel('PurchaseorderDetails');
    //     $this->loadModel('StockAvailable');
    //     $this->loadModel('Device');
    //     $this->loadModel('Users');
    //     $this->loadModel('Taxmaster');


    //     $checkfinancialyear = (date("Y-m-d") >= date("Y-04-01")) ? date("Y-04-01") : date("Y-04-01", strtotime("-1 year"));

    //     $purchaseorderid = $this->Purchaseorder->find('all')->where(['date(Purchaseorder.added_time) >=' => $checkfinancialyear, 'Purchaseorder.is_revised' => '0'])->order(['Purchaseorder.id' => 'desc'])->first();

    //     if ($purchaseorderid['purchaseorder_id'] != "") {
    //         $po_id = explode('-', $purchaseorderid['purchaseorder_id']);
    //         $newpurchaseordertemp = ($po_id[0]) . '-' . (intval($po_id[1]) + 1);
    //     } else {
    //         $current_year = date("Y");
    //         $next_year = $current_year + 1;
    //         $current_year_last_two_digits = substr($current_year, -2);
    //         $next_year_last_two_digits = substr($next_year, -2);
    //         $newpurchaseordertemp = $current_year_last_two_digits . $next_year_last_two_digits . '-1';
    //     }

    //     $this->set('newpurchaseordertemp', $newpurchaseordertemp);




    //     if ($this->request->is(['post', 'put'])) {

    //         if ($this->request->data('vendor_id') == '') {
    //             $this->Flash->error(__('Your enterd Supplier does not exists.'));
    //             return $this->redirect(['action' => 'add']);
    //         }

    //         $userTable2 = TableRegistry::get('Purchaseorder');
    //         $exists2 = $userTable2->exists(['token' => $this->request->data['token']]);
    //         if ($exists2) {
    //             $this->redirect(['controller' => 'Purchaseorder', 'action' => 'index']);
    //         }

    //         $count = count($this->request->data['pitemname']);
    //         $count -= 1;
    //         $taxx = 0;
    //         $qtyy = 0;
    //         $totall = 0;

    //         foreach ($this->request->data['pitemname'] as $key => $value) {
    //             $taxdetails = $this->Taxmaster->find('all')->where(['Taxmaster.id' => $this->request->data['tax_id'][$key]])->first();
    //             $texpercentage = $taxdetails['tax'];

    //             $itemQtyPrice = $this->request->data['pitemquantity'][$key] * $this->request->data['pitemrate'][$key];

    //             if ($this->request->data['tax_cal'] == 1) {
    //                 $itemtaxx = ($itemQtyPrice - ($itemQtyPrice * (100 / (100 + $texpercentage))));
    //                 $totamount = ($itemQtyPrice);
    //             } else {
    //                 $itemtaxx = ($itemQtyPrice * $texpercentage) / 100;
    //                 $totamount = ($itemQtyPrice) + $taxx;
    //             }

    //             $taxx += $itemtaxx;
    //             $totall += $totamount;
    //             $qtyy += $this->request->data['pitemquantity'][$key];
    //         }


    //         $poerder['total_qty'] = $qtyy;
    //         $poerder['total_tax'] = $taxx;
    //         $poerder['total_amt'] = $totall;
    //         $poerder['purchaseorder_id'] = $this->request->data['pono'];
    //         $poerder['vendor_id'] = $this->request->data['vendor_id'];
    //         $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
    //         $poerder['freight'] = $this->request->data['freight'];
    //         $poerder['payment_terms'] = $this->request->data['payment_terms'];
    //         $poerder['transit_insurance'] = $this->request->data['transit_insurance'];
    //         $poerder['remark'] = $this->removeEmojis($this->request->data['payment_term']);
    //         $poerder['payment_term'] = $this->removeEmojis($this->request->data['remark']);
    //         $poerder['added_time'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
    //         $newpo = $this->Purchaseorder->patchEntity($this->Purchaseorder->newEntity(), $poerder);


    //         if ($purchasess = $this->Purchaseorder->save($newpo)) {
    //             $lstid = $purchasess->id;

    //             foreach ($this->request->data['pitemname'] as $key => $value) {
    //                 $taxdetails = $this->Taxmaster->find('all')->where(['Taxmaster.id' => $this->request->data['tax_id'][$key]])->first();
    //                 $texpercentage = $taxdetails['tax'];

    //                 $itemQtyPrice = $this->request->data['pitemquantity'][$key] * $this->request->data['pitemrate'][$key];
    //                 if ($this->request->data['tax_cal'] == 1) {
    //                     $poerder['item_tax_amt'] = ($itemQtyPrice - ($itemQtyPrice * (100 / (100 + $texpercentage))));
    //                     $poerder['item_total_amount'] = ($itemQtyPrice);
    //                 } else {
    //                     $poerder['item_tax_amt'] = ($itemQtyPrice * $texpercentage) / 100;
    //                     $poerder['item_total_amount'] = ($itemQtyPrice) + $poerder['item_tax_amt'];
    //                 }

    //                 $poerder['item_id'] = $value;
    //                 $poerder['poprimary_id'] = $lstid;
    //                 $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
    //                 $poerder['purchaseorder_id'] = $this->request->data['pono'];
    //                 $poerder['item_amt'] = $this->request->data['pitemrate'][$key];
    //                 $poerder['uom'] = $this->request->data['unit_name'][$key];
    //                 $poerder['weight'] = $this->request->data['weight'][$key];
    //                 $poerder['volume'] = $this->request->data['volume'][$key];
    //                 $poerder['tax_id'] = $this->request->data['tax_id'][$key];
    //                 $poerder['inward_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));

    //                 $newpo = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $poerder);
    //                 $this->PurchaseorderDetails->save($newpo);
    //             }
    //         }
    //         $this->Purchaseordertemp->deleteAll(array('Purchaseordertemp.purchaseorder_id' => $newpurchaseordertemp));
    //         $this->Flash->success(__('The Purchase Order created sucessfully'));
    //         $this->request->session()->write('openfess_recipt3', $lstid);
    //         $this->request->session()->write('openfess_recipt5', $newpurchaseordertemp);
    //         return $this->redirect(['action' => 'index']);
    //     }
    // }

    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Vendors');
        $this->loadModel('Stockregister');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('StockAvailable');
        $this->loadModel('Device');
        $this->loadModel('Users');
        $this->loadModel('Taxmaster');


        $checkfinancialyear = (date("Y-m-d") >= date("Y-04-01")) ? date("Y-04-01") : date("Y-04-01", strtotime("-1 year"));

        $purchaseorderid = $this->Purchaseorder->find('all')->where(['date(Purchaseorder.added_time) >=' => $checkfinancialyear, 'Purchaseorder.is_revised' => '0'])->order(['Purchaseorder.id' => 'desc'])->first();

        if ($purchaseorderid['purchaseorder_id'] != "") {
            $po_id = explode('-', $purchaseorderid['purchaseorder_id']);
            $newpurchaseordertemp = ($po_id[0]) . '-' . (intval($po_id[1]) + 1);
        } else {
            $current_year = date("Y");
            $next_year = $current_year + 1;
            $current_year_last_two_digits = substr($current_year, -2);
            $next_year_last_two_digits = substr($next_year, -2);
            $newpurchaseordertemp = $current_year_last_two_digits . $next_year_last_two_digits . '-1';
        }

        $this->set('newpurchaseordertemp', $newpurchaseordertemp);


        if ($this->request->is(['post', 'put'])) {

            // pr($this->request->data);die;

            if (empty($this->request->data['vendor_id'])) {
                $this->Flash->error(__('Your entered Supplier does not exist.'));
                return $this->redirect(['action' => 'add']);
            }

            $poTable = TableRegistry::get('Purchaseorder');
            $poExists = $poTable->exists(['token' => $this->request->data['token']]);
            if ($poExists) {
                return $this->redirect(['action' => 'index']);
            }

            $totalItems = count($this->request->data['pitemname']) - 1;

            $totalTax = 0;
            $totalQuantity = 0;
            $totalAmount = 0;

            foreach ($this->request->data['pitemname'] as $index => $itemName) {
                $taxDetails = $this->Taxmaster->find()
                    ->where(['Taxmaster.id' => $this->request->data['tax_id'][$index]])
                    ->first();

                $taxPercentage = $taxDetails ? $taxDetails['tax'] : 0;

                $unitPrice = (float) $this->request->data['pitemrate'][$index];
                $quantity = (float) $this->request->data['pitemquantity'][$index];
                $baseAmount = $unitPrice * $quantity;

                if ($this->request->data['tax_cal'] == 1) {
                    $taxAmount = $baseAmount - ($baseAmount * (100 / (100 + $taxPercentage)));
                    $lineTotal = $baseAmount;
                } else {
                    $taxAmount = ($baseAmount * $taxPercentage) / 100;
                    $lineTotal = $baseAmount + $taxAmount;
                }

                $totalTax += $taxAmount;
                $totalAmount += $lineTotal;
                $totalQuantity += $quantity;
            }

            $purchaseOrderData = [
                'total_qty' => $totalQuantity,
                'total_tax' => $totalTax,
                'total_amt' => $totalAmount,
                'purchaseorder_id' => $this->request->data['pono'],
                'vendor_id' => $this->request->data['vendor_id'],
                'vendorshipaddress' => $this->request->data['vendorshipaddress'] ?? '',
                'delivery_date' => date('Y-m-d', strtotime($this->request->data['delivery_date'])),
                'freight' => $this->request->data['freight'],
                'payment_terms' => $this->request->data['payment_terms'],
                'transit_insurance' => $this->request->data['transit_insurance'],
                'remark' => $this->removeEmojis($this->request->data['remark']),
                'payment_term' => $this->removeEmojis($this->request->data['payment_term']),
                'added_time' => date('Y-m-d', strtotime($this->request->data['inwarddate'])),
                'revised_date' => date('Y-m-d', strtotime($this->request->data['inwarddate'])),
                'amendment_remarks' => $this->request->data['amendment_remarks'] ?? '',
                'issue_vendor' => $this->request->data['issue_vendor'] === 'Y' ? 'Y' : 'N',
                'postatus' => 'O', // Assuming 'O' means Open or initial state
                'is_revised' => 0
            ];

            $newPOEntity = $this->Purchaseorder->patchEntity($this->Purchaseorder->newEntity(), $purchaseOrderData);

            if ($savedPO = $this->Purchaseorder->save($newPOEntity)) {
                $newPOId = $savedPO->id;

                foreach ($this->request->data['pitemname'] as $index => $itemId) {
                    $taxDetails = $this->Taxmaster->find()
                        ->select(['tax'])
                        ->where(['id' => $this->request->data['tax_id'][$index]])
                        ->first();

                    $taxPercentage = $taxDetails ? $taxDetails->tax : 0;
                    $unitPrice = (float) $this->request->data['pitemrate'][$index];
                    $quantity = (float) $this->request->data['pitemquantity'][$index];
                    $baseAmount = $unitPrice * $quantity;

                    if ($this->request->data['tax_cal'] == 1) {
                        $taxAmount = $baseAmount - ($baseAmount * (100 / (100 + $taxPercentage)));
                        $totalLineAmount = $baseAmount;
                    } else {
                        $taxAmount = ($baseAmount * $taxPercentage) / 100;
                        $totalLineAmount = $baseAmount + $taxAmount;
                    }

                    $poItemData = [
                        'item_id' => $itemId,
                        'poprimary_id' => $newPOId,
                        'item_amt' => $unitPrice,
                        'item_qty' => $quantity,
                        'item_base_price' => $baseAmount,
                        'tax_percentage' => $taxPercentage,
                        'item_tax_amt' => $taxAmount,
                        'item_total_amount' => $totalLineAmount,
                        'purchaseorder_id' => $this->request->data['pono'],
                        'uom' => $this->request->data['unit_name'][$index],
                        'weight' => $this->request->data['weight'][$index],
                        'volume' => $this->request->data['volume'][$index],
                        'tax_id' => $this->request->data['tax_id'][$index],
                        'inward_date' => date('Y-m-d', strtotime($this->request->data['inwarddate'])),
                        'revised_date' => date('Y-m-d', strtotime($this->request->data['inwarddate']))
                    ];

                    $poDetailEntity = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $poItemData);
                    $this->PurchaseorderDetails->save($poDetailEntity);
                    // pr($poDetailEntity);die;
                }

                $this->Flash->success(__('The Purchase Order has been successfully created.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Failed to create the Purchase Order. Please try again.'));
            }
        }
    }

    public function edit($id = null, $poid = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Vendors');
        $this->loadModel('Stockregister');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Users');
        $this->loadModel('StockAvailable');
        $this->loadModel('Taxmaster');
        $this->loadModel('Goodsreceived');

        $taxMaster = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])
            ->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('taxMaster', $taxMaster);

        $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $id, 'Purchaseorder.purchaseorder_id' => $poid])->order(['Purchaseorder.id' => 'Desc'])->first();
        $vendorname = $this->Vendors->find('all')->select(['name', 'id'])->where(['Vendors.id' => $revised['vendor_id']])->first();
        $poitems = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.poprimary_id' => $id])->toarray();

        $this->set(compact('poitems', 'vendorname', 'revised'));
        $totalReceived =  $this->Goodsreceived->find()
            ->where(['purchaseorder_id' => $poid])
            ->sumOf('total_qty');
        // po revised code in edit 28-08-24
        $po = $this->Purchaseorder->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $id])->order(['Purchaseorder.id' => 'Desc'])->first();
            $isrevised = $revised['is_revised'] + 1;

            $count = count($this->request->data['pitemname']);
            $count -= 1;
            $taxx = 0;
            $qtyy = 0;
            $totall = 0;
            for ($i = 0; $i <= $count; $i++) {
                if ($this->request->data['pitemquantity'][$i] != 0) {
                    $taxx += $this->request->data['pitemtax'][$i];
                    $qtyy += $this->request->data['pitemquantity'][$i];
                    $totall += $this->request->data['totalamount'][$i];
                }
            }

            $poId = $this->request->data['purchaseorder_id'] . ' R-' . $isrevised;
            $vendoName = $this->request->data['vendorname'];
            $billDate = date('d-m-Y', strtotime($this->request->data['bill_date']));
            $billNo = $this->request->data['bill_no'];
            $totalAmt = number_format((float) $totall, 2, '.', '');
            $date = date('d-m-Y');
            $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
            foreach ($device_details as $key => $value) {
                $deviceToken = $value['device']['token'];
                $tokens[] = $deviceToken;
            }

            $message = 'A New PO(' . $poId . ') is Revised Dated:' . $date . ' Issued to:' . $vendoName . ' of Amount:' . $totalAmt . '';
            $title = 'PurchaseOrder';
            $push = new \Push(
                'PurchaseOrder',
                $message
            );
            $firebase = new \Firebase();
            $mPushNotification = $push->getPush();

            foreach ($tokens as $tok) {
                $this->sendNotification($tok, $title, $message);
            }
            if ($qtyy === $totalReceived) {
                $poerder['postatus'] = 'C';
            } else {
                $poerder['postatus'] = 'O';
            }
            // $test = $firebase->send($tokens, $mPushNotification);

            $poerder['total_qty'] = $qtyy;
            $poerder['total_tax'] = $taxx;
            $poerder['total_amt'] = $totall;
            $poerder['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
            $poerder['vendor_id'] = $this->request->data['vendor_id'];
            $poerder['vendorshipaddress'] = $this->request->data['vendorshipaddress'];
            $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
            $poerder['freight'] = $this->request->data['freight'];
            $poerder['payment_terms'] = $this->request->data['payment_terms'];
            $poerder['transit_insurance'] = $this->request->data['transit_insurance'];
            $poerder['remark'] = $this->removeEmojis($this->request->data['remark']);
            $poerder['payment_term'] = $this->removeEmojis($this->request->data['payment_term']);
            $poerder['status'] = 'R';
            $poerder['is_revised'] = $isrevised;
            $poerder['added_time'] = date('Y-m-d', strtotime($revised['added_time']));
            $poerder['revised_date'] = date('Y-m-d');
            $poerder['amendment_remarks'] = $this->request->data['amendment_remarks'];
            if ($this->request->data['issue_vendor'] == 'Y') {
                $poerder['issue_vendor'] = 'Y';
            }
            $newpo = $this->Purchaseorder->patchEntity($po, $poerder);

            if ($purchasess = $this->Purchaseorder->save($newpo)) {
                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $poerder['item_id'] = $value;
                    $poerder['poprimary_id'] = $lstid;
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    $poerder['item_tax_amt'] = $this->request->data['pitemtax'][$key];
                    $poerder['item_total_amount'] = $this->request->data['totalamount'][$key];
                    $poerder['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
                    $poerder['item_amt'] = $this->request->data['pitemrate'][$key];
                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    $poerder['weight'] = $this->request->data['weight'][$key];
                    $poerder['volume'] = $this->request->data['volume'][$key];
                    $poerder['tax_id'] = $this->request->data['tax_id'][$key];
                    $poerder['inward_date'] = date('Y-m-d', strtotime($revised['added_time']));
                    $poerder['revised_date'] = date('Y-m-d');

                    $newpo = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $poerder);
                    $poresustnew = $this->PurchaseorderDetails->save($newpo);
                }
            }

            $connsss = ConnectionManager::get('default');
            $dbname = $this->request->session()->read('Auth.User.db');
            $poprimary_id = $revised['id'];
            // for deliery shedule update
            $deliery_update = "UPDATE $dbname.`po_delivery_note` SET `poprimary_id` = $lstid  WHERE `poprimary_id` = '$poprimary_id' ";
            $connsss->execute($deliery_update);

            // for stockregister  update
            $grn_update = "UPDATE $dbname.`st_stock_register` SET `purchaseorder_id` = $lstid  WHERE `purchaseorder_id` = '$poprimary_id' ";
            $connsss->execute($grn_update);

            $this->Flash->success(__('The Purchase Order Sucessfully Revised.'));
            return $this->redirect(['action' => 'index']);
        }
    }


    public function award_quotation($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Vendors');
        $this->loadModel('Stockregister');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Users');
        $this->loadModel('StockAvailable');
        $this->loadModel('Quotation');


        $this->loadModel('QuotationReceived');
        $this->loadModel('QuotationReceivedDetails');

        $quotationReceived = $this->QuotationReceived->find('all')->where(['id' => $id])->first();
        $quotationReceivedDetails = $this->QuotationReceivedDetails->find('all')->where(['received_id' => $id])->toarray();
        $this->set(compact('quotationReceived', 'quotationReceivedDetails'));


        $checkfinancialyear = (date("Y-m-d") >= date("Y-04-01")) ? date("Y-04-01") : date("Y-04-01", strtotime("-1 year"));

        $purchaseorderid = $this->Purchaseorder->find('all')->where(['date(Purchaseorder.added_time) >=' => $checkfinancialyear, 'Purchaseorder.is_revised' => '0'])->order(['Purchaseorder.id' => 'desc'])->first();

        if ($purchaseorderid['purchaseorder_id'] != "") {
            $po_id = explode('-', $purchaseorderid['purchaseorder_id']);
            $newpurchaseordertemp = ($po_id[0]) . '-' . (intval($po_id[1]) + 1);
        } else {
            $current_year = date("Y");
            $next_year = $current_year + 1;
            $current_year_last_two_digits = substr($current_year, -2);
            $next_year_last_two_digits = substr($next_year, -2);
            $newpurchaseordertemp = $current_year_last_two_digits . $next_year_last_two_digits . '-1';
        }

        $vendorname = $this->Vendors->find('all')->select(['name', 'id'])->where(['Vendors.id' => $quotationReceived['vendor_id']])->first();
        $this->set(compact('newpurchaseordertemp', 'vendorname'));





        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data);
            // die;

            if ($this->request->data('vendor_id') == '') {
                $this->Flash->error(__('Your enterd Supplier does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            $userTable2 = TableRegistry::get('Purchaseorder');
            $exists2 = $userTable2->exists(['token' => $this->request->data['token']]);
            if ($exists2) {
                $this->redirect(['controller' => 'Purchaseorder', 'action' => 'index']);
            }


            foreach ($this->request->data['pitemquantity'] as $key => $value) {
                $totalQty += $value;
            }

            $poerder['total_qty'] = $totalQty;
            $poerder['quotation_id'] = $quotationReceived['quotation_id'];
            $poerder['total_tax'] = $this->request->data['bidTax'];
            $poerder['total_amt'] = $this->request->data['bidAmount'];
            $poerder['purchaseorder_id'] = $this->request->data['pono'];
            $poerder['vendor_id'] = $this->request->data['vendor_id'];
            $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
            $poerder['freight'] = $this->request->data['freight'];
            $poerder['transit_insurance'] = $this->request->data['transit_insurance'];
            $poerder['remark'] = $this->request->data['remark'];
            $poerder['payment_term'] = $this->request->data['payment_terms'];
            $poerder['added_time'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
            $newpo = $this->Purchaseorder->patchEntity($this->Purchaseorder->newEntity(), $poerder);


            if ($purchasess = $this->Purchaseorder->save($newpo)) {


                $quotationStatus = $this->Quotation->find()
                    ->where(['Quotation.quotation_id' => $purchasess['quotation_id']])
                    ->first();

                if ($quotationStatus) {
                    $quotationStatus->is_award = 'Y';
                    $quotationStatus->vendor_id = $purchasess['vendor_id'];
                    $quotationStatus->selected_bid_id = $id;
                    $this->Quotation->save($quotationStatus);
                }




                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {

                    $poerder['item_id'] = $value;
                    $poerder['poprimary_id'] = $lstid;
                    $poerder['purchaseorder_id'] = $this->request->data['pono'];

                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    $poerder['item_amt'] = $this->request->data['bid_unit_price'][$key];
                    $poerder['tax_id'] = $this->request->data['tax_id'][$key];
                    $poerder['item_tax_amt'] = $this->request->data['bid_tax'][$key];
                    $poerder['item_total_amount'] = $this->request->data['bid_total_amount'][$key];

                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    $poerder['inward_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));

                    $newpo = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $poerder);
                    $this->PurchaseorderDetails->save($newpo);
                }


                $vendorDetails = $this->Vendors->find('all')->where(['Vendors.id' => $this->request->data['vendor_id']])->first();
                // $message = "Your bided request successfully for awarded.";
                $message = '<!DOCTYPE html>
                <html>
                   <head>
                      <meta charset="UTF-8">
                      <meta name="viewport" content="width=device-width, initial-scale=1.0">
                      <title>Quotation Email</title>
                   </head>
                   <body style="margin: 0px;">
                      <div style="background-color: #448AFF30;">
                         <div style="max-width:600px; margin: auto;  font-family: Arial, Helvetica, sans-serif;">
                            <div style="background-color: #448AFF; ">
                               <div style="width: 150px; margin: auto; background-color: #fff; padding: 10px; border-radius: 0px 0px 8px 8px;">
                                  <img src="' . SITE_URL . 'images/logo.png" alt="logo" style="max-width: 100%;" />
                               </div>
                               <div style=" padding: 55px 20px 55px;">
                                  <div style="text-align: center; height: 180px;">
                                     <img src="' . SITE_URL . 'images/bidAwarded.png" alt="pc" style="max-width: 90%; display: block; margin: auto;"/>
                                  </div>
                               </div>
                               <div style="background-color: #fff; border-radius: 20px 20px 0px 0px;  padding: 20px 20px 0px 20px;">
                                  <div style="height: 120px;"></div>
                                  <h1 style="color: #448AFF; margin: 0px; margin-bottom: 18px; text-align: center;">Hello, <span>' . $vendorDetails['contact_person'] . '</span></h1>
                                  <h6 style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;" >Your Bid Has Been Awarded</h6>
                                  <p style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;">
                         We are pleased to inform you that your bid has been accepted and the contract has been awarded to your company.Kindly get in touch to proceed with the next steps. We look forward to working with you.
                         
                                  </p>
                               </div>
                            </div>
                            <!-- top -->
                         </div>
                      </div>
                   </body>
                </html>';



                $subject = 'Bid Awarded: ' . date("d-m-Y");
                $to = $vendorDetails['email'];
                $this->send_email($to, $subject, $message);
            }
            $this->Purchaseordertemp->deleteAll(array('Purchaseordertemp.purchaseorder_id' => $newpurchaseordertemp));
            $this->Flash->success(__('The Purchase Order created sucessfully'));
            $this->request->session()->write('openfess_recipt3', $lstid);
            $this->request->session()->write('openfess_recipt5', $newpurchaseordertemp);
            return $this->redirect(['action' => 'index']);
        }
    }


    public function searchitem()
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Goodsreceived');
        $this->loadModel('PurchaseorderDetails');

        $reqdata = $_GET;
        $vendor_id = $reqdata['vendor_id'];
        $item_id = $reqdata['item_id'];

        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $status = $reqdata['status'];
        $purchaseorder_id = $reqdata['purchaseorder_id'];
        $apk = [];

        if (!empty($purchaseorder_id)) {
            $apk['Purchaseorder.purchaseorder_id'] = $purchaseorder_id;
        }
        if (!empty($vendor_id)) {
            $apk['Purchaseorder.vendor_id'] = $vendor_id;
        }
        if (!empty($item_id)) {
            $pol['PurchaseorderDetails.item_id'] = $item_id;
        }

        if ($datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {
            if ($reqdata['type'] == 'deli') {
                $apk['DATE(Purchaseorder.delivery_date) >='] = $datefrom;
                $apk['DATE(Purchaseorder.delivery_date) <='] = $dateto2;
            } else {
                $apk['DATE(Purchaseorder.added_time) >='] = $datefrom;
                $apk['DATE(Purchaseorder.added_time) <='] = $dateto2;
            }
        }
        if (!empty($status)) {
            $apk['Purchaseorder.postatus'] = $status;
        }

        $po_id = $this->PurchaseorderDetails->find()->where([$pol])->order(['PurchaseorderDetails.id' => 'DESC'])->toarray();
        $purchaseOrderIds = array_map(fn($item) => $item['purchaseorder_id'], $po_id);
        if (!empty($purchaseOrderIds)) {
            $apk['Purchaseorder.purchaseorder_id IN'] = $purchaseOrderIds;
        }else{
            $apk['Purchaseorder.purchaseorder_id IN'] = 0;
        }
        $this->request->session()->write('apk', $apk);
        $allpodata = $this->Purchaseorder->find()->where([$apk])->order(['Purchaseorder.id' => 'DESC'])->toarray();

        $podata = [];
        foreach ($allpodata as $value) {
            $podata1 = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $value['purchaseorder_id']])->order(['Purchaseorder.id' => 'DESC'])->first();
            if ($value['id'] != $podata1['id']) {
                continue;
            } else {
                $podata[] = $value;
            }
        }

        if ($reqdata['type'] == 'deli') {
            usort($podata, function ($a, $b) {
                return strtotime($b['delivery_date']) - strtotime($a['delivery_date']);
            });
        }
        $paginatedData = $this->paginateArray($podata, 50);
        $this->set('podata', $paginatedData['data']);
        $this->set('paging', $paginatedData['paging']);
    }


    // Rupam Singh -31-05-2025
    public function revised($po_id = null, $id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Vendors');
        $this->loadModel('Stockregister');
        $this->loadModel('Indent');
        $this->loadModel('Taxmaster');
        $this->loadModel('Vendorbillto');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Users');
        $this->loadModel('Device');

        $taxMaster = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])
            ->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('taxMaster', $taxMaster);

        $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $id, 'Purchaseorder.purchaseorder_id' => $po_id])->order(['Purchaseorder.id' => 'Desc'])->first();

        $poitems = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $po_id, 'PurchaseorderDetails.poprimary_id' => $id])->toarray();

        $vendorname = $this->Vendors->find('all')->select(['name', 'id'])->where(['Vendors.id' => $revised['vendor_id']])->first();
        $this->set(compact('poitems', 'revised', 'vendorname'));

        $newpurchaseordertemp = $revised['purchaseorder_id'];

        $po = $this->Purchaseorder->newEntity();

        if ($this->request->is(['post', 'put'])) {

            // pr($this->request->data);

            $existingPO = $this->Purchaseorder->find('all')
                ->where(['Purchaseorder.id' => $po_id])
                ->order(['Purchaseorder.id' => 'Desc'])
                ->first();

            $revisionNumber = $existingPO['is_revised'] + 1;

            $totalItems = count($this->request->data['pitemname']) - 1;

            $totalTax = 0;
            $totalQuantity = 0;
            $totalAmount = 0;

            for ($i = 0; $i <= $totalItems; $i++) {
                if ($this->request->data['pitemquantity'][$i] != 0) {
                    $totalTax += $this->request->data['pitemtax'][$i];
                    $totalQuantity += $this->request->data['pitemquantity'][$i];
                    $totalAmount += $this->request->data['totalamount'][$i];
                }
            }

            $poDisplayId = $this->request->data['purchaseorder_id'] . ' R-' . $revisionNumber;
            $vendorName = $this->request->data['vendorname'];
            $billDate = date('d-m-Y', strtotime($this->request->data['bill_date']));
            $billNo = $this->request->data['bill_no'];
            $formattedTotalAmount = number_format((float) $totalAmount, 2, '.', '');
            $currentDate = date('d-m-Y');

            $deviceUsers = $this->Users->find('all')
                ->contain(['Device'])
                ->where(['Users.id' => 1])
                ->toArray();

            $deviceTokens = [];
            foreach ($deviceUsers as $user) {
                $deviceTokens[] = $user['device']['token'];
            }

            $notificationMessage = 'A New PO(' . $poDisplayId . ') is Revised Dated:' . $currentDate .
                ' Issued to:' . $vendorName . ' of Amount:' . $formattedTotalAmount;

            $pushNotification = new \Push('PurchaseOrder', $notificationMessage);
            $firebase = new \Firebase();
            $pushData = $pushNotification->getPush();
            $notificationTitle = 'PurchaseOrder';

            foreach ($deviceTokens as $token) {
                $this->sendNotification($token, $notificationTitle, $notificationMessage);
            }

            $poData = [
                'total_qty' => $totalQuantity,
                'total_tax' => $totalTax,
                'total_amt' => $totalAmount,
                'purchaseorder_id' => $this->request->data['purchaseorder_id'],
                'vendor_id' => $this->request->data['vendor_id'],
                'vendorshipaddress' => $this->request->data['vendorshipaddress'],
                'delivery_date' => date('Y-m-d', strtotime($this->request->data['delivery_date'])),
                'freight' => $this->request->data['freight'],
                'payment_terms' => $this->request->data['payment_terms'],
                'transit_insurance' => $this->request->data['transit_insurance'],
                'remark' => $this->removeEmojis($this->request->data['remark']),
                'payment_term' => $this->removeEmojis($this->request->data['payment_term']),
                'status' => 'R',
                'is_revised' => $revisionNumber,
                'added_time' => date('Y-m-d', strtotime($existingPO['added_time'])),
                'revised_date' => date('Y-m-d', strtotime($this->request->data['inwarddate'])),
                'amendment_remarks' => $this->request->data['amendment_remarks'],
                'issue_vendor' => $this->request->data['issue_vendor'] === 'Y' ? 'Y' : 'N'
            ];

            $updatedPO = $this->Purchaseorder->patchEntity($po, $poData);

            if ($savedPO = $this->Purchaseorder->save($updatedPO)) {
                $newPOId = $savedPO->id;

                foreach ($this->request->data['pitemname'] as $index => $itemId) {

                    $taxDetails = $this->Taxmaster->find()
                        ->select(['tax'])
                        ->where(['id' => $this->request->data['tax_id'][$index]])
                        ->first();

                    $taxPercentage = $taxDetails ? $taxDetails->tax : 0;

                    $unitPrice = (float) $this->request->data['pitemrate'][$index];
                    $quantity = (float) $this->request->data['pitemquantity'][$index];
                    $baseAmount = $unitPrice * $quantity;
                    $taxAmount = ($baseAmount * $taxPercentage) / 100;
                    $totalLineAmount = $baseAmount + $taxAmount;

                    $itemDetails = [
                        'item_id' => $itemId,
                        'poprimary_id' => $newPOId,
                        'item_amt' => $unitPrice,
                        'item_qty' => $quantity,
                        'item_base_price' => $baseAmount,
                        'tax_percentage' => $taxPercentage,
                        'item_tax_amt' => $taxAmount,
                        'item_total_amount' => $totalLineAmount,
                        'purchaseorder_id' => $this->request->data['purchaseorder_id'],
                        'uom' => $this->request->data['unit_name'][$index],
                        'weight' => $this->request->data['weight'][$index],
                        'volume' => $this->request->data['volume'][$index],
                        'tax_id' => $this->request->data['tax_id'][$index],
                        'inward_date' => date('Y-m-d', strtotime($existingPO['added_time'])),
                        'revised_date' => date('Y-m-d', strtotime($this->request->data['inwarddate']))
                    ];

                    $poDetail = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $itemDetails);
                    $this->PurchaseorderDetails->save($poDetail);
                }
            }

            $this->Purchaseordertemp->deleteAll(['Purchaseordertemp.purchaseorder_id' => $this->request->data['purchaseorder_id']]);

            $connection = ConnectionManager::get('default');
            $databaseName = $this->request->session()->read('Auth.User.db');
            $oldPOId = $existingPO['id'];

            $updateDeliveryNoteSQL = "UPDATE $databaseName.`po_delivery_note` SET `poprimary_id` = $newPOId WHERE `poprimary_id` = '$oldPOId'";
            $connection->execute($updateDeliveryNoteSQL);


            // for stockregister  update
            $grn_update = "UPDATE $databaseName.`st_stock_register` SET `purchaseorder_id` = $newPOId  WHERE `purchaseorder_id` = '$oldPOId' ";
            $connection->execute($grn_update);



            $this->Flash->success(__('The Purchase Order has been successfully revised.'));

            $this->request->session()->delete('openfess_recipt4');
            $this->request->session()->delete('openfess_recipt3');
            $this->request->session()->delete('openfess_recipt5');

            $this->request->session()->write('openfess_recipt3', $newPOId);
            $this->request->session()->write('openfess_recipt5', $this->request->data['purchaseorder_id']);
            $this->request->session()->write('openfess_recipt4', 1);

            return $this->redirect(['action' => 'index']);
        }
    }

    public function revisedV1($po_id = null, $id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Purchaseordertemp');
        $this->loadModel('Purchaseorderitem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Vendors');
        $this->loadModel('Stockregister');
        $this->loadModel('Indent');
        $this->loadModel('Taxmaster');
        $this->loadModel('Vendorbillto');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Users');
        $this->loadModel('Device');

        // pr('iff');exit;

        $taxMaster = $this->Taxmaster->find('list', ['keyField' => 'tax', 'valueField' => 'tax'])
            ->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('taxMaster', $taxMaster);

        $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $id, 'Purchaseorder.purchaseorder_id' => $po_id])->order(['Purchaseorder.id' => 'Desc'])->first();
        $poitems = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $po_id, 'PurchaseorderDetails.poprimary_id' => $id])->toarray();
        $vendorname = $this->Vendors->find('all')->select(['name', 'id'])->where(['Vendors.id' => $revised['vendor_id']])->first();
        $this->set(compact('poitems', 'revised', 'vendorname'));

        $newpurchaseordertemp = $revised['purchaseorder_id'];

        $po = $this->Purchaseorder->newEntity();

        if ($this->request->is(['post', 'put'])) {

            pr($this->request->data);
            exit;
            // pr($this->request->data['delivery_date']);
            $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $po_id])->order(['Purchaseorder.id' => 'Desc'])->first();
            $isrevised = $revised['is_revised'] + 1;

            $count = count($this->request->data['pitemname']);
            $count -= 1;
            $taxx = 0;
            $qtyy = 0;
            $totall = 0;
            for ($i = 0; $i <= $count; $i++) {
                if ($this->request->data['pitemquantity'][$i] != 0) {
                    $taxx += $this->request->data['pitemtax'][$i];
                    $qtyy += $this->request->data['pitemquantity'][$i];
                    $totall += $this->request->data['totalamount'][$i];
                }
            }

            $poId = $this->request->data['purchaseorder_id'] . ' R-' . $isrevised;
            $vendoName = $this->request->data['vendorname'];
            $billDate = date('d-m-Y', strtotime($this->request->data['bill_date']));
            $billNo = $this->request->data['bill_no'];
            $totalAmt = number_format((float) $totall, 2, '.', '');
            $date = date('d-m-Y');
            $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
            foreach ($device_details as $key => $value) {
                $deviceToken = $value['device']['token'];
                $tokens[] = $deviceToken;
            }

            $message = 'A New PO(' . $poId . ') is Revised Dated:' . $date . ' Issued to:' . $vendoName . ' of Amount:' . $totalAmt . '';
            $push = new \Push(
                'PurchaseOrder',
                $message
            );
            $firebase = new \Firebase();
            $mPushNotification = $push->getPush();
            $title = 'PurchaseOrder';
            foreach ($tokens as $tok) {
                $this->sendNotification($tok, $title, $message);
            }
            // $test = $firebase->send($tokens, $mPushNotification);

            $poerder['total_qty'] = $qtyy;
            $poerder['total_tax'] = $taxx;
            $poerder['total_amt'] = $totall;
            $poerder['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
            $poerder['vendor_id'] = $this->request->data['vendor_id'];
            $poerder['vendorshipaddress'] = $this->request->data['vendorshipaddress'];
            $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
            $poerder['freight'] = $this->request->data['freight'];
            $poerder['payment_terms'] = $this->request->data['payment_terms'];
            $poerder['transit_insurance'] = $this->request->data['transit_insurance'];
            $poerder['remark'] = $this->removeEmojis($this->request->data['remark']);
            $poerder['payment_term'] = $this->removeEmojis($this->request->data['payment_term']);
            $poerder['status'] = 'R';
            $poerder['is_revised'] = $isrevised;
            $poerder['added_time'] = date('Y-m-d', strtotime($revised['added_time']));
            $poerder['revised_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
            $poerder['amendment_remarks'] = $this->request->data['amendment_remarks'];
            // pr($poerder);exit;

            if ($this->request->data['issue_vendor'] == 'Y') {
                $poerder['issue_vendor'] = 'Y';
            }
            $newpo = $this->Purchaseorder->patchEntity($po, $poerder);

            if ($purchasess = $this->Purchaseorder->save($newpo)) {
                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $poerder['item_id'] = $value;
                    $poerder['poprimary_id'] = $lstid;
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    $poerder['item_tax_amt'] = $this->request->data['pitemtax'][$key];
                    $poerder['item_total_amount'] = $this->request->data['totalamount'][$key];
                    $poerder['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
                    $poerder['item_amt'] = $this->request->data['pitemrate'][$key];
                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    $poerder['weight'] = $this->request->data['weight'][$key];
                    $poerder['volume'] = $this->request->data['volume'][$key];
                    $poerder['tax_id'] = $this->request->data['tax_id'][$key];
                    $poerder['inward_date'] = date('Y-m-d', strtotime($revised['added_time']));
                    $poerder['revised_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));

                    $newpo = $this->PurchaseorderDetails->patchEntity($this->PurchaseorderDetails->newEntity(), $poerder);
                    $poresustnew = $this->PurchaseorderDetails->save($newpo);

                    // for entry in stock
                    // if ($poresustnew = $this->PurchaseorderDetails->save($newpo)) {
                    //     $newsr = $this->Stockregister->newEntity();

                    //     $newsrentity['purchaseorder_id'] = $lstid;
                    //     $newsrentity['po_id'] = $this->request->data['purchaseorder_id'];
                    //     $newsrentity['indent_id'] = $this->request->data['indent_id'][$key];
                    //     $newsrentity['item_id'] = $this->request->data['pitemname'][$key];
                    //     $newsrentity['quantity'] = $this->request->data['pitemquantity'][$key];
                    //     $newsrentity['rate'] = $this->request->data['pitemrate'][$key];
                    //     $newsrentity['cost_price'] = $this->request->data['pitemamount'][$key];
                    //     $newsrentity['issue_date'] = date('Y-m-d');
                    //     $newsrentity['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
                    //     $newsrentity['amount'] = $this->request->data['totalamount'][$key];
                    //     $newsrentity['tax_id'] = $this->request->data['tax_id'][$key];
                    //     $newsrentity['tax'] = $this->request->data['pitemtax'][$key];
                    //     $newsrentity['central_store_id'] = '0';
                    //     $newsrentity['central_store_type'] = '0';
                    //     $newsrentity['store_id'] = '0';
                    //     $newsrentity['store_type'] = '4';
                    //     $newsrentity['is_revised'] = $isrevised;
                    //     $newsrentity['store_quantity'] = '0';
                    //     $newsrentity['student_id'] = '0';
                    //     $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                    //     $this->Stockregister->save($podetail);
                    // }
                }
            }

            $this->Purchaseordertemp->deleteAll(array('Purchaseordertemp.purchaseorder_id' => $this->request->data['purchaseorder_id']));

            $connsss = ConnectionManager::get('default');
            $dbname = $this->request->session()->read('Auth.User.db');
            $poprimary_id = $revised['id'];
            // for deliery shedule update
            $deliery_update = "UPDATE $dbname.`po_delivery_note` SET `poprimary_id` = $lstid  WHERE `poprimary_id` = '$poprimary_id' ";
            $connsss->execute($deliery_update);
            $this->Flash->success(__('The Purchase Order Sucessfully Revised'));
            $this->request->session()->delete('openfess_recipt4');
            $this->request->session()->delete('openfess_recipt3');
            $this->request->session()->delete('openfess_recipt5');
            $this->request->session()->write('openfess_recipt3', $lstid);
            $this->request->session()->write('openfess_recipt5', $this->request->data['purchaseorder_id']);
            $this->request->session()->write('openfess_recipt4', 1);
            return $this->redirect(['action' => 'index']);
        }
    }


    public function status($id, $status)
    {
        $this->loadModel('Purchaseorder');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Purchaseorder->get($id);
                $user->status = $status;
                if ($this->Purchaseorder->save($user)) {
                    $this->Flash->success(__('Item status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Purchaseorder->get($id);
                $user->status = $status;
                if ($this->Purchaseorder->save($user)) {
                    $this->Flash->success(__('Item status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('PurchaseorderDetails');
        $purchase = $this->Purchaseorder->get($id);
        $productitem = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.poprimary_id' => $id])->toarray();
        if ($purchase) {
            $this->Purchaseorder->delete($purchase);
            foreach ($productitem as $product1) {
                $this->PurchaseorderDetails->delete($product1);
            }
            $this->Flash->success('The Purchase Order deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }


    public function getsubcategory()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemcategory');
        $id = $this->request->data['dataString'];
        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent' => $id, 'Itemcategory.status' => 'Y'])->order(['Itemcategory.id' => 'asc'])->toarray();
        header('Content-Type: application/json');
        echo json_encode($categary);
        die;
    }

    public function getitemname()
    {
        $this->loadModel('Additem');
        $this->loadModel('Sizemanager');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $numberAfterUnderscore = $this->request->data['number'];

        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y', 'Additem.itemtype' => 'RawMaterial'])->toarray();
        foreach ($searchst as $value) {
            if ($check == 0) {
                echo '<li onclick="cllbckretail0(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
            } else {
                echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail0' . $check . '(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $numberAfterUnderscore . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . '</a></li>';
            }
        }
        die;
    }

    public function getfinisheditemname()
    {
        $this->loadModel('Additem');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y', 'Additem.itemtype' => 'FinishedProduct'])->toarray();
        foreach ($searchst as $value) {
            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . '</a></li>';
        }
        die;
    }

    public function getallitemname()
    {
        $this->loadModel('Additem');
        $stsearch = $this->request->data['fetch'];
        // $check = $this->request->data['check']; // not needed for function name here
        $searchst = $this->Additem->find('all')
            ->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y'])
            ->toArray();

        foreach ($searchst as $value) {
            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail2(\'' . $value['item_name'] . '\', \'' . $value['id'] . '\')">
                <a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . '</a>
              </li>';
        }
        die;
    }





    public function view($id = null, $co = null, $idd = null, $erpID = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('Officersname');
        $this->loadModel('PurchaseorderDetails');
        $this->viewBuilder()->layout('ajax');


        $dbname = $this->request->session()->read('Auth.User.db');
        // pr($dbname);die;
        if (empty($dbname)) {
            $this->connection($erpID);
            $connss = ConnectionManager::get($erpID);

            $site = $connss->execute("SELECT * FROM `sitesettings` limit 1");
            $sitesetting = $site->fetch('assoc');
            $sitedetail = $connss->execute("SELECT * FROM `sitesettings_details` where `status` = 'Y' limit 1");
            $site_details = $sitedetail->fetch('assoc');

            $studentrfidsd = $connss->execute("SELECT * FROM `st_purchaseorder` where `purchaseorder_id`= '" . $id . "' AND `id` = '" . $idd . "' AND `is_revised` = '" . $co . "' order by id desc limit 1");
            $users = $studentrfidsd->fetch('assoc');

            $vendor_id = $users['vendor_id'];
            $vendor = $connss->execute("SELECT * FROM `vendors` where `id`='" . $vendor_id . "' order by id desc limit 1");
            $sup = $vendor->fetch('assoc');

            $stock = $connss->execute("SELECT * FROM `st_purchaseorderDetails` where `poprimary_id`='" . $idd . "' AND `purchaseorder_id` = '" . $id . "' ");
            $puritems = $stock->fetchAll('assoc');
        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.id' => $idd, 'Purchaseorder.is_revised' => $co])->first();
            $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();

            $puritems = $this->PurchaseorderDetails->find('all')->contain(['Additem' => ['Measurementunit']])->where(['PurchaseorderDetails.poprimary_id' => $idd, 'PurchaseorderDetails.purchaseorder_id' => $id])->toarray();

            $officer = $this->Officersname->find('all')->where(['Officersname.designation' => 'Purchase Officer', 'Officersname.status' => 'Y'])->first();
        }

        $this->set(compact(['sitesetting', 'site_details', 'officer']));
        $this->set(compact('users', 'sup', 'puritems', 'co'));

        $this->response->type('pdf');
    }


    public function view1($id = null, $co = null, $idd = null, $erpID = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Emailtemplate');
        $this->viewBuilder()->layout('ajax');


        $dbname = $this->request->session()->read('Auth.User.db');
        if (empty($dbname)) {
            $this->connection($erpID);
            $connss = ConnectionManager::get($erpID);

            $site = $connss->execute("SELECT * FROM `sitesettings` limit 1");
            $sitesetting = $site->fetch('assoc');
            $sitedetail = $connss->execute("SELECT * FROM `sitesettings_details` where `status` = 'Y' limit 1");
            $site_details = $sitedetail->fetch('assoc');

            $studentrfidsd = $connss->execute("SELECT * FROM `st_purchaseorder` where `purchaseorder_id`= '" . $id . "' AND `id` = '" . $idd . "' AND `is_revised` = '" . $co . "' order by id desc limit 1");
            $users = $studentrfidsd->fetch('assoc');

            $vendor_id = $users['vendor_id'];
            $vendor = $connss->execute("SELECT * FROM `vendors` where `id`='" . $vendor_id . "' order by id desc limit 1");
            $sup = $vendor->fetch('assoc');

            $stock = $connss->execute("SELECT * FROM `st_purchaseorderDetails` where `poprimary_id`='" . $idd . "' AND `purchaseorder_id` = '" . $id . "' ");
            $puritems = $stock->fetchAll('assoc');
        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.id' => $idd, 'Purchaseorder.is_revised' => $co])->first();
            $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();

            $puritems = $this->PurchaseorderDetails->find('all')->contain(['Additem' => ['Measurementunit']])->where(['PurchaseorderDetails.poprimary_id' => $idd, 'PurchaseorderDetails.purchaseorder_id' => $id])->toarray();
        }
        $template = $this->Emailtemplate->find('all')->where(['status' => 'Y', 'type_name' => 'PO'])->first();
        //   pr($template['body']);die;
        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('users', 'sup', 'puritems', 'co', 'template'));

        $this->response->type('pdf');
    }

    public function poexcel()
    {
        $this->loadModel('Purchaseorder');
        $where = $this->request->session()->read('apk');
        if (isset($where)) {
            $podata = $this->Purchaseorder->find('all')->where([$where, 'Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->toarray();
        }
        $this->set(compact('podata'));
    }

    public function viewpodetail($id = null, $co = null, $idd = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('PurchaseorderDetails');
        $this->viewBuilder()->layout('ajax');

        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact(['sitesetting', 'site_details']));
        $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.id' => $idd, 'Purchaseorder.is_revised' => $co])->first();
        $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();
        $puritems = $this->PurchaseorderDetails->find('all')->contain(['Additem' => ['Measurementunit']])->where(['PurchaseorderDetails.poprimary_id' => $idd, 'PurchaseorderDetails.purchaseorder_id' => $id])->toarray();
        $this->set(compact('users', 'sup', 'puritems', 'co'));
        // $this->response->type('pdf');
    }



    public function viewpodetailspdf($id = null, $co = null, $idd = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('PurchaseorderDetails');

        $this->viewBuilder()->layout('ajax');
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.id' => $idd, 'Purchaseorder.is_revised' => $co])->first();
        $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();
        $puritems = $this->PurchaseorderDetails->find('all')->contain(['Additem' => ['Measurementunit']])->where(['PurchaseorderDetails.poprimary_id' => $idd, 'PurchaseorderDetails.purchaseorder_id' => $id])->toarray();
        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('users', 'sup', 'puritems', 'co'));
        $this->response->type('pdf');
    }

    public function viewdetail($id = null)
    {
        $this->loadModel('Stockregister');
        $this->viewBuilder()->layout('admin');
        $podetail = $this->Stockregister->find('all')->contain(['Additem'])->where(['Stockregister.po_id' => $id, 'Stockregister.store_type' => 1])->order(['Stockregister.id' => 'DESC'])->group(['Stockregister.is_revised'])->toarray();
        $this->set('podetail', $podetail);
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

        $purchaseorder = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $this->request->data['id'], 'Purchaseorder.status !=' => 'N'])->order(['Purchaseorder.id' => 'DESC'])->first();
        $poprimary_id = $purchaseorder['id'];
        // pr($poprimary_id);die;
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
        $stockitems = $this->PurchaseorderDetails->find('all')->contain(['Additem', 'Taxmaster'])->where(['PurchaseorderDetails.purchaseorder_id' => $this->request->data['id'], 'PurchaseorderDetails.poprimary_id' => $poprimary_id])->order(['PurchaseorderDetails.id' => 'asc'])->toarray();
        $this->set(compact('stockitems'));
        $this->set('poid', $this->request->data['id']);
        $this->set('purchaseorder_id', $purchaseorder['id']);
        $itemname = $this->Additem->find('list', ['keyField' => 'id', 'valueField' => 'item_name'])->where(['Additem.status' => 'Y'])->order(['Additem.id' => 'asc'])->toarray();
        $this->set('itemname', $itemname);
    }



    public function poreviseditem()
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
        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax_name'])->where(['Taxmaster.status' => 'Y', 'Taxmaster.parent' => '0'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('tax', $tax);
        $revisecount = $this->Stockregister->find('all')->select(['is_revised'])->where(['Stockregister.po_id' => $this->request->data['id'], 'Stockregister.purchaseorder_id' => $this->request->data['ids'], 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '0'])->order(['Stockregister.id' => 'Desc'])->first();
        $stockitems = $this->Stockregister->find('all')->contain(['Additem' => ['Taxmaster']])->where(['Stockregister.po_id' => $this->request->data['id'], 'Stockregister.purchaseorder_id' => $this->request->data['ids'], 'Stockregister.is_revised' => $revisecount['is_revised'], 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '0'])->toarray();
        $this->set(compact('stockitems'));
        $itemname = $this->Additem->find('list', ['keyField' => 'id', 'valueField' => 'item_name'])->where(['Additem.status' => 'Y'])->order(['Additem.id' => 'asc'])->toarray();
        $this->set('itemname', $itemname);
    }

    public function getvendorname()
    {
        $this->loadModel('Vendor');
        $stsearch = $this->request->data['fetch'];
        $searchst = $this->Vendor->find('all')->select(['name', 'id'])->where(['Vendor.name LIKE' => '%' . $stsearch . '%', 'Vendor.status' => 'Y'])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretail(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['name'] . '</a></li>';
        }
        die;
    }

    public function getvendorshipaddress()
    {
        $this->loadModel('Vendorshipfrom');
        $stsearch = $this->request->data['fetch'];
        $retail_id = $this->request->data['retail_id'];
        $searchst = $this->Vendorshipfrom->find('all')->contain(['States', 'Cities'])->where(['Vendorshipfrom.vendor_id' => $retail_id, 'Vendorshipfrom.address LIKE' => '%' . $stsearch . '%'])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretails(' . "'" . $value['address'] . "-" . $value['state']['name'] . "-" . $value['city']['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . "'" . $value['address'] . "-" . $value['state']['name'] . "-" . $value['city']['name'] . "'" . '</a></li>';
        }
        die;
    }

    public function getvendorissue()
    {
        $this->loadModel('Purchaseorder');
        $retail_id = $this->request->data['retail_id'];
        $searchst = $this->Purchaseorder->find('all')->where(['Purchaseorder.vendor_id' => $retail_id, 'Purchaseorder.issue_vendor' => 'Y'])->order(['Purchaseorder.id' => 'ASC'])->toarray();
        $cnt = 1;
        $rtn = '';
        if (isset($searchst) && !empty($searchst)) {
            $rtn .= '<p style="color:red;font-size: 16px;">Issue With Vendor</p>
 <table  class="table table-bordered table-striped dataTable no-footer" role="grid" aria-describedby="example14_info">

 <thead>
   <tr role="row">
   <th class="sorting_disabled" rowspan="1" colspan="1">S.No.</th>
   <th class="sorting_disabled" rowspan="1" colspan="1">PO ID</th>
   <th class="sorting_disabled" rowspan="1" colspan="1">Amendment Remarks</th>
   </tr>
 </thead>
 <tbody>';
            foreach ($searchst as $value) {
                $rtn .= '<tr role="row" class="odd">
                        <td>' . $cnt++ . '</td>
                        <td>' . $value['purchaseorder_id'] . '</td>
                        <td>' . $value['amendment_remarks'] . '</td>
                        <tr>';
            }
            $rtn .= '</tbody></table>';
        }
        echo $rtn;
        die;
    }


    public function getvendorshipaddressall()
    {
        $this->loadModel('Vendorshipfrom');
        $retail_id = $this->request->data['retail_id'];
        $searchst = $this->Vendorshipfrom->find('all')->contain(['States', 'Cities'])->where(['Vendorshipfrom.vendor_id' => $retail_id])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretails(' . "'" . $value['address'] . "-" . $value['state']['name'] . "-" . $value['city']['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . "'" . $value['address'] . "-" . $value['state']['name'] . "-" . $value['city']['name'] . "'" . '</a></li>';
        }
        die;
    }

    public function indentitems()
    {
        $this->loadModel('Indent');
        $this->loadModel('Additem');
        $this->loadModel('Taxmaster');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendorbillto');
        $fetch = $this->request->data['fetch'];
        $fetc = $this->request->data['fetc'];
        $this->set('fetc', $fetc);
        $itemname = $this->Additem->find('All')->contain(['Measurementunit'])->where(['Additem.status' => 'Y', 'Additem.id' => $fetch])->order(['Additem.id' => 'asc'])->first();
        $this->set('itemname', $itemname);
        $taxMaster = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])
            ->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('taxMaster', $taxMaster);
        $indent = $this->Indent->find('list', ['keyField' => 'indent_id', 'valueField' => 'indent_id'])->group(['indent_id'])->where(['Indent.indent_status' => 'P'])->order(['Indent.id' => 'asc'])->toarray();
        $this->set('indent', $indent);
    }

    public function gettax()
    {
        $this->loadModel('Taxmaster');
        $taxid = $this->request->data['fetch'];
        $tax = $this->Taxmaster->find('all')->select(['tax'])->where(['Taxmaster.id' => $taxid])->first();
        echo $tax['tax'];
        die;
    }


    public function getitemdetail()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $response = array();
        $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch'],'Additem.status'=>'Y'])->first();
        // $unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $unitid['unit_id']])->first();
        $response['id'] = $unitid['id'];
        $response['item_name'] = $unitid['item_name'];
        die(json_encode($response));
    }



    // function deliverynote($id = null)
    // {
    //     $this->loadModel('Podeliverynote');
    //     $this->loadModel('PurchaseorderDetails');
    //     $this->loadModel('Additem');

    //     $PurchaseOrderDetails = $this->PurchaseorderDetails->find('all')->contain(['Additem'])->where(['PurchaseorderDetails.poprimary_id' => $id])->toarray();
    //     $this->set('PurchaseOrderDetails', $PurchaseOrderDetails);
    //     $this->set('id', $PurchaseOrderDetails[0]['purchaseorder_id']);

    //     $DeliverNote_data = $this->Podeliverynote->find('all')->where(['Podeliverynote.poprimary_id' => $id, 'Podeliverynote.status' => 'Y'])->group(['delivery_date'])->toarray();
    //     $this->set('DeliverNote_data', $DeliverNote_data);

    //     if ($this->request->is(['post', 'put'])) {
    //         foreach ($this->request->data['item_qty'] as $key => $vall) {
    //             if ($vall >= 1) {
    //                 $cat = $this->Podeliverynote->newEntity();
    //                 $item['item_id'] = $key;
    //                 $item['item_qty'] = $vall;
    //                 $item['po_id'] = $this->request->data['po_id'];
    //                 $item['poprimary_id'] = $this->request->data['po_primary'];
    //                 $item['delivery_date'] = date("Y-m-d", strtotime($this->request->data['delivery_date']));
    //                 $item['delivery_note'] = $this->request->data['delivery_note'];
    //                 $pnewdetail = $this->Podeliverynote->patchEntity($cat, $item);
    //                 $this->Podeliverynote->save($pnewdetail);
    //             }
    //         }
    //         $this->Flash->success(__('Delivery Note added successfully updated.'));
    //         return $this->redirect(['action' => 'index']);
    //     }
    // }



    function deliverynote($id = null)
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Purchaseorder');
        $this->loadModel('Vendors');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Podeliverynote');
        $this->loadModel('Additem');

        $revised = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $id])->first();
        $poitems = $this->PurchaseorderDetails->find('all')->contain('Additem')->where(['PurchaseorderDetails.poprimary_id' => $id])->toarray();
        $vendorname = $this->Vendors->find('all')->select(['name', 'id'])->where(['Vendors.id' => $revised['vendor_id']])->first();
        $this->set(compact('poitems', 'revised', 'vendorname'));



        if ($this->request->is(['post', 'put'])) {
            $deliveryNotes = $this->Podeliverynote->find('all')->where(['poprimary_id' => $id])->toarray();
            $this->Podeliverynote->deleteAll($deliveryNotes);

            foreach ($this->request->data['inwarddate'] as $key => $inwarddate) {
                if ($inwarddate != '') {
                    foreach ($this->request->data['qty'] as $itemid => $qty) {
                        if ($qty[$key] > 0) {
                            $newentity = $this->Podeliverynote->newEntity();
                            $item['item_id'] = $itemid;
                            $item['item_qty'] = $qty[$key];
                            $item['po_id'] = $this->request->data['purchaseorder_id'];
                            $item['poprimary_id'] = $id;
                            $item['delivery_date'] = date("Y-m-d", strtotime($inwarddate));
                            $item['delivery_note'] = $this->request->data['remark'];
                            $item['vendor_id'] = $this->request->data['vendor_id'];
                            $pnewdetail = $this->Podeliverynote->patchEntity($newentity, $item);
                            $this->Podeliverynote->save($pnewdetail);
                        }
                    }
                }
            }
            $this->Flash->success(__('Delivery Schedule added successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    // this function is use to check delivery note data in goodsrecived page.
    function checkdeliverynote()
    {
        $this->loadModel('Podeliverynote');
        $po_id = $this->request->data['id'];

        $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id ' => $po_id])->order(['Purchaseorder.id' => 'DESC'])->first();
        $DeliverNote_data = $this->Podeliverynote->find('all')->where(['Podeliverynote.poprimary_id' => $podata['id'], 'Podeliverynote.status' => 'Y'])->group('delivery_date')->toarray();

        usort($DeliverNote_data, function ($a, $b) {
            return strtotime($a['delivery_date']) - strtotime($b['delivery_date']);
        });

        if ($DeliverNote_data) {
            // foreach ($DeliverNote_data as $value) {
            echo '<div class="col-sm-12">';
            echo '<input style="margin-right: 8px;" id="checkreq" type="checkbox" name="delivery_status" value="' . date("Y-m-d", strtotime($DeliverNote_data[0]['delivery_date'])) . '">';
            echo '<span style=" color:red;" >Delivery Date:-</span>' . date("d-m-Y", strtotime($DeliverNote_data[0]['delivery_date'])) . '';
            echo '<span style="margin-left :10px; color:red;" >Note:-</span>' . $DeliverNote_data[0]['delivery_note'] . '';
            echo '</div>';
            // }
        } else {
            echo '0';
        }
        die;
    }
    function addsupplier()
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Vendor');
        $this->loadModel('States');
        $state = $this->States->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['States.id' => 'Asc'])->toarray();
        $this->set('state', $state);
        if ($this->request->is(['post', 'put'])) {
            $supplier['name'] = $this->request->data['name'];
            $supplier['contact_no'] = $this->request->data['contact_no'];
            $supplier['email'] = $this->request->data['email'];
            $supplier['type'] = $this->request->data['vendortype'];
            $supplier['state_id'] = $this->request->data['billtostate_id'];
            $supplier['address'] = $this->request->data['billtoaddress'];
            $supplier['gst_number'] = $this->request->data['billtogst_number'];
            $supplier['pancard_number'] = $this->request->data['pancard_number'];
            $vendors = $this->Vendor->patchEntity($this->Vendor->newEntity(), $supplier);
            $result = $this->Vendor->save($vendors);
            $this->set('result', $result);
        }
    }


    public function checkpono()
    {
        $this->loadModel('Purchaseorder');
        $po_id = trim($this->request->data['po_ids']);
        $rtender = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $po_id])->count();
        echo json_encode($rtender);
        die;
    }

    public function posummaryreport()
    {
        $this->loadModel('Purchaseorder');
        $where = $this->request->session()->read('apk');
        if (isset($where)) {
            $podata = $this->Purchaseorder->find('list', ['keyField' => 'id', 'valueField' => 'purchaseorder_id'])->select(['id' => 'MAX(id)', 'purchaseorder_id'])->where([$where])->group('purchaseorder_id')->order(['Purchaseorder.id' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $podata = $this->Purchaseorder->find('list', ['keyField' => 'id', 'valueField' => 'purchaseorder_id'])
                ->select(['id' => 'MAX(id)', 'purchaseorder_id'])->group('purchaseorder_id')->order(['id' => 'DESC'])->toArray();
        }
        $this->set(compact('podata'));
    }


    // }

    // Convert associative array to sequential array
    // $groupedData = array_values($groupedData);


    // public function updategrnquery()
    // {
    //     $this->loadModel('Purchaseorder');
    //     $this->loadModel('PurchaseorderDetails');

    //     $purchaseorderno = $this->Purchaseorder->find('all')->order(['Purchaseorder.id' => 'DESC'])->toarray();

    //     foreach ($purchaseorderno as $value) {
    //         $podetails = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.poprimary_id' => 213])->order(['PurchaseorderDetails.id' => 'DESC'])->toarray();

    //         $inwarddate = '';
    //         $vendor_id = '';
    //         $poprimary_id = '';
    //         foreach ($podetails as $totalvalue) {
    //             $inwarddate = date('Y-m-d',strtotime($value['added_time']));
    //             $vendor_id = $value['vendor_id'];
    //             $poprimary_id = $value['id'];

    //             $connsss = ConnectionManager::get('default');
    //             $dbname = $this->request->session()->read('Auth.User.db');
    //             $status_update = "UPDATE $dbname.`st_purchaseorderDetails` SET `inward_date`='$inwarddate' , `vendor_id`= $vendor_id WHERE `poprimary_id`='$poprimary_id'";
    //             $connsss->execute($status_update);

    //         }
    //     }
    // }

    // public function updatepoquery()
    // {
    //     $this->loadModel('Purchaseorder');
    //     $this->loadModel('PurchaseorderDetails');

    //     $purchaseorderno = $this->Purchaseorder->find('all')->order(['Purchaseorder.id' => 'DESC'])->toarray();

    //     // $purchaseorderno = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.tax_id is null'])->order(['PurchaseorderDetails.id' => 'DESC'])->group('purchaseorder_id')->toarray();
    //     // pr($purchaseorderno);die;

    //     foreach ($purchaseorderno as $value) {
    //         $podetails = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $value['purchaseorder_id']])->order(['PurchaseorderDetails.id' => 'DESC'])->toarray();

    //         $totaltax = '';
    //         $totalamount = '';
    //         foreach ($podetails as $totalvalue) {
    //             $totaltax += $totalvalue['item_tax_amt'];
    //             $totalamount += $totalvalue['item_total_amount'];
    //         }

    //         $poid = $value['purchaseorder_id'];
    //         $tax = $totaltax;
    //         $amount =  $totalamount;


    //         $connsss = ConnectionManager::get('default');
    //         $dbname = $this->request->session()->read('Auth.User.db');
    //         $status_update = "UPDATE $dbname.`st_purchaseorder` SET `total_tax`=$tax , `total_amt`= $amount WHERE `purchaseorder_id`='$poid'";

    //         // pr($status_update);die;
    //         $connsss->execute($status_update);

    //     }


    // }

    // public function updategrnquery()
    // {
    //     $this->loadModel('Goodsreceived');
    //     $this->loadModel('Stockregister');

    //     $goodsreceivedno = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC'])->toarray();
    //     // $goodsreceivedno = $this->Stockregister->find('all')->where(['Stockregister.tax_id is null'])->order(['Stockregister.id' => 'DESC'])->group('purchaseorder_id')->toarray();
    //     // pr($goodsreceivedno);die;

    //     foreach ($goodsreceivedno as $value) {
    //         $stockdetails = $this->Stockregister->find('all')->where(['Stockregister.goods_id' => $value['id']])->order(['Stockregister.id' => 'DESC'])->toarray();

    //         $totaltax = '';
    //         $totalamount = '';
    //         foreach ($stockdetails as $totalvalue) {
    //             $totaltax += $totalvalue['tax'];
    //             $totalamount += $totalvalue['amount'];
    //         }

    //         $grnid = $value['id'];
    //         $tax = $totaltax;
    //         $amount =  $totalamount;

    //         $connsss = ConnectionManager::get('default');
    //         $dbname = $this->request->session()->read('Auth.User.db');
    //         $status_update = "UPDATE $dbname.`st_goodsreceive` SET `total_tax`=$tax , `total_amt`= $amount WHERE `id`='$grnid' " ;

    //         // pr($status_update);die;
    //         $connsss->execute($status_update);

    //     }


    // public function updategrnquery()
    // {
    //     $this->loadModel('Goodsreceived');
    //     $this->loadModel('Stockregister');
    //     $goodsreceivedno = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'DESC'])->toarray();
    //     foreach ($goodsreceivedno as $value) {
    //         $grnid = $value['id'];
    //         $vendor_id = $value['vendor_id'];
    //         $connsss = ConnectionManager::get('default');
    //         $dbname = $this->request->session()->read('Auth.User.db');
    //         $vendor_update = "UPDATE $dbname.`st_stock_register` SET `vendor_id`=$vendor_id  WHERE `goods_id`='$grnid' ";
    //         $connsss->execute($vendor_update);
    //     }
    // }


    // last five purchase price of item 14-02-24
    public function viewitemdetail($id = null)
    {
        $this->loadModel('PurchaseorderDetails');
        $this->viewBuilder()->layout('ajax');

        $itemdetails = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.item_id' => $id])->order(['PurchaseorderDetails.inward_date' => 'DESC', 'PurchaseorderDetails.id' => 'DESC'])->toarray();

        // remove revised po
        $podata = [];
        $poid = [];
        foreach ($itemdetails as $value) {
            if (!in_array($value['purchaseorder_id'], $poid)) {
                $poid[] = $value['purchaseorder_id'];
                $podata[] = $value;
            }
        }
        $podata = array_slice($podata, 0, 5);
        $this->set('itemdetails', $podata);
    }

    public function viewitemdetailpdf($id = null, $status = null)
    {
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->viewBuilder()->layout('ajax');
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact(['sitesetting', 'site_details']));

        $itemdetails = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.item_id' => $id])->order(['PurchaseorderDetails.inward_date' => 'DESC', 'PurchaseorderDetails.id' => 'DESC'])->toarray();
        // remove revised po
        $podata = [];
        $poid = [];
        foreach ($itemdetails as $value) {
            if (!in_array($value['purchaseorder_id'], $poid)) {
                $poid[] = $value['purchaseorder_id'];
                $podata[] = $value;
            }
        }
        if (!empty($status)) {
            $this->set('itemdetails', $podata);
        } else {
            $podata = array_slice($podata, 0, 5);
            $this->set('itemdetails', $podata);
        }
    }


    // to export delivery shedule base on date search
    public function deliveryreport()
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Podeliverynote');

        $where = $this->request->session()->read('apk');

        // get all deliverynote
        $apk = [];
        $apk['DATE(Podeliverynote.delivery_date) >='] = $where['DATE(Purchaseorder.added_time) >='] ? $where['DATE(Purchaseorder.added_time) >='] : $where['DATE(Purchaseorder.delivery_date) >='];
        $apk['DATE(Podeliverynote.delivery_date) <='] = $where['DATE(Purchaseorder.added_time) <='] ? $where['DATE(Purchaseorder.added_time) <='] : $where['DATE(Purchaseorder.delivery_date) <='];
        $podeliverydata = $this->Podeliverynote->find('all')->contain('Purchaseorder')->where([$apk, 'Podeliverynote.status' => 'Y'])->order(['Podeliverynote.delivery_date' => 'DESC', 'Podeliverynote.poprimary_id' => 'DESC'])->toarray();

        $deliveryDetails = [];
        $checkexistpo = [];
        $storepo_id = [];
        foreach ($podeliverydata as $entity) {
            $poId = $entity->po_id;
            $deliveryDate = $entity->delivery_date->format('Y-m-d');
            $purchaseOrder_id = ($entity['purchaseorder']['status'] == 'R') ? ($entity['purchaseorder']['purchaseorder_id'] . ' R-' . $entity['purchaseorder']['is_revised']) : $entity['purchaseorder']['purchaseorder_id'];

            // to check is it revised or not and closed
            $podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $poId])->order(['Purchaseorder.id' => 'DESC'])->first();
            if ($entity['poprimary_id'] != $podata['id'] || $entity['Purchaseorder']['postatus'] == 'C') {
                continue;
            }

            $key = $poId . '-' . $deliveryDate;
            if (!isset($checkexistpo[$key])) {
                $storepo_id[] = $poId;
                $deliveryDetails[] = [
                    'delivery_date' => $deliveryDate,
                    'purchaseOrder_id' => $purchaseOrder_id,
                    'poprimary_id' => $entity['poprimary_id'],
                    'vendor_id' => $entity['purchaseorder']['vendor_id'],
                    'po_date' => $entity->purchaseorder->added_time->format('Y-m-d'),
                ];
                $checkexistpo[$key] = true;
            }
        }

        // get purchase order delivery date without delivery note
        $cond = [];
        $cond['DATE(Purchaseorder.delivery_date) >='] = $where['DATE(Purchaseorder.added_time) >='] ? $where['DATE(Purchaseorder.added_time) >='] : $where['DATE(Purchaseorder.delivery_date) >='];
        $cond['DATE(Purchaseorder.delivery_date) <='] = $where['DATE(Purchaseorder.added_time) <='] ? $where['DATE(Purchaseorder.added_time) <='] : $where['DATE(Purchaseorder.delivery_date) <='];
        $podata2 = $this->Purchaseorder->find('all')->where([$cond, 'Purchaseorder.postatus' => 'O'])->order(['Purchaseorder.id' => 'DESC'])->toarray();

        foreach ($podata2 as $value) {
            $poId = $value->purchaseorder_id;
            $deliveryDate = $value->delivery_date->format('Y-m-d');
            $purchaseOrder_id = ($value['status'] == 'R') ? ($value['purchaseorder_id'] . ' R-' . $value['is_revised']) : $value['purchaseorder_id'];

            // to check is it revised or not
            $podata1 = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $poId])->order(['Purchaseorder.id' => 'DESC'])->first();
            if ($value['id'] != $podata1['id']) {
                continue;
            }

            if (!in_array($poId, $storepo_id)) {
                $deliveryDetails[] = [
                    'delivery_date' => $deliveryDate,
                    'purchaseOrder_id' => $purchaseOrder_id,
                    'poprimary_id' => $value['id'],
                    'vendor_id' => $value['vendor_id'],
                    'po_date' => $value->added_time->format('Y-m-d'),
                ];
            }
        }
        usort($deliveryDetails, function ($a, $b) {
            return strtotime($b['delivery_date']) - strtotime($a['delivery_date']);
        });
        $this->set(compact('deliveryDetails'));
    }

    public function productcomparisonreport()
    {
        // item base excel
        // $this->loadModel('Additem');
        // $itemName = $this->Additem->find('all')->where([ 'Additem.status' => 'Y', 'Additem.itemtype' => 'RawMaterial'])->order(['Additem.item_name' => 'ASC'])->toarray();
        // $this->set(compact('itemName'));

        // po base excel
        $where = $this->request->session()->read('apk');
        $allpodata = $this->Purchaseorder->find('all')->where([$where])->order(['Purchaseorder.id' => 'DESC'])->toarray();

        $podata = [];
        foreach ($allpodata as $value) {
            // to check is it revised or not
            $podata1 = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $value['purchaseorder_id']])->order(['Purchaseorder.id' => 'DESC'])->first();
            if ($value['id'] != $podata1['id']) {
                continue;
            } else {
                $podata[] = $value;
            }
        }
        $this->set(compact('podata'));
    }

    // print Delivery schedule
    public function printdeliveryschedule($idd = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('PurchaseorderDetails');

        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.id' => $idd])->first();
        $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();

        $puritems = $this->PurchaseorderDetails->find('all')->contain(['Additem' => ['Measurementunit']])->where(['PurchaseorderDetails.poprimary_id' => $idd])->toarray();

        $this->set(compact('users', 'sup', 'puritems', 'sitesetting', 'site_details'));
        $this->response->type('pdf');
    }

    // for print all po
    public function printallpo($po_no = null)
    {
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendor');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('PurchaseorderDetails');

        $this->viewBuilder()->layout('ajax');
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $users = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $po_no])->order(['Purchaseorder.id' => 'DESC'])->toarray();

        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('users'));
        $this->response->type('pdf');
    }


    // for custom pagenation
    private function paginateArray($data, $limit)
    {
        $page = $this->request->query('page') ?? 1;
        $total = count($data);
        $pages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $paginatedData = array_slice($data, $offset, $limit);
        return [
            'data' => $paginatedData,
            'paging' => [
                'page' => $page,
                'total' => $total,
                'pages' => $pages,
                'limit' => $limit,
                'prev' => $page > 1 ? $page - 1 : null,
                'next' => $page < $pages ? $page + 1 : null,
            ]
        ];
    }

    public function getPoDetails()
    {
        $this->loadModel('Purchaseorder');
        $this->autoRender = false;
        $poId = $this->request->data('purchaseorder_id');
        $poDetails = $this->Purchaseorder->find('all')->where(['Purchaseorder.purchaseorder_id' => $poId])->first();

        if ($poDetails) {
            $minDate = date('d-m-Y', strtotime($poDetails->added_time)); // Example field
            echo json_encode($minDate);
        } else {
            echo json_encode(null);
        }
    }
}
