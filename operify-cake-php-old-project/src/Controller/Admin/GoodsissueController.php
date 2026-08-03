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


class GoodsissueController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	//~ public function index(){         
        //~ $this->viewBuilder()->layout('admin');  
          //~ $this->loadModel('Goodsissue');   
          //~ if($this->request->session()->read('openfess_recipt3')){
					
			
			
			//~ $ids3=$this->request->session()->read('openfess_recipt3');
			//~ $ids4=$this->request->session()->read('openfess_recipt4');
			        //~ $this->set(compact('ids3'));
			        //~ $this->set(compact('ids4'));
			        	//~ $this->request->session()->delete('openfess_recipt3');
			//~ $this->request->session()->delete('openfess_recipt4');
		//~ }  
        //~ $goodsreceived=$this->Goodsreceived->find('all')->order(['Goodsreceived.id'=>'Asc'])->toarray();
        //~ //pr($users); die;
        //~ $this->set(compact('goodsreceived'));    
        
    //~ }

    public function add($id=null){
        $this->viewBuilder()->layout('admin');       
        $this->loadModel('Purchaseorder');        
        $this->loadModel('Purchaseordertemp');        
        $this->loadModel('Purchaseorderitem');        
        $this->loadModel('Itemcategory');   
        $this->loadModel('Measurementunit');        
        $this->loadModel('Companymaster');        
        $this->loadModel('Itemname');   
        $this->loadModel('Suppliers');   
        $this->loadModel('Stockregister');   
        $this->loadModel('Goodsreceived');   
        $this->loadModel('Indent'); 
if($id){
    $this->set('id', $id);  

    $indent = $this->Indent->find('list', ['keyField' => 'indent_id', 'valueField' => 'indent_id'])->group(['indent_id'])->where(['Indent.indent_status'=>'P','Indent.indent_id'=>$id])->order(['Indent.id' => 'asc'])->toarray();
    //pr($indent); die;
    



}else{

    $indent = $this->Indent->find('list', ['keyField' => 'indent_id', 'valueField' => 'indent_id'])->group(['indent_id'])->where(['Indent.indent_status'=>'P'])->order(['Indent.id' => 'asc'])->toarray();
    //pr($indent); die;


}

$this->set('indent', $indent);  


        $purchaseorderid = $this->Purchaseorder->find('list', ['keyField' => 'purchaseorder_id', 'valueField' => 'purchaseorder_id'])->where(['Purchaseorder.status !='=>'N'])->order(['Purchaseorder.id' => 'desc'])->toarray();
     
        $this->set('purchaseorderid', $purchaseorderid);
        
         $goodsrecived = $this->Goodsreceived->newEntity();
  
        if ($this->request->is(['post'])) { 
     
            $count = count($this->request->data['pitemname']);
            $count -= 1;
            if($this->request->data['tqty']==0){

                $this->Flash->error(__("We have doesn't able to issue Indent item! "));
               
                return $this->redirect(['controller' => 'indent','action' => 'index']);


            }
           


            for($i=0; $i<=$count; $i++){
				 if($this->request->data['pitemrate'][$i]!=0){
                    $qtyy=0;
                    $total_qty=0;
                // Calculate entry of Purchase Order in Stock Register
               $qtyy =$this->request->data['pitemrate'][$i];
               $indent_id =$this->request->data['indent_id'][$i];
               $pitemid =$this->request->data['pitemname'][$i];
               $indent = $this->Indent->find('all')->where(['Indent.indent_status'=>'P','Indent.indent_id'=>$indent_id,'Indent.item_id'=>$pitemid])->order(['Indent.id' => 'asc'])->first();
               $item['id']=$indent['id'];
               $total_qty=intval($indent['quantity'])-intval($indent['return_qty']);
               
            


               if($total_qty > $qtyy){
                $item['indent_status'] = 'P';
     
               }else{ 
                $item['indent_status']  = 'C';
               }
               $item['return_qty']=intval($qtyy)+intval($indent['return_qty']);
               $cat = $this->Indent->get($item['id']);


               $pnewdetail = $this->Indent->patchEntity($cat, $item);
              
               $this->Indent->save($pnewdetail);


			}
            }
                         
      
          

           
			
	
            for($i=0; $i<=$count; $i++){
				 if($this->request->data['pitemrate'][$i]!=0){
                    $indent_id =$this->request->data['indent_id'][$i];
                    $pitemid =$this->request->data['pitemname'][$i];
                  

      $Stockregister = $this->Stockregister->find('all')->where(['Stockregister.indent_id '=>$indent_id,'Stockregister.item_id '=>$pitemid,'Stockregister.store_type'=>0])->order(['Stockregister.id' => 'desc'])->first();

      if($Stockregister){
        $newsrentity['po_id'] = $Stockregister['po_id'];  
        $newsrentity['purchaseorder_id'] = $Stockregister['purchaseorder_id'];  
        $newsrentity['amount'] = 0;  
        $newsrentity['rate'] = $Stockregister['rate'];  
        $Stockregister['cost_price']=$this->request->data['pitemrate'][$i]*$Stockregister['rate'];
        $newsrentity['cost_price'] = $Stockregister['cost_price'];  
        $newsrentity['tax_id'] = 0;  
        $newsrentity['tax'] = 0;  

      }else{
        $newsrentity['po_id'] = 0;  
        $newsrentity['purchaseorder_id'] = 0;  
        $newsrentity['amount'] =0;
        $newsrentity['rate'] = 0;
        $newsrentity['cost_price'] = 0;
        $newsrentity['tax_id'] = 0;
        $newsrentity['tax'] = 0;



      }
                
                // New entry of Purchase Order in Stock Register
                $newsr = $this->Stockregister->newEntity();
           
                $newsrentity['indent_id'] = $this->request->data['indent_id'][$i];  
                $newsrentity['item_id'] = $this->request->data['pitemname'][$i];
                $newsrentity['quantity'] = $this->request->data['pitemrate'][$i];
                $newsrentity['issue_date'] = date('Y-m-d');
                 $newsrentity['delivery_date'] = date('Y-m-d');
                $newsrentity['central_store_id'] = '0';
                $newsrentity['central_store_type'] = '0';
                $newsrentity['store_id'] = '0';
                $newsrentity['store_type'] = '2';
                $newsrentity['store_quantity'] = '0';
                $newsrentity['student_id'] = '0';
               

                $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                $ponewsr = $this->Stockregister->save($podetail);
			}
            }
				
				$this->Flash->success(__('The Indent Status Updated Successfully !'));
               
                return $this->redirect(['controller' => 'indent','action' => 'index']);
            
            
        }
        
        
        
    }


    public function indentitems(){       
        $this->loadModel('Indent');
        $this->loadModel('Additem');
        $this->loadModel('Taxmaster');
        $indentid = $this->request->data['fetch'];
        $itemname = $this->Additem->find('list', ['keyField' => 'id', 'valueField' => 'item_name'])->where(['Additem.status'=>'Y'])->order(['Additem.id' => 'asc'])->toarray();
        $this->set('itemname', $itemname);

        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax_name'])->where(['Taxmaster.status'=>'Y','Taxmaster.parent'=>'0'])->order(['Taxmaster.id' => 'asc'])->toarray();
        //pr($tax); die;
        $this->set('tax', $tax);

        $indent = $this->Indent->find('all')->contain(['Additem'])->where(['Indent.indent_id IN'=>$indentid,'Indent.indent_status'=>'P'])->order(['Indent.id'=>'Asc'])->toarray();
        //pr($indent);
        $this->set('indent', $indent);
       
    }

      public function view($id=null){
        $this->loadModel('Purchaseorder');
        $this->loadModel('Stockregister');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendors');
        $this->loadModel('Goodsreceived');
        $this->viewBuilder()->layout('ajax');

        // //echo $id; 
        $users=$this->Goodsreceived->find('all')->where(['Goodsreceived.id'=>$id])->order(['Goodsreceived.id' => 'DESC'])->first();
        
        $sup=$this->Vendors->find('all')->select(['name','contact_no','email'])->where(['Vendors.id'=>$users['vendor_id']])->first();
        
        $puritems = $this->Stockregister->find('all')->contain(['Additem'])->where(['Stockregister.goods_id' =>$id,'Stockregister.status !=' => 'N','Stockregister.store_type' => '1'])->toarray();
        //pr($puritems); die;

        
        $this->set(compact('users','sup','puritems','co'));
        $this->response->type('pdf');
    }

    public function getpurchaseorderid(){       
        $this->loadModel('Purchaseorder');
        //pr($this->request->data); die;
        $stsearch=$this->request->data['fetch'];        
        $searchst=$this->Vendor->find('all')->select(['name','id'])->where(['Vendor.name LIKE'=>$stsearch.'%'])->toarray();
        //pr($searchst); die;
        foreach($searchst as $value){ 
            echo '<li onclick="cllbckretail('."'".$value['name']."'".','."'".$value['id']."'".','."'".$i."'".')"><a href="javascript:void(0)">'.$value['name'].'</a></li>';
        }
        die; 
    }
   

    public function goodsrecieve($id=null){
        $this->loadModel('Indent');
        $this->loadModel('Indenttemp');
        $this->viewBuilder()->layout('ajax');

        //echo $id; 
        $users=$this->Indent->find('all')->contain(['Itemcategory','Companymaster','Itemname','Measurementunit'])->where(['Indent.indent_id'=>$id])->toarray();
        $this->set(compact('users'));
        $this->response->type('pdf');
    }



}
