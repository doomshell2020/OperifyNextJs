<?php
namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

class SeoController extends AppController

{ 

	public $helpers = ['CakeJs.Js'];
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Seo');



 $seo = $this->Seo->find('all')->order(['Seo.id'=>'DESC']);
  $this->set('seo', $seo->toarray());


}
	

	public function add()
	{
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Seo');
		$newpack = $this->Seo->newEntity();
		if ($this->request->is(['post', 'put'])) { 			
      //pr($this->request->data); die;
      //$this->request->data['description']=$this->request->data['seodescription'];
			$savepack = $this->Seo->patchEntity($newpack, $this->request->data);
			$results=$this->Seo->save($savepack);
			if ($results){
				$this->Flash->success(__('Seo has been saved.'));
				return $this->redirect(['action' => 'index']);	
			}else{
				$this->Flash->error(__('Seo not saved please fill your all fields'));
			return $this->redirect(['action' => 'add']);
			}
		}
	}

	public function status($id,$status){

		$this->loadModel('Seo');
		if(isset($id) && !empty($id)){
			$product = $this->Seo->get($id);
			$product->status = $status;
			if ($this->Seo->save($product)) {
				$this->Flash->success(__('Seo status has been updated.'));
				return $this->redirect(['action' => 'index']);  
			}
		}
	}

	public function delete($id)
	{
		$this->loadModel('Seo');
		$seotdel = $this->Seo->get($id);
		if($seotdel){
			$this->Seo->deleteAll(['Seo.id' => $id]); 
			$this->Seo->delete($seotdel);

			$this->Flash->success(__('Seo has been deleted successfully.'));
			return $this->redirect(['action' => 'index']);
		}else{
			$this->Flash->error(__('Seo not  delete'));
			return $this->redirect(['action' => 'index']);
		}
	}

	public function edit($id)
	{
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Seo');

		$newpack = $this->Seo->get($id);
		$this->set('newpack',$newpack);
		if ($this->request->is(['post', 'put'])) {	

			$savepack = $this->Seo->patchEntity($newpack, $this->request->data);
			$results=$this->Seo->save($savepack);
			if ($results){
				$this->Flash->success(__('Seo has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}else{
				$this->Flash->error(__('Seo not updated'));
			return $this->redirect(['action' => 'edit']);
			}		    
		}
	}
	
	public function viewdocument($id)
	{
	//echo $id; die;
	$this->loadModel('Seo');
		$popupdata = $this->Seo->find('all')->where(['Seo.id'=>$id])->order(['Seo.id'=>DESC]);
		$this->set('popupdata', $this->paginate($popupdata)->toarray());
	
	}
	
		public function viewkeywords($id)
	{
	//echo $id; die;
	$this->loadModel('Seo');
		$keyworddata = $this->Seo->find('all')->where(['Seo.id'=>$id])->order(['Seo.id'=>DESC]);
		$this->set('keyworddata', $this->paginate($keyworddata)->toarray());
	
	}

	public function seosearch(){

  $this->loadModel('Seo');
 
  $req_data = $this->request->getQueryParams();
   $location = trim($req_data['location']);
  //pr($location); die;
  $cond = [];
  $session = $this->request->session(); 
  $session->delete('cond');       
  
  if(!empty($location))
  { 
    $cond['Seo.location LIKE']='%'.$location.'%';
   
  }
  
    if($cond){
   $searchresult = $this->Seo->find('all')->order(['Seo.id'=>'DESC'])->where([$cond]);
  $this->set('searchresult', $this->paginate($searchresult)->toarray());
}else{
 $searchresult = $this->Seo->find('all')->order(['Seo.id'=>'DESC']);
  $this->set('searchresult', $this->paginate($searchresult)->toarray());
}


}
	
}