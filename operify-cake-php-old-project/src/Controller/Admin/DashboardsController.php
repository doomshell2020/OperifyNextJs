<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Http\Client;

use Cake\Utility\Security;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class DashboardsController extends AppController
{

	public function initialize()
	{
		//load all models
		parent::initialize();
		$this->Auth->allow(['vishnupo']);

		$this->loadModel('Contracts');
		$this->loadModel('Production');
		$this->loadModel('Bom');
		$this->loadModel('Maintenance');
		$this->loadModel('Purchaseorder');
		$this->loadModel('Goodsreceived');
		$this->loadModel('Vendor');
		$this->loadModel('Productionorder');
		$this->loadModel('Production');
		$this->loadModel('InspectionReport');
	}
	//-------------------------------------------------------------------------------************************************************


	function overview()
	{

		$this->viewBuilder()->layout('admin');

		$user_id = $this->Auth->user('role_id');
		// pr($user_id);die;
		// TOTAL CONTRACTS,PO,GRN,HEADER DATA---------
		$currentdate = date('Y-m-d');
		$lastweek = date('Y-m-d', strtotime('-7 days'));
		$monthdate = date('Y-m-01');
		if (date("Y-m-d") >= date("Y-04-01")) {
			$financialyear = date("Y-04-01");
		} else {
			$financialyear = date("Y-04-01", strtotime("-1 year"));
		}

		$contractcount = $this->Contracts->find('all')->where(['DATE(Contracts.added_time) >=' => $financialyear])->count();
		$todaycontractcount = $this->Production->find('all')->where(['DATE(Production.production_date)' => $currentdate])->group('contract_id')->count();
		$weekcontractcount = $this->Production->find('all')->where(['DATE(Production.production_date) >' => $lastweek])->group('contract_id')->count();
		$monthcontractcount = $this->Production->find('all')->where(['DATE(Production.production_date) >=' => $monthdate])->group('contract_id')->count();
		$this->set(compact('contractcount', 'todaycontractcount', 'weekcontractcount', 'monthcontractcount'));

		$purchasenordercount = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >=' => $financialyear])->count();
		$todaypurchasenordercount = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time)' => $currentdate])->count();
		$weekpurchasenordercount = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >' => $lastweek])->count();
		$monthpurchasenordercount = $this->Purchaseorder->find('all')->where(['DATE(Purchaseorder.added_time) >=' => $monthdate])->count();
		$this->set(compact('purchasenordercount', 'todaypurchasenordercount', 'weekpurchasenordercount', 'monthpurchasenordercount'));

		$totalgrncount = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >=' => $financialyear])->count();
		$todaylgrncount = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date)' => $currentdate])->count();
		$weeklgrncount = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >' => $lastweek])->count();
		$monthgrncount = $this->Goodsreceived->find('all')->where(['DATE(Goodsreceived.created_date) >=' => $monthdate])->count();
		$this->set(compact('totalgrncount', 'todaylgrncount', 'weeklgrncount', 'monthgrncount'));

		$suppliercount = $this->Vendor->find('all')->where(['Vendor.status' => 'Y'])->count();
		$todaylsuppliercount = $this->Vendor->find('all')->where(['DATE(Vendor.created_date)' => $currentdate])->count();
		$weeklsuppliercount = $this->Vendor->find('all')->where(['DATE(Vendor.created_date) >' => $lastweek])->count();
		$monthsuppliercount = $this->Vendor->find('all')->where(['DATE(Vendor.created_date) >=' => $monthdate])->count();
		$this->set(compact('suppliercount', 'todaylsuppliercount', 'weeklsuppliercount', 'monthsuppliercount'));

		$maintenancecount = $this->Maintenance->find('all')->where([['DATE(Maintenance.datefrom) >=' => $financialyear]])->count();
		$todaymaintenancecount = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y', 'DATE(Maintenance.datefrom)' => $currentdate])->count();
		$weekmaintenancecount = $this->Maintenance->find('all')->where([['DATE(Maintenance.datefrom) >' => $lastweek]])->count();
		$monthmaintenancecount = $this->Maintenance->find('all')->where(['DATE(Maintenance.datefrom) >=' => $monthdate])->count();
		$this->set(compact('maintenancecount', 'todaymaintenancecount', 'weekmaintenancecount', 'monthmaintenancecount'));



		// GRAPH PIE CHART DATA--------------
		$purchasenordercount = $this->Purchaseorder->find('all')->count();
		$completepurchasenorder = $this->Purchaseorder->find('all')->where(['(Purchaseorder.postatus)' => 'C'])->count();
		$totalgrn = $this->Goodsreceived->find('all')->group('Goodsreceived.purchaseorder_id')->count();
		$activepo = $totalgrn - $completepurchasenorder;
		$pendingpo = $purchasenordercount - $totalgrn;
		$this->set(compact('activepo', 'completepurchasenorder', 'pendingpo'));

		$totalproductionorder = $this->Productionorder->find('all')->count();
		$completeproductionorder = $this->Productionorder->find('all')->where(['(Productionorder.status)' => 'C'])->count();
		$activeproductionorder = $this->Production->find('all', ['DISTINCT po_id', 'item_id'])->count() - $completeproductionorder;
		$pendingproductionorder = $totalproductionorder - $activeproductionorder - $completeproductionorder;
		$this->set(compact('completeproductionorder', 'activeproductionorder', 'pendingproductionorder'));

		$completemaintenancecount = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y', 'Maintenance.maintenance_status' => 'completed'])->count();
		$assignedmaintenancecount = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y', 'Maintenance.maintenance_status' => 'assigned'])->count();
		$pendingmaintenancecount = $this->Maintenance->find('all')->where(['Maintenance.status' => 'Y', 'Maintenance.maintenance_status' => 'pending'])->count();
		$this->set(compact('completemaintenancecount', 'assignedmaintenancecount', 'pendingmaintenancecount'));


		// TABLES  DATA------------
		$maintenancedetails = $this->Maintenance->find('all')->contain('Machinemaster')->where(['Maintenance.status' => 'Y',])->order(['Maintenance.datefrom' => 'Desc'])->limit(5)->toarray();
		$GoodsreceivedCount = $this->Goodsreceived->find('all')->group('Goodsreceived.purchaseorder_id')->toarray();
		foreach ($GoodsreceivedCount as $ids) {
			$poid[] = $ids['purchaseorder_id'];
		}
		$podata = $this->Purchaseorder->find('all')->where(['Purchaseorder.status IN' => ['Y', 'R']])->order(['Purchaseorder.id' => 'DESC'])->limit(5)->toarray();
		$goodsreceived = $this->Goodsreceived->find('all')->order(['Goodsreceived.inwarddate' => 'DESC'])->limit(5)->toarray();
		$supplier = $this->Vendor->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Vendor.status' => 'Y'])->order(['Vendor.id' => "Asc"])->toarray();
		$productionorder = $this->Productionorder->find('all')->order(['Productionorder.id' => 'Desc'])->limit(5)->toarray();
		$inspection = $this->InspectionReport->find('all')->where(['InspectionReport.status' => "Y"])->order(['InspectionReport.id' => 'Desc'])->toarray();
		$this->set(compact('maintenancedetails', 'podata', 'goodsreceived', 'company', 'productionorder', 'inspection'));
	}





	// In your controller action
	public function dynamicPieChart()
	{
		$data = [
			'labels' => ['Category A', 'Category B', 'Category C', 'Category D'],
			'data' => [30, 20, 25, 25],
		];
		$this->set(compact('data'));
	}

	public function index()
	{
		$this->loadModel('Users');
		$this->viewBuilder()->layout('admin');
		$user_id = $this->Auth->user('role_id');

		$roleid = $user_id;
		if ($roleid == '105') {
			return $this->redirect(['controller' => 'dashboards', 'action' => 'headbranch']);
		} else {
			return $this->redirect(['controller' => 'dashboards', 'action' => 'adminbranch']);
		}
		if ($roleid == '18' || $roleid == '19') {
			return $this->redirect(['controller' => 'indent', 'action' => 'index']);
		}
		if ($roleid == '0') {
			return $this->redirect(['controller' => 'logins', 'action' => 'logout']);
			$this->Flash->success(__('Employee can not have defined role'));
		}

		//------------------

		$data_count = [];

		$data_count['student_count'] = $this->Students->find('all')->where(['Students.status' => 'Y'])->count();
		$data_count['faculty_count'] = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.designation_id' => '4'])->count();
		$total_staff = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->count();
		$data_count['non_teaching_staff_count'] = $total_staff - $data_count['faculty_count'];
		$data_count['class_count'] = $this->Classes->find('all')->where(['Classes.status' => '1'])->count();
		$data_count['section_count'] = $this->Classections->find('all')->where(['Classections.status' => 'Y'])->count();
		$data_count['book_count'] = $this->Book->find('all')->where(['Book.status' => 'Y'])->count();
		$data_count['event_count'] = $this->Events->find('all')->where(['Events.eventt' => '11'])->count();
		$data_count['new_admission_count'] = $this->Students->find('all')->where(['Students.admissionyear' => date("Y") . '-' . (date("y") + 1), 'Students.status' => 'Y'])->count();
		$this->set(compact('data_count'));

		//------------------

		$report_data = $this->sessionFeesReport();

		$this->set(compact('report_data'));
		// pr($report_data);die;

		//  For time table //

		$role = $this->request->session()->read('Auth.User.role_id');
		$username = $this->request->session()->read('Auth.User.tech_id');
		//pr($username); die;
		$emailgg = $this->Users->find('all')->where(['Users.role_id' => $role, 'Users.tech_id' => $username])->first();

		$email = $this->Employees->find('all')->where(['Employees.id' => $emailgg['tech_id']])->first();
		if ($role == '3') {
			$findclasssection = $this->findclassectionsed();
			$classid = $findclasssection['class_id'];
			$sectionid = $findclasssection['section_id'];
		}
		$fname = $email['fname'];
		$this->set('fname', $fname);
		$middlename = $email['middlename'];
		$this->set('middlename', $middlename);
		$lname = $email['lname'];
		$this->set('lname', $lname);


		$sectionclassselectlist = $this->Classections->find('all')->contain(['Classes', 'Sections'])->where(['Classections.teacher_id' => $email['id']])->order(['Sections.title' => 'ASC'])->first();

		$this->set('classsss', $sectionclassselectlist['Classes']['title']);

		$this->set('sectionsss', $sectionclassselectlist['Sections']['title']);

		$this->viewBuilder()->layout('admin');


		$subjectslist = $this->Subjects->find('list', [
			'keyField' => 'alias',
			'valueField' => 'name'
		])->where(['status' => '1'])->order(['name' => 'ASC'])->toArray();
		$this->set('subjectslist', $subjectslist);


		$sectionselectlist = $this->Classections->find('list', [
			'keyField' => 'Sections.id',
			'valueField' => 'Sections.title'
		])->contain(['Sections'])->where(['Classections.class_id' => $classid])->order(['Sections.title' => 'ASC'])->toArray();
		$this->set('sectionselectlist', $sectionselectlist);
		$this->set('seletedclassid', $classid);
		$this->set('seletedsectionid', $sectionid);
		$this->set('class', $classid);
		$this->set('section', $sectionid);

		$m = date('m');

		if ($m < 03) {
			$d = date('y');
			$current = $d - 1;
			$dsa = '20' . $current;
			$yeard = $dsa . '-' . $d;
			$acedimc = $yeard;
		} else {

			$date = date('Y');
			$date1 = date('y');
			$d = $date1 + 1;
			$acedimc = $date . "-" . $d;
		}
		$this->set('acedimc', $acedimc);

		$sections = $this->Classections->find('all')->where(['Classections.class_id' => $classid, 'Classections.section_id' => $sectionid])->toArray();
		$this->set('classectionid', $email['id']);

		$timetable_data = $this->Timetables->find('all')->where(['Timetables.status' => '1'])->order(['Timetables.id' => 'ASC'])->toarray();
		//pr($timetable_data); die;
		$this->set('timetabledata', $timetable_data);
	}



}
