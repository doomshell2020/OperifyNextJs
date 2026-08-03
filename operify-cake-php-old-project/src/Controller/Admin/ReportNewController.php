<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\View\Helper;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class ReportNewController extends AppController
{
	//initialize component
	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Enquires');
		$this->loadModel('Classfee');
		$this->loadModel('Classes');
		$this->loadModel('Modes');
		$this->loadModel('Followup');
		$this->loadModel('Students');
		$this->loadModel('Sections');
		$this->loadModel('Employees');
		$this->loadModel('Departments');
		$this->loadModel('Designations');
		$this->loadModel('Studentfees');
		$this->loadModel('Classections');
		$this->loadModel('Cities');
		$this->loadModel('States');
		$this->loadModel('Country');
		$this->loadModel('Department');
		$this->loadModel('Designations');
		$this->loadModel('Transportfees');
		$this->loadModel('Locations');
		$this->loadModel('Transports');
		$this->loadModel('StudentTransfees');
		$this->loadModel('Hostels');
		$this->loadModel('StudentHostalfees');
		$this->loadModel('BookStatus');
		$this->loadModel('BookCategory');
		$this->loadModel('Studattends');
	}


	public function index($id = null)
	{
		$this->viewBuilder()->layout('admin');

		$classes = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Classes.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('classes'));
	}

	public function studentsubjectreport()
	{
		$this->loadModel('Classections');
		$this->loadModel('Sections');
		$this->viewBuilder()->layout('admin');
		$classes = $this->Classections->find('list', [
			'keyField' => 'Classes.id',
			'valueField' => 'Classes.title'
		])->contain(['Classes'])->where(['Classes.id IN' => ['12', '13', '15', '17', '20', '22', '26', '27']])->order(['Classes.id' => 'asc'])->toArray();
		$this->set('classes', $classes);

		$sectionslist = $this->Sections->find('list', [
			'keyField' => 'id',
			'valueField' => 'title'
		])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
		$this->set('sectionslist', $sectionslist);
	}

	public function stusubsearch()
	{

		$cid = $this->request->data['class_id'];
		$sid = $this->request->data['section_id'];
		$apk = array();
		if (!empty($cid)) {

			$stts = array('Students.class_id' => $cid);
			$apk[] = $stts;
		}

		if (!empty($sid)) {

			$stts = array('Students.section_id' => $sid);
			$apk[] = $stts;
		}

		$stts = array('Students.status' => 'Y');
		$apk[] = $stts;
		$this->request->session()->delete('condition');
		$this->request->session()->write('condition', $apk);

		$studetail = $this->Students->find('all')->select(['fname', 'middlename', 'lname', 'enroll', 'class_id', 'section_id', 'comp_sid', 'opt_sid'])->where($apk)->order(['Students.fname' => 'ASC'])->toArray();
		//pr($studetail); die;

		$this->set(compact('studetail'));
	}



	public function stusubsearchexcel()
	{
		$this->autoRender = false;
		$this->loadModel('Classections');
		$this->loadModel('Sections');
		$this->loadModel('Subjects');
		$cn = $this->request->session()->read('condition');
		$session = $this->request->session();

		if ($cn) {

			$studetail = $this->Students->find('all')->select(['fname', 'middlename', 'lname', 'enroll', 'class_id', 'section_id', 'comp_sid', 'opt_sid'])->where($cn)->order(['Students.fname' => 'ASC'])->toArray();
		}


		ini_set('max_execution_time', 1600);
		$headerRow = array("S.No.", "Student Name", "SR. No.", "Class", "Section", "Subject 1", "Subject 2", "Subject 3", "Subject 4", "Subject 5", "Subject 6");
		$output = implode("\t", $headerRow) . "\n";

		$cv = 1;
		foreach ($studetail as $people) {
			//pr($people); die;
			if ($people['comp_sid'] != "" && $people['opt_sid'] != "") {
				$com = explode(',', $people['comp_sid']);

				$opt = explode(',', $people['opt_sid']);
				$df = array_merge($com, $opt);
			} else {

				$df = "";
			}
			$asn = $people['asn_no'];
			$cls = $this->Classes->find('all')->select(['id', 'title'])->where(['Classes.id' => $people['class_id']])->first();
			$sls = $this->Sections->find('all')->select(['id', 'title'])->where(['Sections.id' => $people['section_id']])->first();


			$result = array();

			$str = "";



			$result[] = $cv;
			$result[] = ucwords($people['fname'] . ' ' . $people['middlename'] . ' ' . $people['lname']);
			$result[] = $people['enroll'];


			$result[] = $cls['title'];
			$result[] = $sls['title'];

			if (!empty($df)) {
				for ($i = 0; $i <= 5; $i++) {
					$sub = $this->Subjects->find('all')->select(['name', 'alias'])->where(['Subjects.id' => $df[$i]])->first();
					if (isset($sub['alias'])) {
						$result[] = $sub['name'];
					} else {
						$result[] = "N\A";
					}
				}
			} else {
				for ($i = 0; $i <= 5; $i++) {
					$result[] = "N\A";
				}
			}


			$output .=  implode("\t", $result) . "\n";
			$cv++;
		}


		//echo $output; die;
		$filename = "Studentsubject.xls";
		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
	}




	public function find_section($id = null)
	{

		$classid = $this->request->data['id'];
		$this->viewBuilder()->layout('admin');
		$sections = $this->Classections->find('list', [
			'keyField' => 'Sections.id',
			'valueField' => 'Sections.title'
		])->contain(['Sections'])->where(['Classections.class_id' => $classid])->order(['Sections.title' => 'ASC'])->toArray();



		echo "<option value=''>---Select Section---</option>";

		foreach ($sections as $sections => $value) {
			echo "<option value=" . $sections . ">" . $value . "</option>";
		}
		die;
	}


	// For Enquiry

	public function search()
	{

		$conn = ConnectionManager::get('default');
		$from = date('Y-m-d', strtotime($this->request->data['from']));
		$to = date('Y-m-d', strtotime($this->request->data['to'] . '+1 days'));
		$acedmicyear = $this->request->data['acedmicyear'];
		$class_id = $this->request->data['class_id'];
		$name = $this->request->data['name'];
		$status = $this->request->data['status'];
		$detail = "SELECT Enquires.id,Enquires.s_name,Enquires.mobile,Enquires.username,Enquires.next_followup_date,Enquires.created,Enquires.status,Enquires.enquiry,Enquires.mode_id,Enquires.acedmicyear,Enquires.class_id,Enquires.admissionyear, Modes.name as modename,  Classes.title as classtitle  FROM `enquires` Enquires LEFT JOIN classes Classes ON Enquires.`class_id` = Classes.id LEFT JOIN modes Modes ON Enquires.`mode_id` = Modes.id WHERE  1=1 ";
		$cond = ' ';
		$session = $this->request->session();
		$session->delete('from');
		$session->delete('to');

		if (!empty($from || $to)) {

			$session->write('from', $from);
			$session->write('to', $to);
			$cond .= " AND (Enquires.created   >='" . $from . "' AND Enquires.created  <='" . $to . "')";
		}

		$session->delete('status');
		if (!empty($status)) {

			$session->write('status', $status);
			$cond .= " AND Enquires.status LIKE '" . $status . "%' ";
		}

		$session->delete('acedmicyear');
		if (!empty($acedmicyear)) {

			$session->write('acedmicyear', $acedmicyear);
			$cond .= " AND Enquires.acedmicyear LIKE '" . $acedmicyear . "%' ";
		}

		$session->delete('class_id');
		if (!empty($class_id)) {

			$session->write('class_id', $class_id);
			$cond .= " AND Enquires.class_id LIKE '" . $class_id . "%' ";
		}

		$session->delete('name');
		if (!empty($name)) {

			$session->write('name', $name);
			$cond .= " AND Enquires.s_name  LIKE  '" . $name . "%' || Enquires.username  LIKE  '" . $name . "%' || Enquires.id  LIKE  '" . $name . "%' ";
		}

		$detail = $detail . $cond;
		$resultss = $conn->execute($detail)->fetchAll('assoc');
		$this->set('t_enquiry', $resultss);
	}


	// For Enquiry

	public function user_supportiv()
	{

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$detail = "SELECT Enquires.id,Enquires.s_name,Enquires.mobile,Enquires.username,Enquires.next_followup_date,Enquires.created,Enquires.status,Enquires.enquiry,Enquires.mode_id,Enquires.acedmicyear,Enquires.class_id,Enquires.admissionyear, Modes.name as modename,  Classes.title as classtitle  FROM `enquires` Enquires LEFT JOIN classes Classes ON Enquires.`class_id` = Classes.id LEFT JOIN modes Modes ON Enquires.`mode_id` = Modes.id WHERE  1=1 ";
		$cond = ' ';
		$session = $this->request->session();
		$from = $session->read('from');
		$to = $session->read('to');
		if (!empty($from || $to)) {

			$cond .= " AND (Enquires.created   >='" . $from . "' AND Enquires.created  <='" . $to . "')";
		}
		$status = $session->read('status');

		if (!empty($status)) {

			$cond .= " AND Enquires.status LIKE '" . $status . "%' ";
		}


		$acedmicyear = $session->read('acedmicyear');

		if (!empty($acedmicyear)) {

			$cond .= " AND Enquires.acedmicyear LIKE '" . $acedmicyear . "%' ";
		}


		$class_id = $session->read('class_id');
		if (!empty($class_id)) {

			$cond .= " AND Enquires.class_id LIKE '" . $class_id . "%' ";
		}

		$name = $session->read('name');
		if (!empty($name)) {

			$cond .= " AND Enquires.s_name  LIKE  '" . $name . "%' || Enquires.username  LIKE  '" . $name . "%' || Enquires.id  LIKE  '" . $name . "%' ";
		}

		$detail = $detail . $cond;
		$resultss = $conn->execute($detail)->fetchAll('assoc');

		//pr($resultss); die;

		$output = "";
		$output .= '"Enquiry Date",';
		$output .= '"Registration No",';
		$output .= '"Name",';
		$output .= '"Mobile",';
		$output .= '"Academic Year",';
		$output .= '"Class",';
		$output .= '"Email Id",';
		$output .= '"Last Follow-up Status",';
		$output .= '"Enquiry Status",';
		$output .= "\n";

		foreach ($resultss as $people) {
			$str = "";
			if ($people['status'] == 'Y') {
				$status = "Opened";
			} elseif ($people['status'] == '1') {
				$status = "Completed";
			} else {
				$status = "Closed";
			}


			$output .= date('d-m-Y', strtotime($people['created'])) . ",";
			$output .= $people["id"] . ",";
			$output .= $people["s_name"] . ",";
			$output .= $people["mobile"] . ",";
			$output .= $people["acedmicyear"] . ",";
			$output .= $people["classtitle"] . ",";
			$output .= $people["username"] . ",";
			$output .= $people["next_followup_date"] . ",";
			$output .= $status . ",";
			$output .= "\r\n";
		}

		//echo $output;	die;
		$filename = "Enquiry.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;

		die;
		$this->redirect($this->referer());
	}


	// Follow Up Index

	public function followup()
	{
		$this->viewBuilder()->layout('admin');
		$classes = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Classes.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('classes'));
	}

	// follow Up Search

	public function search2()
	{

		$conn = ConnectionManager::get('default');
		$from = date('Y-m-d', strtotime($this->request->data['from']));
		$response = date('Y-m-d', strtotime($this->request->data['response']));
		$acedmicyear = $this->request->data['acedmicyear'];
		$class_id = $this->request->data['class_id'];
		$name = $this->request->data['name'];
		$status = $this->request->data['status'];


		$detail = "SELECT temp.*,enquires.*FROM (SELECT  * FROM followup ORDER BY created DESC) as temp INNER JOIN enquires ON temp.enq_id = enquires.id WHERE 1=1  ";

		$cond = ' ';
		$session = $this->request->session();

		$session->delete('from');
		if (!empty($from) && $from != '1970-01-01') {

			$session->write('from', $from);

			$cond .= " AND temp.add_date LIKE '" . $from . "%' ";
		}

		$session->delete('response');
		if (!empty($response) && $response != '1970-01-01') {

			$session->write('response', $response);

			$cond .= " AND temp.f_date LIKE '" . $response . "%' ";
		}

		$session->delete('class_id');
		if (!empty($class_id)) {

			$session->write('class_id', $class_id);

			$cond .= " AND enquires.class_id LIKE '" . $class_id . "%' ";
		}

		$session->delete('name');
		if (!empty($name)) {

			$session->write('name', $name);

			$cond .= " AND enquires.s_name  LIKE  '" . $name . "%' || enquires.username  LIKE  '" . $name . "%' || enquires.id  LIKE  '" . $name . "%' ";
		}

		$session->delete('acedmicyear');

		if (!empty($acedmicyear)) {

			$session->write('acedmicyear', $acedmicyear);

			$cond .= " AND enquires.acedmicyear  LIKE  '" . $acedmicyear . "%'";
		}


		$session->delete('status');
		if (!empty($status)) {

			$session->write('status', $status);

			$cond .= " AND temp.active LIKE '" . $status . "%' ";
		}


		$detail = $detail . $cond;
		$SQL = $detail . "GROUP BY temp.enq_id";
		//echo $SQL; die;
		$results = $conn->execute($SQL)->fetchAll('assoc');

		$this->set('f_enquiry', $results);
	}


	// Follow Up

	public function user_supportiv2()
	{

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$detail = "SELECT temp.*,enquires.*FROM (SELECT  * FROM followup ORDER BY created DESC) as temp INNER JOIN enquires ON temp.enq_id = enquires.id WHERE 1=1  ";
		$cond = ' ';
		$session = $this->request->session();


		$from = $session->read('from');
		if (!empty($from) && $from != '1970-01-01') {

			$cond .= " AND temp.add_date LIKE '" . $from . "%' ";
		}

		$response = $session->read('response');

		if (!empty($response) && $response != '1970-01-01') {


			$cond .= " AND temp.f_date LIKE '" . $response . "%' ";
		}

		$class_id = $session->read('class_id');

		if (!empty($class_id)) {



			$cond .= " AND enquires.class_id LIKE '" . $class_id . "%' ";
		}

		$name = $session->read('name');

		if (!empty($name)) {

			$cond .= " AND enquires.s_name  LIKE  '" . $name . "%' || enquires.username  LIKE  '" . $name . "%' || enquires.id  LIKE  '" . $name . "%' ";
		}


		$acedmicyear = $session->read('acedmicyear');
		if (!empty($acedmicyear)) {



			$cond .= " AND enquires.acedmicyear  LIKE  '" . $acedmicyear . "%'";
		}

		$status = $session->read('status');

		if (!empty($status)) {

			$cond .= " AND temp.active LIKE '" . $status . "%' ";
		}


		$detail = $detail . $cond;
		$SQL = $detail . "GROUP BY temp.enq_id";
		//echo $SQL; die;
		$results = $conn->execute($SQL)->fetchAll('assoc');
		//	pr($results); die;

		$output = "";
		$output .= '"Next Follow-up Date",';
		$output .= '"Last Follow-up Date ",';
		$output .= '"Registration No",';
		$output .= '"Name",';
		$output .= '"Mobile",';
		$output .= '"Academic Year",';
		$output .= '"Class",';
		$output .= '"Email Id",';
		$output .= '"Status",';
		$output .= "\n";
		foreach ($results as $people) {
			$str = "";
			if ($people['active'] == 'Y') {
				$status = "Opened";
			} elseif ($people['active'] == '1') {
				$status = "Completed";
			} else {
				$status = "Closed";
			}
			if (!empty($people['class_id'])) {
				$class_name = $this->findclass($people['class_id']);
				$class = $class_name['title'];
			}
			$output .= date('d-m-Y', strtotime($people['f_date'])) . ",";
			$output .= date('d-m-Y', strtotime($people['add_date'])) . ",";
			$output .= $people["id"] . ",";
			$output .= $people["s_name"] . ",";
			$output .= $people["mobile"] . ",";
			$output .= $people["acedmicyear"] . ",";
			$output .= $class . ",";
			$output .= $people["username"] . ",";
			$output .= $status . ",";

			$output .= "\r\n";
		}

		//echo $output;	die;
		$filename = "Follow Up.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;

		die;
		$this->redirect($this->referer());
	}

	// For Students

	public function student()
	{
		$this->viewBuilder()->layout('admin');
		$classes = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Classes.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('classes'));
	}

	// For Students
	public function search3()
	{
		$session = $this->request->session();
		$conn = ConnectionManager::get('default');
		$acedmicyear = $this->request->data['acedmicyear'];
		$class_id = $this->request->data['class_id'];
		$name = $this->request->data['name'];
		$ids = $this->request->data['ids'];
		$personal = implode(",", $this->request->data['selectField']);
		$session->delete('personal');
		$array = $this->request->data['selectField'];
		$this->set('allarray', $array);
		$session->write('personal', $personal);
		$session->write('alldata', $this->request->data['selectField']);
		$detail = "SELECT " . `echo $personal` . "FROM `students` LEFT JOIN addresses  ON students.id = addresses.user_id LEFT JOIN guardians  ON students.id = guardians.user_id WHERE `students`.`status`='Y'";

		$cond = ' ';


		$session->delete('acedmicyear');

		if (!empty($acedmicyear)) {
			$session->write('acedmicyear', $acedmicyear);
			$cond .= " AND students.acedmicyear ='" . $acedmicyear . "' ";
		}

		$session->delete('class_id');
		if (!empty($class_id)) {
			$session->write('class_id', $class_id);
			$cond .= " AND students.class_id ='" . $class_id . "' ";
		}

		$session->delete('name');
		if (!empty($name)) {
			$session->write('name', $name);
			$cond .= " AND  students.fname  LIKE  '" . $name . "%' || students.lname  LIKE  '" . $name . "%' || students.username  LIKE '" . $name . "%'";
		}

		$session->delete('ids');
		if (!empty($ids)) {
			$session->write('ids', $ids);
			$cond .= " AND students.id ='" . $ids . "' ";
		}

		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY students.id DESC";
		$resul = $conn->execute($SQL)->fetchAll('assoc');
		//pr($resul); die;

		$this->set('s_enquiry', $resul);
	}

	public function user_supportiv3()
	{

		$this->autoRender = false;
		$session = $this->request->session();
		$conn = ConnectionManager::get('default');
		$personal = $session->read('personal');
		$s_data = $session->read('alldata');
		$detail = "SELECT " . `echo $personal` . "FROM `students` LEFT JOIN addresses  ON students.id = addresses.user_id LEFT JOIN guardians  ON students.id = guardians.user_id WHERE `students`.`status`='Y'";
		$cond = ' ';

		$acedmicyear = $session->read('acedmicyear');

		if (!empty($acedmicyear)) {

			$cond .= " AND students.acedmicyear ='" . $acedmicyear . "' ";
		}

		$class_id = $session->read('class_id');
		if (!empty($class_id)) {

			$cond .= " AND students.class_id ='" . $class_id . "' ";
		}

		$name = $session->read('name');
		if (!empty($name)) {

			$cond .= " AND students.fname ='" . $name . "' ";
		}

		$ids = $session->read('ids');
		if (!empty($ids)) {

			$cond .= " AND students.id ='" . $ids . "' ";
		}
		//pr($s_data); die;
		//  $acedmicyear=$session->read('acedmicyear');
		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY students.id DESC";
		$resul = $conn->execute($SQL)->fetchAll('assoc');
		//  pr($resul); die;
		if (in_array('students.id', $s_data)) {
			$output .= '"Student Id",';
		}
		if (in_array('fname', $s_data)) {
			$output .= '"FirstName",';
		}
		if (in_array('middlename', $s_data)) {
			$output .= '"Middle Name",';
		}
		if (in_array('lname', $s_data)) {
			$output .= '"Last Name",';
		}
		if (in_array('gender', $s_data)) {
			$output .= '"Gender",';
		}
		if (in_array('dob', $s_data)) {
			$output .= '"Dob",';
		}
		if (in_array('username', $s_data)) {
			$output .= '"Email",';
		}
		if (in_array('bloodgroup', $s_data)) {
			$output .= '"BloodGroup",';
		}
		if (in_array('mobile', $s_data)) {
			$output .= '"Mobile",';
		}
		if (in_array('students.created', $s_data)) {
			$output .= '"Admission Date",';
		}
		if (in_array('students.status', $s_data)) {
			$output .= '"Status",';
		}
		if (in_array('cast', $s_data)) {
			$output .= '"Cast",';
		}
		if (in_array('religion', $s_data)) {
			$output .= '"Religion",';
		}
		if (in_array('admissionyear', $s_data)) {
			$output .= '"Admission Year",';
		}
		if (in_array('acedmicyear', $s_data)) {
			$output .= '"Academic Year",';
		}
		if (in_array('class_id', $s_data)) {
			$output .= '"Class",';
		}
		if (in_array('section_id', $s_data)) {
			$output .= '"Section",';
		}
		if (in_array('c_address', $s_data)) {
			$output .= '"CurrentAddress",';
		}
		if (in_array('c_city_id', $s_data)) {
			$output .= '"CurrentCity",';
		}
		if (in_array('c_s_id', $s_data)) {
			$output .= '"CurrentState",';
		}
		if (in_array('c_c_id', $s_data)) {
			$output .= '"CurrentCountry",';
		}
		if (in_array('c_pincode', $s_data)) {
			$output .= '"PinCode",';
		}
		if (in_array('p_address', $s_data)) {
			$output .= '"PermanantAddress",';
		}
		if (in_array('p_city_id', $s_data)) {
			$output .= '"PermanantCity",';
		}
		if (in_array('p_s_id', $s_data)) {
			$output .= '"PermanantState",';
		}
		if (in_array('p_c_id', $s_data)) {
			$output .= '"PermanantCountry",';
		}
		if (in_array('p_pincode', $s_data)) {
			$output .= '"PinCode",';
		}
		if (in_array('fullname', $s_data)) {
			$output .= '"Guardians Name",';
		}
		if (in_array('relation', $s_data)) {
			$output .= '"Relation",';
		}
		if (in_array('guardians.qualification', $s_data)) {
			$output .= '"Qualification",';
		}
		if (in_array('occupation', $s_data)) {
			$output .= '"Occupation",';
		}
		if (in_array('mobileno', $s_data)) {
			$output .= '"Mobile No",';
		}
		if (in_array('guardians.emails', $s_data)) {
			$output .= '"Guardians.Email",';
		}
		$output .= "\n";
		foreach ($resul as $people) {

			$classewrw = $this->findclassess($people['class_id']);
			$cls = $classewrw[0]['title'];

			$sect = $this->sections($people['section_id']);
			$sections = $sect[0]['title'];

			$citie = $this->cities($people['c_city_id']);
			$city = $citie[0]['name'];

			$state = $this->states($people['c_s_id']);
			$statess = $state[0]['name'];

			$country = $this->countries($people['c_c_id']);
			$countryname = $country[0]['name'];

			$p_citie = $this->cities($people['p_city_id']);
			$P_city = $p_citie[0]['name'];

			$p_state = $this->states($people['p_s_id']);
			$p_statess = $p_state[0]['name'];

			$p_country = $this->countries($people['p_c_id']);
			$p_countryname = $p_country[0]['name'];

			$dt = date('d-m-Y', strtotime($people['created']));
			if (!empty($dt) && ($dt != '01-01-1970')) {
				$admission_date = $dt;
			}

			if (!empty($people['status'])) {
				if ($people['status'] == 'Y') {
					$act = "Active";
				} else {
					$act = "Inactive";
				}
			}
			if (in_array('students.id', $s_data)) {
				$output .= $people["id"] . ",";
			}
			if (in_array('fname', $s_data)) {
				$output .= $people["fname"] . ",";
			}
			if (in_array('middlename', $s_data)) {
				$output .= $people["middlename"] . ",";
			}
			if (in_array('lname', $s_data)) {
				$output .= $people["lname"] . ",";
			}
			if (in_array('gender', $s_data)) {
				$output .= $people["gender"] . ",";
			}
			if (in_array('dob', $s_data)) {
				$output .= $people["dob"] . ",";
			}
			if (in_array('username', $s_data)) {
				$output .= $people["username"] . ",";
			}
			if (in_array('bloodgroup', $s_data)) {
				$output .= $people["bloodgroup"] . ",";
			}
			if (in_array('mobile', $s_data)) {
				$output .= $people["mobile"] . ",";
			}

			if (in_array('students.created', $s_data)) {
				$output .= $admission_date . ",";
			}
			if (in_array('students.status', $s_data)) {
				$output .= $act . ",";
			}
			if (in_array('cast', $s_data)) {
				$output .= $people["cast"] . ",";
			}
			if (in_array('religion', $s_data)) {
				$output .= $people["religion"] . ",";
			}
			if (in_array('admissionyear', $s_data)) {
				$output .= $people["admissionyear"] . ",";
			}
			if (in_array('acedmicyear', $s_data)) {
				$output .= $people["acedmicyear"] . ",";
			}
			if (in_array('class_id', $s_data)) {
				$output .= $cls . ",";
			}
			if (in_array('section_id', $s_data)) {
				$output .= $sections . ",";
			}
			if (in_array('c_address', $s_data)) {
				$output .= str_replace(" ", "", $people["c_address"]) . ",";
			}
			if (in_array('c_city_id', $s_data)) {
				$output .= $city . ",";
			}
			if (in_array('c_s_id', $s_data)) {
				$output .= $statess . ",";
			}
			if (in_array('c_c_id', $s_data)) {
				$output .= $countryname . ",";
			}
			if (in_array('c_pincode', $s_data)) {
				$output .= $people["c_pincode"] . ",";
			}
			if (in_array('p_address', $s_data)) {
				$output .= $people["p_address"] . ",";
			}
			if (in_array('p_city_id', $s_data)) {
				$output .= $P_city . ",";
			}
			if (in_array('p_s_id', $s_data)) {
				$output .= $p_statess . ",";
			}
			if (in_array('p_c_id', $s_data)) {
				$output .= $p_countryname . ",";
			}
			if (in_array('p_pincode', $s_data)) {
				$output .= $people["p_pincode"] . ",";
			}
			if (in_array('fullname', $s_data)) {
				$output .= $people["fullname"] . ",";
			}
			if (in_array('relation', $s_data)) {
				$output .= $people["relation"] . ",";
			}
			if (in_array('guardians.qualification', $s_data)) {
				$output .= $people["qualification"] . ",";
			}
			if (in_array('occupation', $s_data)) {
				$output .= $people["occupation"] . ",";
			}
			if (in_array('mobileno', $s_data)) {
				$output .= $people["mobileno"] . ",";
			}
			if (in_array('guardians.emails', $s_data)) {
				$output .= $people["emails"] . ",";
			}
			$output .= "\r\n";
		}
		$filename = "Student-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;

		die;
		$this->redirect($this->referer());
	}

	public function employee()
	{
		$this->viewBuilder()->layout('admin');
		$department_name = $this->Departments->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Departments.status' => 'Y'])->order(['Departments.name' => 'ASC'])->toArray();
		$this->set(compact('department_name'));
		$Designations = $this->Designations->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Designations.status' => 'Y'])->order(['Designations.name' => 'ASC'])->toArray();
		$this->set(compact('Designations'));
	}

	public function search4()
	{
		$session = $this->request->session();
		$conn = ConnectionManager::get('default');
		$department_id = $this->request->data['department_id'];
		$desination_id = $this->request->data['desination_id'];
		$gender = $this->request->data['gender'];
		$personals = implode(",", $this->request->data['selectField']);
		$session->delete('personals');
		$this->set('personalsd', $this->request->data['selectField']);
		$session->write('personals', $personals);
		$session->write('edata', $this->request->data['selectField']);
		$detail = "SELECT " . `echo $personals` . "FROM `employees` LEFT JOIN otherinfos  ON employees.id = otherinfos.user_id LEFT JOIN addresses  ON employees.id = addresses.user_id LEFT JOIN guardians  ON employees.id = guardians.user_id WHERE 1";

		$cond = ' ';


		$session->delete('department_id');
		if (!empty($department_id)) {
			$session->write('department_id', $department_id);
			$cond .= " AND employees.department_id ='" . $department_id . "' ";
		}

		$session->delete('desination_id');
		if (!empty($desination_id)) {
			$session->write('desination_id', $desination_id);
			$cond .= " AND employees.designation_id ='" . $desination_id . "' ";
		}

		$session->delete('gender');
		if (!empty($gender)) {
			$session->write('gender', $gender);
			$cond .= " AND employees.gender ='" . $gender . "' ";
		}

		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY employees.id DESC";
		//	pr($SQL); die;
		$resul = $conn->execute($SQL)->fetchAll('assoc');

		//	pr($resul); die;
		$this->set('employee', $resul);
	}

	public function user_supportiv4()
	{
		$this->autoRender = false;
		$session = $this->request->session();
		$conn = ConnectionManager::get('default');
		$personals = $session->read('personals');
		$e_complete = $session->read('edata');
		$detail = "SELECT " . `echo $personals` . "FROM `employees` LEFT JOIN otherinfos  ON employees.id = otherinfos.user_id LEFT JOIN addresses  ON employees.id = addresses.user_id LEFT JOIN guardians  ON employees.id = guardians.user_id WHERE 1";

		$cond = ' ';
		$department_id = $session->read('department_id');
		if (!empty($department_id)) {

			$cond .= " AND employees.department_id ='" . $department_id . "' ";
		}
		$desination_id = $session->read('desination_id');
		if (!empty($desination_id)) {

			$cond .= " AND employees.designation_id ='" . $desination_id . "' ";
		}

		$gender = $session->read('gender');
		if (!empty($gender)) {
			$cond .= " AND employees.gender ='" . $gender . "' ";
		}


		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY employees.id DESC";
		$result = $conn->execute($SQL)->fetchAll('assoc');

		//pr($e_complete); die;

		$output = "";
		if (in_array('employees.id', $e_complete)) {
			$output .= '"Employee Id",';
		}
		if (in_array('fname', $e_complete)) {
			$output .= '"Fname",';
		}
		if (in_array('middlename', $e_complete)) {
			$output .= '"Middle Name",';
		}
		if (in_array('lname', $e_complete)) {

			$output .= '"Last Name",';
		}
		if (in_array('employees.email', $e_complete)) {

			$output .= '"Email",';
		}
		if (in_array('martial_status', $e_complete)) {

			$output .= '"Martial Status",';
		}
		if (in_array('gender', $e_complete)) {
			$output .= '"Gender",';
		}

		if (in_array('dob', $e_complete)) {
			$output .= '"Dob",';
		}

		if (in_array('mobile', $e_complete)) {
			$output .= '"Mobile",';
		}
		if (in_array('hobbies', $e_complete)) {
			$output .= '"Hobbies",';
		}

		if (in_array('aadharno', $e_complete)) {
			$output .= '"Attendance Card ID",';
		}
		if (in_array('department_id', $e_complete)) {
			$output .= '"Department",';
		}
		if (in_array('designation_id', $e_complete)) {
			$output .= '"Designation",';
		}
		if (in_array('nationality', $e_complete)) {
			$output .= '"Nationality",';
		}

		if (in_array('joiningdate', $e_complete)) {
			$output .= '"Joining Date",';
		}
		if (in_array('otherinfos.qualifications', $e_complete)) {
			$output .= '"Qualifications",';
		}
		if (in_array('specialization', $e_complete)) {
			$output .= '"Specialization",';
		}
		if (in_array('reference', $e_complete)) {
			$output .= '"Reference",';
		}
		if (in_array('accountno', $e_complete)) {
			$output .= '"Bank Account No",';
		}

		if (in_array('c_address', $e_complete)) {
			$output .= '"Current Address",';
		}
		if (in_array('c_city_id', $e_complete)) {
			$output .= '"Current City",';
		}

		if (in_array('c_s_id', $e_complete)) {
			$output .= '"Current State",';
		}
		if (in_array('c_c_id', $e_complete)) {
			$output .= '"Current Country",';
		}
		if (in_array('c_pincode', $e_complete)) {
			$output .= '"Pincode",';
		}

		if (in_array('p_address', $e_complete)) {
			$output .= '"Permanant Address",';
		}

		if (in_array('p_city_id', $e_complete)) {
			$output .= '"Permanant City",';
		}

		if (in_array('p_s_id', $e_complete)) {
			$output .= '"Permanant State",';
		}

		if (in_array('p_c_id', $e_complete)) {
			$output .= '"Permanant Country",';
		}
		if (in_array('p_pincode', $e_complete)) {
			$output .= '"Pincode",';
		}

		if (in_array('fullname', $e_complete)) {
			$output .= '"Fullname",';
		}
		if (in_array('relation', $e_complete)) {
			$output .= '"Relation",';
		}
		if (in_array('guardians.qualification', $e_complete)) {
			$output .= '"G.Qualification",';
		}

		if (in_array('occupation', $e_complete)) {
			$output .= '"Occupation",';
		}

		if (in_array('mobileno', $e_complete)) {
			$output .= '"G.Mobile No",';
		}

		if (in_array('guardians.emails', $e_complete)) {
			$output .= '"G.Email",';
		}

		$output .= "\n";
		foreach ($result as $value) {

			$department_id = $this->finddepartment($value['department_id']);
			$department = $department_id[0]['name'];

			$designation_id = $this->finddesignation($value['designation_id']);
			$designation = $designation_id[0]['name'];


			$citie = $this->cities($value['c_city_id']);
			$city = $citie[0]['name'];

			$state = $this->states($value['c_s_id']);
			$statess = $state[0]['name'];

			$country = $this->countries($value['c_c_id']);
			$countryname = $country[0]['name'];

			$p_citie = $this->cities($value['p_city_id']);
			$P_city = $p_citie[0]['name'];

			$p_state = $this->states($value['p_s_id']);
			$p_statess = $p_state[0]['name'];

			$p_country = $this->countries($value['p_c_id']);
			$p_countryname = $p_country[0]['name'];

			if (in_array('employees.id', $e_complete)) {
				$output .= $value["id"] . ",";
			}
			if (in_array('fname', $e_complete)) {
				$output .= $value["fname"] . ",";
			}

			if (in_array('middlename', $e_complete)) {
				$output .= $value["middlename"] . ",";
			}
			if (in_array('lname', $e_complete)) {
				$output .= $value["lname"] . ",";
			}
			if (in_array('employees.email', $e_complete)) {
				$output .= $value["email"] . ",";
			}

			if (in_array('martial_status', $e_complete)) {
				$output .= $value["martial_status"] . ",";
			}

			if (in_array('gender', $e_complete)) {
				$output .= $value["gender"] . ",";
			}
			if (in_array('dob', $e_complete)) {
				$output .= $value["dob"] . ",";
			}
			if (in_array('mobile', $e_complete)) {
				$output .= $value["mobile"] . ",";
			}

			if (in_array('hobbies', $e_complete)) {
				$output .= str_replace(",", "-", $value["hobbies"]) . ",";
			}
			if (in_array('aadharno', $e_complete)) {
				$output .= $value["aadharno"] . ",";
			}
			if (in_array('department_id', $e_complete)) {
				$output .= str_replace(" ", "", $department) . ",";
			}
			if (in_array('designation_id', $e_complete)) {
				$output .= str_replace(" ", "", $designation) . ",";
			}
			if (in_array('nationality', $e_complete)) {
				$output .= $value["nationality"] . ",";
			}
			if (in_array('joiningdate', $e_complete)) {
				$output .= $value["joiningdate"] . ",";
			}
			if (in_array('otherinfos.qualifications', $e_complete)) {
				$output .= $value["qualifications"] . ",";
			}

			if (in_array('specialization', $e_complete)) {
				$output .= $value["specialization"] . ",";
			}
			if (in_array('reference', $e_complete)) {
				$output .= str_replace(" ", "-", $value["reference"]) . ",";
			}

			if (in_array('accountno', $e_complete)) {
				$output .= $value["accountno"] . ",";
			}

			if (in_array('c_address', $e_complete)) {
				$output .= $value["c_address"] . ",";
			}

			if (in_array('c_city_id', $e_complete)) {
				$output .= $city . ",";
			}

			if (in_array('c_s_id', $e_complete)) {
				$output .= $statess . ",";
			}
			if (in_array('c_c_id', $e_complete)) {
				$output .= $countryname . ",";
			}

			if (in_array('c_pincode', $e_complete)) {
				$output .= $value["c_pincode"] . ",";
			}

			if (in_array('p_address', $e_complete)) {
				$output .= $value["p_address"] . ",";
			}

			if (in_array('p_city_id', $e_complete)) {
				$output .= $P_city . ",";
			}

			if (in_array('p_s_id', $e_complete)) {
				$output .= $p_statess . ",";
			}

			if (in_array('p_c_id', $e_complete)) {
				$output .= $p_countryname . ",";
			}
			if (in_array('p_pincode', $e_complete)) {
				$output .= $value["p_pincode"] . ",";
			}
			if (in_array('fullname', $e_complete)) {
				$output .= $value["fullname"] . ",";
			}

			if (in_array('relation', $e_complete)) {
				$output .= $value["relation"] . ",";
			}

			if (in_array('guardians.qualification', $e_complete)) {
				$output .= $value["qualification"] . ",";
			}

			if (in_array('occupation', $e_complete)) {
				$output .= $value["occupation"] . ",";
			}

			if (in_array('mobileno', $e_complete)) {
				$output .= $value["mobileno"] . ",";
			}

			if (in_array('guardians.emails', $e_complete)) {
				$output .= $value["emails"] . ",";
			}
			$output .= "\r\n";
		}
		$filename = "Employee-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
		$this->redirect($this->referer());
	}


	public function fees()
	{
		$this->viewBuilder()->layout('admin');
		$classes = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Classes.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('classes'));
	}

	public function search5()
	{
		$s_id = $this->request->data['class_id'];
		$acedmicyear = $this->request->data['acedmicyear'];
		$Classections = $this->Classections->find('list', ['keyField' => 'id', 'valueField' => 'section_id'])->where(['class_id' => $s_id])->toArray();
		$this->set(compact('Classections'));
		$this->set(compact('s_id'));
		$this->set(compact('acedmicyear'));
	}

	public function students_all($class_id, $section_id, $academicyear, $fees)
	{
		$this->viewBuilder()->layout('admin');
		$this->set(compact('class_id'));
		$this->set(compact('section_id'));
		$this->set(compact('academicyear'));
		$this->set(compact('fees'));

		$student = $this->Students->find('all')->where(['Students.acedmicyear' => $academicyear, 'Students.status' => 'Y', 'Students.class_id' => $class_id, 'Students.section_id' => $section_id])->toArray();
		$this->set(compact('student'));
	}

	public function findamountmonth($id, $a_year)
	{
		// pr ($rout); die;
		// Use the HTML helper to output
		// Formatted data:
		$articles = TableRegistry::get('Classfee');
		$m = date('Y-m-d');
		// Start a new query.

		return	 $articles->find('all')->contain(['Classes'])->select(['qu4_fees' =>  $articles->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'Classfee.qu4_date <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
	}
	public function findamount3month($id, $a_year)
	{
		// pr ($rout); die;
		// Use the HTML helper to output
		// Formatted data:
		$articles = TableRegistry::get('Classfee');
		$m = date('Y-m-d');
		// Start a new query.

		return	 $articles->find('all')->contain(['Classes'])->select(['qu3_fees' =>  $articles->find()->func()->sum('Classfee.qu3_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'Classfee.qu3_date <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
	}
	public function findamount2month($id, $a_year)
	{
		// pr ($rout); die;
		// Use the HTML helper to output
		// Formatted data:
		$articles = TableRegistry::get('Classfee');
		$m = date('Y-m-d');
		// Start a new query.

		return	 $articles->find('all')->contain(['Classes'])->select(['qu2_fees' =>  $articles->find()->func()->sum('Classfee.qu2_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'Classfee.qu2_date <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
	}


	public function findamount1month($id, $a_year)
	{
		// pr ($rout); die;
		// Use the HTML helper to output
		// Formatted data:
		$articles = TableRegistry::get('Classfee');
		$m = date('Y-m-d');
		// Start a new query.

		return	 $articles->find('all')->contain(['Classes'])->select(['qu1_fees' =>  $articles->find()->func()->sum('Classfee.qu1_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'Classfee.qu1_date <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
	}


	public function user_supportiv5()
	{
		$this->autoRender = false;
		$session = $this->request->session();
		$Classections = $session->read('Classections');
		$class_id = $session->read('s_id');
		$acedmicyear = $session->read('acedmicyear');
		// pr($Classections); die;
		$output = "";

		if ($Classections) {
			$output .= '"Section",';
		}
		if ($Classections) {
			$output .= '"Amount to be paid",';
		}
		if ($Classections) {
			$output .= '"Paid Amount",';
		}
		if ($Classections) {
			$output .= '"Discount",';
		}
		if ($Classections) {
			$output .= '"Due Amount",';
		}
		$output .= "\n";
		$total = 0;
		$totalfee = 0;
		$discount = 0;
		$out = 0;
		foreach ($Classections as $service) {
			$sec_name = $this->sections($service);
			$output .= $sec_name[0]["title"] . ",";

			$totalstudentcount = $this->findstudentcount($class_id, $acedmicyear, $sec_name[0]["id"]);
			$amount = $this->findamount($class_id, $acedmicyear);
			$output .= (($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * ($totalstudentcount)) . ",";
			$paidamount = $this->findpaidamount($acedmicyear);

			$total += (($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * $totalstudentcount);

			$total1 = 0;
			$total2 = 0;
			foreach ($paidamount as $key => $value) {
				if ($value['student']['class_id'] == $class_id && $value['student']['section_id'] == $sec_name[0]["id"] && $value['student']['acedmicyear'] == $acedmicyear) {
					$total1 += $value['fee'];
					$totalfee += $value['fee'];
				}
			}
			$output .= $total1 . ",";

			foreach ($paidamount as $key => $value) {
				if ($value['student']['class_id'] == $class_id && $value['student']['section_id'] == $sec_name[0]["id"] && $value['student']['acedmicyear'] == $acedmicyear) {
					$total2 += $value['amount'] - $value['fee'];
					$discount += $value['amount'] - $value['fee'];
				}
			}
			$output .= $total2 . ",";
			$output .= "-" . ((($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * $totalstudentcount) - ($total1));

			$out += (($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * $totalstudentcount) - ($total1);


			$output .= "\r\n";
		}
		$output .= '"GRAND TOTAL",';
		$output .= $total . ",";
		$output .= $totalfee . ",";
		$output .= $discount . ",";
		$output .= "-" . $out . ",";
		$filename = "Fee-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
		$this->redirect($this->referer());
	}

	public function user_supportiv6()
	{
		$this->autoRender = false;
		$session = $this->request->session();
		$student = $session->read('student');
		// pr($student);
		$class_id = $session->read('class_id');
		// echo($class_id);
		$section_id = $session->read('section_id');
		//	 echo($section_id);
		$fees = $session->read('fees');
		// echo($fees);
		$academicyear = $session->read('academicyear');
		//    echo($academicyear);

		$output = "";

		if ($class_id) {
			$output .= '"Student Name",';
		}
		if ($class_id) {
			$output .= '"Amount to be paid",';
		}
		if ($class_id) {
			$output .= '"Due Amount",';
		}
		if ($class_id) {
			$output .= '"Paid Amount",';
		}
		if ($class_id) {
			$output .= '"Discount",';
		}
		if ($class_id) {
			$output .= '"Due Amount",';
		}
		$output .= "\n";
		$feestotal = 0;
		$perticular_feestotal = 0;
		$perticular_feestotal1 = 0;
		$out = 0;
		$dueamt = 0;
		$total_dues_amount = 0;
		foreach ($student as $key => $value) {
			$output .= $value['fname'] . "-" . $value['middlename'] . "-" . $value['lname'] . ",";
			$output .= $fees . ",";

			$findamountmonth = $this->findamountmonth($class_id, $academicyear); // pr($findamountmonth);
			$findamount3month = $this->findamount3month($class_id, $academicyear);
			$findamount2month = $this->findamount2month($class_id, $academicyear);
			$findamount1month = $this->findamount1month($class_id, $academicyear);
			$findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];
			$perticularamounts = $this->findperticularamount($value['id'], $academicyear);
			$paidfeestotal = 0;
			foreach ($perticularamounts as $values) {

				$paidfeestotal += $values['fee'];
			}
			if ($findsum > $paidfeestotal) {

				$dueamt = $findsum - $paidfeestotal;
				$total_dues_amount += $dueamt;
				$output .= $dueamt . ",";
			} else {

				$output .= "-,";
			}



			$feestotal += $fees;
			$perticularamounts = $this->findperticularamount($value['id'], $academicyear);
			$paidfeestotal = 0;
			foreach ($perticularamounts as $values) {
				$paidfeestotal += $values['fee'];
				$perticular_feestotal += $values['fee'];;
			}
			$output .= $paidfeestotal . ",";




			$dis = 0;
			foreach ($perticularamounts as $values) {

				$dis = $values['amount'] - $values['fee'];
				$perticular_feestotal1 += $dis;
			}
			$output .= $dis . ",";
			$output .= "-" . ($fees - $paidfeestotal);
			$out += ($fees - $paidfeestotal);
			$output .= "\r\n";
		}
		$output .= '"GRAND TOTAL",';
		$output .= $feestotal . ",";
		$output .= $total_dues_amount . ",";
		$output .= $perticular_feestotal . ",";
		$output .= $perticular_feestotal1 . ",";
		$output .= "-" . $out . ",";

		//die;
		$filename = "Fee-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
		$this->redirect($this->referer());
	}

	public function transport($id = null)
	{
		$this->viewBuilder()->layout('admin');
		$location = $this->Transportfees->find('all')->select(['loc_id'])->where(['Transportfees.status' => 'Y'])->toArray();
		$this->set(compact('location'));
	}

	public function search7()
	{
		$id = $this->request->data['transport_id'];
		$location = $this->Locations->find('all')->where(['Locations.id' => $id])->first()->toArray();
		$this->set(compact('location'));
		$students = $this->Students->find('all')->where(['Students.transportloc_id' => $id, 'Students.is_transport' => '1', 'Students.status' => 'Y'])->toArray();
		$this->set(compact('students'));
	}
	public function user_supportiv7()
	{
		$this->autoRender = false;
		$session = $this->request->session();
		$students = $session->read('students');
		$location = $session->read('location');
		$output .= '"Student Name",';
		$output .= '"Location",';
		$output .= '"Bus No",';
		$output .= '"Driver Name",';
		$output .= '"Driver Mobile No",';
		$output .= '"Total Amount",';
		$output .= '"Paid Amount",';
		$output .= "\n";
		$totaltransportamount = 0;
		$total_transport_paidamount = 0;
		foreach ($students as $service) {
			$output .= $service['fname'] . "-" . $service['middlename'] . "-" . $service['lname'] . ",";
			$output .= str_replace(" ", "-", $location['name']) . ",";
			$route = $this->findroute($location['id']);
			$output .= $route[0]['vechical_no'] . ",";
			$output .= str_replace(" ", "-", $route[0]['driver_name']) . ",";
			$output .= $route[0]['driver_mobile'] . ",";
			$amount = $this->findtransportamount($location['id'], $service['acedmicyear']);
			$output .= $amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees'] . ",";
			$totaltransportamount += $amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees'];
			$paidamount = $this->findpaidtransportamount($service['id'], $service['acedmicyear']);
			$total1 = 0;
			foreach ($paidamount as $key => $value) {

				$total1 += $value['fee'];
				$total_transport_paidamount += $value['fee'];
			}
			$output .= $total1 . ",";
			$output .= "\r\n";
		}
		$output .= ",";
		$output .= ",";
		$output .= ",";
		$output .= ",";
		$output .= '"Total Amount",';
		$output .= $totaltransportamount . ",";
		$output .= $total_transport_paidamount . ",";



		$filename = "Transport-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
		$this->redirect($this->referer());
	}

	public function hostelfee($id = null)
	{
		$this->viewBuilder()->layout('admin');
		$classes = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Classes.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('classes'));

		$sections = $this->Sections->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where(['Sections.status' => 1])->order(['title' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('sections'));

		$hostel = $this->Hostels->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Hostels.status' => 'Y'])->order(['name' => 'ASC'])->toArray();
		//	pr($classes); die;
		$this->set(compact('hostel'));
	}


	public function search8()
	{
		$conn = ConnectionManager::get('default');
		$h_id = $this->request->data['h_id'];
		$acedmicyear = $this->request->data['acedmicyear'];
		$class_id = $this->request->data['class_id'];
		$section_id = $this->request->data['section_id'];
		$name = $this->request->data['name'];
		$ids = $this->request->data['ids'];
		$detail = "SELECT Students.id,Students.fname,Students.mobile,Students.acedmicyear,Students.room_no,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE Students.`status` = 'Y' AND 1=1 ";
		$cond = ' ';
		if (!empty($h_id)) {

			$cond .= " AND Students.h_id =" . $h_id;
		}

		if (!empty($acedmicyear)) {

			$cond .= " AND Students.acedmicyear LIKE '" . $acedmicyear . "%' ";
		}
		if (!empty($class_id)) {

			$cond .= " AND Students.class_id LIKE '" . $class_id . "%' ";
		}

		if (!empty($section_id)) {

			$cond .= " AND Students.section_id LIKE '" . $section_id . "%' ";
		}



		if (!empty($name)) {

			$cond .= " AND Students.fname LIKE '" . $name . "%' ";
		}

		if (!empty($ids)) {

			$cond .= " AND Students.id LIKE '" . $ids . "%' ";
		}
		$cond .= " AND Students.is_hostel=" . '1';
		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY Students.id DESC";

		$results = $conn->execute($SQL)->fetchAll('assoc');
		$this->set('hostel', $results);
	}

	public function user_supportiv8()
	{
		$this->autoRender = false;
		$session = $this->request->session();
		$hostel = $session->read('hostel');
		$output .= '"Student Id",';
		$output .= '"Student Name",';
		$output .= '"Class",';
		$output .= '"Section",';
		$output .= '"Room",';
		$output .= '"Total Amount",';
		$output .= '"Discount",';
		$output .= '"Paid Amount",';
		$output .= "\n";
		$totalhostelamount = 0;
		$total_hostel_paidamount = 0;
		$discount = 0;
		foreach ($hostel as $service) {
			$output .= $service['id'] . ",";
			$output .= $service['fname'] . ",";
			$output .= $service['classtitle'] . ",";
			$output .= $service['sectiontitle'] . ",";
			$output .= "Room-" . $service['room_no'] . ",";
			$amount = $this->findhostelamount($service['id'], $service['acedmicyear'], $service['h_id']);
			$output .= $amount[0]['amount'] . ",";
			$output .= $amount[0]['amount'] - $amount[0]['fee'] . ",";
			$output .= $amount[0]['fee'] . ",";
			$totalhostelamount = $amount[0]['amount'];
			$total_hostel_paidamount = $amount[0]['fee'];
			$discount = $amount[0]['amount'] - $amount[0]['fee'];
			$output .= "\r\n";
		}
		$output .= ",";
		$output .= ",";
		$output .= ",";
		$output .= ",";
		$output .= '"Total Amount",';
		$output .= $totalhostelamount . ",";
		$output .= $discount . ",";
		$output .= $total_hostel_paidamount . ",";



		$filename = "Transport-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $output;
		die;
		$this->redirect($this->referer());
	}


	public function leave($id = null)
	{
		$this->viewBuilder()->layout('admin');
		$employee = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['fname' => 'ASC'])->toarray();
		$this->set('employee', $employee);
	}

	public function search9()
	{

		$conn = ConnectionManager::get('default');
		$emp_id = $this->request->data['emp_id'];

		//$detail="SELECT * FROM `leaves` Leaves WHERE 1=1  LEFT JOIN employees Employees ON Leaves.`emp_id` = Employees.id";
		$detail = "SELECT Leaves.date_from,Leaves.date_to,Leaves.t_days,Leaves.narration,Employees.id,Employees.fname,Employees.mobile,Employees.department_id,Employees.designation_id FROM `leaves` Leaves LEFT JOIN employees Employees ON Leaves.`emp_id` = Employees.id WHERE 1=1";
		$cond = ' ';
		if (!empty($emp_id)) {

			$cond .= " AND Leaves.emp_id =" . $emp_id;
		}
		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY Leaves.id DESC";
		$leav = $conn->execute($SQL)->fetchAll('assoc');
		$this->set('leav', $leav);
	}

	public function findclassess($id = null)
	{
		$clsasses = $this->Classes->find('all')->where(['Classes.id' => $id])->toArray();
		return $clsasses;
	}
	public function findclass($id = null)
	{
		$clsasses = $this->Classes->find('all')->where(['Classes.id' => $id])->first()->toArray();
		return $clsasses;
	}

	public function sections($id = null)
	{
		$clsasses = $this->Sections->find('all')->where(['Sections.id' => $id])->toArray();
		return $clsasses;
	}
	public function cities($id = null)
	{
		$clsasses = $this->Cities->find('all')->where(['Cities.id' => $id])->toArray();
		return $clsasses;
	}

	public function states($id = null)
	{
		$clsasses = $this->States->find('all')->where(['States.id' => $id])->toArray();
		return $clsasses;
	}
	public function countries($id = null)
	{
		$clsasses = $this->Country->find('all')->where(['Country.id' => $id])->toArray();
		return $clsasses;
	}
	public function finddepartment($id)
	{

		return	$this->Departments->find('all')->where(['Departments.id' => $id])->toArray();
	}
	public function finddesignation($id)
	{

		return	$this->Designations->find('all')->where(['Designations.id' => $id])->toArray();
	}

	public function findamount($id, $a_year)
	{

		return	$this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->select([
			'qu1_fees' =>  $this->Classfee->find()->func()->sum('Classfee.qu1_fees'),
			'qu2_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu2_fees'),
			'qu3_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu3_fees'),
			'qu4_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu4_fees'),
			'Classes.title',
			'Classfee.academic_year',
			'Classfee.id',
			'Classfee.status',
			'Classfee.class_id'
		])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year])->order(['Classfee.id' => 'ASC'])->toArray();
	}


	public function findstudentcount($id, $a_year, $section_id)
	{
		return	 $this->Students->find('all')->where(['Students.acedmicyear' => $a_year, 'Students.status' => 'Y', 'Students.class_id' => $id, 'Students.section_id' => $section_id])->count();
	}

	public function findpaidamount($a_year)
	{
		return	 $this->Studentfees->find('all')->contain(['Students'])->where(['Studentfees.acedmicyear' => $a_year, 'Studentfees.acedmicyear' => $a_year])->order(['Studentfees.id' => 'ASC'])->toArray();
	}

	public function findperticularamount($id, $a_year)
	{

		return $this->Studentfees->find('all')->select(['id', 'fee', 'discount', 'amount'])->where(['Studentfees.student_id' => $id, 'Studentfees.acedmicyear' => $a_year])->toArray();
	}

	public function findroute($id)
	{
		$articles = TableRegistry::get('Transports');

		//  $arrayOfIds =['3'];
		return $this->Transports->find('all')->where(['FIND_IN_SET(\'' . $id . '\',Transports.route)'])->toarray();
	}
	public function findtransportamount($id, $a_year)
	{
		return $this->Transportfees->find('all')->group('Transportfees.academic_year')->group('Transportfees.loc_id')->select(['qu1_fees' =>  $this->Transportfees->find()->func()->sum('Transportfees.qu1_fees'), 'qu2_fees' =>  $this->Transportfees->find('all')->func()->sum('Transportfees.qu2_fees'), 'qu3_fees' => $this->Transportfees->find('all')->func()->sum('Transportfees.qu3_fees'), 'qu4_fees' => $this->Transportfees->find('all')->func()->sum('Transportfees.qu4_fees')])->where(['Transportfees.loc_id' => $id, 'Transportfees.academic_year' => $a_year])->order(['Transportfees.id' => 'ASC'])->toArray();
	}

	public function findpaidtransportamount($id, $a_year)
	{

		return  $this->StudentTransfees->find('all')->where(['StudentTransfees.student_id' => $id, 'StudentTransfees.acedmicyear' => $a_year])->toArray();
	}
	public function findhostelamount($s_id, $a_year, $h_id)
	{

		return   $this->StudentHostalfees->find('all')->where(['StudentHostalfees.student_id' => $s_id, 'StudentHostalfees.acedmicyear' => $a_year, 'StudentHostalfees.h_id' => $h_id,])->toArray();
	}

	//------------------------------------------------------------
	// Books Report
	public function issuedBooksReport()
	{


		$holder_type = ['Student' => 'Student', 'Employee' => 'Employee'];
		$this->set('holder_type', $holder_type);

		$status_data = $this->BookStatus->find()->select('name')->where(['BookStatus.status' => 'Y'])->order(['BookStatus.name' => 'Asc'])->toarray();

		foreach ($status_data as $value) {
			$element = $value['name'];

			$b_status[$element] = $element;
		}

		$this->set('b_status', $b_status);
	}

	//---------------------------------------------------------------------
	public function searchIssuedBook()
	{
		$session = $this->request->session();

		$conn = ConnectionManager::get('default');

		$isbn_no = $this->request->data['isbn_no'];
		$b_name = $this->request->data['b_name'];
		$holder_type_id = $this->request->data['holder_type_id'];
		$holder_name = $this->request->data['holder_name'];
		$issue_date = $this->request->data['issue_date'];
		$due_date = $this->request->data['due_date'];


		$detail = "SELECT IssueBooks.asn_no,IssueBooks.holder_name,IssueBooks.holder_type_id,IssueBooks.issue_date,IssueBooks.due_date, BookCopyDetails.book_id, Books.ISBN_NO,Books.name FROM `library_issue_books` IssueBooks LEFT JOIN `library_book_copy_details` BookCopyDetails ON IssueBooks.`asn_no` = BookCopyDetails.`id` LEFT JOIN `library_books` Books ON Books.`id` = BookCopyDetails.`book_id` WHERE  1=1";

		$cond = ' ';
		if (!empty($isbn_no)) {
			$cond .= " AND Books.ISBN_NO LIKE '" . $isbn_no . "%' ";
		}

		if (!empty($b_name)) {
			$cond .= " AND UPPER(Books.name) LIKE '" . strtoupper($b_name) . "%' ";
		}

		if (!empty($holder_type_id)) {
			$cond .= " AND UPPER(IssueBooks.holder_type_id) = '" . strtoupper($holder_type_id) . "' ";
		}

		if (!empty($holder_name)) {
			$cond .= " AND UPPER(IssueBooks.holder_name) LIKE '" . strtoupper($holder_name) . "%' ";
		}

		if (!empty($issue_date)) {
			$cond .= " AND IssueBooks.issue_date = '" . date('Y-m-d', strtotime($issue_date)) . "' ";
		}

		if (!empty($due_date)) {
			$cond .= " AND IssueBooks.due_date = '" . date('Y-m-d', strtotime($due_date)) . "' ";
		}


		$detail = $detail . $cond;
		$SQL = $detail . " ORDER BY IssueBooks.asn_no ASC";

		$results = $conn->execute($SQL)->fetchAll('assoc');

		$this->set('books', $results);

		$session->delete($results_issued_books);
		$session->write('results_issued_books', $results);
	}

	//---------------------------------------------------------------------
	public function autocompleteList()
	{
		$conn = ConnectionManager::get('default');

		$h_type = $this->request->data['h_type'];

		if ($h_type != '') {
			if ($h_type == 'Student') {
				$stmt = $conn->execute("select DISTINCT holder_name from library_issue_books where holder_type_id = 'Student' ORDER BY holder_name;");
			} else if ($h_type == 'Employee') {
				$stmt = $conn->execute("select DISTINCT holder_name from library_issue_books where holder_type_id = 'Employee' ORDER BY holder_name;");
			}

			$results = $stmt->fetchAll('assoc');

			foreach ($results as $value) {
				$data_list[] = $value['holder_name'];
			}
		} else {
			$data_list[] = [];
		}

		header("Content-Type: application/json");
		echo json_encode($data_list);

		exit();
	}

	//---------------------------------------------------------------------
	public function excelExportIssuedBooks()
	{
		$this->autoRender = false;

		$session = $this->request->session();
		$issued_books = $session->read('results_issued_books');

		$output .= '"ASN No.",';
		$output .= '"Book Name",';
		$output .= '"ISBN No.",';
		$output .= '"Holder Name",';
		$output .= '"Holder Type",';
		$output .= '"Issue Date",';
		$output .= '"Due Date",';
		$output .= '"Duration",';
		$output .= "\n";

		foreach ($issued_books as $service) {
			$d1 = date('d-m-Y', strtotime($service['issue_date']));
			$d2 = date('d-m-Y', strtotime($service['due_date']));

			$output .= $service['asn_no'] . ",";
			$output .= $service['name'] . ",";
			$output .= $service['ISBN_NO'] . ",";
			$output .= $service['holder_name'] . ",";
			$output .= $service['holder_type_id'] . ",";
			$output .= $d1 . ",";
			$output .= $d2 . ",";

			if (!empty($d1) && !empty($d2)) {
				if (strtotime(date("d-m-Y")) <= strtotime($d2)) {
					$diff = date_diff(date_create(date("d-m-Y")), date_create($d2));

					$output .= $diff->format("%a day(s) left") . ",";
				} else {
					$diff = date_diff(date_create(date("d-m-Y")), date_create($d2));

					$output .= $diff->format("Overdue %a day(s)") . ",";
				}
			} else {
				echo "N/A";
			}

			$output .= "\r\n";
		}

		$filename = "issued-books-report.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);

		echo $output;

		exit();
	}

	//------------------------------------------------------------
	// Books Report 
	public function booksReport()
	{
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Book');
		$this->loadModel('IssueBook');
		$this->loadModel('BookCopyDetail');
		$this->loadModel('Language');
		$this->loadModel('BookStatus');
		$this->viewBuilder()->layout('admin');
		$lahu = $this->Language->find('list', [
			'keyField' => 'id',
			'valueField' => 'language'
		])->order(['Language.id' => 'asc'])->toArray();
		//pr($lahu); die;
		$this->set('lahu', $lahu);
		$this->loadModel('PeriodicalMasterDetails');



		$periodical = $this->PeriodicalMasterDetails->find('all')->contain(['PeriodicalMaster'])->where(['PeriodicalMasterDetails.subs_end_date IN(SELECT MAX(subs_end_date) FROM `library_periodical_details` GROUP BY `periodic_id`)', 'PeriodicalMasterDetails.id IN(SELECT MAX(id) FROM `library_periodical_details` GROUP BY `periodic_id` )'])->order(['PeriodicalMaster.name' => 'Asc'])->count();

		$total_books = $this->Book->find()->where(['Book.typ !=' => '1'])->count();
		$eng_book = $this->Book->find()->where(['Book.lang' => '5', 'Book.typ !=' => '1'])->count();
		$hin_book = $this->Book->find()->where(['Book.lang' => '6', 'Book.typ !=' => '1'])->count();
		$junior_book = $this->Book->find()->contain(['CupBoard' => ['conditions' => ['CupBoard.roomid' => '1']]])->where(['Book.typ !=' => '1'])->count();
		$senior_book = $this->Book->find()->contain(['CupBoard' => ['conditions' => ['CupBoard.roomid' => '2']]])->where(['Book.typ !=' => '1'])->count();
		//$periodical=$this->Book->find()->where(['Book.typ' => '1'])->count();
		$fgh = $this->request->session()->read('Auth.User.id');
		$rt = '0';
		if ($fgh == '8') {
			$rt = '2';
		} else {
			$rt = '1';
		}

		$issued_book = $this->IssueBook->find()->contain(['BookCopyDetail' => ['Book' => ['CupBoard' => ['conditions' => ['CupBoard.roomid' => $rt]]]]])->where(['IssueBook.status' => 'Y'])->count();

		$this->set(compact('total_books', 'eng_book', 'hin_book', 'junior_book', 'senior_book', 'issued_book', 'periodical'));

		$b_category_data = $this->BookCategory->find()->select('name')->where(['BookCategory.status' => 'Y'])->order(['BookCategory.name' => 'Asc'])->toarray();

		foreach ($b_category_data as $value) {
			$element = $value['name'];

			$b_category[$element] = $element;
		}

		$this->set('b_category', $b_category);

		//---------------------------------------------

		$status_data = $this->BookStatus->find()->select('name')->where(['BookStatus.status' => 'Y', 'BookStatus.id IN' => ['3', '15']])->order(['BookStatus.name' => 'Asc'])->toarray();

		foreach ($status_data as $value) {
			$element = $value['name'];

			$b_status[$element] = $element;
		}

		$this->set('b_status', $b_status);
	}

	//------------------------------------------------------------
	// Search Books Report 
	public function searchBook()
	{

		$session = $this->request->session();
		$session->delete('srch_status');

		$conn = ConnectionManager::get('default');

		$asn_no = $this->request->data['asn_no'];
		$isbn_no = $this->request->data['isbn_no'];
		$b_name = $this->request->data['b_name'];
		$b_category = $this->request->data['b_category'];
		$author = $this->request->data['author'];
		$status = $this->request->data['status'];
		$subtitle = $this->request->data['subtitle'];
		$adv_cat = $this->request->data['adv_cat'];
		$datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
		$dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
		$blang = $this->request->data['langu'];
		$vbn = $this->request->data['type'];
		$role_id = $session->read('Auth.User.role_id');
		$user_id = $session->read('Auth.User.id');
		// pr($this->request->data); die;
		$a = 0;
		if ($user_id == '9' || $role_id == '7') {
			$a = '1';
		}

		if ($user_id == '8') {
			$a = '2';
		}

		if ($status == 'Available' && $vbn != 1) {

			$detail = 	"SELECT Book.ISBN_NO,Book.bilno,Book.bildt,Book.accsnno,Book.periodic_category_id,Book.lang as blang,Book.name as b_name,Book.typ,Book.author, BookCategory.name as b_category, Cupboard.name as cupboard, CupboardShelf.name as shelf, BookCopyDetail.id as asn_no, BookCopyDetail.status
					FROM `library_books` Book
					LEFT JOIN `library_book_categories` BookCategory ON Book.`book_category_id` = BookCategory.`id`
					LEFT JOIN `library_cup_boards` Cupboard ON Book.`cup_board_id` = Cupboard.`id` AND Cupboard.`roomid` = $a
					LEFT JOIN `library_cup_board_shelves` CupboardShelf ON Book.`cup_board_shelf_id` = CupboardShelf.`id`
					LEFT JOIN `library_book_copy_details` BookCopyDetail ON Book.`id` = BookCopyDetail.`book_id`
					LEFT JOIN `library_issue_books` IssueBook ON BookCopyDetail.id = IssueBook.`asn_no2` AND  IssueBook.`status`='Y'  WHERE Book.typ!='1' AND 1=1";
		} else if ($status == 'Available' && $vbn == '1') {

			$detail = 	"SELECT Book.ISBN_NO,Book.bilno,Book.bildt,Book.accsnno,Book.periodic_category_id,Book.lang as blang,Book.name as b_name,Book.typ,Book.periodic_category_id,Book.author, BookCategory.name as b_category, Cupboard.name as cupboard,
					CupboardShelf.name as shelf, BookCopyDetail.id as asn_no, BookCopyDetail.status
					 FROM `library_books` Book
					LEFT JOIN `library_book_categories` BookCategory ON Book.`book_category_id` = BookCategory.`id`
					LEFT JOIN `library_cup_boards` Cupboard ON Book.`cup_board_id` = Cupboard.`id` AND Cupboard.`roomid` = $a
					LEFT JOIN `library_cup_board_shelves` CupboardShelf ON Book.`cup_board_shelf_id` = CupboardShelf.`id`
					LEFT JOIN `library_book_copy_details` BookCopyDetail ON Book.`id` = BookCopyDetail.`book_id`
					LEFT JOIN `library_issue_books` IssueBook ON BookCopyDetail.id = IssueBook.`asn_no2` AND  IssueBook.`status`='Y'  WHERE Book.typ!='0' AND 1=1";
		} else if ($status == 'ALL') {

			$detail = 	"SELECT Book.ISBN_NO,Book.bilno,Book.bildt,Book.accsnno,Book.periodic_category_id,Book.lang as blang,Book.name as b_name,Book.typ,Book.periodic_category_id,Book.author, BookCategory.name as b_category, Cupboard.name as cupboard,
					CupboardShelf.name as shelf, BookCopyDetail.id as asn_no, BookCopyDetail.status
					 FROM `library_books` Book
					LEFT JOIN `library_book_categories` BookCategory ON Book.`book_category_id` = BookCategory.`id`
					LEFT JOIN `library_cup_boards` Cupboard ON Book.`cup_board_id` = Cupboard.`id` AND Cupboard.`roomid` = $a
					LEFT JOIN `library_cup_board_shelves` CupboardShelf ON Book.`cup_board_shelf_id` = CupboardShelf.`id`
					LEFT JOIN `library_book_copy_details` BookCopyDetail ON Book.`id` = BookCopyDetail.`book_id`
					  WHERE  1=1";
		} else {

			$detail = 	"SELECT Book.ISBN_NO,Book.bilno,Book.bildt,Book.accsnno,Book.periodic_category_id,Book.lang as blang,Book.name as b_name,Book.typ,Book.author, BookCategory.name as b_category, Cupboard.name as cupboard,
					CupboardShelf.name as shelf, BookCopyDetail.id as asn_no, BookCopyDetail.status,
					DATEDIFF(now(), IssueBook.due_date) as NumberOfDays FROM `library_books` Book
					LEFT JOIN `library_book_categories` BookCategory ON Book.`book_category_id` = BookCategory.`id`
					LEFT JOIN `library_cup_boards` Cupboard ON Book.`cup_board_id` = Cupboard.`id` AND Cupboard.`roomid` = $a
					LEFT JOIN `library_cup_board_shelves` CupboardShelf ON Book.`cup_board_shelf_id` = CupboardShelf.`id`
					LEFT JOIN `library_book_copy_details` BookCopyDetail ON Book.`id` = BookCopyDetail.`book_id`
					LEFT JOIN `library_issue_books` IssueBook ON BookCopyDetail.id = IssueBook.`asn_no2` AND  IssueBook.`status`='Y'  WHERE  1=1";
		}

		$cond = ' ';


		$cond .= " AND Book.typ='" . $vbn . "'";


		if (!empty($adv_cat)) {
			if ($adv_cat == "FICTION") {
				$cond .= " AND BookCategory.`name` LIKE '%FICTION%' AND BookCategory.`name` NOT LIKE '%NON FICTION%'";
			} else {
				$cond .= " AND BookCategory.`name` LIKE '%" . $adv_cat . "%'";
			}
		}
		if (!empty($subtitle)) {
			$cond .= " AND Book.sub_title LIKE '%" . $subtitle . "%' ";
		}
		if (!empty($asn_no)) {
			$cond .= " AND Book.accsnno='" . $asn_no . "' ";
		}
		if (!empty($blang)) {
			$cond .= " AND Book.lang ='" . $blang . "' ";
		}


		if (!empty($datefrom) && $datefrom != '1970-01-01') {

			$cond .= " AND Book.bildt >= '" . $datefrom . "'";
		}


		if (!empty($dateto2) && $dateto2 != '1970-01-01') {

			$cond .= " AND Book.bildt <= '" . $dateto2 . "'";
		}

		if (!empty($isbn_no)) {
			$cond .= " AND Book.ISBN_NO LIKE '" . $isbn_no . "%' ";
		}

		if (!empty($b_name)) {
			$cond .= " AND UPPER(Book.name) LIKE '" . addslashes($b_name) . "%' ";
		}

		if (!empty($b_category)) {
			$cond .= " AND UPPER(BookCategory.name) = '" . addslashes($b_category) . "' ";
		}

		if (!empty($author)) {
			$cond .= " AND UPPER(Book.author) LIKE '" . addslashes($author) . "%' ";
		}

		if (!empty($status)) {
			if ($status != 'ALL') {
				if ($status != 'Overdue') {
					$cond .= " AND UPPER(BookCopyDetail.status) = '" . strtoupper($status) . "' ";
				} else if ($status == 'Overdue') {
					$cond .= " AND IssueBook.due_date < cast(now() as date) ";

					$this->set('srch_status', $status);
					$session->write('srch_status', $status);
				}
			}
		}
		if ($a != '0') {
			$cond .= " AND Cupboard.`roomid` = '" . $a . "' ";
		}

		$detail = $detail . $cond;

		if (!empty($datefrom) || $datefrom != '1970-01-01') {
			$SQL = $detail . " ORDER BY Book.bildt ASC";
		} else if (!empty($status) && $status == 'Overdue') {
			$SQL = $detail . " ORDER BY IssueBook.due_date ASC";
		} else {
			$SQL = $detail . " ORDER BY BookCopyDetail.id ASC";
		}


		$results = $conn->execute($SQL)->fetchAll('assoc');
		//pr($results); die;

		$this->set('books', $results);

		$session->delete('results_books');
		$session->write('results_books', $results);
	}

	//---------------------------------------------------------------------
	public function excelExportBooks()
	{
		$this->loadModel('PeriodicalMaster');
		$this->loadModel('Language');
		//$this->autoRender=false;

		$session = $this->request->session();
		$books_data = $session->read('results_books');
		$srch_status = $session->read('srch_status');

		$this->set('books_data', $books_data);
		$this->set('srch_status', $srch_status);
		ini_set('max_execution_time', 1600);
		//~ $headerRow = array("ASN No", "ISBN No.", "Book Name", "Category", "Cupboard", "Cupboard Shelf", "Language", "Author", "Status");
		//~ $output = implode("\t", $headerRow)."\n";

		//~ foreach($books_data as $service)
		//~ {
		//~ //pr($service); die;
		//~ if($service['typ']=='1'){
		//~ $percat = $this->PeriodicalMaster->find('all')->where(['id' => $service['periodic_category_id']])->first();

		//~ }
		//~ $result=array();
		//~ $result[]=$service['accsnno'];
		//~ if($service['typ']=='1'){
		//~ $result[]=$percat['ISBN_NO'];
		//~ }else {
		//~ $result[]=$service['ISBN_NO'];
		//~ }
		//~ $result[]=str_replace(',', ' ', $service['b_name']);
		//~ if($service['typ']=='1'){

		//~ $result[]=$percat['ISBN_NO'];
		//~ }else {
		//~ $result[]=$service['b_category'];
		//~ }

		//~ $result[]=$service['cupboard'];
		//~ $result[]=$service['shelf'];
		//~ if($service['typ']=='1'){
		//~ $lasd=$this->Language->find('all')->where(['Language.id'=>$percat['blang']])->first();
		//~ $result[]=$lasd['language'];
		//~ }else {
		//~ $lasd=$this->Language->find('all')->where(['Language.id'=>$service['blang']])->first();
		//~ $result[]=$lasd['language'];
		//~ }
		//~ if($service['typ']=='1'){
		//~ $result[]=$percat['author'];
		//~ }else {
		//~ $result[]=$service['author'];
		//~ }
		//~ if($srch_status != 'ALL') {
		//~ if($srch_status == 'Overdue')
		//~ {
		//~ $result[]="Overdue: ".$service['NumberOfDays']." day(s)";
		//~ }
		//~ else
		//~ {
		//~ $result[]=$service['status'];
		//~ }
		//~ }
		//~ $output .=  implode("\t", $result)."\n";
		//~ }

		//~ $filename = "Books-Report.xls";
		//~ header('Content-type: application/ms-excel');
		//~ header('Content-Disposition: attachment; filename='.$filename);
		//~ echo $output;die;
	}

	public function register($mid = null, $month = null)
	{

		$sid = base64_decode($mid);

		$baseregister = explode('/', $sid);


		$classid = $baseregister[0];
		$sectionid = $baseregister[1];
		$acedmicyear = $baseregister[2];
		$month = $month;

		if ($month == 1 || $month == 2 || $month == 3) {
			$h = date('Y');
			$rff['0'] = $h + 1;
		} else {
			$rff['0'] = date('Y');
		}
		//$rff=explode('-',$acedmicyear);

		$sections = $this->Sections->find('all')->where(['id' => $sectionid])->order(['title' => 'ASC'])->first()->toArray();



		$class = $this->Classes->find('all')->where(['id' => $classid])->order(['title' => 'ASC'])->first()->toArray();

		$class = $class['title'];

		$sections = $sections['title'];

		$days = date("t", mktime(0, 0, 0, $month, 1, $rff['0']));

		for ($i = 1; $i <= $days; $i++) {
			$var[] = $i . "-" . $month . "-" . $rff[0];
		}
		$var[] = 'Total-Present';
		$var[] = 'Total-Absent';
		$_headers = ['Enroll'];

		$_header = array_merge($_headers, $var);


		$monthName = date('F', mktime(0, 0, 0, $month, 10));

		$this->response->download($class . '(' . $sections . ')-' . $monthName . '/' . $rff['0'] . 'attendence.csv');

		$data0 =  $this->Studattends->find()->select([
			'Studattends.stud_id'
		])->where(['Studattends.class_id' => $classid, 'Studattends.section_id' => $sectionid, 'Studattends.acedemic' => $acedmicyear])->group([
			'Studattends.stud_id'
		])->toarray();


		$days = date("t", mktime(0, 0, 0, $month, 1, $rff['0']));
		$is = 0;

		foreach ($data0  as $key => $value) {
			$is++;
			$cntpresent = 0;
			$cntabsent = 0;
			$datas[$is][0] = $value['stud_id'];

			for ($i = 1; $i <= $days; $i++) {

				$d = $rff[0] . "-" . $month . "-" . $i;
				$datal =  $this->Studattends->find('all')->select([
					'Studattends.status',
					'Studattends.date'
				])->where(['Studattends.date' => $d, 'Studattends.class_id' => $classid, 'Studattends.section_id' => $sectionid, 'Studattends.acedemic' => $acedmicyear, 'Studattends.stud_id' => $value['stud_id']])->toarray();
				if (!empty($datal)) {
					$datas[$is][] = $datal[0]['status'];
					$cntpresent++;
				} else {
					$datas[$is][] = 'A';
					$cntabsent++;
				}
			}

			if ($days == '31') {
				$datas[$is][32] = $cntpresent;
				$datas[$is][33] = $cntabsent;
			} else {
				$datas[$is][31] = $cntpresent;
				$datas[$is][32] = $cntabsent;
			}
		}
		$data = $datas;

		//$data =  [[$value['stud_id'], 'b', 'c']];
		// $data = [['a', 'b', 'c']];
		//  $data_two = [[1, 2, 3]];
		//  $data_three = [['you', 'and', 'me']];

		$_serialize = ['data'];



		//$this->set(compact('data', '_serialize'));


		$this->set(compact('data', '_serialize', '_header'));
		//$this->set(compact('data', 'data_two', 'data_three', '_serialize', '_header'));
		$this->viewBuilder()->className('CsvView.Csv');
		return;
	}
}
