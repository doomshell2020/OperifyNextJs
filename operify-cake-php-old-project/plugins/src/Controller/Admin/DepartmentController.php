<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class DepartmentController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('Department');
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$classes_data = $this->Department->find('all')->order(['id' => 'ASC'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$this->set('classes',$classes_data);
		//show data in listing
	}
public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
		     $department = $this->Department->get($id);
             // echo $classes; die;
		}
		else{
		$department = $this->Department->newEntity();
	}
		$this->set('department', $department);
		//show data in listing
			if ($this->request->is(['post', 'put'])) {
				$department = $this->Department->patchEntity($department, $this->request->data);
				//pr($department); die;
			//pr($this->request->data); die;	
			if ($this->Department->save($department)) {
					$this->Flash->success(__('Your Department has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }
	}
}

	public function status($id,$status){
	//echo $id;
	//echo $status; die;
	
	//pr($this->request->data); die;
		$statusquery = $this->Department->find('all')->where(['Department.status' => 'Y'])->count();
		//pr($statusquery); die;
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->Department->get($id);
				$classes->status = $status;
				if ($this->Department->save($classes)) {
					$this->Flash->success(__('Your Department status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 'Y';
			//status update
			$classes = $this->Department->get($id);
			$classes->status = $status;
			if ($this->Department->save($classes)) {
				$this->Flash->success(__('Your Department status has been updated.'));
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
		$classes = $this->Department->get($id);
		//delete pariticular entry
	    if ($this->Department->delete($classes)) {
		$this->Flash->success(__('The Department with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
		public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->Department->get($id);
		$this->set(compact('classes'));
	    }
}
