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
            $reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.id' => 'DESC']);
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
        $this->loadModel('Designsheet');

        $Reverseindentid = $this->Reverseindent->find('all')->order(['Reverseindent.id' => 'Desc'])->first();
        if ($Reverseindentid['reverse_id'] != "") {
            $newindentid = (int)preg_replace('/[^0-9]/', '', $Reverseindentid['reverse_id']) + 1;
        } else {
            $newindentid = "1001";
        }
        $this->set('newindentid', $newindentid);

        if ($this->request->is(['post', 'put'])) {
                        file_put_contents(WWW_ROOT . 'my_debug.txt', print_r($this->request->data, true), FILE_APPEND);
                        $action = isset($this->request->data['action']) ? $this->request->data['action'] : 'finalize';
            if (is_array($action)) {
                $action = in_array('finalize', $action) ? 'finalize' : 'draft';
            }
            $is_draft = ($action === 'draft');
            
            $finished_qty = isset($this->request->data['finished_qty']) ? $this->request->data['finished_qty'] : 0;
            
            if (!empty($finished_qty) && $finished_qty > 0) {
                
                $conn = ConnectionManager::get('default');
                // Ensure status column exists
                $schema = $this->Reverseindent->schema();
                if (!in_array('status', $schema->columns())) {
                    $conn->execute("ALTER TABLE reverseindent ADD COLUMN status ENUM('draft', 'finalized') DEFAULT 'draft'");
                    $this->Reverseindent->schema()->addColumn('status', ['type' => 'string']);
                }

                $conn->begin();
                try {
                    // Check duplicate
                    $existing = $this->Reverseindent->find('all')->where(['reverse_id' => $this->request->data['reverse_id']])->first();
                    if ($existing) {
                        throw new \Exception('Reverse ID already exists.');
                    }

                    if (empty($this->request->data['contract_id']) || empty($this->request->data['finisheditem_id']) || empty($this->request->data['received_name']) || empty($this->request->data['issue_date'])) {
                        throw new \Exception('Please fill all required fields.');
                    }
                    
                    if (!$is_draft) {
                        $finisheditem_id = $this->request->data['finisheditem_id'];
                        $contract_id = $this->request->data['contract_id'];
                        
                        $itemData = $this->Additem->find('all')->where(['id' => $finisheditem_id])->first();
                        $isSemiFinished = ($itemData && isset($itemData['itemtype']) && $itemData['itemtype'] === 'Semi-Finished Product');

                        if (!$isSemiFinished) {
                            $designsheet = $this->Designsheet->find('all')
                                ->where(['contract_id' => $contract_id, 'item_id' => $finisheditem_id])->first();
                            
                            if (!$designsheet) {
                                throw new \Exception('Design sheet not found for this product and contract.');
                            }
                            
                            $req_qty = $designsheet->quantity;
                            $pending_qty = $req_qty; // Design Sheet quantity remains unchanged
                            
                            if ($finished_qty > $pending_qty) {
                                throw new \Exception('Received Qty cannot exceed Design Sheet Qty.');
                            }
                        }
                    }

                    $poerder['reverse_id'] = $this->request->data['reverse_id'];
                    $poerder['contract_id'] = $this->request->data['contract_id'];
                    $poerder['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                    $poerder['machine_id'] = $this->request->data['machines_id'];
                    $poerder['received_name'] = $this->request->data['received_name'];
                    $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                    $newpo = $this->Reverseindent->patchEntity($this->Reverseindent->newEntity(), $poerder);
                    $newpo->set('status', $is_draft ? 'draft' : 'finalized');

                    if ($purchasess = $this->Reverseindent->save($newpo)) {
                        
                            $finisheditem_id = $this->request->data['finisheditem_id'];
                            
                            $opening_exists = $this->Stockregister->find('all')
                                ->where(['item_id' => $finisheditem_id, 'store_type' => '0'])
                                ->count() > 0;
                            
                            if ($is_draft) {
                                $store_type = '99';
                            } else {
                                if ($isSemiFinished) {
                                    if (!$opening_exists) {
                                        $newsr0 = $this->Stockregister->newEntity();
                                        $newsr0entity = [
                                            'reverse_id' => $this->request->data['reverse_id'],
                                            'contract_id' => $contract_id,
                                            'finishedproduct_id' => $finisheditem_id,
                                            'item_id' => $finisheditem_id,
                                            'quantity' => 0,
                                            'issue_date' => date('Y-m-d', strtotime($this->request->data['issue_date'])),
                                            'store_type' => '0'
                                        ];
                                        $podetail0 = $this->Stockregister->patchEntity($newsr0, $newsr0entity);
                                        $podetail0->set($newsr0entity);
                                        $this->Stockregister->save($podetail0);
                                    }
                                    $store_type = '1';
                                } else {
                                    $store_type = $opening_exists ? '1' : '0';
                                }
                            }
                            
                            $newsr = $this->Stockregister->newEntity();
                            $newsrentity['reverse_id'] = $this->request->data['reverse_id'];
                            $newsrentity['contract_id'] = $this->request->data['contract_id'];
                            $newsrentity['finishedproduct_id'] = $finisheditem_id;
                            $newsrentity['item_id'] = $finisheditem_id;
                            $newsrentity['quantity'] = $finished_qty;
                            $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                            $newsrentity['store_type'] = $store_type;
                            
                            $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                            $podetail->set($newsrentity); // Force set fields
                            if (!$this->Stockregister->save($podetail)) {
                                throw new \Exception('Failed to save stock register for finished product.');
                            }
                            
                            // Save raw materials
                            if (!empty($this->request->data['itemquantity'])) {
                                foreach ($this->request->data['itemquantity'] as $key => $value) {
                                    if ($value != '') {
                                        $newsr_raw = $this->Stockregister->newEntity();
                                        $raw_entity['reverse_id'] = $this->request->data['reverse_id'];
                                        $raw_entity['contract_id'] = $contract_id;
                                        $raw_entity['finishedproduct_id'] = $finisheditem_id;
                                        $raw_entity['item_id'] = $this->request->data['item_id'][$key];
                                        $raw_entity['quantity'] = $value;
                                        $raw_entity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                                        $raw_entity['store_type'] = $is_draft ? '99' : '3';
                                        
                                        $podetail_raw = $this->Stockregister->patchEntity($newsr_raw, $raw_entity);
                                        $podetail_raw->set($raw_entity); // Force set fields
                                        if (!$this->Stockregister->save($podetail_raw)) {
                                            throw new \Exception('Failed to save raw material stock register.');
                                        }
                                    }
                                }
                            }
                    } else {
                         throw new \Exception('Failed to save reverse indent.');
                    }
                    
                    $conn->commit();
                    
                    $product = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['finisheditem_id']])->first();
                    $reverseId = $this->request->data['reverse_id'];
                    $contractName = $this->request->data['contractname'];
                    $productName = $product['item_name'];
                    $issuedBy = $this->request->data['received_name'];
                    $date = date('d-m-Y');
                    $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
                    $tokens = [];
                    foreach ($device_details as $key => $value) {
                        if (!empty($value['device']['token'])) {
                            $tokens[] = $value['device']['token'];
                        }
                    }
                    if (!empty($tokens)) {
                        $message = 'Reverse(' . $reverseId . ') is Reverse Indent for Contract- ' . $contractName . ' for Production of  ' . $productName . ' by ' . $issuedBy . ' and Dated:' . $date . '.';
                        $push = new \Push('Reverse', $message);
                        $firebase = new \Firebase();
                        $mPushNotification = $push->getPush();
                        $firebase->send($tokens, $mPushNotification);
                    }
                    
                    $this->Flash->success('Reverse Indent has been saved successfully.');
                    return $this->redirect(['action' => 'index']);
                } catch (\Exception $e) {
                    $conn->rollback();
                    $this->Flash->error($e->getMessage());
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error('Invalid quantity.');
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function edit($reverse_id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Reverseindent');
        $this->loadModel('Stockregister');
        $this->loadModel('Machinemaster');
        $this->loadModel('Designsheet');

        $reverseindentid = $this->Reverseindent->find('all')->where(['Reverseindent.reverse_id' => $reverse_id])->first();
        if (!$reverseindentid) {
            $this->Flash->error('Invalid reverse indent.');
            return $this->redirect(['action' => 'index']);
        }
        
        // Use schema check
        $schema = $this->Reverseindent->schema();
        if (in_array('status', $schema->columns()) && $reverseindentid->status === 'finalized') {
            $this->Flash->error('Finalized reverse indent cannot be edited.');
            return $this->redirect(['action' => 'index']);
        }

        // Just checking stock details if exist for this reverse
        $reverseindentdetails = $this->Stockregister->find('all')->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
        $machinename = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $reverseindentid['machine_id']])->first();
        $this->set(compact('reverseindentid', 'reverseindentdetails', 'machinename'));

        if ($this->request->is(['post', 'put'])) {
                        file_put_contents(WWW_ROOT . 'my_debug.txt', print_r($this->request->data, true), FILE_APPEND);
                        $action = isset($this->request->data['action']) ? $this->request->data['action'] : 'finalize';
            if (is_array($action)) {
                $action = in_array('finalize', $action) ? 'finalize' : 'draft';
            }
            $is_draft = ($action === 'draft');
            
            $finished_qty = isset($this->request->data['finished_qty']) ? $this->request->data['finished_qty'] : 0;

            if (!empty($finished_qty) && $finished_qty > 0) {
                
                $conn = ConnectionManager::get('default');
                if (!in_array('status', $schema->columns())) {
                    $conn->execute("ALTER TABLE reverseindent ADD COLUMN status ENUM('draft', 'finalized') DEFAULT 'draft'");
                    $this->Reverseindent->schema()->addColumn('status', ['type' => 'string']);
                }

                $conn->begin();
                try {
                    if (empty($this->request->data['received_name']) || empty($this->request->data['issue_date'])) {
                        throw new \Exception('Please fill all required fields.');
                    }
                    
                    // We assume finisheditem_id & contract_id are either passed or from existing record
                    $finisheditem_id = isset($this->request->data['finisheditem_id']) ? $this->request->data['finisheditem_id'] : $reverseindentid->finishedproduct_id;
                    $contract_id = isset($this->request->data['contract_id']) ? $this->request->data['contract_id'] : $reverseindentid->contract_id;

                    if (!$is_draft) {
                        $itemData = $this->Additem->find('all')->where(['id' => $finisheditem_id])->first();
                        $isSemiFinished = ($itemData && isset($itemData['itemtype']) && $itemData['itemtype'] === 'Semi-Finished Product');

                        if (!$isSemiFinished) {
                            $designsheet = $this->Designsheet->find('all')
                                ->where(['contract_id' => $contract_id, 'item_id' => $finisheditem_id])->first();
                            
                            if (!$designsheet) {
                                throw new \Exception('Design sheet not found for this product and contract.');
                            }
                            
                            $req_qty = $designsheet->quantity;
                            $pending_qty = $req_qty; // Design Sheet quantity remains unchanged
                            
                            if ($finished_qty > $pending_qty) {
                                throw new \Exception('Received Qty cannot exceed Design Sheet Qty.');
                            }
                        }
                    }

                    $poerder['updated'] = date('Y-m-d H:i:s');
                    $poerder['received_name'] = $this->request->data['received_name'];
                    $poerder['machine_id'] = $this->request->data['machines_id'];
                    $poerder['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                    if (isset($this->request->data['finisheditem_id'])) {
                        $poerder['finishedproduct_id'] = $this->request->data['finisheditem_id'];
                    }
                    $newpo = $this->Reverseindent->patchEntity($reverseindentid, $poerder);
                    $newpo->set('status', $is_draft ? 'draft' : 'finalized');

                    if ($this->Reverseindent->save($newpo)) {
                        
                        // Clear existing stock register entries for this draft
                        $this->Stockregister->deleteAll(['reverse_id' => $reverse_id]);
                        
                        $opening_exists = $this->Stockregister->find('all')
                            ->where(['item_id' => $finisheditem_id, 'store_type' => '0'])
                            ->count() > 0;
                        
                        $itemData = $this->Additem->find('all')->where(['id' => $finisheditem_id])->first();
                        $isSemiFinished = ($itemData && isset($itemData['itemtype']) && $itemData['itemtype'] === 'Semi-Finished Product');
                        
                        if ($is_draft) {
                            $store_type = '99';
                        } else {
                            if ($isSemiFinished) {
                                if (!$opening_exists) {
                                    $newsr0 = $this->Stockregister->newEntity();
                                    $newsr0entity = [
                                        'reverse_id' => $reverse_id,
                                        'contract_id' => $contract_id,
                                        'finishedproduct_id' => $finisheditem_id,
                                        'item_id' => $finisheditem_id,
                                        'quantity' => 0,
                                        'issue_date' => date('Y-m-d', strtotime($this->request->data['issue_date'])),
                                        'store_type' => '0'
                                    ];
                                    $podetail0 = $this->Stockregister->patchEntity($newsr0, $newsr0entity);
                                    $podetail0->set($newsr0entity);
                                    $this->Stockregister->save($podetail0);
                                }
                                $store_type = '1';
                            } else {
                                $store_type = $opening_exists ? '1' : '0';
                            }
                        }
                        
                        // Insert new stock register entry
                        $newsr = $this->Stockregister->newEntity();
                        $newsrentity['reverse_id'] = $reverse_id;
                        $newsrentity['contract_id'] = $contract_id;
                        $newsrentity['finishedproduct_id'] = $finisheditem_id;
                        $newsrentity['item_id'] = $finisheditem_id;
                        $newsrentity['quantity'] = $finished_qty;
                        $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                        $newsrentity['store_type'] = $store_type;
                        
                        $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                        $podetail->set($newsrentity); // Force set fields
                        if (!$this->Stockregister->save($podetail)) {
                            throw new \Exception('Failed to save stock register for finished product.');
                        }
                        
                        // Save raw materials
                        if (!empty($this->request->data['itemquantity'])) {
                            foreach ($this->request->data['itemquantity'] as $key => $value) {
                                if ($value != '') {
                                    $newsr_raw = $this->Stockregister->newEntity();
                                    $raw_entity['reverse_id'] = $reverse_id;
                                    $raw_entity['contract_id'] = $contract_id;
                                    $raw_entity['finishedproduct_id'] = $finisheditem_id;
                                    $raw_entity['item_id'] = $this->request->data['item_id'][$key];
                                    $raw_entity['quantity'] = $value;
                                    $raw_entity['issue_date'] = date('Y-m-d', strtotime($this->request->data['issue_date']));
                                    $raw_entity['store_type'] = $is_draft ? '99' : '3';
                                    
                                    $podetail_raw = $this->Stockregister->patchEntity($newsr_raw, $raw_entity);
                                    $podetail_raw->set($raw_entity); // Force set fields
                                    if (!$this->Stockregister->save($podetail_raw)) {
                                        throw new \Exception('Failed to save raw material stock register.');
                                    }
                                }
                            }
                        }
                    } else {
                         throw new \Exception('Failed to update reverse indent.');
                    }
                    
                    $conn->commit();
                    $this->Flash->success('Reverse Indent has been updated successfully.');
                    return $this->redirect(['action' => 'index']);
                } catch (\Exception $e) {
                    $conn->rollback();
                    $this->Flash->error($e->getMessage());
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error('Invalid quantity.');
                return $this->redirect(['action' => 'index']);
            }
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

    public function getcontractfinished()
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Additem');

        $contractid = $this->request->data['contract_id'];
        
        $items = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid])->toArray();
        $itemname = [];
        
        foreach ($items as $value) {
            $item_id = $value['item_id'];
            $additem = $this->Additem->find('all')
                ->where([
                    'Additem.id' => $item_id, 
                    'itemtype' => 'FinishedProduct'
                ])
                ->first();
            if ($additem) {
                $itemname[] = $additem;
            }
        }

        $unique_items = [];
        $ids = [];
        foreach($itemname as $itm) {
            if (!in_array($itm['id'], $ids)) {
                $unique_items[] = $itm;
                $ids[] = $itm['id'];
            }
        }

        echo json_encode($unique_items);
        die;
    }

    public function getdesignsheetdetails()
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('Designsheetdetails');

        $itemid = $this->request->data['itemid'];
        $contractid = $this->request->data['contractid'];

        $item = $this->Additem->find('all')->where(['Additem.id' => $itemid])->contain(['Measurementunit'])->first();
        $uom = (isset($item['measurementunit']) && isset($item['measurementunit']['unit_name'])) ? $item['measurementunit']['unit_name'] : '';
        
        $isSemiFinished = ($item && isset($item['itemtype']) && $item['itemtype'] === 'Semi-Finished Product');

        $designsheetno = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid, 'Designsheet.item_id' => $itemid])->first();
        
        if ($isSemiFinished) {
            $req_qty = $designsheetno ? $designsheetno['quantity'] : 0;
            $pending_qty = 'N/A'; // Bypassing Design Sheet stock constraint
        } else {
            $req_qty = $designsheetno ? $designsheetno['quantity'] : 0;
            $pending_qty = 'N/A'; // Disabled stock match validation for Semi-Finished product production
        }

        $designsheetdetail = [];
        if ($designsheetno) {
            $designsheetdetail = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno['designsheetno']])->order(['Designsheetdetails.is_group' => 'ASC'])->toArray();
        }
        
        $semiFinishedList = $this->Additem->find('list', [
            'keyField' => 'id',
            'valueField' => 'item_name'
        ])->where(['itemtype' => 'Semi-Finished Product', 'status' => 'Y'])->toArray();

        $this->set(compact('item', 'uom', 'req_qty', 'pending_qty', 'designsheetdetail', 'isSemiFinished', 'semiFinishedList'));
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
