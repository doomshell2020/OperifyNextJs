<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class CountryController extends AppController
{
	//initialize component
	  public function initialize(){
        parent::initialize();
       	$this->loadModel('Country');
    }
	
    // show tha data in index page
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$classes_data = $this->Country->find('all')->order(['id' => 'ASC'])->toarray();
		$this->set('classes',$classes_data);
		
	}

	// this is use to add coutry in tha softwere
    public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
	    $department = $this->Country->get($id);
	    }else{
		$department = $this->Country->newEntity();
	    }
	    $this->set('department', $department);
		//show data in listing
		if ($this->request->is(['post', 'put'])) {
		$department = $this->Country->patchEntity($department, $this->request->data);	
	    if ($this->Country->save($department)) {
			$this->Flash->success(__('Your Country has been saved.'));
			return $this->redirect(['action' => 'index']);	
     
		}
	}
}

	// delete index page record id wise
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Country->get($id);
		//delete pariticular entry
	    if ($this->Country->delete($classes)) {
		$this->Flash->success(__('The Country with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}

   // this is use to show particuler id base data 
	public function view($id){  

	    $this->viewBuilder()->layout('admin');
		$classes = $this->Country->get($id);
		$this->set(compact('classes'));

	}



	 public function status($id,$status){

		$statusquery = $this->Country->find('all')->where(['Country.status' => 'Y'])->count();
		if(isset($id) && !empty($id)){
		if($status =='N' ){
			
				$status = 'Y';
			//status update
				$classes = $this->Country->get($id);
				$classes->status = $status;
				if ($this->Country->save($classes)) {
					$this->Flash->success(__('Your Country status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
			$status = 'N';
			//status update
			$classes = $this->Country->get($id);
			$classes->status = $status;
			if ($this->Country->save($classes)) {
			$this->Flash->success(__('Your Country status has been updated.'));
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
