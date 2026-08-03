<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Network\Email\Email;
use Cake\Datasource\ConnectionManager;


class LoginsController extends AppController
{
   public function beforeFilter(Event $event)
    {    
		$this->loadModel('Users');
        parent::beforeFilter($event);
		$this->loadComponent('Cookie');
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['index', 'logout','forgot']);
    }



	public function index(){
		
      
        
	 	$this->viewBuilder()->layout('login');
		if ($this->request->is('post')) {
		
            		$user = $this->Auth->identify();
		//$user = $this->Users->identify($this->request->data);
		
            	if ($user) {
			$this->Auth->setUser($user);

			
				
				if($this->request->data['remember_me'] == 1){
					$this->Cookie->write('remember_me', $this->request->data['remember_me'], true, '1 month');		
					$this->Cookie->write('email', $this->request->data['email'], true, '1 month');
					$this->Cookie->write('password', $this->request->data['password'], true, '1 month');
                   // $this->Cookie->write('rememberMe', $cookie, true, "1 week");
				}else{
					$this->Cookie->write('remember_me', '', false, 1000);
					$this->Cookie->write('email', '', false, 1000);
					$this->Cookie->write('password', '', false, 1000);
				}
			
			//echo 'asdfdsdf'; die;
			//pr($email);  die;
			//pr($this->Cookie->read('password')); die;
			
			
			
			
			$this->set('user', $user['user_name']);
                	return $this->redirect(['controller' => 'admin/dashboards','action'=>'index']);
            	}
			
            		$this->Flash->error(__('Invalid email or password, try again'));
			
        	}
		$remember_me = $this->Cookie->read('remember_me');
		$email = $this->Cookie->read('email');
		$password = $this->Cookie->read('password');
			
              
            $this->set(compact('email','password','remember_me'));
		
        
           
	}



	public function logout()
    	{      
	  $this->Auth->logout();
        	return $this->redirect(['controller' => 'logins','action'=>'index']);
    	}
	public function forgot(){
		$this->viewBuilder()->layout('login');
		$email =  $this->request->data['email'];
	  	$userDatas = $this->Users->find('all')->where(['email' => $email])->toArray();
		foreach($userDatas as $userData){
			$id = $userData['id'];
			$emaiID = $userData['email'];
		}
	if(isset($userDatas) && !empty($userDatas)){
		$password = $this->randomPassword();
		$hasher = new DefaultPasswordHasher();
		$newpassword = $hasher->hash($password);
		$user = $this->Users->get($id);	
		$user->password = $newpassword;
		if ($this->Users->save($user)) {
				$to = $emaiID;
				$subject = 'Forgot Password';
			
				//set header	
				$header = "From: Creative Studio <creativestudio@gmail.com>\r\n";
				$header .= "Reply-To: creativestudio@gmail.com \r\n";
			
				//set message
				$message = 'Your password has been changed. your Email ID is '.$emaiID.'and password is '.$password ;
				$nmessage .= $message."\r\n\r\n";
				mail($to, $subject, $nmessage, $header);	
				$this->Flash->success(__('Your has been Updated on your Email ID.'));
				return $this->redirect(['action' => 'index']);	
		}else{
				$this->Flash->error(__('Your password not update. Please try again'));
				return $this->redirect(['action' => 'index']);	
		 }
	}else{
		$this->Flash->error(__("This Email ID doesn't exist in database"));
		return $this->redirect(['action' => 'index']);
	}	
	 
	}
	public function randomPassword() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
		
		
}
