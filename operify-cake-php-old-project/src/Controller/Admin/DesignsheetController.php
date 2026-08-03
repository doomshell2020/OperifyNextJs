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

class DesignsheetController extends AppController
{

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Designsheet');

        $req_data = $_GET;
        $contract_id = $req_data['contract_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datestart']));
        $dateto = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (!empty($contract_id)) {
            $contra = ['Designsheet.contract_id LIKE' => '%' . $contract_id . '%'];
            $cond[] = $contra;
        }
        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Designsheet.datefrom ) >=' => $datefrom];
            $cond[] = $contra;
        }
        if ($dateto !== '1970-01-01') {
            $contra = ['DATE(Designsheet.datefrom ) <=' => $dateto];
            $cond[] = $contra;
        }

        if ($req_data) {
            $designs = $this->Designsheet->find('all')->where([$cond])->order(['Designsheet.id' => 'Desc']);
        } else {
            $designs = $this->Designsheet->find('all')->order(['Designsheet.id' => 'desc']);
        }

        $designs = $this->paginate($designs)->toarray();
        $this->set('designs', $designs);
    }

    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');

        $designsheetno = $this->Designsheet->find('all')->order(['Designsheet.designsheetno' => 'Desc'])->first();
        if ($designsheetno['designsheetno'] != "") {
            $newdesignsheetno = $designsheetno['designsheetno'] + 1;
        } else {
            $newdesignsheetno = "1001";
        }

        $this->set('newdesignsheetno', $newdesignsheetno);

        if ($this->request->is(['post'])) {


            // $categoryIds = [];
            // foreach ($this->request->data['is_group'] as $group) {
            //     foreach ($group as $catId => $value) {
            //         $categoryIds[] = $catId;
            //     }
            // }
            // if (count($categoryIds) !== count(array_unique($categoryIds))) {
            //     $this->Flash->error(__('You have entered the same category more than once.'));
            //     return $this->redirect(['action' => 'add']);
            // }


            if ($this->request->data('contract_id') == '') {
                $this->Flash->error(__('Your enterd Contract does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            if ($this->request->data('item_id') == '') {
                $this->Flash->error(__('Your enterd Product does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            $design['contract_id'] = $this->request->data['contract_id'];
            $design['designsheetno'] = $this->request->data['designsheetno'];
            $design['item_id'] = $this->request->data['item_id'];
            $design['quantity'] = $this->request->data['quantity'];
            $design['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));

            $tmp_name = $_FILES['design_sheet']['tmp_name'];
            $image_name = $_FILES['design_sheet']['name'];
            $pext = pathinfo($image_name, PATHINFO_EXTENSION);
            // if ($pext == 'pdf' || $pext == 'jpg') {
            $imagenewname = time() . md5($image_name) . '.' . $pext;
            $webroot = WWW_ROOT;
            $newfile = $webroot . 'designsheet/' . $imagenewname;
            if (move_uploaded_file($tmp_name, $newfile)) {
                $design['design_sheet'] = $imagenewname;
            }

            // } else {
            // $this->Flash->error(__('Invalid file formate.'));
            // return $this->redirect(['action' => 'add']);
            // }

            $dsheet = $this->Designsheet->patchEntity($this->Designsheet->newEntity(), $design);
            if ($purchasess = $this->Designsheet->save($dsheet)) {
                $lstid = $purchasess->id;

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $poerder['designsheet_id'] = $lstid;
                    $poerder['designsheetno'] = $this->request->data['designsheetno'];
                    $poerder['contract_id'] = $this->request->data['contract_id'];
                    $poerder['item_id'] = $value;
                    $poerder['km_item_qty'] = $this->request->data['km_item_qty'][$key];
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    if ($this->request->data['is_group'][$key]) {
                        $poerder['is_group'] = $this->request->data['is_group'][$key];
                    } else {
                        $poerder['is_group'] = '0';
                    }
                    ;
                    $poerder['uom'] = $this->request->data['unit_name'][$key];

                    $newpo = $this->Designsheetdetails->patchEntity($this->Designsheetdetails->newEntity(), $poerder);
                    $poresustnew = $this->Designsheetdetails->save($newpo);
                }

            }
            $this->Flash->success(__('Design Sheet has been saved successfully.'));
            return $this->redirect(['action' => 'index']);
        }


    }


    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Bomrawmaterial');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');



        $desheet = $this->Designsheet->get($id);
        $product = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheet_id' => $id])->toarray();
        $pdffile = [$desheet['r1'], $desheet['r2'], $desheet['r3'], $desheet['r4'], $desheet['r5']];

        $contractid = $desheet['contract_id'];

        $bomfinishedproduct = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.contract_id' => $contractid])->toarray();

        foreach ($bomfinishedproduct as $value) {
            $finishitem[] = $this->Additem->find('all')->where(['Additem.id' => $value['product_id']])->first();
        }

        foreach ($product as $value) {
            $rawitem[] = $value['item_id'];
        }



        $this->set(compact('desheet', 'product', 'finishitem', 'rawitem'));

        if ($this->request->is(['post', 'put'])) {

            // pr($this->request->data);die;

            if ($this->request->data('contract_id') == '') {
                $this->Flash->error(__('Your enterd Contract does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            if ($this->request->data('item_id') == '') {
                $this->Flash->error(__('Your enterd Product does not exists.'));
                return $this->redirect(['action' => 'add']);
            }

            $design['contract_id'] = $this->request->data['contract_id'];
            $design['designsheetno'] = $this->request->data['designsheetno'];
            $design['item_id'] = $this->request->data['item_id'];
            $design['quantity'] = $this->request->data['quantity'];
            $design['datefrom'] = date('Y-m-d H:i:s', strtotime($this->request->data['datefrom']));
            $design['updated'] = date('Y-m-d H:i:s');

            if ($this->request->data['design_sheet']['name'] != '') {

                $webroot = WWW_ROOT . 'designsheet/';
                $filePath = $webroot . $desheet['design_sheet'];

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $tmp_name = $this->request->data['design_sheet']['tmp_name'];
                $image_name = $this->request->data['design_sheet']['name'];
                $pext = pathinfo($image_name, PATHINFO_EXTENSION);

                // if ($pext == 'pdf'  || $pext == 'jpg') {
                $imagenewname = time() . md5($image_name) . '.' . $pext;
                $newfile = $webroot . $imagenewname;
                if (move_uploaded_file($tmp_name, $newfile)) {
                    $design['design_sheet'] = $imagenewname;
                }
                // } else {
                // $this->Flash->error(__('Invalid file formate.'));
                // return $this->redirect(['action' => 'edit/' . $desheet['id']]);
                // }

            } else {
                $design['design_sheet'] = $desheet['design_sheet'];
            }

            $revisedsheet = [
                $this->request->data['r1'],
                $this->request->data['r2'],
                $this->request->data['r3'],
                $this->request->data['r4'],
                $this->request->data['r5']
            ];

            $i = 1;
            foreach ($revisedsheet as $value) {
                if ($value['name'] != '') {

                    $webroot = WWW_ROOT . 'designsheet/';
                    $filePath = $webroot . $desheet['r' . $i];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    $tmp_name = $value['tmp_name'];
                    $image_name = $value['name'];
                    $pext = pathinfo($image_name, PATHINFO_EXTENSION);
                    // if ($pext == 'pdf'  || $pext == 'jpg') {
                    $imagenewname = time() . md5($image_name) . '.' . $pext;
                    $newfile = $webroot . $imagenewname;
                    if (move_uploaded_file($tmp_name, $newfile)) {
                        $design['r' . $i] = $imagenewname;
                    }
                    // } else {
                    // $this->Flash->error(__('Invalid file formate.'));
                    // return $this->redirect(['action' => 'edit/' . $desheet['id']]);
                    // }
                } else {
                    $design['r' . $i] = $desheet['r' . $i];
                }
                $i++;
            }


            $deigsheet = $this->Designsheet->patchEntity($desheet, $design);

            if ($purchasess = $this->Designsheet->save($deigsheet)) {
                $lstid = $purchasess->id;


                foreach ($this->request->data['pitemname11'] as $key => $value) {
                    $designsheetdetail = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $desheet['designsheetno'], 'Designsheetdetails.item_id' => $value])->first();

                    // $poerder['designsheet_id'] = $lstid;
                    // $poerder['contract_id'] = $this->request->data['contract_id'];
                    // $poerder['designsheetno'] = $this->request->data['designsheetno'];
                    // $poerder['item_id'] = $value;
                    // $poerder['km_item_qty'] = $this->request->data['pitemquantity11'][$key];
                    // $poerder['item_qty'] = $this->request->data['pitemquantity12'][$key];
                    // $poerder['uom'] = $this->request->data['unit_name111'][$key];
                    if ($this->request->data['is_group'][$key]) {
                        $poerder['is_group'] = $this->request->data['is_group'][$key];
                    } else {
                        $poerder['is_group'] = '0';
                    }
                    // pr($poerder);die;
                    $newpo = $this->Designsheetdetails->patchEntity($designsheetdetail, $poerder);
                    $poresustnew = $this->Designsheetdetails->save($newpo);
                }

                foreach ($this->request->data['pitemname'] as $key => $value) {
                    $poerder['designsheet_id'] = $lstid;
                    $poerder['designsheetno'] = $this->request->data['designsheetno'];
                    $poerder['contract_id'] = $this->request->data['contract_id'];
                    $poerder['item_id'] = $value;
                    $poerder['km_item_qty'] = $this->request->data['km_item_qty'][$key];
                    $poerder['item_qty'] = $this->request->data['pitemquantity'][$key];
                    $poerder['uom'] = $this->request->data['unit_name'][$key];
                    if ($this->request->data['is_group'][$key]) {
                        $poerder['is_group'] = $this->request->data['is_group'][$key];
                    } else {
                        $poerder['is_group'] = '0';
                    }

                    $newpo = $this->Designsheetdetails->patchEntity($this->Designsheetdetails->newEntity(), $poerder);
                    $poresustnew = $this->Designsheetdetails->save($newpo);
                }

            }

            $this->Flash->success(__('Design Sheet has been updated successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function deletedata()
    {
        $this->loadModel('Designsheetdetails');
        $this->autoRender = false;
        $fetch = $this->request->data['fetch'];
        $result = $this->Designsheetdetails->deleteAll(['Designsheetdetails.id' => $fetch]);
        $this->set('result', $result);

    }



    public function delete($id)
    {
        $this->loadModel('Designsheetdetails');
        $this->loadModel('Designsheet');
        $design = $this->Designsheet->get($id);

        $product = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheet_id' => $id])->toarray();
        $pdffile = [$design['design_sheet'], $design['r1'], $design['r2'], $design['r3'], $design['r4'], $design['r5']];
        $webroot = WWW_ROOT . 'designsheet/';

        if ($this->Designsheet->delete($design)) {
            foreach ($pdffile as $product1) {
                if ($product1 != '') {
                    $filePath = $webroot . $product1;

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            foreach ($product as $product11) {
                if ($product11 != '') {
                    $filePath = $this->Designsheetdetails->delete($product11);
                }
            }

            $this->Flash->success('Production Sheet deleted successfully');
        }
        return $this->redirect(['action' => 'index']);
    }

    public function searchitem()
    {
        $this->loadModel('Designsheet');

        $req_data = $_GET;
        $contract_id = $req_data['contract_id'];
        $datefrom = date('Y-m-d', strtotime($req_data['datestart']));
        $dateto = date('Y-m-d', strtotime($req_data['dateto']));

        $cond = [];
        if (!empty($contract_id)) {
            $contra = ['Designsheet.contract_id' => $contract_id];
            $cond[] = $contra;
        }
        if ($datefrom !== '1970-01-01') {
            $contra = ['DATE(Designsheet.datefrom ) >=' => $datefrom];
            $cond[] = $contra;
        }

        if ($dateto !== '1970-01-01') {
            $contra = ['DATE(Designsheet.datefrom ) <=' => $dateto];
            $cond[] = $contra;
        }

        $user = $this->Designsheet->find('all')->where([$cond])->order(['Designsheet.id' => 'Desc']);
        $user = $this->paginate($user)->toarray();
        $this->set('designs', $user);
    }

    public function indentitems()
    {
        $this->loadModel('Indent');
        $this->loadModel('Additem');
        $this->loadModel('Taxmaster');
        $this->loadModel('Measurementunit');
        $this->loadModel('Vendorbillto');
        $fetch = $this->request->data['fetch'];
        $fetc = $this->request->data['fetc'];
        $this->set('fetc', $fetc);

        $itemname = $this->Additem->find('All')->contain(['Measurementunit'])->where(['Additem.status' => 'Y', 'Additem.id' => $fetch])->order(['Additem.id' => 'asc'])->first();
        // pr($itemname['category_id']);die;
        $this->set('itemname', $itemname);

        $tax = $this->Taxmaster->find('list', ['keyField' => 'id', 'valueField' => 'tax'])->where(['Taxmaster.status' => 'Y', 'Taxmaster.parent' => '0'])->order(['Taxmaster.id' => 'asc'])->toarray();
        $this->set('tax', $tax);

        $indent = $this->Indent->find('list', ['keyField' => 'indent_id', 'valueField' => 'indent_id'])->group(['indent_id'])->where(['Indent.indent_status' => 'P'])->order(['Indent.id' => 'asc'])->toarray();
        $this->set('indent', $indent);
    }

    public function getbomfinshedproduct()
    {
        $this->loadModel('Bom');
        $this->loadModel('Bomfinishedproduct');
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');

        $this->autoRender = false;
        $contractid = $this->request->data['contractid'];

        $bomfinishedproduct = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.contract_id' => $contractid])->toarray();
        foreach ($bomfinishedproduct as $value) {
            $item[] = $this->Additem->find('all')->where(['Additem.id' => $value['product_id']])->first();
        }

        $response = [
            'item' => $item,
        ];

        echo json_encode($response);
        die;
    }

    public function checkdesignsheetitem()
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Bomfinishedproduct');

        $itemid = trim($this->request->data['itemid']);
        $contractid = trim($this->request->data['contractid']);

        $checkdesign = $this->Designsheet->find('all')->where(['Designsheet.item_id' => $itemid, 'Designsheet.contract_id' => $contractid])->first();

        $itemqty = $this->Bomfinishedproduct->find('all')->where(['Bomfinishedproduct.product_id' => $itemid, 'Bomfinishedproduct.contract_id' => $contractid])->first();

        $response = [
            'checkdesign' => $checkdesign,
            'itemqty' => $itemqty['quantity'],
        ];

        echo json_encode($response);
        die;
    }


    public function viewdesignsheet($designsheetno)
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');

        $designsheet = $this->Designsheet->find('all')->where(['Designsheet.designsheetno' => $designsheetno])->first();
        $designsheetdetails = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno])->toarray();
        $this->set(compact('designsheet', 'designsheetdetails'));
    }

    public function viewdesignsheetpdf($designsheetno)
    {
        $this->loadModel('Designsheet');
        $this->loadModel('Designsheetdetails');
        $this->loadModel('Sitesettings');
        $this->loadModel('SitesettingsDetails');

        $sitesetting = $this->Sitesettings->find('all')->first();
        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $designsheet = $this->Designsheet->find('all')->where(['Designsheet.designsheetno' => $designsheetno])->first();
        $designsheetdetails = $this->Designsheetdetails->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno])->toarray();
        $this->set(compact('designsheet', 'designsheetdetails'));
        $this->set(compact(['sitesetting', 'site_details']));
    }


    public function getitemcatg()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $response = array();
        $unitid = $this->Additem->find('all')->where(['Additem.category_id' => $this->request->data['fetch']])->first();
        // pr($unitid);die;
        // $unitname = $this->Measurementunit->find('all')->select(['unit_name'])->where(['Measurementunit.id' => $unitid['unit_id']])->first();
        $response['id'] = $unitid['category_id'];
        // $response['item_name'] = $unitid['item_name'];
        die(json_encode($response));
    }

}