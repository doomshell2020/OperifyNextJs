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


class IndentController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Indenttemp');
        $this->loadModel('Itemcategory');
        $this->loadModel('Taxmaster'); 
        $this->loadModel('Users'); 
        $users=$this->Indent->find('all')->contain(['Users'])->select(['indent_id','added_time','Users.user_name'])->group(['indent_id'])->order(['Indent.id' => 'DESC'])->toarray();
       // pr($users); die;
        $this->set(compact('users'));

        

    }


    public function pendingindent(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Indenttemp');
        $this->loadModel('Itemcategory');
        $this->loadModel('Taxmaster'); 
        $this->loadModel('Users'); 
        $users=$this->Indent->find('all')->contain(['Users'])->select(['indent_id','added_time','Users.user_name'])->where(['Indent.indent_status'=>'P'])->group(['indent_id'])->order(['Indent.id' => 'DESC'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));


    }


    

    public function add($id=null){
        $this->viewBuilder()->layout('admin');       
        $this->loadModel('Indent');        
        $this->loadModel('Indenttemp');        
        $this->loadModel('Indentpreview');        
        $this->loadModel('Itemcategory');   
        $this->loadModel('Measurementunit');        
        $this->loadModel('Sizemanager');          
        $this->loadModel('Additem');   
        //pr($this->request->data); die;

        $userid = $this->request->session()->read('Auth.User.id');         

        // $unit = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->where(['Measurementunit.status' => 'Y'])->order(['Measurementunit.id' => 'asc'])->toarray();
        // // pr($location); die;
        // $this->set('units', $unit);     
        
        // $size = $this->Sizemanager->find('list', ['keyField' => 'id', 'valueField' => 'size_name'])->where(['Sizemanager.status' => 'Y'])->order(['Sizemanager.id' => 'asc'])->toarray();
        // // pr($location); die;
        // $this->set('size', $size);
        
        $indentid = $this->Indent->find('all')->select(['Indent.indent_id'])->order(['Indent.id' => 'Desc'])->first();
        //pr($indentid); die;
        if($indentid['indent_id'] != ""){
            $newindenttemp = $indentid['indent_id'] + 1;
        }else{
            $newindenttemp = "1001";
        }
        $this->set('newindenttemp', $newindenttemp);        
        
        if ($this->request->is(['post'])) {  
            //echo $id; die;
            //pr($this->request->data); die;            
            if($id != 0){
                $count = count($this->request->data['item_id']);
                //echo $count; die;
                $count -= 1;
                for($i=0; $i<=$count; $i++){

                $cat = $this->Indent->newEntity();  
                $item['indent_id'] = $this->request->data['indent'];
                $item['item_id'] = $this->request->data['item_id'][$i];
               // echo $item['item_id'];
                $saleprice = $this->Additem->find('all')->select(['cost_price'])->where(['Additem.id'=>$item['item_id']])->first();
                
                $item['sale_price'] = $saleprice['cost_price'];
                $item['quantity'] = $this->request->data['quant'][$i];
             // pr($item['quantity']); die;
                
                $item['size_id'] = $this->request->data['size_id'][$i];
                
                $salep = $item['cost_price'];    
                $quanti = $item['quantity'];        
                $item['amount'] = $salep * $quanti;                                                                                                   
                $item['added_time'] = date('Y-m-d H:i:s');                
                $item['added_by'] = $userid;    
                
                $pnewdetail = $this->Indent->patchEntity($cat, $item);
              //  pr($pnewdetail); die;
                if($resustnew = $this->Indent->save($pnewdetail)){
                    $this->Indenttemp->deleteAll(array('Indenttemp.indent_id' => $newindenttemp));
                    $indentpc = $this->Indentpreview->find('all')->where(['Indentpreview.indent_id'=>$newindenttemp])->count();
                    if($indentpc > 0){
                        $this->Indentpreview->deleteAll(array('Indentpreview.indent_id' => $newindenttemp));
                    }
                }
           // pr($resustnew);  
            } //die;

            $this->Flash->success(__('The Indent is successfully added.', h($id)));
            return $this->redirect(['action' => 'index']);
            }else{
                $count = count($this->request->data['item_id']);
                //echo $count; die;
                $count -= 1;
                $this->Indentpreview->deleteAll(array('Indentpreview.indent_id' => $newindenttemp));
                for($i=0; $i<=$count; $i++){
                    $cat = $this->Indentpreview->newEntity();                      
                    $item['indent_id'] = $this->request->data['indent'];
                    $item['item_id'] = $this->request->data['item_id'][$i];
                    $saleprice = $this->Additem->find('all')->select(['cost_price'])->where(['Additem.id'=>$item['item_id']])->first();
                    $item['sale_price'] = $saleprice['cost_price'];
                    $item['quantity'] = $this->request->data['quant'][$i];                    
                    $item['size_id'] = $this->request->data['size'][$i]; 
                    $salep = $item['cost_price'];
                    $quanti = $item['quantity'];
                    $item['amount'] = $salep * $quanti;                                                                                              
                    $item['added_time'] = date('Y-m-d H:i:s');                
                    $item['added_by'] = $userid;                
                    $item['status'] = "N";               
                    $pnewdetail = $this->Indentpreview->patchEntity($cat, $item);
                    $resustnew = $this->Indentpreview->save($pnewdetail);
                }
                return $this->redirect(['action' => 'view/', $newindenttemp]);
            } 
        
                          
        }
    }

    public function view($id=null){
      //echo $id; die;
        $this->loadModel('Indent');
        $this->loadModel('Indenttemp');
        $this->loadModel('Indenttemp');
        $this->loadModel('Indentpreview');
        $this->loadModel('Sizemanager');
        $this->loadModel('Measurementunit');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        $this->viewBuilder()->layout('ajax');


        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();  

        $this->set(compact(['sitesetting','site_details']));  

        $getindent = $this->Indent->find('all')->where(['Indent.indent_id'=>$id])->count(); 
        // if($getindent > 0){
        //     $users=$this->Indent->find('all')->contain(['Additem','Sizemanager'])->where(['Indent.indent_id'=>$id])->first(); 
        //     $this->set(compact('users'));   
        // }else{
        //     $users=$this->Indentpreview->find('all')->contain(['Additem','Sizemanager'])->where(['Indentpreview.indent_id'=>$id])->first(); 
        //     //pr($users); die;
        //     $this->set(compact('users'));
        // }
        $users=$this->Indent->find('all')->contain(['Additem'])->where(['Indent.indent_id'=>$id])->first(); 
       //  pr($users); die;
        $this->set(compact('users'));   
        $this->response->type('pdf');
    }
    

            
  

 

    public function status($id,$status){
        $this->loadModel('Indent');
        if(isset($id) && !empty($id)){
            if($status =='Y' ){
                
                $status = 'N';
                $user = $this->Indent->get($id);

                $user->status = $status;
                if ($this->Indent->save($user)) {
                    $this->Flash->success(__('Item status has been updated.'));
                    return $this->redirect(['action' => 'index']);  
                }

            }else{
                $status = 'Y';
                $user = $this->Indent->get($id);
                $user->status = $status;
                if ($this->Indent->save($user)) {
                    $this->Flash->success(__('Item status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id=null){ 
        $this->loadModel('Indent');

                        //pr($this->Users->get($id)); die;
        try {                
            $user = $this->Indent->get($id);
            if ($this->Indent->delete($user)) {

                // $delete_record = 'DELETE FROM `st_locationmaster` WHERE `id` =' . $id;
                // $conn->execute($delete_record);
                $this->Flash->success(__('The Item with id: {0} has been deleted.', h($id)));
                return $this->redirect(['action' => 'index']);
            }
        }
        catch (\PDOException $e) {

            $this->Flash->error(__('This Item is used so you cannot delete this Item location detail'));
            $this->set('error', $error);
            return $this->redirect(['action' => 'index']);
        }
        die;
    

    }


    public function getsubcategory(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Itemcategory');
        //pr($this->request->data['dataString']); die;
        $id=$this->request->data['dataString'];
        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent' => $id, 'Itemcategory.status' => 'Y'])->order(['Itemcategory.id' => 'asc'])->toarray();

        //pr($categary); die;
        //$this->set(compact('users'));

        header('Content-Type: application/json');
        echo json_encode($categary);
        die;
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

    public function indenttemp(){
        $this->loadModel('Indent');
        $this->loadModel('Indenttemp');
        $this->loadModel('Itemcategory');
        $this->loadModel('Itemlocation');
        $this->loadModel('Measurementunit');
        $this->loadModel('Companymaster');
        $this->loadModel('Additem');
        $this->loadModel('Sizemanager');
        $this->loadModel('Taxmaster');
        //pr($this->request->data);
        $srno = $this->request->data['srno'];
        $this->set(compact('srno'));
        $userid = $this->request->session()->read('Auth.User.id');  
        $itemname = $this->Additem->find('all')->select(['item_name', 'sale_price', 'tax', 'id'])->where(['Additem.id' => $this->request->data['item_id']])->first(); 
        //$unitname = $this->Measurementunit->find('all')->select(['unit_name', 'id'])->where(['Measurementunit.id' => $this->request->data['unit_id']])->first();
        // $unit = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->where(['Measurementunit.status' => 'Y'])->order(['Measurementunit.id' => 'asc'])->toarray();        
        // $this->set('units', $unit); 

        // $size = $this->Sizemanager->find('list', ['keyField' => 'id', 'valueField' => 'size_name'])->where(['Sizemanager.status' => 'Y'])->order(['Sizemanager.id' => 'asc'])->toarray();
        // $this->set('size', $size); 
        // pr($location); die;
        
        //pr($this->request->data);
        $cat = $this->Indenttemp->newEntity();
        if ($this->request->is(['post'])) {        
            //pr($this->request->data); die;
            $item['indent_id'] = $this->request->data['indent_id'];
            $item['item_id'] = $this->request->data['item_id'];
            $item['size_id'] = $this->request->data['size_id'];
            $item['sale_price'] = $itemname['sale_price'];
            $item['quantity'] = $this->request->data['quantity'];  
            $quantitys = $item['quantity'];
            $tprice = $itemname['sale_price'];
            $unitamount = $tprice / $quantitys;
            $item['unit_amt'] = $unitamount;            
            $totalamount = $tprice * $quantitys;
            $item['amount'] = $totalamount;
            //echo $item['amount']; die;
            $item['added_time'] = date('Y-m-d H:i:s');
            $item['added_by'] = $userid;
            //pr($item); die;
            $pnewdetail = $this->Indenttemp->patchEntity($cat, $item);
            $resustnew = $this->Indenttemp->save($pnewdetail);
            if($resustnew['size_id'] != ""){
                $indentdetail = $this->Indenttemp->find('all')->contain(['Additem','Sizemanager'])->where(['Indenttemp.id' => $resustnew['id']])->first();
                //pr($indentdetail); die;
            }else{
                $indentdetail = $this->Indenttemp->find('all')->contain(['Additem'])->where(['Indenttemp.id' => $resustnew['id']])->first();
            }
            $this->set('indentdetail', $indentdetail);

            // $taxc = explode(',',$indentdetail['additem']['tax']);
            // $tax = $this->Taxmaster->find('all')->where(['Taxmaster.id IN'=>$taxc])->order(['Taxmaster.id' => 'Asc'])->toarray();
            //$this->set(compact('tax'));
        }


        //die;

    }

    public function removeindenttemp(){
        $this->loadModel('Indent');
        $this->loadModel('Indenttemp');
        $this->loadModel('Itemcategory');
        $this->loadModel('Companymaster');
        $this->loadModel('Itemname');
        $this->loadModel('Measurementunit');
        //pr($this->request->data);
        $id = $this->request->data['indent_id'];
        $user = $this->Indenttemp->get($id);
        if ($this->Indenttemp->delete($user)) {
            echo "1";
        }else{
            echo "0";
        }

        die;
    }


    public function getitemdetail(){
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        //pr($this->request->data);
        
        $unitid = $this->Additem->find('all')->select(['id'])->where(['Additem.id' => $this->request->data['fetch']])->first(); 
        $unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $unitid['id']])->first(); 
        //pr($itemname);
        echo $unitname['unit_name'];
        die;
    }



    public function searchitem()
    {
      $this->loadModel('Indent'); 
      
    $indent_id = $this->request->data['indent_id'];
    $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
    $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));

    $cond = [];
    if (!empty($indent_id)) {
      $indent = ['Indent.indent_id' => $indent_id];
      $cond[] = $indent;
    }
   
    if ($datefrom !== '1970-01-01') {
      $indent = ['DATE(Indent.added_time) >=' => $datefrom];
      $cond[] = $indent;
    }

    if ($dateto2 !== '1970-01-01') {
      $indent = ['DATE(Indent.added_time) <=' => $dateto2];
      $cond[] = $indent;
    }
      $user = $this->Indent->find('all')->where([$cond])->order(['Indent.id' =>'Desc'])->toarray();
      $this->set('users',$user); 

   }
    



}
