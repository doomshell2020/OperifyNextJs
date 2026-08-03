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


class TaxmasterController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Taxmaster');
        $users = $this->Taxmaster->find('all')->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));
    }

    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Taxmaster');
        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax_name'])->where(['Taxmaster.status' => 'Y', 'Taxmaster.parent' => '0'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('tax', $tax);

        $cat = $this->Taxmaster->newEntity();
        if ($this->request->is(['post'])) {

            $attnExist = $this->Taxmaster->exists(['tax' => trim($this->request->data['tax'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Tax already exists.'));
                return $this->redirect(['action' => 'Add']);
            }

            $item['tax'] = $this->request->data['tax'];
            $item['description'] = $this->removeEmojis($this->request->data['description']);
            $item['updated_time'] = "";

            $pnewdetail = $this->Taxmaster->patchEntity($cat, $item);
            if ($resustnew = $this->Taxmaster->save($pnewdetail)) {
                $this->Flash->success(__('Tax Type successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }




    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Taxmaster');
        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax_name'])->where(['Taxmaster.status' => 'Y', 'Taxmaster.parent' => '0'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('tax', $tax);

        $itemcat = $this->Taxmaster->find('All')->where(['Taxmaster.id' => $id])->first();
        //pr($purid); die;
        $this->set('company', $itemcat);

        $cat = $this->Taxmaster->get($id);
        //pr($cat); die;
        $conn = ConnectionManager::get('default');
        if ($this->request->is(['put'])) {

            $attnExist = $this->Taxmaster->exists(['tax' => trim($this->request->data['tax']), 'NOT' => ['id' => $id]]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Tax already exists.'));
                return $this->redirect(['action' => 'Edit/', $id]);
            }
            //pr($this->request->data); die;
            //$item['tax_name'] = $this->request->data['tax_name'];
            // if($this->request->data['parent']){
            //     $item['parent'] = $this->request->data['parent'];

            // }else{
            // 	     $item['parent'] = 0;

            // }
            $item['tax'] = $this->request->data['tax'];
            $item['description'] = $this->removeEmojis($this->request->data['description']);
            $item['updated_time'] = date("Y-m-d H:i:s");
            $cats = $this->Taxmaster->patchEntity($cat, $item);
            if ($resust = $this->Taxmaster->save($cats)) {
                $this->Flash->success(__('Tax Type successfully updated.'));
                return $this->redirect(['action' => 'index']);
            }
            //die;
        }
    }




    public function status($id, $status)
    {
        $this->loadModel('Taxmaster');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {

                $status = 'N';
                $user = $this->Taxmaster->get($id);

                $user->status = $status;
                if ($this->Taxmaster->save($user)) {
                    $this->Flash->success(__('Tax Type status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Taxmaster->get($id);
                $user->status = $status;
                if ($this->Taxmaster->save($user)) {
                    $this->Flash->success(__('Tax Type status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $this->loadModel('Taxmaster');

        //pr($this->Users->get($id)); die;
        try {
            $status = 'N';
            $user = $this->Taxmaster->get($id);
            $user->status = $status;
            if ($this->Taxmaster->save($user)) {

                // $delete_record = 'DELETE FROM `st_categorymaster` WHERE `id` =' . $id;
                // $conn->execute($delete_record);
                $this->Flash->success(__('Tax Type with id: {0} has been deleted.', h($id)));
                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {

            $this->Flash->error(__('This Tax Type is used so you cannot delete this Item category detail'));
            $this->set('error', $error);
            return $this->redirect(['action' => 'index']);
        }
        die;
    }
}
