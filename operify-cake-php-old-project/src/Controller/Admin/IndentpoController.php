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

class IndentpoController extends AppController
{
    //$this->loadcomponent('()->read');
    public function initialize()
    {
        //load all models
        parent::initialize();
        $this->Auth->allow([
            'viewindentpopdf'
        ]);
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Indentpo');
        $this->loadModel('Indentpodetails');

        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $machines_id = trim($reqdata['machines_id']);
        $apk = [];

        if (!empty($machines_id)) {
            $apk['Indentpo.machine_id'] = $machines_id;
        }
        if (!empty($item_id)) {
            $apk['Indentpo.finishedproduct_id'] = $item_id;
        }
        if (!empty($contract_id)) {
            $apk['Indentpo.contract_id'] = $contract_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Indentpo.issue_date) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Indentpo.issue_date) <='] = $dateto2;
        }

        if ($reqdata != '') {
            $indentpoid = $this->Indentpo->find()->where([$apk])->order(['Indentpo.id' => 'DESC']);
        } else {
            $indentpoid = $this->Indentpo->find('all')->order(['Indentpo.indent_id' => 'Desc']);
        }
        $indentpoid = $this->paginate($indentpoid)->toarray();
        $this->set(compact('indentpoid'));
    }


    public function add($id = null)
    {
        require_once 'Firebase.php';
        require_once 'Push.php';
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Indentpo');
        $this->loadModel('Indentpodetails');
        $this->loadModel('Device');
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('Users');

        // if (date("Y-m-d") >= date("Y-04-01")) {
        //     $checkfinancialyear = date("Y-04-01");
        // } else {
        //     $checkfinancialyear = date("Y-04-01", strtotime("-1 year")); 
        // }
        // $indent = $this->Indentpo->find('all')->where(['date(Indentpo.issue_date) >=' => $checkfinancialyear])->order(['Indentpo.indent_id' => 'desc'])->first();

        // if ($indent['indent_id'] != "") {
        //     $indent_id = explode('-', $indent['indent_id']);
        //     $newindentid = ($indent_id[0]) .'-'. (intval($indent_id[1]) + 1);
        // } else {
        //     $current_year = date("Y");
        //     $next_year = $current_year + 1;
        //     $current_year_last_two_digits = substr($current_year, -2);
        //     $next_year_last_two_digits = substr($next_year, -2);
        //     $newindentid = $current_year_last_two_digits . $next_year_last_two_digits . '-1'; 
        // }
        $indent = $this->Indentpo->find('all')->order(['Indentpo.indent_id' => 'desc'])->first();
        if ($indent['indent_id'] != "") {
            $newindentid = ($indent['indent_id'] + 1);
        } else {
            $newindentid = '1001';
        }
        $this->set('newindentid', $newindentid);


        $getUserId = $this->request->session()->read('Auth.User.id');
        if ($this->request->is(['post', 'put'])) {
            foreach ($this->request->data['itemquantity'] as $key => $value) {
                $checkvalue[] = $value;
            }
            if (!empty(array_filter($checkvalue))) {

                $product = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['finisheditem_id']])->first();
                $indentId = $this->request->data['indentid'];
                $contractName = $this->request->data['contractname'];
                $productName = $product['item_name'];
                $issuedBy = $this->request->data['issued_name'];
                $date = date('d-m-Y');
                $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
                foreach ($device_details as $key => $value) {
                    $deviceToken = $value['device']['token'];
                    $tokens[] = $deviceToken;
                }
                $message = 'Indent(' . $indentId . ') is Issued for Contract- ' . $contractName . ' for Production of  ' . $productName . ' by ' . $issuedBy . ' and Dated:' . $date . '.';
                $push = new \Push(
                    'Indent',
                    $message
                );
                $firebase = new \Firebase();
                $mPushNotification = $push->getPush();
                $title = 'Indent';
                foreach ($tokens as $tok) {
                    $this->sendNotification($tok, $title, $message);
                }
                // $test = $firebase->send($tokens, $mPushNotification);

                $poerder['user_id'] = $getUserId;
                $poerder['indent_id'] = $this->request->data['indentid'];
                $poerder['contract_id'] = $this->request->data['contract_id'];
                $poerder['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                $poerder['machine_id'] = $this->request->data['machines_id'];
                $poerder['issued_name'] = $this->request->data['issued_name'];
                $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                $newpo = $this->Indentpo->patchEntity($this->Indentpo->newEntity(), $poerder);
                if ($purchasess = $this->Indentpo->save($newpo)) {
                    $lstid = $purchasess->id;
                    foreach ($this->request->data['itemquantity'] as $key => $value) {
                        //     if ($value != '') {
                        //         $poerder11['indentpo_id'] = $this->request->data['indentid'];
                        //         $poerder11['contract_id'] = $this->request->data['contract_id'];
                        //         $poerder11['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                        //         $poerder11['item_id'] = $this->request->data['item_id'][$key];
                        //         $poerder11['item_qty'] = $this->request->data['itemquantity'][$key];
                        //         $poerder11['uom'] = $this->request->data['unit_name'][$key];
                        //         $newpo11 = $this->Indentpodetails->patchEntity($this->Indentpodetails->newEntity(), $poerder11);
                        //         $poresustnew = $this->Indentpodetails->save($newpo11);
                        //     }
                        if ($value != '') {
                            $newsr = $this->Stockregister->newEntity();
                            $newsrentity['indent_id'] = $this->request->data['indentid'];
                            $newsrentity['contract_id'] = $this->request->data['contract_id'];
                            $newsrentity['item_id'] = $this->request->data['item_id'][$key];
                            $newsrentity['quantity'] = $this->request->data['itemquantity'][$key];
                            $newsrentity['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                            $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                            $newsrentity['store_type'] = '2';
                            $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                            $ponewsr = $this->Stockregister->save($podetail);
                        }
                    }
                }
                $this->Flash->success('Indent has been saved successfully.');
                return $this->redirect(['action' => 'index']);
            }
            return $this->redirect(['action' => 'index']);
        }
    }

    public function edit($poid = null)
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Indentpo');
        $this->loadModel('Indentpodetails');
        $this->loadModel('Stockregister');
        $this->loadModel('Machinemaster');

        $indentpoid = $this->Indentpo->find('all')->where(['Indentpo.indent_id' => $poid])->first();
        $indentpodetails = $this->Stockregister->find('all')->where(['Stockregister.indent_id' => $poid])->toarray();
        $machinename = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $indentpoid['machine_id']])->first();
        $this->set(compact('indentpoid', 'indentpodetails', 'machinename'));

         $getUserId = $this->request->session()->read('Auth.User.id');

        if ($this->request->is(['post', 'put'])) {
            $poerder['user_id'] = $getUserId;
            $poerder['updated'] = date('Y-m-d H:i:s');
            $poerder['issued_name'] = $this->request->data['issued_name'];
            $poerder['machine_id'] = $this->request->data['machines_id'];
            $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
            $newpo = $this->Indentpo->patchEntity($indentpoid, $poerder);
            $this->Indentpo->save($newpo);

            // foreach ($this->request->data['item_id'] as $key => $value) {
            //     $indentpodetails = $this->Indentpodetails->find('all')->where(['Indentpodetails.indentpo_id' => $poid, 'Indentpodetails.item_id' => $value])->first();
            //     $poerder11['indentpo_id'] = $this->request->data['indentid'];
            //     $poerder11['item_id'] = $this->request->data['item_id'][$key];
            //     $poerder11['item_qty'] = $this->request->data['itemquantity'][$key];
            //     $poerder11['uom'] = $this->request->data['unit_name'][$key];

            //     $newpo11 = $this->Indentpodetails->patchEntity($indentpodetails, $poerder11);
            //     $poresustnew = $this->Indentpodetails->save($newpo11);
            // }

            foreach ($this->request->data['item_id'] as $key => $value) {
                $stockDetails = $this->Stockregister->find('all')->where(['Stockregister.indent_id' => $poid, 'Stockregister.item_id' => $value])->first();
                $newsrentity['indent_id'] = $this->request->data['indentid'];
                $newsrentity['item_id'] = $this->request->data['item_id'][$key];
                $newsrentity['quantity'] = $this->request->data['itemquantity'][$key];
                $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                $newsrentity['store_type'] = '2';
                $stock = $this->Stockregister->patchEntity($stockDetails, $newsrentity);
                $poresustnew = $this->Stockregister->save($stock);
            }

            $this->Flash->success('Indent has been saved successfully.');
            return $this->redirect(['action' => 'index']);
        }
    }


    public function delete($poid = null)
    {

        $this->loadModel('Indentpo');
        $this->loadModel('Indentpodetails');
        $this->loadModel('Stockregister');

        $indentpoid = $this->Indentpo->find('all')->where(['Indentpo.indent_id' => $poid])->first();
        // $indentpodetails = $this->Indentpodetails->find('all')->where(['Indentpodetails.indentpo_id' => $poid])->toarray();
        $stockDetails = $this->Stockregister->find('all')->where(['Stockregister.indent_id' => $poid])->toarray();


        if ($indentpoid) {
            $this->Indentpo->delete($indentpoid);
            // foreach ($indentpodetails as $product1) {
            //     $this->Indentpodetails->delete($product1);
            // }
            foreach ($stockDetails as $stock) {
                $this->Stockregister->delete($stock);
            }
            $this->Flash->success('The Indent deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }



    public function searchitem()
    {

        $this->loadModel('Indentpo');


        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $machines_id = trim($reqdata['machines_id']);
        $apk = [];

        if (!empty($machines_id)) {
            $apk['Indentpo.machine_id'] = $machines_id;
        }
        if (!empty($item_id)) {
            $apk['Indentpo.finishedproduct_id'] = $item_id;
        }
        if (!empty($contract_id)) {
            $apk['Indentpo.contract_id'] = $contract_id;
        }

        if ($datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {
            $apk['DATE(Indentpo.issue_date) >='] = $datefrom;
            $apk['DATE(Indentpo.issue_date) <='] = $dateto2;
        } else if ($datefrom != '1970-01-01') {
            $apk['DATE(Indentpo.issue_date) ='] = $datefrom;
        }


        $this->request->session()->write('apk', $apk);
        $indentpoid = $this->Indentpo->find()->where([$apk])->order(['Indentpo.id' => 'DESC']);
        $indentpoid = $this->paginate($indentpoid)->toarray();
        $this->set(compact('indentpoid'));
    }





    public function getdesignsheetdetails()
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');
        $this->loadModel('Additem');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');

        $itemid = $this->request->data['itemid'];
        $contractid = $this->request->data['contractid'];

        $sitesetting = $this->Sitesettings->find('all')->contain(['SitesettingsDetails'])->where(['Sitesettings.id' => 1])->first();
        $stock_update = $sitesetting['sitesettings_detail']['stock_update'];
        // pr($stock_update);die;

        $designsheetno = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid, 'Designsheet.item_id' => $itemid])->first();
        $designsheetdetail = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno['designsheetno']])->order(['Designsheetdetails.is_group' => 'ASC'])->toArray();
        foreach ($designsheetdetail as $value) {
            if ($value['is_group'] > 0) {
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();
                $isgroupcategory[] = $product['category_id'];
            } else {
                $product = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();
                $notgroupcategory[] = $product['category_id'];
            }
        }
        $uniqueArray = array_unique($isgroupcategory);

        $groupcount = count($isgroupcategory);
        $uniquecount = count($uniqueArray);

        $message = '';
        if ($groupcount == $uniquecount) {
            foreach ($notgroupcategory as $value) {
                if (in_array($value, $isgroupcategory)) {
                    $message = 'You can`t indent multiple same category items. Please correct in Designsheet no - ';
                    foreach ($uniqueArray as $uni) {
                        $prod = $this->Additem->find('all')->where(['Additem.category_id' => $uni])->first();

                        if ($prod) {
                            $message .= $designsheetno['designsheetno'] . ' And item name ' . $prod['item_name'] . '. ';
                        }
                    }
                    break;
                }
            }
        } else {
            $message = 'You can`t indent multiple same category items. Please correct in Designsheet no - ';
            foreach ($uniqueArray as $uni) {
                $prod = $this->Additem->find('all')->where(['Additem.category_id' => $uni])->first();
                if ($prod) {
                    $message .= $designsheetno['designsheetno'] . ' And item name ' . $prod['item_name'] . '. ';
                }
            }
        }
        $this->set(compact('message'));
        $this->set(compact('itemid', 'contractid', 'stock_update'));
        $this->set('designsheetdetail', $designsheetdetail);
    }


    public function viewindentpodetail($indentid)
    {
        $this->loadModel('Indentpo');
        $this->loadModel('Stockregister');

        $indentpoid = $this->Indentpo->find('all')->where(['Indentpo.indent_id' => $indentid])->first();
        $indentpodetails = $this->Stockregister->find('all')->where(['Stockregister.indent_id' => $indentid])->toarray();
        $this->set(compact('indentpoid', 'indentpodetails'));
    }


    public function viewindentpopdf($indentid, $erpID = null)
    {
        $this->loadModel('Indentpo');
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

            $reverse = $connss->execute("SELECT * FROM `indentpo` where `indent_id`= '" . $indentid . "' order by id desc");
            $indentpoid = $reverse->fetch('assoc');
            $reversedetails = $connss->execute("SELECT * FROM `st_stock_register` where `indent_id`= '" . $indentid . "'");
            $indentpodetails = $reversedetails->fetchAll('assoc');
        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $indentpoid = $this->Indentpo->find('all')->where(['Indentpo.indent_id' => $indentid])->first();
            $indentpodetails = $this->Stockregister->find('all')->where(['Stockregister.indent_id' => $indentid])->toarray();
        }
        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('indentpoid', 'indentpodetails'));
    }
    public function indentpoexcel()
    {
        $this->loadModel('Indentpo');
        $where = $this->request->session()->read('apk');
        if ($where) {
            $indentpoid = $this->Indentpo->find('all')->where([$where])->order(['Indentpo.issue_date' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $indentpoid = $this->Indentpo->find('all')->order(['Indentpo.issue_date' => 'DESC'])->toarray();
        }
        $this->set(compact('indentpoid'));
    }

    public function getcurrentstock()
    {
        $this->loadModel('Stockregister');

        $item_id = $this->request->data['itemid'];

        $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
        $currentStock = round($grnStock['sum'] - $indentStock['sum'], 2);
        echo json_encode($currentStock);
        die;
    }
    public function getcategoryindent()
    {
        $this->loadModel('Stockregister');

        $item_id = $this->request->data['itemid'];
        $reqQty = $this->request->data['reqQty'];
        $pendQty = $this->request->data['pendQty'];

        $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
        $value['current_stock'] = round($grnStock['sum'] - $indentStock['sum'], 2);
        $value['item_id'] = $item_id;
        $value['reqQty'] = $reqQty;
        $value['pendQty'] = $pendQty;
        $this->set(compact('value'));
    }
}
