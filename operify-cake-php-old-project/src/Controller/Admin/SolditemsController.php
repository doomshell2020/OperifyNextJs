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

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class SolditemsController extends AppController
{


  public function toprequest_check()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
    }
    $this->loadModel('StockAvailable');

    $this->loadModel('Stockregister');
    $this->connection(trim($branch[0]));

    $this->loadModel('Categorywise');
    // pr($this->request->data);
    $categ_name = $this->request->data['categ_name'];
    $item_name = $this->request->data['item_name'];

    //echo $categ_name;
    // echo $item_name;
    $top_item_check = array();
    $category = $this->Categorywise->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Categorywise.category_id' => $categ_name, 'Categorywise.item_name' => $item_name])->order(['Categorywise.id' => 'Desc'])->first();
    //$stock_register = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $category['additem']['id']])->first();
    //pr($stock_register);

    $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

    $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => '2'])->first();

    $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => 3])->first();

    $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
    $final_stock  = $avlstock - $sale_retrun['sum'];

    $top_item_check['stockavl'] = $final_stock;
    $top_item_check['saleprice'] = $category['additem']['sale_price'];
    $top_item_check['discount'] = $category['discount'];
    $top_item_check['tax'] = $category['additem']['taxmaster']['tax'];
    //pr($top_item_check);
    echo json_encode($top_item_check);
    die;
  }

  public function bottomrequest_check()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
    }
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');
    $this->connection(trim($branch[0]));

    $this->loadModel('Categorywise');
    // pr($this->request->data);
    $categ_name = $this->request->data['categ_name'];
    $item_name = $this->request->data['item_name'];

    //echo $categ_name;
    // echo $item_name;
    $bottom_item_check = array();
    $category = $this->Categorywise->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Categorywise.category_id' => $categ_name, 'Categorywise.item_name' => $item_name])->order(['Categorywise.id' => 'Desc'])->first();
    //pr($category);
    // $stock_register = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $category['additem']['id']])->first();

    $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

    $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => '2'])->first();

    $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => 3])->first();

    $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
    $final_stock  = $avlstock - $sale_retrun['sum'];

    $bottom_item_check['stockavl'] = $final_stock;
    $bottom_item_check['saleprice'] = $category['additem']['sale_price'];
    $bottom_item_check['discount'] = $category['discount'];
    $bottom_item_check['tax'] = $category['additem']['taxmaster']['tax'];
    echo json_encode($bottom_item_check);
    die;
  }

  public function socksrequest_check()
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
    }
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');
    $this->connection(trim($branch[0]));

    $this->loadModel('Categorywise');
    // pr($this->request->data);
    $categ_name = $this->request->data['categ_name'];
    $item_name = $this->request->data['item_name'];

    //echo $categ_name;
    // echo $item_name;
    $socks_item_check = array();
    $category = $this->Categorywise->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Categorywise.category_id' => $categ_name, 'Categorywise.item_name' => $item_name])->order(['Categorywise.id' => 'Desc'])->first();

    //$stock_register = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $category['additem']['id']])->first();

    $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

    $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => '2'])->first();

    $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $category['additem']['id'], 'Stockregister.store_type' => 3])->first();

    $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
    $final_stock  = $avlstock - $sale_retrun['sum'];


    $socks_item_check['stockavl'] = $final_stock;
    $socks_item_check['saleprice'] = $category['additem']['sale_price'];
    $socks_item_check['discount'] = $category['discount'];
    $socks_item_check['tax'] = $category['additem']['taxmaster']['tax'];
    echo json_encode($socks_item_check);
    die;
  }


  public function index()
  {

    $this->viewBuilder()->layout('admin');
    $this->loadModel('Itemcategory');
    $this->loadModel('Additem');
    $this->loadModel('Tempsolditemsrequest');
    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');
    $this->loadModel('Students');

    $sold_item = $this->Solditem->find('all')->contain(['Students'])->order(['Solditem.id' => 'Desc'])->toarray();
    //pr($sold_item); die;
    $this->set('sold_item', $sold_item);
  }

  public function add()
  {
    $this->viewBuilder()->layout('admin');


    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }



    $this->loadModel('Categorywise');
    $this->loadModel('Tempsolditemsrequest');
    $this->loadModel('Additem');
    $this->loadModel('Itemcategory');
    $this->loadModel('Taxmaster');



    $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['category_name NOT IN' => 'School Item'])->order(['Itemcategory.id' => 'asc'])->toarray();
    $this->set('categary', $categary);

    $temp_item = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.group_type NOT IN' => ['Top', 'Bottom', 'Socks'], 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();


    $temp_item_top = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.group_type' => 'Top', 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();
    //  pr($temp_item_top); die;
    $temp_item_bottom = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.group_type' => 'Bottom', 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();

    $temp_item_socks = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.group_type' => 'Socks', 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();
    //pr($temp_item_socks); die;

    $this->set(compact('temp_item'));
    $this->set(compact('temp_item_bottom'));
    $this->set(compact('temp_item_top'));
    $this->set(compact('temp_item_socks'));

    foreach ($temp_item as $key => $intusr) { //pr($value); die;

      if ($intusr['discount_amount']) {
        $discount = $intusr['discount_amount'] * $intusr['quantity'];
      } else {
        $discount = 0;
      }
      if ($intusr['additem']['taxmaster']['tax']) {
        $tax = $intusr['additem']['taxmaster']['tax'];
      } else {
        $tax = 0;
      }
      $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
      $total_tax = $total * $tax / 100;

      $total_amount += $total + $total_tax;
    }
    //pr($total_amount); die;
    $this->set(compact('temp_item'));

    if ($this->request->is(['post'])) {


      $stock_check_data  = 0;
      foreach ($temp_item as $intusr) {

        $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => 3])->first();

        $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
        $final_stock  = $avlstock - $sale_retrun['sum'];
        //$item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
        if ($final_stock <= "0") {
          $stock_check_data = 1;
        }
      }
      if ($stock_check_data == 1) {
        $this->Flash->error(__('Stock Item not Available  in this request'));
        return $this->redirect(['action' => 'add']);
      }


      $top_select_prod = implode(",", $this->request->data['top_product']);
      $bottom_select_prod = implode(",", $this->request->data['bottom_product']);
      $socks_select_prod = implode(",", $this->request->data['socks_product']);
      $temp_item_top = '';
      $temp_item_bottom = '';
      // echo ($top_select_prod); die;
      if ($top_select_prod) {
        $temp_item_top = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => explode(",", $top_select_prod), 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();
      }
      if ($top_select_prod) {
        $temp_item_bottom = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => explode(",", $bottom_select_prod), 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();
      }

      if ($socks_select_prod) {
        $temp_item_socks = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => explode(",", $socks_select_prod), 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();
      }
      //tempitem_topstock
      $stock_check_data  = 0;
      foreach ($temp_item_top as $intusr) {
        $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => 3])->first();

        $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
        $final_stock  = $avlstock - $sale_retrun['sum'];

        //$item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
        if ($final_stock <= "0") {
          $stock_check_data = 1;
        }
      }
      if ($stock_check_data == 1) {
        $this->Flash->error(__('Stock Item not Available  in this request'));
        return $this->redirect(['action' => 'add']);
      }

      //tempitem_bottom
      $stock_check_data  = 0;
      foreach ($temp_item_bottom as $intusr) {

        $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => 3])->first();

        $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
        $final_stock  = $avlstock - $sale_retrun['sum'];

        //$item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
        if ($final_stock <= "0") {
          $stock_check_data = 1;
        }
      }
      if ($stock_check_data == 1) {
        $this->Flash->error(__('Stock Item not Available  in this request'));
        return $this->redirect(['action' => 'add']);
      }

      //tempitem_socks
      $stock_check_data  = 0;
      foreach ($temp_item_socks as $intusr) {

        $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['additem']['id'], 'Stockregister.store_type' => 3])->first();

        $avlstock  = $added_stock['sum'] -  $sold_stock['sum'];
        $final_stock  = $avlstock - $sale_retrun['sum'];

        //$item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $intusr['additem']['id']])->first();
        if ($final_stock <= "0") {
          $stock_check_data = 1;
        }
      }
      if ($stock_check_data == 1) {
        $this->Flash->error(__('Stock Item not Available  in this request'));
        return $this->redirect(['action' => 'add']);
      }



      //top
      foreach ($temp_item_top as $key => $intusr) { //pr($value); die;
        if ($intusr['discount_amount']) {
          $discount = $intusr['discount_amount'] * $intusr['quantity'];
        } else {
          $discount = 0;
        }
        if ($intusr['additem']['taxmaster']['tax']) {
          $tax = $intusr['additem']['taxmaster']['tax'];
        } else {
          $tax = 0;
        }
        $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
        $total_tax = $total * $tax / 100;
        $total_amount_top += $total + $total_tax;
      }

      //Bottom
      foreach ($temp_item_bottom as $key => $intusr) { //pr($value); die;
        if ($intusr['discount_amount']) {
          $discount = $intusr['discount_amount'] * $intusr['quantity'];
        } else {
          $discount = 0;
        }
        if ($intusr['additem']['taxmaster']['tax']) {
          $tax = $intusr['additem']['taxmaster']['tax'];
        } else {
          $tax = 0;
        }
        $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
        $total_tax = $total * $tax / 100;
        $total_amount_bottom += $total + $total_tax;
      }
      //Socks
      foreach ($temp_item_socks as $key => $intusr) { //pr($value); die;
        if ($intusr['discount_amount']) {
          $discount = $intusr['discount_amount'] * $intusr['quantity'];
        } else {
          $discount = 0;
        }
        if ($intusr['additem']['taxmaster']['tax']) {
          $tax = $intusr['additem']['taxmaster']['tax'];
        } else {
          $tax = 0;
        }
        $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
        $total_tax = $total * $tax / 100;
        $total_amount_socks += $total + $total_tax;
      }
      $final_amt = $total_amount + $total_amount_top + $total_amount_bottom + $total_amount_socks;

      $this->set('total_amount', $final_amt);
      $_SESSION['soldItem']['final_amt'] = $final_amt;
      $_SESSION['soldItem']['customer_name'] = $this->request->data['name'];
      $_SESSION['soldItem']['customer_id'] = $this->request->data['stu_name'];
      $_SESSION['soldItem']['saledate'] = date('Y-m-d H:i:s', strtotime($this->request->data['sale_date']));
      $_SESSION['soldItem']['description'] = $this->request->data['description'];
      $_SESSION['soldItem']['top_product'] = $this->request->data['top_product'];
      $_SESSION['soldItem']['bottom_product'] = $this->request->data['bottom_product'];
      $_SESSION['soldItem']['socks_product'] = $this->request->data['socks_product'];
      $this->Flash->success(__('Request has been approved successfully'), ['key' => 'pay_request']);
      return $this->redirect(['action' => 'add']);
    }
  }


  public function payamount()
  {

    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }

    $this->loadModel('Itemcategory');
    $this->loadModel('Additem');
    $this->loadModel('Tempsolditemsrequest');
    $this->loadModel('Categorywise');

    // pr($categary); die;
    $temp_item = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.group_type NOT IN' => ['Top', 'Bottom', 'Socks'], 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->toarray();

    foreach ($temp_item as $key => $intusr) { //pr($value); die;
      if ($intusr['discount_amount']) {
        $discount = $intusr['discount_amount'] * $intusr['quantity'];
      } else {
        $discount = 0;
      }
      if ($intusr['additem']['taxmaster']['tax']) {
        $tax = $intusr['additem']['taxmaster']['tax'];
      } else {
        $tax = 0;
      }
      $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
      $total_tax = $total * $tax / 100;
      $total_amount += $total + $total_tax;
    }

    if ($this->request->is(['post'], ['put'])) {
      //pr($this->request->data); die;
      // pr($_SESSION); die;
      $cat = $this->Solditem->newEntity();
      $sold_data['payamount'] = $this->request->data['pay_amount'];
      $sold_data['pay_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['pay_date']));
      $sold_data['discount'] = $this->request->data['discount'];
      $sold_data['indent_no'] = $this->request->data['indent_no'];
      $sold_data['pay_remark'] = $this->request->data['pay_remark'];
      $sold_data['manual_receipt_no'] = $this->request->data['manual_receipt_no'];
      $sold_data['manual_receipt_date'] =  date('Y-m-d H:i:s', strtotime($this->request->data['manual_receipt_date']));
      if (isset($this->request->data['bank_name']) && !empty($this->request->data['bank_name'])) {
        $sold_data['mode_payment'] = $this->request->data['mode_pay'];
        $sold_data['bank_name'] = $this->request->data['bank_name'];
        $sold_data['bankbranch_name'] = $this->request->data['bankbranch_name'];
        $sold_data['chequeno'] = $this->request->data['chequeno'];
        $sold_data['cheque_date'] = $this->request->data['cheque_date'];
      } else {
        $sold_data['mode_payment'] = $this->request->data['mode_pay'];

        $sold_data['bank_name'] = '';
        $sold_data['bankbranch_name'] = '';
        $sold_data['chequeno'] = '';
        $sold_data['cheque_date'] = '';
      }


      $sold_data['customer_name'] = $this->request->data['name'];
      $sold_data['student_id'] = $_SESSION['soldItem']['customer_id'];
      $sold_data['customer_type'] = "Student";
      $sold_data['totalamount'] = $_SESSION['soldItem']['final_amt'];
      $sold_data['created'] = date('Y-m-d H:i:s');
      $sold_data['saledate'] =  date('Y-m-d H:i:s', strtotime($this->request->data['saledate']));

      $sold_data['status'] = "Approved";
      $sold_data['description'] = $this->request->data['description'];
      $sold_data['other_amt'] = $this->request->data['other_amt'];



      $pnewdetail = $this->Solditem->patchEntity($cat, $sold_data);
      //  pr($pnewdetail); die;
      $resustnew = $this->Solditem->save($pnewdetail);
      $student_id = $_SESSION['soldItem']['customer_id'];
      if ($resustnew) {
        foreach ($temp_item as $key => $value) { //pr($value); die;
          $branch_detail = $this->Solditemdetails->newEntity();

          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();


          // if($categorywiseitem_data){
          //   $qty= $value['quantity']*$categorywiseitem_data['quantity'];
          //  }else{
          $qty = $value['quantity'];
          //  }


          $item_details['sold_id'] = $resustnew->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_id'] = $value['category_id'];
          $item_details['item_qty'] = $qty;
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($value['additem']['taxmaster']['tax']) {
            $item_tax = $value['additem']['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['created'] = date('Y-m-d H:i:s');
          $discount = $categorywiseitem_data['discount'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $item_details['item_name'] = $value['additem']['item_name'];
          $branch_details = $this->Solditemdetails->patchEntity($branch_detail, $item_details);
          //  pr($branch_details);
          $branch_request = $this->Solditemdetails->save($branch_details);
          if ($branch_request) {
            $item_id = $value['item_id'];
            $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $item_id])->first();
            //  pr($item_data);
            $stock_data_id = $item_data['id'];
            $conn = ConnectionManager::get('default');
            $db = $this->request->session()->read('Auth.User.db');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $sold_stock  = $item_data['sold_stock'] + $branch_request['item_qty'];
            $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
            $conn->execute($stock_update);


            $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id' => $item_id])->first();

            //pr($item_data_stock_reg); die;
            // $po_id = $item_data_stock_reg['po_id'];
            // $purchase_order_id =  $item_data_stock_reg['purchaseorder_id'];
            // $indent_id = $item_data_stock_reg['indent_id'];
            $po_id =  $resustnew->id;
            $purchase_order_id =  $branch_request['id'];
            $indent_id = $branch_request['id'];
            $item_id = $item_id;
            $issue_date = date('Y-m-d H:i:s');
            $delivery_date = date('Y-m-d H:i:s');
            $qty = $branch_request['item_qty'];

            $rate = $branch_request['item_amount'];
            $cost_price = $qty * $branch_request['item_amount'];
            $tax_id = $branch_request['item_tax'];
            $tax =  $cost_price * $branch_request['item_tax'] / 100;
            $amount = sprintf('%.2f', $tax + $cost_price);
            $store_type = '2';

            $stock_register_entry = ConnectionManager::get('default');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $db = $this->request->session()->read('Auth.User.db');
            $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_id')";
            $stock_register_entry->execute($stock_insert);
          }
        }

        //top
        for ($i = 0; $i < count($_SESSION['soldItem']['top_product']); $i++) {
          //foreach ($temp_item_top as $key => $value) { //pr($value); die;
          $branch_detail = $this->Solditemdetails->newEntity();

          $value = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => $_SESSION['soldItem']['top_product'][$i], 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->first();

          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();

          $item_details['sold_id'] = $resustnew->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_id'] = $value['category_id'];
          $item_details['item_qty'] = $value['quantity'];
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($value['additem']['taxmaster']['tax']) {
            $item_tax = $value['additem']['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['created'] = date('Y-m-d H:i:s');
          $discount = $categorywiseitem_data['discount'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $item_details['item_name'] = $value['additem']['item_name'];
          $branch_details = $this->Solditemdetails->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Solditemdetails->save($branch_details);
          if ($branch_request) {
            //pr($branch_request); die;
            $item_id = $branch_request->item_id;
            $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $item_id])->first();
            //  pr($item_data);
            $stock_data_id = $item_data['id'];
            $conn = ConnectionManager::get('default');
            $db = $this->request->session()->read('Auth.User.db');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $sold_stock  = $item_data['sold_stock'] + $branch_request['item_qty'];
            $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
            // $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";
            // $conn->execute($stock_update);


            $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id' => $item_id])->first();

            //pr($item_data_stock_reg); die;
            $po_id =  $resustnew->id;
            $purchase_order_id =  $branch_request['id'];
            $indent_id = $branch_request['id'];
            // $po_id = $item_data_stock_reg['po_id'];
            // $purchase_order_id =  $item_data_stock_reg['purchaseorder_id'];
            // $indent_id = $item_data_stock_reg['indent_id'];
            $item_id = $item_id;
            $issue_date = date('Y-m-d H:i:s');
            $delivery_date = date('Y-m-d H:i:s');
            $qty = $branch_request['item_qty'];

            $rate = $branch_request['item_amount'];
            $cost_price = $qty * $branch_request['item_amount'];
            $tax_id = $branch_request['item_tax'];
            $tax =  $cost_price * $branch_request['item_tax'] / 100;
            $amount = sprintf('%.2f', $tax + $cost_price);
            $store_type = '2';

            $stock_register_entry = ConnectionManager::get('default');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $db = $this->request->session()->read('Auth.User.db');
            $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_id')";
            $stock_register_entry->execute($stock_insert);
          }
        }

        //bottom
        for ($i = 0; $i < count($_SESSION['soldItem']['bottom_product']); $i++) {
          //foreach ($temp_item_bottom as $key => $value) { //pr($value); die;
          $branch_detail = $this->Solditemdetails->newEntity();

          $value = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => $_SESSION['soldItem']['bottom_product'][$i], 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->first();

          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();

          $item_details['sold_id'] = $resustnew->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_id'] = $value['category_id'];
          $item_details['item_qty'] = $value['quantity'];
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($value['additem']['taxmaster']['tax']) {
            $item_tax = $value['additem']['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['created'] = date('Y-m-d H:i:s');
          $discount = $categorywiseitem_data['discount'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $item_details['item_name'] = $value['additem']['item_name'];
          $branch_details = $this->Solditemdetails->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Solditemdetails->save($branch_details);

          if ($branch_request) {
            $item_id = $branch_request->item_id;
            $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $item_id])->first();
            //  pr($item_data);
            $stock_data_id = $item_data['id'];
            $conn = ConnectionManager::get('default');
            $db = $this->request->session()->read('Auth.User.db');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $sold_stock  = $item_data['sold_stock'] + $branch_request['item_qty'];
            $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";


            $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id' => $item_id])->first();

            //pr($item_data_stock_reg); die;
            // $po_id = $item_data_stock_reg['po_id'];
            // $purchase_order_id =  $item_data_stock_reg['purchaseorder_id'];
            // $indent_id = $item_data_stock_reg['indent_id'];
            $po_id =  $resustnew->id;
            $purchase_order_id =  $branch_request['id'];
            $indent_id = $branch_request['id'];
            $item_id = $item_id;
            $issue_date = date('Y-m-d H:i:s');
            $delivery_date = date('Y-m-d H:i:s');
            $qty = $branch_request['item_qty'];

            $rate = $branch_request['item_amount'];
            $cost_price = $qty * $branch_request['item_amount'];
            $tax_id = $branch_request['item_tax'];
            $tax =  $cost_price * $branch_request['item_tax'] / 100;
            $amount = sprintf('%.2f', $tax + $cost_price);
            $store_type = '2';

            $stock_register_entry = ConnectionManager::get('default');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $db = $this->request->session()->read('Auth.User.db');
            $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_id')";
            $stock_register_entry->execute($stock_insert);
          }
        }

        //socks
        for ($i = 0; $i < count($_SESSION['soldItem']['socks_product']); $i++) {
          //foreach ($temp_item_socks as $key => $value) { //pr($value); die;
          $branch_detail = $this->Solditemdetails->newEntity();

          $value = $this->Tempsolditemsrequest->find('all')->contain(['Additem' => ['Taxmaster'], 'Itemcategory'])->where(['Tempsolditemsrequest.id IN' => $_SESSION['soldItem']['socks_product'][$i], 'Tempsolditemsrequest.branch_name' => $dbname])->order(['Tempsolditemsrequest.id' => 'Desc'])->first();

          $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name' => $value['item_id'], 'Categorywise.category_id' => $value['category_id']])->first();

          $item_details['sold_id'] = $resustnew->id;
          $item_details['item_id'] = $value['item_id'];
          $item_details['category_id'] = $value['category_id'];
          $item_details['item_qty'] = $value['quantity'];
          $item_details['hsncode'] = $value['additem']['item_isbn'];
          if ($value['additem']['taxmaster']['tax']) {
            $item_tax = $value['additem']['taxmaster']['tax'];
          } else {
            $item_tax = 0;
          }
          $item_details['item_tax'] = $item_tax;
          $item_details['created'] = date('Y-m-d H:i:s');
          $discount = $categorywiseitem_data['discount'];
          $item_details['discount'] = $discount;
          $item_details['item_amount'] = $value['additem']['sale_price'];
          $item_details['item_name'] = $value['additem']['item_name'];
          $branch_details = $this->Solditemdetails->patchEntity($branch_detail, $item_details);
          $branch_request = $this->Solditemdetails->save($branch_details);

          if ($branch_request) {
            $item_id = $branch_request->item_id;
            $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $item_id])->first();
            //  pr($item_data);
            $stock_data_id = $item_data['id'];
            $conn = ConnectionManager::get('default');
            $db = $this->request->session()->read('Auth.User.db');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $sold_stock  = $item_data['sold_stock'] + $branch_request['item_qty'];
            $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
            // $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";
            // $conn->execute($stock_update);
            $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id' => $item_id])->first();

            // //pr($item_data_stock_reg); die;
            // $po_id = $item_data_stock_reg['po_id'];
            // $purchase_order_id =  $item_data_stock_reg['purchaseorder_id'];
            // $indent_id = $item_data_stock_reg['indent_id'];
            $po_id =  $resustnew->id;
            $purchase_order_id =  $branch_request['id'];
            $indent_id = $branch_request['id'];
            $item_id = $item_id;
            $issue_date = date('Y-m-d H:i:s');
            $delivery_date = date('Y-m-d H:i:s');
            $qty = $branch_request['item_qty'];

            $rate = $branch_request['item_amount'];
            $cost_price = $qty * $branch_request['item_amount'];
            $tax_id = $branch_request['item_tax'];
            $tax =  $cost_price * $branch_request['item_tax'] / 100;
            $amount = sprintf('%.2f', $tax + $cost_price);
            $store_type = '2';

            $stock_register_entry = ConnectionManager::get('default');
            $stock_back  = $item_data['stock_available'] - $branch_request['item_qty'];
            $db = $this->request->session()->read('Auth.User.db');
            $stock_insert =  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_id')";
            $stock_register_entry->execute($stock_insert);
          }
        }

        $this->Tempsolditemsrequest->deleteAll();
        $this->Flash->success(__('Sold Item  request successfully saved.'));
        $this->Tempsolditemsrequest->deleteAll(['Tempsolditemsrequest.branch_name' => $dbname]);
        //$this->Tempsolditemsrequest->deleteAll();
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
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
    $this->loadModel('Additem');
    $this->loadModel('Categorywise');
    $this->loadModel('Tempsolditemsrequest');

    if ($this->request->is(['post'], ['put'])) {
      // pr($this->request->data); die;
      $this->Tempsolditemsrequest->deleteAll(['Tempsolditemsrequest.branch_name' => $dbname]);
      $cat_id = $this->request->data['category_id'];

      $branch_name = $this->request->data['branch_name'];

      $cat_qty = $this->request->data['category_qty'];
      $category = $this->Categorywise->find('all')->where(['category_id' => $cat_id])->order(['Categorywise.id' => 'Desc'])->toarray();
      // pr($category); die;
      foreach ($category as $key => $value) {
        $group_type = $this->Categorywise->find('all')->where(['Categorywise.id' => $value['id']])->first();
        //$item_data = $this->Additem->find('all')->where(['Additem.id'=>$value['item_name']])->first();
        $qty = $cat_qty * $group_type['quantity'];

        $cat = $this->Tempsolditemsrequest->newEntity();
        $cat_item['category_id'] = $cat_id;
        $cat_item['category_name'] = $value['group_cat_name'];
        $cat_item['item_id'] = $value['item_name'];
        $cat_item['quantity'] = $qty;
        $cat_item['group_type'] = $group_type['group_type'];
        $cat_item['discount_type'] = $group_type['discount_type'];
        $cat_item['discount_amount'] = $group_type['discount'];
        $cat_item['branch_name'] = $branch_name;

        $pnewdetail = $this->Tempsolditemsrequest->patchEntity($cat, $cat_item);
        // pr($pnewdetail); die;
        $resustnew = $this->Tempsolditemsrequest->save($pnewdetail);
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

  public function delete($id)
  {
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempsolditemsrequest');
    $classes = $this->Tempsolditemsrequest->get($id);
    //delete pariticular entry
    try {
      if ($this->Tempsolditemsrequest->delete($classes)) {
        $this->Flash->success(__(' You delete item: {0} has been deleted.', h($id)));
        return $this->redirect(['action' => 'add']);
      }
    } catch (\PDOException $e) {
      $this->Flash->error(__(' You can not delete this record because it is used in another table.'));
      $this->set('error', $error);
      return $this->redirect(['action' => 'add']);
    }
  }

  public function billgenerate($id)
  {

    $this->loadModel('Itemcategory');
    $this->loadModel('Additem');
    $this->loadModel('Tempsolditemsrequest');
    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');


    $branch_request = $this->Solditem->find('all')->contain(['Solditemdetails'])->where(['Solditem.id' => $id])->order(['Solditem.id' => 'Desc'])->first();
    //pr($branch_request); die;

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $this->loadModel('Companymaster');
    $sitesetting = $this->Sitesettings->find('all')->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    $company_master = $this->Companymaster->find('all')->order(['Companymaster.id' => 'DESC'])->first();


    $this->set(compact(['branch_request', 'sitesetting', 'site_details', 'company_master', 'id']));
  }


  // public function connection($dbs)
  // {
  //   //echo $dbs; die;
  //   ConnectionManager::config($dbs, [
  //     'className' => 'Cake\Database\Connection',
  //     'driver' => 'Cake\Database\Driver\Mysql',
  //     'persistent' => false,
  //     'host' => DBHOSTNAME,
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

  public function salebillgenerate($id)
  {
    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');
    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $solditem = $this->Solditem->find('all')->contain(['Solditemdetails' => ['Additem']])->where(['Solditem.id' => $id])->order(['Solditem.id' => 'Desc'])->first();
    $sitesetting = $this->Sitesettings->find('all')->first();
    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
    //   pr($sitesetting); die;
    $this->set(compact(['solditem', 'sitesetting', 'site_details']));
  }

  // public function cancelrequest($id)
  // {

  //   $this->loadModel('Solditem');

  //   $cancel_req = $this->Solditem->get($id);
  //   // $this->set('cancel_req', $cancel_req);

  //   if ($this->request->is(['post', 'put'])) {

  //     $this->request->data['remark'] = $this->request->data['remark'];
  //     $this->request->data['status'] = "Cancel";


  //     $entity = $this->Solditem->patchEntity($cancel_req, $this->request->data);
  //     $resustnew = $this->Solditem->save($entity);

  //     if ($resustnew) {
  //       $this->Flash->success(__('Request has been cancelled'));
  //       return $this->redirect(['action' => 'index']);
  //     } else {
  //       $this->Flash->error(__('Somethign went wrong contact to administrator'));
  //       return $this->redirect(['action' => 'index']);
  //     }
  //   }
  // }


  public function cancelrequest($id)
  {

    $this->loadModel('Solditem');
    $this->loadModel('Solditemdetails');
    $this->loadModel('StockAvailable');
    $this->loadModel('Stockregister');

    $cancel_req = $this->Solditem->get($id);

    if ($this->request->is(['post', 'put'])) {

      $this->request->data['remark'] = $this->request->data['remark'];
      $this->request->data['status'] = "Cancel";


      $entity = $this->Solditem->patchEntity($cancel_req, $this->request->data);
      $resustnew = $this->Solditem->save($entity);

      if ($resustnew) {
        $student_ids = $resustnew['student_id'];

        $sold_id = $resustnew->id;

        $sold_item_request = $this->Solditemdetails->find('all')->where(['Solditemdetails.sold_id' => $sold_id])->toarray();


        foreach ($sold_item_request as $value) {


          $item_id = $value['item_id'];

          $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $item_id])->first();
          $stock_data_id = $item_data['id'];
          $conn = ConnectionManager::get('default');
          $db = $this->request->session()->read('Auth.User.db');

          $stock_back  = $item_data['stock_available'] + $value['item_qty'];

          $sold_stock  = $item_data['sold_stock'] - $value['item_qty'];

          $stock_update = "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`sold_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";

          $conn->execute($stock_update);

          $item_sold_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.student_id' => $student_ids, 'Stockregister.po_id' => $sold_id, 'Stockregister.purchaseorder_id' => $value['id']])->first();

          $po_id =  $item_sold_stock_reg['po_id'];
          $purchase_order_id =  $item_sold_stock_reg['purchaseorder_id'];
          $indent_id = $item_sold_stock_reg['indent_id'];
          $item_id = $item_sold_stock_reg['item_id'];
          $issue_date = date('Y-m-d', $item_sold_stock_reg['issue_date']);
          $delivery_date = date('Y-m-d', $item_sold_stock_reg['delivery_date']);
          $qty = $item_sold_stock_reg['quantity'];

          $rate = $item_sold_stock_reg['rate'];
          $cost_price = $qty * $item_sold_stock_reg['rate'];
          $tax_id = $item_sold_stock_reg['tax_id'];
          $tax =  $item_sold_stock_reg['tax'];
          $amount = $item_sold_stock_reg['amount'];
          $store_type = $item_sold_stock_reg['store_type'];
          $created = date('Y-m-d H:i:s');

          $stock_register_entry = ConnectionManager::get('default');

          $db = $this->request->session()->read('Auth.User.db');
          $stock_insert =  "INSERT INTO $db.st_cancel_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`,`cancel_created_time`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_id','$created')";
          $stock_register_entry->execute($stock_insert);

          $classes = $this->Stockregister->get($item_sold_stock_reg['id']);
          $this->Stockregister->delete($classes);
        }
        $this->Flash->success(__('Request has been cancelled'));
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      }
    }
  }




  public function getstudentname()
  {
    $this->loadModel('Students');

    $stsearch = $this->request->data['fetch'];
    $check = $this->request->data['check'];
    $checks = $this->request->data['id'];

    $searchst = $this->Students->find('all')->where(['Students.fname LIKE' => $stsearch . '%', 'Students.status' => 'Y'])->toarray();

    foreach ($searchst as $value) {
      echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . ucfirst($value['fname']) . " " . strtolower($value['lname']) . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . ucfirst($value['fname']) . " " . strtolower($value['lname']) . "(" . $value['enroll'] . ")" . '</a></li>';
    }

    die;
  }

  public function getitemname()
  {
    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Additem');
    $this->loadModel('Sizemanager');
    //pr($this->request->data); die;
    $stsearch = $this->request->data['fetch'];
    $check = $this->request->data['check'];
    //echo $stsearch; die;       
    $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y'])->toarray();
    //pr($searchst); die;        

    foreach ($searchst as $value) {

      echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
    }
    die;
  }


  public function itemrequest()
  {


    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);
    if ($dbname != $branch[0]) {

      $this->connection(trim($branch[0]));
    }

    $this->loadModel('Tempsolditemsrequest');
    $this->loadModel('Itemcategory');
    $this->loadModel('Additem');

    if ($this->request->is(['post'], ['put'])) {
      // pr($this->request->data); //die;
      $item_id = $this->request->data['item_id'];
      $cat_qty = $this->request->data['item_qty'];

      $items = $this->Additem->find('all')->where(['item_name' => $item_id])->first();
      //pr($items); die;
      $cat = $this->Tempsolditemsrequest->newEntity();
      $branch_name =  $this->request->session()->read('Auth.User.db');
      $cat_item['category_id'] = $items['category_id'];
      $cat_item['category_name'] = $items['itemcategory']['category_name'];
      $cat_item['item_id'] = $items['id'];
      $cat_item['quantity'] = $cat_qty;
      $cat_item['branch_name'] = $branch_name;
      $cat_item['group_type'] = "";
      $cat_item['discount_type'] = "Amount"; //$group_type['discount_type'];
      $cat_item['discount_amount'] = $items['discount'];
      // $cat_item['category_id'] = $items['category_id'];
      // $cat_item['category_name'] = $items['itemcategory']['category_name'];
      // $cat_item['item_id'] = $items['id'];
      // $cat_item['quantity'] = $cat_qty;

      $pnewdetail = $this->Tempsolditemsrequest->patchEntity($cat, $cat_item);
      $resustnew = $this->Tempsolditemsrequest->save($pnewdetail);


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

  // bottomitemdelete function
  public function bottomitemdelete($id)
  {

    $dbname = $this->request->session()->read('Auth.User.db');
    $branch = explode("_", $dbname);

    if ($dbname != $branch[0]) {
      $this->connection(trim($branch[0]));
    }
    $this->loadModel('Tempsolditemsrequest');
    if ($this->Tempsolditemsrequest->deleteAll(['group_type' => 'Bottom', 'Tempbranchrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempsolditemsrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempsolditemsrequest Recored not  delete successfully');
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
    $this->loadModel('Tempsolditemsrequest');
    if ($this->Tempsolditemsrequest->deleteAll(['group_type' => 'Top', 'Tempsolditemsrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempsolditemsrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempsolditemsrequest Recored not  delete successfully');
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
    $this->loadModel('Tempsolditemsrequest');
    if ($this->Tempsolditemsrequest->deleteAll(['Tempsolditemsrequest.group_type' => 'Socks', 'Tempsolditemsrequest.branch_name' => $dbname])) {
      $this->Flash->success('Tempsolditemsrequest Recored deleted successfully');
      return $this->redirect(['action' => 'add']);
    } else {
      $this->Flash->error('Tempsolditemsrequest Recored not  delete successfully');
      return $this->redirect(['action' => 'add']);
    }
  }
  //daily collection report index page view
  public function storedetailreport()
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Solditem');
  }

  //serach data
  public function searchdetailreport()
  {
    //pr($this->request->data); die;
    $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
    $dateto = date('Y-m-d', strtotime($this->request->data['dateto']));
    $this->set(compact('datefrom'));
    $this->set(compact('dateto'));
    $this->loadModel('Solditem');
  }


  // export excel
  public function exportsoldreport()
  {
    $this->loadModel('Solditem');
    $this->loadModel('Taxmaster');
    $this->loadModel('Solditemdetails');
    $this->loadModel('Additem');
    $this->loadModel('Students');
    $this->loadModel('Classes');
    //pr($_SESSION); die;
    $datefrom = $_SESSION['datefrom'];
    $dateto =  $_SESSION['dateto'];
    //	$cond = [];
    if (!empty($datefrom)) {

      $cond .= " AND DATE(Solditem.pay_date) >= '" . $datefrom . "'";
    }
    if (!empty($d2dateto)) {

      $cond .= " AND DATE(Solditem.pay_date) <= '" . $dateto . "'";
    }

    $store_datas = $this->Solditem->find('all')->where(['DATE(Solditem.pay_date) >=' => $datefrom, 'DATE(Solditem.pay_date) <=' => $dateto, 'Solditem.status' => 'Approved'])->order(['Solditem.id' => 'Asc'])->toarray();
    // pr($store_datas); die;
    foreach ($store_datas as $val) {
      $sold_request_data[] = $val['id'];
    }
    //pr($sold_request_data); die;
    if ($sold_request_data) {
      $branch_request_detail = $this->Solditemdetails->find('all')->where(['Solditemdetails.sold_id IN' => $sold_request_data, 'Solditem.status' => 'Approved'])->contain(['Additem' => ['Taxmaster'], 'Solditem' => ['Students' => ['Classes']]])->order(['Solditem.pay_date' => 'Asc'])->toarray();
    }
    // pr($branch_request_detail); die;
    $this->set('branch_request_detail', $branch_request_detail);
  }

  public function searchreq()
  {
    $this->loadModel('Students');
    $this->loadModel('Solditem');

    $sale_id = $this->request->data['sale_id'];
    $stu_name = $this->request->data['stu_name'];

    $cond = [];
    if (isset($sale_id) && $sale_id != '') {
      $cond['Solditem.id'] = $sale_id;
    }

    if (isset($stu_name) && $stu_name != '') {
      $cond['Solditem.customer_name LIKE'] = '%' . trim($stu_name) . '%';
    }


    $sold_item = $this->Solditem->find('all')->contain(['Students'])->where($cond)->order(['Solditem.id' => 'Desc'])->toarray();
    // pr($sold_item); die;
    $this->set('sold_item', $sold_item);
  }

  // ho sold item index function
  public function hosolditems()
  {

    $this->viewBuilder()->layout('admin');
    $this->loadModel('Branchrequest');

    $session = $this->request->session();
    $session->delete('hosolddata');

    $branch_sold  =  $this->Branchrequest->find('all')->where(['status' => 'Approved'])->order(['Branchrequest.id' => 'desc']);

    // branch name show in drop sown list
    $branches  =  $this->Branchrequest->find('all')->order(['Branchrequest.id' => "Asc"])->group(['branch_name'])->toarray();

    $enquires = $this->paginate($branch_sold)->toarray();

    $this->set('hosold', $enquires);
    $this->set('branches', $branches);
  }

  // ho sold item bill genrate function
  public function soldhobillgenerate($id)
  {

    $this->response->type('pdf');

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


    $this->set(compact(['branch_request', 'sitesetting', 'site_details', 'company_master', 'tax_master', 'id', 'branches']));
  }

  // ho sold item with serching data view function
  public function searchho()
  {

    $this->loadModel('Branchrequest');


    $session = $this->request->session();
    $item = $this->request->data['req_no'];
    $branchname = $this->request->data['branch_name'];
    $session->delete('hosolddata');

    $cond = [];
    if (isset($item) && $item != '') {
      $cond['Branchrequest.id'] = $item;
    }

    if (isset($branchname) && $branchname != '') {
      $cond['Branchrequest.branch_name'] = $branchname;
    }


    $user = $this->Branchrequest->find('all')->where(['Branchrequest.status' => 'Approved', $cond])->order(['Branchrequest.id' => 'Desc'])->toarray();
    $this->set('branch_request', $user);

    $session->delete('branch_name');
    $session->write('hosolddata', $cond);
  }

  // search base excel export

  function exporthosolditems()
  {
    $this->loadModel('Branchrequest');

    $cond = $_SESSION['hosolddata'];
    if ($cond) {

      $ho_solditem_detail = $this->Branchrequest->find('all')->where(['Branchrequest.status' => 'Approved', $cond])->order(['Branchrequest.id' => 'Desc'])->toarray();
    } else {
      $ho_solditem_detail = $this->Branchrequest->find('all')->where(['Branchrequest.status' => 'Approved'])->order(['Branchrequest.id' => 'Desc'])->toarray();
    }
    foreach ($ho_solditem_detail as $values) {
      $ho_data[] = [
        'req_no' => $values['id'],
        'branch_name' => $values['branch_name'],
        'description' => $values['description'],
        'remark' => $values['remark'],
        'status' => $values['status'],
        'approved_date' => $values['approved_date']
      ];
    }
    $this->set('sold_data_ho', $ho_data);
  }
}
