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


class TransporterController extends AppController
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
        $this->loadModel('Transporter');
        $this->loadModel('Vendors');


        $req_data = $_GET;
        $transport_id = $req_data['transports_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (!empty($transport_id)) {
            $contra = ['Transporter.transport_id' => $transport_id];
            $cond[] = $contra;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Transporter.datefrom) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Transporter.datefrom) <=' => $dateto2];
            $cond[] = $contra;
        }

        $array = ['datefrom' => $datefrom, 'dateto2' => $dateto2];

        if($req_data){
            $data = $this->Transporter->find('all')->contain('Vendors')->where([$cond, 'Transporter.status' => 'Y'])->order(['Transporter.datefrom' => 'Desc']);
        }else{
            $data = $this->Transporter->find('all')->contain('Vendors')->where(['Transporter.status' => 'Y'])->order(['Transporter.datefrom' => 'Desc']);
        }

        $data = $this->paginate($data)->toarray();
        $this->set('data', $data);
    }

    //add function 
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Transporter');

        if ($this->request->is(['post'], ['put'])) {
            if ($this->request->data['transports_id'] == "") {
                $this->Flash->error(__('Your entered transporter does not exists.'));
                return $this->redirect(['action' => 'add']);
            } else {
                $contr = $this->Transporter->newEntity();
                $tulam['transport_id'] = $this->request->data['transports_id'];
                $tulam['transport_to'] = $this->request->data['to'];
                $tulam['transport_from'] = $this->request->data['from'];
                $tulam['vehicle_no'] = $this->request->data['vehicle_no'];
                $tulam['gr_no'] = $this->request->data['gr_no'];
                $tulam['weight'] = $this->request->data['weight'];
                $tulam['freight'] = $this->request->data['freight'];
                $tulam['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));

                $tmp_name = $this->request->data['upload_doc']['tmp_name'];
                $image_name = $this->request->data['upload_doc']['name'];
                $pext = pathinfo($image_name, PATHINFO_EXTENSION);
                $imagenewname = time() . md5($image_name) . '.' . $pext;
                $webroot = WWW_ROOT;
                $newfile = $webroot . 'transporterupload/' . $imagenewname;
                if (move_uploaded_file($tmp_name, $newfile)) {
                $tulam['upload'] = $imagenewname;
                }
                $transporterdetail = $this->Transporter->patchEntity($contr, $tulam);
                $save = $this->Transporter->save($transporterdetail);

                $this->Flash->success(__('Transport details added successfully.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }




    // edit function 
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Transporter');
        $this->loadModel('Vendors');


        $users = $this->Transporter->get($id);
        $transport_id = $users['transport_id'];
        $company_name = $this->Vendors->find('all')->where(['Vendors.id' => $transport_id])->first();

        $this->set(compact('users', 'company_name'));

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['transports_id'] == "") {
                $this->Flash->error(__('Your entered transporter does not exists.'));
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $tulam['transport_id'] = $this->request->data['transports_id'];
                $tulam['transport_to'] = $this->request->data['transport_to'];
                $tulam['transport_from'] = $this->request->data['transport_from'];
                $tulam['vehicle_no'] = $this->request->data['vehicle_no'];
                $tulam['gr_no'] = $this->request->data['gr_no'];
                $tulam['weight'] = $this->request->data['weight'];
                $tulam['freight'] = $this->request->data['freight'];
                $tulam['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));
                $tulam['updated'] = date('Y-m-d H:i:s');

                if ($this->request->data['upload_doc']['name'] != '') {

                    $tmp_name = $this->request->data['upload_doc']['tmp_name'];
                    $image_name = $this->request->data['upload_doc']['name'];
                    $pext = pathinfo($image_name, PATHINFO_EXTENSION);
                    $imagenewname = time() . md5($image_name) . '.' . $pext;
                    $newfile = '/var/www/html/Farmwork/tpplerp/webroot/transporterupload/' . $imagenewname;

                    if (move_uploaded_file($tmp_name, $newfile)) {
                    }
                    $tulam['upload'] = $imagenewname;
                } else {
                    $tulam['upload'] = $users['upload'];
                }


                // pr($tulam['upload']);die;

                $transporterdetail = $this->Transporter->patchEntity($users, $tulam);
                // pr($transporterdetail);die;

                if ($this->Transporter->save($transporterdetail)) {

                    $this->Flash->success(__('Transport details updated successfully .'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }




    // status     
    public function status($id, $status)
    {
        $this->loadModel('Transporter');
        $user = $this->Transporter->get($id);
        $user->status = $status;
        if ($this->Transporter->save($user)) {
            $this->Flash->success(__('Transporter details deleted successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }



    public function searchitem()
    {
        $this->loadModel('Transporter');
        $this->loadModel('Vendors');

        $req_data = $_GET;
        $transport_id = $req_data['transports_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (!empty($transport_id)) {
            $contra = ['Transporter.transport_id' => $transport_id];
            $cond[] = $contra;
        }

        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Transporter.datefrom) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto2 !== '1970-01-01') {
            $contra = ['DATE(Transporter.datefrom) <=' => $dateto2];
            $cond[] = $contra;
        }

        $array = ['datefrom' => $datefrom, 'dateto2' => $dateto2];

        $this->request->session()->write('cond', $cond);
        $this->request->session()->write('array', $array);

        $user = $this->Transporter->find('all')->contain('Vendors')->where([$cond, 'Transporter.status' => 'Y'])->order(['Transporter.datefrom' => 'Desc']);
        $user = $this->paginate($user)->toarray();
        $this->set('data', $user);
    }



    public function viewpdf()
    {
        $this->loadModel('Transporter');
        $this->loadModel('Vendors');

        $where = $this->request->session()->read('cond');
        $search = $this->request->session()->read('array');

        if (isset($where)) {
            $data = $this->Transporter->find('all')->contain('Vendors')->where([$where, 'Transporter.status' => 'Y'])->order(['Transporter.datefrom' => 'Desc'])->toarray();
            $this->set('data', $data);
            $this->set('contra', $search);
            $this->request->session()->delete('cond');
            $this->request->session()->delete('array');
        } else {
            $data = $this->Transporter->find('all')->contain('Vendors')->where(['Transporter.status' => 'Y'])->order(['Transporter.datefrom' => 'Desc'])->toarray();
            $this->set('data', $data);
        }

        $this->response->type('pdf');
    }
    public function addtransporter()
    {
        $this->loadModel('Transporter');
        $this->loadModel('Vendor');
        $this->loadModel('States');
        $state = $this->States->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['States.id' => 'Asc'])->toarray();
        $this->set('state', $state);

        // $name = $this->request->data;
        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data);die;
            $new = $this->Vendor->newEntity();
            $transp['name'] = $this->request->data['name'];
            $transp['contact_no'] = $this->request->data['contact_no'];
            $transp['email'] = $this->request->data['email'];
            $transp['type'] = "Transporter";
            $transp['state_id'] = $this->request->data['billtostate_id'];
            $transp['address'] = $this->request->data['billtoaddress'];
            $transp['gst_number'] = $this->request->data['billtogst_number'];
            $transp['pancard_number'] = $this->request->data['pancard_number'];

            $transporter = $this->Vendor->patchEntity($this->Vendor->newEntity(), $transp);
            $result = $this->Vendor->save($transporter);
            $this->set('result', $result);
        }
    }
}
