<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class UsersController extends AppController
{
	
	public function login(){ 
		$this->viewBuilder()->layout('login');
		return $this->redirect('/logins');
	}
	
}
