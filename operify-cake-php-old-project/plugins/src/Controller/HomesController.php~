<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class HomesController extends AppController
{
	public function initialize()
    	{
	$this->loadModel('Contacts');
	$this->loadModel('Subscribes');
	$this->loadModel('Pages');
	$this->loadModel('Testimonials');

	$this->loadModel('Sitesettings');
        parent::initialize();
	$this->Auth->allow(['index', 'add','addsubscribe','contactservice']);
        //$this->loadComponent('Flash');  Include the FlashComponent
    	}


    	public function index(){
	 	$this->viewBuilder()->layout('front');
		$homes = $this->Pages->find('all')->where(['Pages.slug' => 'Home', 'status'=>1])->toArray();
		$pages = $this->Pages->find('all')->where(['Pages.slug' => 'About-US', 'status'=>1])->toArray();
		$services_headings = $this->Pages->find('all')->where(['Pages.slug' => 'Services', 'status'=>1])->toArray();
		$whats = $this->Pages->find('all')->where(['Pages.slug' => 'What-We-Do', 'status'=>1])->toArray();
		$contacts = $this->Pages->find('all')->where(['Pages.slug' => 'Contact-Us', 'status'=>1])->toArray();
		$subscribes = $this->Pages->find('all')->where(['Pages.slug' => 'Stay-In-The-Know', 'status'=>1])->toArray();
		$clients = $this->Pages->find('all')->where(['Pages.slug' => 'Who-We-ve-Help-Done-For', 'status'=>1])->toArray();
		$testimonials = $this->Testimonials->find('all')->where(['status'=>1])->toArray();
		
		$sitesettings = $this->Sitesettings->find('all')->toArray();
		$craveatives = $this->Pages->find('all')->where(['Pages.slug' => 'CraveativeMedia', 'status'=>1])->toArray();
		$hires = $this->Pages->find('all')->where(['Pages.slug' => 'hires', 'status'=>1])->toArray();
		
	$this->set(compact('pages','services','whats','testimonials','contacts','subscribes','clients','works','sitesettings','homes','craveatives','hires','services_headings'));
	}
	// This function use for contact form add
	public function add()   
    	{	
 		$this->viewBuilder()->layout('front');
		$contact = $this->Contacts->newEntity();
		if ($this->request->is('post')) {
			if(isset($this->request->data['image']['name']) && !empty($this->request->data['image']['name'])){
				$file = $this->request->data['image'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$arr_ext = array('jpg', 'jpeg', 'gif','png');
				$setNewFileName = time() . "_" . rand(000000, 999999);
				// check image extension
			     if (in_array($ext, $arr_ext)) {
				    //image upload particular folder	
				    move_uploaded_file($file['tmp_name'], WWW_ROOT . 'upload/' . $setNewFileName . '.' . $ext);
				    $imageFileName = $setNewFileName . '.' . $ext;
				    $contact = $this->Contacts->patchEntity($contact, $this->request->data);	
				    $contact->image = $imageFileName;
				// save all data in database
			if(isset($this->request->data['g-recaptcha-response']) && !empty($this->request->data['g-recaptcha-response'])){
				if ($this->Contacts->save($contact)) {
					$path = WWW_ROOT . 'upload/';
					$file = $path.$imageFileName;
					$content = file_get_contents( $file);
					$content = chunk_split(base64_encode($content));
					$uid = md5(uniqid(time()));
					$name = basename($file);
					$from_name = $this->request->data['name'];
					$from_mail = $this->request->data['email'];
					$replyto = $this->request->data['email'];
					$message = $this->request->data['message'];
					
					//set header	
					$header = "From: ".$from_name." <".$from_mail.">\r\n";
					$header .= "Reply-To: ".$replyto."\r\n";
					$header .= "MIME-Version: 1.0\r\n";
					$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
					
					//set message
					$nmessage = "--".$uid."\r\n";
					$nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
					$nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
					$nmessage .= $message."\r\n\r\n";
					$nmessage .= "--".$uid."\r\n";
					$nmessage .= "Content-Type: application/octet-stream; name=\"".$imageFileName."\"\r\n";
					$nmessage .= "Content-Transfer-Encoding: base64\r\n";
					$nmessage .= "Content-Disposition: attachment; filename=\"".$imageFileName."\"\r\n\r\n";
					$nmessage .= "--".$uid."--";

					$to = 'amit@gmail.com';
					$subject = 'contact';
					//send email	
					mail($to, $subject, $nmessage, $header); 
					$this->request->session()->write('cont', 'contact');
					$this->Flash->success(__('Your contact has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{
			  		$this->Flash->error(__('Unable to add your contact.'));
					return $this->redirect(['action' => 'index']);	
				}
			}else{
				$this->Flash->error(__('Please fill captcha.'));
				return $this->redirect(['action' => 'index']);	
			}
			  }else{
			 	$this->Flash->error(__('Image formate should be in jpg, jpeg and gif.'));
				return $this->redirect(['action' => 'index']);	

				}
			}

           	   }
            }
	public function addsubscribe(){
		$this->viewBuilder()->layout('front');
		
		//$this->request->session()->read('Subscrib');
		$subscribe = $this->Subscribes->newEntity();
		if ($this->request->is('post')) {
			 $subscribe = $this->Subscribes->patchEntity($subscribe, $this->request->data);
			if ($this->Subscribes->save($subscribe)) {
				$this->request->session()->write('Subscrib', 'subscribes');
				$this->Flash->success(__('Your subscription has been saved.'));
				return $this->redirect(['action' => 'index']);
			}else{
				if($subscribe->errors()){
						$error_msg = [];
						foreach( $subscribe->errors() as $errors){
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

				return $this->redirect(['action' => 'index']);	
			}	
		}
		
	}	
	public function contactservice(){
		
		$name = $this->request->data['name'];
		$email = $this->request->data['email'];
		
		$to = 'amit@gmail.com';
		$replyto = $email;
		$from_name = $name;	
		$from_email = $email;
		$subject = $this->request->data['subject'];
		$message = $this->request->data['message'];
		
		//set header	
		$header = "From: ".$from_name."\r\n";
		$header .= "Reply-To: ".$replyto."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		
					
		//set message
		
		$nmessage = $message."\r\n\r\n";
		
		//send email	
		if(mail($to, $subject, $nmessage, $header)){
			echo 1;
		}else{
			echo 0;
		}
	exit;
	}	
   
    
}
