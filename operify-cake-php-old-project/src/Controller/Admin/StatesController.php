<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class StatesController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('Country');
       		$this->loadModel('States');
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');

		$country=$this->Country->find('list')->where(['Country.status' => 'Y'])->order(['Country.name' => 'Asc'])->toarray();
		$this->set('country',$country);
		
		//show data in listing
		$classes_data = $this->States->find('all')->contain(['Country'])->toarray();
		//$this->paginate($service_data);
		$this->set('classes',$classes_data);
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
	   $country=$this->Country->find('list')->where(['Country.status' => 'Y'])->order(['Country.name' => 'Asc'])->toarray();
		$this->set('country',$country);
		if(isset($id) && !empty($id)){
			//using for edit
		     $classes = $this->States->get($id);

		}else{
			//using for new entry
			$classes = $this->States->newEntity();
			$statusquery = $this->States->find('all')->where(['States.status' => 1])->count();
		
			
			$this->request->data['status'] = 'Y';
		
		}
		if ($this->request->is(['post', 'put'])) {
			
  		
				
				// save all data in database
				$classes = $this->States->patchEntity($classes, $this->request->data);
				if ($this->States->save($classes)) {
					$this->Flash->success(__('Your State has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{ 
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
		     $classes = $this->States->get($id);
		
		}else{
			//using for new entry
			$classes = $this->States->newEntity();
		}
	
	if($this->request->is(['post', 'put'])) {
	
		//$this->request->data = $this->request->data['sort']; 
               $classes->sort = $this->request->data['sort'];

		if ($this->States->save($classes)) {
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
	    // echo $id;
		$classes =$this->States->find()->where(['States.id' => $id])->contain(['Country'])->first()->toarray();
		//pr($classes); die;
		$this->set(compact('classes'));
	    }
	//delete functionality
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->States->get($id);
		//delete pariticular entry
	    if ($this->States->delete($classes)) {
		$this->Flash->success(__('The State with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	//status update functionality
	public function status($id,$status){

		$statusquery = $this->States->find('all')->where(['States.status' => 'Y'])->count();
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->States->get($id);
				$classes->status = $status;
				if ($this->States->save($classes)) {
					$this->Flash->success(__('Your State status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 1;
			//status update
			$classes = $this->States->get($id);
			$classes->status = $status;
			if ($this->States->save($classes)) {
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
}
