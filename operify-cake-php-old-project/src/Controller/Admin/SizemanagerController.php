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


class SizemanagerController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index($id=null){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Sizemanager');
        $users=$this->Sizemanager->find('all')->order(['Sizemanager.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));

        if(isset($id)){
            $cat = $this->Sizemanager->get($id); 
            $this->set('cat',$cat);           
        }else{
            $cat = $this->Sizemanager->newEntity();
        }

            if ($this->request->is(['post','put'])) {
                //pr($this->request->data); die;
                $item['size_name'] = $this->request->data['size_name'];            
                $item['description'] = $this->request->data['description']; 
                if(empty($id)){
                    $item['updated_time']= "";
                }else{
                    $item['updated_time'] = date("Y-m-d H:i:s");
                }
                $pnewdetail = $this->Sizemanager->patchEntity($cat, $item);
                if($resustnew = $this->Sizemanager->save($pnewdetail)){
                    $this->Flash->success(__('Item Size successfully added.'));
                    return $this->redirect(['action' => 'index']);
                } 
                //pr($resustnew);  die;
            }
    }




public function status($id,$status){
    $this->loadModel('Sizemanager');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){            
            $status = 'N';
            $user = $this->Sizemanager->get($id);
            $user->status = $status;
            if ($this->Sizemanager->save($user)) {
                $this->Flash->success(__('Size status has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }
        }else{
            $status = 'Y';
            $user = $this->Sizemanager->get($id);
            $user->status = $status;
            if ($this->Sizemanager->save($user)) {
                $this->Flash->success(__('Size status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}

public function delete($id=null){ 
    $this->loadModel('Sizemanager');

    //                 //pr($this->Users->get($id)); die;
    // try {                
    //     $user = $this->Sizemanager->get($id);
    //     $user->status = 'N';



    //     if ($this->Sizemanager->save($res)) {

           
    //         $this->Flash->success(__('The Size with id: {0} has been deleted.', h($id)));
    //         return $this->redirect(['action' => 'index']);
    //     }
    // }
    // catch (\PDOException $e) {

    //     $this->Flash->error(__('This Size is used so you cannot delete this Item category detail'));
    //     $this->set('error', $error);
    //     return $this->redirect(['action' => 'index']);
    // }
    // die;
// }

                    $res = $this->Sizemanager->get($id);

                   
                    if ($this->Sizemanager->delete($res)) {

                        $this->Flash->success('Sizemanager deleted successfully');
                        return $this->redirect(['controller' => 'Sizemanager', 'action' => 'index']);
                    } else {
                        $this->Flash->error('Sizemanager not  delete successfully');
                        return $this->redirect(['controller' => 'Sizemanager', 'action' => 'index']);
                    }
                }




}
