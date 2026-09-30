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

class AdditemController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');
        $this->loadModel('Itemcategory');
        $this->loadModel('Sizemanager');
        $this->loadModel('Measurementunit');
        $this->loadModel('Taxmaster');
        $this->loadModel('Companymaster');
        $this->loadModel('Itemlocation');


        $reqdata = $_GET;
        $item = $reqdata['item_name'];
        $location = $reqdata['location_name'];
        $itemtype = $reqdata['itemtype'];
        $category = $reqdata['category_id'];
        $company = $reqdata['cname'];
        $cond = [];

        if (isset($item) && $item != '') {
            $cond['Additem.item_name LIKE'] = '%' . trim($item) . '%';
        }

        if (isset($location) && $location != '') {
            $cond['Additem.location_name'] = $location;
        }

        if (isset($itemtype) && $itemtype != '') {
            $cond['Additem.itemtype'] = $itemtype;
        }

        if (isset($category) && $category != '') {
            $cond['Additem.category_id'] = $category;
        }

        if (isset($company) && $company != '') {
            $cond['Additem.cname'] = $company;
        }


        $hasFilters = ($item || $location || $itemtype || $category || $company);

        if ($hasFilters) {
            $user = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where(['Additem.status' => 'Y', $cond])->order(['Additem.item_name' => 'asc']);
        } else {
            $user = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation'])->where(['Additem.status' => "Y", 'Additem.itemtype' => 'RawMaterial'])->order(['Additem.item_name' => 'asc']);
        }


        $user = $this->paginate($user)->toarray();
        $this->set('users', $user);

        $finishedprocess = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.id' => "Asc"])->toarray();
        $this->set('finishedprocess', $finishedprocess);

        $itemlocation = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->order(['Itemlocation.id' => "Asc"])->toarray();
        $this->set('item', $itemlocation);

        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->order(['Itemcategory.category_name' => 'asc'])->toarray();
        $this->set('categary', $categary);

        $company = $this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'cname'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => "Asc"])->toarray();
        $this->set('company', $company);
    }

    //add function in additem
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Additem');
        $this->loadModel('Finishedprocess');
        $this->loadModel('Itemcategory');
        $this->loadModel('Sizemanager');
        $this->loadModel('Measurementunit');
        $this->loadModel('Taxmaster');
        $this->loadModel('Itemlocation');
        $this->loadModel('Companymaster');



        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.status' => 'Y'])->order(['Itemcategory.category_name' => 'asc'])->toarray();
        $this->set('categary', $categary);

        $size = $this->Sizemanager->find('list', ['keyField' => 'id', 'valueField' => 'size_name'])->where(['Sizemanager.status' => 'Y'])->order(['Sizemanager.id' => 'asc'])->toarray();
        $this->set('size', $size);

        $finishedprocess = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.id' => "Asc"])->toarray();
        $this->set('finishedprocess', $finishedprocess);

        $unit = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->where(['Measurementunit.status' => 'Y'])->order(['Measurementunit.id' => 'asc'])->toarray();
        $this->set('units', $unit);

        $item = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.status' => 'Y', 'Itemlocation.parent' => 0])->order(['Itemlocation.id' => "Asc"])->toarray();
        $this->set('item', $item);

        $sub_location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.status' => 'Y', 'Itemlocation.parent NOT IN ' => 0])->order(['Itemlocation.id' => "Asc"])->toarray();
        $this->set('sub_location', $sub_location);
        // pr($sub_location);die;


        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => "Asc"])->toarray();
        $this->set('tax', $tax);

        $company = $this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'id'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => "Asc"])->toarray();
        // pr($company); die;
        $this->set('company', $company);
        if ($this->request->is(['post'], ['put'])) {

            $attnExist = $this->Additem->exists(['item_name' => trim($this->request->data['item_name'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Item already exists.'));
                return $this->redirect(['action' => 'Add']);
            }


            $cat = $this->Additem->newEntity();
            $item['item_name'] = strtoupper($this->request->data['item_name']);
            $item['category_id'] = $this->request->data['category_id'];
            $item['uom'] = $this->request->data['uom'];
            $item['size_id'] = $this->request->data['size'];
            $item['location_name'] = $this->request->data['location_name'];
            $item['sub_location'] = $this->request->data['sub_location'];
            $item['cname'] = $this->request->data['cname'];
            $item['tax'] = $this->request->data['tax'];
            $item['sale_price'] = ($this->request->data['sale_price'] - $this->request->data['discount']);
            $item['discount'] = $this->request->data['discount'];
            $item['item_isbn'] = $this->request->data['item_isbn'];
            $item['itemtype'] = $this->request->data['itemtype'];
            $item['weight'] = $this->request->data['weight'];
            $item['volume'] = $this->request->data['volume'];
            $item['min_order_qty'] = $this->request->data['min_order_qty'];
            $item['finishedprocess_id'] = $this->request->data['finishedprocess_id'];
            $item['productprocess_id'] = implode(',', $this->request->data['productprocess_id']);
            $item['added_time'] = date('Y-m-d H:i:s');
            $pnewdetail = $this->Additem->patchEntity($cat, $item);

            $this->Additem->save($pnewdetail);
            $this->Flash->success(__('Item added successfully updated.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    //find sub location ajax
    public function find_sublocation($id = null)
    {
        $location = $this->request->data['id'];
        $this->loadModel('Itemlocation');
        $this->viewBuilder()->layout('admin');
        $sublocation = $this->Itemlocation->find('list', [
            'keyField' => 'id',
            'valueField' => 'location_name',
        ])->where(['Itemlocation.parent' => $location])->order(['Itemlocation.location_name' => 'ASC'])->toArray();
        echo "<option value=''>Select Sub Location</option>";
        foreach ($sublocation as $sublocation => $value) {
            echo "<option value=" . $sublocation . ">" . $value . "</option>";
        }
        die;
    }




    // edit function for additem manager
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Additem');
        $this->loadModel('Itemcategory');
        $this->loadModel('Finishedprocess');
        $this->loadModel('Sizemanager');
        $this->loadModel('Measurementunit');
        $this->loadModel('Taxmaster');
        $this->loadModel('Companymaster');
        $this->loadModel('Itemlocation');
        $this->loadModel('Designsheetdetails');


        $addeditem = $this->Additem->find('all')->where(['Additem.id' => $id])->order(['Additem.id' => 'Asc'])->first();
        $categaryname = $this->Itemcategory->find('all')->where(['Itemcategory.id' => $addeditem['category_id']])->first();
        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->order(['Itemcategory.category_name' => 'asc'])->toarray();
        $size = $this->Sizemanager->find('list', ['keyField' => 'id', 'valueField' => 'size_name'])->order(['Sizemanager.id' => 'asc'])->toarray();
        $units = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->order(['Measurementunit.id' => 'asc'])->toarray();
        $company = $this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'id'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => "Asc"])->toarray();
        $addeditemtype =  $addeditem['itemtype'];
        $finishedprocess = $this->Finishedprocess->find('list', ['keyField' => 'id', 'valueField' => 'process_name'])->order(['Finishedprocess.id' => "Asc"])->toarray();
        $item = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.status' => 'Y', 'Itemlocation.parent' => 0])->order(['Itemlocation.id' => "Asc"])->toarray();
        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => "Asc"])->toarray();
        $sub_location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.status' => 'Y', 'Itemlocation.parent NOT IN ' => 0])->order(['Itemlocation.id' => "Asc"])->toarray();
        $chechdesignsheet = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.item_id' => $id])->count();

        $this->set(compact('addeditem', 'categaryname', 'categary', 'size', 'units', 'company', 'addeditemtype', 'item', 'tax', 'sub_location', 'chechdesignsheet'));
        $cat = $this->Additem->get($id);
        $conn = ConnectionManager::get('default');
        if ($this->request->is(['put'])) {

            $attnExist = $this->Additem->exists(['item_name' => trim($this->request->data['item_name']), 'NOT' => ['id' => $id]]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Item already exists.'));
                return $this->redirect(['action' => 'Edit/', $id]);
            }


            $item['item_name'] = strtoupper($this->request->data['item_name']);
            $item['category_id'] = $this->request->data['category_id'];
            $item['uom'] = $this->request->data['uom'];
            $item['size_id'] = $this->request->data['size_id'];
            $item['location_name'] = $this->request->data['location_name'];
            $item['sub_location'] = $this->request->data['sub_location'];
            $item['cname'] = $this->request->data['cname'];
            $item['tax'] = $this->request->data['tax'];
            $item['sale_price'] = $this->request->data['sale_price'];
            $item['discount'] = $this->request->data['discount'];
            $item['item_isbn'] = $this->request->data['item_isbn'];
            $item['itemtype'] = $this->request->data['itemtype'];
            $item['consumble'] = $this->request->data['consumble'];
            $item['finishedprocess_id'] = $this->request->data['finishedprocess_id'];
            $item['productprocess_id'] = implode(',', $this->request->data['productprocess_id']);
            $item['updated_time'] = date('Y-m-d H:i:s');
            $cats = $this->Additem->patchEntity($cat, $item);
            if ($resust = $this->Additem->save($cats)) {
                $this->Flash->success(__('Item name successfully updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    public function delete($id = null)
    {
        $this->loadModel('Additem');
        $this->loadModel('PurchaseorderDetails');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Designsheetdetails');

        $checkpo = $this->PurchaseorderDetails->find('all')->where(['PurchaseorderDetails.item_id' => $id])->count();
        $checkcontract = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.product_id' => $id])->count();
        $chechdesignsheet = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.item_id' => $id])->count();
        $totalcount = $checkpo +  $checkcontract + $chechdesignsheet;

        if ($totalcount > 0) {
            $this->Flash->error('This Item Can not be Deleted as it is already used in the system');
            return $this->redirect(['action' => 'index']);
        } else {
            $res = $this->Additem->get($id);
            if ($this->Additem->Delete($res)) {
                $this->Flash->success('item has been deleted successfully');
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function searchitem()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemlocation');
        $this->loadModel('Itemcategory');

        $reqdata = $_GET;
        $item = $reqdata['item_name'];
        $location = $reqdata['location_name'];
        $itemtype = $reqdata['itemtype'];
        $category = $reqdata['category_id'];
        $company = $reqdata['cname'];
        $status = $reqdata['status'];

        $cond = [];
        if (isset($item) && $item != '') {
            $cond['Additem.item_name LIKE'] = '%' . trim($item) . '%';
        }
        if (isset($location) && $location != '') {

            $cond['Additem.location_name'] = $location;
        }
        if (isset($itemtype) && $itemtype != '') {
            $cond['Additem.itemtype'] = $itemtype;
        }
        if (isset($category) && $category != '') {
            $cond['Additem.category_id'] = $category;
        }

        if (isset($company) && $company != '') {
            $cond['Additem.cname'] = $company;
        }
        if (isset($status) && $status != '') {
            $cond['Additem.status'] = $status;
        }


        $this->request->session()->write('cond', $cond);
        $user = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where([$cond])->order(['Additem.item_name' => 'asc']);
        $user = $this->paginate($user)->toarray();
        $this->set('users', $user);
    }


    // public function view()
    // {
    //     $this->loadModel('Additem');
    //     $this->loadModel('Itemname');
    //     $this->loadModel('Itemcategory');
    //     $this->loadModel('Vendors');

    //     $where = $this->request->session()->read('cond');
    //     if (isset($where)) {
    //         $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where([$where])->order(['Additem.item_name' => 'asc'])->toarray();
    //         $this->request->session()->delete('cond');
    //     } else {
    //         $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where(['Additem.status' => "Y", 'Additem.itemtype' => 'RawMaterial'])->order(['Additem.item_name' => 'asc'])->toarray();
    //     }
    //     $this->set(compact('users'));
    //     $this->response->type('pdf');
    // }



    public function viewitemexcel()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemname');
        $this->loadModel('Itemcategory');
        $this->loadModel('Vendors');


        $where = $this->request->session()->read('cond');
        if (isset($where)) {
            $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where([$where])->order(['Additem.category_id' => 'asc'])->toarray();
            $this->request->session()->delete('cond');
        } else {
            $users = $this->Additem->find('all')->contain(['Itemcategory', 'Measurementunit'])->where(['Additem.itemtype' => 'RawMaterial', 'Additem.status' => 'Y'])->order(['Additem.category_id' => 'desc', 'Additem.item_name' => 'asc'])->toarray();
        }
        $this->set(compact('users'));
    }

    public function getitemdetail()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch']])->first();

        echo json_encode($unitid);
        die;
    }


    public function getitemname()
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {

            $this->connection(trim($branch[0]));
        }

        $this->loadModel('Itemcategory');
        $this->loadModel('Additem');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y', 'Additem.itemtype' => 'RawMaterial'])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
        }

        die;
    }

    public function status($id, $status)
    {
        $this->loadModel('Additem');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Additem->get($id);
                $user->status = $status;
                if ($this->Additem->save($user)) {
                    $this->Flash->error(__('The product has been deactivated successfully.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Additem->get($id);
                $user->status = $status;
                if ($this->Additem->save($user)) {
                    $this->Flash->success(__('The product has been activated successfully.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }
}
