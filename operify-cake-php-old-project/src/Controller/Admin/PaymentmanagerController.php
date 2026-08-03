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
class PaymentmanagerController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        parent::initialize();
    }
    public function index($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Particularpayments');
        $particularList = $this->Particularpayments->find()
            ->select(['particular'])
            ->group('particular')
            ->order(['particular' => 'ASC'])
            ->combine('particular', 'particular')
            ->toArray();

        $this->set('particularList', $particularList);

        $req_data = $_GET;
        $bg_for = $req_data['particular'];
        $invoice = $req_data['invoice'];
        $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
        $dateto   = !empty($req_data['dateto'])   ? date('Y-m-d', strtotime($req_data['dateto']))   : null;
        $status = $req_data['status'] ?? null;
        $sortby = $req_data['sortby'] ?? null;

        $cond = [];
        if (!empty($bg_for)) {
            $contra = ['Particularpayments.particular' => $bg_for];
            $cond[] = $contra;
        }

        if (!empty($status)) {
            $cond['Particularpayments.status'] = $status;
        }
        if (!empty($invoice)) {
            $contra = ['Particularpayments.invoice' => $invoice];
            $cond[] = $contra;
        }

        $query = $this->Particularpayments->find()->where($cond);

        if (!empty($datefrom) && !empty($dateto)) {
            $query->where(function ($exp) use ($datefrom, $dateto) {
                return $exp->between(
                    new \Cake\Database\Expression\QueryExpression("DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)"),
                    $datefrom,
                    $dateto
                );
            });
        }
        // $this->request->session()->write('apk', $query->clause('where'));

        if ($sortby == 'N') {
            $order = ['(Particularpayments.bill_dis_date IS NULL)' => 'DESC'];
        } elseif ($sortby == 'D') {
            $order = [
                '(Particularpayments.bill_dis_date IS NULL)' => 'ASC',
                'DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)' => 'ASC'
            ];
        } elseif ($sortby == 'L') {
            $order = ['Particularpayments.id' => 'DESC'];
        } else {
            $order = [
                '(Particularpayments.bill_dis_date IS NULL)' => 'ASC',
                'DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)' => 'ASC'
            ];
        }

        if (!empty($cond) || (!empty($datefrom) && $datefrom !== '1970-01-01')) {
            $Particularpayments = $query->order($order);
            $users = $this->paginate($Particularpayments)->toArray();

            $this->set('users', $users);
        } else {
            $users = $this->Particularpayments->find('all')->where(['Particularpayments.status' => 'P'])->order($order);
            $users = $this->paginate($users)->toarray();
            $this->set(compact('users'));
        }
    }






    public function searchitem()
    {
        $this->loadModel('Particularpayments');
        $req_data = $_GET;
        $bg_for = $req_data['particular'] ?? null;
        $status = $req_data['status'] ?? null;
        $use_due_period = !empty($req_data['due_period']);
        $datefrom = !empty($req_data['datefrom']) ? date('Y-m-d', strtotime($req_data['datefrom'])) : null;
        $dateto   = !empty($req_data['dateto'])   ? date('Y-m-d', strtotime($req_data['dateto']))   : null;
        $sortby = $req_data['sortby'] ?? null;

        $cond = [];

        if (!empty($bg_for)) {
            $cond['Particularpayments.particular'] = $bg_for;
        }
        if (!empty($invoice)) {
            $cond['Particularpayments.invoice'] = $invoice;
        }
        if (!empty($status)) {
            $cond['Particularpayments.status'] = $status;
        }

        $query = $this->Particularpayments->find()->where($cond);

        if (!empty($datefrom) && !empty($dateto)) {
            $query->where(function ($exp) use ($datefrom, $dateto) {
                return $exp->between(
                    new \Cake\Database\Expression\QueryExpression("DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)"),
                    $datefrom,
                    $dateto
                );
            });
        }

        if ($sortby == 'N') {
            $order = ['(Particularpayments.bill_dis_date IS NULL)' => 'DESC'];
        } elseif ($sortby == 'D') {
            $order = [
                '(Particularpayments.bill_dis_date IS NULL)' => 'ASC',
                'DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)' => 'ASC'
            ];
        } elseif ($sortby == 'L') {
            $order = ['Particularpayments.id' => 'DESC'];
        } else {
            $order = [
                '(Particularpayments.bill_dis_date IS NULL)' => 'ASC',
                'DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)' => 'ASC'
            ];
        }
        $this->request->session()->write('apk', $query->clause('where'));

        $this->request->session()->write('order', $order);

        $Particularpayments = $query->order($order);
        $users = $this->paginate($Particularpayments)->toArray();

        $this->set('bg_for', $bg_for);
        $this->set('users', $users);
    }



    public function status($id, $status)
    {
        $this->loadModel('Paymentmanager');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Paymentmanager->get($id);
                $user->status = $status;
                if ($this->Paymentmanager->save($user)) {
                    $this->Flash->success(__('Payment Details status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Paymentmanager->get($id);
                $user->status = $status;
                if ($this->Paymentmanager->save($user)) {
                    $this->Flash->success(__('Payment Details status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }




    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Particularpayments');
        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data); die;

            $dateFields = ['datefrom', 'bill_dis_date'];

            foreach ($dateFields as $field) {
                if (!empty($this->request->data[$field]) && $this->request->data[$field] != '1970-01-01') {
                    $this->request->data[$field] = date('Y-m-d', strtotime($this->request->data[$field]));
                } else {
                    $this->request->data[$field] = null;
                }
            }

            $cat = $this->Particularpayments->newEntity();
            // $this->request->data['datefrom'] = date('Y-m-d', strtotime($this->request->data['datefrom']));
            // $this->request->data['bill_dis_date'] = date('Y-m-d', strtotime($this->request->data['bill_dis_date']));
            $pnewdetail = $this->Particularpayments->patchEntity($cat, $this->request->data);
            if ($resustnew = $this->Particularpayments->save($pnewdetail)) {
                $this->Flash->success(__('Payment Details successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Particularpayments');
        // $this->loadModel('EmdRemarks');

        $EmdGuarantees = $this->Particularpayments->get($id);
        $this->set(compact('EmdGuarantees'));

        if ($this->request->is('post')) {
            $item = $this->request->data;

            $dateFields = ['datefrom', 'bill_dis_date'];

            foreach ($dateFields as $field) {
                if (!empty($item[$field]) && $item[$field] != '01-01-1970') {
                    $item[$field] = date('Y-m-d', strtotime($item[$field]));
                } else {
                    $item[$field] = null;
                }
            }


            $savepack = $this->Particularpayments->patchEntity($EmdGuarantees, $item);
            $results = $this->Particularpayments->save($savepack);


            if ($results) {

                $this->Flash->success(__('Payment details have been updated.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Payments not Updated.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    public function delete($id = null)
    {
        $this->loadModel('Particularpayments');
        $this->loadModel('Particularpayreceive');

        $this->autoRender = false;

        $res = $this->Particularpayments->get($id);

        if ($res) {
            $this->Particularpayreceive->deleteAll(['particular_id' => $id]);
            if ($this->Particularpayments->delete($res)) {
                $this->Flash->success('Payment Details deleted successfully');
            } else {
                $this->Flash->error('Failed to delete Payment Details');
            }
        } else {
            $this->Flash->error('Payment record not found');
        }

        return $this->redirect(['controller' => 'Paymentmanager', 'action' => 'index']);
    }


    public function viewamount($id)
    {
        $this->loadModel('Particularpayments');
        $this->loadModel('Particularpayreceive');

        $EmdGuarantees = $this->Particularpayments->find('all')->where(['Particularpayments.id' => $id])->order(['Particularpayments.id' => 'Desc'])->first();
        $totalReceived = $this->Particularpayreceive->find()
            ->where(['particular_id' => $EmdGuarantees['id']])
            ->sumOf('recive_amount');

        $remainingAmount = $EmdGuarantees['amount'] - $totalReceived;

        $this->set(compact('remainingAmount'));


        if ($this->request->data) {

            $remarkEntity = $this->Particularpayreceive->newEntity();
            $data = $this->request->data;
            $data['particular_id'] = $id;
            $data['created'] = date('Y-m-d H:i:s');
            $reciveDate = $data['recive_date'] ?? null;

            if ($this->request->data['invoice_file']['name']) {
                $imgname = $this->request->data['invoice_file']['name'];
                $item = $this->request->data['invoice_file']['tmp_name'];
                $ext = end(explode('.', $this->request->data['invoice_file']['name']));
                $imagename = rand() . '.' . $ext;
                $db = $this->request->session()->read('Auth.User.db');
                $dest = WWW_ROOT . 'images/' . $db . '_image/payments/' . $imagename;
                // pr($dest);exit;
                if (move_uploaded_file($item, $dest)) {
                    $data['invoice_file'] = $imagename;
                }
            }



            if ($data['recive_amount'] > $remainingAmount) {
                $this->Flash->error(__('Receive amount exceeds the remaining allowed amount.'));
                return $this->redirect($this->referer());
            }

            if (!empty($reciveDate)) {
                $data['recive_date'] = date('Y-m-d', DateTime::createFromFormat('d/m/Y', $reciveDate)->getTimestamp());
            }
            $data['total_amount'] = $EmdGuarantees['amount'];
            $data['description'] = $this->removeEmojis($data['description']);

            $remarkEntity = $this->Particularpayreceive->patchEntity($remarkEntity, $data);

            if ($this->Particularpayreceive->save($remarkEntity)) {
                $EmdGuarantees = $this->Particularpayments->find('all')->where(['Particularpayments.id' => $id])->order(['Particularpayments.id' => 'Desc'])->first();
                $totalReceivedAfterSave = $this->Particularpayreceive->find()
                    ->where(['particular_id' => $EmdGuarantees->id])
                    ->sumOf('recive_amount');

                // Close status if fully received
                if ($totalReceivedAfterSave >= $EmdGuarantees->amount) {
                    $EmdGuarantees->status = 'C';
                    $this->Particularpayments->save($EmdGuarantees);
                }
                $this->Flash->success(__('Amount has been added successfully.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Failed to add Amount. Please try again.'));
            }
        }


        $EmdRemarks = $this->Particularpayreceive->find('all')->where(['Particularpayreceive.particular_id' => $id])->order(['Particularpayreceive.id' => 'Desc'])->toarray();
        $this->set(compact('EmdRemarks', 'EmdGuarantees'));
    }


   
    // public function excel()
    // {
    //     // Include PHPExcel files
    //     $this->viewBuilder()->layout('admin');


    //     if ($this->request->is('post')) {

    //         if (!empty($this->request->data('excel_file')['tmp_name'])) {
    //             // Load the uploaded Excel file
    //             $filePath = $this->request->data('excel_file')['tmp_name'];

    //             // Load Excel file using PHPExcel
    //             $objPHPExcel = \PHPExcel_IOFactory::load($filePath);

    //             $sheet = $objPHPExcel->getActiveSheet();

    //             // Get highest row and column
    //             $highestRow = $sheet->getHighestRow();
    //             $highestColumn = $sheet->getHighestColumn();


    //             for ($row = 2; $row <= $highestRow; $row++) { // Skip header row
    //                 // Get cell objects
    //                 $dateFromCell = $sheet->getCell('E' . $row);
    //                 $validUptoCell = $sheet->getCell('F' . $row);

    //                 $datefrom = $this->convertExcelDate($dateFromCell);
    //                 $validupto = $this->convertExcelDate($validUptoCell);

    //                 $data = [
    //                     'particular'        => $sheet->getCell('A' . $row)->getValue(),
    //                     'datefrom'        => $datefrom,
    //                     'consignee' =>  $sheet->getCell('B' . $row)->getValue(),
    //                     'po_no'       => $sheet->getCell('C' . $row)->getValue(),
    //                     'invoice'           => $sheet->getCell('D' . $row)->getValue(),
    //                     'amount'          => $sheet->getCell('H' . $row)->getValue(),
    //                     'bill_dis_date'       => $validupto,
    //                     'due_period'       =>  $sheet->getCell('G' . $row)->getValue(),
    //                 ];

    //                 $this->importData($data);
    //             }


    //             $this->Flash->success(__('Excel file data has been imported successfully.'));
    //             return $this->redirect(['controller' => 'Paymentmanager', 'action' => 'index']);
    //         } else {
    //             $this->Flash->error(__('Please select an Excel file.'));
    //             return $this->redirect(['controller' => 'Paymentmanager', 'action' => 'index']);
    //         }
    //     }
    // }

    // private function importData($data)
    // {
    //     $connection = ConnectionManager::get('default'); // CakePHP DB connection
    //     $query = $connection->insert('particular_payments', $data);
    // }


    // private function convertExcelDate($cell)
    // {
    //     if (!$cell instanceof \PHPExcel_Cell) {
    //         return null;
    //     }

    //     if (\PHPExcel_Shared_Date::isDateTime($cell)) {
    //         $value = $cell->getValue();
    //         if (is_numeric($value)) {
    //             return date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($value));
    //         }
    //     }

    //     $value = trim($cell->getValue());
    //     if (!empty($value)) {
    //         $timestamp = strtotime($value);
    //         if ($timestamp !== false) {
    //             return date('Y-m-d', $timestamp);
    //         }
    //     }

    //     return null;
    // }




    public function excel()
    {
        $this->loadModel('Particularpayments');
        $where = $this->request->session()->read('apk');
        $order = $this->request->session()->read('order');
        if ($where) {
            $Particularpayments = $this->Particularpayments->find('all')->where([$where])->order($order)->toarray();
            // pr($Particularpayments);exit;
            $this->request->session()->delete('apk');
            $this->request->session()->delete('order');
        } else {
            $Particularpayments = $this->Particularpayments->find('all')->where(['Particularpayments.status' => 'P'])->order([
                '(Particularpayments.bill_dis_date IS NULL)' => 'ASC',
                'DATE_ADD(Particularpayments.bill_dis_date, INTERVAL Particularpayments.due_period DAY)' => 'ASC'
            ])->toarray();
        }

        $this->set(compact('Particularpayments'));
    }
}
