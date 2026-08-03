<?php

namespace App\Controller\Admin;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;

class SpamController extends AppController

{   // Index
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('IpRanges');
        $ips = $this->IpRanges->find('all')->order(['IpRanges.id' => 'Desc']);
        $this->set('ips', $this->paginate($ips)->toarray());
    }

    public function delete($id)
    {
        $this->loadModel('IpRanges');
        $spamdel = $this->IpRanges->get($id);
        if ($spamdel) {
            $this->IpRanges->deleteAll(['IpRanges.id' => $id]);
            $this->Flash->success(__(' IP Range has been deleted successfully.'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Flash->error(__(' IP Range information cant be deleted.'));
            $this->redirect(array('action' => 'index'));
        }
    }


    public function addiprange()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('IpRanges');

        $newpack = $this->IpRanges->newEntity();
        if ($this->request->is(['post', 'put'])) {

            $savepack = $this->IpRanges->patchEntity($newpack, $this->request->data);
            $results = $this->IpRanges->save($savepack);
            if ($results) {
                $this->Flash->success(__('IP range has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('IP range not saved.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }
}
