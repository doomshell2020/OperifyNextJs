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
class ProductionController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->Auth->allow([
            'viewcontractdetailspdf',
            'viewproductionpdf'
        ]);
    }

    public function index($poid = null, $process_id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Finishedprocess');

        $reqdata = $_GET;
        $po_id = trim($reqdata['po_id']);
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $machines_id = $reqdata['machines_id'];
        $productprocess_id = $reqdata['productprocess_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $searchdata = [];


        if (!empty($po_id)) {
            $searchdata['Production.po_id'] = $po_id;
        }
        if (!empty($contract_id)) {
            $searchdata['Production.contract_id'] = $contract_id;
        }
        if (!empty($item_id)) {
            $searchdata['Production.item_id'] = $item_id;
        }
        if (!empty($machines_id)) {
            $searchdata['Production.machine_id'] = $machines_id;
        }
        if (!empty($productprocess_id)) {
            $searchdata['Production.productprocess_id'] = $productprocess_id;
        }
        if ($datefrom != '1970-01-01') {
            $searchdata['DATE(Production.production_date) ='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $searchdata['DATE(Production.production_date) <='] = $dateto2;
        }

        if ($reqdata) {
            $item = $this->Production->find()->contain(['Machinemaster'])->where([$searchdata])->order(['Production.id' => 'DESC']);
        } else {
            $item = $this->Production->find('all')->contain(['Machinemaster'])->order(['Production.id' => 'desc']);
        }

        if ($process_id) {
            $item = $this->Production->find()->contain(['Machinemaster'])->where(['Production.po_id' => $poid, 'Production.productprocess_id' => $process_id])->order(['Production.id' => 'DESC']);
        }


        $item = $this->paginate($item)->toarray();
        $this->set('production_data', $item);

        $processoptions = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.id' => 'asc'])->toarray();
        $this->set('processoptions', $processoptions);
    }


    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');

        $process_id = $this->Finishedprocess->find('all')->toarray();
        $this->set('process_id', $process_id);

        $productions = $this->Production->newEntity();

        if ($this->request->is(['post'])) {
            // pr($this->request->data);die;

            $poexists = $this->Productionorder->exists(['po_id' => $this->request->data('po_id')]);
            if ($poexists == '' && $poexists == null) {
                $this->Flash->error(__('Your enterd PO id does not exists.'));
                return $this->redirect(['action' => 'add']);
            }
            if ($this->request->data('machines_id') == '') {
                $this->Flash->error(__('Your enterd Machine does not exists.'));
                return $this->redirect(['action' => 'add']);
            }


            //this is validation code
            $itemId = $this->request->data['item_id'];
            $poId   = $this->request->data['po_id'];

            // Step 1: Get required processes from Additem
            $item = $this->Additem->find()
                ->select(['productprocess_id'])
                ->where(['id' => $itemId])
                ->first();

            if (empty($item) || empty($item->productprocess_id)) {
                $this->Flash->error('No process defined for this item.');
                return $this->redirect(['action' => 'index']);
            }

            // Convert to array
            $requiredProcesses = array_map('trim', explode(',', $item->productprocess_id));

            // Step 2: Get completed processes from Production
            $completedProcesses = $this->Production->find()
                ->select(['productprocess_id'])
                ->where([
                    'po_id'   => $poId,
                    'item_id' => $itemId,
                    'productprocess_id IN' => $requiredProcesses
                ])
                ->distinct(['productprocess_id'])
                ->extract('productprocess_id')
                ->toArray();

            // Step 3: Find missing processes
            $missing = array_diff($requiredProcesses, $completedProcesses);

            // Step 4: Validation
            if (!empty($missing)) {

                // Get process names from master table
                $missingNames = $this->Finishedprocess->find()
                    ->select(['process_name'])
                    ->where(['id IN' => $missing])
                    ->extract('process_name')
                    ->toArray();

                $this->Flash->error(
                    'Processes not completed: ' . implode(', ', $missingNames)
                );

                return $this->redirect(['action' => 'index']);
            }
            // ✅ All processes completed
            $check_item_process = true;


            $data['production_date'] = date('Y-m-d', strtotime($this->request->data['production_date']));
            $data['contract_id'] = $this->request->data['contract_id'];
            $data['po_id'] = $this->request->data['po_id'];
            $data['item_id'] = $this->request->data['item_id'];
            $data['productprocess_id'] = $this->request->data['productprocess_id'];
            $data['machine_id'] = $this->request->data['machines_id'];
            $data['plan_qty'] = $this->request->data['plan_qty'];
            $data['production_shift_a'] = $this->request->data['production_shift_a'];
            $data['production_shift_b'] = $this->request->data['production_shift_b'];
            $data['reading8am'] = $this->request->data['reading8am'];
            $data['reading8pm'] = $this->request->data['reading8pm'];
            $data['nextday8am'] = $this->request->data['nextday8am'];
            $data['manpower_day'] = $this->request->data['manpower_day'];
            $data['manpower_night'] = $this->request->data['manpower_night'];
            $data['scrap'] = $this->request->data['scrap'];
            $data['remark'] = $this->removeEmojis($this->request->data['remark']);
            $data['is_completed'] = $this->request->data['completed'];

            $po_no = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $this->request->data['po_id'], 'Productionorder.item_id' => $this->request->data['item_id']])->first();

            $productionsqty = $this->Production->find('all')->where(['Production.po_id' => $this->request->data['po_id'], 'Production.item_id' => $this->request->data['item_id'], 'Production.productprocess_id' => $this->request->data['productprocess_id']])->order(['Production.production_date' => 'DESC'])->toarray();

            $quantity = '';
            foreach ($productionsqty as $value) {
                $quantity += $value['production_shift_a'] + $value['production_shift_b'];
            }
            $totalqty = $quantity + $this->request->data['production_shift_a'] + $this->request->data['production_shift_b'];

            if ($totalqty >= $po_no['plannedqty']) {
                $data['status'] = 'C';
            } else {
                $data['status'] = 'O';
            }


            $itemdetails = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['item_id']])->first();

            $cats = $this->Production->patchEntity($productions, $data);
            if ($purchasess = $this->Production->save($cats)) {
                $this->Flash->success(__('Daily Sheet Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Productionorder');
        $this->loadModel('Additem');


        $productions = $this->Production->get($id);
        $this->set('productions', $productions);

        $machine_id = $productions['machine_id'];
        $machine_name = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $machine_id])->first();
        $this->set('machine_name', $machine_name);

        $po_no = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $productions['po_id'], 'Productionorder.item_id' => $productions['item_id']])->first();

        if ($this->request->is(['post', 'put'])) {

            $poexists = $this->Productionorder->exists(['po_id' => $this->request->data('po_id')]);
            if ($poexists == '' && $poexists == null) {
                $this->Flash->error(__('Your enterd PO id does not exists.'));
                return $this->redirect(['action' => 'edit/', $id]);
            }

            if ($this->request->data('machines_id') == '') {
                $this->Flash->error(__('Your enterd Machine does not exists.'));
                return $this->redirect(['action' => 'edit/', $id]);
            }

            $attnExist = $this->Production->find('all')->where([
                'Production.machine_id' => $this->request->data['machines_id'],
                'Production.production_date' => date('Y-m-d'),
                'NOT' => ['Production.id' => $id]
            ])->first();

            if ($attnExist) {
                $this->Flash->error(__('Entry For this machine on ' . date('Y-m-d') . ' Already Exists.'));
                return $this->redirect(['action' => 'edit/', $id]);
            }


            $data['production_date'] = date('Y-m-d', strtotime($this->request->data['production_date']));
            $data['contract_id'] = $this->request->data['contract_id'];
            $data['po_id'] = $this->request->data['po_id'];
            $data['item_id'] = $this->request->data['item_id'];
            $data['productprocess_id'] = $this->request->data['productprocess_id'];
            $data['machine_id'] = $this->request->data['machines_id'];
            $data['plan_qty'] = $this->request->data['plan_qty'];
            $data['production_shift_a'] = $this->request->data['production_shift_a'];
            $data['production_shift_b'] = $this->request->data['production_shift_b'];
            $data['reading8am'] = $this->request->data['reading8am'];
            $data['reading8pm'] = $this->request->data['reading8pm'];
            $data['nextday8am'] = $this->request->data['nextday8am'];
            $data['manpower_day'] = $this->request->data['manpower_day'];
            $data['manpower_night'] = $this->request->data['manpower_night'];
            $data['scrap'] = $this->request->data['scrap'];
            $data['updated'] = date('Y-m-d H:i:s');
            $data['remark'] = $this->removeEmojis($this->request->data['remark']);


            $productionsqty = $this->Production->find('all')->where(['Production.po_id' => $productions['po_id'], 'Production.item_id' => $productions['item_id'], 'Production.productprocess_id' => $productions['productprocess_id'], 'NOT' => ['Production.id' => $productions['id']]])->order(['Production.production_date' => 'DESC'])->toarray();

            $quantity = '';
            foreach ($productionsqty as $value) {
                $quantity += $value['production_shift_a'] + $value['production_shift_b'];
            }
            $totalqty = $quantity + $this->request->data['production_shift_a'] + $this->request->data['production_shift_b'];

            if ($totalqty >= $po_no['plannedqty']) {
                $data['status'] = 'C';
            } else {
                $data['status'] = 'O';
            }

            $itemdetails = $this->Additem->find('all')->where(['Additem.id' => $productions['item_id']])->first();

            // if ($itemdetails['finishedprocess_id'] == $productions['productprocess_id']) {
            //     $postatus = $data['status'];
            //     $poid = $po_no['po_id'];
            //     $item_id = $po_no['item_id'];
            //     $connection = ConnectionManager::get('default');
            //     $postatusupdate = "UPDATE `productionorder` SET `status`='$postatus' WHERE `po_id`='$poid' AND `item_id`='$item_id'";
            //     $connection->execute($postatusupdate);
            // }



            $cats = $this->Production->patchEntity($productions, $data);

            if ($purchasess = $this->Production->save($cats)) {
                $this->Flash->success(__('Data Update has been updated Successfully.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function add_datewise_dailysheet()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');
        $this->loadModel('Machinemaster');

        $machineNames = $this->Machinemaster->find('all')->order(['machine_name' => 'asc'])->toarray();
        $processoptions = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.process_name' => 'asc'])->toarray();
        $this->set(compact('machineNames', 'processoptions'));



        if ($this->request->is(['post'])) {
            // pr($this->request->data);
            // die;

            foreach ($this->request->data['plan_qty'] as $key => $planQty) {

                if ($planQty == '') {
                    continue;
                }

                $productions = $this->Production->newEntity();

                $data['production_date'] = date('Y-m-d');
                $data['contract_id'] = $this->request->data['contract_id'][$key];
                $data['po_id'] = $this->request->data['po_id'][$key];
                $data['item_id'] = $this->request->data['item_id'][$key];
                $data['productprocess_id'] = $this->request->data['productprocess_id'][$key];
                $data['machine_id'] = $this->request->data['machines_id'][$key];
                $data['plan_qty'] = $this->request->data['plan_qty'][$key];
                $data['production_shift_a'] = $this->request->data['production_shift_a'];
                $data['production_shift_b'] = $this->request->data['production_shift_b'];
                $data['reading8am'] = NULL;
                $data['reading8pm'] = NULL;
                $data['nextday8am'] = NULL;
                $data['manpower_day'] = NULL;
                $data['manpower_night'] = NULL;
                $data['scrap'] = $this->request->data['scrap'];
                $data['remark'] = $this->removeEmojis($this->request->data['remark']);
                $data['is_completed'] = NULL;

                $cats = $this->Production->patchEntity($productions, $data);
                $purchasess = $this->Production->save($cats);
            }

            $this->Flash->success(__('Daily Sheet Successfully Added.'));
            return $this->redirect(['action' => 'index']);
        }
    }






    public function edit_datewise_dailysheet($date)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');
        $this->loadModel('Machinemaster');

        $machineNames = $this->Machinemaster->find('all')->order(['machine_name' => 'asc'])->toarray();
        $processoptions = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.process_name' => 'asc'])->toarray();

        $productionDeatils = $this->Production->find('all')->where(['Production.production_date' => $date])->toarray();

        $machineIds = [];
        foreach ($productionDeatils as $production) {
            $machineIds[] = $production['machine_id'];
        }

        $this->set(compact('machineNames', 'processoptions', 'date', 'machineIds'));



        if ($this->request->is(['post'])) {
            // pr($this->request->data);
            // die;

            foreach ($this->request->data['plan_qty'] as $key => $planQty) {

                if ($planQty == '') {
                    continue;
                }

                $productions = $this->Production->find('all')->where(['production_date' => $date, 'machine_id' => $this->request->data['machines_id'][$key]])->order(['id' => 'desc'])->first();

                $data['production_date'] = $date;
                $data['contract_id'] = $this->request->data['contract_id'][$key];
                $data['po_id'] = $this->request->data['po_id'][$key];
                $data['item_id'] = $this->request->data['item_id'][$key];
                $data['productprocess_id'] = $this->request->data['productprocess_id'][$key];
                $data['machine_id'] = $this->request->data['machines_id'][$key];
                $data['plan_qty'] = $this->request->data['plan_qty'][$key];
                $data['production_shift_a'] = $this->request->data['production_shift_a'][$key];
                $data['production_shift_b'] = $this->request->data['production_shift_b'][$key];
                $data['reading8am'] = $this->request->data['reading8am'][$key];
                $data['reading8pm'] = $this->request->data['reading8pm'][$key];
                $data['nextday8am'] = $this->request->data['nextday8am'][$key];
                $data['manpower_day'] = $this->request->data['manpower_day'][$key];
                $data['manpower_night'] = $this->request->data['manpower_night'][$key];
                $data['scrap'] = $this->request->data['scrap'][$key];
                $data['remark'] = $this->request->data['remark'][$key];
                $data['is_completed'] = NULL;

                $cats = $this->Production->patchEntity($productions, $data);
                $purchasess = $this->Production->save($cats);
            }

            $this->Flash->success(__('Daily Sheet Successfully Added.'));
            return $this->redirect(['action' => 'index']);
        }
    }



    public function deletedailysheet($id)
    {
        $this->loadModel('Production');
        $this->loadModel('Productiondetails');

        $podelete = $this->Production->get($id);
        $podetails = $this->Productiondetails->find('all')->where(['Productiondetails.production_id' => $id])->toarray();
        if ($this->Production->delete($podelete)) {

            foreach ($podetails as $value) {

                $this->Productiondetails->delete($value);
            }

            $this->Flash->success('Daily Shaeet deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }


    public function searchitem()
    {
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');

        $reqdata = $_GET;
        $po_id = trim($reqdata['po_id']);
        $contract_id = $reqdata['contract_id'];
        $item_id = $reqdata['item_id'];
        $machines_id = $reqdata['machines_id'];
        $productprocess_id = $reqdata['productprocess_id'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $searchdata = [];


        if (!empty($po_id)) {
            $searchdata['Production.po_id'] = $po_id;
        }
        if (!empty($contract_id)) {
            $searchdata['Production.contract_id'] = $contract_id;
        }
        if (!empty($item_id)) {
            $searchdata['Production.item_id'] = $item_id;
        }
        if (!empty($machines_id)) {
            $searchdata['Production.machine_id'] = $machines_id;
        }
        if (!empty($productprocess_id)) {
            $searchdata['Production.productprocess_id'] = $productprocess_id;
        }
        if ($datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {
            $searchdata['DATE(Production.production_date) >='] = $datefrom;
            $searchdata['DATE(Production.production_date) <='] = $dateto2;
        } else if ($datefrom != '1970-01-01') {
            $searchdata['DATE(Production.production_date) ='] = $datefrom;
        }


        $this->request->session()->write('searchdata', $searchdata);

        $production_data = $this->Production->find()->contain(['Machinemaster'])->where([$searchdata])->order(['Production.id' => 'DESC']);
        $production_data = $this->paginate($production_data)->toarray();
        $this->set(compact('production_data'));
    }


    public function getdailysheet()
    {
        $this->loadModel('Productionorder');
        $this->loadModel('Finishedprocess');

        $poid = $this->request->data['po_id'];
        $productionorder = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $poid])->order(['Productionorder.id' => 'Desc'])->first();
        $processname = $this->Finishedprocess->find('all')->toarray();
        $this->set(compact('productionorder', 'processname'));
    }

    public function getname()
    {
        $this->loadModel('Machinemaster');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $numberAfterUnderscore = $this->request->data['number'];
        $inputId = $this->request->data['inputId'];

        $searchst = $this->Machinemaster->find('all')->where(['Machinemaster.machine_name LIKE' => '%' . $stsearch . '%', 'Machinemaster.status' => 'Y'])->order(['Machinemaster.machine_name' => 'asc'])->toarray();
        $i = 1;
        foreach ($searchst as $value) {

            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['machine_name'] . "'" . ','
                . "'" . $value['id'] . "'" . ',' .
                "'" . $numberAfterUnderscore . "'" . ',' .
                "'" . $inputId . "'" . ')">
             <a href="javascript:void(0)" style="color: black;">' . $i . '.' . $value['machine_name'] . '</a></li>';
            $i++;
        }
        die;
    }



    public function dailysheetexcel()
    {
        $this->loadModel('Production');
        $reqdata = $this->request->session()->read('searchdata');
        if ($reqdata) {
            $item = $this->Production->find()->contain(['Machinemaster'])->where([$reqdata])->order(['Production.id' => 'DESC'])->toarray();
            $this->request->session()->delete('searchdata');
        } else {
            $item = $this->Production->find('all')->contain(['Machinemaster'])->order(['Production.id' => 'desc'])->toarray();
        }
        $this->set(compact('item'));
    }





    function billsofmaterials()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Bom');
        $this->loadModel('Productionorder');
        $item = $this->Bom->find('all')->order(['Bom.contract_id' => 'desc']);
        $item = $this->paginate($item)->toarray();
        $this->set('bills_data', $item);
    }


    public function getbom()
    {
        $this->loadModel('Bom');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Bom->find('all')->where(['Bom.title LIKE' => '%' . $stsearch . '%'])->toarray();
        $i = 1;
        foreach ($searchst as $value) {

            echo '<li  style="padding: 5px 8px; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . $check . '(' . "'" . $value['title'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $i . '.' . $value['title'] . '</a></li>';
            $i++;
        }
        die;
    }
    public function getrawproductname()
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $stsearch = $this->request->data['fetch'];
        $bomname = $this->Bom->find('all')->where(['Bom.id' => $stsearch])->first();
        $item = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.bom_id' => $stsearch])->toarray();
        $this->set(compact('item', 'bomname'));
    }

    function addbom()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Designsheet');

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data('contract_id') == '') {
                $this->Flash->error(__('Your enterd Contract does not exists.'));
                return $this->redirect(['action' => 'addbom']);
            }
            $contraexist = $this->Bom->exists(['contract_id' => trim($this->request->data['contract_id'])]);
            if ($contraexist) {
                $this->Flash->error(__('Your entered contract already exists.'));
                return $this->redirect(['action' => 'addbom']);
            }
            $poerder['contract_id'] = $this->request->data['contract_id'];
            $poerder['comment'] = $this->request->data['comment'];
            $newpo = $this->Bom->patchEntity($this->Bom->newEntity(), $poerder);
            if ($purchasess = $this->Bom->save($newpo)) {
                $lstid = $purchasess->id;
                return $this->redirect(['action' => 'editaddbom/', $lstid]);
            }
        }
    }

    function editaddbom($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Designsheet');
        $this->loadModel('Indentpo');

        $user = $this->Bom->find('all')->where(['Bom.id' => $id])->first();
        $contractid = $user['contract_id'];
        $indentpoid = $this->Indentpo->find('all')->where(['Indentpo.contract_id' => $contractid])->order(['Indentpo.indent_id' => 'Desc'])->toarray();
        $product = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.contract_id' => $user['contract_id']])->toarray();
        $raw = $this->Bomrawmaterial->find('all')->where(['Bomrawmaterial.contract_id' => $id])->toarray();
        $contractdetails = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid])->toarray();
        $this->set(compact('user', 'product', 'raw', 'contractdetails', 'indentpoid'));
    }
    public function deletedata()
    {
        $this->loadModel('Bomfinishedproduct');
        $this->autoRender = false;
        $fetch = $this->request->data['fetch'];
        $result = $this->Bomfinishedproduct->deleteAll(['Bomfinishedproduct.id' => $fetch]);
        $this->set('result', $result);
    }
    public function deletedata1()
    {
        $this->loadModel('Bomrawmaterial');
        $this->autoRender = false;

        $fetch = $this->request->data['fetch'];
        $result = $this->Bomrawmaterial->deleteAll(['Bomrawmaterial.id' => $fetch]);
        $this->set('result', $result);
    }


    function viewcontractdetailspdf($contraid, $erpID = null)
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');
        $this->loadModel('InspectionReport');
        $this->loadModel('Productionorder');
        $this->loadModel('Finishedprocess');
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

            $contract = $connss->execute("SELECT * FROM `contracts` where `id`='" . $contraid . "' limit 1");
            $contractdetail = $contract->fetch('assoc');

            $finishedpro = $connss->execute("SELECT * FROM `bom_finisedproduct` where `contract_id`= '" . $contraid . "'");
            $finsheddetails = $finishedpro->fetchAll('assoc');

            $designsheet = $connss->execute("SELECT * FROM `designsheetdetails` where `contract_id` = '" . $contraid . "' group by item_id ");
            $designs = $designsheet->fetchAll('assoc');

            $production = $connss->execute("SELECT * FROM `productionorder` where `contract_id`= '" . $contraid . "' order by id desc");
            $podetails = $production->fetchAll('assoc');

            $process = $connss->execute("SELECT * FROM `finishedproduct_process`");
            $processname = $process->fetchAll('assoc');
        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
            $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contraid])->toarray();
            $designs = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.contract_id' => $contraid])->group('item_id')->toarray();
            $podetails = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contraid])->order(['Productionorder.id ' => 'Desc'])->toarray();
            $inspection = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y", 'InspectionReport.work_order_no' => $contraid])->order(['InspectionReport.id' => 'Desc'])->toarray();
            $processname = $this->Finishedprocess->find('all')->toarray();
        }

        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('contractdetail', 'finsheddetails', 'designs', 'podetails', 'inspection', 'processname'));
    }
    public function viewcontractdetail($contraid)
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');
        $this->loadModel('InspectionReport');
        $this->loadModel('Productionorder');
        $this->loadModel('Finishedprocess');

        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contraid])->toarray();
        $designs = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.contract_id' => $contraid])->group('item_id')->toarray();
        $podetails = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contraid])->order(['Productionorder.id ' => 'Desc'])->toarray();
        $inspection = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y", 'InspectionReport.work_order_no' => $contraid])->order(['InspectionReport.id' => 'Desc'])->toarray();
        $processname = $this->Finishedprocess->find('all')->toarray();
        $this->set(compact('contractdetail', 'finsheddetails', 'designs', 'podetails', 'inspection', 'processname'));
    }




    function productionorders()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Productionorder');

        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $status = $reqdata['status'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $condition = [];


        if (!empty($contract_id)) {
            $condition['Productionorder.contract_id'] = $contract_id;
        }
        if (!empty($status)) {
            $condition['Productionorder.status'] = $status;
        }
        if ($datefrom != '1970-01-01') {
            $condition['DATE(Productionorder.complete_date) ='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $condition['DATE(Productionorder.complete_date) <='] = $dateto2;
        }

        if ($reqdata) {
            $productionorder = $this->Productionorder->find()->where([$condition])->order(['Productionorder.po_id' => 'DESC']);
        } else {
            $productionorder = $this->Productionorder->find('all')->order(['Productionorder.po_id' => 'Desc']);
        }

        $productionorder = $this->paginate($productionorder)->toarray();
        $this->set('productionorder', $productionorder);
    }



    function addproductionorders()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Productionorder');

        $productorderid = $this->Productionorder->find('all')->order(['Productionorder.po_id' => 'Desc'])->first();
        if ($productorderid['po_id'] != "") {
            $newproductorderid = $productorderid['po_id'] + 1;
        } else {
            $newproductorderid = "1001";
        }
        $this->set('newproductorderid', $newproductorderid);


        if ($this->request->is(['post', 'put'])) {

            foreach ($this->request->data['plannedqty'] as $key => $value) {
                if ($value != '') {
                    if ($this->request->data('contract_id') == '') {
                        $this->Flash->error(__('Your enterd Contract does not exists.'));
                        return $this->redirect(['action' => 'add']);
                    }

                    $poerder['po_id'] = $this->request->data['po_id'];
                    $poerder['issuedate'] = date('Y-m-d', strtotime($this->request->data['issuedate']));
                    $poerder['contract_id'] = $this->request->data['contract_id'];
                    $poerder['item_id'] = $this->request->data['item_id'][$key];
                    $poerder['plannedqty'] = $this->request->data['plannedqty'][$key];
                    $poerder['uom'] = $this->request->data['uom'][$key];
                    $poerder['startdate'] = date('Y-m-d', strtotime($this->request->data['startdate'][$key]));
                    $poerder['enddate'] = date('Y-m-d', strtotime($this->request->data['enddate'][$key]));
                    $poerder['totaldays'] = $this->request->data['totaldays'][$key];
                    $newpo = $this->Productionorder->patchEntity($this->Productionorder->newEntity(), $poerder);
                    $purchasess = $this->Productionorder->save($newpo);
                }
            }
            $this->Flash->success(__('Data has been saved Successfully.'));
            return $this->redirect(['action' => 'productionorders']);
        }
    }

    public function delete($id)
    {
        $this->loadModel('Productionorder');
        $podelete = $this->Productionorder->get($id);

        if ($this->Productionorder->delete($podelete)) {
            $this->Flash->success('Production Order deleted successfully');
            return $this->redirect(['action' => 'productionorders']);
        }
    }

    // public function status($id, $status)
    // {
    //     $this->loadModel('Productionorder');
    //     $this->autoRender = false;

    //     $production = $this->Productionorder->get($id);
    //     if ($status == 'C') {
    //         $production->complete_date = date('Y-m-d');
    //         $message = 'Production Order close successfully.';
    //     } else {
    //         $production->complete_date = NULL;
    //         $message = 'Production Order open successfully.';
    //     }

    //     $production->status = $status;
    //     if ($this->Productionorder->save($production)) {
    //         $this->Flash->success($message);
    //         return $this->redirect(['action' => 'productionorders']);
    //     };
    // }

    public function status($id, $status)
    {
        $this->loadModel('Productionorder');
        $this->loadModel('Production');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');

        $this->autoRender = false;

        $production = $this->Productionorder->get($id);

        // ✅ Only check when closing
        if ($status == 'C') {

            $itemId = $production->item_id;
            $poId   = $production->po_id; // ✅ correct

            // Step 1: Get required processes from Additem
            $item = $this->Additem->find()
                ->select(['productprocess_id'])
                ->where(['id' => $itemId])
                ->first();

            if (empty($item) || empty($item->productprocess_id)) {
                $this->Flash->error('No process defined for this item.');
                return $this->redirect(['action' => 'productionorders']);
            }

            // Convert to array (int)
            $requiredProcesses = array_map('intval', array_map('trim', explode(',', $item->productprocess_id)));

            // Step 2: Get completed processes from Production
            $completedProcesses = $this->Production->find()
                ->select(['productprocess_id'])
                ->where([
                    'po_id'   => $poId,
                    'item_id' => $itemId,
                    'productprocess_id IN' => $requiredProcesses
                ])
                ->distinct(['productprocess_id']) // duplicate avoid
                ->extract('productprocess_id')   // 🔥 IMPORTANT FIX
                ->toArray();

            // Normalize
            $completedProcesses = array_map('intval', $completedProcesses);

            // Step 3: Find missing processes
            $missing = array_diff($requiredProcesses, $completedProcesses);

            $finishedprocess = $this->Finishedprocess->find('all')
                ->order(['Finishedprocess.id' => "ASC"])
                ->toArray();

            // Create mapping: [id => process_name]
            $processMap = [];

            foreach ($finishedprocess as $process) {
                $processMap[$process->id] = $process->process_name;
            }

            // Convert missing IDs → Names
            $missingNames = [];

            foreach ($missing as $id) {
                if (isset($processMap[$id])) {
                    $missingNames[] = $processMap[$id];
                } else {
                    $missingNames[] = 'Unknown(' . $id . ')';
                }
            }

            // Final check
            if (!empty($missingNames)) {
                $this->Flash->error(
                    'Cannot close. Pending processes: ' . implode(', ', $missingNames)
                );
                return $this->redirect(['action' => 'productionorders']);
            }

            // ✅ All processes completed
            $production->complete_date = date('Y-m-d');
            $message = 'Production Order closed successfully.';
        } else {
            // Re-open case
            $production->complete_date = null;
            $message = 'Production Order opened successfully.';
        }

        // Update status
        $production->status = $status;

        if ($this->Productionorder->save($production)) {
            $this->Flash->success($message);
        } else {
            $this->Flash->error('Something went wrong.');
        }

        return $this->redirect(['action' => 'productionorders']);
    }



    public function searchpodetail()
    {
        $this->loadModel('Productionorder');

        $reqdata = $_GET;
        $contract_id = $reqdata['contract_id'];
        $status = $reqdata['status'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));
        $condition = [];

        if (!empty($contract_id)) {
            $condition['Productionorder.contract_id'] = $contract_id;
        }
        if (!empty($status)) {
            $condition['Productionorder.status'] = $status;
        }
        if ($datefrom != '1970-01-01') {
            $condition['DATE(Productionorder.complete_date) ='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $condition['DATE(Productionorder.complete_date) <='] = $dateto2;
        }

        $this->request->session()->write('condition', $condition);
        $productionorder = $this->Productionorder->find()->where([$condition])->order(['Productionorder.po_id' => 'DESC']);
        $productionorder = $this->paginate($productionorder)->toarray();
        $this->set(compact('productionorder'));
    }


    public function viewproductiondetails($poid)
    {
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Finishedprocess');
        $productionorder = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $poid])->order(['Productionorder.id' => 'Desc'])->first();
        $processname = $this->Finishedprocess->find('all')->toarray();
        $this->set(compact('productionorder', 'processname'));
    }

    public function viewproductionpdf($poid, $erpID = null)
    {
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Finishedprocess');
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

            $production = $connss->execute("SELECT * FROM `productionorder` where `po_id`= '" . $poid . "' order by id desc");
            $productionorder = $production->fetch('assoc');

            $process = $connss->execute("SELECT * FROM `finishedproduct_process`");
            $processname = $process->fetchAll('assoc');
        } else {
            $sitesetting = $this->Sitesettings->find('all')->first();
            $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
            $productionorder = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $poid])->order(['Productionorder.id' => 'Desc'])->first();
            $processname = $this->Finishedprocess->find('all')->toarray();
        }
        $this->set(compact(['sitesetting', 'site_details']));
        $this->set(compact('productionorder', 'processname'));
    }
    function productionorderexcel()
    {
        $this->loadModel('Productionorder');
        $where = $this->request->session()->read('condition');
        if ($where) {
            $productionorder = $this->Productionorder->find('all')->where([$where])->order(['Productionorder.id' => 'Desc'])->toarray();
            $this->request->session()->delete('condition');
        } else {
            $productionorder = $this->Productionorder->find('all')->order(['Productionorder.id' => 'Desc'])->toarray();
        }
        $this->set('productionorder', $productionorder);
    }
    function operationreports()
    {

        $this->viewBuilder()->layout('admin');
    }

    function productionoperations()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addproductionoperations()
    {

        $this->viewBuilder()->layout('admin');
    }

    function labourcosts()
    {

        $this->viewBuilder()->layout('admin');
    }
    function routings()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addroutings()
    {


        $this->viewBuilder()->layout('admin');
    }
    function overview()
    {
        $this->viewBuilder()->layout('admin');
    }
    function audit()
    {
        $this->viewBuilder()->layout('admin');
    }

    public function getitemsname()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $fetch = $this->request->data['fetch'];

        $itemname = $this->Additem->find('All')->where(['Additem.status' => 'Y', 'Additem.id' => $fetch])->order(['Additem.id' => 'asc'])->first();
        $unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $itemname['uom']])->first();
        $this->set(compact('itemname', 'unitname'));
    }


    public function getmaterialname()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $fetch = $this->request->data['fetch'];

        $itemname = $this->Additem->find('All')->where(['Additem.status' => 'Y', 'Additem.id' => $fetch])->order(['Additem.id' => 'asc'])->first();
        $unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $itemname['uom']])->first();
        $this->set(compact('itemname', 'unitname'));
    }

    public function getcontract()
    {
        $this->loadModel('Contracts');
        $check = $this->request->data['check'];
        $stsearch = $this->request->data['fetch'];
        // $searchst = $this->Contracts->find('all')->where(['Contracts.workorder LIKE' => '%' . $stsearch . '%', 'Contracts.status' => 'Y',])->toarray();

        $searchst = $this->Contracts->find('all')
            ->where([
                'Contracts.status' => 'Y',
                'OR' => [
                    ['Contracts.workorder LIKE' => '%' . $stsearch . '%'],
                    ['Contracts.title LIKE' => '%' . $stsearch . '%'],
                ]
            ])
            ->toArray();;


        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;margin-left:-32px; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . $check . '(' . "'" . $value['title'] . '(' . $value['workorder'] . ')' . "'" . ',' . "'" . $value['id'] . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['title'] . '(' . $value['workorder'] . ')</a></li>';
        }
        die;
    }


    public function getcontractfordailysheet()
    {
        $this->loadModel('Contracts');
        $check = $this->request->data['check'];
        $stsearch = $this->request->data['fetch'];
        $inputId = $this->request->data['inputId'];

        $searchst = $this->Contracts->find('all')
            ->where([
                'Contracts.status' => 'Y',
                'OR' => [
                    ['Contracts.workorder LIKE' => '%' . $stsearch . '%'],
                    ['Contracts.title LIKE' => '%' . $stsearch . '%'],
                ]
            ])
            ->toArray();;


        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;margin-left:-32px; border: 1px solid lightgray; list-style:none;" 
            onclick="cllbckretail' . $check . '(' . "'" . $value['title'] . '(' . $value['workorder'] . ')' . "'" . ','
                . "'" . $value['id'] . "'" . ','
                . "'" . $inputId . "'" . ')">
            <a href="javascript:void(0)" style="color: black;">' . $value['title'] . '(' . $value['workorder'] . ')</a>
            </li>';
        }
        die;
    }

    public function finisheditems()
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Productionorder');


        $contractid = $this->request->data['id'];
        $productid = $this->request->data['productid'];

        $item = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contractid, 'Bomfinishedproduct.product_id' => $productid])->toarray();
        $itemcount = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contractid])->count();
        $this->set(compact('item', 'contractid', 'itemcount'));
    }
    public function getpo_id()
    {
        $this->loadModel('Productionorder');

        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Productionorder->find('all')->where(['Productionorder.po_id LIKE' => '%' . $stsearch . '%'])->group(['Productionorder.po_id'])->toarray();

        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . $check . '(' . $value['po_id'] . ')"><a href="javascript:void(0)" style="color: black;">' . $value['po_id'] . '</a></li>';
        }
        die;
    }


    public function getmachinereading()
    {
        $this->loadModel('Production');

        $stsearch = $this->request->data['fetch'];
        // $searchst = $this->Production->find('all')->where(['Production.machine_id ' => $stsearch, 'NOT' => (['Production.production_date' => date('Y-m-d')])])->order(['Production.id ' => 'Desc'])->first();

        $searchst = $this->Production->find('all')->where(['Production.machine_id ' => $stsearch])->order(['Production.id ' => 'Desc'])->first();

        if (!empty($searchst)) {
            $nextday8am = $searchst->nextday8am;
            echo json_encode(['nextday8am' => $nextday8am]);
        } else {
            echo json_encode(['nextday8am' => null]);
        }
        die;
    }
    public function checkmachinentry()
    {
        $this->loadModel('Production');

        $stsearch = $this->request->data['machineid'];

        $searchst = $this->Production->find('all')->where(['Production.machine_id ' => $stsearch, 'Production.production_date ' => date('Y-m-d')])->count();

        echo json_encode($searchst);
        die;
    }

    public function getindentno()
    {
        $this->loadModel('Indentpo');
        $this->autoRender = false;

        $itemid = $this->request->data['itemid'];
        $poid = $this->request->data['poid'];
        $identpo = $this->Indentpo->find('all')->where(['Indentpo.finishedproduct_id' => $itemid, 'Indentpo.productionorderno' => $poid])->toarray();
        echo json_encode($identpo);
        die;
    }


    public function getproductionorder()
    {
        $this->loadModel('Productionorder');
        $this->autoRender = false;

        $contractid = $this->request->data['contractid'];
        $item_id = $this->request->data['item_id'];
        $pono = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contractid, 'Productionorder.item_id' => $item_id, 'Productionorder.status' => 'O'])->group('po_id')->toarray();
        echo json_encode($pono);
        die;
    }

    public function getfinishedproduct()
    {
        $this->loadModel('Productionorder');
        $this->loadModel('Additem');
        $this->autoRender = false;

        $poid = $this->request->data['po_id'];
        $searchst = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $poid])->toarray();
        foreach ($searchst as $value) {
            $item[] = $this->Additem->find('all')->where(['Additem.id' => $value['item_id']])->first();
        }
        echo json_encode($item);
        die;
    }
    public function getcompletepo()
    {
        $this->loadModel('Productionorder');

        $contractid = $this->request->data['contractid'];
        $podetails = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contractid])->order(['Productionorder.id ' => 'Desc'])->toarray();
        $this->set('podetails', $podetails);
    }


    public function getcontractfinished()
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Productionorder');

        $contractid = $this->request->data['contract_id'];

        $item = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contractid])->toarray();
        foreach ($item as $value) {
            $itemname[] = $this->Additem->find('all')->where(['Additem.id' => $value['product_id']])->first();
        }
        echo json_encode($itemname);
        die;
    }

    public function getproductprocess()
    {
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');
        $this->autoRender = false;

        $poid = $this->request->data['productid'];
        $searchst = $this->Additem->find('all')->where(['Additem.id' => $poid])->first();

        $process = [];
        $process = explode(',', $searchst['productprocess_id']);
        foreach ($process as $value) {
            $process_id[] = $this->Finishedprocess->find('all')->where(['Finishedprocess.id' => $value])->first();
        }
        echo json_encode($process_id);
        die;
    }

    public function checkprocesscompletion()
    {
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->autoRender = false;

        $processid = $this->request->data['processid'];
        $po_id = $this->request->data['po_id'];
        $product_id = $this->request->data['product_id'];

        $reqproductqty = $this->Productionorder->find('all')->where(['Productionorder.po_id' => $po_id, 'Productionorder.item_id' => $product_id])->first();
        $processcal = $this->Production->find('all')->where(['Production.po_id' => $po_id, 'Production.productprocess_id' => $processid, 'Production.item_id' => $product_id])->toarray();

        if ($processcal) {
            foreach ($processcal as $value) {
                $itemqty += $value['production_shift_a'] + $value['production_shift_b'];
            }
        } else {
            $itemqty = 0;
        }

        if ($itemqty >= $reqproductqty['plannedqty']) {
            $checkpro = 'true';
        }

        echo json_encode($checkpro);
        die;
    }
}
