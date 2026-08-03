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
class ReverseindentController extends AppController
{
    //$this->loadcomponent('()->read');
    public function initialize()
    {
        //load all models
        parent::initialize();
        $this->Auth->allow([
            'viewreverseindentpdf'
        ]);
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Reverseindent');

        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $machines_id = trim($reqdata['machines_id']);
        $apk = [];

        if (!empty($machines_id)) {
            $apk['Reverseindent.machine_id'] = $machines_id;
        }
        if (!empty($item_id)) {
            $apk['Reverseindent.finishedproduct_id'] = $item_id;
        }
        if (!empty($contract_id)) {
            $apk['Reverseindent.contract_id'] = $contract_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Reverseindent.issue_date) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Reverseindent.issue_date) <='] = $dateto2;
        }


        if ($reqdata != '') {
            $reverseindentid = $this->Reverseindent->find()->where([$apk])->order(['Reverseindent.id' => 'DESC']);
        } else {
            $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.reverse_id' => 'Desc']);
        }

        $reverseindentid = $this->paginate($reverseindentid)->toarray();
        $this->set(compact('reverseindentid'));
    }


    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');
        $this->loadModel('Device');
        $this->loadModel('Users');
        $this->loadModel('Additem');

        $Reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.reverse_id' => 'Desc'])->first();
        if ($Reverseindentid['reverse_id'] != "") {
            $newindentid = $Reverseindentid['reverse_id'] + 1;
        } else {
            $newindentid = "1001";
        }
        $this->set('newindentid', $newindentid);

        if ($this->request->is(['post', 'put'])) {
            foreach ($this->request->data['itemquantity'] as $key => $value) {
                $checkvalue[] = $value;
            }

            if (!empty(array_filter($checkvalue))) {

                $product = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['finisheditem_id']])->first();
                $reverseId = $this->request->data['reverse_id'];
                $contractName = $this->request->data['contractname'];
                $productName = $product['item_name'];
                $issuedBy = $this->request->data['received_name'];
                $date = date('d-m-Y');
                $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
                foreach ($device_details as $key => $value) {
                    $deviceToken = $value['device']['token'];
                    $tokens[] = $deviceToken;
                }
                $message = 'Reverse(' . $reverseId . ') is Reverse Indent for Contract- ' . $contractName . ' for Production of  ' . $productName . ' by ' . $issuedBy . ' and Dated:' . $date . '.';
                $push = new \Push(
                    'Reverse',
                    $message
                );
                $firebase = new \Firebase();
                $mPushNotification = $push->getPush();
                $test = $firebase->send($tokens, $mPushNotification);
                // pr($test);die;

                $poerder['reverse_id'] = $this->request->data['reverse_id'];
                $poerder['contract_id'] = $this->request->data['contract_id'];
                $poerder['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                $poerder['machine_id'] = $this->request->data['machines_id'];
                $poerder['received_name'] = $this->request->data['received_name'];
                $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                $newpo = $this->Reverseindent->patchEntity($this->Reverseindent->newEntity(), $poerder);

                if ($purchasess = $this->Reverseindent->save($newpo)) {
                    $lstid = $purchasess->id;
                    foreach ($this->request->data['itemquantity'] as $key => $value) {
                        if ($value != '') {
                            $newsr = $this->Stockregister->newEntity();
                            $newsrentity['reverse_id'] = $this->request->data['reverse_id'];
                            $newsrentity['contract_id'] = $this->request->data['contract_id'];
                            $newsrentity['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                            $newsrentity['item_id'] = $this->request->data['item_id'][$key];
                            $newsrentity['quantity'] = $this->request->data['itemquantity'][$key];
                            $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                            $newsrentity['store_type'] = '3';
                            $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                            $ponewsr = $this->Stockregister->save($podetail);
                        }
                    }
                }
                $this->Flash->success('Reverse Indent has been saved successfully.');
                return $this->redirect(['action' => 'index']);
            }
            return $this->redirect(['action' => 'index']);
        }
    }

    public function edit($reverse_id = null)
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');
        $this->loadModel('Machinemaster');

        $reverseindentid = $this->Reverseindent->find('all')->where(['Reverseindent.reverse_id' => $reverse_id])->first();
        $reverseindentdetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
        $machinename = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $reverseindentid['machine_id']])->first();
        $this->set(compact('reverseindentid', 'reverseindentdetails', 'machinename'));

        if ($this->request->is(['post', 'put'])) {
            $poerder['updated'] = date('Y-m-d H:i:s');
            $poerder['received_name'] = $this->request->data['received_name'];
            $poerder['machine_id'] = $this->request->data['machines_id'];
            $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
            $newpo = $this->Reverseindent->patchEntity($reverseindentid, $poerder);
            $this->Reverseindent->save($newpo);



            foreach ($this->request->data['item_id'] as $key => $value) {
                $stockDetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id, 'Stockregister.item_id' => $value])->first();

                $newsrentity['reverse_id'] = $this->request->data['reverse_id'];
                $newsrentity['item_id'] = $this->request->data['item_id'][$key];
                $newsrentity['quantity'] = $this->request->data['itemquantity'][$key];
                $newsrentity['store_type'] = '3';
                $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));

                $stock = $this->Stockregister->patchEntity($stockDetails, $newsrentity);
                $poresustnew = $this->Stockregister->save($stock);
            }


            $this->Flash->success('Reverse Indent has been saved successfully.');
            return $this->redirect(['action' => 'index']);
        }
    }


    public function delete($reverse_id = null)
    {
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');
        $Reverseindentid = $this->Reverseindent->find('all')->where(['Reverseindent.reverse_id' => $reverse_id])->first();
        $stockDetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
        if ($Reverseindentid) {
            $this->Reverseindent->delete($Reverseindentid);
            foreach ($stockDetails as $stock) {
                $this->Stockregister->delete($stock);
            }
            $this->Flash->success('The Reverse Indent deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }



    public function searchitem()
    {

        $this->loadModel('Reverseindent');
        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $machines_id = trim($reqdata['machines_id']);
        $apk = [];

        if (!empty($machines_id)) {
            $apk['Reverseindent.machine_id'] = $machines_id;
        }
        if (!empty($item_id)) {
            $apk['Reverseindent.finishedproduct_id'] = $item_id;
        }
        if (!empty($contract_id)) {
            $apk['Reverseindent.contract_id'] = $contract_id;
        }

        if ($datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {
            $apk['DATE(Reverseindent.issue_date) >='] = $datefrom;
            $apk['DATE(Reverseindent.issue_date) <='] = $dateto2;
        } else if ($datefrom != '1970-01-01') {
            $apk['DATE(Reverseindent.issue_date) ='] = $datefrom;
        }
        $this->request->session()->write('apk', $apk);
        $reverseindentid = $this->Reverseindent->find()->where([$apk])->order(['Reverseindent.id' => 'DESC']);
        $reverseindentid = $this->paginate($reverseindentid)->toarray();
        $this->set(compact('reverseindentid'));
    }

    public function getdesignsheetdetails()
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');

        $itemid = $this->request->data['itemid'];
        $contractid = $this->request->data['contractid'];

        $designsheetno = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid, 'Designsheet.item_id' => $itemid])->first();
        $designsheetdetail = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno['designsheetno']])->order(['Designsheetdetails.is_group' => 'ASC'])->toArray();
        $this->set('designsheetdetail', $designsheetdetail);
    }


    public function viewreverseindent($reverse_id)
    {
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');

        $reverseindentid = $this->Reverseindent->find('all')->where(['Reverseindent.reverse_id' => $reverse_id])->first();
        $reverseindentdetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
        $this->set(compact('reverseindentid', 'reverseindentdetails'));
    }


    public function viewreverseindentpdf($reverse_id, $erpID = null)
    {
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');

        $dbname = $this->request->session()->read('Auth.User.db');
        if (empty($dbname)) {
            $this->connection($erpID);
            $connss = ConnectionManager::get($erpID);

            $site = $connss->execute("SELECT * FROM `sitesettings` limit 1");
            $sitesetting = $site->fetch('assoc');
            $sitedetail = $connss->execute("SELECT * FROM `sitesettings_details` where `status` = 'Y' limit 1");
            $site_details = $sitedetail->fetch('assoc');

            $reverse = $connss->execute("SELECT * FROM `reverseindent` where `reverse_id`= '" . $reverse_id . "' order by id desc");
            $reverseindentid = $reverse->fetch('assoc');

            $reversedetails = $connss->execute("SELECT * FROM `st_stock_register` where `reverse_id`= '" . $reverse_id . "'");
            $reverseindentdetails = $reversedetails->fetchAll('assoc');

        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $reverseindentid = $this->Reverseindent->find('all')->where(['Reverseindent.reverse_id' => $reverse_id])->first();
            $reverseindentdetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
        }
        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('reverseindentid', 'reverseindentdetails'));
    }
    public function excel()
    {
        $this->loadModel('Reverseindent');
        $where = $this->request->session()->read('apk');
        if ($where) {
            $reverseindentid = $this->Reverseindent->find('all')->where([$where])->order(['Reverseindent.issue_date' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.issue_date' => 'DESC'])->toarray();
        }
        $this->set(compact('reverseindentid'));
    }


    public function getcategoryindent()
    {
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $item_id = $this->request->data['itemid'];
        $reqQty = $this->request->data['reqQty'];
        $pendQty = $this->request->data['pendQty'];

        $categoryname = $this->Additem->find('all')->where(['Additem.id' => $item_id])->contain('Measurementunit')->first();
        $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
        $value['current_stock'] = round($grnStock['sum'] - $indentStock['sum'], 2);
        $value['item_id'] = $item_id;
        $value['reqQty'] = $reqQty;
        $value['pendQty'] = $pendQty;
        $value['uom'] = $categoryname['measurementunit']['unit_name'];
        $this->set(compact('value'));
    }


}
