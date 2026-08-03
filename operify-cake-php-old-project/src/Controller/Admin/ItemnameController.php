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


class ItemnameController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Itemname');
        $this->loadModel('Itemcategory');
        $this->loadModel('Taxmaster'); 
        $users=$this->Itemname->find('all')->contain(['Maincategory','Subcategory','Mainlocation','Sublocation','Measurementunit','Companymaster'])->order(['Itemname.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));

        $checkbox = explode(',', $users['0']['tax']);
        //pr($checkbox); die;
        $taxvalue = $this->Taxmaster->find('all')->where(['Taxmaster.id IN'=>$checkbox])->toarray();
        //pr($tax); die;
        $this->set(compact('taxvalue'));


    }

    public function add(){
        $this->viewBuilder()->layout('admin');       
        $this->loadModel('Itemname');        
        $this->loadModel('Itemcategory');        
        $this->loadModel('Itemlocation');        
        $this->loadModel('Measurementunit');        
        $this->loadModel('Companymaster');        
        $this->loadModel('Taxmaster');        
        $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent'=>0, 'Itemcategory.status'=>'Y'])->order(['Itemcategory.id' => 'asc'])->toarray();
        //pr($categary); die;
        $this->set('categary', $categary);

        $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent'=>0, 'Itemlocation.status'=>'Y'])->order(['Itemlocation.id' => 'asc'])->toarray();
       // pr($location); die;
        $this->set('locations', $location);

        $unit = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->where(['Measurementunit.status' => 'Y'])->order(['Measurementunit.id' => 'asc'])->toarray();
        // pr($location); die;
        $this->set('units', $unit);

        $company = $this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => 'asc'])->toarray();
        // pr($location); die;
        $this->set('companys', $company);

        $tax = $this->Taxmaster->find('all')->order(['Taxmaster.id' => 'Asc'])->toarray();
        //pr($tax); die;
        $this->set('taxs', $tax);


        $cat = $this->Itemname->newEntity();
        if ($this->request->is(['post'])) {      
           // pr($this->request->data); die;
            $item['main_category_id'] = $this->request->data['main_category_id'];
            $item['category_id'] = $this->request->data['category_id'];
            $item['main_location_id'] = $this->request->data['main_location_id'];
            $item['location_id'] = $this->request->data['location_id'];
            $item['unit_id'] = $this->request->data['unit_id'];
            $item['sale_price'] = $this->request->data['sale_price']; 
            $item['item_name'] = $this->request->data['item_name'];    
            $item['company_id'] = $this->request->data['company_id'];
            $checkbox = implode(',', $this->request->data['tax']);
            //echo $checkbox; die;
            $item['tax'] = $checkbox;
            $item['added_time'] = date('Y-m-d H:i:s');
            //pr($item); die;

            $pnewdetail = $this->Itemname->patchEntity($cat, $item);
            if($resustnew = $this->Itemname->save($pnewdetail)){
                $this->Flash->success(__('Item location successfully added.'));
                return $this->redirect(['action' => 'index']);
            } 
            //pr($resustnew);  die;
        }                            
    }

            
  

public function edit($id=null){ 
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Itemname');
    $this->loadModel('Itemcategory');
    $this->loadModel('Itemlocation');
    $this->loadModel('Measurementunit');
    $this->loadModel('Companymaster');
    $this->loadModel('Taxmaster');

    $itemnamedetail = $this->Itemname->find('all')->where(['Itemname.id' => $id])->first();
    $this->set('itemnamedetail', $itemnamedetail);
    //pr($itemnamedetail); die;
    $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent' => 0, 'Itemcategory.status' => 'Y'])->order(['Itemcategory.id' => 'asc'])->toarray();
    //pr($categary); die;
    $this->set('categary', $categary);  


    $catid = $itemnamedetail['main_category_id'];
    $subcategary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent' => $catid, 'Itemcategory.status' => 'Y'])->order(['Itemcategory.id' => 'asc'])->toarray();
    //pr($subcategary); die;
    $this->set('subcategary', $subcategary);

    $lid = $itemnamedetail['main_location_id'];
    //echo $lid; die;
    $sublocation = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent' => $lid, 'Itemlocation.status' => 'Y'])->order(['Itemlocation.id' => 'asc'])->toarray();
    //pr($sublocation); die;

    $this->set('sublocation', $sublocation);
    

    $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent' => 0, 'Itemlocation.status' => 'Y'])->order(['Itemlocation.id' => 'asc'])->toarray();
    // pr($location); die;
    $this->set('locations', $location);

    $unit = $this->Measurementunit->find('list', ['keyField' => 'id', 'valueField' => 'unit_name'])->where(['Measurementunit.status' => 'Y'])->order(['Measurementunit.id' => 'asc'])->toarray();
    // pr($location); die;
    $this->set('units', $unit);

    $company = $this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => 'asc'])->toarray();
    // pr($location); die;
    $this->set('companys', $company);

    $tax = $this->Taxmaster->find('all')->order(['Taxmaster.id' => 'Asc'])->toarray();
    //pr($tax); die;
    $this->set('taxs', $tax);


    $cat = $this->Itemname->get($id);
    //pr($cat); die;
     $conn = ConnectionManager::get('default');
     if ($this->request->is(['put'])) {
        $item['main_category_id'] = $this->request->data['main_category_id'];
        $item['category_id'] = $this->request->data['category_id'];
        $item['main_location_id'] = $this->request->data['main_location_id'];
        $item['location_id'] = $this->request->data['location_id'];
        $item['unit_id'] = $this->request->data['unit_id'];
        $item['sale_price'] = $this->request->data['sale_price'];
        $item['item_name'] = $this->request->data['item_name'];
        $item['company_id'] = $this->request->data['company_id'];
        $checkbox = implode(',', $this->request->data['tax']);
        //echo $checkbox; die;
        $item['tax'] = $checkbox;
        $item['updated_time'] = date('Y-m-d H:i:s');
        $cats = $this->Itemname->patchEntity($cat, $item);
        if ($resust = $this->Itemname->save($cats)) {
            $this->Flash->success(__('Item name successfully updated.'));
            return $this->redirect(['action' => 'index']);
        }
            //die;

            
        }

    }

 

public function status($id,$status){
    $this->loadModel('Itemname');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){
            
            $status = 'N';
            $user = $this->Itemname->get($id);

            $user->status = $status;
            if ($this->Itemname->save($user)) {
                $this->Flash->success(__('Item status has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }

        }else{
            $status = 'Y';
            $user = $this->Itemname->get($id);
            $user->status = $status;
            if ($this->Itemname->save($user)) {
                $this->Flash->success(__('Item status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}

public function delete($id=null){ 
    $this->loadModel('Itemname');

                    //pr($this->Users->get($id)); die;
    try {                
        $user = $this->Itemname->get($id);
        if ($this->Itemname->delete($user)) {

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


public function getsublocation(){ 
    $this->viewBuilder()->layout('admin');        
    $this->loadModel('Itemlocation');
    //pr($this->request->data['dataString']); die;
    $id=$this->request->data['dataString'];
    $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent' => $id, 'Itemlocation.status' => 'Y'])->order(['Itemlocation.id' => 'asc'])->toarray();

    //pr($categary); die;
    //$this->set(compact('users'));

    header('Content-Type: application/json');
    echo json_encode($location);
    die;
}





}
