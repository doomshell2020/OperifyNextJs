<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

class SuppliersController extends AppController
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
        $this->loadModel('Suppliers');
        $users = $this->Suppliers->find('all')->order(['Suppliers.id' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));
    }

    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Suppliers');
        $cat = $this->Suppliers->newEntity();
        if ($this->request->is(['post'])) {
            //pr($this->request->data); die;
            $item['supplier_name'] = $this->request->data['supplier_name'];
            $item['contact_person'] = $this->request->data['contact_person'];
            $item['phone'] = $this->request->data['phone'];
            $item['email'] = $this->request->data['email'];
            $item['address'] = $this->request->data['address'];
            $item['vat_no'] = $this->request->data['vat_no'];
            $item['tin_no'] = $this->request->data['tin_no'];
            $item['tin_date'] = date('Y-m-d', strtotime($this->request->data['tin_date']));
            $item['description'] = $this->request->data['description'];
            $item['updated_time'] = "";
            $pnewdetail = $this->Suppliers->patchEntity($cat, $item);
            if ($resustnew = $this->Suppliers->save($pnewdetail)) {
                $this->Flash->success(__('Spplier successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
            //pr($resustnew);  die;
        }
    }

    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Suppliers');

        $itemcat = $this->Suppliers->find('All')->where(['Suppliers.id' => $id])->first();
        //pr($purid); die;
        $this->set('company', $itemcat);

        $cat = $this->Suppliers->get($id);
        //pr($cat); die;
        $conn = ConnectionManager::get('default');
        if ($this->request->is(['put'])) {
            $item['supplier_name'] = $this->request->data['supplier_name'];
            $item['contact_person'] = $this->request->data['contact_person'];
            $item['phone'] = $this->request->data['phone'];
            $item['email'] = $this->request->data['email'];
            $item['address'] = $this->request->data['address'];
            $item['vat_no'] = $this->request->data['vat_no'];
            $item['tin_no'] = $this->request->data['tin_no'];
            $item['tin_date'] = date('Y-m-d', strtotime($this->request->data['tin_date']));
            $item['description'] = $this->request->data['description'];
            $item['updated_time'] = date('Y-m-d H:i:s');
            $cats = $this->Suppliers->patchEntity($cat, $item);
            if ($resust = $this->Suppliers->save($cats)) {
                $this->Flash->success(__('Item company successfully updated.'));
                return $this->redirect(['action' => 'index']);
            }
            //die;

        }

    }

    public function status($id, $status)
    {
        $this->loadModel('Suppliers');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {

                $status = 'N';
                $user = $this->Suppliers->get($id);

                $user->status = $status;
                if ($this->Suppliers->save($user)) {
                    $this->Flash->success(__('Supplier status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }

            } else {
                $status = 'Y';
                $user = $this->Suppliers->get($id);
                $user->status = $status;
                if ($this->Suppliers->save($user)) {
                    $this->Flash->success(__('Supplier status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $this->loadModel('Suppliers');

        //pr($this->Users->get($id)); die;
        try {
            $user = $this->Suppliers->get($id);
            if ($this->Suppliers->delete($user)) {

                // $delete_record = 'DELETE FROM `st_categorymaster` WHERE `id` =' . $id;
                // $conn->execute($delete_record);
                $this->Flash->success(__('The Supplier with id: {0} has been deleted.', h($id)));
                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {

            $this->Flash->error(__('This Supplier is used so you cannot delete this Item category detail'));
            $this->set('error', $error);
            return $this->redirect(['action' => 'index']);
        }
        die;

    }

}
