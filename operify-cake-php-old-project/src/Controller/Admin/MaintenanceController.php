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
use App\Controller\Component\WhatsAppComponent;
require_once 'Firebase.php';
require_once 'Push.php';

class MaintenanceController extends AppController
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

        $reqdata = $_GET;
        $machine_id = $reqdata['machines_id'];
        $assigned = $reqdata['assigned_to'];
        $mstatus = $reqdata['m_status'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));

        $cond = [];
        if (!empty($machine_id)) {
            $contra = ['Maintenance.machine_id' => $machine_id];
            $cond[] = $contra;
        }
        if (!empty($mstatus)) {
            $contra = ['Maintenance.maintenance_status' => $mstatus];
            $cond[] = $contra;
        }
        if (!empty($assigned)) {
            $contra = ['Maintenance.assigned_to LIKE' => '%' . $assigned . '%'];
            $cond[] = $contra;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Maintenance.datefrom) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Maintenance.datefrom) <=' => $dateto2];
            $cond[] = $contra;
        }


        if ($reqdata) {
            $user = $this->Maintenance->find('all')->contain('Machinemaster')->where([$cond, 'Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'Desc']);
        } else {
            $user = $this->Maintenance->find('all')->contain('Machinemaster')->where(['Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'Desc']);
        }



        $user = $this->paginate($user)->toarray();
        $this->set('data', $user);
    }


    //add function in Maintenance
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Maintenance');
        $this->loadModel('Device');
        $this->loadModel('Users');
        $this->loadModel('Role');
        $this->loadComponent('WhatsApp');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('Machinemaster');
        $this->loadModel('Users');
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();

        $maintenanceUsers = $this->Users->find('all')->where(['role_id IN' => ['104', '23']])->toarray();
        $assignedToOptions = [];
        foreach ($maintenanceUsers as $user) {
            $assignedToOptions[$user->id] = $user->user_name;
        }

        $maintenance = $this->Users->find('all')->where(['role_id IN' => ['111']])->toarray();
        $assigned1 = [];
        foreach ($maintenance as $use) {
            $assigned1[$use->id] = $use->user_name;
        }

        $maintenancein = $this->Users->find('all')->where(['role_id IN' => ['104', '112']])->toarray();
        $assignedin = [];
        foreach ($maintenancein as $use) {
            $assignedin[$use->id] = $use->user_name;
        }

        $maintenancepro = $this->Users->find('all')->where(['role_id IN' => ['102']])->toarray();
        $assignedpro = [];
        foreach ($maintenancepro as $use) {
            $assignedpro[$use->id] = $use->user_name;
        }

        $this->set(compact('assignedToOptions', 'assigned1', 'assignedin', 'assignedpro'));

        if ($this->request->is(['post'], ['put'])) {

            if ($this->request->data['machines_id'] == "") {
                $this->Flash->error(__('Your entered machine does not exists.'));
                return $this->redirect(['action' => 'add']);
            } else {
                // pr($this->request->data);die;

                $machineName = $this->request->data['machine_id'];
                $inchargeName = $this->request->data['maintenance_incharge'];
                $date = date('d-m-Y');
                $device_details = $this->Users->find('all')->contain(['Device'])->where(['Users.id' => 1])->toArray();
                foreach ($device_details as $key => $value) {
                    $deviceToken = $value['device']['token'];
                    $tokens[] = $deviceToken;
                }

                $message = 'A New Maintenance Request added by:' . $inchargeName . ' for Machine:' . $machineName . ' Dated:' . $date . '.';
                $push = new \Push(
                    'Maintenance',
                    $message
                );
                $firebase = new \Firebase();
                $mPushNotification = $push->getPush();
                $title = 'Maintenance';
                foreach ($tokens as $tok) {
                    $this->sendNotification($tok, $title, $message);
                }
                // $test = $firebase->send($tokens, $mPushNotification);
                // pr($test);die;

                $contr = $this->Maintenance->newEntity();
                $data['machine_id'] = $this->request->data['machines_id'];
                $data['breakdown_type'] = $this->request->data['breakdown_type'];
                $data['assigned_to'] = $this->request->data['assigned_to'];
                $data['total_time'] = $this->request->data['total_time'];
                $data['shift_incharge'] = $this->request->data['shift_incharge'];
                $data['maintenance_incharge'] = $this->request->data['maintenance_incharge'];
                $data['production_head'] = $this->request->data['production_head'];
                $data['remark'] = $this->removeEmojis($this->request->data['remark']);
                $data['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));

                $maintenancedetail = $this->Maintenance->patchEntity($contr, $data);
                $result = $this->Maintenance->save($maintenancedetail);
                if ($result) {
                    $company_name = $site_details['company_name'];
                    $to = '91'.$this->Users->get($this->request->data['assigned_to'])->mobile;
                    $machine_name = $this->Machinemaster->get($this->request->data['machines_id'])->machine_name;
                    $assigned = $this->Users->get($this->request->data['assigned_to'])->user_name;
                    $date = date('d-m-Y', strtotime($this->request->data['datefrom']));
                    $time = $this->request->data['total_time'];
                    $remarks = $this->removeEmojis($this->request->data['remark']);
                    $getResponce = $this->WhatsApp->feeapprovalwhatsappmessage($company_name, $to, $machine_name, $assigned, $date, $time, $remarks);
                }

                $this->Flash->success(__('Maintenance details added successfully.'));
                return $this->redirect(['action' => 'index']);
            }

        }
    }




    // edit function for contracts manager
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Maintenance');
        $this->loadModel('Machinemaster');

        $users = $this->Maintenance->get($id);
        $machine_id = $users['machine_id'];
        $machine_name = $this->Machinemaster->find('all')->where(['Machinemaster.id' => $machine_id])->first();

        $this->set(compact('users', 'machine_name'));

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['machines_id'] == "") {
                $this->Flash->error(__('Your entered machine does not exists.'));
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $mainten['breakdown_type'] = $this->request->data['breakdown_type'];
                $mainten['assigned_to'] = $this->request->data['assigned_to'];
                $mainten['total_time'] = $this->request->data['total_time'];
                $mainten['shift_incharge'] = $this->request->data['shift_incharge'];
                $mainten['maintenance_incharge'] = $this->request->data['maintenance_incharge'];
                $mainten['production_head'] = $this->request->data['production_head'];
                $mainten['remark'] = $this->removeEmojis($this->request->data['remark']);
                $mainten['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));
                $mainten['updated'] = date('Y-m-d H:i:s');
                $maintenancedetail = $this->Maintenance->patchEntity($users, $mainten);
                if ($this->Maintenance->save($maintenancedetail)) {

                    $this->Flash->success(__('Maintenance details successfully updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }

    }




    // status in  contracts manager  
    public function status($id, $status)
    {
        $this->loadModel('Maintenance');
        $user = $this->Maintenance->get($id);
        $user->status = $status;
        if ($this->Maintenance->save($user)) {
            $this->Flash->success(__('Maintenance details deleted successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }


    
    public function maintenancestatus()
    {
        $this->loadModel('Maintenance');
        $this->autoRender = false;

        $mstatus = $this->request->data('status');
        $id = $this->request->data('id');
    
        $maintenance = $this->Maintenance->get($id);
        $currentStatus = $maintenance->maintenance_status;
    
        if ($currentStatus == 'completed') {
            if ($mstatus == 'assigned' || $mstatus == 'pending') {
                $reponse = 'Cannot change status from completed to assigned or pending.';
                echo json_encode($reponse);
                die;
            }
        } elseif ($currentStatus == 'assigned') {
            if ($mstatus == 'pending') {
                $reponse = 'Cannot change status from assigned to pending.';
                echo json_encode($reponse);
                die;
            }
        }

        if ($this->request->data['status'] == 'assigned') {
            $maintenance->assigned_date = date('Y-m-d');
        }
    
        $maintenance->maintenance_status = $mstatus;
        if ($this->Maintenance->save($maintenance)) {
            $reponse = 'Maintenance status updated successfully..';
                echo json_encode($reponse);
                die;
        } 
    }


    //   public function ff()
    // {
    //     $this->loadModel('Maintenance');
    //     $this->autoRender = false;

    //     $mstatus = $this->request->data['status'];
    //     $id = $this->request->data['id'];

    //     $user = $this->Maintenance->get($id);
    //     $user->maintenance_status = $mstatus;

    //     $result = $this->Maintenance->save($user);
    //     $this->Flash->success(__('Maintenance status Updated successfully.'));
    //     $this->set('result', $result);

    // }
    





    public function searchitem()
    {
        $this->loadModel('Maintenance');

        $reqdata = $_GET;
        $machine_id = $reqdata['machines_id'];
        $assigned = $reqdata['assigned_to'];
        $mstatus = $reqdata['m_status'];
        $datefrom = date('Y-m-d', strtotime($reqdata['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($reqdata['dateto']));

        $cond = [];
        if (!empty($machine_id)) {
            $contra = ['Maintenance.machine_id' => $machine_id];
     
            $cond[] = $contra;
        }
        if (!empty($mstatus)) {
            $contra = ['Maintenance.maintenance_status' => $mstatus];
            $cond[] = $contra;
        }
        if (!empty($assigned)) {
            $contra = ['Maintenance.assigned_to LIKE' => '%' . $assigned . '%'];
            $cond[] = $contra;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Maintenance.datefrom) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Maintenance.datefrom) <=' => $dateto2];
            $cond[] = $contra;
        }

        $array = ['assigned' => $assigned, 'mstatus' => $mstatus, 'datefrom' => $datefrom, 'dateto2' => $dateto2];

        $this->request->session()->write('cond', $cond);
        $this->request->session()->write('contra', $array);

        $user = $this->Maintenance->find('all')->contain('Machinemaster')->where([$cond, 'Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'Desc']);
        $podata = $this->paginate($user)->toarray();
        $this->set('data', $user);

    }



    public function viewpdf()
    {
        $this->loadModel('Maintenance');

        $where = $this->request->session()->read('cond');
        $contra = $this->request->session()->read('contra');
        if (isset($where)) {
            $data = $this->Maintenance->find('all')->contain('Machinemaster')->where([$where, 'Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'Desc'])->toarray();

            $this->set(compact('data', 'contra'));
            $this->request->session()->delete('cond');
            $this->request->session()->delete('contra');
        } else {
            $data = $this->Maintenance->find('all')->contain('Machinemaster')->where(['Maintenance.status' => 'Y'])->order(['Maintenance.datefrom' => 'Desc'])->toarray();
            $this->set('data', $data);
        }
        $this->response->type('pdf');
    }





    public function completeMaintenance()
{
    $this->loadModel('Maintenance');
    if ($this->request->is('post')) {
      
        $maintenance = $this->Maintenance->get($this->request->data['maintenance_id']); 
        $maintenance->completion_date = $this->request->data['completion_date'];
        $maintenance->completion_time = $this->request->data['completion_time'];
        $maintenance->remarks = $this->request->data['remarks'];
        $maintenance->maintenance_status = 'completed'; 
        
        if ($this->Maintenance->save($maintenance)) {
            $this->Flash->success(__('Maintenance marked as completed.'));
        } else {
            $this->Flash->error(__('Unable to complete maintenance.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}


}