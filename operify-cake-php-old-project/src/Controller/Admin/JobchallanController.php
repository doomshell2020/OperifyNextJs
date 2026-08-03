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

class JobchallanController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('JobChallans');
        $this->loadModel('JobChallanItems');
        $this->loadModel('SubContractors');
        $this->loadModel('Taxmaster');
        $this->loadModel('Stockregister');
        $this->loadModel('JobChallanReceives');
        $this->loadModel('Additem');
    }

    // 📋 LIST + FILTER
    public function index()
    {

        $this->viewBuilder()->layout('admin');
        $query = $this->JobChallans->find()->contain(['SubContractors', 'JobChallanItems']);

        $data = $this->request->data;

        // if (!empty($data['vendor_id'])) {
        //     $query->where(['vendor_id' => $data['vendor_id']]);
        // }

        if (!empty($data['status'])) {
            $query->where(['status' => $data['status']]);
        }

        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $query->where([
                'date >=' => $data['from_date'],
                'date <=' => $data['to_date']
            ]);
        }

        $jobChallans = $this->paginate($query);
        // pr($jobChallans); die;

        $subContractors = $this->SubContractors->find('list');

        $this->set(compact('jobChallans', 'subContractors'));
    }

    // ➕ ADD
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $entity = $this->JobChallans->newEntity();

        $taxMaster = $this->Taxmaster->find('list', ['keyField' => 'tax', 'valueField' => 'tax'])
            ->where(['Taxmaster.status' => 'Y'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('taxMaster', $taxMaster);




        if ($this->request->is('post')) {

            $data = $this->request->data;


            // Challan No Auto
            $data['challan_no'] = 'JC-' . rand(10001, 99999);
            $data['jc_date'] = date('Y-m-d', strtotime($this->request->data['jc_dates']));

            $total = 0;
            $total_tax = 0;

            // ✅ ITEM LOOP
            if (!empty($data['job_challan_items'])) {

                foreach ($data['job_challan_items'] as $item) {

                    $qty = isset($item['quantity']) ? (float)$item['quantity'] : 0;
                    $rate = isset($item['rate']) ? (float)$item['rate'] : 0;
                    $taxRate = isset($item['tax_rate']) ? (float)$item['tax_rate'] : 0;

                    // Basic Amount
                    $item['amount'] = $qty * $rate;

                    // Tax Amount
                    $item['tax_amount'] = ($item['amount'] * $taxRate) / 100;

                    // Total
                    $item['total'] = $item['amount'] + $item['tax_amount'];

                    // Optional GST split
                    $item['cgst'] = $item['tax_amount'] / 2;
                    $item['sgst'] = $item['tax_amount'] / 2;

                    // Grand totals
                    $total += $item['amount'];
                    $total_tax += $item['tax_amount'];
                }
            }

            // ✅ MASTER TOTAL
            $data['total_amount'] = $total;
            $data['gst_amount'] = $total_tax;
            $data['final_amount'] = $total + $total_tax;



            $entity = $this->JobChallans->patchEntity($entity, $data, [
                'associated' => ['JobChallanItems']
            ]);

            $result = $this->JobChallans->save($entity);
            // pr($entity); die;
            if ($result) {

                $newsr = $this->Stockregister->newEntity();
                $newsrentity['indent_id'] = $result['id'];
                $newsrentity['contract_id'] = 0;
                $newsrentity['item_id'] = $result['job_challan_items'][0]['item_id'];
                $newsrentity['quantity'] = $result['job_challan_items'][0]['quantity'];
                $newsrentity['finishedproduct_id'] = 0;
                $newsrentity['issue_date'] = date('Y-m-d', strtotime($result['jc_date']));
                $newsrentity['store_type'] = '2';
                $newsrentity['sub_contractors_id'] = $result['sub_contractors_id'];
                $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                $ponewsr = $this->Stockregister->save($podetail);
                // pr($ponewsr); die;
                $this->Flash->success('Challan Created');
                return $this->redirect(['action' => 'index']);
            }
        }

        $subContractors = $this->SubContractors->find('list');
        $this->set(compact('entity', 'subContractors'));
    }


    // 👁️ VIEW
    public function view($id)
    {
        $this->viewBuilder()->layout('admin');

        $data = $this->JobChallans->get($id, [
            'contain' => ['JobChallanItems', 'SubContractors']
        ]);

        $this->set(compact('data'));
    }

    // 🗑 DELETE
    public function delete($id)
    {
        // $this->request->allowMethod(['post']);

        $this->loadModel('Stockregister');


        $challan = $this->JobChallans->get($id);

        // ✅ CHECK RECEIVE DATA EXISTS OR NOT
        $receiveExists = $this->JobChallanReceives->find()
            ->where(['challan_id' => $id])
            ->count();

        if ($receiveExists > 0) {

            $this->Flash->error('Material already received for this challan. Delete not allowed!');
            return $this->redirect(['action' => 'index']);
        }




        $this->Stockregister->deleteAll([
            'indent_id' => $id,
            'store_type IN' => ['2', '1'] // dispatch entry
        ]);

        if ($this->JobChallans->delete($challan)) {
            $this->Flash->success('Challan and stock deleted successfully');
        } else {
            $this->Flash->error('Unable to delete challan');
        }

        return $this->redirect(['action' => 'index']);
    }



    public function getitemname()
    {


        $this->loadModel('Additem');
        $this->loadModel('Sizemanager');
        //pr($this->request->data); die;
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        //echo $stsearch; die;       
        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y'])->toarray();
        // pr($searchst); die;        

        foreach ($searchst as $value) {



            echo '<li data-id="' . $value['id'] . '">' . $value['item_name'] . '</li>';
        }


        die;
    }

    public function getVendorGst()
    {
        $this->autoRender = false;

        $id = $this->request->data('vendor_id');

        $vendor = $this->SubContractors->find()
            ->where(['id' => $id])
            ->first();

        if ($vendor) {
            echo $vendor->gst_no;
        } else {
            echo '';
        }
    }

    // for item based In Hand Stock Fetch
    public function getItemInHandStock()
    {

        $this->autoRender = false;
        $id = $this->request->data('item_id');

        $articles = TableRegistry::get('Stockregister');
        $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type IN' => ['2', '4']])->first();

        echo $currentStock = $grnStock['sum'] - $indentStock['sum'];
    }

    // for export pdf
    public function viewpdf($job_id)
    {
        $jc_data = $this->JobChallans->find('all')->contain(['SubContractors', 'JobChallanItems'])->where(['JobChallans.id' => $job_id])->order(['JobChallans.id' => 'desc'])->first();
        $this->set('jc_data', $jc_data);
    }

    public function itemreceived($challan_id, $dispatchItemID)
    {
        $this->loadModel('JobChallanReceives');
        $this->loadModel('Jobchallans');
        $this->loadModel('JobChallanItems');
        $this->loadModel('Taxmaster');

        // Tax dropdown
        $taxMaster = $this->Taxmaster->find('list', [
            'keyField' => 'tax',
            'valueField' => 'tax'
        ])
            ->where(['Taxmaster.status' => 'Y'])
            ->order(['Taxmaster.id' => 'asc'])
            ->toArray();

        $this->set('taxMaster', $taxMaster);

        $job = $this->JobChallanReceives->newEntity();


        if ($this->request->is('post')) {

            $data = $this->request->data;
            // pr($data);

            foreach ($data['job_challan_items'] as $item) {

                // ✅ skip empty rows
                if (empty($item['item_id']) || empty($item['quantity'])) {
                    continue;
                }

                // ✅ DATE CONVERT
                $receive_date = date('Y-m-d', strtotime($item['received_date']));

                $item_id = $item['item_id'];
                $currentReceive = $item['quantity'];

                // // 🔥 GET DISPATCH QTY
                $dispatch = $this->JobChallanItems->find()
                    ->where([
                        'item_id' => $item_id,
                        'challan_id' => $challan_id
                    ])
                    ->first();

                $dispatch_qty = $dispatch ? $dispatch->quantity : 0;

                $jc_data = $this->JobChallans->find('all')->where(['JobChallans.id' => $challan_id])->first();

                // // 🔥 TOTAL RECEIVED
                // $totalReceived = $this->JobChallanReceives->find()
                //     ->where([
                //         'item_id' => $item_id,
                //         'challan_id' => $challan_id
                //     ])
                //     ->sumOf('received_qty');

                // // ❌ VALIDATION
                // if (($totalReceived + $currentReceive) > $dispatch_qty) {

                //     $this->Flash->error('Received qty exceeds dispatched qty for item ID: ' . $item_id);
                //     return $this->redirect($this->referer());
                // }

                // ✅ PENDING
                // $pending = $dispatch_qty - ($totalReceived + $currentReceive);

                // ✅ SAVE DATA
                $saveData = [
                    'challan_id'   => $challan_id,
                    'item_id'      => $item_id,
                    'dispatch_qty' => $dispatch_qty,
                    'received_qty' => $currentReceive,
                    // 'pending_qty'  => $pending,
                    'receive_date' => $receive_date,
                    'remarks'      => $item['remarks'] ?? '',
                    'sub_contractors_id' => $jc_data['sub_contractors_id']
                ];

                $entity = $this->JobChallanReceives->newEntity($saveData);
                // pr($entity); 
                $result =  $this->JobChallanReceives->save($entity);

                if ($result) {

                    $newsr = $this->Stockregister->newEntity();
                    $newsrentity['indent_id'] = $result['challan_id'];
                    $newsrentity['contract_id'] = 0;
                    $newsrentity['item_id'] = $result['item_id'];
                    $newsrentity['quantity'] = $result['received_qty'];
                    $newsrentity['finishedproduct_id'] = 0;
                    $newsrentity['issue_date'] = date('Y-m-d', strtotime($result['receive_date']));
                    $newsrentity['store_type'] = '1';
                    $newsrentity['sub_contractors_id'] = $result['sub_contractors_id'];
                    $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                    $this->Stockregister->save($podetail);
                }
            }

            // 🔥 STATUS UPDATE (AFTER LOOP)
            $jobChallan = $this->Jobchallans->get($challan_id);

            $totalDispatch = $this->JobChallanItems->find()
                ->where(['challan_id' => $challan_id])
                ->sumOf('quantity');

            $totalReceivedAll = $this->JobChallanReceives->find()
                ->where(['challan_id' => $challan_id])
                ->sumOf('received_qty');

            if ($totalReceivedAll == 0) {
                $status = 'Dispatched';
            } elseif ($totalReceivedAll < $totalDispatch) {
                $status = 'Partially Received';
            } else {
                $status = 'Received';
            }

            $jobChallan->status = $status;
            $this->Jobchallans->save($jobChallan);




            $this->Flash->success('All items received successfully');
            return $this->redirect(['action' => 'index']);
        }




        $this->set(compact('job'));
    }

    public function jcinfo($id)
    {
        $this->viewBuilder()->layout('admin');
        $dispatch_item_details = $this->JobChallanItems->find()
            ->select([
                'item_name',
                'total_qty' => $this->JobChallanItems->find()->func()->sum('quantity')
            ])
            ->where(['challan_id' => $id])
            ->group(['item_name'])
            ->toArray();

        $history = $this->JobChallanReceives->find()
            ->where([
                'JobChallanReceives.challan_id' => $id
            ])->contain(['Additem'])
            ->order(['JobChallanReceives.receive_date' => 'ASC'])
            ->toArray();


        $this->set(compact('dispatch_item_details', 'history'));
    }
}
