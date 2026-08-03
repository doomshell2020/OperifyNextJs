<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class StateController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('State');
       	$this->loadModel('Country');
       	
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
			$Country=$this->Country->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
	     $this->set('Country',$Country);
	     
		$student_data = $this->State->find('all')->contain(['Country'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$this->set('classes',$student_data);
		//show data in listing
	}
public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		$Country=$this->Country->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		
		$this->set('Country',$Country);
		if(isset($id) && !empty($id)){
			//using for edit
		     $state = $this->State->get($id);
             // echo $classes; die;
		}
		else{
		$state = $this->State->newEntity();
	}
		$this->set('state', $state);
		//show data in listing
			if ($this->request->is(['post', 'put'])) {
				$state = $this->State->patchEntity($state, $this->request->data);
				//pr($department); die;
			//pr($this->request->data); die;	
			if ($this->State->save($state)) {
					$this->Flash->success(__('Your State has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }
	}
}

	public function status($id,$status){
	//echo $id;
	//echo $status; die;
	
	//pr($this->request->data); die;
		$statusquery = $this->State->find('all')->where(['State.status' => 'Y'])->count();
		//pr($statusquery); die;
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->State->get($id);
				$classes->status = $status;
				if ($this->State->save($classes)) {
					$this->Flash->success(__('Your State status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 'Y';
			//status update
			$classes = $this->State->get($id);
			$classes->status = $status;
			if ($this->State->save($classes)) {
				$this->Flash->success(__('Your State status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
			
			}else{
				$this->Flash->error(__('8 Entries all ready activate. Please deactivate one of activate'));
				return $this->redirect(['action' => 'index']);	
	 		}
		}

	}
		
	}
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->State->get($id);
		//delete pariticular entry
	    if ($this->State->delete($classes)) {
		$this->Flash->success(__('The State with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
		public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->State->find()->where(['State.id' => $id])->contain(['Country'])->first()->toarray();
		$this->set(compact('classes'));
	    }
}
