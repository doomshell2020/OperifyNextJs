<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class TestimonialsController extends AppController
{
	
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show data in listing
		$testimonial_data = $this->Testimonials->find('all')->toArray();
		$this->set('testimonials',$testimonial_data);
	}
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//using for edit
		     $testimonial = $this->Testimonials->get($id);
		}else{
			//using for new entry
			$testimonial = $this->Testimonials->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {
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
				$this->request->data['image'] = $testimonial->image;
 			}
				$newtimestamp = strtotime($this->request->data['add_date']);
				$data_date = date('Y-m-d', $newtimestamp);
				$this->request->data['add_date'] = $data_date;
				// save all data in database
				$testimonial = $this->Testimonials->patchEntity($testimonial, $this->request->data);
				if ($this->Testimonials->save($testimonial)) {
					$this->Flash->success(__('Your testimonial has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{
					//validation error
					if($testimonial->errors()){
					          $error_msg = [];
						foreach( $testimonial->errors() as $errors){
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
		$this->set('testimonial', $testimonial);
	}
	//view functionality
	public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$testimonial = $this->Testimonials->get($id);
		$this->set(compact('testimonial'));
	    }
	//delete functionality
	public function delete($id){
	    //$this->request->allowMethod(['post', 'delete']);
		$testimonial = $this->Testimonials->get($id);
		//delete pariticular entry
	    if ($this->Testimonials->delete($testimonial)) {
		$this->Flash->success(__('The Testimonial with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	//status update functionality
	public function status($id,$status){
		if($status == 1){
			$status = 0;
		}else{
			$status = 1;
		}

         	//$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			//status update
			$testimonial = $this->Testimonials->get($id);
			$testimonial->status = $status;
			if ($this->Testimonials->save($testimonial)) {
				$this->Flash->success(__('Your testimonial status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
		}
	}
}
