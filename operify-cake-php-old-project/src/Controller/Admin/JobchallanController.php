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

    /**
     * Get the current company's name from st_companymaster (dynamic, no hardcoding)
     */
    private function getMyCompanyName()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('default');
        $row = $conn->execute("SELECT cname FROM st_companymaster WHERE main_branch = 'Y' LIMIT 1")->fetch('assoc');
        return $row ? trim($row['cname']) : '';
    }

    /**
     * Ensure a database connection exists for the given database name
     */
    private function ensureConnection($dbName)
    {
        try {
            return \Cake\Datasource\ConnectionManager::get($dbName);
        } catch (\Exception $e) {
            \Cake\Datasource\ConnectionManager::config($dbName, [
                'className' => 'Cake\Database\Connection',
                'driver' => 'Cake\Database\Driver\Mysql',
                'persistent' => false,
                'host' => DBHOSTNAME,
                'username' => MYSQLUSERNAME,
                'password' => MYSQLPASSWORD,
                'database' => $dbName,
                'encoding' => 'utf8mb4',
                'timezone' => 'UTC',
                'cacheMetadata' => true,
            ]);
            return \Cake\Datasource\ConnectionManager::get($dbName);
        }
    }

    /**
     * Get all sister company databases from local sub_contractors table
     * Returns array of ['id' => X, 'name' => 'Company Name', 'database_name' => 'db_name']
     */
    private function getSenderDatabases()
    {
        $currentDb = $this->request->session()->read('Auth.User.db');
        $conn = \Cake\Datasource\ConnectionManager::get('default');
        $rows = $conn->execute(
            "SELECT id, name, database_name FROM sub_contractors WHERE database_name IS NOT NULL AND database_name != '' AND database_name != :currentDb",
            ['currentDb' => $currentDb]
        )->fetchAll('assoc');
        return $rows;
    }


    // 📋 LIST + FILTER
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $query = $this->JobChallans->find()->contain(['SubContractors', 'JobChallanItems'])->order(['JobChallans.id' => 'DESC']);

        $data = $this->request->query;

        if (!empty($data['vendor_id'])) {
            $query->where(['JobChallans.sub_contractors_id' => $data['vendor_id']]);
        }

        if (!empty($data['status'])) {
            $query->where(['JobChallans.status' => $data['status']]);
        }
        
        if (!empty($data['challan_no'])) {
            $query->where(['JobChallans.challan_no LIKE' => '%' . $data['challan_no'] . '%']);
        }

        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $query->where([
                'JobChallans.jc_date >=' => $data['from_date'],
                'JobChallans.jc_date <=' => $data['to_date']
            ]);
        }

        $jobChallans = $this->paginate($query);

        $subContractors = $this->SubContractors->find('list');

        $this->set(compact('jobChallans', 'subContractors'));
        
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->layout(false);
            $this->render('ajax_index');
        }
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

            if (empty($data['challan_no'])) {
                $this->Flash->error("JC No. is required.");
                return $this->redirect($this->referer());
            }
            $data['challan_no'] = trim($data['challan_no']);

            if (!preg_match('/^[0-9]+$/', $data['challan_no'])) {
                $this->Flash->error("JC No. must contain only numeric values.");
                return $this->redirect($this->referer());
            }

            // Check if challan_no already exists
            $existingChallan = $this->JobChallans->find()
                ->where(['challan_no' => $data['challan_no']])
                ->first();
                
            if ($existingChallan) {
                $this->Flash->error("JC No. already exists. Please use a different JC No.");
                return $this->redirect($this->referer());
            }

            $data['jc_date'] = date('Y-m-d', strtotime($this->request->data['jc_dates']));

            // ✅ Process Type — default to Manufacturing
            $processType = !empty($data['processing_type']) ? trim($data['processing_type']) : 'Manufacturing';
            $data['processing_type'] = $processType;

            // Remove vehicle_no from JC header (it belongs to JC Receive, not JC Dispatch)
            unset($data['vehicle_no']);

            $total = 0;
            $total_tax = 0;

            // ✅ ITEM LOOP — for Raw Materials (both Manufacturing & In Progress)
            if (!empty($data['job_challan_items'])) {

                // Filter out completely empty rows
                $data['job_challan_items'] = array_values(array_filter($data['job_challan_items'], function($item) {
                    return !empty($item['item_id']) && isset($item['quantity']) && (float)$item['quantity'] > 0;
                }));

                if (empty($data['job_challan_items']) && $processType === 'Manufacturing') {
                    $this->Flash->error("Please add at least one item with a valid quantity.");
                    return $this->redirect($this->referer());
                }

                foreach ($data['job_challan_items'] as &$item) {

                    $qty = isset($item['quantity']) ? (float)$item['quantity'] : 0;
                    $item_id = $item['item_id'];
                    $return_type = isset($item['return_type']) ? $item['return_type'] : 'Raw Material';

                    $itemNameData = $this->Additem->find()->where(['id' => $item_id])->first();
                    $itemName = $itemNameData ? $itemNameData->item_name : 'Unknown';

                    // ❌ MUST BE RAW MATERIAL
                    if ($itemNameData && $itemNameData->itemtype !== 'RawMaterial') {
                        $this->Flash->error("Item '{$itemName}' is not a Raw Material. Only Raw Materials can be added in the items table.");
                        return $this->redirect($this->referer());
                    }

                    // ❌ STOCK VALIDATION (skip for Finished Goods)
                    if ($return_type !== 'Finished Goods') {
                        $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
                        $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
                        $currentStock = $grnStock['sum'] - $indentStock['sum'];

                        if ($qty > $currentStock) {
                            $this->Flash->error("Insufficient stock for item: {$itemName}. Required: {$qty}, Available: {$currentStock}");
                            return $this->redirect($this->referer());
                        }
                    }

                    $rate = isset($item['rate']) ? (float)$item['rate'] : 0;
                    $taxRate = isset($item['tax_rate']) ? (float)$item['tax_rate'] : 0;

                    // Basic Amount
                    $item['amount'] = $qty * $rate;

                    // GST Amount
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
                unset($item);
            }

            // ✅ SEMI-FINISHED PRODUCT VALIDATION (In Progress Only)
            if ($processType === 'In Progress') {
                if (empty($data['semi_finished_item_id']) || empty($data['semi_finished_quantity'])) {
                    $this->Flash->error("Please select a Semi-Finished Product and enter its quantity.");
                    return $this->redirect($this->referer());
                }

                $sf_item_id = $data['semi_finished_item_id'];
                $sf_qty = (float)$data['semi_finished_quantity'];

                $sfItemData = $this->Additem->find()->where(['id' => $sf_item_id])->first();
                $sfItemName = $sfItemData ? $sfItemData->item_name : 'Unknown';

                if ($sfItemData && $sfItemData->itemtype !== 'Semi-Finished Product') {
                    $this->Flash->error("Item '{$sfItemName}' is not configured as a Semi-Finished Product.");
                    return $this->redirect($this->referer());
                }

                // Check stock for the semi-finished product
                $grnStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $sf_item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
                $indentStock = $this->Stockregister->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $sf_item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
                $currentStock = $grnStock['sum'] - $indentStock['sum'];

                if ($sf_qty > $currentStock) {
                    $this->Flash->error("Insufficient stock for Semi-Finished Product: {$sfItemName}. Required: {$sf_qty}, Available: {$currentStock}");
                    return $this->redirect($this->referer());
                }

                // Append the Semi-Finished product to the items array so it gets saved
                $data['job_challan_items'][] = [
                    'item_id' => $sf_item_id,
                    'item_name' => $sfItemName,
                    'quantity' => $sf_qty,
                    'return_type' => 'Semi-Finished Product',
                    'amount' => 0,
                    'tax_amount' => 0,
                    'total' => 0,
                    'rate' => 0
                ];
            }

            // ✅ MASTER TOTAL
            $data['total_amount'] = $total;
            $data['gst_amount'] = $total_tax;
            $data['final_amount'] = $total + $total_tax;

            $entity = $this->JobChallans->patchEntity($entity, $data, [
                'associated' => ['JobChallanItems']
            ]);

            $result = $this->JobChallans->save($entity);
            if ($result) {

                // ✅ STOCK REGISTER — for both Manufacturing and In Progress
                if (!empty($result['job_challan_items'])) {
                    foreach ($result['job_challan_items'] as $index => $jc_item) {
                        $newsr = $this->Stockregister->newEntity();
                        $newsrentity = [];
                        $newsrentity['indent_id'] = $result['id'];
                        $newsrentity['contract_id'] = 0;
                        $newsrentity['item_id'] = $jc_item['item_id'];
                        $newsrentity['quantity'] = $jc_item['quantity'];
                        $newsrentity['finishedproduct_id'] = 0;
                        $newsrentity['issue_date'] = date('Y-m-d', strtotime($result['jc_date']));
                        $newsrentity['store_type'] = '2';
                        $newsrentity['sub_contractors_id'] = $result['sub_contractors_id'];
                        
                        $podetail = $this->Stockregister->patchEntity($newsr, $newsrentity);
                        $podetail->set($newsrentity); // Force set fields to bypass mass-assignment protection
                        $this->Stockregister->save($podetail);
                    }
                }

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
            'contain' => ['JobChallanItems' => ['Additem'], 'SubContractors']
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
            $this->Flash->error('This JC cannot be deleted because it has already been used in JC Receive.');
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
        $stsearch = isset($this->request->data['fetch']) ? $this->request->data['fetch'] : '';
        $challan_id = isset($this->request->data['challan_id']) ? $this->request->data['challan_id'] : null;

        if ($challan_id) {
            $this->loadModel('JobChallanItems');
            $this->loadModel('JobChallanReceives');
            
            $items = $this->JobChallanItems->find()
                ->where(['JobChallanItems.challan_id' => $challan_id])
                ->contain(['Additem'])
                ->toArray();
                
            foreach ($items as $item) {
                if (empty($item->additem)) continue;
                if ($stsearch !== '' && stripos($item->additem->item_name, $stsearch) === false) continue;
                
                $totalReceived = $this->JobChallanReceives->find()
                    ->where([
                        'challan_id' => $challan_id,
                        'item_id' => $item->item_id
                    ])
                    ->sumOf('received_qty');
                    
                $pending = max(0, $item->quantity - $totalReceived);
                
                if ($pending > 0) {
                    echo '<li data-id="' . $item->item_id . '" data-jc-qty="' . $item->quantity . '" data-pending-qty="' . $pending . '">' . $item->additem->item_name . ' (Pending: ' . $pending . ')</li>';
                }
            }
        } else {
            $process_type = isset($this->request->data['process_type']) ? $this->request->data['process_type'] : '';

            $conditions = [
                'Additem.item_name LIKE' => '%' . $stsearch . '%',
                'Additem.status' => 'Y'
            ];

            if ($process_type === 'Manufacturing') {
                $conditions['Additem.itemtype'] = 'RawMaterial';
            } elseif ($process_type === 'Semi-Finished') {
                $conditions['Additem.itemtype'] = 'Semi-Finished Product';
            }

            $searchst = $this->Additem->find('all')->where($conditions)->toarray();
            foreach ($searchst as $value) {
                echo '<li data-id="' . $value['id'] . '">' . $value['item_name'] . '</li>';
            }
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

        $currentStock = $grnStock['sum'] - $indentStock['sum'];
        
        $addItem = TableRegistry::get('Additem')->find('all')->where(['id' => $id])->first();
        
        $tax_rate = '';
        if ($addItem && !empty($addItem->tax)) {
            $taxMaster = TableRegistry::get('Taxmaster')->find('all')->where(['id' => $addItem->tax])->first();
            if ($taxMaster) {
                $tax_rate = $taxMaster->tax;
            }
        }
        
        $hsn_code = $addItem ? $addItem->item_isbn : '';

        echo json_encode([
            'inhand_qty' => $currentStock,
            'tax_id' => $tax_rate,
            'hsn_code' => $hsn_code
        ]);
    }

    // for export pdf
    public function viewpdf($job_id)
    {
        $currentDb = $this->request->session()->read('Auth.User.db');
        $senderDb = $this->request->query('sender_db');
        
        $sourceDb = $currentDb;
        if (!empty($senderDb)) {
            $sourceDb = $senderDb;
            $conn = $this->ensureConnection($senderDb);
            $this->JobChallans->connection($conn);
            $this->JobChallanItems->connection($conn);
            $this->JobChallans->SubContractors->connection($conn);
        } else {
            // Check if we need to search sister companies dynamically (fallback)
            try {
                $jc_data = $this->JobChallans->find('all')->contain(['SubContractors', 'JobChallanItems'])->where(['JobChallans.id' => $job_id])->first();
            } catch (\Exception $e) {
                $jc_data = null;
            }
            if (!$jc_data) {
                $sisterCompanies = $this->getSenderDatabases();
                foreach ($sisterCompanies as $sister) {
                    $otherConn = $this->ensureConnection($sister['database_name']);
                    $check = $otherConn->execute("SELECT id FROM job_challans WHERE id = :id LIMIT 1", ['id' => $job_id])->fetch('assoc');
                    if ($check) {
                        $sourceDb = $sister['database_name'];
                        $this->JobChallans->connection($otherConn);
                        $this->JobChallanItems->connection($otherConn);
                        $this->JobChallans->SubContractors->connection($otherConn);
                        break;
                    }
                }
            }
        }

        $jc_data = $this->JobChallans->find('all')
            ->contain(['SubContractors', 'JobChallanItems'])
            ->where(['JobChallans.id' => $job_id])
            ->first();
        
        if (!$jc_data) {
            $this->Flash->error("Job Challan not found.");
            return $this->redirect($this->referer());
        }

        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        
        // Ensure site settings are loaded from the source database
        $sourceConn = $this->ensureConnection($sourceDb);
        $this->Sitesettings->connection($sourceConn);
        $this->SitesettingsDetails->connection($sourceConn);

        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();

        $this->set(compact('jc_data', 'sitesetting', 'site_details'));
        if ($sourceDb !== 'tirupati_tppl') {
            $this->render('viewsubcontractorpdf');
        }
    }

    public function viewreturnpdf($id)
    {
        $currentDb = $this->request->session()->read('Auth.User.db');
        
        $this->loadModel('JobChallanReceives');
        $this->loadModel('JobChallans');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');

        $return_data = null;
        $sourceDb = $currentDb;

        // Try to find in current database first
        try {
            $return_data = $this->JobChallanReceives->find('all')
                ->where(['JobChallanReceives.id' => $id])
                ->contain(['Additem'])
                ->first();
        } catch (\Exception $e) {
            // Ignore, try sister databases
        }

        if (!$return_data) {
            // Check sister company databases
            $sisterCompanies = $this->getSenderDatabases();
            foreach ($sisterCompanies as $sister) {
                $otherConn = $this->ensureConnection($sister['database_name']);
                $check = $otherConn->execute("SELECT id FROM job_challan_receives WHERE id = :id LIMIT 1", ['id' => $id])->fetch('assoc');
                if ($check) {
                    $sourceDb = $sister['database_name'];
                    $this->JobChallanReceives->connection($otherConn);
                    $this->JobChallanReceives->Additem->connection($otherConn);
                    $this->JobChallans->connection($otherConn);
                    $this->JobChallans->SubContractors->connection($otherConn);
                    $return_data = $this->JobChallanReceives->find('all')
                        ->where(['JobChallanReceives.id' => $id])
                        ->contain(['Additem'])
                        ->first();
                    break;
                }
            }
        }

        if (!$return_data) {
            $this->Flash->error("Return transaction not found.");
            return $this->redirect($this->referer());
        }

        // Get the associated Job Challan in the determined source database
        $jc_data = $this->JobChallans->find('all')
            ->contain(['SubContractors'])
            ->where(['JobChallans.id' => $return_data->challan_id])
            ->first();

        if (!$jc_data) {
            $this->Flash->error("Original Job Challan not found.");
            return $this->redirect($this->referer());
        }

        // Do not allow return challan generation for Cancelled or Deleted JCs
        if ($jc_data->status === 'Cancelled' || $jc_data->status === 'Deleted') {
            $this->Flash->error("Cannot generate Return Challan for a cancelled or deleted Job Challan.");
            return $this->redirect($this->referer());
        }

        // Authorization check: User must belong to either the issuer company or the receiver subcontractor
        $subcontractor_db = $jc_data['sub_contractor']['database_name'] ?? '';
        if ($currentDb !== 'tirupati_tppl' && $currentDb !== $subcontractor_db) {
            $this->Flash->error("Unauthorized access: You are not authorized to view this Return Challan.");
            return $this->redirect($this->referer());
        }

        // Load site settings (company details) of the source database
        $sourceConn = $this->ensureConnection($sourceDb);
        $this->Sitesettings->connection($sourceConn);
        $this->SitesettingsDetails->connection($sourceConn);
        
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();

        $this->set(compact('return_data', 'jc_data', 'sitesetting', 'site_details'));
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

                // 🔥 TOTAL RECEIVED
                $totalReceived = $this->JobChallanReceives->find()
                    ->where([
                        'item_id' => $item_id,
                        'challan_id' => $challan_id
                    ])
                    ->sumOf('received_qty');

                // ❌ VALIDATION
                if (($totalReceived + $currentReceive) > $dispatch_qty) {
                    $pending = max(0, $dispatch_qty - $totalReceived);
                    $itemNameData = $this->Additem->find()->where(['id' => $item_id])->first();
                    $itemName = $itemNameData ? $itemNameData->item_name : 'Unknown';
                    $this->Flash->error("Receive quantity cannot be greater than pending quantity for item: {$itemName}. Pending: {$pending}");
                    return $this->redirect($this->referer());
                }

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
                    'vehicle_no'   => $item['vehicle_no'] ?? '',
                    'rate'         => !empty($item['rate']) ? (float)$item['rate'] : null,
                    'tax_rate'     => !empty($item['tax_rate']) ? (float)$item['tax_rate'] : null,
                    'tax_amount'   => !empty($item['tax_amount']) ? (float)$item['tax_amount'] : null,
                    'amount'       => !empty($item['amount']) ? (float)$item['amount'] : null,
                    'sub_contractors_id' => $jc_data['sub_contractors_id']
                ];

                $entity = $this->JobChallanReceives->newEntity($saveData);
                
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
                $status = 'Pending';
            } elseif ($totalReceivedAll < $totalDispatch) {
                $status = 'Partially Returned';
            } else {
                $status = 'Completed';
            }

            $jobChallan->status = $status;
            $this->Jobchallans->save($jobChallan);




            $this->Flash->success('All items received successfully');
            return $this->redirect(['action' => 'index']);
        }




        $this->set(compact('job', 'challan_id'));
    }

    public function jcinfo($id)
    {
        $this->viewBuilder()->layout('admin');
        $currentDb = $this->request->session()->read('Auth.User.db');
        
        // Try to find the JC in the current database first
        $sourceDb = $currentDb;
        try {
            $jobChallan = $this->JobChallans->get($id, ['contain' => ['SubContractors']]);
        } catch (\Exception $e) {
            // JC not in current DB — search sister company databases
            $sisterCompanies = $this->getSenderDatabases();
            $found = false;
            foreach ($sisterCompanies as $sister) {
                $otherConn = $this->ensureConnection($sister['database_name']);
                $check = $otherConn->execute("SELECT id FROM job_challans WHERE id = :id LIMIT 1", ['id' => $id])->fetch('assoc');
                if ($check) {
                    $sourceDb = $sister['database_name'];
                    $this->JobChallans->connection($otherConn);
                    $this->JobChallans->SubContractors->connection($otherConn);
                    $this->JobChallanItems->connection($otherConn);
                    $this->JobChallanReceives->connection($otherConn);
                    $this->JobChallanReceives->Additem->connection($otherConn);
                    $jobChallan = $this->JobChallans->get($id, ['contain' => ['SubContractors']]);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->Flash->error("Job Challan not found.");
                return $this->redirect($this->referer());
            }
        }

        $subcontractorName = $jobChallan->sub_contractor->name;

        $dispatch_item_details = $this->JobChallanItems->find()
            ->select([
                'item_name',
                'item_id',
                'total_qty' => $this->JobChallanItems->find()->func()->sum('quantity')
            ])
            ->where(['challan_id' => $id])
            ->group(['item_name', 'item_id'])
            ->toArray();

        $history = $this->JobChallanReceives->find()
            ->where([
                'JobChallanReceives.challan_id' => $id
            ])->contain(['Additem'])
            ->order(['JobChallanReceives.receive_date' => 'ASC'])
            ->toArray();

        // Determine the target (receiver) database dynamically from sub_contractors.database_name
        $tracking = [];
        $returnedItems = [];
        $targetDb = '';

        // Look up the subcontractor's database_name in the SOURCE database
        $sourceConn = $this->ensureConnection($sourceDb);
        $subRow = $sourceConn->execute(
            "SELECT database_name FROM sub_contractors WHERE id = :id LIMIT 1",
            ['id' => $jobChallan->sub_contractors_id]
        )->fetch('assoc');
        if ($subRow && !empty($subRow['database_name'])) {
            $targetDb = $subRow['database_name'];
        }

        if ($targetDb) {
            $targetConn = $this->ensureConnection($targetDb);

            foreach ($dispatch_item_details as $dispatch) {
                $targetItem = $targetConn->execute("SELECT id FROM st_additem WHERE item_name = :name LIMIT 1", ['name' => $dispatch['item_name']])->fetch('assoc');
                
                $consumed = 0;
                $balance = 0;
                if ($targetItem) {
                    $targetItemId = $targetItem['id'];
                    $consumedRow = $targetConn->execute("SELECT ROUND(SUM(quantity), 2) as total FROM st_stock_register WHERE item_id = :iid AND store_type IN ('2', '4')", ['iid' => $targetItemId])->fetch('assoc');
                    $consumed = $consumedRow['total'] ? $consumedRow['total'] : 0;
                    
                    $grnStock = $targetConn->execute("SELECT ROUND(SUM(quantity), 2) as total FROM st_stock_register WHERE item_id = :iid AND store_type IN ('0', '1', '3')", ['iid' => $targetItemId])->fetch('assoc');
                    $indentStock = $targetConn->execute("SELECT ROUND(SUM(quantity), 2) as total FROM st_stock_register WHERE item_id = :iid AND store_type IN ('2', '4')", ['iid' => $targetItemId])->fetch('assoc');
                    $balance = ($grnStock['total'] ?: 0) - ($indentStock['total'] ?: 0);
                }

                $tracking[$dispatch['item_name']] = [
                    'consumed' => $consumed,
                    'balance' => $balance
                ];
            }
        }
        $returnedItems = [];
        foreach ($history as $h) {
            if ($h->additem) {
                $name = $h->additem->item_name;
                if (!isset($returnedItems[$name])) {
                    $returnedItems[$name] = [
                        'type' => 'Processed/Returned',
                        'output' => '-',
                        'quantity' => 0
                    ];
                }
                $returnedItems[$name]['quantity'] += (float)$h->received_qty;
            }
        }

        $this->set(compact('jobChallan', 'dispatch_item_details', 'history', 'tracking', 'returnedItems'));
    }

    public function receiveAdd($preselect_id = null)
    {
        $this->viewBuilder()->layout('admin');
        $currentDb = $this->request->session()->read('Auth.User.db');
        
        $eligibleJCs = [];
        $sisterCompanies = $this->getSenderDatabases();
        
        foreach ($sisterCompanies as $sister) {
            $senderConn = $this->ensureConnection($sister['database_name']);
            
            // Find my company's subcontractor ID in the sender's database
            $sub = $senderConn->execute(
                "SELECT id FROM sub_contractors WHERE database_name = :myDb LIMIT 1",
                ['myDb' => $currentDb]
            )->fetch('assoc');
            
            if (!$sub) continue;
            $subId = $sub['id'];
            
            $jcs = $senderConn->execute("
                SELECT jc.id, jc.challan_no, jc.jc_date, SUM(jci.quantity) as total_qty
                FROM job_challans jc
                JOIN job_challan_items jci ON jc.id = jci.challan_id
                WHERE jc.sub_contractors_id = :sub_id AND jc.status NOT IN ('Completed', 'Cancelled', 'Deleted')
                GROUP BY jc.id
                ORDER BY jc.id DESC
            ", ['sub_id' => $subId])->fetchAll('assoc');
            
            foreach ($jcs as $jc) {
                $received = $senderConn->execute("SELECT SUM(received_qty) as total_received FROM job_challan_receives WHERE challan_id = :cid", ['cid' => $jc['id']])->fetch('assoc');
                $totalReceived = $received['total_received'] ? $received['total_received'] : 0;
                
                if ($jc['total_qty'] > $totalReceived) {
                    $eligibleJCs[$jc['id'] . '|' . $sister['database_name']] = $jc['challan_no'] . ' (' . $sister['name'] . ')';
                }
            }
        }
        
        $this->set(compact('eligibleJCs', 'preselect_id'));

        // Fetch products from LOCAL DB
        $this->loadModel('Additem');
        $productsList = $this->Additem->find('list', [
            'keyField' => 'id',
            'valueField' => 'item_name'
        ])->where(['status' => 'Y'])->order(['item_name' => 'ASC'])->toArray();

        if ($this->request->is('post')) {
            $data = $this->request->data;
            $combinedKey = $data['challan_id'];
            
            $parts = explode('|', $combinedKey);
            $challan_id = $parts[0] ?? null;
            $senderDb = $parts[1] ?? null;
            
            if (empty($challan_id) || empty($senderDb)) {
                $this->Flash->error("Invalid JC selected.");
                return $this->redirect($this->referer());
            }

            if (empty($data['items']) || !is_array($data['items'])) {
                $this->Flash->error("No items submitted to receive.");
                return $this->redirect($this->referer());
            }

            // Connect to sender DB and validate ownership
            $senderConn = $this->ensureConnection($senderDb);
            
            // Find my sub ID in sender's DB
            $sub = $senderConn->execute(
                "SELECT id FROM sub_contractors WHERE database_name = :myDb LIMIT 1",
                ['myDb' => $currentDb]
            )->fetch('assoc');
            
            if (!$sub) {
                $this->Flash->error("Unauthorized.");
                return $this->redirect($this->referer());
            }

            $jc = $senderConn->execute("SELECT * FROM job_challans WHERE id = :id AND sub_contractors_id = :sub_id", ['id' => $challan_id, 'sub_id' => $sub['id']])->fetch('assoc');

            if (!$jc) {
                $this->Flash->error("Unauthorized or Invalid Job Challan.");
                return $this->redirect($this->referer());
            }

            // --- Database Transaction Start ---
            $conn = ConnectionManager::get('default');
            $conn->begin();
            $senderConn->begin();

            try {
                $receivedSomething = false;
                $receive_date = date('Y-m-d');

                foreach ($data['items'] as $itemData) {
                    $item_id = $itemData['item_id'] ?? null;
                    $receive_qty = isset($itemData['receive_qty']) ? (float)$itemData['receive_qty'] : 0;
                    
                    if (empty($item_id) || $receive_qty <= 0) {
                        continue;
                    }

                    $jcItems = $senderConn->execute("SELECT i.*, a.item_name FROM job_challan_items i LEFT JOIN st_additem a ON i.item_id = a.id WHERE challan_id = :id AND i.item_id = :iid", ['id' => $challan_id, 'iid' => $item_id])->fetchAll('assoc');
                    if (!$jcItems) {
                        throw new \Exception("Job Challan item (ID: {$item_id}) not found.");
                    }

                    $jcItem = $jcItems[0]; 

                    $received = $senderConn->execute("SELECT SUM(received_qty) as total_received FROM job_challan_receives WHERE challan_id = :cid AND item_id = :iid", [
                        'cid' => $challan_id,
                        'iid' => $jcItem['item_id']
                    ])->fetch('assoc');
                    $totalReceived = $received['total_received'] ? $received['total_received'] : 0;
                    $pendingQty = max(0, $jcItem['quantity'] - $totalReceived);

                    if ($receive_qty > $pendingQty) {
                        throw new \Exception("Receive Qty cannot be greater than Pending Qty for {$jcItem['item_name']} (Pending: {$pendingQty}).");
                    }

                    // 1. Insert into job_challan_receives in sender DB
                    $senderConn->insert('job_challan_receives', [
                        'challan_id' => $challan_id,
                        'item_id' => $jcItem['item_id'],
                        'dispatch_qty' => $jcItem['quantity'],
                        'received_qty' => $receive_qty,
                        'receive_date' => $receive_date,
                        'vehicle_no' => $data['vehicle_no'] ?? '',
                        'sub_contractors_id' => $jc['sub_contractors_id'],
                        'created' => date('Y-m-d H:i:s')
                    ]);

                    // 2. Map local item by name
                    $localItem = $this->Additem->find()->where(['item_name' => $jcItem['item_name']])->first();
                    if (!$localItem) {
                        throw new \Exception("Item '{$jcItem['item_name']}' does not exist in local Item Master. Please create it first.");
                    }
                    $localItemId = $localItem->id;

                    // 3. Receive stock locally (store_type = 1)
                    $this->loadModel('Stockregister');
                    $newsr = $this->Stockregister->newEntity();
                    $newsrentity = [
                        'indent_id' => $challan_id,
                        'contract_id' => 0,
                        'item_id' => $localItemId,
                        'quantity' => $receive_qty,
                        'finishedproduct_id' => 0,
                        'issue_date' => $receive_date,
                        'store_type' => '1',
                        'sub_contractors_id' => $jc['sub_contractors_id']
                    ];
                    $this->Stockregister->save($this->Stockregister->patchEntity($newsr, $newsrentity));
                    
                    $receivedSomething = true;
                }

                if (!$receivedSomething) {
                    throw new \Exception("Please enter a receive quantity for at least one item.");
                }

                // 4. Update JC Status in sender DB
                $totalDispatchAll = $senderConn->execute("SELECT SUM(quantity) as total FROM job_challan_items WHERE challan_id = :cid", ['cid' => $challan_id])->fetch('assoc')['total'];
                $totalReceivedAll = $senderConn->execute("SELECT SUM(received_qty) as total FROM job_challan_receives WHERE challan_id = :cid", ['cid' => $challan_id])->fetch('assoc')['total'];
                
                if ($totalReceivedAll == 0) {
                    $status = 'Pending';
                } elseif ($totalReceivedAll < $totalDispatchAll) {
                    $status = 'Partially Returned';
                } else {
                    $status = 'Completed';
                }
                
                $senderConn->update('job_challans', ['status' => $status], ['id' => $challan_id]);

                $conn->commit();
                $senderConn->commit();

                $this->Flash->success('JC Received and Processed Successfully.');
                return $this->redirect(['action' => 'receiveIndex']);

            } catch (\Exception $e) {
                $conn->rollback();
                $senderConn->rollback();
                $this->Flash->error("Error processing JC: " . $e->getMessage());
                return $this->redirect($this->referer());
            }
        }
    }

    public function getJcDetails()
    {
        $this->autoRender = false;
        $this->response->type('json');

        $combinedKey = $this->request->query('challan_id');
        if (empty($combinedKey)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            return;
        }

        // Parse challan_id|sender_db
        $parts = explode('|', $combinedKey);
        $challan_id = $parts[0] ?? null;
        $senderDb = $parts[1] ?? null;

        if (empty($challan_id) || empty($senderDb)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            return;
        }

        $currentDb = $this->request->session()->read('Auth.User.db');
        $senderConn = $this->ensureConnection($senderDb);
        
        // Find my sub ID in sender's DB using database_name
        $sub = $senderConn->execute(
            "SELECT id FROM sub_contractors WHERE database_name = :myDb LIMIT 1",
            ['myDb' => $currentDb]
        )->fetch('assoc');

        if ($sub) {
            $subId = $sub['id'];

            $jc = $senderConn->execute("SELECT * FROM job_challans WHERE id = :id AND sub_contractors_id = :sub_id", ['id' => $challan_id, 'sub_id' => $subId])->fetch('assoc');
            
            if ($jc) {
                $jcItems = $senderConn->execute("SELECT i.*, a.item_name, c.category_name FROM job_challan_items i LEFT JOIN st_additem a ON i.item_id = a.id LEFT JOIN st_categorymaster c ON a.category_id = c.id WHERE challan_id = :id", ['id' => $challan_id])->fetchAll('assoc');
                
                $itemsData = [];
                foreach ($jcItems as $jcItem) {
                    $received = $senderConn->execute("SELECT SUM(received_qty) as total_received FROM job_challan_receives WHERE challan_id = :cid AND item_id = :iid", ['cid' => $challan_id, 'iid' => $jcItem['item_id']])->fetch('assoc');
                    $totalReceived = $received['total_received'] ? $received['total_received'] : 0;
                    $pendingQty = max(0, $jcItem['quantity'] - $totalReceived);
                    
                    if ($pendingQty > 0) {
                        $itemsData[] = [
                            'item_id' => $jcItem['item_id'],
                            'item_name' => $jcItem['item_name'],
                            'category' => $jcItem['category_name'] ?? 'N/A',
                            'original_qty' => $jcItem['quantity'],
                            'received_qty' => $totalReceived,
                            'pending_qty' => $pendingQty,
                            'unit' => 'KG'
                        ];
                    }
                }

                // Get sender company name dynamically
                $senderCompanyRow = $senderConn->execute("SELECT cname FROM st_companymaster WHERE main_branch = 'Y' LIMIT 1")->fetch('assoc');
                $senderCompanyName = $senderCompanyRow ? trim($senderCompanyRow['cname']) : 'Unknown';

                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'challan_no' => $jc['challan_no'],
                        'jc_date' => date('d-m-Y', strtotime($jc['jc_date'])),
                        'from_company' => $senderCompanyName,
                        'items' => $itemsData
                    ]
                ]);
                return;
            }
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized or Not Found']);
    }

    public function receiveIndex()
    {
        $this->viewBuilder()->layout('admin');
        $currentDb = $this->request->session()->read('Auth.User.db');
        
        $receives = [];
        $sisterCompanies = $this->getSenderDatabases();
        
        foreach ($sisterCompanies as $sister) {
            $senderConn = $this->ensureConnection($sister['database_name']);
            
            // Find my company's subcontractor ID in the sender's database
            $sub = $senderConn->execute(
                "SELECT id FROM sub_contractors WHERE database_name = :myDb LIMIT 1",
                ['myDb' => $currentDb]
            )->fetch('assoc');
            
            if (!$sub) continue;
            
            $rows = $senderConn->execute("
                SELECT 
                    j.id as challan_id, 
                    j.challan_no, 
                    j.jc_date,
                    j.vehicle_no,
                    i.item_id, 
                    a.item_name, 
                    i.quantity as dispatch_qty,
                    COALESCE((SELECT SUM(received_qty) FROM job_challan_receives r WHERE r.challan_id = j.id AND r.item_id = i.item_id), 0) as total_received
                FROM job_challans j
                JOIN job_challan_items i ON j.id = i.challan_id
                LEFT JOIN st_additem a ON i.item_id = a.id
                WHERE j.sub_contractors_id = :sub_id
                AND j.status != 'Cancelled'
                ORDER BY j.id DESC
            ", ['sub_id' => $sub['id']])->fetchAll('assoc');
            
            // Tag each row with the sender info
            foreach ($rows as &$row) {
                $row['sender_db'] = $sister['database_name'];
                $row['sender_name'] = $sister['name'];
            }
            unset($row);
            
            $receives = array_merge($receives, $rows);
        }
        
        $this->set('receives', $receives);
    }

}
