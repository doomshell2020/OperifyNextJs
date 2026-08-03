<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class HousesController extends AppController
{
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//using for listing
		$work_data = $this->Houses->find('all')->toArray();
		$this->set('works',$work_data);
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			$work = $this->Houses->get($id);
		}else{
			$work = $this->Houses->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {
 		
				// save all data in database
				 $work = $this->Houses->patchEntity($work, $this->request->data);
				if ($this->Houses->save($work)) {
					$this->Flash->success(__('Your house has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{    //check validation error
					if($work->errors()){
					         $error_msg = [];
						foreach( $work->errors() as $errors){
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
		$this->set('work', $work);
	}
	// view functionality particular id
	public function view($id){ 
	   	$this->viewBuilder()->layout('admin');
		$work = $this->Houses->get($id);
		$this->set(compact('work'));
	    }
	// delete particular id
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$work = $this->Houses->get($id);
	    if ($this->Houses->delete($work)) {
		$this->Flash->success(__('The house with id: {0} has been deleted.', h($id)));
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
			$work = $this->Houses->get($id);
			$work->status = $status;
			if ($this->Houses->save($work)) {
				$this->Flash->success(__('Your house status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
		}
	}
}
