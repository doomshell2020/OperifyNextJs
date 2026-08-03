<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\View\Helper;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
/**
 *Creating Cup Boards for Library Management System 
 */
class VendorsController extends AppController
{
	//----------------------------------------
	public $helpers = ['CakeJs.Js'];

	//----------------------------------------
	public function initialize()
	{
		parent::initialize();

		//Loading Models
		$this->loadModel('Vendor');
		$this->loadModel('States');
		$this->loadModel('Cities');
		$this->loadModel('Vendorbillto');
		$this->loadModel('Vendorshipfrom');
	}

	//data add and edit function
	public function add($id = null)
	{
		$this->viewBuilder()->layout('admin');
		$state = $this->States->find('list')->order(['States.id' => 'Asc'])->toarray();
		$this->set('state', $state);
		$city = $this->Cities->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Cities.status' => 'Y'])->order(['Cities.id' => 'asc'])->toarray();
		$this->set('city', $city);
		if (isset($id) && !empty($id)) {
			$bookvendor = $this->Vendor->get($id);
			$Vendorbillto = $this->Vendorbillto->find('all')->where(['Vendorbillto.vendor_id' => $id])->toarray();
			$Vendorshipfrom = $this->Vendorshipfrom->find('all')->where(['Vendorshipfrom.vendor_id' => $id])->toarray();
			$this->set('Vendorbillto', $Vendorbillto);
			$this->set('Vendorshipfrom', $Vendorshipfrom);
		} else {
			$bookvendor = $this->Vendor->newEntity();
		}
		$this->set('vendor', $bookvendor);
		if ($this->request->is(['post', 'put'])) {

			if ($id) {
				$exisits = $this->Vendor->exists(['name' => trim($this->request->data['name']), 'NOT' => ['id' => $id]]);
			} else {
				$exisits = $this->Vendor->exists(['name' => trim($this->request->data['name'])]);
			}

			if ($exisits) {
				$this->Flash->error(__("Your entered vendor already exists"));
				if ($id) {
					return $this->redirect(['action' => 'add/', $id]);
				} else {
					return $this->redirect(['action' => 'add']);
				}
			}

			$req_data['name'] = strtoupper($this->request->data['name']);
			$req_data['contact_no'] = $this->request->data['contact_no'];
			$req_data['email'] = strtolower($this->request->data['email']);
			$req_data['vat_no'] = $this->request->data['vat_no'];
			$req_data['tin_no'] = $this->request->data['tin_no'];
			$req_data['type'] = $this->request->data['vendortype'];
			$req_data['state_id'] = $this->request->data['billtostate_id'];
			$req_data['address'] = $this->request->data['billtoaddress'];
			$req_data['gst_number'] = $this->request->data['billtogst_number'];
			$req_data['contact_person'] = $this->request->data['contactperson'];
			$req_data['tin_date'] = date('Y-m-d', strtotime($this->request->data['tin_dated']));
			$req_data['pancard_number'] = $this->request->data['pancard_number'];

			if ($this->request->data['tds'] == 1) {
				$req_data['tds'] = $this->request->data['tds'];
			}
			$req_data['description'] = $this->removeEmojis($this->request->data['description']);
			$bookvendor = $this->Vendor->patchEntity($bookvendor, $req_data);
			if ($vendo = $this->Vendor->save($bookvendor)) {
				if (isset($req_data['button']) && !empty($req_data['button']))
					$this->Flash->success(__('Your Vendor has been updated.'));
				else
					$this->Flash->success(__('Your Vendor has been created.'));
				return $this->redirect(['action' => 'index']);
			} else {
				if ($bookvendor->errors()) {
					$error_msg = [];
					foreach ($bookvendor->errors() as $errors) {
						if (is_array($errors)) {
							foreach ($errors as $error) {
								$error_msg[] = $error;
							}
						} else {
							$error_msg[] = $errors;
						}
					}
					if (!empty($error_msg)) {
						$this->Flash->error(__("Please fix the following error(s): " . implode("\n \r", $error_msg)));
					}
				}
			}
		}
		$vendors = $this->Vendor->find('all')->order(['Vendor.id' => 'DESC'])->toarray();
		$this->set('vendors', $vendors);
	}

	public function index($id = null)
	{
		$this->viewBuilder()->layout('admin');
		$state = $this->States->find('list')->order(['States.id' => 'Asc'])->toarray();
		$this->set('state', $state);
		$city = $this->Cities->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Cities.status' => 'Y'])->order(['Cities.id' => 'asc'])->toarray();
		$this->set('city', $city);

		if (isset($id) && !empty($id)) {
			$bookvendor = $this->Vendor->get($id);
		} else {
			$bookvendor = $this->Vendor->newEntity();
		}
		$this->set('vendor', $bookvendor);

		$req_data = $_GET;
		$vendor_id = $req_data['vendor_id'];
		$contact = $req_data['contact'];

		$apk = array();
		if (!empty($vendor_id)) {
			$stts = array('Vendors.id' => $vendor_id);
			$apk[] = $stts;
		}
		if (!empty($contact)) {
			$stts = array('Vendors.contact_no LIKE' => '%' . $contact . '%');
			$apk[] = $stts;
		}
		$stts = array('Vendors.status' => 'Y');
		$apk[] = $stts;
		if ($req_data) {
			$vendors = $this->Vendors->find('all')->where($apk)->order(['Vendors.name' => 'ASC']);
		} else {
			$vendors = $this->Vendor->find('all')->order(['Vendor.name' => 'ASC ']);
		}
		$vendors = $this->paginate($vendors)->toarray();
		$this->set('vendors', $vendors);

		//code remove on 01-03-2024
	}



	//---------------------------------------------------------
	public function delete($id)
	{
		$this->loadModel('Vendor');
		$this->loadModel('Goodsreceived');
		$this->autoRender = false;
		$vendor_count = $this->Goodsreceived->find('all')->where(['Goodsreceived.vendor_id' => $id])->count();
		if ($vendor_count > 0) {
			$this->Flash->error('This Vendor Can not be Deleted as it is already used in the system');
			return $this->redirect(['action' => 'index']);
		}
		$vendor = $this->Vendor->get($id);

		if ($this->Vendor->Delete($vendor)) {
			$this->Flash->success(__('The Vendor with id: {0} has been deleted.', h($id)));
			return $this->redirect(['action' => 'index']);
		}
	}

	//---------------------------------------------------------
	public function view($id)
	{
		$this->viewBuilder()->layout('admin');
		$bookvendor = $this->Vendor->get($id);
		$vendors = $this->Vendors->find('all')->order(['Vendors.id' => 'Desc'])->toarray();
		$this->set(compact('vendor'));
	}


	public function excel()
	{
		$this->viewBuilder()->layout('admin');
		$cond = $this->request->session()->read('searchitem');
		$vendors = $this->Vendors->find('all')->where([$cond])->order(['Vendors.name' => 'ASC'])->toarray();
		$this->set(compact('vendors'));
	}

	
	// billto function use to vendors add form
	public function billto()
	{
		$state = $this->States->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['States.status' => 'Y'])->order(['States.id' => 'asc'])->toarray();
		$this->set('state', $state);
		$city = $this->Cities->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Cities.status' => 'Y'])->order(['Cities.id' => 'asc'])->toarray();
		$this->set('city', $city);
		$srno = $this->request->data['srno'];
		$this->set('srno', $srno);
	}

	public function shipfrom()
	{
		$state = $this->States->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['States.status' => 'Y'])->order(['States.id' => 'asc'])->toarray();
		$this->set('state', $state);

		$srno = $this->request->data['srno'];
		$this->set('srno', $srno);
	}
	//search in vendors index page
	public function searchitem()
	{

		$this->loadModel('Vendors');
		$req_data = $_GET;
		$vendor_id = $req_data['vendor_id'];
		$contact = $req_data['contact'];
		$apk = array();
		if (!empty($vendor_id)) {
			$stts = array('Vendors.id' => $vendor_id);
			$apk[] = $stts;
		}
		if (!empty($contact)) {
			$stts = array('Vendors.contact_no LIKE' => '%' . $contact . '%');
			$apk[] = $stts;
		}
		$stts = array('Vendors.status' => 'Y');
		$apk[] = $stts;

		$this->request->session()->delete('searchitem');
		$this->request->session()->write('searchitem', $apk);
		$vendors = $this->Vendors->find('all')->where($apk)->order(['Vendors.name' => 'ASC']);
		$vendors = $this->paginate($vendors)->toarray();
		$this->set(compact('vendors'));
	}



	public function getname()
	{
		$this->loadModel('Vendors');
		$stsearch = $this->request->data['fetch'];
		$check = $this->request->data['check'];
		$searchst = $this->Vendors->find('all')->where(['Vendors.name LIKE' => '%' . $stsearch . '%', 'Vendors.status' => 'Y'])->toarray();
		foreach ($searchst as $value) {
		echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['name'] . '</a></li>';
		}
		die;
	}
	public function gettransoptername()
	{
		$this->loadModel('Vendors');


		$stsearch = $this->request->data['fetch'];
		$check = $this->request->data['check'];
		$searchst = $this->Vendors->find('all')->where(['Vendors.name LIKE' => '%' . $stsearch . '%', 'Vendors.status' => 'Y', 'Vendors.type' => 'Transporter'])->toarray();
		foreach ($searchst as $value) {
			echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['name'] . '</a></li>';
		}
		die;
	}
	// show city in vendors add form
	public function getcity()
	{

		$id = $this->request->data['dataString'];
		$city = $this->Cities->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Cities.s_id' => $id, 'Cities.status' => 'Y'])->order(['Cities.id' => 'asc'])->toarray();

		header('Content-Type: application/json');
		echo json_encode($city);
		die;
	}

	public function storedetailreport()
	{
		$this->viewBuilder()->layout('admin');


		$this->loadModel('Branchrequest');

		// $store_data = $this->branchrequest->find('all')->where([['DATE(branchrequest.created)>='=>date('Y-m-01')] && ['DATE(branchrequest.created)<='=>date('Y-m-d')]])->toarray();
		$store_data = $this->Branchrequest->find('all')->where(['Branchrequest.pay_date NOT IN' => ['Null']])->toarray();
		$this->set('store_data', $store_data);
	}

	public function searchdetailreport()
	{
		$datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
		$dateto = date('Y-m-d', strtotime($this->request->data['dateto']));
		$this->set(compact('datefrom'));
		$this->set(compact('dateto'));
		$this->loadModel('Branchrequest');
		$store_data = $this->Branchrequest->find('all')->where(['Branchrequest.pay_date NOT IN' => ['Null']])->toarray();
		$this->set('store_data', $store_data);
	}

	public function exportstorereport()
	{
		$this->loadModel('Branchrequest');
		$datefrom = $_SESSION['datefrom'];
		$dateto = $_SESSION['dateto'];
		if (!empty($datefrom)) {
			$cond .= " AND DATE(Branchrequest.approved_date) >= '" . $datefrom . "'";
		}
		if (!empty($d2dateto)) {
			$cond .= " AND DATE(Branchrequest.approved_date) <= '" . $dateto . "'";
		}

		$this->loadModel('Taxmaster');
		$this->loadModel('Branchrequestdetail');
		$this->loadModel('Additem');
		$store_datas = $this->Branchrequest->find('all')->where(['DATE(Branchrequest.approved_date) >=' => $datefrom, 'DATE(Branchrequest.approved_date) <=' => $dateto, 'Branchrequest.status' => 'Approved'])->order(['Branchrequest.id' => 'Asc'])->toarray();
		foreach ($store_datas as $val) {
			$branch_request_data[] = $val['id'];
		}

		if ($branch_request_data) {
			$branch_request_detail = $this->Branchrequestdetail->find('all')->where(['Branchrequestdetail.branchrequest_id IN' => $branch_request_data, 'Branchrequest.status' => 'Approved'])->contain(['Additem' => ['Taxmaster'], 'Branchrequest'])->order(['Branchrequest.pay_date' => 'Asc'])->toarray();
		}
		$this->set('branch_request_detail', $branch_request_detail);
	}




	public function posummaryreport()
	{
		$this->loadModel('Vendor');
		$this->viewBuilder()->layout('admin');

		if ($this->request->is(['post'])) {
			if ($this->request->data['file']['tmp_name']) {
				$empexcel = $this->request->data['file'];
				$inputfilename = $empexcel['tmp_name'];

				try {
					$objPHPExcel = \PHPExcel_IOFactory::load($inputfilename);
				} catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputfilename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
				}

				$sheet = $objPHPExcel->getActiveSheet();
				$dataArr = array();
				$highestRow = $sheet->getHighestDataRow();
				$highestColumn = $sheet->getHighestDataColumn();
				$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
				for ($row = 2; $row <= $highestRow; ++$row) {
					$rowData = array();
					for ($col = 0; $col < $highestColumnIndex; ++$col) {
						$val = $sheet->getCellByColumnAndRow($col, $row)->getValue();
						$rowData[] = $val;
					}
					$dataArr[] = $rowData;
				}
				$atn = array();
				foreach ($dataArr as $key => $opt) {
					$vendor = $this->Vendor->get($opt[0]);
					$atn['name'] = $opt[1];
					$atn['pancard_number'] = $opt[2];
					$atn['contact_no'] = $opt[3];
					$atn['email'] = $opt[4];
					$atn['address'] = $opt[5];
					$atn['contact_person'] = $opt[6];
					$atn['type'] = $opt[7];

					$save = $this->Vendor->patchEntity($vendor, $atn);
					$this->Vendor->save($save);
				}
				if ($save) {
					$this->Flash->success(__('Your Vendor Excel Successfully'));
					return $this->redirect(['action' => 'index']);
				} else {
					$this->Flash->error(__('Your Vendor Excel Not Success'));
					return $this->redirect(['action' => 'index']);
				}
			}
		}
	}


	public function viewdetail($id)
	{
		$this->loadModel('Vendor');
		$this->loadModel('States');
		$vendor = $this->Vendor->find('all')->contain('States')->where(['Vendor.id' => $id])->first();
		$this->set('vendor', $vendor);
	}

}
