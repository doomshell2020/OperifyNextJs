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

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
class ProductionController extends AppController
{

    public function index()
    {
        $this->viewBuilder()->layout('admin');

        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Plannedtype');

        $item = $this->Production->find('all')->contain(['Machinemaster', 'Plannedtype'])->order(['Production.id' => 'desc'])->toarray();
        $this->set('production_data', $item);
    }


    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Plannedtype');

        $plannedtype = $this->Plannedtype->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['Plannedtype.id' => 'asc'])->toarray();
        $this->set('plannedtype', $plannedtype);


        $productions = $this->Production->newEntity();

        if ($this->request->is(['post'])) {


            $attnExist = $this->Production->exists(['machine_id' => $this->request->data['machines_id'], 'production_date' => date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']))]);

            $date = date('Y-m-d');
            if ($attnExist) {
                $this->Flash->error(__('Entry For ' . $date . ' Already Exists.'));
                return $this->redirect(['action' => 'Add']);
            }

            // pr($this->request->data); die;
            $data['production_date'] =  date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));;
            $data['machine_id'] = $this->request->data['machines_id'];
            $data['description'] = $this->request->data['description'];
            $data['planned'] = $this->request->data['planned'];
            $data['planned_type'] = $this->request->data['planned_type'];
            $data['production_shift_a'] = $this->request->data['production_shift_a'];
            $data['production_shift_b'] = $this->request->data['production_shift_b'];
            $data['production_shift_c'] = $this->request->data['production_shift_c'];
            $data['break_down_time'] = $this->request->data['break_down_time'];
            $data['break_down_hrs'] = $this->request->data['break_down_hrs'];
            $data['material_issued'] = $this->request->data['material_issued'];
            $data['material_consumed_desgin'] = $this->request->data['material_issued_desgin'];
            $data['material_consumed_actual'] = $this->request->data['material_issued_actual'];
            $data['balance'] = $this->request->data['balance'];
            $data['scrap_shfit_a'] = $this->request->data['scrap_shfit_a'];
            $data['scrap_shfit_b'] = $this->request->data['scrap_shfit_b'];
            $data['scrap_shfit_c'] = $this->request->data['scrap_shfit_c'];
            $data['remark'] = $this->request->data['remark'];
            $cats = $this->Production->patchEntity($productions, $data);

            if ($this->Production->save($cats)) {
                $this->Flash->success(__('Item Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    public function edit($id)
    {
        // pr($id);exit;
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Production');
        $this->loadModel('Plannedtype');
        $this->loadModel('Machinemaster');

        $plannedtype = $this->Plannedtype->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['Plannedtype.id' => 'asc'])->toarray();
        $this->set('plannedtype', $plannedtype);

        $productions = $this->Production->get($id);
        $this->set('productions', $productions);

        $machine_id = $productions['machine_id'];

        $machine_name = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $machine_id])->first();
        $this->set('machine_name', $machine_name);



        if ($this->request->is(['post', 'put'])) {

            $productions_data['production_date'] =  date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));;
            $productions_data['machine_id'] = $this->request->data['machines_id'];
            $productions_data['description'] = $this->request->data['description'];
            $productions_data['planned'] = $this->request->data['planned'];
            $productions_data['planned_type'] = $this->request->data['planned_type'];
            $productions_data['production_shift_a'] = $this->request->data['production_shift_a'];
            $productions_data['production_shift_b'] = $this->request->data['production_shift_b'];
            $productions_data['production_shift_c'] = $this->request->data['production_shift_c'];
            $productions_data['break_down_time'] = $this->request->data['break_down_time'];
            $productions_data['break_down_hrs'] = $this->request->data['break_down_hrs'];
            $productions_data['material_issued'] = $this->request->data['material_issued'];
            $productions_data['material_consumed_desgin'] = $this->request->data['material_consumed_desgin'];
            $productions_data['material_consumed_actual'] = $this->request->data['material_consumed_actual'];
            $productions_data['balance'] = $this->request->data['balance'];
            $productions_data['scrap_shfit_a'] = $this->request->data['scrap_shfit_a'];
            $productions_data['scrap_shfit_b'] = $this->request->data['scrap_shfit_b'];
            $productions_data['scrap_shfit_c'] = $this->request->data['scrap_shfit_c'];
            $productions_data['remark'] = $this->request->data['remark'];


            $slid = $this->Production->patchEntity($productions, $productions_data);
            //pr($slid);exit;

            if ($slider_save = $this->Production->save($slid)) {
                $this->Flash->success(__('Data Update has been updated Successfully.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function getname()
    {
        $this->loadModel('Machinemaster');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Machinemaster->find('all')->where(['Machinemaster.machine_name LIKE' => $stsearch . '%', 'Machinemaster.status' => 'Y'])->toarray();

        foreach ($searchst as $value) {

            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['machine_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['machine_name'] . '</a></li>';
        }
        die;
    }


    public function export_excel()
    {
        $this->loadModel('Production');
        $this->loadModel('Machinemaster');
        $this->loadModel('Plannedtype');

        $item = $this->Production->find('all')->contain(['Machinemaster', 'Plannedtype'])->order(['Production.id' => 'desc'])->toarray();
        $this->set('production_data', $item);
    }



    function billsofmaterials()
    {

        $this->viewBuilder()->layout('admin');
    }

    function addbom()
    {

        $this->viewBuilder()->layout('admin');
    }

    function productionorders()
    {

        $this->viewBuilder()->layout('admin');
    }



    function operationreports()
    {

        $this->viewBuilder()->layout('admin');
    }


    function productionoperations()
    {

        $this->viewBuilder()->layout('admin');
    }

    function addproductionoperations()
    {

        $this->viewBuilder()->layout('admin');
    }

    function addproductionorders()
    {

        $this->viewBuilder()->layout('admin');
    }

    function labourcosts()
    {

        $this->viewBuilder()->layout('admin');
    }
    function routings()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addroutings()
    {


        $this->viewBuilder()->layout('admin');
    }
    function overview()
    {


        $this->viewBuilder()->layout('admin');
    }
    function audit()
    {


        $this->viewBuilder()->layout('admin');
    }
}
