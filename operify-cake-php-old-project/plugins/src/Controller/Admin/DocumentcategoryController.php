<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class DocumentcategoryController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('Documentcategory');
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$classes_data = $this->Documentcategory->find('all')->order(['id' => 'ASC'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$this->set('classes',$classes_data);
		//show data in listing
	}
public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
		     $department = $this->Documentcategory->get($id);
             // echo $classes; die;
		}
		else{
		$department = $this->Documentcategory->newEntity();
	}
		$this->set('department', $department);
		//show data in listing
			if ($this->request->is(['post', 'put'])) {
				$department = $this->Documentcategory->patchEntity($department, $this->request->data);
				//pr($department); die;
			//pr($this->request->data); die;	
			if ($this->Documentcategory->save($department)) {
					$this->Flash->success(__('Your Document Category has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }
	}
}

	
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Documentcategory->get($id);
		//delete pariticular entry
	    if ($this->Documentcategory->delete($classes)) {
		$this->Flash->success(__('The Department with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
		public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->Documentcategory->get($id);
		$this->set(compact('classes'));
	    }
	       public function studentdetail()
    {

    }
	    
	    
}
