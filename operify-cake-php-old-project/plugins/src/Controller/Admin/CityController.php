<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class CityController extends AppController
{
	//initialize component
	public function initialize(){
        parent::initialize();
       	$this->loadModel('City');
       	$this->loadModel('State');
       		$this->loadModel('Country');
    	}
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
	
	//	$this->set('students',$student_data);
		$Country=$this->Country->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		$this->set('Country',$Country);
		$State_data=$this->State->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		$this->set('State_data',$State_data);
		//$classes_data = $this->City->find('all')->order(['id' => 'ASC'])->toarray();
		//pr($classes_data); die;
		//$this->paginate($service_data);
		$student_data = $this->City->find('all')->contain(['State','Country'])->toarray();

		$this->set('classes',$student_data);
		//$this->set('student_data',$student_data);
	//pr($student_data); die;
		//show data in listing
	}
public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		$Country=$this->Country->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		$this->set('Country',$Country);
		$State_data=$this->State->find('List')->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		$this->set('State_data',$State_data);
		if(isset($id) && !empty($id)){
			//using for edit
		     $city = $this->City->get($id);
             // echo $classes; die;
		}
		else{
		$city = $this->City->newEntity();
	}
		$this->set('city', $city);
		//show data in listing
			if ($this->request->is(['post', 'put'])) {
				$city = $this->City->patchEntity($city, $this->request->data);
				//pr($department); die;
			//pr($this->request->data); die;	
			if ($this->City->save($city)) {
					$this->Flash->success(__('Your City has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }
	}
}

	public function status($id,$status){
	//echo $id;
	//echo $status; die;
	
	//pr($this->request->data); die;
		$statusquery = $this->City->find('all')->where(['City.status' => 'Y'])->count();
		//pr($statusquery); die;
		if(isset($id) && !empty($id)){
		if($status == 'Y' ){
			
				$status = 'N';
			//status update
				$classes = $this->City->get($id);
				$classes->status = $status;
				if ($this->City->save($classes)) {
					$this->Flash->success(__('Your City status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 'Y';
			//status update
			$classes = $this->City->get($id);
			$classes->status = $status;
			if ($this->City->save($classes)) {
				$this->Flash->success(__('Your City status has been updated.'));
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
		$classes = $this->City->get($id);
		//delete pariticular entry
	    if ($this->City->delete($classes)) {
		$this->Flash->success(__('The City with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
		public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes =$this->City->find()->where(['City.id' => $id])->contain(['State','Country'])->first()->toarray();
		$this->set(compact('classes'));
	    }
	    public function getstate($id=null){    
			
		$this->viewBuilder()->layout('admin');
     	$sts=$this->State->find('all')->where(['State.c_id' => $this->request->data['id']])->toArray();
	      $this->set('sts',$sts);
	   //	pr($sts); die;
	    }
}
