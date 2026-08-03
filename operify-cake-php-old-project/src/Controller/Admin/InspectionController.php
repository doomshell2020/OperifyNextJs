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


class InspectionController extends AppController
{
    public function initialize()
    {

        parent::initialize();
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('InspectionReport');
        $users = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y"])->order(['InspectionReport.id' => 'Desc'])->toarray();

        $this->set(compact('users'));
    }

    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('InspectionReport');

        $cat = $this->InspectionReport->newEntity();
        if ($this->request->is(['post', 'put'])) {

            if($this->request->data('wo_no') ==''){
                $this->Flash->error(__('Your enterd Contract does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            $item['name'] = $this->request->data['name'];
            $item['work_order_no'] = $this->request->data['wo_no'];
            $item['remark'] = $this->removeEmojis($this->request->data['remark']);
            $item['inspection_date'] = date('Y-m-d', strtotime($this->request->data['inspection_date']));

            if (isset($this->request->data['doc_upload']['name']) && !empty($this->request->data['doc_upload']['name'])) {

                $tmp_name = $this->request->data['doc_upload']['tmp_name'];
                $image_name = $this->request->data['doc_upload']['name'];
                $pext = pathinfo($image_name, PATHINFO_EXTENSION);
                $imagenewname = md5(microtime($filename)) . '.' . $pext;

                $dest = "InspectionReport/";
                $newfile = $dest . $imagenewname;
                // pr($newfile);exit;
                if (move_uploaded_file($tmp_name, $newfile)) {
                    $item['file'] = $imagenewname;
                }
            }
            $pnewdetail = $this->InspectionReport->patchEntity($cat, $item);
            if ($resustnew = $this->InspectionReport->save($pnewdetail)) {
                $this->Flash->success(__('Inspection Report Successfully Added.'));
                return $this->redirect(['action' => 'index']);
            }
            //pr($resustnew);  die;
        }
    }


    public function getworkorderID()
    {
        $this->loadModel('Contracts');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Contracts->find('all')->where(['Contracts.workorder' => $stsearch])->group(['Contracts.workorder'])->toarray();
        foreach ($searchst as $value) {
            echo '<li  style="padding: 5px 8px;; border: 1px solid lightgray; list-style:none;" onclick="cllbckretail' . '(' . $value['workorder'] . ')"><a href="javascript:void(0)" style="color: black;">' . $value['workorder'] . '</a></li>';
        }
        die;
    }


    public function searchitem()
    {
        $this->loadModel('InspectionReport');

        $work_order_no = $this->request->data['work_order_no'];
        $inspection_date = date('Y-m-d', strtotime($this->request->data['inspection_date']));

        $cond = [];
        if (isset($work_order_no) && $work_order_no != '') {
            $cond['InspectionReport.work_order_no'] = $work_order_no;
        }

        if (isset($inspection_date) && $inspection_date != '') {

            $cond['DATE(InspectionReport.inspection_date)'] = $inspection_date;
        }

        $user = $this->InspectionReport->find('all')->where(['InspectionReport.status' => 'Y', $cond])->order(['InspectionReport.id' => 'Desc'])->toarray();
        $this->set('users', $user);

    }

    public function delete($id = null)
    {
        $this->loadModel('InspectionReport');
        $res = $this->InspectionReport->get($id);
        // unlink("InspectionReport/" . $res['file']);
        $res->status = 'N';
        if ($this->InspectionReport->save($res)) {
            $this->Flash->success('item has been deleted successfully');
            return $this->redirect(['action' => 'index']);
        }
    }
}
