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
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$classes_data = $this->Country->find('all')->order(['id' => 'ASC'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$this->set('classes',$classes_data);
		//show data in listing
	}
public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
		     $country = $this->Country->get($id);
             // echo $classes; die;
		}
		else{
		$country = $this->Country->newEntity();
	}
		$this->set('country', $country);
		//show data in listing
			if ($this->request->is(['post', 'put'])) {
				$country = $this->Country->patchEntity($country, $this->request->data);
				//pr($department); die;
			//pr($this->request->data); die;	
			if ($this->Country->save($country)) {
					$this->Flash->success(__('Your Country has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }
	}
}

	public function status($id,$status){
	//echo $id;
	//echo $status; die;
	
	//pr($this->request->data); die;
		$statusquery = $this->Country->find('all')->where(['Country.status' => 'Y'])->count();
		//pr($statusquery); die;
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->Country->get($id);
				$classes->status = $status;
				if ($this->Country->save($classes)) {
					$this->Flash->success(__('Your Country status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 'Y';
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
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Country->get($id);
		//delete pariticular entry
	    if ($this->Country->delete($classes)) {
		$this->Flash->success(__('The Country with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
		public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->Country->get($id);
		$this->set(compact('classes'));
	    }
}
