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


class StorelocationpermissionController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Storelocationpermission');
        $this->loadModel('Itemlocation');
        $this->loadModel('Users');

        $users=$this->Storelocationpermission->find('all')->contain(['Users','Itemlocation'])->select(['Storelocationpermission.id','Storelocationpermission.status','Users.user_name','Itemlocation.location_name'])->order(['Storelocationpermission.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));
    }

    public function add(){
        $this->viewBuilder()->layout('admin');       
        $this->loadModel('Storelocationpermission');
        $this->loadModel('Itemlocation');
        $this->loadModel('Users');
        $user_role = array('1','3');
        //pr($user_role); die;
        $users = $this->Users->find('list',['keyField' => 'id','valueField' => 'user_name'])->where(['Users.role_id NOT IN'=>$user_role])->order(['Users.id' => 'asc'])->toarray();
        //pr($users); die;
        $this->set('users', $users);        

        $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent'=>'0'])->order(['Itemlocation.id' => 'asc'])->toarray();
        //pr($users); die;
        $this->set('locations', $location);

        $sublocation = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->order(['Itemlocation.id' => 'asc'])->toarray();
        //pr($users); die;
        $this->set('sublocation', $sublocation);

        $cat = $this->Storelocationpermission->newEntity();
        if ($this->request->is(['post'])) {
            //pr($this->request->data); die;
            $item['location_id'] = $this->request->data['sublocation_id'];
            $item['staff_id'] = $this->request->data['staff_id'];
            $item['date_added'] = date('Y-m-d H:i:s');
            
            $pnewdetail = $this->Storelocationpermission->patchEntity($cat, $item);
            if($resustnew = $this->Storelocationpermission->save($pnewdetail)){
                $this->Flash->success(__('Store location permission successfully added.'));
                return $this->redirect(['action' => 'index']);
            } 
            //pr($resustnew);  die;
        }                            
    }

            
  

public function edit($id=null){ 
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Storelocationpermission');
    $this->loadModel('Itemlocation');
    $this->loadModel('Users');
    
    $itemcat = $this->Storelocationpermission->find('All')->where(['Storelocationpermission.id' => $id])->first();
    //pr($purid); die;
    $this->set('itemcat', $itemcat);
    //pr($itemcat); die;

    $itemmaincat = $this->Itemlocation->find('All')->select(['Itemlocation.parent'])->where(['Itemlocation.id' => $itemcat['location_id']])->first();
    //pr($itemmaincat); die;
    $this->set('itemmaincat', $itemmaincat);

    $user_role = array('1', '3');
    $users = $this->Users->find('list',['keyField' => 'id','valueField' => 'user_name'])->where(['Users.role_id NOT IN'=>$user_role])->order(['Users.id' => 'asc'])->toarray();
    //pr($users); die;
    $this->set('users', $users);        

    $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent'=>'0'])->order(['Itemlocation.id' => 'asc'])->toarray();
    //pr($users); die;
    $this->set('locations', $location);

    $sublocation = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent !='=>'0'])->order(['Itemlocation.id' => 'asc'])->toarray();
        //pr($sublocation); die;
        $this->set('sublocation', $sublocation);

    $cat = $this->Storelocationpermission->get($id);
    //pr($cat); die;
     $conn = ConnectionManager::get('default');
     if ($this->request->is(['put'])) {
        $item['location_id'] = $this->request->data['sublocation_id'];
        $item['staff_id'] = $this->request->data['staff_id'];
        $cats = $this->Storelocationpermission->patchEntity($cat, $item);
        if ($resust = $this->Storelocationpermission->save($cats)) {
            $this->Flash->success(__('Store location permission successfully updated.'));
            return $this->redirect(['action' => 'index']);
        }
        //die;
            
        }
 
       


    }


public function status($id,$status){
    $this->loadModel('Storelocationpermission');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){
            
            $status = 'N';
            $user = $this->Storelocationpermission->get($id);

            $user->status = $status;
            if ($this->Storelocationpermission->save($user)) {
                $this->Flash->success(__('This Store location Permission status has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }

        }else{
            $status = 'Y';
            $user = $this->Storelocationpermission->get($id);
            $user->status = $status;
            if ($this->Storelocationpermission->save($user)) {
                $this->Flash->success(__('This Store location Permission status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}

public function delete($id=null){ 
    $this->loadModel('Storelocationpermission');

                    //pr($this->Users->get($id)); die;
    try {                
        $user = $this->Storelocationpermission->get($id);
        if ($this->Storelocationpermission->delete($user)) {

            // $delete_record = 'DELETE FROM `st_locationmaster` WHERE `id` =' . $id;
            // $conn->execute($delete_record);
            $this->Flash->success(__('This Store location Permission with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }
    catch (\PDOException $e) {
        $this->Flash->error(__('This Store location Permission is used so you cannot delete this Item location detail'));
        $this->set('error', $error);
        return $this->redirect(['action' => 'index']);
    }
    die;
 

}



    public function getsublocation(){ 
        $this->loadModel('Itemlocation');
        $id = $this->request->data['dataString'];
        $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent'=>$id])->order(['Itemlocation.id' => 'asc'])->toarray();
        header('Content-Type: application/json');
        echo json_encode($location);
        die;
    }

}
