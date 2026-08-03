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
use \DateTime;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class EmdController extends AppController
{

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');


        $req_data = $_GET;

        $bg_for = $req_data['bg_for'] ?? null;
        $status = $req_data['status'] ?? null;
        $bankguaranteeno = $req_data['bankguaranteeno'] ?? null;

        $datefrom = !empty($req_data['from_date']) ? date('Y-m-d', strtotime($req_data['from_date'])) : null;
        $dateto   = !empty($req_data['to_date']) ? date('Y-m-d', strtotime($req_data['to_date'])) : null;
        $due_from = !empty($req_data['due_from']) ? date('Y-m-d', strtotime($req_data['due_from'])) : null;
        $due_to  = !empty($req_data['due_to']) ? date('Y-m-d', strtotime($req_data['due_to'])) : null;

        // pr($dateto);
        // pr($datefrom);
        // exit;
        $cond = [];

        if (!empty($bg_for)) {
            $cond[] = ['EmdGuarantees.bg_for' => $bg_for];
        }
        if (!empty($bankguaranteeno)) {
            $cond[] = ['EmdGuarantees.bankguaranteeno LIKE' => '%' . trim($bankguaranteeno) . '%'];
        }
        if (!empty($status)) {
            $cond[] = ['EmdGuarantees.status' => $status];
        }

        if (!empty($datefrom)) {
            $cond[] = ['DATE(EmdGuarantees.datefrom) >=' => $datefrom];
        }

        if (!empty($dateto)) {
            $cond[] = ['DATE(EmdGuarantees.datefrom) <=' => $dateto];
        }
        if (!empty($due_from) && !empty($due_to)) {
            $cond[] = [
                'OR' => [
                    [
                        'DATE(EmdGuarantees.validupto) >=' => $due_from,
                        'DATE(EmdGuarantees.validupto) <=' => $due_to
                    ],
                    [
                        'DATE(EmdGuarantees.claim_upto) >=' => $due_from,
                        'DATE(EmdGuarantees.claim_upto) <=' => $due_to
                    ],
                    [
                        'DATE(EmdGuarantees.extenstionupto) >=' => $due_from,
                        'DATE(EmdGuarantees.extenstionupto) <=' => $due_to
                    ]
                ]
            ];
        }


        $this->request->session()->write('apk', $cond);

        if (!empty($cond)) {
            $EmdGuarantees = $this->EmdGuarantees->find('all')->where([$cond])->order(['EmdGuarantees.id' => 'Desc']);
            $EmdGuarantees = $this->paginate($EmdGuarantees)->toarray();
        } else {
            $EmdGuarantees = $this->EmdGuarantees->find('all')->where(['EmdGuarantees.status' => 'N'])->order(['EmdGuarantees.id' => 'Desc']);
            $EmdGuarantees = $this->paginate($EmdGuarantees)->toarray();
        }

        $this->set('EmdGuarantees', $EmdGuarantees);
    }

    public function searchitem()
    {
        $this->loadModel('EmdGuarantees');
        $req_data = $_GET;
        $bg_for = $req_data['bg_for'];
        $status = $req_data['status'];
        $datefrom = date('Y-m-d', strtotime($req_data['from_date']));
        $dateto   = !empty($req_data['to_date'])   ? date('Y-m-d', strtotime($req_data['to_date']))   : null;
        $validupto = date('Y-m-d', strtotime($req_data['validupto']));
        $lastdate = date('Y-m-d', strtotime($req_data['lastdate']));

        $cond = [];
        if (!empty($bg_for)) {
            $contra = ['EmdGuarantees.bg_for' => $bg_for];
            $cond[] = $contra;
        }
        if (!empty($status)) {
            $contra = ['EmdGuarantees.status' => $status];
            $cond[] = $contra;
        }
        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(EmdGuarantees.datefrom) >=' => $datefrom];
            $cond[] = $contra;
        }
        if (!empty($dateto)) {
            $cond[] = ['DATE(EmdGuarantees.datefrom) <=' => $dateto];
        }

        if ($validupto !== '1970-01-01') {
            $contra = ['DATE(EmdGuarantees.validupto) <=' => $validupto];
            $cond[] = $contra;
        }

        if ($lastdate !== '1970-01-01') {
            $contra = ['DATE(EmdGuarantees.lastdate) <=' => $lastdate];
            $cond[] = $contra;
        }
        // pr($cond);exit;
        $this->request->session()->write('apk', $cond);

        $EmdGuarantees = $this->EmdGuarantees->find('all')->where([$cond])->order(['EmdGuarantees.id' => 'Desc']);
        $EmdGuarantees = $this->paginate($EmdGuarantees)->toarray();
        $this->set('EmdGuarantees', $EmdGuarantees);
    }

    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');

        $EmdGuarantees = $this->EmdGuarantees->find('all')->where(['EmdGuarantees.id' => $id])->first();


        $bankGuarantee = $this->EmdGuarantees->newEntity();

        if ($this->request->data) {
            $data = $this->request->data;

            $dateFields = ['datefrom', 'validupto', 'extenstionupto', 'claim_upto', 'lastdate', 'relese_date'];

            foreach ($dateFields as $field) {
                if (!empty($data[$field]) && $data[$field] != '1970-01-01') {
                    $data[$field] = date('Y-m-d', strtotime($data[$field]));
                } else {
                    $data[$field] = null;
                }
            }

            if ($this->request->data['invoice_file']['name']) {
                $imgname = $this->request->data['invoice_file']['name'];
                $item = $this->request->data['invoice_file']['tmp_name'];
                $ext = end(explode('.', $this->request->data['invoice_file']['name']));
                $imagename = rand() . '.' . $ext;
                $db = $this->request->session()->read('Auth.User.db');
                $dest = WWW_ROOT . 'images/' . $db . '_image/emd/' . $imagename;
                // pr($dest);exit;
                if (move_uploaded_file($item, $dest)) {
                    $data['invoice_file'] = $imagename;
                }
            }

            $bankGuarantee = $this->EmdGuarantees->patchEntity($bankGuarantee, $data);

            if ($this->EmdGuarantees->save($bankGuarantee)) {
                if (!empty($data['remark'])) {
                    $remark = $this->EmdRemarks->newEntity([
                        'bank_guarantee_id' => $bankGuarantee->id,
                        // 'remark' => $data['remark'],
                        'remark' => $this->removeEmojis($data['remark']),
                        'remarked_by' => $data['remark_by'],
                        'created' => $bankGuarantee->datefrom,
                    ]);
                    $this->EmdRemarks->save($remark);
                }

                $this->Flash->success(__('Bank Guarantee and Remark have been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('Unable to save bank guarantee.'));
        }

        $this->set(compact('bankGuarantee', 'EmdGuarantees'));
    }





    public function viewamount($id)
    {
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdAmount');

        $EmdGuarantees = $this->EmdGuarantees->find()
            ->where(['EmdGuarantees.id' => $id])
            ->first();

        $totalReceived = $this->EmdAmount->find()
            ->where(['bank_guarantee_id' => $EmdGuarantees['id']])
            ->sumOf('recive_amount');

        $remainingAmount = $EmdGuarantees['amount'] - $totalReceived;

        $this->set(compact('EmdGuarantees', 'remainingAmount'));

        if ($this->request->is('post')) {
            $remarkEntity = $this->EmdAmount->newEntity();
            $data = $this->request->data;
            $data['bank_guarantee_id'] = $id;
            $data['created'] = date('Y-m-d H:i:s');

            if ($this->request->data['invoice_file']['name']) {
                $imgname = $this->request->data['invoice_file']['name'];
                $item = $this->request->data['invoice_file']['tmp_name'];
                $ext = end(explode('.', $this->request->data['invoice_file']['name']));
                $imagename = rand() . '.' . $ext;
                $db = $this->request->session()->read('Auth.User.db');
                $dest = WWW_ROOT . 'images/' . $db . '_image/emd/' . $imagename;
                // pr($dest);exit;
                if (move_uploaded_file($item, $dest)) {
                    $data['invoice_file'] = $imagename;
                }
            }

            if ($data['recive_amount'] > $remainingAmount) {
                $this->Flash->error(__('Receive amount exceeds the remaining allowed amount.'));
                return $this->redirect($this->referer());
            }

            if (!empty($data['recive_date'])) {
                $data['recive_date'] = date('Y-m-d', DateTime::createFromFormat('d/m/Y', $data['recive_date'])->getTimestamp());
            }

            $data['total_amount'] = $EmdGuarantees['amount'];
            $remarkEntity = $this->EmdAmount->patchEntity($remarkEntity, $data);

            if ($this->EmdAmount->save($remarkEntity)) {
                $totalReceivedAfterSave = $this->EmdAmount->find()
                    ->where(['bank_guarantee_id' => $id])
                    ->sumOf('recive_amount');

                if ($totalReceivedAfterSave >= $EmdGuarantees['amount']) {
                    $EmdGuarantees->status = 'Y';
                    $this->EmdGuarantees->save($EmdGuarantees);
                }

                $this->Flash->success(__('Amount has been added successfully.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Failed to add Amount. Please try again.'));
            }
        }

        $Emdamount = $this->EmdAmount->find()
            ->where(['bank_guarantee_id' => $id])
            ->order(['id' => 'DESC'])
            ->toArray();

        $this->set(compact('Emdamount'));
    }




    public function viewremarks($id)
    {
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');

        if ($this->request->data) {
            $remarkEntity = $this->EmdRemarks->newEntity();
            $data = $this->request->data;
            $data['bank_guarantee_id'] = $id;
            $data['created'] = date('Y-m-d H:i:s');

            if ($this->request->data['invoice_file']['name']) {
                $imgname = $this->request->data['invoice_file']['name'];
                $item = $this->request->data['invoice_file']['tmp_name'];
                $ext = end(explode('.', $this->request->data['invoice_file']['name']));
                $imagename = rand() . '.' . $ext;
                $db = $this->request->session()->read('Auth.User.db');
                $dest = WWW_ROOT . 'images/' . $db . '_image/emd/' . $imagename;
                // pr($dest);exit;
                if (move_uploaded_file($item, $dest)) {
                    $data['invoice_file'] = $imagename;
                }
            }
            $data['remark'] = $this->removeEmojis($this->request->data['remark']);
            $remarkEntity = $this->EmdRemarks->patchEntity($remarkEntity, $data);

            if ($this->EmdRemarks->save($remarkEntity)) {
                $this->Flash->success(__('Remark has been added successfully.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Failed to add remark. Please try again.'));
            }
        }


        $EmdGuarantees = $this->EmdGuarantees->find('all')->where(['EmdGuarantees.id' => $id])->order(['EmdGuarantees.id' => 'Desc'])->first();

        $EmdRemarks = $this->EmdRemarks->find('all')->where(['EmdRemarks.bank_guarantee_id' => $id])->order(['EmdRemarks.id' => 'Desc'])->toarray();
        $this->set(compact('EmdRemarks', 'EmdGuarantees'));
    }



    public function excel()
    {
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');
        $where = $this->request->session()->read('apk');
        if ($where) {
            $EmdGuarantees = $this->EmdGuarantees->find('all')->where([$where])->order(['EmdGuarantees.id' => 'DESC'])->toarray();
            $this->request->session()->delete('apk');
        } else {
            $EmdGuarantees = $this->EmdGuarantees->find('all')->where(['EmdGuarantees.status' => 'N'])->order(['EmdGuarantees.id' => 'DESC'])->toarray();
        }
        $this->set(compact('EmdGuarantees'));
    }



    public function status($id, $status)
    {
        $this->loadModel('EmdGuarantees');
        if (isset($id) && !empty($id)) {
            $product = $this->EmdGuarantees->get($id);
            $product->status = $status;
            if ($this->EmdGuarantees->save($product)) {
                if ($status == 'Y') {
                    $this->Flash->success(__('EMD status has been Activeted.'));
                } else {
                    $this->Flash->success(__('EMD status has been Deactiveted.'));
                }
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');

        $EmdGuarantees = $this->EmdGuarantees->get($id);
        $this->set(compact('EmdGuarantees'));

        if ($this->request->is('post')) {
            $item = $this->request->data;

            $dateFields = ['datefrom', 'validupto', 'extenstionupto', 'claim_upto', 'lastdate', 'relese_date'];

            foreach ($dateFields as $field) {
                if (!empty($item[$field]) && $item[$field] != '1970-01-01') {
                    $item[$field] = date('Y-m-d', strtotime($item[$field]));
                } else {
                    $item[$field] = null;
                }
            }

            if ($this->request->data['invoice_file']['name']) {
                $imgname = $this->request->data['invoice_file']['name'];
                $tmpFile  = $this->request->data['invoice_file']['tmp_name'];
                $ext = end(explode('.', $this->request->data['invoice_file']['name']));
                $imagename = rand() . '.' . $ext;
                $db = $this->request->session()->read('Auth.User.db');
                $dest = WWW_ROOT . 'images/' . $db . '_image/emd/' . $imagename;
                // pr($dest);exit;
                if (move_uploaded_file($tmpFile, $dest)) {
                    $item['invoice_file'] = $imagename;
                }
            } else {
                $item['invoice_file'] = $EmdGuarantees['invoice_file'];
            }


            if ($this->request->data['amount'] > $EmdGuarantees['amount']) {
                $item['status'] = 'N';
            }
            $savepack = $this->EmdGuarantees->patchEntity($EmdGuarantees, $item);
            $results = $this->EmdGuarantees->save($savepack);


            if ($results) {

                $this->Flash->success(__('Bank Guarantee and Remark have been updated.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('EMD not Updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }




    public function delete($id)
    {
        $this->loadModel('EmdGuarantees');
        $this->loadModel('EmdRemarks');
        $this->loadModel('EmdAmount');

        $delete = $this->EmdGuarantees->get($id);
        if ($delete) {
            $result = $this->EmdGuarantees->deleteAll(['EmdGuarantees.id' => $id]);
            if ($result) {
                $this->EmdRemarks->deleteAll(['EmdRemarks.bank_guarantee_id' => $id]);
                $this->EmdAmount->deleteAll(['EmdAmount.bank_guarantee_id' => $id]);
            }
            $this->Flash->success(__('EMD has been deleted successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }
}
