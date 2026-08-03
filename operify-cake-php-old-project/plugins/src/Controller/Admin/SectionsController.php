<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class SectionsController extends AppController
{
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//using for listing
		$section_data = $this->Sections->find('all')->toArray();
		$this->set('sections',$section_data);
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			$sections = $this->Sections->get($id);
		}else{
			$sections = $this->Sections->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {
 		
				// save all data in database
				 $sections = $this->Sections->patchEntity($sections, $this->request->data);
				if ($this->Sections->save($sections)) {
					$this->Flash->success(__('Your Section has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{    //check validation error
					if($sections->errors()){
					         $error_msg = [];
						foreach( $sections->errors() as $errors){
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
		$this->set('sections', $sections);
	}
	// view functionality particular id
	public function view($id){ 
	   	$this->viewBuilder()->layout('admin');
		$sections = $this->Sections->get($id);
		$this->set(compact('sections'));
	    }
	// delete particular id
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$work = $this->Sections->get($id);
	    if ($this->Sections->delete($work)) {
		$this->Flash->success(__('The Section with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	public function status($id,$status){
		if($status == 1){
			$status = 0;
		}else{
			$status = 1;
		}
		//$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			$section = $this->Sections->get($id);
			$section->status = $status;
			if ($this->Sections->save($section)) {
				$this->Flash->success(__('Your section status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
		}
	}
}
