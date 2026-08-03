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
use Cake\Routing\Router;


class CategorywiseController extends AppController
{
	

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Categorywise');
        $this->loadModel('Additem');
        $this->loadModel('Itemcategory');
        $category = $this->Categorywise->find('all')->contain(['Additem','Itemcategory'])->order(['Categorywise.id' => 'Desc'])->group(['category_name'])->toarray();
        $this->set('category_wise',$category);
       

    }

    public function getitemdetail(){
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        //pr($this->request->data); //die;
        
        $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch']])->first(); 
        //$unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $unitid['unit_id']])->first(); 
        //pr($itemname);
      //  pr($unitid);  die;
       echo $unitid;
        die;
    }

    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Categorywise');
        $this->loadModel('Itemcategory');
        $categarys = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['category_name NOT IN' => 'School Item'])->toarray();
        $this->set('categary', $categarys);
        $cat = $this->Categorywise->newEntity();
        if ($this->request->is(['post'])) {
            
            //  pr($this->request->data); die;
           // $item['group_cat_name'] = $this->request->data['group_cat_name'];
            $item['item_name'] = $this->request->data['item_id'];
            $item['quantity'] = $this->request->data['quantity'];
            $item['discount_type'] = $this->request->data['discount_type'];
            $item['discount'] = $this->request->data['discount'];
            $item['group_type'] = $this->request->data['group_type'];
            $item['category_id'] = $this->request->data['category_name'];
            $pnewdetail = $this->Categorywise->patchEntity($cat, $item);
 // pr($pnewdetail); die;
            if ($resustnew = $this->Categorywise->save($pnewdetail)) {
                $this->Flash->success(__('category wise successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
           
        }
    }

   public function edit($id){

    $this->viewBuilder()->layout('admin');
    $this->loadModel('Categorywise');
    $this->loadModel('Additem');
    $this->loadModel('Itemcategory');
    $this->loadModel('Taxmaster');



    $cat = $this->Categorywise->get($id);
 
    $this->set('item', $cat);

    $category = $this->Categorywise->find('all')->where(['Categorywise.category_id'=>$cat['category_id']])->contain(['Additem'=>['Taxmaster'],'Itemcategory'])->toarray();
    $this->set('category_wise',$category);


    $categarys = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['category_name NOT IN' => 'School Item'])->toarray();
    $this->set('categary', $categarys);


    $item_details = $this->Categorywise->newEntity();
    if ($this->request->is(['post','put'])) {
        
        $item['group_cat_name'] =$this->request->data['group_cat_name'];
        $item['item_name'] = $this->request->data['item_id'];
        $item['quantity'] = $this->request->data['quantity'];
        $item['discount_type'] = $this->request->data['discount_type'];
        $item['discount'] = $this->request->data['discount'];
        $item['group_type'] = $this->request->data['group_type'];
        $item['category_id'] = $this->request->data['category_name'];

        $item_cat = $this->Categorywise->patchEntity($item_details, $item);
        if ($resustnew = $this->Categorywise->save($item_cat)) {
        $this->Flash->success(__('category wise successfully added.'));
        return $this->redirect(['action' => 'edit/'.$id]);
            }
    }
}

 public function delete($id)
 {
    
     $this->loadModel('Categorywise');
          $this->autoRender = false;
          $res = $this->Categorywise->find('all')->Where(['category_id'=>$id])->toarray();
          foreach($res as $key=>$value){
           
         $delete_all[]=$value['id'];
         
        }
          $user=$delete_all;
          $condition = array('Categorywise.id in' => $user);

         if ($this->Categorywise->deleteAll($condition,false)) {

             $this->Flash->success('Categorywise Recored deleted successfully');
             return $this->redirect(['action' => 'index']);
         }else {
             $this->Flash->error('Categorywise Recored not  delete successfully');
             return $this->redirect(['action' => 'index']);
         }
  }

        public function subdelete($id)
        {
         $this->loadModel('Categorywise');
         $this->autoRender = false;
         $res = $this->Categorywise->get($id);
        //  pr($res); die;
        if ($this->Categorywise->delete($res)) {
          
            $this->Flash->success('Categorywise Recored deleted successfully');
            return $this->redirect( Router::url( $this->referer(), true ));
        }else {
            $this->Flash->error('Categorywise Recored not  delete successfully');
            return $this->redirect( Router::url( $this->referer(), true ));
        }
        }


        public function searchitem()
        {
            $this->loadModel('Itemcategory');

            $name = $this->request->data['category_name'];
            $cond = [];   
            if(isset($name) && $name!='')
           {
            $cond['Itemcategory.category_name LIKE']='%'.trim($name).'%';	
            }
     
          $users = $this->Itemcategory->find('all')->where([$cond,'category_name NOT IN' => 'School Item'])->order(['Itemcategory.id' => 'Desc'])->toarray();
          $this->set('user', $users);
     
     
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
    
 }


     