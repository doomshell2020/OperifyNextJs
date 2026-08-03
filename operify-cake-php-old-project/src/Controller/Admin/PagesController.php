<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class PagesController extends AppController
{
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show all data in listing page
		$page_data = $this->Pages->find('all')->toArray();
		$this->set('pages',$page_data);
	}
	public function add($id=null){ 
	     $this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
			$page = $this->Pages->get($id);
		}else{
			//using for new entry
			$page = $this->Pages->newEntity();
		}
    		if ($this->request->is(['post', 'put'])) {
			if(isset($id) && !empty($id)){
				$query = 0;
			}else{
				//check existing slug
				$slug = $this->create_url_slug($this->request->data['title']);
				$query = $this->Pages->find('all')->where(['Pages.slug' => $slug])->count();
			}
			if($query == 0){
				if(isset($this->request->data['image']['name']) && !empty($this->request->data['image']['name'])){
				$file = $this->request->data['image'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$arr_ext = array('jpg', 'jpeg', 'gif','png','PNG','GIF','JPEG','JPG');
				$setNewFileName = time() . "_" . rand(000000, 999999);
				// check image extension
			     		if (in_array($ext, $arr_ext)) {
					    	//image upload particular folder	
					    	move_uploaded_file($file['tmp_name'], WWW_ROOT . 'upload/' . $setNewFileName . '.' . $ext);
					    	$imageFileName = $setNewFileName . '.' . $ext;
					    	$this->request->data['image'] = $imageFileName;
				 	}else{
					 	$this->Flash->error(__('Image formate should be in jpg, jpeg, png and gif.'));
						return $this->redirect(['action' => 'add']);	
					}
				}else{
					$this->request->data['image'] = $page->image;
 				}
				if(isset($id) && !empty($id)){
					$this->request->data['slug'] = $page->slug;
				}else{
					$this->request->data['slug'] = $slug;
					$this->request->data['status'] = '1';
				 }
					// save all data in database
				 	$page = $this->Pages->patchEntity($page, $this->request->data);
					if ($this->Pages->save($page)) {
						$this->Flash->success(__('Your page has been saved.'));
						return $this->redirect(['action' => 'index']);	
				  	}else{
						//validation error
						if($page->errors()){
						          $error_msg = [];
							foreach( $page->errors() as $errors){
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
                        }else{
				//existing title error
				$this->Flash->error(__('This title is already exist.'));
				return $this->redirect(['action' => 'add']);		    
			     }	   
		     }
		
            $this->set('page', $page);
	}
	public function create_url_slug($string){
		//create slug
		$slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		return $slug;
	}
		// create view functionality
	public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get all data
		$page = $this->Pages->get($id);
		$this->set(compact('page'));
	    }
	//create delete functionality
	public function delete($id){
	    $page = $this->Pages->get($id);
		//delete particular entry
	    if ($this->Pages->delete($page)) {
		$this->Flash->success(__('The Page with id: {0} has been deleted.', h($id)));
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
			$page = $this->Pages->get($id);
			$page->status = $status;
			//status update
			if ($this->Pages->save($page)) {
				$this->Flash->success(__('Your page status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
		}
	}
}
