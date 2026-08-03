<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class SubscribesController extends AppController
{	
	//initialize component
	public function initialize(){
        parent::initialize();
        $this->loadComponent('Paginator');
    	}
	//set pagination	
	public $paginate = [
		'limit' => 7,
		'order' => [
		    'Subscribes.email' => 'asc'
			]
    		];
	// show all data in listing with pagination
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show all data in listing page
		$subscribe_data = $this->Subscribes->find('all');
		$this->set('subscribes',$this->paginate($subscribe_data));
		
	}
	
}
