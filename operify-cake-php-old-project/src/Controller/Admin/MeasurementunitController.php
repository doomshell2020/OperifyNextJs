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


class MeasurementunitController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index($id=null){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Measurementunit');
        $users=$this->Measurementunit->find('all')->order(['Measurementunit.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));

        if(isset($id)){
            $cat = $this->Measurementunit->get($id);
            $this->set('cat',$cat);
        }else{
            $cat = $this->Measurementunit->newEntity();
        }
        if ($this->request->is(['post','put'])) {

            if($id){
				$exisits = $this->Measurementunit->exists(['unit_name' => trim($this->request->data['unit_name']),'NOT' => ['id' => $id]]);
			}else{
				$exisits = $this->Measurementunit->exists(['unit_name' => trim($this->request->data['unit_name'])]);
			}

			if($exisits){
				$this->Flash->error(__("Your entered Measurementunit already exists"));
					return $this->redirect(['action' => 'index']);
			}
            
            $item['unit_name'] = $this->request->data['unit_name'];            
            $item['description'] = $this->removeEmojis($this->request->data['description']);
            if(empty($cat)){
                $item['updated_time'] = "";
            }else{
                $item['updated_time'] = date("Y-m-d H:i:s");
            }
            $pnewdetail = $this->Measurementunit->patchEntity($cat, $item);
            if($resustnew = $this->Measurementunit->save($pnewdetail)){
                $this->Flash->success(__('Measurement unit successfully added.'));
                return $this->redirect(['action' => 'index']);
            } 
            //pr($resustnew);  die;
        } 
    }

//     public function add($id=null){
//         $this->viewBuilder()->layout('admin');       
//         $this->loadModel('Measurementunit');        
                                   
//     }

            
  

// public function edit($id=null){ 
//     $this->viewBuilder()->layout('admin');
//     $this->loadModel('Measurementunit');
    
//     $itemcat = $this->Measurementunit->find('All')->where(['Measurementunit.id' => $id])->first();
//     //pr($purid); die;
//     $this->set('company', $itemcat);

//     $cat = $this->Measurementunit->get($id);
//     //pr($cat); die;
//      $conn = ConnectionManager::get('default');
//      if ($this->request->is(['put'])) {
//         //pr($this->request->data); die;
//         $item['unit_name'] = $this->request->data['unit_name'];
//         $item['unit_quantity'] = $this->request->data['unit_quantity'];
//         $item['description'] = $this->request->data['description'];
//         $item['updated_time'] = date("Y-m-d H:i:s");
//         $cats = $this->Measurementunit->patchEntity($cat, $item);
//         if ($resust = $this->Measurementunit->save($cats)) {
//             $this->Flash->success(__('Measurement Unit successfully updated.'));
//             return $this->redirect(['action' => 'index']);
//         }
//             //die;
            
//         }
 
       


//     }




public function status($id,$status){
    $this->loadModel('Measurementunit');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){
            
            $status = 'N';
            $user = $this->Measurementunit->get($id);

            $user->status = $status;
            if ($this->Measurementunit->save($user)) {
                $this->Flash->success(__('Measurement Unit status has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }

        }else{
            $status = 'Y';
            $user = $this->Measurementunit->get($id);
            $user->status = $status;
            if ($this->Measurementunit->save($user)) {
                $this->Flash->success(__('Measurement Unit status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}

public function delete($id=null){ 
    $this->loadModel('Measurementunit');
//     $this->autoRender = false;
//     $res = $this->Measurementunit->get($id);
    
//                     //pr($this->Users->get($id)); die;
//     try {                
//         $user = $this->Measurementunit->get($id);
//         $user->status = 'Y';
//         if ($this->Measurementunit->status($user)) {

         
//             $this->Flash->success(__('The Measurement unit with id: {0} has been deleted.', h($id)));
//             return $this->redirect(['action' => 'index']);
//         }
//     }
//     catch (\PDOException $e) {

//         $this->Flash->error(__('This Measurement unit is used so you cannot delete this Measurement unit detail'));
//         $this->set('error', $error);
//         return $this->redirect(['action' => 'index']);
//     }
//     die;
 

// }

$this->autoRender = false;
$res = $this->Measurementunit->get($id);
//pr($res); die;

if ($this->Measurementunit->delete($res)) {

    $this->Flash->success('Measurementunit Recored deleted successfully');
    return $this->redirect(['controller' => 'Measurementunit', 'action' => 'index']);
} else {
    $this->Flash->error('Measurementunit Recored not  delete successfully');
    return $this->redirect(['controller' => 'Measurementunit', 'action' => 'index']);
}
}




}
