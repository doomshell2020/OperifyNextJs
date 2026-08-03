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

class SalereturnController extends AppController
{
  //   public function connection($dbs)
  //   {
  //     //echo $dbs; die;
  //    ConnectionManager::config($dbs, [
  //      'className' => 'Cake\Database\Connection',
  //      'driver' => 'Cake\Database\Driver\Mysql',
  //      'persistent' => false,
  //      'host' => 'localhost',
  //      'username' => 'tpplerp',
  //      'password' => 'tpplerp@23~',
  //      'database' => $dbs,
  //      'encoding' => 'utf8mb4',
  //      'timezone' => 'UTC',
  //      'cacheMetadata' => true,
  //  ]);
  //  ConnectionManager::drop('default');
  //  ConnectionManager::get($dbs);
  //  \Cake\Datasource\ConnectionManager::alias($dbs, 'default');
  //   }


    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_",$dbname);
        if($dbname != $branch[0]){

          $this->connection(trim($branch[0]));
          }
        
          $this->loadModel('Salesreturn');
          $this->loadModel('Salesreturndetails');

          if($dbname != $branch[0]){

            $branch_request = $this->Salesreturn->find('all')->where(['branch_name'=>$dbname])->order(['Salesreturn.id' => 'desc'])->toarray();
            }else{
             $branch_request = $this->Salesreturn->find('all')->order(['Salesreturn.id' => 'desc'])->toarray();
            }
            $this->set(compact('branch_request'));
    }


    public function viewdetail($id)
    {
      $this->set(compact('id'));
      $dbname = $this->request->session()->read('Auth.User.db');
      $branch = explode("_",$dbname);
      if($dbname != $branch[0]){
     $this->connection(trim($branch[0]));
      }
      $this->loadModel('Salesreturn');
      $this->loadModel('Salesreturndetils');
   

     $branch_request= $this->Salesreturn->find('all')->where(['id'=>$id])->order(['id' => 'Desc'])->first();
     $requestdetails= $this->Salesreturndetils->find('all')->contain(['Additem','Salesreturn'])->where(['salereturn_id'=>$id])->order(['Additem.item_name' => 'Asc'])->toarray();
      
     $this->set(compact(['requestdetails','branch_request']));

    }




    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('StockAvailable');
        $this->loadModel('Stockregister');
        
        $dbname = $this->request->session()->read('Auth.User.db'); 
        $branch = explode("_",$dbname);
        if($dbname != $branch[0]){
          
        }
        
        $this->connection(trim($branch[0]));
        
        $this->loadModel('Salesreturn');
        $this->loadModel('Tempsalereturn');
        $this->loadModel('Itemcategory');
        $this->loadModel('Taxmaster');
        $this->loadModel('Categorywise');
      
        $this->loadModel('Additem');
  
        $category = $this->Categorywise->find('all')->contain(['Itemcategory'])->group(['category_id'])->order(['Categorywise.id' => 'Desc'])->toarray();
      
        $this->set('categary', $category);

        $temp_item = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.group_type NOT IN'=>['Top','Bottom','Socks'],'Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->toarray();
        $this->set(compact('temp_item'));


        $temp_item_top = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.group_type'=>'Top','Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->toarray();

         $temp_item_bottom = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.group_type'=>'Bottom','Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->toarray();
        
         $temp_item_socks = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.group_type'=>'Socks','Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->toarray();
        
         $this->set(compact('temp_item_bottom'));
         $this->set(compact('temp_item_top'));
         $this->set(compact('temp_item_socks'));
    $db = $this->request->session()->read('Auth.User.db');
    
         if ($this->request->is(['post'],['put'])) {

          $temp_item = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.group_type NOT IN'=>['Top','Bottom','Socks'],'Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->toarray();
        
          if($temp_item){
          
          }else{
           $this->Flash->error(__('Items not added'));
           return $this->redirect(['action' => 'add']);
          }
   
         $this->loadModel('Salesreturn');
         $this->loadModel('Salesreturndetils');
         $this->loadModel('Additem');
         $this->loadModel('Taxmaster');
   
         $cat = $this->Salesreturn->newEntity();
    
         $stock_check_data  = 0;
         foreach($temp_item as $intusr){
     
        
          $item_data = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $intusr['item_id'],'Stockregister.store_type IN' => ['0', '1']])->first();

             if($item_data['stock_available'] == "0"){
               $stock_check_data = 1;
             }
         } 
         if($stock_check_data == 1){
           $this->Flash->error(__('Stock Item not Available  in this request'));
           return $this->redirect(['action' => 'add']);
         }


   
    
    //tempitem_topstock
    $stock_check_data  = 0;
    //foreach ($temp_item_top as $intusr) {
     for($i=0; $i<count($this->request->data['top_product']); $i++){

      $item_data = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $this->request->data['top_product'][$i]])->first();

    // $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' =>$this->request->data['top_product'][$i]])->first();

    if ($item_data['stock_available'] == "0") {
      $stock_check_data = 1;
    }
    }
    if ($stock_check_data == 1) {
    $this->Flash->error(__('Stock Item not Available  in this request'));
    return $this->redirect(['action' => 'add']);
    }

    //tempitem_bottom
    $stock_check_data  = 0;
    //foreach ($temp_item_bottom as $intusr) {
    for($i=0; $i<count($this->request->data['bottom_product']); $i++){	  

    // $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $this->request->data['bottom_product'][$i]])->first();

    $item_data = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $this->request->data['bottom_product'][$i]])->first();


    if ($item_data['stock_available'] == "0") {
      $stock_check_data = 1;
    }
    }
    if ($stock_check_data == 1) {
    $this->Flash->error(__('Stock Item not Available  in this request'));
    return $this->redirect(['action' => 'add']);
    }

  //tempitem_socks
  $stock_check_data  = 0;
  //foreach ($temp_item_socks as $intusr) {
   for($i=0; $i<count($this->request->data['socks_product']); $i++){

  // $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id' => $this->request->data['socks_product'][$i]])->first();

  $item_data = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $this->request->data['socks_product'][$i]])->first();


    if ($item_data['stock_available'] == "0") {
        $stock_check_data = 1;
      }
    }
  if ($stock_check_data == 1) {
    $this->Flash->error(__('Stock Item not Available  in this request'));
    return $this->redirect(['action' => 'add']);
  }

  //total amount
         foreach($temp_item as $key=>$value){ //pr($value); //die;
           $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
           $total_amount +=  $item_data['sale_price']*$value['quantity'];
         }
         
          $cat_item['branch_name'] = $dbname;
          $cat_item['totalamount'] = $total_amount;
          $cat_item['description'] = $this->request->data['description'];
          $cat_item['status'] = "Processing";
          $cat_item['created'] = date('Y-m-d H:i:s');
          $pnewdetail = $this->Salesreturn->patchEntity($cat, $cat_item);
          $branch_request_data = $this->Salesreturn->save($pnewdetail);
        
          if($branch_request_data){
             foreach($temp_item as $key=>$value){ //pr($value); die;
   
               $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
               $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name'=>$value['item_id'],'Categorywise.category_id'=>$value['category_id']])->first();
              
               if($categorywiseitem_data){
                $qty= $value['quantity']*$categorywiseitem_data['quantity'];
               }else{
                 $qty= $value['quantity'];
               }

               $branch_detail = $this->Salesreturndetils->newEntity();
               $item_details['item_qty'] = $qty;
               $item_details['salereturn_id'] = $branch_request_data->id;
               $item_details['item_id'] = $value['item_id'];
               $item_details['category_name'] = $value['category_id'];
               
               $item_details['hsncode'] = $value['additem']['item_isbn'];
               if($item_data['taxmaster']['tax']){
                $item_tax =  $item_data['taxmaster']['tax'];
               }else{
                 $item_tax = 0;
               }
               $item_details['item_tax'] = $item_tax;
               $item_details['item_amount'] = $item_data['sale_price'];
   
               $discount= $categorywiseitem_data['discount'];
               $item_details['discount_type'] =$categorywiseitem_data['discount_type'];
               $item_details['discount'] =$discount;
               $item_details['item_amount'] = $value['additem']['sale_price'];

               $branch_details = $this->Salesreturndetils->patchEntity($branch_detail, $item_details);
               $branch_request = $this->Salesreturndetils->save($branch_details);
               
                $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$value['item_id']])->first();
                $stock_data_id = $item_data['id'];
                $conn = ConnectionManager::get('default');

                $stock_back  = $item_data['stock_available']-$qty; 
                $sold_stock  = $item_data['issue_stock']+$qty; 
              
                $stock_update= "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`issue_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
                echo $stock_update;
                $conn->execute($stock_update);
                $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.store_type'=>3])->order(['Stockregister.id' => 'desc'])->first();
                
                $po_id =  $branch_request_data->id;
                $purchase_order_id =  $branch_request->id;
                $indent_id = $branch_request->id;
                $item_id = $value['item_id'];
                $issue_date = date('Y-m-d H:i:s');
                $delivery_date = date('Y-m-d H:i:s');
                $qty = $qty;
        
                $rate = $value['item_amount'];
                $cost_price = $qty*$value['item_amount'];
                $tax_id = $value['additem']['tax'];
                $tax =  $cost_price*$value['item_tax']/100;
                $amount = sprintf('%.2f',$tax+$cost_price);
                $store_type = '3';
                
                $stock_register_entry = ConnectionManager::get('default');
                $db = $this->request->session()->read('Auth.User.db');
                $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
                $stock_register_entry->execute($stock_insert); 


               
            }
           //top
           for($i=0; $i<count($this->request->data['top_product']); $i++){
        //   foreach($temp_item_top as $key=>$value){ //pr($value); die;
   
             $value = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.id '=>$this->request->data['top_product'][$i],'Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->first();
   
             $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
             $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name'=>$value['item_id'],'Categorywise.category_id'=>$value['category_id']])->first();
             $branch_detail = $this->Salesreturndetils->newEntity();
   
             $item_details['salereturn_id'] = $branch_request_data->id;
             $item_details['item_id'] = $value['item_id'];
             $item_details['category_name'] = $value['category_id'];
             $item_details['item_qty'] =  1;
             $item_details['hsncode'] = $value['additem']['item_isbn'];
               if($item_data['taxmaster']['tax']){
                $item_tax =  $item_data['taxmaster']['tax'];
               }else{
                 $item_tax = 0;
               }
               $item_details['item_tax'] = $item_tax;
               $item_details['item_amount'] = $item_data['sale_price'];
   
   
             // if($value['additem']['discount']){
             //   $discount= $value['additem']['discount'];
             //  }else{
               $discount= $categorywiseitem_data['discount'];
            //  }
             $item_details['discount_type'] =$categorywiseitem_data['discount_type'];
             $item_details['discount'] =$discount;
             $item_details['item_amount'] = $value['additem']['sale_price'];
   
   
           $branch_details = $this->Salesreturndetils->patchEntity($branch_detail, $item_details);
          
           $branch_request = $this->Salesreturndetils->save($branch_details);
           
            $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$value['item_id']])->first();
                $stock_data_id = $item_data['id'];
                $conn = ConnectionManager::get('default');

                $stock_back  = $item_data['stock_available']-$qty; 
                $sold_stock  = $item_data['issue_stock']+$qty; 
              
                $stock_update= "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`issue_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
                
                $conn->execute($stock_update);
             //   echo $stock_update; die;
                $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.store_type'=>3])->order(['Stockregister.id' => 'desc'])->first();
                
                $po_id =  $branch_request_data->id;
                $purchase_order_id =  $branch_request->id;
                $indent_id = $branch_request->id;
                $item_id = $value['item_id'];
                $issue_date = date('Y-m-d H:i:s');
                $delivery_date = date('Y-m-d H:i:s');
                $qty = $qty;
        
                $rate = $value['item_amount'];
                $cost_price = $qty*$value['item_amount'];
                $tax_id = $value['additem']['tax'];
                $tax =  $cost_price*$value['item_tax']/100;
                $amount = sprintf('%.2f',$tax+$cost_price);
                $store_type = '3';
                
                $stock_register_entry = ConnectionManager::get('default');
               
                $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
                $stock_register_entry->execute($stock_insert); 
         } 
         //bottom
         for($i=0; $i<count($this->request->data['bottom_product']); $i++){
           //   foreach($temp_item_top as $key=>$value){ //pr($value); die;
             $value = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.id'=>$this->request->data['bottom_product'][$i],'Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->first();
   
        // foreach($temp_item_bottom as $key=>$value){ //pr($value); die;
   
           $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
           $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name'=>$value['item_id'],'Categorywise.category_id'=>$value['category_id']])->first();
           $branch_detail = $this->Salesreturndetils->newEntity();
   
           $item_details['salereturn_id'] = $branch_request_data->id;
           $item_details['item_id'] = $value['item_id'];
           $item_details['category_name'] =$value['category_id'];
           $item_details['item_qty'] = 1;
           $item_details['hsncode'] = $value['additem']['item_isbn'];
           if($item_data['taxmaster']['tax']){
            $item_tax =  $item_data['taxmaster']['tax'];
           }else{
             $item_tax = 0;
           }
           $item_details['item_tax'] = $item_tax;
           $item_details['item_amount'] = $item_data['sale_price'];
   
   
           // if($value['additem']['discount']){
           //   $discount= $value['additem']['discount'];
           //  }else{
             $discount= $categorywiseitem_data['discount'];
          //  }
           $item_details['discount_type'] =$categorywiseitem_data['discount_type']; 
           $item_details['discount'] =$discount;
           $item_details['item_amount'] = $value['additem']['sale_price'];
   
   
         $branch_details = $this->Salesreturndetils->patchEntity($branch_detail, $item_details);
        
         $branch_request = $this->Salesreturndetils->save($branch_details);
         $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$value['item_id']])->first();
                $stock_data_id = $item_data['id'];
                $conn = ConnectionManager::get('default');

                $stock_back  = $item_data['stock_available']-$qty; 
                $sold_stock  = $item_data['issue_stock']+$qty; 
              
                $stock_update= "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`issue_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
                $conn->execute($stock_update);
                $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.store_type'=>3])->order(['Stockregister.id' => 'desc'])->first();
                
                $po_id =  $branch_request_data->id;
                $purchase_order_id =  $branch_request->id;
                $indent_id = $branch_request->id;
                $item_id = $value['item_id'];
                $issue_date = date('Y-m-d H:i:s');
                $delivery_date = date('Y-m-d H:i:s');
                $qty = $qty;
                $rate = $value['item_amount'];
                $cost_price = $qty*$value['item_amount'];
                $tax_id = $value['additem']['tax'];
                $tax =  $cost_price*$value['item_tax']/100;
                $amount = sprintf('%.2f',$tax+$cost_price);
                $store_type = '3';
                
                $stock_register_entry = ConnectionManager::get('default');
                $db = $this->request->session()->read('Auth.User.db');
                $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
                $stock_register_entry->execute($stock_insert);    
       }
   
       //Item socks
       for($i=0; $i<count($this->request->data['socks_product']); $i++){
  
         $value = $this->Tempsalereturn->find('all')->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->where(['Tempsalereturn.id'=>$this->request->data['socks_product'][$i],'Tempsalereturn.branch_name'=>$dbname])->order(['Tempsalereturn.id' => 'Desc'])->first();
         $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
        // pr($item_data); die;
         $categorywiseitem_data = $this->Categorywise->find('all')->where(['Categorywise.item_name'=>$value['item_id'],'Categorywise.category_id'=>$value['category_id']])->first();
         $branch_detail = $this->Salesreturndetils->newEntity();
   
         $item_details['salereturn_id'] = $branch_request_data->id;
         $item_details['item_id'] = $value['item_id'];
         $item_details['category_name'] = $value['category_id'];
         $item_details['item_qty'] =  1;
         $item_details['hsncode'] = $value['additem']['item_isbn'];
               if($item_data['taxmaster']['tax']){
                $item_tax =  $item_data['taxmaster']['tax'];
               }else{
                 $item_tax = 0;
               }
               $item_details['item_tax'] = $item_tax;
               $item_details['item_amount'] = $item_data['sale_price'];
   
         $discount= $categorywiseitem_data['discount'];
         $item_details['discount_type'] =$categorywiseitem_data['discount_type'];
         $item_details['discount'] =$discount;
         $item_details['item_amount'] = $value['additem']['sale_price'];
    
       $branch_details = $this->Salesreturndetils->patchEntity($branch_detail, $item_details);
       
       $branch_request = $this->Salesreturndetils->save($branch_details);
       $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$value['item_id']])->first();
                $stock_data_id = $item_data['id'];
                $conn = ConnectionManager::get('default');

                $stock_back  = $item_data['stock_available']-$qty; 
                $sold_stock  = $item_data['sold_stock']+$qty; 
              
                $stock_update= "UPDATE $db.`st_stock_available` SET `stock_available`='$stock_back',`issue_stock` = '$sold_stock' WHERE `id`='$stock_data_id'";
                $conn->execute($stock_update);
                $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.store_type'=>3])->order(['Stockregister.id' => 'desc'])->first();
                
                $po_id =  $branch_request_data->id;
                $purchase_order_id =  $branch_request->id;
                $indent_id = $branch_request->id;
                $item_id = $value['item_id'];
                $issue_date = date('Y-m-d H:i:s');
                $delivery_date = date('Y-m-d H:i:s');
                $qty = $qty;
        
                $rate = $value['item_amount'];
                $cost_price = $qty*$value['item_amount'];
                $tax_id = $value['additem']['tax'];
                $tax =  $cost_price*$value['item_tax']/100;
                $amount = sprintf('%.2f',$tax+$cost_price);
                $store_type = '3';
                
                $stock_register_entry = ConnectionManager::get('default');
                $db = $this->request->session()->read('Auth.User.db');
                $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
                $stock_register_entry->execute($stock_insert); 
          
     }
           $this->Flash->success(__('Sale Return request successfully saved.'));
         
           $this->Tempsalereturn->deleteAll(['Salesreturndetails.branch_name'=>$dbname]);
           return $this->redirect(['action' => 'index']);
          }else{
           $this->Flash->error(__('Somethign went wrong contact to administrator'));
           return $this->redirect(['action' => 'index']);
          }  
   
        }   
    
    }

    public function categoryrequest()
    {
     
      $dbname = $this->request->session()->read('Auth.User.db'); 
      $branch = explode("_",$dbname);
      if($dbname != $branch[0]){
        
        $this->connection(trim($branch[0]));
      }
      $this->loadModel('Itemcategory');
      $this->loadModel('Tempsalereturn');
      $this->loadModel('Additem');
      $this->loadModel('Categorywise');

      if ($this->request->is(['post'],['put'])) {
     
        $this->Tempsalereturn->deleteAll(['Tempsalereturn.branch_name'=>$dbname]);
        $cat_id = $this->request->data['category_id'];
        $session = $this->request->session();
        $session->write('category_id',$cat_id);
        $cat_qty = $this->request->data['category_qty'];
        $branch_name=$this->request->data['branch_name'];
        
        $category = $this->Categorywise->find('all')->where(['category_id' => $cat_id])->order(['Categorywise.id' => 'Desc'])->toarray();

        foreach($category as $key=>$value){
          $item_data = $this->Additem->find('all')->where(['Additem.id'=>$value['item_name']])->first();
          $group_type = $this->Categorywise->find('all')->where(['Categorywise.id'=>$value['id']])->first();
          
          // $temp_group_request_check = $this->Tempbranchrequest->find('all')->where(['category_id' => $item_data['category_id'],'group_type'=>$group_type['group_type']])->first();
          // if(empty($temp_group_request_check)){
          $cat = $this->Tempsalereturn->newEntity();
          $cat_item['category_id'] = $cat_id;
          $cat_item['item_id'] = $value['item_name'];
          $cat_item['category_name'] = $value['group_cat_name'];
          $cat_item['quantity'] = $cat_qty;
          $cat_item['branch_name'] = $branch_name;
          $cat_item['group_type'] = $group_type['group_type'];

          $pnewdetail = $this->Tempsalereturn->patchEntity($cat, $cat_item);
      
          $resustnew = $this->Tempsalereturn->save($pnewdetail); 
          //}
        }
        if($resustnew){
          $this->Flash->success(__('Item has been added successfully'));
          echo "0"; die;
        }else{
          $this->Flash->success(__('Somthing went wrong contact to administrator'));
          echo "1"; die;
        }
      }

    }
    public function itemrequest()
    {
     
      
    $dbname = $this->request->session()->read('Auth.User.db'); 
    $branch = explode("_",$dbname);
    if($dbname != $branch[0]){
      
      $this->connection(trim($branch[0]));
      }
      $this->loadModel('Tempsalereturn');
      $this->loadModel('Itemcategory');
      $this->loadModel('Additem');
      if ($this->request->is(['post'],['put'])) {
    
        $item_id = $this->request->data['item_id'];
        $cat_qty = $this->request->data['item_qty'];
 
        $items = $this->Additem->find('all')->where(['item_name' => $item_id])->first();
     
        $branch_name =  $this->request->session()->read('Auth.User.db');

          $sales = $this->Tempsalereturn->newEntity();
          
          $cat_item['category_id'] = $items['category_id'];
          $cat_item['category_name'] = $items['itemcategory']['category_name'];
          $cat_item['item_id'] = $items['id'];
          $cat_item['quantity'] = $cat_qty;

          $cat_item['branch_name'] = $branch_name;
          $cat_item['group_type'] = "";
          $cat_item['hsncode'] = $items['item_isbn'];

          
          $pnewdetail = $this->Tempsalereturn->patchEntity($sales, $cat_item);
          $resustnew = $this->Tempsalereturn->save($pnewdetail); 
         
        //  pr($resustnew); die;
     
        if($resustnew){
          $this->Flash->success(__('Item has been added successfully'));
          echo "0"; die;
        }else{
          $this->Flash->success(__('Somthing went wrong contact to administrator'));
          echo "1"; die;
        }

      }

    }

    public function getitemname(){       
        $dbname = $this->request->session()->read('Auth.User.db'); 
          $branch = explode("_",$dbname);
          if($dbname != $branch[0]){
            
            $this->connection(trim($branch[0]));
            }
          
            $this->loadModel('Itemcategory');
            $this->loadModel('Additem');
            $stsearch=$this->request->data['fetch'];
            $check=$this->request->data['check']; 
            $searchst=$this->Additem->find('all')->where(['Additem.item_name LIKE'=>'%'.$stsearch.'%','Additem.status'=>'Y'])->toarray();      
            foreach($searchst as $value){ 
            
             
            echo '<li onclick="cllbckretail('."'".$value['item_name']."'".','."'".$value['id']."'".','."'".$i."'".')"><a href="javascript:void(0)">'.$value['item_name'].'</a></li>';
                
            }
        
      
        die; 
      }
      
      public function delete($id){
        
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_",$dbname);
        if($dbname != $branch[0]){
          $this->connection(trim($branch[0]));
        }
        $this->loadModel('Tempsalereturn');
        $classes = $this->Tempsalereturn->get($id);
        //delete pariticular entry
        try { 
          if ($this->Tempsalereturn->delete($classes)) {
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
        $branch = explode("_",$dbname);
        
        if($dbname != $branch[0]){
          $this->connection(trim($branch[0]));
        }
          $this->loadModel('Tempsalereturn');
         if($this->Tempsalereturn->deleteAll(['group_type' =>'Bottom','Tempsalereturn.branch_name'=>$dbname])){
          $this->Flash->success('Tempsalereturn Recored deleted successfully');
          return $this->redirect(['action' => 'add']);
         }else {
          $this->Flash->error('Tempsalereturn Recored not  delete successfully');
          return $this->redirect(['action' => 'add']);
        }
  
      }
  
  // bottomitemdelete function
      public function topitemdelete()
      {
        
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_",$dbname);
        
        if($dbname != $branch[0]){
          $this->connection(trim($branch[0]));
        }
        $this->loadModel('Tempsalereturn');
        if($this->Tempsalereturn->deleteAll(['group_type' =>'Top','Tempsalereturn.branch_name'=>$dbname])){
          $this->Flash->success('Tempsalereturn Recored deleted successfully');
          return $this->redirect(['action' => 'add']);
         }else {
          $this->Flash->error('Tempsalereturn Recored not  delete successfully');
          return $this->redirect(['action' => 'add']);
        }
      }
  
  // socksitemdelete function
      public function socksitemdelete()
      {
        
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_",$dbname);
        
        if($dbname != $branch[0]){
          $this->connection(trim($branch[0]));
        }
        $this->loadModel('Tempsalereturn');
        if($this->Tempsalereturn->deleteAll(['group_type' =>'Socks','Tempsalereturn.branch_name'=>$dbname])){
          $this->Flash->success('Tempsalereturn Recored deleted successfully');
          return $this->redirect(['action' => 'add']);
         }else {
          $this->Flash->error('Tempsalereturn Recored not  delete successfully');
          return $this->redirect(['action' => 'add']);
        }
      }

      public function cancelrequest($id){

            
          
        $this->loadModel('Salesreturn');
        $this->loadModel('Salesreturndetils');
        $cancel_req = $this->Salesreturn->get($id);
        $dbname =  $cancel_req['branch_name'];
        
         $conn =$this->connection(trim($dbname));
     
  $this->loadModel('Stockregister');
      
        if ($this->request->is(['post', 'put'])) {
           $this->request->data['remark']= $this->request->data['remark'];
           $this->request->data['status']= "Cancel";
    
       $entity = $this->Salesreturn->patchEntity($cancel_req, $this->request->data);  
       $resustnew = $this->Salesreturn->save($entity); 

      if($resustnew){
         $sales_item_request = $this->Stockregister->find('all')->where(['Stockregister.po_id'=>$id])->toarray();
       // pr($sales_item_request); die;
     
        foreach($sales_item_request as $value){
          $po_id =  $value['po_id'];
          $purchase_order_id =  $value['purchaseorder_id'];
          $indent_id = $value['indent_id'];
          $item_id = $value['item_id'];
          $issue_date = date('Y-m-d H:i:s');
          $delivery_date = date('Y-m-d H:i:s');
          $qty = $value['quantity'];
          $rate = $value['rate'];
          $cost_price = $qty * $value['rate'];
  $tax_id = $value['tax_id'];
  $tax =  $value['tax'];
  $amount =$value['amount'];
  $store_type = $value['store_type'];
         $created = date('Y-m-d H:i:s');
          $stock_register_entry = ConnectionManager::get('default');
          $db =  $cancel_req['branch_name'];
          $stock_insert=  "INSERT INTO $db.st_stock_cancel_sales_return (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,cancel_created_time) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$created')";
        
          $stock_register_entry->execute($stock_insert); 
  
          $sales_delete = $this->Stockregister->get($value['id']);
        //  pr($sales_delete); die;
          $this->Stockregister->delete($sales_delete);
        }
        $this->Flash->success(__('Request has been cancelled'));
        return $this->redirect(['action' => 'index']);
      }else{
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      } 
      }
    }
// Ho sale return approved funtion
    public function viewitemdetail($id)
    {
      $this->viewBuilder()->layout('admin');
      $this->set(compact('id'));
 
     $this->loadModel('StockAvailable');
     $this->loadModel('Stockregister');


      $dbname = $this->request->session()->read('Auth.User.db');
      $branch = explode("_",$dbname);
      if($dbname != $branch[0]){
     $this->connection(trim($branch[0]));
      }
     $this->loadModel('Salesreturn');
     $this->loadModel('Salesreturndetils');
     $this->loadModel('Itemcategory');
     $this->loadModel('Categorywise');

     $approve_req = $this->Salesreturn->get($id);
    //  pr($approve_req); die;
     $requestdetails= $this->Salesreturndetils->find('all')->contain(['Additem','Itemcategory'])->where(['salereturn_id'=>$id])->order(['Additem.item_name' => 'Asc'])->toarray();
     $this->set(compact(['requestdetails','approve_req']));
     //pr($requestdetails); die;

     if ($this->request->is(['post'],['put'])) {
   //pr($this->request->data); die;
       $this->request->data['remark']=$this->request->data['description'];
       $this->request->data['approved_date']= date('Y-m-d H:i:s', strtotime($this->request->data['sale_date']));
       $this->request->data['customer_type']=$this->request->data['customer_type'];
       $this->request->data['customer_name']=$this->request->data['customer_name'];
       $this->request->data['status']='Approved';
           
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
      $entity = $this->Salesreturn->patchEntity($approve_req, $this->request->data); 
      $resustnew = $this->Salesreturn->save($entity); 

      if($resustnew){

         $requestdetails= $this->Salesreturndetils->find('all')->contain(['Additem','Itemcategory'])->where(['salereturn_id'=>$id])->order(['Additem.item_name' => 'Asc'])->toarray();
         foreach($requestdetails as $intusr){
       //  pr($intusr);
           $item_data = $this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$intusr['additem']['id']])->first();
           $stock_data_id = $item_data['id'];
           $conn = ConnectionManager::get('default');
           $stock_back  = $item_data['stock_available']+$intusr['item_qty']; 
             //$stock_back  = $item_data['stock_available']+$intusr['item_qty']; 
           $stock_update= "UPDATE `st_stock_available` SET `stock_available`='$stock_back' WHERE `id`='$stock_data_id'";
           $conn->execute($stock_update);
        
           $item_data_stock_reg = $this->Stockregister->find('all')->where(['Stockregister.item_id'=>$intusr['additem']['id']])->first();
        
           //pr($item_data_stock_reg); die;
           $po_id = $intusr['salereturn_id'];
           $purchase_order_id =  $intusr['salereturn_id'];
          $indent_id = $intusr['id'];
           $item_id = $intusr['additem']['id'];
           $issue_date = date('Y-m-d H:i:s');
           $delivery_date = date('Y-m-d H:i:s');
           $qty = $intusr['item_qty'];
        
           $rate = $intusr['item_amount'];
           $cost_price = $qty*$intusr['item_amount'];
           $tax_id = $intusr['additem']['tax'];
           $tax =  $cost_price*$intusr['item_tax']/100;
           $amount = sprintf('%.2f',$tax+$cost_price);
           $store_type = '1';
          
           $stock_register_entry = ConnectionManager::get('default');
           $db = $this->request->session()->read('Auth.User.db');
           $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type')";
           $stock_register_entry->execute($stock_insert);
         }
        $this->Flash->success(__('Request has been approved successfully'));
        return $this->redirect(['action' => 'index']);
      }else{
        $this->Flash->error(__('Somethign went wrong contact to administrator'));
        return $this->redirect(['action' => 'index']);
      }  
     
     }

    }
// bill gernate pdf
    public function billgenerate($id)
    {
      $this->response->type('pdf');
     
     $dbname = $this->request->session()->read('Auth.User.db');
     $branch = explode("_",$dbname);
  
     if($dbname != $branch[0]){
     $this->connection(trim($branch[0]));
     }
     $this->loadModel('Salesreturn');
     $this->loadModel('Sitesettings');
     $this->loadModel('SitesettingsDetails');
     $this->loadModel('Companymaster');
     $this->loadModel('Taxmaster');
     $this->loadModel('Additem');
     $this->loadModel('Salesreturndetils');


     $tax_master=$this->Taxmaster->find('all')->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'Asc'])->toarray();

      $branch_request = $this->Salesreturn->find('all')->contain(['Salesreturndetils'=>['Additem']])->where(['Salesreturn.id' =>$id])->order(['id' => 'asc'])->first();
     
      
      $sitesetting = $this->Sitesettings->find('all')->first();
      $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
      $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
      $company_master =$this->Companymaster->find('all')->where(['main_branch' => 'Y'])->order(['Companymaster.id' => 'DESC'])->first();
     //pr($branch_request); die;
   
        
      $this->set(compact(['branch_request','sitesetting','site_details','company_master','tax_master','id']));
    } 
}