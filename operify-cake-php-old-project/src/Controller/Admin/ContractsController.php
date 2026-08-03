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


class ContractsController extends AppController
{
    // $this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
        $this->loadModel('Contracts');
        $this->loadModel('Vendor');
        $this->Auth->allow(['vishnupo']);
    }



    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Contracts');
        $this->loadModel('Vendor');

        $reqdata = $_GET;

        $vendor_id = $reqdata['vendor_id'];
        $contract_id = $reqdata['contract_id'];
        $cost = trim($reqdata['cost']);
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));

        $cond = [];
        if (!empty($contract_id)) {
            $contra = ['Contracts.id' => $contract_id];
            $cond[] = $contra;
        }

        if (!empty($vendor_id)) {
            $contra = ['Contracts.supplier_id' => $vendor_id];
            $cond[] = $contra;
        }
        if (!empty($cost)) {
            $contra = ['Contracts.cost LIKE' => '%' . $cost . '%'];
            $cond[] = $contra;
        }

        // if ($datefrom !== '1970-01-01') {
        //     $contra = ['DATE(Contracts.contract_start_date) >=' => $datefrom];
        //     $cond[] = $contra;
        // }else if($dateto2 !== '1970-01-01'){
        //     $contra = ['DATE(Contracts.contract_end_date) <=' => $dateto2];
        //     $cond[] = $contra;
        // }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Contracts.contract_start_date) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Contracts.contract_end_date) <=' => $dateto2];
            $cond[] = $contra;
        }


        if ($reqdata) {
            $users = $this->Contracts->find('all')->where($cond)->order(['Contracts.id' => 'Desc']);
        } else {
            $users = $this->Contracts->find('all')->order(['Contracts.id' => 'desc']);
        }

        $users = $this->paginate($users)->toarray();
        $this->set('users', $users);
    }


    //add function in contracts
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Contracts');
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');

        if ($this->request->is(['post'], ['put'])) {
            // pr($this->request->data);die;


            $attnExist = $this->Contracts->exists(['title' => trim($this->request->data['title'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Contract already exists.'));
                return $this->redirect(['action' => 'Add']);
            }

            if ($this->request->data['vendor_id'] == "") {
                $this->Flash->error(__('Your entered supplier does not exists.'));
                return $this->redirect(['action' => 'add']);
            } else {
                $contr = $this->Contracts->newEntity();
                $data['supplier_id'] = $this->request->data['vendor_id'];
                $data['title'] = $this->request->data['title'];
                $data['workorder'] = $this->request->data['workorder'];
                $data['cost'] = $this->request->data['cost'];
                $data['operation_cost'] = $this->request->data['operation_cost'];
                $data['labour_cost'] = $this->request->data['labour_cost'];
                // $data['description'] = $this->request->data['description'];
                $data['description'] = $this->removeEmojis($this->request->data['description']);

                $data['contract_start_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['contract_start_date']));
                $data['contract_end_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['contract_end_date']));
                $data['issuedate'] = date('Y-m-d H:i:s', strtotime($this->request->data['issuedate']));
                $data['added_time'] = date('Y-m-d H:i:s');

                $contractdetail = $this->Contracts->patchEntity($contr, $data);

                // pr($contractdetail);
                // die;


                if ($purchasess = $this->Contracts->save($contractdetail)) {

                    $lstid = $purchasess->id;
                    $poerder['contract_id'] = $lstid;
                    $poerder['comment'] = $this->removeEmojis($this->request->data['description']);
                    $poerder['operation_cost'] = $this->request->data['operation_cost'];
                    $poerder['labour_cost'] = $this->request->data['labour_cost'];
                    $poerder['created'] = date('Y-m-d H:i:s', strtotime($this->request->data['issuedate']));
                    $newpo = $this->Bom->patchEntity($this->Bom->newEntity(), $poerder);
                    $this->Bom->save($newpo);

                    foreach ($this->request->data['finished_pro_itemid'] as $key => $value) {
                        $poerder1['contract_id'] = $lstid;
                        $poerder1['product_id'] = $value;
                        $poerder1['price'] = $this->request->data['price1'][$key];
                        $poerder1['quantity'] = $this->request->data['quantity1'][$key];
                        $newpo1 = $this->Bomfinishedproduct->patchEntity($this->Bomfinishedproduct->newEntity(), $poerder1);
                        $this->Bomfinishedproduct->save($newpo1);
                    }

                    $this->Flash->success(__('Contract added successfully.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }


   
    // edit function for contracts manager
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Contracts');
        $this->loadModel('Vendors');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bom');

        $users = $this->Contracts->get($id);
        $product = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.contract_id' => $id])->toarray();

        foreach ($product as $value) {
            $finishedproduct[] = $value['product_id'];
        }
        $this->set('finishedproduct');

        $bom = $this->Bom->find('all')->where(['Bom.contract_id' => $id])->first();
        $supplier_id = $users['supplier_id'];

        $supplier_name = $this->Vendors->find('all')->where(['Vendors.id' => $supplier_id])->first();
        $this->set(compact('supplier_name', 'users', 'product', 'finishedproduct'));

        if ($this->request->is(['post', 'put'])) {

            $attnExist = $this->Contracts->exists(['title' => trim($this->request->data['title']), 'NOT' => ['id' => $id]]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Contract already exists.'));
                return $this->redirect(['action' => 'Edit/', $id]);
            }

            if ($this->request->data['vendor_id'] == "") {
                $this->Flash->error(__('Your entered supplier does not exists.'));
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $data['supplier_id'] = $this->request->data['vendor_id'];
                $data['title'] = $this->request->data['title'];
                $data['workorder'] = $this->request->data['workorder'];
                $data['cost'] = $this->request->data['cost'];
                $data['operation_cost'] = $this->request->data['operation_cost'];
                $data['labour_cost'] = $this->request->data['labour_cost'];
                $data['description'] = $this->removeEmojis($this->request->data['description']);
                $data['contract_start_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['contract_start_date']));
                $data['contract_end_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['contract_end_date']));
                $data['issuedate'] = date('Y-m-d H:i:s', strtotime($this->request->data['issuedate']));
                $data['added_time'] = date('Y-m-d H:i:s');

                $contractdetail = $this->Contracts->patchEntity($users, $data);
                if ($purchasess = $this->Contracts->save($contractdetail)) {
                    $lstid = $purchasess->id;


                    $poerder['contract_id'] = $lstid;
                    $poerder['comment'] = $this->removeEmojis($this->request->data['description']);
                    $poerder['operation_cost'] = $this->request->data['operation_cost'];
                    $poerder['labour_cost'] = $this->request->data['labour_cost'];
                    // $newpo = $this->Bom->patchEntity($bom, $poerder);
                    // $this->Bom->save($newpo);


                    foreach ($this->request->data['finished_pro_itemid'] as $key => $value) {
                        $poerder1['contract_id'] = $lstid;
                        $poerder1['product_id'] = $value;
                        $poerder1['price'] = $this->request->data['price1'][$key];
                        $poerder1['quantity'] = $this->request->data['quantity1'][$key];
                        $newpo1 = $this->Bomfinishedproduct->patchEntity($this->Bomfinishedproduct->newEntity(), $poerder1);
                        $this->Bomfinishedproduct->save($newpo1);
                    }

                    $this->Flash->success(__('Contract successfully updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }




    // status in  contracts manager  
    public function status($id, $status)
    {
        $this->loadModel('Contracts');
        $user = $this->Contracts->get($id);
        $user->status = $status;
        if ($this->Contracts->save($user)) {
            $this->Flash->success(__('Contract status has been updated.'));
            return $this->redirect(['action' => 'index']);
        }
    }




    // delete additem data from index page
    public function delete($id = null)
    {
        $this->loadModel('Contracts');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bom');

        $contra = $this->Contracts->get($id);
        $user = $this->Bom->find('all')->where(['Bom.contract_id' => $id])->first();
        $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $id])->toarray();

        if ($this->Contracts->delete($contra)) {
            $this->Bom->delete($user);
            foreach ($finsheddetails as $value) {
                $this->Bomfinishedproduct->delete($value);
            }

            $this->Flash->success('Contract deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }




    // getname in  contracts manager 
    public function getname()
    {
        $this->loadModel('Vendor');
        $stsearch = $this->request->data['fetch'];
        $searchst = $this->Vendor->find('all')->where(['Vendor.name LIKE' => $stsearch . '%', 'Vendor.status' => 'Y'])->toarray();

        foreach ($searchst as $value) {
            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['name'] . '</a></li>';
        }
        die;
    }

    public function searchitem()
    {

        $this->loadModel('Contracts');
        $this->loadModel('Vendor');
        $reqdata = $_GET;

        $vendor_id = $reqdata['vendor_id'];
        $contract_id = $reqdata['contract_id'];
        $cost = trim($reqdata['cost']);
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));

        $cond = [];
        if (!empty($vendor_id)) {
            $contra = ['Contracts.supplier_id' => $vendor_id];
            $cond[] = $contra;
        }

        if (!empty($contract_id)) {
            $contra = ['Contracts.id' => $contract_id];
            $cond[] = $contra;
        }

        if (!empty($cost)) {
            $contra = ['Contracts.cost LIKE' => '%' . $cost . '%'];
            $cond[] = $contra;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Contracts.contract_start_date) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Contracts.contract_end_date) <=' => $dateto2];
            $cond[] = $contra;
        }

        $user = $this->Contracts->find('all')->where($cond)->order(['Contracts.id' => 'Desc']);
        $user = $this->paginate($user)->toarray();
        $this->set('users', $user);
    }


    public function viewcontractdetail($contraid)
    {

        $this->viewBuilder()->layout('admin');
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
        $this->loadModel('Taxmaster');


        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $finsheddetails = $this->Bomfinishedproduct->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Bomfinishedproduct.contract_id' => $contraid])->toarray();

        $designs = $this->Designsheet->find('all')->where(['Designsheet.contract_id' => $contraid])->group('item_id')->toarray();
        $designsheetitems = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.contract_id' => $contraid])->group('item_id')->toarray();

        $podetails = $this->Productionorder->find('all')->where(['Productionorder.contract_id' => $contraid])->order(['Productionorder.id ' => 'Desc'])->toarray();

        $processname = $this->Finishedprocess->find('all')->toarray();
        $this->set(compact('contractdetail', 'finsheddetails', 'designs', 'podetails', 'designsheetitems', 'processname'));
    }


    public function viewexpenditure($contraid)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheetdetails');

        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $designsheetitems = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.contract_id' => $contraid])->group('item_id')->toarray();
        $this->set(compact('contractdetail', 'designsheetitems'));
    }

    public function viewreverse($contraid)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Contracts');
        $this->loadModel('Designsheetdetails');

        $contractdetail = $this->Contracts->find('all')->where(['Contracts.id' => $contraid])->first();
        $designsheetitems = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.contract_id' => $contraid])->group('item_id')->toarray();
        $this->set(compact('contractdetail', 'designsheetitems'));
    }
}
