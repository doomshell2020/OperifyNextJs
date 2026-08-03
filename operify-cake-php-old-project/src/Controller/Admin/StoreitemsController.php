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


class StoreitemsController extends AppController
{

public function index()
{
    $this->viewBuilder()->layout('admin');        
    $this->loadModel('Storeitem');
    $this->loadModel('Vendor');
    $store=$this->Storeitem->find('all')->order(['Storeitem.id' => 'Desc'])->toarray();
    $this->set(compact('store'));
    $supplier=$this->Vendor->find('all', ['keyField' => 'id', 'valueField' => 'name'])->order(['Vendor.id' => "Asc"])->toarray();
    //pr($supplier); die;
    $this->set('supplier', $supplier);

    $suppliers=$this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['Vendor.id' => "Asc"])->toarray();
    //pr($supplier); die;
    $this->set('suppliers', $suppliers);


}
//add store items function
public function viewdetail($id)
{
    $this->loadModel('Storeitem');
    $store=$this->Storeitem->find('all')->where(['Storeitem.id'=>$id])->order(['Storeitem.id' => 'Desc'])->first();
    $this->set(compact('store'));
}
public function add()
{
    $this->viewBuilder()->layout('admin');        
    $this->loadModel('Storeitem');
    $this->loadModel('Vendor');
    $this->loadModel('Additem');
    $this->loadModel('Stockregister');
    $this->loadModel('Purchaseorder');
    $supplier=$this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
    $this->set('supplier', $supplier);
    //pr($supplier); die;

    if ($this->request->is(['post'],['put'])) {
        //pr($this->request->data); //die;
            $item_name = $this->Storeitem->find('all')->where(['Storeitem.item_id'=>$this->request->data['item_id']])->first();  
           // pr($item_name); die;
            if($item_name){
                $this->Flash->error(__('Item has been already in your store.'));
                return $this->redirect(['action' => 'index']);
            }
//pr($this->request->data);
              $cat = $this->Storeitem->newEntity();
              $item['item_id'] = $this->request->data['item_id'];           
              $item['item_name'] = $this->request->data['item_name']; 
              $item['mrp_price'] = $this->request->data['mrp_price'];
              $item['cost_price'] = $this->request->data['cost_price'];                
              $item['sale_price'] = $this->request->data['sale_price'];
              $item['quantity'] = $this->request->data['quantity'];
              $item['supplier_name'] = $this->request->data['supplier_name'];
              $item['min_stock'] = $this->request->data['min_stock'];
              $item['max_stock'] = $this->request->data['max_stock'];
              $item['hsn_isbn'] = $this->request->data['item_isbn'];
              $item['description'] = $this->request->data['description'];
              $item['expiry_date'] = date('Y-m-d', strtotime($this->request->data['expiry_date']));
             // pr($item); die;

              $pnewdetail = $this->Storeitem->patchEntity($cat, $item);
              if($resustnew = $this->Storeitem->save($pnewdetail)){ 
              // Purchase order new entity
                $po = $this->Purchaseorder->newEntity();
                $purchaseorderid = $this->Purchaseorder->find('all')->order(['Purchaseorder.purchaseorder_id' => 'Desc'])->first();
                
                if($purchaseorderid['purchaseorder_id'] != ""){
                    $newpurchaseordertemp = $purchaseorderid['purchaseorder_id'] + 1;
                }else{
                    $newpurchaseordertemp = "200001";
                }
          

                $po_item['purchaseorder_id'] =  $newpurchaseordertemp;
                $po_item['vendor_id'] =  $this->request->data['supplier_name'];
                $po_item['delivery_date'] = date('Y-m-d');
                $po_item['total_qty'] = $this->request->data['quantity'];
                $totalamount = $this->request->data['quantity'] *$this->request->data['mrp_price'];
                $tax_amount = $totalamount*18/100;
                $po_item['total_tax'] = $tax_amount;
                $po_item['total_amt'] = $totalamount;
                $ponewdetail = $this->Purchaseorder->patchEntity($po, $po_item);
                if($po_result = $this->Purchaseorder->save($ponewdetail)){

              $stock = $this->Stockregister->newEntity();
               
              $stock_item['po_id'] = $po_result['id'];
              $stock_item['purchaseorder_id'] = $po_result['purchaseorder_id'];;  
              $stock_item['goods_id'] = $item_name['id'];
              //$stock_item['indent_id'] = '--';
              $stock_item['item_id'] = $this->request->data['item_id'];
              $stock_item['created'] = date('Y-m-d');
              $stock_item['issue_date'] = date('Y-m-d');
              $stock_item['delivery_date'] = date('Y-m-d');
              $stock_item['quantity'] = $this->request->data['quantity'];
              $stock_item['rate'] = $this->request->data['cost_price'];
              $stock_item['cost_price'] = $this->request->data['cost_price'];
              $stock_item['sale_price'] = $this->request->data['sale_price'];
              $stock_item['tax_id'] = '17';
              $stock_item['tax'] = $tax_amount;
              $stock_item['amount'] = $totalamount;
              $stock_item['store_type'] = '1';
              $stock_item['status'] = 'Y';
              $stocknewdetail = $this->Stockregister->patchEntity($stock, $stock_item);
              $this->Stockregister->save($stocknewdetail); 
              }
            }
//die;
          $this->Flash->success(__('Item name successfully updated.'));
          return $this->redirect(['action' => 'index']);
      }  
}

//storitem edit function
public function edit($id)
{
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Storeitem');
    $this->loadModel('Vendor');
    $this->loadModel('Additem');
    $this->loadModel('Stockregister');
    $this->loadModel('Purchaseorder');

    $supplier=$this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
    $this->set('supplier', $supplier);
    
    $itemcat = $this->Storeitem->find('All')->where(['Storeitem.id' => $id])->first();
    $this->set('store', $itemcat);

    $cat = $this->Storeitem->get($id);

    if ($this->request->is(['put'])) {  
    
          $item['expiry_date'] = date('Y-m-d', strtotime($this->request->data['expirydd']));
           $item['item_name'] = $this->request->data['item_name'];            
              $item['mrp_price'] = $this->request->data['mrp_price'];
              $item['cost_price'] = $this->request->data['cost_price'];                
              $item['sale_price'] = $this->request->data['sale_price'];
              $item['quantity'] = $this->request->data['quantity'];
              $item['supplier_name'] = $this->request->data['supplier_name'];
              $item['min_stock'] = $this->request->data['min_stock'];
              $item['max_stock'] = $this->request->data['max_stock'];
              $item['hsn_isbn'] = $this->request->data['hsn_isbn'];
           
               $item['description'] = $this->request->data['description'];

               $save = $this->Storeitem->patchEntity($cat, $item);
       
       
         if ($resust = $this->Storeitem->save($save)){


                          // Purchase order new entity
                          $po = $this->Purchaseorder->newEntity();
                          $purchaseorderid = $this->Purchaseorder->find('all')->order(['Purchaseorder.purchaseorder_id' => 'Desc'])->first();
                          
                          if($purchaseorderid['purchaseorder_id'] != ""){
                              $newpurchaseordertemp = $purchaseorderid['purchaseorder_id'] + 1;
                          }else{
                              $newpurchaseordertemp = "200001";
                          }
                    
          
                          $po_item['purchaseorder_id'] =  $newpurchaseordertemp;
                          $po_item['vendor_id'] =  $this->request->data['supplier_name'];
                          $po_item['delivery_date'] = date('Y-m-d');
                          $po_item['total_qty'] = $this->request->data['quantity'];
                          $totalamount = $this->request->data['quantity'] *$this->request->data['mrp_price'];
                          $tax_amount = $totalamount*18/100;
                          $po_item['total_tax'] = $tax_amount;
                          $po_item['total_amt'] = $totalamount;
                          $ponewdetail = $this->Purchaseorder->patchEntity($po, $po_item);
                          if($po_result = $this->Purchaseorder->save($ponewdetail)){
          
                        $stock = $this->Stockregister->newEntity();
                         
                        $stock_item['po_id'] = $po_result['id'];
                        $stock_item['purchaseorder_id'] = $po_result['purchaseorder_id'];;  
                        $stock_item['goods_id'] = '--';
                        //$stock_item['indent_id'] = '--';
                        $stock_item['item_id'] = $this->request->data['item_id'];
                        $stock_item['created'] = date('Y-m-d');
                        $stock_item['issue_date'] = date('Y-m-d');
                        $stock_item['delivery_date'] = date('Y-m-d');
                        $stock_item['quantity'] = $this->request->data['quantity'];
                        $stock_item['rate'] = $this->request->data['cost_price'];
                        $stock_item['cost_price'] = $this->request->data['cost_price'];
                        $stock_item['sale_price'] = $this->request->data['sale_price'];
                        $stock_item['tax_id'] = '17';
                        $stock_item['tax'] = $tax_amount;
                        $stock_item['amount'] = $totalamount;
                        $stock_item['store_type'] = '1';
                        $stock_item['status'] = 'Y';
                        $stocknewdetail = $this->Stockregister->patchEntity($stock, $stock_item);
                        $this->Stockregister->save($stocknewdetail); 
                        }



             $this->Flash->success(__('Item name  successfully updated.'));
             return $this->redirect(['action' => 'index']);
         } 

        }

        
}



public function getitemname(){       
    $this->loadModel('Additem');
    $this->loadModel('Sizemanager');
    //pr($this->request->data); die;
    $stsearch=$this->request->data['fetch'];
    $check=$this->request->data['check'];
    //echo $stsearch; die;       
    $searchst=$this->Additem->find('all')->where(['Additem.item_name LIKE'=>$stsearch.'%','Additem.status'=>'Y'])->toarray();
    //pr($searchst); die;        
    
    foreach($searchst as $value){ 
        if($value['size_id'] != ""){
            $sizedetail = $this->Sizemanager->find('all')->select(['id','size_name'])->where(['Sizemanager.id'=>$value['size_id']])->first();
            //pr($sizedetail); die
            if($sizedetail['id'] ==6){
                 if($check == 0){
                echo '<li onclick="cllbckretail('."'".$value['item_name']."'".','."'".$value['id']."'".','."'".$value['size_id']."'".','."'".$i."'".')"><a href="javascript:void(0)">'.$value['item_name'].'</a></li>';
            }else{
                echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail'.$check.'('."'".$value['item_name']."'".','."'".$value['size_id']."'".','."'".$value['id']."'".','."'".$i."'".')"><a href="javascript:void(0)" style="color: black;">'.$value['item_name'].'</a></li>';
            } 
                
            }else{ 
                
                if($check == 0){
                echo '<li onclick="cllbckretail('."'".$value['item_name'].' ('.$sizedetail['size_name'].')'."'".','."'".$value['id']."'".','."'".$value['size_id']."'".','."'".$i."'".')"><a href="javascript:void(0)">'.$value['item_name'].' ('.$sizedetail['size_name'].')'.'</a></li>';
            }else{
                echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail'.$check.'('."'".$value['item_name'].' ('.$sizedetail['size_name'].')'."'".','."'".$value['id']."'".','."'".$value['size_id']."'".','."'".$i."'".')"><a href="javascript:void(0)" style="color: black;">'.$value['item_name'].' ('.$sizedetail['size_name'].')'.'</a></li>';
            } 
        }
        }else{
            if($check == 0){
                echo '<li onclick="cllbckretail('."'".$value['item_name']."'".','."'".$value['id']."'".','."'".$i."'".')"><a href="javascript:void(0)">'.$value['item_name'].'</a></li>';
            }else{
                echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail'.$check.'('."'".$value['item_name']."'".','."'".$value['id']."'".','."'".$i."'".')"><a href="javascript:void(0)" style="color: black;">'.$value['item_name'].'</a></li>';
            } 
        }
    }

    die; 
}
public function getitemdetail($id){
    $this->loadModel('Additem');
    $this->loadModel('Measurementunit');
    $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch']])->first(); 

    echo json_encode($unitid);
    die;
}

//Storitem status function
public function status($id,$status){
    $this->loadModel('Storeitem');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){
            
            $status = 'N';
            $user = $this->Storeitem->get($id);

            $user->status = $status;
            if ($this->Storeitem->save($user)) {
                $this->Flash->success(__('Store Item status has been Deactive.'));
                return $this->redirect(['action' => 'index']);  
            }
            
        }else{
            $status = 'Y';
            $user = $this->Storeitem->get($id);
            $user->status = $status;
            if ($this->Storeitem->save($user)) {
                $this->Flash->success(__('Store Item status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}


//store item delete function

   public function delete($id)
   {
    $this->loadModel('Storeitem');
    $supplier=$this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
    $this->set('supplier', $supplier);
   

    $res = $this->Storeitem->get($id);
       
        
    if ($this->Storeitem->delete($res)) {

        $this->Flash->success('Store Item deleted successfully');
        return $this->redirect(['controller' => 'Storeitems', 'action' => 'index']);
    } else {
        $this->Flash->error('Store Item not  delete successfully');
        return $this->redirect(['controller' => 'Storeitems', 'action' => 'index']);
    }
}


    public function searchitem()
    {
    $this->loadModel('Storeitem');
    $this->loadModel('Vendor');

    $supplier=$this->Vendor->find('all', ['keyField' => 'id', 'valueField' => 'name'])->order(['Vendor.id' => "Asc"])->toarray();
   
    $this->set('supplier', $supplier); 
    
    $supplier = $this->request->data['supplier_name'];
   // pr($supplier); die;
    $cond = []; 
  
    if(isset($supplier) && $supplier!='')
     {
    $cond['Storeitem.supplier_name']=$supplier;

    //pr($cond); die;
     }
 

     $stores=$this->Storeitem->find('all')->Where([$cond])->order(['Storeitem.id' => 'Desc'])->toarray();
     $this->set('store', $stores);
    

    }


}