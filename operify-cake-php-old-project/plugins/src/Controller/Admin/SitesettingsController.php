<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Auth\DefaultPasswordHasher;


class SitesettingsController extends AppController
{
	
	
	public function add($id=1){ 
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Users');
		$user =$this->Users->get($this->Auth->user('id')); 
		if(isset($id) && !empty($id)){
			//using for edit
			$sitesetting = $this->Sitesettings->get($id);
		}
		if ($this->request->is(['post', 'put'])) {
			//check old password and new password
			if((isset($this->request->data['new_password']) && !empty($this->request->data['new_password'])) && (isset($this->request->data['confirm_pass']) && !empty($this->request->data['confirm_pass']))){
				if($this->request->data['new_password'] == $this->request->data['confirm_pass']){
					$this->request->data['password'] = (new DefaultPasswordHasher)->hash($this->request->data['new_password']);			//change password
					$user = $this->Users->patchEntity($user, $this->request->data); 
					$this->Users->save($user);
				}else{
					$this->Flash->error(__('Your new password and confirm password doesnot match, try again.'));
					return $this->redirect(['action' => 'add']);	
				}
			}	// edit site setting
				$sitesetting = $this->Sitesettings->patchEntity($sitesetting, $this->request->data);
				if ($this->Sitesettings->save($sitesetting)) {
					$this->Flash->success(__('Your site setting has been updated.'));
					return $this->redirect(['controller'=>'dashboards','action' => 'index']);	
				  }
                     }
		$this->set('sitesetting', $sitesetting);
	}
}
