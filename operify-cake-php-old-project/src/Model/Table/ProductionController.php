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
    }

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Plannedtype');

        $item = $this->Production->find('all')->contain(['Machinemaster'])->order(['Production.production_date' => 'desc']);
        $item = $this->paginate($item)->toarray();
        $this->set('production_data', $item);
    }


    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Productionorder');
        $this->loadModel('Plannedtype');

        $productions = $this->Production->newEntity();

        if ($this->request->is(['post'])) {

            $poexists = $this->Productionorder->exists(['po_id' => $this->request->data('po_id')]);
            if($poexists == '' && $poexists == null){
                $this->Flash->error(__('Your enterd PO id does not exists.'));
                return $this->redirect(['action' => 'add']);
            }
            
            if($this->request->data('machines_id') ==''){
                $this->Flash->error(__('Your enterd Machine does not exists.'));
                return $this->redirect(['action' => 'add']);
            }


            $data['production_date'] = date('Y-m-d', strtotime($this->request->data['production_date']));
            $data['po_id'] = $this->request->data['po_id'];
            $data['item_id'] = $this->request->data['item_id'];
            $data['machine_id'] = $this->request->data['machines_id'];
            $data['production_shift_a'] = $this->request->data['production_shift_a'];
            $data['production_shift_b'] = $this->request->data['production_shift_b'];
            $data['reading8am'] = $this->request->data['reading8am'];
            $data['reading8pm'] = $this->request->data['reading8pm'];
            $data['nextday8am'] = $this->request->data['nextday8am'];
            $data['material_issued'] = $this->request->data['material_issued'];
            $data['material_consumed_desgin'] = $this->request->data['material_issued_desgin'];
            $data['material_consumed_actual'] = $this->request->data['material_issued_actual'];
            $data['balance'] = $this->request->data['balance'];
            $data['manpower_day'] = $this->request->data['manpower_day'];
            $data['manpower_night'] = $this->request->data['manpower_night'];
            $data['scrap'] = $this->request->data['scrap'];
            $data['remark'] = $this->request->data['remark'];
            $cats = $this->Production->patchEntity($productions, $data);
            if ($this->Production->save($cats)) {
                $this->Flash->success(__('Daily Sheet Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Plannedtype');
        $this->loadModel('Machinemaster');
        $this->loadModel('Productionorder');


        $productions = $this->Production->get($id);
        $this->set('productions', $productions);

        $machine_id = $productions['machine_id'];
        $machine_name = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $machine_id])->first();
        $this->set('machine_name', $machine_name);


        if ($this->request->is(['post', 'put'])) {

            $poexists = $this->Productionorder->exists(['po_id' => $this->request->data('po_id')]);
            if($poexists == '' && $poexists == null){
                $this->Flash->error(__('Your enterd PO id does not exists.'));
                return $this->redirect(['action' => 'edit/',$id]);
            }
            
            if($this->request->data('machines_id') ==''){
                $this->Flash->error(__('Your enterd Machine does not exists.'));
                return $this->redirect(['action' => 'edit/',$id]);
            }

            $attnExist = $this->Production->find('all')->where(['Production.machine_id' => $this->request->data['machines_id'],
                'Production.production_date' => date('Y-m-d'),'NOT' => ['Production.id' => $id]])->first();


             if ($attnExist) {
                $this->Flash->error(__('Entry For this machine on ' . date('Y-m-d') . ' Already Exists.'));
                return $this->redirect(['action' => 'edit/',$id]);
            }

            // pr($this->request->data);die;

            $data['production_date'] = date('Y-m-d', strtotime($this->request->data['production_date']));
            $data['po_id'] = $this->request->data['po_id'];
            if($this->request->data['item_id1']){
                $data['item_id'] = $this->request->data['item_id1'];
            }else{
                $data['item_id'] = $this->request->data['item_id'];
            }
            $data['machine_id'] = $this->request->data['machines_id'];
            $data['production_shift_a'] = $this->request->data['production_shift_a'];
            $data['production_shift_b'] = $this->request->data['production_shift_b'];
            $data['reading8am'] = $this->request->data['reading8am'];
            $data['reading8pm'] = $this->request->data['reading8pm'];
            $data['nextday8am'] = $this->request->data['nextday8am'];
            $data['material_issued'] = $this->request->data['material_issued'];
            $data['material_consumed_desgin'] = $this->request->data['material_issued_desgin'];
            $data['material_consumed_actual'] = $this->request->data['material_issued_actual'];
            $data['balance'] = $this->request->data['balance'];
            $data['manpower_day'] = $this->request->data['manpower_day'];
            $data['manpower_night'] = $this->request->data['manpower_night'];
            $data['scrap'] = $this->request->data['scrap'];
            $data['remark'] = $this->request->data['remark'];


            $slid = $this->Production->patchEntity($productions, $data);
            if ($this->Production->save($slid)) {
                $this->Flash->success(__('Data Update has been updated Successfully.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    
    public function deletedailysheet($id)
    {
        $this->loadModel('Production');
        $podelete = $this->Production->get($id);

        if ($this->Production->delete($podelete)) {
            $this->Flash->success('Daily Shaeet deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function getname()
    {
        $this->loadModel('Machinemaster');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $numberAfterUnderscore = $this->request->data['number'];

        $searchst = $this->Machinemaster->find('all')->where(['Machinemaster.machine_name LIKE' => '%'.$stsearch . '%', 'Machinemaster.status' => 'Y'])->order(['Machinemaster.machine_name' => 'asc'])->toarray();
        $i = 1;
        foreach ($searchst as $value) {

            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['machine_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $numberAfterUnderscore . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $i . '.' . $value['machine_name'] . '</a></li>';
            $i++;
        }
        die;
    }



    public function export_excel()
    {
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Plannedtype');
        $item = $this->Production->find('all')->contain(['Machinemaster', 'Plannedtype'])->order(['Production.id' => 'desc'])->toarray();
        $this->set('production_data', $item);
    }





    function billsofmaterials()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Bom');
        $this->loadModel('Productionorder');
        $item = $this->Bom->find('all')->order(['Bom.id' => 'desc']);
        $item = $this->paginate($item)->toarray();
        $this->set('bills_data', $item);
    }



    public function getbom()
    {
        $this->loadModel('Bom');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Bom->find('all')->where(['Bom.title LIKE' => '%'.$stsearch . '%'])->toarray();
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

            if($this->request->data('contract_id') ==''){
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
        $user = $this->Bom->find('all')->where(['Bom.id' => $id])->first();
        $contractid = $user['contract_id'];
        $product = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.bom_id' => $id])->toarray();
        $raw = $this->Bomrawmaterial->find('all')->where(['Bomrawmaterial.bom_id' => $id])->toarray();
        $contractdetails = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contractid])->toarray();
        $this->set(compact('user', 'product', 'raw', 'contractdetails'));


        if ($this->request->is(['post', 'put'])) {
            $poerder['contract_id'] = $this->request->data['contract_id'];
            $poerder['comment'] = $this->request->data['comment'];
            $poerder['operation_cost'] = $this->request->data['operation_cost'];
            $poerder['labour_cost'] = $this->request->data['labour_cost'];
            $newpo = $this->Bom->patchEntity($user, $poerder);


            if ($purchasess = $this->Bom->save($newpo)) {
                $lstid = $purchasess->id;
                foreach ($this->request->data['finished_pro_itemid'] as $key => $value) {
                    $poerder1['bom_id'] = $lstid;
                    $poerder1['product_id'] = $value;
                    $poerder1['price'] = $this->request->data['price1'][$key];
                    $poerder1['quantity'] = $this->request->data['quantity1'][$key];
                    $newpo1 = $this->Bomfinishedproduct->patchEntity($this->Bomfinishedproduct->newEntity(), $poerder1);
                    $this->Bomfinishedproduct->save($newpo1);
                }

                foreach ($this->request->data['raw_materials_item_id'] as $key => $value) {
                    $poerder2['bom_id'] = $lstid;
                    $poerder2['product_id'] = $value;
                    $poerder2['price'] = $this->request->data['price2'][$key];
                    $poerder2['quantity'] = $this->request->data['quantity2'][$key];
                    $newpo2 = $this->Bomrawmaterial->patchEntity($this->Bomrawmaterial->newEntity(), $poerder2);
                    $this->Bomrawmaterial->save($newpo2);
                }

            }
            $this->Flash->success(__('Data Update has been updated Successfully.'));
            return $this->redirect(['action' => 'billsofmaterials']);
        }

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

    function viewdetail($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $user = $this->Bom->find('all')->where(['Bom.id' => $id])->first();
        $item = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.bom_id' => $id])->toarray();
        $rawitem = $this->Bomrawmaterial->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomrawmaterial.bom_id' => $id])->toarray();
        $this->set(compact('user', 'item', 'rawitem'));
    }
    function viewpdf($id, $contraid)
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheet');
        $this->loadModel('InspectionReport');

        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $bomdetail = $this->Bom->find('all')->where(['Bom.id' => $id])->first();

        $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.bom_id' => $id])->toarray();

        $rawitem = $this->Bomrawmaterial->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomrawmaterial.bom_id' => $id])->toarray();

        $designs = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contraid])->toarray();

        $inspection = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y",'InspectionReport.work_order_no' => $contraid])->order(['InspectionReport.id' => 'Desc'])->toarray();

        $this->set(compact('bomdetail', 'contractdetail', 'finsheddetails', 'rawitem', 'designs','inspection'));
        $this->response->type('pdf');

    }
    public function viewcontractdetail($id, $contraid)
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheet');
        $this->loadModel('InspectionReport');

        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $bomdetail = $this->Bom->find('all')->where(['Bom.id' => $id])->first();

        $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.bom_id' => $id])->toarray();

        $rawitem = $this->Bomrawmaterial->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomrawmaterial.bom_id' => $id])->toarray();

        $designs = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contraid])->toarray();

        $inspection = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y",'InspectionReport.work_order_no' => $contraid])->order(['InspectionReport.id' => 'Desc'])->toarray();

        $this->set(compact('bomdetail', 'contractdetail', 'finsheddetails', 'rawitem', 'designs','inspection'));
    }

    function productionorders()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Productionorder');

        $productionorder = $this->Productionorder->find('all')->order(['Productionorder.id' => 'Desc']);
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

            foreach ($this->request->data['item_id'] as $key => $value) {

                if($this->request->data('contract_id') ==''){
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
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Contracts->find('all')->where(['Contracts.title LIKE' => '%'.$stsearch . '%','Contracts.status' => 'Y',])->toarray();

        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;margin-left:-32px; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . $check . '(' . "'" . $value['title'] . '(' . $value['workorder'] . ')' . "'" . ',' . "'" . $value['id'] . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['title'] . '(' . $value['workorder'] . ')</a></li>';

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


        $stsearch = $this->request->data['id'];
        $bomname = $this->Bom->find('all')->where(['Bom.contract_id' => $stsearch])->first();

        $bomid = $bomname['id'];
        $item = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.bom_id' => $bomid])->toarray();
        $this->set(compact('item', 'bomname'));

    }
    public function getpo_id()
    {
        $this->loadModel('Productionorder');

        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Productionorder->find('all')->where(['Productionorder.po_id LIKE' => '%'.$stsearch . '%'])->group(['Productionorder.po_id'])->toarray();

        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . $check . '(' . $value['po_id'] . ')"><a href="javascript:void(0)" style="color: black;">' . $value['po_id'] . '</a></li>';

        }
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

        // $options = [];
        // foreach ($item as $value) {
        //     $options[] = [
        //         'id' => $value['id'],
        //         'item_name' => $value['item_name']
        //     ];
        // }
        echo json_encode($item); die;
    }

    public function getmachinereading()
    {
        $this->loadModel('Production');
    
        $stsearch = $this->request->data['fetch'];
        $searchst = $this->Production->find('all')->where(['Production.machine_id ' => $stsearch,'NOT' =>(['Production.production_date' => date('Y-m-d')])])->order(['Production.id ' => 'Desc'])->first();
        
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

        $searchst = $this->Production->find('all')->where(['Production.machine_id ' => $stsearch,'Production.production_date ' => date('Y-m-d')])->count();
        
         echo json_encode($searchst);
        die;
    }
}


