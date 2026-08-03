<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class DashboardsController extends AppController
{
	//$this->loadcomponent('Session');
	public function initialize(){	
		//load all models
		$this->loadModel('Users');
		$this->loadModel('Subscribes');
		$this->loadModel('Contacts');
		$this->loadModel('Testimonials');
	
		parent::initialize();
	}
	public function index(){ 
		// All dashboard data like counts and admin user name	
	 	$this->viewBuilder()->layout('admin');
		$user_id =  $this->Auth->user('id'); 
		$user_data = $this->Users->get($user_id);
   		$admin_url =	configure::read('ADMIN_URL');
		$user_name = $user_data['user_name'];
		$count_subscribe = $this->Subscribes->find('all')->count();
		$count_contact = $this->Contacts->find('all')->count();
		$count_testimonials = $this->Testimonials->find('all')->count();
		$count_services = '0';
		$this->set(compact('admin_url','user_name','count_subscribe','count_contact','count_testimonials','count_services'));
	}
}
