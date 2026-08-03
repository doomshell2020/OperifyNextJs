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

class StudentpurchasereturnController extends AppController
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
   
        $this->loadModel('StudentPurchasereturn');
        $this->loadModel('Students');
        
        $purches = $this->StudentPurchasereturn->find('all')->contain(['Students'])->order(['StudentPurchasereturn.id' => 'desc'])->toarray();
         //pr($purches); die;
        $this->set(compact('purches'));
         
        

    }


    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('TempStudentpurchasereturn');
        $this->loadModel('StudentPurchasereturn');
        $this->loadModel('StudentPurchasereturnDetails');
        $this->loadModel('Solditem');
        $this->loadModel('Solditemdetails');

        
        $dbname = $this->request->session()->read('Auth.User.db'); 
        $branch = explode("_",$dbname);
        if($dbname != $branch[0]){
        //  echo $branch[0]; die;
          $this->connection(trim($branch[0])); 
        }
        
        $this->loadModel('Itemcategory');
        $this->loadModel('Taxmaster');
        $this->loadModel('Categorywise');
      
        $this->loadModel('Additem');
  
        $category = $this->Categorywise->find('all')->contain(['Itemcategory'])->group(['category_id'])->order(['Categorywise.id' => 'Desc'])->toarray();
      
        $this->set('categary', $category);


        $temp_item = $this->TempStudentpurchasereturn->find('all')->where(['TempStudentpurchasereturn.group_type NOT IN'=>['Top','Bottom','Socks']])->order(['TempStudentpurchasereturn.id' => 'asc'])->toarray();
       $this->set(compact('temp_item'));


       $temp_item_top = $this->TempStudentpurchasereturn->find('all')->where(['TempStudentpurchasereturn.group_type'=>'Top'])->order(['TempStudentpurchasereturn.id' => 'Desc'])->toarray();

       $temp_item_bottom = $this->TempStudentpurchasereturn->find('all')->where(['TempStudentpurchasereturn.group_type'=>'Bottom'])->order(['TempStudentpurchasereturn.id' => 'Desc'])->toarray();
      
       $temp_item_socks = $this->TempStudentpurchasereturn->find('all')->where(['TempStudentpurchasereturn.group_type'=>'Socks'])->order(['TempStudentpurchasereturn.id' => 'Desc'])->toarray();
      
       $this->set(compact('temp_item_bottom'));
       $this->set(compact('temp_item_top'));
       $this->set(compact('temp_item_socks'));


       if ($this->request->is(['post'],['put'])) {
 
         foreach($temp_item as $value){

          $sold_item_exist = $this->Solditemdetails->exists(['Solditemdetails.item_id' => $value['item_id'],'Solditemdetails.sold_id' => $this->request->data['invoice_no']]);

          if(empty($sold_item_exist)){
            $this->Flash->success(__('This item was not given in invoice'));
            return $this->redirect(['action' => 'add']);
          }     

       }      
         $cat = $this->StudentPurchasereturn->newEntity();
        $temp_item = $this->TempStudentpurchasereturn->find('all')->contain(['Itemcategory'])->toarray();
        
        foreach($temp_item as $key=>$value){ 
          $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();

        $total_amount +=  $item_data['sale_price']*$value['quantity'];
        
        }
        
        
    $sold_item_exist = $this->Solditem->exists(['Solditem.student_id' => $this->request->data['stu_name'],'Solditem.id' => $this->request->data['invoice_no']]);

    if(empty($sold_item_exist)){
      $this->Flash->success(__('Invoice number not matched'));
      return $this->redirect(['action' => 'add']);
    }

         $cat_item['invoice_no'] = $this->request->data['invoice_no'];
         $cat_item['stu_id'] = $this->request->data['stu_name'];
         $cat_item['totalamount'] = $total_amount;
         $cat_item['description'] = $this->request->data['description'];
         $cat_item['category_id'] = $this->request->data['category_id'];
         $cat_item['created'] = date('Y-m-d H:i:s');
      
         $pnewdetail = $this->StudentPurchasereturn->patchEntity($cat, $cat_item);
        // pr($pnewdetail); die;
      
         $branch_request_data = $this->StudentPurchasereturn->save($pnewdetail);
      
      
        if($branch_request_data){
      
        $temp_item = $this->TempStudentpurchasereturn->find('all')->contain(['Itemcategory'])->toarray();
        foreach($temp_item as $value){ 
      
        $item_data = $this->Additem->find('all')->contain(['Taxmaster'])->where(['Additem.id'=>$value['item_id']])->first();
          
         
          $branch_detail = $this->StudentPurchasereturnDetails->newEntity();
      
          $item_details['stupurchasereturn_id'] = $branch_request_data->id;
          $item_details['stu_id'] = $this->request->data['stu_name'];
          $item_details['item_id'] = $value['item_id'];
          $item_details['item_price'] = $item_data['sale_price'];
          $item_details['item_qty'] = $value['quantity'];
          $item_details['item_tax'] = $item_data['tax'];
          $item_details['hsncode'] = $item_data['item_isbn'];
          $item_details['description'] = $this->request->data['description'];
      
          $branch_details = $this->StudentPurchasereturnDetails->patchEntity($branch_detail, $item_details);
          $branch_request = $this->StudentPurchasereturnDetails->save($branch_details);
   

          $po_id =  $branch_request->stupurchasereturn_id;
          $purchase_order_id =  $branch_request->stupurchasereturn_id;
          $indent_id = $branch_request->stupurchasereturn_id;
          $item_id = $item_data['id'];
          $issue_date = date('Y-m-d H:i:s');
          $delivery_date = date('Y-m-d H:i:s');
          $qty = $value['quantity'];
  
          $rate = $item_data['sale_price'];
          $cost_price = $qty*$item_data['sale_price'];
          $tax_id = $item_data['taxmaster']['tax'];
          $tax =  $cost_price*$item_data['taxmaster']['tax']/100;
          $amount = sprintf('%.2f',$tax+$cost_price);
          $store_type = '6';
          $student_name  = $branch_request->stu_id;
          
          $stock_register_entry = ConnectionManager::get('default');
          $db = $this->request->session()->read('Auth.User.db');
          $stock_insert=  "INSERT INTO $db.st_stock_register (`po_id`,`purchaseorder_id`,`indent_id`,`item_id`,`issue_date`,`delivery_date`,`quantity`,`rate`,`cost_price`,`tax_id`,`tax`,`amount`,`store_type`,`student_id`) VALUES ('$po_id','$purchase_order_id','$indent_id','$item_id','$issue_date','$delivery_date','$qty','$rate','$cost_price','$tax_id','$tax','$amount','$store_type','$student_name')";
         // pr($stock_insert); die;
          $stock_register_entry->execute($stock_insert); 
        }
           }
           if($branch_request){
            $this->Flash->success(__('Item has been added successfully'));
            return $this->redirect(['action' => 'index']);	
          }else{
            $this->Flash->success(__('Somthing went wrong contact to administrator'));
      } 
    }
   }

//add item request in temporery table
public function itemrequest()
{
  //pr($this->request->data); die;
  $this->autoRender=false;
  $this->loadModel('TempStudentpurchasereturn');
  $dbname = $this->request->session()->read('Auth.User.db'); 
        $branch = explode("_",$dbname);
        if($dbname != $branch[0]){
          $this->connection(trim($branch[0]));
        }

  $this->loadModel('Additem');
  if ($this->request->is(['post'],['put'])) {

    $item_id = $this->request->data['item_id'];
    $cat_qty = $this->request->data['item_qty'];
    $item_idss = $this->request->data['item'];
    $item_exist = $this->TempStudentpurchasereturn->exists(['TempStudentpurchasereturn.item_id' => $item_idss]);
  
    if($item_exist){
      $this->Flash->success(__('Item already added'));
      echo '0'; die;
    }
  
    $items = $this->Additem->find('all')->where(['id' => $item_idss])->first();
    $sales = $this->TempStudentpurchasereturn->newEntity();
      
      $cat_item['category_id'] = $items['category_id'];
      $cat_item['category_name'] = $items['itemcategory']['id'];
      $cat_item['item_id'] = $items['id'];
      $cat_item['quantity'] = $cat_qty;
      $cat_item['item_price'] = $item_price;
      $cat_item['group_type']= '';


      $pnewdetail = $this->TempStudentpurchasereturn->patchEntity($sales, $cat_item);
      $resustnew = $this->TempStudentpurchasereturn->save($pnewdetail);
 
    if($resustnew){
      $this->Flash->success(__('Item has been added successfully'));
      echo "0";
      die;
    }else{
      $this->Flash->success(__('Somthing went wrong contact to administrator'));
      echo "1";
      die;
    }

  }

}


public function categoryrequest()
    {
	    //  $this->autoRender=false;

       $this->loadModel('TempStudentpurchasereturn');
       $dbname = $this->request->session()->read('Auth.User.db');
       $branch = explode("_", $dbname);
       if ($dbname != $branch[0]) {
   
         $this->connection(trim($branch[0]));
       }
      $this->loadModel('Itemcategory');
      $this->loadModel('Additem');
      $this->loadModel('Categorywise');

      if ($this->request->is(['post'],['put'])) {
        // echo $dbname; die;
        // $this->TempStudentpurchasereturn->deleteAll(['Tempsalereturn.branch_name'=>$dbname]);
        $cat_id = $this->request->data['category_id'];
        $session = $this->request->session();
        $session->write('category_id',$cat_id);
        $cat_qty = $this->request->data['category_qty'];
    
        
        $category = $this->Categorywise->find('all')->where(['category_id' => $cat_id])->order(['Categorywise.id' => 'Desc'])->toarray();

        foreach($category as $key=>$value){
          $item_data = $this->Additem->find('all')->where(['Additem.item_name'=>$value['item_name']])->first();
          $group_type = $this->Categorywise->find('all')->where(['Categorywise.id'=>$value['id']])->first();
      
          $cat = $this->TempStudentpurchasereturn->newEntity();
          $cat_item['category_id'] = $cat_id;
          $cat_item['item_id'] = $value['item_name'];
          $cat_item['category_name'] = $value['group_cat_name'];
          $cat_item['quantity'] = $cat_qty;
          $cat_item['branch_name'] = $branch_name;
          $cat_item['group_type'] = $group_type['group_type'];

          $pnewdetail = $this->TempStudentpurchasereturn->patchEntity($cat, $cat_item);
      
          $resustnew = $this->TempStudentpurchasereturn->save($pnewdetail); 
      
        }
        if($resustnew){
          $this->Flash->success(__('Item has been added successfully'));
          echo "0";
          die;
        }else{
          $this->Flash->success(__('Somthing went wrong contact to administrator'));
          echo "1";
          die;
        }
      }

    }
    public function delete($id){
        
        $this->loadModel('TempStudentpurchasereturn');
        $classes = $this->TempStudentpurchasereturn->get($id);
        //delete pariticular entry
        try { 
          if ($this->TempStudentpurchasereturn->delete($classes)) {
          $this->Flash->success(__(' You have deleted this record succesfully.', h($id)));
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
    
            $this->loadModel('TempStudentpurchasereturn');
           if($this->TempStudentpurchasereturn->deleteAll(['group_type' =>'Bottom'])){
            $this->Flash->success('TempStudentpurchasereturn Recored deleted successfully');
            return $this->redirect(['action' => 'add']);
           }else {
            $this->Flash->error('TempStudentpurchasereturn Recored not  delete successfully');
            return $this->redirect(['action' => 'add']);
          }
    
        }
    
    // topitemdelete function
        public function topitemdelete()
        {
          
         
          $this->loadModel('TempStudentpurchasereturn');
          if($this->TempStudentpurchasereturn->deleteAll(['group_type' =>'Top'])){
            $this->Flash->success('TempStudentpurchasereturn Recored deleted successfully');
            return $this->redirect(['action' => 'add']);
           }else {
            $this->Flash->error('TempStudentpurchasereturn Recored not  delete successfully');
            return $this->redirect(['action' => 'add']);
          }
        }
    
    // socksitemdelete function
        public function socksitemdelete()
        {
         
          $this->loadModel('TempStudentpurchasereturn');
          if($this->TempStudentpurchasereturn->deleteAll(['group_type' =>'Socks'])){
            $this->Flash->success('TempStudentpurchasereturn Recored deleted successfully');
            return $this->redirect(['action' => 'add']);
           }else {
            $this->Flash->error('TempStudentpurchasereturn Recored not  delete successfully');
            return $this->redirect(['action' => 'add']);
          }
        }
  

        public function viewdetail($id)
        {
          //pr($id); die;
          $this->set(compact('id'));
          $this->loadModel('StudentPurchasereturn');
          $this->loadModel('StudentPurchasereturnDetails');
          $this->loadModel('Students');
          $dbname = $this->request->session()->read('Auth.User.db'); 
          $branch = explode("_",$dbname);
          if($dbname != $branch[0]){
            
            $this->connection(trim($branch[0]));
      }
            $this->loadModel('Additem');
        

          $branch_request= $this->StudentPurchasereturn->find('all')->where(['id'=>$id])->order(['id' => 'Desc'])->first();
          $requestdetails= $this->StudentPurchasereturnDetails->find('all')->contain(['StudentPurchasereturn'=>['Students']])->where(['StudentPurchasereturnDetails.stupurchasereturn_id'=>$id])->toarray();
          //pr($requestdetails); die;
 
          
           $this->set(compact(['requestdetails','branch_request','item_data']));
        
        }


        public function billgenerate($id)
        {
          $this->response->type('pdf');
         
      
         $this->loadModel('StudentPurchasereturn');
         $this->loadModel('Sitesettings');
         $this->loadModel('SitesettingsDetails');
         $this->loadModel('Taxmaster');
         $this->loadModel('Additem');
         $this->loadModel('StudentPurchasereturnDetails');
         
         $dbname = $this->request->session()->read('Auth.User.db'); 
         $branch = explode("_",$dbname);
         if($dbname != $branch[0]){
           
           $this->connection(trim($branch[0]));
          }
          $this->loadModel('Companymaster');


    
    
         $tax_master=$this->Taxmaster->find('all')->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'Asc'])->toarray();
    
          $branch_request =$this->StudentPurchasereturnDetails->find('all')->contain(['StudentPurchasereturn'=>['Students']])->where(['StudentPurchasereturnDetails.stupurchasereturn_id'=>$id])->toarray();
      
    
          $sitesetting = $this->Sitesettings->find('all')->first();
          $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
          $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
          $company_master =$this->Companymaster->find('all')->where(['main_branch' => 'Y'])->order(['Companymaster.id' => 'DESC'])->first();
         //pr($branch_request); die;
       
            
          $this->set(compact(['branch_request','sitesetting','site_details','company_master','tax_master','id']));
        } 


        public function getitemdetail(){


          $dbname = $this->request->session()->read('Auth.User.db'); 
          $branch = explode("_",$dbname);
          if($dbname != $branch[0]){
            
            $this->connection(trim($branch[0]));
      }
            $this->loadModel('Additem');
          
            $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch']])->first(); 
        
            echo json_encode($unitid);
            die;
        }
        
        

}   