<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class ClassesController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('Classes');
       
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show data in listing
		$classes_data = $this->Classes->find('all')->order(['id' => 'ASC'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$this->set('classes',$classes_data);
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
		     $classes = $this->Classes->get($id);

		}else{
			//using for new entry
			$classes = $this->Classes->newEntity();
			$statusquery = $this->Classes->find('all')->where(['Classes.status' => 1])->count();
			if($statusquery < 8){
			
			$this->request->data['status'] = '1';
			}else{
			
				$this->request->data['status'] = '0';
			}
		}
		if ($this->request->is(['post', 'put'])) {
			
  				
			//pr($this->request->data); die;
				
				// save all data in database
				$classes = $this->Classes->patchEntity($classes, $this->request->data);
				if ($this->Classes->save($classes)) {
					$this->Flash->success(__('Your class has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{ //pr($classes->errors());
					//validation error
					if($classes->errors()){
					          $error_msg = [];
						foreach( $classes->errors() as $errors){
						    if(is_array($errors)){
							foreach($errors as $error){
							    $error_msg[]    =   $error;
							}
						    }else{
							$error_msg[]    =   $errors;
						    }
						}
					if(!empty($error_msg)){
					    $this->Flash->error(
						__("Please fix the following error(s): ".implode("\n \r", $error_msg))
					    );
					}
				    }

				}
			
                }

		$this->set('classes', $classes);
	}


	public function sort(){
	$this->viewBuilder()->layout('admin');
	$id = $this->request->data[id];
	if(isset($id) && !empty($id)){
			//using for edit
		     $classes = $this->Classes->get($id);
		
		}else{
			//using for new entry
			$classes = $this->Classes->newEntity();
		}
	
	if($this->request->is(['post', 'put'])) {
	
		//$this->request->data = $this->request->data['sort']; 
               $classes->sort = $this->request->data['sort'];

		if ($this->Classes->save($classes)) {
			echo $classes['sort'];		
		}else{  
			echo 'wrong'; 
				
		}
	}	
	die;
	}
	//view functionality
	public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->Classes->get($id);
		$this->set(compact('classes'));
	    }
	//delete functionality
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Classes->get($id);
		//delete pariticular entry
	    if ($this->Classes->delete($classes)) {
		$this->Flash->success(__('The class with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	//status update functionality
	public function status($id,$status){
//pr($this->request->data); die;
		$statusquery = $this->Classes->find('all')->where(['Classes.status' => 1])->count();
		if(isset($id) && !empty($id)){
		if($status == 1 ){
			
				$status = 0;
			//status update
				$classes = $this->Classes->get($id);
				$classes->status = $status;
				if ($this->Classes->save($classes)) {
					$this->Flash->success(__('Your class status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 1;
			//status update
			$classes = $this->Classes->get($id);
			$classes->status = $status;
			if ($this->Classes->save($classes)) {
				$this->Flash->success(__('Your class status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
			
			}else{
				$this->Flash->error(__('8 Entries all ready activate. Please deactivate one of activate'));
				return $this->redirect(['action' => 'index']);	
	 		}
		}

	}
		
	}
}
