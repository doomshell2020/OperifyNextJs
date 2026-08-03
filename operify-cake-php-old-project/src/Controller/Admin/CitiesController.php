<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\View\Helper;

class CitiesController extends AppController
{ 	
	
	public $helpers = ['CakeJs.Js'];
	
	//initialize component
	public function initialize(){
	
        parent::initialize();
       	$this->loadModel('Country');
       		$this->loadModel('States');
       		$this->loadModel('Cities');
    	}
	

	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$country=$this->Country->find('list')->where(['Country.status' => 'Y'])->order(['Country.name' => 'Asc'])->toarray();
		$this->set('country',$country);
		$State=$this->States->find('list')->where(['States.status' => 'Y'])->order(['States.name' => 'Asc'])->toarray();
		$this->set('State',$State);
		$classes_data = $this->Cities->find('all')->contain(['States','Country'])->toarray();
		$this->set('classes',$classes_data);
	}


	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
	    $country=$this->Country->find('list')->where(['Country.status' => 'Y'])->order(['Country.name' => 'Asc'])->toarray();
		$this->set('country',$country);
	    $State=$this->States->find('list')->where(['States.status' => 'Y'])->order(['States.name' => 'Asc'])->toarray();
		$this->set('State',$State);
		if(isset($id) && !empty($id)){
		     $classes = $this->Cities->get($id);  
		}else{
			$classes = $this->Cities->newEntity();	
			$this->request->data['status'] = 'Y';	
		}
		if ($this->request->is(['post', 'put'])) {
			// save all data in database
				$classes = $this->Cities->patchEntity($classes, $this->request->data);
				if ($this->Cities->save($classes)) {
					$this->Flash->success(__('Your City has been saved.'));
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
		     $classes = $this->Cities->get($id);
		
		}else{
			//using for new entry
			$classes = $this->Cities->newEntity();
		}
	
	if($this->request->is(['post', 'put'])) {
	
		//$this->request->data = $this->request->data['sort']; 
               $classes->sort = $this->request->data['sort'];

		if ($this->Cities->save($classes)) {
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
	   //  echo $id;
		$classes =$this->Cities->find()->where(['Cities.id' => $id])->contain(['States','Country'])->first()->toarray();
		//pr($classes); die;
		$this->set(compact('classes'));
	    }
	//delete functionality
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Cities->get($id);
		//delete pariticular entry
	    if ($this->Cities->delete($classes)) {
		$this->Flash->success(__('The City with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	//status update functionality
	public function status($id,$status){

		$statusquery = $this->Cities->find('all')->where(['Cities.status' => 'Y'])->count();
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->Cities->get($id);
				$classes->status = $status;
				if ($this->Cities->save($classes)) {
					$this->Flash->success(__('Your City status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			
				$status = 'Y';
			//status update
			$classes = $this->Cities->get($id);
			$classes->status = $status;
			if ($this->Cities->save($classes)) {
				$this->Flash->success(__('Your City status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
			
			
		}

	}
		
	}
		public function find_state(){
			$this->viewBuilder()->layout('admin');
                 $id=$this->request->data['id'];
		$statelist =$this->States->find('list')->where(['States.c_id' => $id,'States.status' =>'Y'])->toArray();
		//pr($statelist);die;
	echo "<option value=''>Select State</option>";
		foreach($statelist as $state=>$value){
			echo "<option value=".$state.">".$value."</option>";
		} die;
		}
		
		
		public function find_cities(){
			$this->viewBuilder()->layout('admin');
                 $id=$this->request->data['id'];
		$statelist =$this->Cities->find('list')->where(['Cities.s_id' => $id,'Cities.status' =>'Y'])->toArray();
		//pr($statelist);die;
		echo "<option value=''>Select City</option>";
		
		foreach($statelist as $state=>$value){
			echo "<option value=".$state.">".$value."</option>";
		} die;
		}		
}
