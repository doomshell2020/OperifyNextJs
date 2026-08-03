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


class CompanymasterController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		parent::initialize();
	}
	public function index(){ 
        $this->viewBuilder()->layout('admin');        
        $this->loadModel('Companymaster');
        $users=$this->Companymaster->find('all')->order(['Companymaster.id' => 'DESC'])->toarray();
        $this->set(compact('users'));
        $company=$this->Companymaster->find('list', ['keyField' => 'id', 'valueField' => 'cname'])->where(['Companymaster.status' => 'Y'])->order(['Companymaster.id' => "Asc"])->toarray();
        $this->set('company', $company);
        $cat = $this->Companymaster->newEntity();
        if ($this->request->is(['post'])) {
             $item['cname'] = $this->request->data['cname'];
             $item['gst'] = $this->request->data['gst'];
             $item['accountno'] = $this->request->data['accountno'];
             $item['tin_date'] = date('Y-m-d', strtotime($this->request->data['tin_date']));
             $item['ifsc'] = $this->request->data['ifsc'];
             $item['address'] = $this->request->data['address'];
             $pnewdetail = $this->Companymaster->patchEntity($cat, $item);
             if($resustnew = $this->Companymaster->save($pnewdetail)){
                 $this->Flash->success(__('Company successfully added.'));
                 return $this->redirect(['action' => 'index']);
             } 
         }                            
 }


public function edit($id=null){ 
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Companymaster');
    $itemcat = $this->Companymaster->find('All')->where(['Companymaster.id' => $id])->first();
    $this->set('company', $itemcat);
    $cat = $this->Companymaster->get($id);
    $conn = ConnectionManager::get('default');
    if ($this->request->is(['put'])) {
        $item['cname'] = $this->request->data['cname'];
        $item['gst'] = $this->request->data['gst'];
        $item['accountno'] = $this->request->data['accountno'];
        $item['tin_date'] = date('Y-m-d', strtotime($this->request->data['tin_datenn']));
        $item['ifsc'] = $this->request->data['ifsc'];
        $item['address'] = $this->request->data['address'];
        $cats = $this->Companymaster->patchEntity($cat, $item);
        if ($resust = $this->Companymaster->save($cats)) {
            $this->Flash->success(__('Item company successfully updated.'));
            return $this->redirect(['action' => 'index']);
       }  
    }
}

public function status($id,$status){
    $this->loadModel('Companymaster');
    if(isset($id) && !empty($id)){
        if($status =='Y' ){
            $status = 'N';
            $user = $this->Companymaster->get($id);
            $user->status = $status;
            if ($this->Companymaster->save($user)) {
                $this->Flash->success(__('Item company status has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }
        }else{
            $status = 'Y';
            $user = $this->Companymaster->get($id);
            $user->status = $status;
            if ($this->Companymaster->save($user)) {
             $this->Flash->success(__('Item company status has been updated.'));
             return $this->redirect(['action' => 'index']);
            }
        }
    }
}

public function delete($id=null){ 
    $this->loadModel('Companymaster');
    try {                
        $user = $this->Companymaster->get($id);
        if ($this->Companymaster->delete($user)) {
            $this->Flash->success(__('The Item company with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }
    catch (\PDOException $e) {
        $this->Flash->error(__('This Item company is used so you cannot delete this Item category detail'));
        $this->set('error', $error);
        return $this->redirect(['action' => 'index']);
    }
    die;
}

public function searchitem()
  {
    $this->loadModel('Companymaster'); 
    $company = $this->request->data['id'];
   
    $cond = [];  
    if(isset($company) && $company!='')
    {
    $cond['Companymaster.id']=$company;	
    }
    $user = $this->Companymaster->find('all')->where([$cond])->order(['Companymaster.id' =>'Desc'])->toarray();
    // pr($user); die;
     $this->set('users',$user); 
 }

}
