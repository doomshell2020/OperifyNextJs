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
use Cake\Network\Email\Email;


include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class QuotationController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();

        $this->Auth->allow([
            'vendor_quotation'
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
    }



    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Quotation');

        $quotationDeatails = $this->Quotation->find('all')->order(['Quotation.id' => 'desc'])->toarray();
        $this->set('quotationDeatails', $quotationDeatails);
    }




    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Quotation');
        $this->loadModel('QuotationDetails');
        $this->loadModel('Taxmaster');
        $this->loadModel('Vendors');

        $quotationDeatails = $this->Quotation->find('all')->order(['Quotation.id' => 'desc'])->first();
        $quotationNo = ($quotationDeatails['quotation_id'] != "") ? ($quotationDeatails['quotation_id'] + 1) : 1001;
        $vendornames = $this->Vendors->find('all')->order(['Vendors.name' => 'asc'])->toarray();
        $this->set(compact('vendornames', 'quotationNo'));


        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data);exit;
            $taxx = 0;
            $qtyy = 0;
            $totall = 0;
            foreach ($this->request->data['pitemname'] as $key => $value) {
                $taxdetails = $this->Taxmaster->find('all')->where(['Taxmaster.id' => $this->request->data['tax_id'][$key]])->first();
                $texpercentage = $taxdetails['tax'];

                $itemQtyPrice = $this->request->data['pitemquantity'][$key] * $this->request->data['pitemrate'][$key];

                // if ($this->request->data['tax_cal'] == 1) {
                //     $itemtaxx = ($itemQtyPrice - ($itemQtyPrice * (100 / (100 + $texpercentage))));
                //     $totamount = ($itemQtyPrice);
                // } else {
                //     $itemtaxx = ($itemQtyPrice * $texpercentage) / 100;
                //     $totamount = ($itemQtyPrice) + $taxx;
                // }
                // $taxx += $itemtaxx;
                // $totall += $totamount;
                $qtyy += $this->request->data['pitemquantity'][$key];
                $totall += $this->request->data['totalamount'][$key];
                $taxx += $this->request->data['pitemtax'][$key];
            }


            $poerder['total_qty'] = $qtyy;
            $poerder['total_tax'] = $taxx;
            $poerder['total_amt'] = $totall;
            $poerder['quotation_id'] = $this->request->data['quotation_no'];
            $poerder['vendor_id'] = 0;
            $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
            $poerder['freight'] = $this->request->data['freight'];
            $poerder['payment_terms'] = $this->request->data['payment_terms'];
            $poerder['transit_insurance'] = '';
            $poerder['remark'] = '';
            $poerder['payment_term'] = '';
            $poerder['added_time'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
            $poerder['acceptance_date'] = date('Y-m-d', strtotime($this->request->data['acceptance_date']));

            $newpo = $this->Quotation->patchEntity($this->Quotation->newEntity(), $poerder);


            if ($purchasess = $this->Quotation->save($newpo)) {
                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $taxdetails = $this->Taxmaster->find('all')->where(['Taxmaster.id' => $this->request->data['tax_id'][$key]])->first();
                    $texpercentage = $taxdetails['tax'];

                    $itemQtyPrice = $this->request->data['pitemquantity'][$key] * $this->request->data['pitemrate'][$key];
                    if ($this->request->data['tax_cal'] == 1) {
                        $poerder['item_tax_amt'] = ($itemQtyPrice - ($itemQtyPrice * (100 / (100 + $texpercentage))));
                        $poerder['item_total_amount'] = ($itemQtyPrice);
                    } else {
                        $poerder['item_tax_amt'] = ($itemQtyPrice * $texpercentage) / 100;
                        $poerder['item_total_amount'] = ($itemQtyPrice) + $poerder['item_tax_amt'];
                    }

                    $poerder['item_id'] = $value;
                    $poerder['poprimary_id'] = $lstid;
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    $poerder['quotation_id'] = $this->request->data['quotation_no'];
                    $poerder['item_amt'] = $this->request->data['pitemrate'][$key];
                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    $poerder['weight'] = $this->request->data['weight'][$key];
                    $poerder['volume'] = $this->request->data['volume'][$key];
                    $poerder['tax_id'] = $this->request->data['tax_id'][$key];
                    $poerder['inward_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));

                    $newpo = $this->QuotationDetails->patchEntity($this->QuotationDetails->newEntity(), $poerder);
                    $this->QuotationDetails->save($newpo);
                }
            }
            $this->Flash->success(__('The Quotation created sucessfully.'));
            return $this->redirect(['controller' => 'Quotation', 'action' => 'send_quotations/' . $quotationNo]);
        }
    }




    public function vendor_quotation($linkData)
    {
        $decodedData = base64_decode($linkData);
        $parts = explode('/', $decodedData);
        $quotationNo = $parts[0];
        $vendorId = $parts[1];
        $dbname = $parts[2];

        $this->db($dbname);
        $this->viewBuilder()->layout('admin');

        $this->loadModel('Quotation');
        $this->loadModel('QuotationDetails');
        $this->loadModel('QuotationReceived');
        $this->loadModel('QuotationReceivedDetails');
        $this->loadModel('QuotationSend');
        $this->loadModel('Vendors');

        $quotationCheck = $this->QuotationReceived->find('all')->where(['quotation_id' => $quotationNo, 'vendor_id' => $vendorId])->first();
        if ($quotationCheck) {
            $this->Flash->error(__('You have already bided for this quotation.'));
            return $this->redirect(['prefix' => false, 'controller' => 'Homes', 'action' => 'index']);
        };


        $quotation = $this->Quotation->find('all')->where(['quotation_id' => $quotationNo])->first();
        $quotationItemDeatails = $this->QuotationDetails->find('all')->where(['quotation_id' => $quotationNo])->toarray();

        $this->set(compact('vendorId', 'quotation', 'quotationItemDeatails', 'vendornames'));


        if ($this->request->is(['post', 'put'])) {
            $totalQty = 0;

            if ($quotation['is_award'] == "Y") {

                $this->Flash->error(__('This quotation is already awarded. Kindly try in the next opportunity.'));
                return $this->redirect(['prefix' => false, 'controller' => 'Homes', 'action' => 'index']);
            }

            $currentDate = date('Y-m-d');
            $acceptanceDate = date('Y-m-d', strtotime($quotation['acceptance_date']));

            if ($acceptanceDate < $currentDate) {
                $this->Flash->error(__('This quotation is expired. Kindly try in the next opportunity.'));
                return $this->redirect(['prefix' => false, 'controller' => 'Homes', 'action' => 'index']);
            }



            foreach ($this->request->data['pitemquantity'] as $key => $value) {
                $totalQty += $value;
            }

            $poerder['quotation_id'] = $quotationNo;
            $poerder['vendor_id'] = $vendorId;
            $poerder['quotation_date'] = date('Y-m-d', strtotime($quotation['added_time']));
            $poerder['delivery_date'] = date('Y-m-d', strtotime($this->request->data['delivery_date']));
            // $poerder['remark'] = $this->request->data['remark'];
            $poerder['remark'] = $this->removeEmojis($this->request->data['remark']);
            $poerder['total_qty'] = $totalQty;
            $poerder['total_tax'] = $this->request->data['tax'];
            $poerder['total_amt'] = $this->request->data['amount'];
            $poerder['total_tax_bid'] = $this->request->data['bidTax'];
            $poerder['total_amt_bid'] = $this->request->data['bidAmount'];
            $newpo = $this->QuotationReceived->patchEntity($this->QuotationReceived->newEntity(), $poerder);


            if ($purchasess = $this->QuotationReceived->save($newpo)) {
                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $poerder['quotation_id'] = $quotationNo;
                    $poerder['received_id'] = $lstid;
                    $poerder['vendor_id'] = $vendorId;
                    $poerder['item_id'] = $value;
                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    $poerder['tax_id'] = $this->request->data['tax_id'][$key];
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];

                    $poerder['item_price'] = $this->request->data['pitemrate'][$key];
                    $poerder['item_amt'] = $this->request->data['pitemamount'][$key];
                    $poerder['item_tax_amt'] = $this->request->data['pitemtax'][$key];
                    $poerder['item_total_amount'] = $this->request->data['totalamount'][$key];

                    $poerder['item_price_bid'] = $this->request->data['bid_unit_price'][$key];
                    $poerder['item_amt_bid'] = $this->request->data['bid_total_price'][$key];
                    $poerder['item_tax_amt_bid'] = $this->request->data['bid_tax'][$key];
                    $poerder['item_total_amount_bid'] = $this->request->data['bid_total_amount'][$key];

                    $newpo = $this->QuotationReceivedDetails->patchEntity($this->QuotationReceivedDetails->newEntity(), $poerder);
                    $this->QuotationReceivedDetails->save($newpo);
                }

                $vendorDetails = $this->Vendors->find('all')->where(['Vendors.id' => $vendorId])->first();
                // $message = "You have bided successfully for quotation.";
                $message = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Quotation Email</title>
                </head>
                <body style="margin: 0px;">
                    <div style="background-color: #448AFF30;">
                        <div style="max-width:600px; margin: auto; font-family: Arial, Helvetica, sans-serif;">
                            <div style="background-color: #448AFF;">
                                <div style="width: 150px; margin: auto; background-color: #fff; padding: 10px; border-radius: 0px 0px 8px 8px;">
                                    <img src="' . SITE_URL . 'images/logo.png" alt="logo" style="max-width: 100%;" />
                                </div>
                                <div style="padding: 55px 20px; text-align: center;">
                                    <div style="text-align: center; height: 180px;">
                                        <img src="' . SITE_URL . 'images/bidSuccessfullImg.png" alt="pc" style="max-width: 90%; display: block; margin: auto;"/>
                                    </div>
                                </div>
                                <div style="background-color: #fff; border-radius: 20px 20px 0px 0px; padding: 20px;">
                                    <div style="height: 160px;"></div>
                                    <h1 style="color: #448AFF; margin: 0px; margin-bottom: 18px; text-align: center;">Hello, <span>' . $vendorDetails['contact_person'] . '</span></h1>
                                    <h6 style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;">Your Bid Successfully Listed</h6>
                                    <p style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;">
                                        You have bided successfully for quotation.
                                    </p>
                                   
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>';

                $subject = 'Bid Received: ' . date("d-m-Y");
                $to = $vendorDetails['email'];
                $this->send_email($to, $subject, $message);


                $this->Flash->success(__('You have bided successfully for quotation.'));
                return $this->redirect(['prefix' => false, 'controller' => 'Homes', 'action' => 'index']);
            }
        }
    }

    // private function removeEmojis($text)
    // {
    //     return preg_replace('/[\x{1F600}-\x{1F64F}' . 
    //         '\x{1F300}-\x{1F5FF}' .
    //         '\x{1F680}-\x{1F6FF}' .
    //         '\x{2600}-\x{26FF}' .
    //         '\x{2700}-\x{27BF}]++/u', '', $text);
    // }


    public function view_received_quotation($quotationNo)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('QuotationSend');
        $quotationVendor = $this->QuotationSend->find('all')->where(['QuotationSend.quotation_id' => $quotationNo])->order(['id' => 'desc'])->toarray();
        $this->set('quotationVendor', $quotationVendor);
    }


    public function send_quotations($quotationId)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('QuotationSend');
        $this->loadModel('Vendors');
        $this->loadModel('Schools');

        $vendornames = $this->Vendors->find('all')->order(['Vendors.name' => 'asc'])->toarray();
        $sendVendors = $this->QuotationSend->find('all')->where(['QuotationSend.quotation_id' => $quotationId])->toarray();
        $vendorIdsArray = [];
        foreach ($sendVendors as $vendorId) {
            $vendorIdsArray[] = $vendorId['vendor_id'];
        }

        $this->set(compact('vendornames', 'vendorIdsArray'));


        if ($this->request->is(['post', 'put'])) {
            foreach ($this->request->data['vendor_ids'] as $vendorId) {
                $this->loadModel('QuotationSend');

                $vendorData['quotation_id'] = $quotationId;
                $vendorData['vendor_id'] = $vendorId;
                $newpo = $this->QuotationSend->patchEntity($this->QuotationSend->newEntity(), $vendorData);
                $this->QuotationSend->save($newpo);


                $vendorDetails = $this->Vendors->find('all')->where(['Vendors.id' => $vendorId])->first();
                $dbname = $this->request->session()->read('Auth.User.db');

                // for find company name
                $company_id = $this->request->session()->read('Auth.User.c_id');
                $conn = ConnectionManager::get('default');
                $comapny_details = $conn->execute("SELECT * FROM operify.`schools` WHERE id = :id", ['id' => $company_id])->fetch('assoc');

                $company_name = ucwords(strtolower($comapny_details['school_name']));


                $quotation = $this->Quotation->find('all')->where(['quotation_id' => $quotationId])->first();
                $acceptance_date = date('jS F, Y', strtotime($quotation['acceptance_date']));



                $linkData = base64_encode($quotationId . '/' . $vendorId . '/' . $dbname);

                $url = SITE_URL . "admin/Quotation/vendor_quotation/" . $linkData;
                // pr($url);exit;

                $message = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Quotation Email</title>
                </head>
                <body style="margin: 0px;">
                    <div style="background-color: #448AFF30;">
                        <div style="max-width:600px; margin: auto; font-family: Arial, Helvetica, sans-serif;">
                            <div style="background-color: #448AFF;">
                                <div style="width: 150px; margin: auto; background-color: #fff; padding: 10px; border-radius: 0px 0px 8px 8px;">
                                    <img src="' . SITE_URL . 'images/logo.png" alt="logo" style="max-width: 100%;" />
                                </div>
                                <div style="padding: 55px 20px 55px;">
                                    <div style="text-align: center; height: 180px;">
                                        <img src="' . SITE_URL . 'images/pc.png" alt="pc" style="max-width: 90%; display: block; margin: auto;"/>
                                    </div>
                                </div>
                                <div style="background-color: #fff; border-radius: 20px 20px 0px 0px; padding: 20px 20px 0px 20px;">
                                    <div style="height: 120px;"></div>
                                    <h1 style="color: #448AFF; margin: 0px; margin-bottom: 18px; text-align: center;">Hello, <span>' . $vendorDetails['contact_person'] . '</span></h1>
                                    <p style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;">Your organization has been pre-selected to provide a Lump Sum Bid against Quotation ID-' . $quotationId . '.</p>
                                    <p style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px; margin-bottom: 10px;">You are requested to read the Request for Quotation documents and all Enclosures.</p>
                                    <p style="text-align: center; font-size: 16px; line-height: 22px; color: #222222; margin-top: 0px;">Responses (Quotations) should reach us no later than 12th March, 2025</p>
                                    <div style="text-align: center; margin-top: 22px;">
                                        <a href="' . $url . '" style="display: inline-flex; justify-content: center; align-items: center; color: #fff; background-color: #448AFF; font-size: 16px; padding: 8px 16px; text-decoration: none;">Click Here</a>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>';

                // $subject = 'Quotation request: ' . date("d-m-Y");
                $subject = $company_name . ' Quotation requested for Quotation ID: ' . $quotationId;
                $to = $vendorDetails['email'];
                $this->send_email($to, $subject, $message);
            }
            $this->Flash->success(__('The Quotation created sucessfully.'));
            return $this->redirect(['controller' => 'Quotation', 'action' => 'index']);
        }
    }

    public function viewquotationdetail($quotationNo)
    {
        $this->loadModel('Quotation');
        $this->loadModel('QuotationDetails');

        $quotation = $this->Quotation->find('all')->where(['quotation_id' => $quotationNo])->first();
        $quotationItemDeatails = $this->QuotationDetails->find('all')->where(['quotation_id' => $quotationNo])->toarray();
        $this->set(compact('quotation', 'quotationItemDeatails'));
    }

    public function viewvendorquotation($receivedId)
    {
        $this->loadModel('QuotationReceived');
        $this->loadModel('QuotationReceivedDetails');
        $this->loadModel('Quotation');
        $this->loadModel('QuotationDetails');

        $bidDetails = $this->QuotationReceived->find('all')->where(['id' => $receivedId])->first();
        $bidItemDeatails = $this->QuotationReceivedDetails->find('all')->where(['received_id' => $receivedId])->toarray();

        $quotation = $this->Quotation->find('all')->where(['quotation_id' => $bidDetails['quotation_id']])->first();
        $quotationItemDeatails = $this->QuotationDetails->find('all')->where(['quotation_id' => $bidDetails['quotation_id']])->toarray();

        $this->set(compact('bidDetails', 'bidItemDeatails', 'quotation', 'quotationItemDeatails'));
    }



    public function viewbiddedvendors($quotationNo)
    {
        $this->loadModel('QuotationReceived');
        $bidVendorsDetails = $this->QuotationReceived->find('all')->where(['quotation_id' => $quotationNo])->toarray();
        $this->set(compact('bidVendorsDetails'));
    }
}
