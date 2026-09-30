<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;

class GatepassesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->viewBuilder()->layout('admin');
    }

    public function index()
    {
        $query = $this->Gatepasses->find()->contain(['SubContractors']);

        $data = $this->request->query;
        if (!empty($data['sub_contractor_id'])) {
            $query->where(['Gatepasses.sub_contractor_id' => $data['sub_contractor_id']]);
        }
        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $query->where([
                'Gatepasses.date >=' => $data['from_date'],
                'Gatepasses.date <=' => $data['to_date']
            ]);
        }

        $limit = 20;
        if (!empty($data['limit']) && is_numeric($data['limit'])) {
            $limit = (int)$data['limit'];
        }

        $this->paginate = [
            'order' => ['Gatepasses.id' => 'DESC'],
            'limit' => $limit
        ];
        $gatepasses = $this->paginate($query);

        $subContractors = $this->Gatepasses->SubContractors->find('list', ['limit' => 200, 'conditions' => ['status' => 'Active']]);

        $this->set(compact('gatepasses', 'subContractors'));
        
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->layout(false);
            $this->render('ajax_index');
        }
    }

    public function add()
    {
        $gatepass = $this->Gatepasses->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (empty($data['date'])) {
                $data['date'] = date('Y-m-d');
            } else {
                $data['date'] = date('Y-m-d', strtotime($data['date']));
            }
            if (!empty($data['return_date'])) {
                $data['return_date'] = date('Y-m-d', strtotime($data['return_date']));
            }
            
            // Format gatepass items
            if (!empty($data['items'])) {
                $items = [];
                foreach ($data['items'] as $item) {
                    if (!empty($item['item_id']) && !empty($item['quantity'])) {
                        if (!empty($item['jc_no'])) {
                            $item['description'] = 'JC No: ' . $item['jc_no'] . (!empty($item['description']) ? "\n" . $item['description'] : '');
                        }
                        $items[] = $item;
                    }
                }
                $data['gatepass_items'] = $items;
            }

            if (!empty($data['jc_id'])) {
                if (is_array($data['jc_id'])) {
                    $exists = $this->Gatepasses->find()->where(['jc_id IN' => $data['jc_id']])->first();
                } else {
                    $exists = $this->Gatepasses->find()->where(['jc_id' => $data['jc_id']])->first();
                }
                
                if ($exists) {
                    $this->Flash->error(__('A Gate Pass has already been created for one of these Job Challans.'));
                    return $this->redirect(['action' => 'add']);
                }

                // Convert array to string for database saving if it's multiple
                if (is_array($data['jc_id'])) {
                    $data['jc_id'] = implode(',', $data['jc_id']);
                }
            }

            $gatepass = $this->Gatepasses->patchEntity($gatepass, $data, [
                'associated' => ['GatepassItems']
            ]);

            if ($this->Gatepasses->save($gatepass)) {
                // Generate Gatepass No if empty
                if (empty($gatepass->gatepass_no)) {
                    $gatepass->gatepass_no = 'GP-' . str_pad($gatepass->id, 4, '0', STR_PAD_LEFT);
                    $this->Gatepasses->save($gatepass);
                }
                
                $this->Flash->success(__('The gate pass has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate pass could not be saved. Please, try again.'));
        }

        $subContractors = $this->Gatepasses->SubContractors->find('list', ['limit' => 200, 'conditions' => ['status' => 'Active']]);
        
        $existingJcs = $this->Gatepasses->find()->select(['jc_id'])->where(['jc_id IS NOT' => null])->extract('jc_id')->toArray();
        $jcConditions = ['status !=' => 'Deleted'];
        if (!empty($existingJcs)) {
            $jcConditions['id NOT IN'] = $existingJcs;
        }

        $this->loadModel('JobChallans');
        $jcs = $this->JobChallans->find('list', [
            'keyField' => 'id',
            'valueField' => 'challan_no',
            'conditions' => $jcConditions
        ])->order(['id' => 'DESC']);

        $this->loadModel('Additem');
        $items = $this->Additem->find('list', [
            'keyField' => 'id',
            'valueField' => 'item_name',
            'conditions' => ['status' => 'Y']
        ]);

        $this->set(compact('gatepass', 'subContractors', 'jcs', 'items'));
    }

    public function edit($id = null)
    {
        $gatepass = $this->Gatepasses->get($id, [
            'contain' => ['GatepassItems' => ['Additem']]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->data;
            if (!empty($data['date'])) {
                $data['date'] = date('Y-m-d', strtotime($data['date']));
            }
            if (!empty($data['return_date'])) {
                $data['return_date'] = date('Y-m-d', strtotime($data['return_date']));
            }

            // To replace items properly, we should delete existing ones and add new, or rely on primary keys.
            // Using a simple workaround: delete old ones if new are provided.
            if (isset($data['items'])) {
                $this->Gatepasses->GatepassItems->deleteAll(['gatepass_id' => $gatepass->id]);
                $items = [];
                foreach ($data['items'] as $item) {
                    if (!empty($item['item_id']) && !empty($item['quantity'])) {
                        $items[] = $item;
                    }
                }
                $data['gatepass_items'] = $items;
            }

            $gatepass = $this->Gatepasses->patchEntity($gatepass, $data, [
                'associated' => ['GatepassItems']
            ]);

            if ($this->Gatepasses->save($gatepass)) {
                $this->Flash->success(__('The gate pass has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate pass could not be updated. Please, try again.'));
        }
        $subContractors = $this->Gatepasses->SubContractors->find('list', ['limit' => 200, 'conditions' => ['status' => 'Active']]);
        
        $existingJcs = $this->Gatepasses->find()->select(['jc_id'])->where(['jc_id IS NOT' => null, 'id !=' => $id])->extract('jc_id')->toArray();
        $jcConditions = ['status !=' => 'Deleted'];
        if (!empty($existingJcs)) {
            $jcConditions['id NOT IN'] = $existingJcs;
        }

        $this->loadModel('JobChallans');
        $jcs = $this->JobChallans->find('list', [
            'keyField' => 'id',
            'valueField' => 'challan_no',
            'conditions' => $jcConditions
        ])->order(['id' => 'DESC']);

        $this->loadModel('Additem');
        $items = $this->Additem->find('list', [
            'keyField' => 'id',
            'valueField' => 'item_name',
            'conditions' => ['status' => 'Y']
        ]);
        $this->set(compact('gatepass', 'subContractors', 'jcs', 'items'));
    }

    public function view($id = null)
    {
        $gatepass = $this->Gatepasses->get($id, [
            'contain' => ['SubContractors', 'JobChallans', 'GatepassItems' => ['Additem']]
        ]);

        $this->set('gatepass', $gatepass);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->Flash->error(__('Once a Gate Pass is created, it cannot be deleted to maintain data integrity. Please use the edit option.'));
        return $this->redirect(['action' => 'index']);
    }

    public function getJcData()
    {
        $this->autoRender = false;
        $jc_ids = $this->request->query('jc_id');
        
        if (empty($jc_ids)) {
            echo json_encode(['status' => 'error']);
            exit;
        }

        $this->loadModel('JobChallans');

        // Handle both single and array of IDs
        if (!is_array($jc_ids)) {
            $jc_ids = [$jc_ids];
        }

        $jcs = $this->JobChallans->find('all', [
            'contain' => ['JobChallanItems' => ['Additem']],
            'conditions' => ['JobChallans.id IN' => $jc_ids]
        ])->toArray();

        if (!empty($jcs)) {
            $items = [];
            $sub_contractor_id = null;
            $vehicle_no = null;
            
            foreach ($jcs as $jc) {
                // Use the first non-empty details
                if (!$sub_contractor_id) $sub_contractor_id = $jc->sub_contractors_id;
                if (!$vehicle_no) $vehicle_no = $jc->vehicle_no;

                foreach ($jc->job_challan_items as $jci) {
                    $items[] = [
                        'item_id' => $jci->item_id,
                        'item_name' => $jci->additem ? $jci->additem->item_name : '',
                        'quantity' => $jci->quantity,
                        'jc_no' => $jc->challan_no
                    ];
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'sub_contractor_id' => $sub_contractor_id,
                    'vehicle_no' => $vehicle_no,
                    'items' => $items
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

    public function gatepasspdf($id = null)
    {
        $gatepass = $this->Gatepasses->get($id, [
            'contain' => ['SubContractors', 'JobChallans', 'GatepassItems' => ['Additem']]
        ]);

        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');
        
        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();

        $this->set(compact('gatepass', 'sitesetting', 'site_details'));
    }
}
