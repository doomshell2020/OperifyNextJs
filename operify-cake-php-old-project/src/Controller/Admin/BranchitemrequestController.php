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
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Client;
use Cake\Utility\Security;
use Cake\Validation\Validator;

class BranchitemrequestController extends AppController
{


  // public function connection($dbs)
  // {
  //   //echo $dbs; die;
  //   ConnectionManager::config($dbs, [
  //     'className' => 'Cake\Database\Connection',
  //     'driver' => 'Cake\Database\Driver\Mysql',
  //     'persistent' => false,
  //     'host' => 'localhost',
  //     'username' => 'tpplerp',
  //     'password' => 'tpplerp@23~',
  //     'database' => $dbs,
  //     'encoding' => 'utf8mb4',
  //     'timezone' => 'UTC',
  //     'cacheMetadata' => true,
  //   ]);
  //   ConnectionManager::drop('default');
  //   ConnectionManager::get($dbs);
  //   \Cake\Datasource\ConnectionManager::alias($dbs, 'default');
  // }

  public function index()
  {

    $this->viewBuilder()->layout('admin');
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    // $this->loadModel('Sitesettings');
    // $this->loadModel('SitesettingsDetails');
    // $sitesetting = $this->Sitesettings->find('all')->first();
    // $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();

    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $sitesetting = $this->Sitesettings->find('all')->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    // pr($sitesetting);
    // pr($site_details);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }

    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');



    if ($dbname != $branch[0]) {

      $branch_request = $this->Branchrequest->find('all')->where(['branch_name' => $dbname])->order(['Branchrequest.id' => 'desc']);
    } else {

      $branch_request = $this->Branchrequest->find('all')->order(['Branchrequest.id' => 'desc']);
    }
    // drop down
    $branches = $this->Branchrequest->find('all')->order(['Branchrequest.branch_name' => "Asc"])->group(['branch_name'])->toarray();

    // index page pegination add
    $branch_request = $this->paginate($branch_request)->toarray();

    //pr($branch_request);
    $this->set(compact('branch_request', 'sitesetting', 'site_details', 'branches'));
  }
  public function createmrn($id)
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('St_mrn');
    // $this->loadModel('Mrndetail');

    $mrn = $this->St_mrn->find('all')->order(['St_mrn.id' => 'desc'])->first();

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }

    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');



    $branch_request = $this->Branchrequest->find('all')->contain(['Branchrequestdetail' => ['Additem' => ['Taxmaster']]])->where(['id' => $id])->order(['Branchrequest.id' => 'desc'])->first();

    //pr($branch_request);
    $this->set(compact(['branch_request', 'sitesetting', 'site_details', 'mrn', 'id']));
  }

  public function viewmrn()
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('St_mrn');
    $st_mrn = $this->St_mrn->find('all')->order(['St_mrn.id' => 'Desc'])->toarray();
    $this->set('st_mrn', $st_mrn);
    // pr($categary); die;
  }

  public function mrnadd()
  {
    $this->viewBuilder()->layout('admin');

    $this->loadModel('St_mrn');
    if ($this->request->is(['post'], ['put'])) {
      //pr($this->request->data); die;
      $stmrn = $this->St_mrn->newEntity();
      $mrn_data['id'] = $this->request->data['mrn_no'];
      $mrn_data['mrn_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['mrn_date']));
      $mrn_data['bill_challan_no'] = $this->request->data['bill_challan_no'];
      $mrn_data['purchase_order_no'] = $this->request->data['purchase_order_no'];
      $mrn_data['suppliername'] = $this->request->data['suppliername'];
      $mrn_data['bill_challan_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['bill_challan_date']));
      $mrn_data['transport_charges'] = $this->request->data['transport_charges'];
      $mrn_data['other_charges'] = $this->request->data['other_charges'];
      $mrn_data['remark'] = $this->request->data['remark'];
      $mrn_data['bill_type'] = $this->request->data['bill_type'];

      $mrn_data['created'] = date('Y-m-d H:i:s');
      $pnewdetail = $this->St_mrn->patchEntity($stmrn, $mrn_data);
      $resustnew = $this->St_mrn->save($pnewdetail);

      if ($resustnew) {
        $this->loadModel('StockAvailable');
        $this->loadModel('Stockregister');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
          $this->connection(trim($branch[0]));
        }
        $this->loadModel('Branchrequest');
        $this->loadModel('Branchrequestdetail');
        //echo $this->request->data['branchrequest_id']; die;
        $requestdetails = $this->Branchrequestdetail->find('all')->contain(['Additem'])->where(['Branchrequestdetail.branchrequest_id' => $this->request->data['branchrequest_id']])->order(['Additem.item_name' => 'Asc'])->toarray();
        // pr($requestdetails); die;
        $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.store_type' => 0])->order(['Stockregister.id' => 'desc'])->first();

        foreach ($requestdetails as $intusr) {
          // pr($intusr); die;  
          $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
          //pr($item_data); die;
          $stock_data_id = $item_data['id'];
          $conn = ConnectionManager::get('default');
          $stock_back  = $item_data['stock_available'] + $intusr['item_qty'];
          $db = $this->request->session()->read('Auth.User.db');
          $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";

          $conn->execute($stock_update);

          // echo $stock_update; die;
          //pr($item_data_stock_reg); die;
          $po_id = $this->request->data['branchrequest_id'];
          $purchase_order_id =  $intusr['id'];
          $indent_id = $intusr['id'];
          $item_id = $intusr['additem']['id'];
          $issue_date = date('Y-m-d H:i:s');
          $delivery_date = date('Y-m-d H:i:s');
          $qty = $intusr['item_qty'];

          $rate = $intusr['item_amount'];
          $cost_price = $qty * $intusr['item_amount'];
          $tax_id = $intusr['additem']['tax'];
          $tax =  $cost_price * $intusr['item_tax'] / 100;
          $amount = sprintf('%.2f', $tax + $cost_price);
          $store_type = '1';

          $stock_register_entry = ConnectionManager::get('default');
          $db = $this->request->session()->read('Auth.User.db');
          $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
          $stock_register_entry->execute($stock_insert);
        }




        $this->Flash->success(__('MRN Generated successfully'));
        return $this->redirect(['action' => 'viewmrn']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'viewmrn']);
      }
    }
  }

  public function add()
  {
    $this->viewBuilder()->layout('admin');
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
    }

    $this->connection(trim($branch[0]));
    $this->loadModel('Tempbranchrequest');
    $this->loadModel('Itemcategory');
    $this->loadModel('Taxmaster');
    $this->loadModel('Categorywise');

    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');
    $this->loadModel('Additem');
    $this->loadModel('Taxmaster');

    $category = $this->Categorywise->find('all')->contain(['Itemcategory'])->group(['category_id'])->order(['Categorywise.id' => 'Desc'])->toarray();
    $this->set('categary', $category);

    $temp_item = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.group_type NOT IN' => ['Top', 'Bottom', 'Socks'], 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->toarray();



    $temp_item_top = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.group_type' => 'Top', 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->toarray();

    // pr($temp_item_top); die;
    $temp_item_bottom = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.group_type' => 'Bottom', 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->toarray();

    $temp_item_socks = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.group_type' => 'Socks', 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->toarray();
    //pr($temp_item_socks); die;

    $this->set(compact('temp_item'));
    $this->set(compact('temp_item_bottom'));
    $this->set(compact('temp_item_top'));
    $this->set(compact('temp_item_socks'));

    if ($this->request->is(['post'], ['put'])) {

      $temp_item = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.group_type NOT IN' => ['Top', 'Bottom', 'Socks'], 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->toarray();

      //validation temp item
      if ($temp_item) {
      } else {
        $this->Flash->error(__('Items not added'));
        return $this->redirect(['action' => 'add']);
      }
      $cat = $this->Branchrequest->newEntity();

      foreach ($temp_item as $key => $value) { //pr($value); //die;
        $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id' => $value['item_id']])->first();
        $total_amount +=  $item_data['sale_price'] * $value['quantity'];
      }
      $cat_item['branch_name'] = $dbname;
      $cat_item['totalamount'] = $total_amount;
      $cat_item['description'] = $this->request->data['description'];
      $cat_item['status'] = "Processing";
      $cat_item['created'] = date('Y-m-d H:i:s');
      $pnewdetail = $this->Branchrequest->patchEntity($cat, $cat_item);
      $branch_request_data = $this->Branchrequest->save($pnewdetail);
      if ($branch_request_data) {
        foreach ($temp_item as $key => $value) { //pr($value); die;
          $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id' => $value['item_id']])->first();
          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();
          if ($categorywiseitem_data) {
            $qty = $value['quantity'] * $categorywiseitem_data['quantity'];
          } else {
            $qty = $value['quantity'];
          }

          $branch_detail = $this->Branchrequestdetail->newEntity();
          $item_details['branchrequest_id'] = $branch_request_data->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_name'] = $value['category_id'];
          $item_details['item_qty'] = $qty;
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($item_data['taxmaster']['tax']) {
            $item_tax =  $item_data['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['item_amount'] = $item_data['sale_price'];


          if ($value['category_id'] == '1') {
            $discount = $value['additem']['discount'];
          } else {
            $discount = $categorywiseitem_data['discount'];
          }
          if ($value['category_id'] == '1') {

            $item_details['discount_type'] = "Amount";
          } else {
            $item_details['discount_type'] = $categorywiseitem_data['discount_type'];
          }
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];

          $branch_details = $this->Branchrequestdetail->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Branchrequestdetail->save($branch_details);
        }

        //top
        for ($i = 0; $i < count($this->request->data['top_product']); $i++) {
          $value = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.id ' => $this->request->data['top_product'][$i], 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->first();

          $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id' => $value['item_id']])->first();
          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();

          $branch_detail = $this->Branchrequestdetail->newEntity();

          $item_details['branchrequest_id'] = $branch_request_data->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_name'] = $value['category_id'];
          $item_details['item_qty'] =  1;
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($item_data['taxmaster']['tax']) {
            $item_tax =  $item_data['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['item_amount'] = $item_data['sale_price'];


          // if($value['additem']['discount']){
          //   $discount= $value['additem']['discount'];
          //  }else{
          $discount = $categorywiseitem_data['discount'];
          //  }
          $item_details['discount_type'] = $categorywiseitem_data['discount_type'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $branch_details = $this->Branchrequestdetail->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Branchrequestdetail->save($branch_details);
        }

        //bottom
        for ($i = 0; $i < count($this->request->data['bottom_product']); $i++) {
          $value = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.id' => $this->request->data['bottom_product'][$i], 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->first();
          $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id' => $value['item_id']])->first();
          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();
          $branch_detail = $this->Branchrequestdetail->newEntity();

          $item_details['branchrequest_id'] = $branch_request_data->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_name'] = $value['category_id'];
          $item_details['item_qty'] = 1;
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($item_data['taxmaster']['tax']) {
            $item_tax =  $item_data['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['item_amount'] = $item_data['sale_price'];


          // if($value['additem']['discount']){
          //   $discount= $value['additem']['discount'];
          //  }else{
          $discount = $categorywiseitem_data['discount'];
          //  }
          $item_details['discount_type'] = $categorywiseitem_data['discount_type'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];


          $branch_details = $this->Branchrequestdetail->patchEntity($branch_detail, $item_details);

          $branch_request = $this->Branchrequestdetail->save($branch_details);
        }


        //socks
        for ($i = 0; $i < count($this->request->data['socks_product']); $i++) {

          $value = $this->Tempbranchrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempbranchrequest.id' => $this->request->data['socks_product'][$i], 'Tempbranchrequest.branch_name' => $dbname])->order(['Tempbranchrequest.id' => 'Desc'])->first();

          $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id' => $value['item_id']])->first();


          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();
          $branch_detail = $this->Branchrequestdetail->newEntity();

          $item_details['branchrequest_id'] = $branch_request_data->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_name'] = $value['category_id'];
          $item_details['item_qty'] =  1;
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($item_data['taxmaster']['tax']) {
            $item_tax =  $item_data['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['item_amount'] = $item_data['sale_price'];


          // if($value['additem']['discount']){
          //   $discount= $value['additem']['discount'];
          //  }else{
          $discount = $categorywiseitem_data['discount'];
          // }
          $item_details['discount_type'] = $categorywiseitem_data['discount_type'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $branch_details = $this->Branchrequestdetail->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Branchrequestdetail->save($branch_details);
        }

        $this->Flash->success(__('Branch Item  request successfully saved.'));

        $this->Tempbranchrequest->deleteAll(['Tempbranchrequest.branch_name' => $dbname]);
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      }
    }
  }
  
  public function headofficerequest($id)
  {
    $this->set(compact('id'));
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    $this->connection(trim($branch[0]));
    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');

    $requestdetails = $this->Branchrequestdetail->find('all')->contain(['Additem'])->where(['branchrequest_id' => $id])->order(['Branchrequestdetail.id' => 'Desc'])->toarray();
    $this->set(compact('requestdetails'));
  }
  public function viewdetail($id)
  {
    $this->set(compact('id'));
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');
    $branch_request = $this->Branchrequest->find('all')->where(['id' => $id])->order(['Branchrequest.id' => 'Desc'])->first();
    $requestdetails = $this->Branchrequestdetail->find('all')->contain(['Additem', 'Branchrequest'])->where(['branchrequest_id' => $id])->order(['Additem.item_name' => 'Asc'])->toarray();
    //pr($requestdetails); die;
    $this->set(compact(['requestdetails', 'branch_request']));
  }

  public function mrngenerate($id)
  {
    $this->response->type('pdf');
    $this->loadModel('St_mrn');

    $mrn_request = $this->St_mrn->find('all')->where(['St_mrn.id' => $id])->first();

    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $sitesetting = $this->Sitesettings->find('all')->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Additem');

    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');
    $branch_request = $this->Branchrequest->find('all')->contain(['Branchrequestdetail' => ['Additem']])->where(['id' => $mrn_request['purchase_order_no']])->order(['Branchrequest.id' => 'Desc'])->first();

    // pr($branch_request); die;
    //  pr($site_details); die;
    $this->set(compact(['mrn_request', 'sitesetting', 'site_details', 'branch_request']));
  }

  public function billgenerate($id)
  {
    $this->response->type('pdf');

    // $branch_name = $this->request->data['branch_name'];
    // $dbname = $this->request->session()->read('Auth.User.db');
    // $branch = explode("_",$dbname);
    // if($dbname != $branch_nam){
    //   $this->connection($branch_name);
    // }
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Branchrequest');
    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $this->loadModel('Companymaster');
    $this->loadModel('Taxmaster');
    $this->loadModel('Additem');

    $tax_master = $this->Taxmaster->find('all')->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'Asc'])->toarray();

    $branch_request = $this->Branchrequest->find('all')->contain(['Branchrequestdetail' => ['Additem']])->where(['Branchrequest.id' => $id])->order(['Branchrequest.id' => 'asc'])->first();


    $sitesetting = $this->Sitesettings->find('all')->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    $company_master = $this->Companymaster->find('all')->where(['main_branch' => 'Y'])->order(['Companymaster.id' => 'DESC'])->first();
    //pr($company_master); die;


    $this->set(compact(['branch_request', 'sitesetting', 'site_details', 'company_master', 'tax_master', 'id']));
  }
  public function payamount()
  {
    $this->loadModel('Branchrequest');
    $this->loadModel('StockAvailable');
    $this->loadModel('Branchrequestdetail');
    $this->loadModel('Stockregister');
    $id = $this->request->data['id'];

    if ($this->request->is(['post'], ['put'])) {
      $pay_req = $this->Branchrequest->get($id);
      //pr($this->request->data); die;
      $pay_data['payamount'] = $this->request->data['pay_amount'];
      $pay_data['pay_date'] = $this->request->data['pay_date'];
      $pay_data['discount'] = $this->request->data['discount'];
      $pay_data['indent_no'] = $this->request->data['indent_no'];
      $pay_data['pay_remark'] = $this->request->data['pay_remark'];
      $pay_data['manual_receipt_no'] = $this->request->data['manual_receipt_no'];
      $pay_data['manual_receipt_date'] = $this->request->data['manual_receipt_date'];
      if (isset($this->request->data['bank_name'])) {
        $pay_data['mode_payment'] = $this->request->data['mode_pay'];
      } else {
        $pay_data['mode_payment'] = $this->request->data['mode_pay'];
      }

      $connsss = ConnectionManager::get('default');
      $status_update = "UPDATE `branchrequest` SET `status`='Approved' WHERE `id`='$id'";
      $connsss->execute($status_update);

      $entity = $this->Branchrequest->patchEntity($pay_req, $pay_data);
      $resustnew = $this->Branchrequest->save($entity);

      if ($resustnew) {

        $requestdetails = $this->Branchrequestdetail->find('all')->contain(['Additem', 'Itemcategory'])->where(['branchrequest_id' => $id])->order(['Additem.item_name' => 'Asc'])->toarray();
        foreach ($requestdetails as $intusr) {


          $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
          $stock_data_id = $item_data['id'];
          $conn = ConnectionManager::get('default');

          $stock_back  = $item_data['stock_available'] - $intusr['item_qty'];
          $sold_stock  = $item_data['sold_stock'] + $intusr['item_qty'];

          $stock_update = "UPDATE `st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
          $conn->execute($stock_update);

          $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id' => $intusr['additem']['id']])->first();
          //pr($item_data_stock_reg); die;
          $po_id = $id;
          $purchase_order_id =  $intusr['id'];
          $indent_id = $intusr['id'];
          $item_id = $intusr['additem']['id'];
          $issue_date = date('Y-m-d H:i:s');
          $delivery_date = date('Y-m-d H:i:s');
          $qty = $intusr['item_qty'];

          $rate = $intusr['item_amount'];
          $cost_price = $qty * $intusr['item_amount'];
          $tax_id = $intusr['additem']['tax'];
          $tax =  $cost_price * $intusr['item_tax'] / 100;
          $amount = sprintf('%.2f', $tax + $cost_price);
          $store_type = '2';

          $stock_register_entry = ConnectionManager::get('default');
          $stock_back  = $item_data['stock_available'] - $intusr['item_qty'];
          $db = $this->request->session()->read('Auth.User.db');
          $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
          $stock_register_entry->execute($stock_insert);
        }

        $this->Flash->success(__('payment has been submited suceessfully'));
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      }
    }
  }
  public function viewitemdetail($id)
  {
    $this->viewBuilder()->layout('admin');
    $this->set(compact('id'));
    $this->loadModel('SitesettingsDetails');
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Branchrequest');
    $this->loadModel('Branchrequestdetail');
    $this->loadModel('Itemcategory');
    $this->loadModel('Categorywise');

    $approve_req = $this->Branchrequest->get($id);
    //  pr($approve_req); die;
    $requestdetails = $this->Branchrequestdetail->find('all')->contain(['Additem', 'Itemcategory'])->where(['branchrequest_id' => $id])->order(['Additem.item_name' => 'Asc'])->toarray();
    $this->set(compact(['requestdetails', 'approve_req']));
    //pr($requestdetails); die;

    if ($this->request->is(['post'], ['put'])) {

      $stock_check_data  = 0;
      foreach ($requestdetails as $intusr) {
        //$item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$intusr['additem']['id']])->first();

        $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => 3])->first();

        $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
        $final_stock  = $avlstock - $sale_retrun['sum'];

        if ($final_stock <= "0") {
          $stock_check_data = 1;
        }
      }
      if ($stock_check_data == 1) {
        $this->Flash->error(__('Stock Item not Available  in this request'));
        return $this->redirect(['action' => 'viewitemdetail/' . $approve_req['id']]);
      }


      $this->request->data['remark'] = $this->request->data['description'];
      $this->request->data['approved_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['sale_date']));
      $this->request->data['customer_type'] = $this->request->data['customer_type'];
      $this->request->data['customer_mobile'] = $this->request->data['cmobile_no'];


      if ($this->request->data['customer_name']) {

        $this->request->data['customer_name'] = $this->request->data['customer_name'];
      } else {

        $this->Flash->error(__('Company details mandatory please add company details'));
        return $this->redirect(['action' => 'viewitemdetail/' . $approve_req['id']]);
      }

      if (isset($this->request->data['upload_description']['name']) && !empty($this->request->data['upload_description']['name'])) {

        $tmp_name = $this->request->data['upload_description']['tmp_name'];
        $image_name = $this->request->data['upload_description']['name'];
        $pext = pathinfo($image_name, PATHINFO_EXTENSION);
        $imagenewname = md5(time($filename)) . '.' . $pext;
        $dest = "storedata/canvas/";
        $newfile = $dest . $imagenewname;
        if (move_uploaded_file($tmp_name, $newfile)) {
          $this->request->data['remark_document'] = $imagenewname;
        }
      }
      $entity = $this->Branchrequest->patchEntity($approve_req, $this->request->data);
      // pr($entity); die; 
      $resustnew = $this->Branchrequest->save($entity);

      if ($resustnew) {
        $this->Flash->success(__('Request has been approved successfully'), ['key' => 'pay_request']);
        return $this->redirect(['action' => 'viewitemdetail/' . $approve_req['id']]);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'viewitemdetail/' . $approve_req['id']]);
      }
    }
  }
  public function categoryrequest()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Itemcategory');
    $this->loadModel('Tempbranchrequest');
    $this->loadModel('Additem');
    $this->loadModel('Categorywise');
    //cho "test"; die;
    if ($this->request->is(['post'], ['put'])) {
      //  pr($this->request->data); die;
      //$this->Tempbranchrequest->deleteAll();
      $this->Tempbranchrequest->deleteAll(['Tempbranchrequest.branch_name' => $dbname]);
      $cat_id = $this->request->data['category_id'];
      $session = $this->request->session();
      $session->write('category_id', $cat_id);
      $cat_qty = $this->request->data['category_qty'];
      $branch_name = $this->request->data['branch_name'];

      $category = $this->Categorywise->find('all')->where(['category_id' => $cat_id])->order(['Categorywise.id' => 'Desc'])->toarray();

      foreach ($category as $key => $value) {
        $item_data = $this->Additem->find('all')->where(['Additem.id' => $value['item_name']])->first();
        $group_type = $this->Categorywise->find('all')->where(['Categorywise.id' => $value['id']])->first();

        // $temp_group_request_check = $this->Tempbranchrequest->find('all')->where(['category_id' => $item_data['category_id'],'group_type'=>$group_type['group_type']])->first();
        // if(empty($temp_group_request_check)){
        $cat = $this->Tempbranchrequest->newEntity();
        $cat_item['category_id'] = $cat_id;
        $cat_item['item_id'] = $value['item_name'];
        $cat_item['category_name'] = $value['group_cat_name'];
        $cat_item['quantity'] = $cat_qty;
        $cat_item['branch_name'] = $branch_name;
        $cat_item['group_type'] = $group_type['group_type'];

        $pnewdetail = $this->Tempbranchrequest->patchEntity($cat, $cat_item);
        $resustnew = $this->Tempbranchrequest->save($pnewdetail);
        //}
      }
      if ($resustnew) {
        $this->Flash->success(__('Item has been added successfully'));
        echo "0";
        die;
      } else {
        $this->Flash->success(__('Somthing went wrong contact to administrator'));
        echo "1";
        die;
      }
    }
  }
  public function cancelrequest($id)
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Branchrequest');

    $cancel_req = $this->Branchrequest->get($id);
    // $this->set('cancel_req', $cancel_req);

    if ($this->request->is(['post', 'put'])) {

      $this->request->data['remark'] = $this->request->data['remark'];
      $this->request->data['status'] = "Cancel";


      $entity = $this->Branchrequest->patchEntity($cancel_req, $this->request->data);
      $resustnew = $this->Branchrequest->save($entity);

      if ($resustnew) {
        $this->Flash->success(__('Request has been cancelled'));
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      }
    }
  }
  public function delete($id)
  {
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempbranchrequest');
    $classes = $this->Tempbranchrequest->get($id);
    //delete pariticular entry
    try {
      if ($this->Tempbranchrequest->delete($classes)) {
        $this->Flash->success(__(' Class with id: {0} has been deleted.', h($id)));
        return $this->redirect(['action' => 'add']);
      }
    } catch (\PDOException $e) {
      $this->Flash->error(__(' You can not delete this record because it is used in another table.'));
      $this->set('error', $error);
      return $this->redirect(['action' => 'add']);
    }
  }
  // bottomitemdelete function
  public function bottomitemdelete($id)
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempbranchrequest');
    if ($this->Tempbranchrequest->deleteAll(['group_type' => 'Bottom', 'Tempbranchrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempbranchrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempbranchrequest Recored not  delete successfully');
      return $this->redirect(['action' => 'add']);
    }
  }

  // bottomitemdelete function
  public function topitemdelete()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempbranchrequest');
    if ($this->Tempbranchrequest->deleteAll(['group_type' => 'Top', 'Tempbranchrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempbranchrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempbranchrequest Recored not  delete successfully');
      return $this->redirect(['action' => 'add']);
    }
  }

  // socksitemdelete function
  public function socksitemdelete()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempbranchrequest');
    if ($this->Tempbranchrequest->deleteAll(['group_type' => 'Socks', 'Tempbranchrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempbranchrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempbranchrequest Recored not  delete successfully');
      return $this->redirect(['action' => 'add']);
    }
  }



  public function itemrequest()
  {


    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempbranchrequest');
    $this->loadModel('Itemcategory');
    $this->loadModel('Additem');
    if ($this->request->is(['post'], ['put'])) {
      // pr($this->request->data); die;
      $item_id = $this->request->data['item_id'];
      $cat_qty = $this->request->data['item_qty'];

      $items = $this->Additem->find('all')->where(['item_name' => $item_id])->first();
      $branch_name =  $this->request->session()->read('Auth.User.db');
      $cat = $this->Tempbranchrequest->newEntity();
      $cat_item['category_id'] = $items['category_id'];
      $cat_item['category_name'] = $items['itemcategory']['category_name'];
      $cat_item['item_id'] = $items['id'];
      $cat_item['quantity'] = $cat_qty;
      $cat_item['branch_name'] = $branch_name;
      $cat_item['group_type'] = "";
      $cat_item['discount_type'] = "Amount"; //$group_type['discount_type'];
      $cat_item['discount_amount'] = $items['discount'];
      $pnewdetail = $this->Tempbranchrequest->patchEntity($cat, $cat_item);
      $resustnew = $this->Tempbranchrequest->save($pnewdetail);


      if ($resustnew) {
        $this->Flash->success(__('Item has been added successfully'));
        echo "0";
        die;
      } else {
        $this->Flash->success(__('Somthing went wrong contact to administrator'));
        echo "1";
        die;
      }
    }
  }




  public function edit()
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Itemcategory');

    $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['group_id' => 1])->order(['Itemcategory.id' => 'asc'])->toarray();

    $this->set('categary', $categary);
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
    $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y'])->toarray();
    foreach ($searchst as $value) {


      echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
    }


    die;
  }


  public function searchreq()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $role_id = $this->request->session()->read('Auth.User.role_id');

    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Branchrequest');


    $item = $this->request->data['req_no'];
    $branchname = $this->request->data['branch_name'];

    $cond = [];
    if (isset($item) && $item != '') {
      $cond['Branchrequest.id'] = $item;
    }

    if (isset($branchname) && $branchname != '') {
      $cond['Branchrequest.branch_name'] = $branchname;
    }
    if ($role_id == "105") {
      $user = $this->Branchrequest->find('all')->where([$cond])->order(['Branchrequest.id' => 'Desc'])->toarray();
    } else {
      $user = $this->Branchrequest->find('all')->where(['branch_name' => $dbname, $cond])->order(['Branchrequest.id' => 'Desc'])->toarray();
    }

    $this->set('branch_request', $user);
  }
}
