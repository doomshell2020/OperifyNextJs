<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class MachineController extends AppController
{

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Machinemaster');
        $data = $this->Machinemaster->find('all')->order(['Machinemaster.machine_name' => 'asc']);
        $data = $this->paginate($data)->toarray();
        $this->set('machine_data', $data);
    }

    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Machinemaster');

        $machines = $this->Machinemaster->newEntity();
        
        if ($this->request->is(['post'])) {

            $attnExist = $this->Machinemaster->exists(['machine_name' =>trim($this->request->data['machine_name'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered machine already exists.'));
                return $this->redirect(['action' => 'Add']);
            }

            $data = $this->Machinemaster->patchEntity($machines, $this->request->data);
            if ($this->Machinemaster->save($data)) {
                $this->Flash->success(__('Machine Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
         
    }
    }


    public function edit($id)
    {
        // pr($id);exit;
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Machinemaster');

        $machines = $this->Machinemaster->get($id);
        $this->set('machines', $machines);

        if ($this->request->is(['post', 'put'])) {

            $attnExist = $this->Machinemaster->exists(['machine_name' =>trim($this->request->data['machine_name'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered machine already exists.'));
                return $this->redirect(['action' => 'edit/',$id]);
            }

            $data = $this->Machinemaster->patchEntity($machines, $this->request->data);
            if ($this->Machinemaster->save($data)) {
                $this->Flash->success(__('Machine has been updated Successfully.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    public function status($id,$status){
        $this->loadModel('Machinemaster');
            $user = $this->Machinemaster->get($id);
            $user->status = $status;
            if ($this->Machinemaster->save($user)) {
             $this->Flash->success('Machine status has been updated.');
             return $this->redirect(['action' => 'index']);
            }
        
    }
    public function viewpdf(){
        $this->loadModel('Machinemaster');
        $data = $this->Machinemaster->find('all')->order(['Machinemaster.id' => 'desc'])->toarray();
        $this->set('machine_data', $data);   
    }


}