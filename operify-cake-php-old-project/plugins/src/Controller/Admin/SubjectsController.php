<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class SubjectsController extends AppController
{	
	
	
	// show all data in listing with pagination
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show all data in listing page
		$subjects_data = $this->Subjects->find('all');
		$this->set('subjects',$subjects_data);
	}
	// create view functionality
	public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get all data particular id
		$subjects = $this->Subjects->get($id);
		$this->set(compact('subjects'));
	}
	// create delete functionality
	public function delete($id){
	    	$contact = $this->Subjects->get($id);
		//delete particular entry
	    if ($this->Subjects->delete($contact)) {
		$this->Flash->success(__('The subject with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			$subject = $this->Subjects->get($id);
		}else{
			$subject = $this->Subjects->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {
 		
				// save all data in database
				 $subject = $this->Subjects->patchEntity($subject, $this->request->data);
				if ($this->Subjects->save($subject)) {
					$this->Flash->success(__('Your subject has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{    //check validation error
					if($work->errors()){
					         $error_msg = [];
						foreach( $subject->errors() as $errors){
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
		$this->set('subject', $subject);
	}


	//status update functionality
	public function status($id,$status){

		$statusquery = $this->Subjects->find('all')->where(['Subjects.status' => 1])->count();
		if(isset($id) && !empty($id)){
		if($status == 1 ){
			
				$status = 0;
			//status update
				$classes = $this->Subjects->get($id);
				$classes->status = $status;
				if ($this->Subjects->save($classes)) {
					$this->Flash->success(__('Your subject status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 1;
			//status update
			$classes = $this->Subjects->get($id);
			$classes->status = $status;
			if ($this->Subjects->save($classes)) {
				$this->Flash->success(__('Your subject status has been updated.'));
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
