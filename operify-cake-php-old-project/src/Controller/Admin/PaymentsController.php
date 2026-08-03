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


class PaymentsController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        parent::initialize();
    }
    public function index($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Payments');
        $this->loadModel('Vendors');

        $req_data = $_GET;
        $vendor_id = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $apk = [];
        if (!empty($vendor_id)) {
            $apk['Payments.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Payments.bill_date) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Payments.bill_date) <='] = $dateto2;
        }

        if ($req_data) {
            $payments = $this->Payments->find('all')->where([$apk, 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC']);
                $date = $dateto2;
        } else {
            $vendor_id = $this->Vendors->find('all')->order(['Vendors.name' => 'ASC'])->first();
            $payments = $this->Payments->find('all')->where(['Payments.vendor_id' => $vendor_id['id'], 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC']);
            $date = date('Y-m-d');
        }
        $payments = $this->paginate($payments)->toarray();
        $this->set(compact('payments', 'vendor_id','date'));
    }


    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Payments');

        $payments = $this->Payments->find('all')->order(['Payments.id' => 'DESC'])->where(['Payments.store_type' => '2', 'Payments.status' => 'Y',])->first();
        if ($payments) {
            $receipt_no = $payments['receipt_no'] + 1;
        } else {
            $receipt_no = 101;
        }
        $this->set(compact('receipt_no'));


        if ($this->request->is(['post', 'put'])) {

            $item['bill_date'] = date('Y-m-d', strtotime($this->request->data['pay_date']));
            $item['pay_date'] = date('Y-m-d', strtotime($this->request->data['pay_date']));
            $item['receipt_no'] = $this->request->data['receipt_no'];
            $item['vendor_id'] = $this->request->data['vendor_id'];
            $item['total_amt'] = $this->request->data['total_amt'];
            $item['remark'] = $this->request->data['remark'];
            $item['store_type'] = '2';

            $pnewdetail = $this->Payments->patchEntity($this->Payments->newEntity(), $item);
            if ($resustnew = $this->Payments->save($pnewdetail)) {
                $this->Flash->success(__('Payment successfully added.'));
                return $this->redirect(['action' => 'index']);
            }

        }
    }

    public function delete($id)
    {
        $this->loadModel('Payments');
        if (isset($id) && !empty($id)) {
            $status = 'N';
            $user = $this->Payments->get($id);
            $user->status = $status;
            if ($this->Payments->save($user)) {
                $this->Flash->success(__('Payment has been deleted.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function searchitem()
    {
        $this->loadModel('Payments');
        $req_data = $_GET;
        $vendor_id = $req_data['vendor_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));

        $apk = [];
        if (!empty($vendor_id)) {
            $apk['Payments.vendor_id'] = $vendor_id;
        }
        if ($datefrom != '1970-01-01') {
            $apk['DATE(Payments.bill_date) >='] = $datefrom;
        }
        if ($dateto2 != '1970-01-01') {
            $apk['DATE(Payments.bill_date) <='] = $dateto2;
        }

        
        $date = $datefrom;

        $this->request->session()->write('apk', $apk);
        $payments = $this->Payments->find('all')->where([$apk, 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC']);
        $payments = $this->paginate($payments)->toarray();
        $this->set(compact('payments','date'));
    }

    public function viewpdf()
    {
        $this->loadModel('Payments');
        $this->loadModel('Vendors');

        $where = $this->request->session()->read('apk');
        
        if ($where) {
            $payments = $this->Payments->find('all')->where([$where, 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC'])->toarray();
            $date = date("Y-m-d", strtotime($where['DATE(Payments.bill_date) <=']));
            $this->set(compact('payments','where','date'));
            $this->request->session()->delete('apk');
        } else {
            $date = date('Y-m-d');
            $vendor_id = $this->Vendors->find('all')->order(['Vendors.name' => 'ASC'])->first();
            $payments = $this->Payments->find('all')->where(['Payments.bill_date' => $date,'Payments.vendor_id' => $vendor_id['id'], 'Payments.status' => 'Y'])->order(['Payments.bill_date' => 'ASC', 'Payments.id' => 'ASC'])->toarray();
            $this->set(compact('payments','date'));
        }


    }

    public function updategrnquery()
    {
        $this->loadModel('Goodsreceived');
        $goodsreceivedno = $this->Goodsreceived->find('all')->order(['Goodsreceived.id' => 'ASC'])->toarray();
        foreach ($goodsreceivedno as $value) {
            $vendorid = $value['vendor_id'];
            $inwarddate = date('Y-m-d' ,strtotime($value['inwarddate']));
            $bill_no = $value['bill_no'];
            $bill_date = date('Y-m-d' ,strtotime($value['bill_date']));
            $total_amt = $value['total_amt'];
            $remark = $value['remark'];
            $id = $value['id'];
            $created_date = date('Y-m-d' ,strtotime($value['created_date']));

            $connsss = ConnectionManager::get('default');
            $dbname = $this->request->session()->read('Auth.User.db');

            $status_update =  "INSERT INTO $dbname.payments (`vendor_id`, `inwarddate`, `bill_no`, `bill_date`, `total_amt`, `remark`, `status`, `created_date`, `goods_id`) VALUES ($vendorid, '$inwarddate', '$bill_no', '$bill_date', $total_amt, '$remark', 'Y','$created_date','$id')";

            $connsss->execute($status_update);
        }
        echo 'dfhs';die;
    }


    public function getvendorname()
    {
        $this->loadModel('Payments');
        $this->loadModel('Vendors');

        $vendor_id = $this->request->data['vendor_id'];
        $vendordetails = $this->Vendors->find('all')->where(['Vendors.id' => $vendor_id])->first();

        $creditamt = $this->Payments->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.store_type IN' => ['1']])->first();

        $debitamt = $this->Payments->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.status' => 'Y', 'Payments.store_type IN' => ['2']])->first();

        $amount = $creditamt['sum'] - $debitamt['sum'];
        $balance = $amount ? $amount : '0';

        $response['vendordetails'] = $vendordetails;
        $response['balance'] = $balance;
        echo json_encode($response);
        die;
    }

}
