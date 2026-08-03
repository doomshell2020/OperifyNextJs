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


class CopperstockController extends AppController
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
        $this->loadModel('Copperstock');
        $this->loadModel('Additem');

        $data = $this->Additem->find('all')->where(['Additem.category_id' => '26',])->order(['Additem.id' => 'Desc'])->toarray();
        $this->set('data', $data);

        $curdate = date("Y-m-d");
        $copperstockname = $this->Copperstock->find('all')->where(['DATE(Copperstock.created_at)' => $curdate])->order(['Copperstock.id' => 'Desc'])->first();
          

        if ($this->request->is(['post'], ['put'])) {
            // pr($this->request->data);die;
            if ($copperstockname != '') {
                foreach ($this->request->data['type'] as $keyId => $indVal) {
                    $cstock11 = $this->Copperstock->find('all')->where(['DATE(Copperstock.created_at)' => $curdate, '(Copperstock.product_id)' => $keyId])->first();
                    $copper['product_id'] = $keyId;
                    $copper['type'] = $this->request->data['type'][$keyId];
                    $copper['tppl'] = $this->request->data['tppl'][$keyId];
                    $copper['kcpl'] = $this->request->data['kcpl'][$keyId];

                    $cstock1 = $this->Copperstock->patchEntity($cstock11, $copper);
                    $this->Copperstock->save($cstock1);
                 }
                 $this->Flash->success(__('Data Successfully Updated.'));
                 return $this->redirect(['action' => 'index']);
            } else {
                foreach ($this->request->data['type'] as $keyId => $indVal) {
                    $copper['product_id'] = $keyId;
                    $copper['type'] = $this->request->data['type'][$keyId];
                    $copper['tppl'] = $this->request->data['tppl'][$keyId];
                    $copper['kcpl'] = $this->request->data['kcpl'][$keyId];;

                    $cstock = $this->Copperstock->patchEntity($this->Copperstock->newEntity(), $copper);
                    $cstock = $this->Copperstock->patchEntity($this->Copperstock->newEntity(), $copper);
                    // pr($cstock);die;
                    $this->Copperstock->save($cstock);
                }
                $this->Flash->success(__('Data Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function searchitem()
    {
        $this->loadModel('Copperstock');
        $this->loadModel('Additem');

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $curdate =date("Y-m-d");

        $cond = [];
        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Copperstock.created_at)' => $datefrom];
        }else{
            $contra = ['DATE(Copperstock.created_at)' => $curdate]; 
        }

        $this->request->session()->write('contra', $datefrom);
        $this->request->session()->write('cond', $contra);

        $user = $this->Copperstock->find('all')->where([$contra])->contain('Additem')->order(['Copperstock.product_id' => 'Desc'])->toarray();
        $this->set('data', $user);
    }

    public function viewpdf()
    {
        $this->loadModel('Copperstock');
        $this->loadModel('Additem');

        $where = $this->request->session()->read('contra');
        $search = $this->request->session()->read('cond');
        $curdate = date("Y-m-d");

        if (isset($search)) {
            $data = $this->Copperstock->find('all')->contain('Additem')->where([$search])->order(['Copperstock.product_id' => 'Desc'])->toarray();
            $this->set('data', $data);
            $this->set('contra', $where);
            $this->request->session()->delete('contra');
            $this->request->session()->delete('cond');
        } else {
            $data = $this->Copperstock->find('all')->where(['DATE(Copperstock.created_at)' => $curdate])->contain('Additem')->order(['Copperstock.product_id' => 'Desc'])->toarray();
            $this->set('data', $data);
        }
        $this->response->type('pdf');
    }
}
