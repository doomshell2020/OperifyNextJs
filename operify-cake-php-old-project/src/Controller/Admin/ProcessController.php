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


class ProcessController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        parent::initialize();
    }
    


    public function status($id, $status)
    {
        $this->loadModel('Finishedprocess');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {

                $status = 'N';
                $user = $this->Finishedprocess->get($id);

                $user->status = $status;
                if ($this->Finishedprocess->save($user)) {
                    $this->Flash->success(__('Process Type status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }

            } else {
                $status = 'Y';
                $user = $this->Finishedprocess->get($id);
                $user->status = $status;
                if ($this->Finishedprocess->save($user)) {
                    $this->Flash->success(__('Process Type status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $this->loadModel('Finishedprocess');

        if (!$id) {
            $this->Flash->error(__('Invalid Process Type ID.'));
            return $this->redirect(['action' => 'index']);
        }

        try {
            $user = $this->Finishedprocess->get($id);

            if ($this->Finishedprocess->delete($user)) {
                $this->Flash->success(__('Process Type with id: {0} has been deleted.', h($id)));
            } else {
                $this->Flash->error(__('Unable to delete the Process Type with id: {0}.', h($id)));
            }
        } catch (\PDOException $e) {
            $this->Flash->error(__('This Process Type is in use and cannot be deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }




    public function index($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Finishedprocess');
        $users = $this->Finishedprocess->find('all')->order(['Finishedprocess.id' => 'Asc'])->toArray();
        $this->set(compact('users'));

        if (isset($id) && !empty($id)) {
            $rolesnew = $this->Finishedprocess->get($id);
        } else {
            $rolesnew = $this->Finishedprocess->newEntity();
        }

        $this->set('company', $rolesnew);

        if ($this->request->is(['post', 'put'])) {
        
            // if (!empty($this->request->data['id'])) {
            //     $id = $this->request->data['id'];
            // }

            $attnExist = $this->Finishedprocess->exists([
                'process_name' => trim($this->request->data['process_name'])
                // 'NOT' => ['id' => $id] 
            ]);

            if ($attnExist) {
                $this->Flash->error(__('The process name already exists.'));
                return $this->redirect(['action' => 'index']);
            }

            $rolesnew = $this->Finishedprocess->patchEntity($rolesnew, $this->request->data);

            if ($this->Finishedprocess->save($rolesnew)) {
                $this->Flash->success(__('Process Type successfully saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Unable to save the process type.'));
            }
        }
    }



}
