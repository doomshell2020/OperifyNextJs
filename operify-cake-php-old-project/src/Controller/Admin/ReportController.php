<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
class ReportController extends AppController
{

    //initialize component
    public function initialize()
    {
        ini_set('max_execution_time', 1600);
        parent::initialize();
        $this->loadModel('Enquires');
        $this->loadModel('Classfee');
        $this->loadModel('Classes');
        $this->loadModel('Modes');
        $this->loadModel('Followup');
        $this->loadModel('Students');
        $this->loadModel('DropOutStudent');
        $this->loadModel('Sections');
        $this->loadModel('Employees');
        $this->loadModel('Departments');
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
        $this->loadModel('Guardians');
        $this->loadModel('Studattends');
        $this->loadModel('Events');
        $this->loadModel('Shift');
        $this->loadModel('EmployeeAttendance');
        $this->loadModel('Users');
        $this->loadModel('Feesheads');
        $this->loadModel('DiscountCategory');
        $this->loadModel('Houses');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsmanager');
        $this->loadModel('Studentshistory');
        $this->loadModel('Sms');
        $this->loadModel('ExamSubjects');
        $this->loadModel('Transportfees');

        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['po_pdf']);

        require_once 'Firebase.php';
        require_once 'Push.php';
    }

    public function employees_login_details()
    {

        $this->loadModel('Users');
        $this->loadModel('Employees');

        $employees = $this->Employees->find('all')->contain(['Users'])->order(['Employees.id' => 'ASC'])->toarray();
        // pr($students); die;
        $this->set('employees', $employees);
    }

    //pdf
    public function employees_login_details_pdf()
    {
        $this->response->type('pdf');
        $this->loadModel('Users');
        $this->loadModel('Employees');

        $employees = $this->Employees->find('all')->contain(['Users'])->order(['Employees.id' => 'ASC'])->toarray();
        // pr($students); die;
        $this->set('employees', $employees);
    }


    public function parent_logindetails()
    {
        $this->loadModel('Sections');
        $this->loadModel('Classes');
        $this->loadModel('Students');

        // pr($_SESSION); die;
        // $students = $this->Students->find('all')->contain(['Classes', 'Sections'])->order(['Students.id' => 'ASC'])->toarray();
        // pr($students); die;
        // pr($fname);die;
        $conn = ConnectionManager::get('default');
        $detail = "SELECT Students.id,Students.enroll,Students.password,Students.sms_mobile,Students.category,Students.oldenroll,Students.fname,Students.fathername,Students.mothername,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.class_id,Students.created,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students Right JOIN classes Classes ON Students.`class_id` = Classes.id Right JOIN sections Sections ON Students.`section_id` = Sections.id WHERE Students.`status`='Y' and Students.board_id='1' and '1'='1'";

        if (!empty($_SESSION['parentlogindata'])) {

            $detail = $detail . $_SESSION['parentlogindata'];
        } else {

            $detail = $detail;
        }
        $SQL = $detail . "ORDER BY Classes.sort ASC";
        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->set('parent_login', $results);
    }

    public function detailreport()
    {

        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', ['keyField' => 'Classes.id', 'valueField' => 'Classes.title'])->contain(['Classes'])
            ->where(['Classections.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toArray();
        //pr($classes); die;

        $this->set(compact('classes'));
        $ar = array('2', '3');
        $feesheadstotal = $this->Feesheads->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'sort !=' => '0'])->order(['sort' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));
    }


    public function searchdetailreport()
    {

        $mode = $this->request->data['mode'];
        $selectField = $this->request->data['selectField'];

        if (in_array('Tution Fee', $selectField)) {
            array_push($selectField, "Quater1", "Quater2", "Quater3", "Quater4");
        }

        $gh = array_flip($selectField);
        //$this->set(compact('selectField'));
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        //    echo $datefrom; echo $dateto; die;

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $this->set(compact('datefrom'));

            $stts = array('Studentfees.paydate >=' => $datefrom);
            $apk[] = $stts;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {
            $this->set(compact('dateto'));
            $this->set(compact('dateto2'));
            $stts = array('Studentfees.paydate <=' => $dateto2);
            $apk[] = $stts;
        }

        if (!empty($mode)) {
            $pii = array('Studentfees.mode IN' => $mode);
            $apk[] = $pii;
        }

        $pii = array('Studentfees.status' => 'Y');
        $apk[] = $pii;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii = array('Students.board_id' => 1);
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {

            $pii = array('Students.board_id IN' => ['2', '3']);
            $apk[] = $pii;
        }
        //$pii=array('Students.status'=>'Y');
        //$apk[]=$pii;

        $pii = array('Studentfees.recipetno !=' => '0');
        $apk[] = $pii;

        $Classections21 = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.recipetno' => 'ASC'])->toarray();

        if (!empty($datefrom) && $datefrom != '1970-01-01') {
            $stts2 = array('Studentfees.paydate >=' => $datefrom);
            $apk2[] = $stts2;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {

            $stts2 = array('Studentfees.paydate <=' => $dateto2);
            $apk2[] = $stts2;
        }

        if (!empty($mode)) {
            $pii2 = array('Studentfees.mode IN' => $mode);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.status' => 'Y');
        $apk2[] = $pii2;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii2 = array('DropOutStudent.board_id IN' => ['1']);
            $apk2[] = $pii2;
        } elseif ($rolepresent == '8') {

            $pii2 = array('DropOutStudent.board_id IN' => ['2', '3']);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.recipetno !=' => '0');
        $apk2[] = $pii2;

        $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk2)->order(['Studentfees.recipetno' => 'ASC'])->toarray();
        //    pr($Classections212); die;

        $Classections21 = array_merge($Classections21, $Classections212);

        $extra1 = array('Late Fee', 'Discount Fee', 'Other Discount');
        $extra = array_flip($extra1);
        $Classections = array();
        $sel = array();

        $findrecipiet = $this->checkregistration($datefrom, $dateto2);
        if ($findrecipiet[0]['regfee']) {
            //    if (in_array("CASH", $mode)) {
            if (in_array('Registration', $selectField)) {
                array_push($sel, "Registration");
            }

            //    }
        }

        $findprospectus = $this->checkprospectus($datefrom, $dateto2);

        if ($findprospectus[0]['p_fees']) {
            //    if (in_array("CASH", $mode)) {

            if (in_array('Prospectus', $selectField)) {
                array_push($sel, "Prospectus");
            }

            //    }
        }
        foreach ($Classections21 as $fgh) {

            $qua = unserialize($fgh['quarter']);
            $arr = array();
            foreach ($qua as $fg => $cg) { //pr($qua);

                if (ctype_digit(trim($fg, '"'))) {
                    $arr['Prev. Due'] = $cg;
                } else {
                    $arr[$fg] = $cg;
                }
            }

            //pr($arr);
            $rty = array_intersect_key($gh, $arr);
            if (!empty($rty)) {
                foreach ($rty as $yu => $rt) {
                    $sel[] = $yu;
                }
            }
            if (!empty($rty)) {

                $Classections[] = $fgh;
            }
        }

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Late Fee') {
                        if ($fgh['lfine'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }

        array_push($sel, "Prev. Access Amount");
        array_push($sel, "Access Amount");

        sort($sel);

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Discount Fee') {
                        if ($fgh['discount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } elseif ($ok == 'Other Discount') {
                        if ($fgh['addtionaldiscount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }
        array_push($sel, "Due Amount");
        array_push($sel, "Other Fees");

        $rk = array_unique($sel);

        $nm = array_unique($Classections);

        //array_push($rk,"Due Amount");

        $this->set('selectField', $rk);
        //die;

        $this->set('Classections', $nm);
        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmicyear = $users['academic_year'];
        $this->set(compact('acedmicyear'));
    }

    public function document()
    {
        $this->loadModel('Documentcategory');

        $this->loadModel('Documents');
        $this->request->session()->delete('students');
        $this->viewBuilder()->layout('admin');

        $documentcategory = $this->Documentcategory->find('list', [
            'keyField' => 'id',
            'valueField' => 'categoryname',
        ])->where(['Documentcategory.type !=' => 0])->order(['id' => 'DESC'])->toArray();
        //pr($documentcategory); die;
        $this->set('documentcategory', $documentcategory);

        $students = $this->Documents->find('all')->contain(['Students', 'Documentcategory'])->order(['Documents.created' => 'DESC'])->toarray();

        $this->set(compact('students'));
    }

    public function documentdropout()
    {
        $this->loadModel('Documentcategory');
        $this->loadModel('DropOutStudent');
        $this->loadModel('Documents');
        $this->request->session()->delete('students');
        $this->viewBuilder()->layout('admin');

        $documentcategory = $this->Documentcategory->find('list', [
            'keyField' => 'id',
            'valueField' => 'categoryname',
        ])->where(['Documentcategory.type !=' => 0])->order(['id' => 'DESC'])->toArray();
        //pr($documentcategory); die;
        $this->set('documentcategory', $documentcategory);

        $students = $this->Documents->find('all')->contain(['DropOutStudent', 'Documentcategory'])->order(['Documents.created' => 'DESC'])->toarray();

        $this->set(compact('students'));
    }

    public function documentsearch()
    {

        $this->loadModel('documentcategory');
        $this->loadModel('Documents');
        $this->loadModel('Students');
        $this->loadModel('DropOutStudent');

        $sms_temp_id = $this->request->data['document_id'];
        $enroll = $this->request->data['enroll'];

        //echo $sms_temp_id; die;
        $apk = array();
        if (!empty($sms_temp_id)) {
            $stts = array('Documents.doccat_id' => $sms_temp_id);
            $apk[] = $stts;
        }

        if (!empty($enroll)) {
            $stts = array('Students.enroll' => $enroll);
            $apk[] = $stts;
        }

        $students = $this->Documents->find('all')->contain(['Students', 'Documentcategory'])->where($apk)->order(['Documents.id' => 'DESC', 'Students.fname' => 'ASC'])->toarray();

        // pr($students); die;

        $this->set(compact('students'));
    }

    public function documentsearchdropout()
    {

        $this->loadModel('documentcategory');
        $this->loadModel('Documents');
        $this->loadModel('Students');
        $this->loadModel('DropOutStudent');

        $sms_temp_id = $this->request->data['document_id'];
        $enroll = $this->request->data['enroll'];

        //echo $sms_temp_id; die;
        $apk = array();
        if (!empty($sms_temp_id)) {
            $stts = array('Documents.doccat_id' => $sms_temp_id);
            $apk[] = $stts;
        }

        if (!empty($enroll)) {
            $stts = array('DropOutStudent.enroll' => $enroll);
            $apk[] = $stts;
        }

        $students = $this->Documents->find('all')->contain(['DropOutStudent', 'Documentcategory'])->where($apk)->order(['Documents.created' => 'DESC'])->toarray();

        // pr($students); die;

        $this->set(compact('students'));
    }

    public function documentexcel()
    {

        $this->loadModel('documentcategory');
        $this->loadModel('Documents');
        $students = $this->request->session()->read('students');

        if (isset($students)) {

            $this->set(compact('students'));
        } else {

            $students = $this->Documents->find('all')->contain(['Students', 'Documentcategory'])->order(['Documents.created' => 'DESC', 'Students.fname' => 'ASC'])->toarray();

            //pr($students); die;

            $this->set(compact('students'));
        }
    }

    public function documentexcel2()
    {

        $this->loadModel('documentcategory');
        $this->loadModel('Documents');
        $students = $this->request->session()->read('students');

        if (isset($students)) {

            $this->set(compact('students'));
        } else {

            $students = $this->Documents->find('all')->contain(['DropOutStudent', 'Documentcategory'])->order(['Documents.created' => 'DESC', 'DropOutStudent.fname' => 'ASC'])->toarray();

            $this->set(compact('students'));
        }
    }

    public function age()
    {

        $this->autoRender = false;
        $this->loadModel('Disabilitys');

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        $headerRow = array("");
        $fgh = array();
        foreach ($classes as $key => $classes) {
            $headerRow[] = "Class-" . $classes;
            $headerRow[] = "";
            $fgh[] = $key;
        }

        $output = implode("\t", $headerRow) . "\n";

        $headerRow2 = array("Age");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow2[] = "Boys";
            $headerRow2[] = "Girls";
        }

        $output .= implode("\t", $headerRow2) . "\n";

        $gender = array('Male', 'Female');

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        for ($i = 3; $i <= 23; $i++) {
            $result = array();

            $str = "";

            $result[] = $i;
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {

                    $conn = ConnectionManager::get('default');
                    $date = date('Y-m-d');
                    $SQL = "SELECT id,DATE_FORMAT(FROM_DAYS(DATEDIFF('" . $date . "',students.dob)), '%Y')+0 AS age FROM students  WHERE  class_id='" . $lgh . "' AND gender='" . $gendervalue . "' AND acedmicyear='" . $acedmic . "' AND DATE_FORMAT(FROM_DAYS(DATEDIFF('2019-12-07',students.dob)), '%Y')+0='" . $i . "'";

                    $find_data = $conn->execute($SQL)->count();

                    if ($find_data > 0) {
                        $result[] = $find_data;
                    } else {
                        $result[] = '0';
                    }
                }
            }

            $output .= implode("\t", $result) . "\n";
        }

        $headerRow3[] = "Total";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result3 = 0;

                for ($i = 3; $i <= 23; $i++) {

                    $conn = ConnectionManager::get('default');
                    $date = date('Y-m-d');
                    $SQL = "SELECT id,DATE_FORMAT(FROM_DAYS(DATEDIFF('" . $date . "',students.dob)), '%Y')+0 AS age FROM students  WHERE  class_id='" . $lgh . "' AND gender='" . $gendervalue . "' AND acedmicyear='" . $acedmic . "' AND DATE_FORMAT(FROM_DAYS(DATEDIFF('2019-12-07',students.dob)), '%Y')+0='" . $i . "'";

                    $find_data = $conn->execute($SQL)->count();

                    $result3 += $find_data;
                }
                $headerRow3[] = $result3;
            }
        }
        $output .= implode("\t", $headerRow3) . "\n";

        //pr ($result); die;

        $filename = "Enrollment-By-Age-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        //pr ($result); die;
        echo $output;
        die;
    }

    public function disabilitys()
    {
        $this->autoRender = false;
        $this->loadModel('Disabilitys');

        ini_set('max_execution_time', 1600);
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id ' => '22'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        $headerRow = array("", "");
        $fgh = array();
        foreach ($classes as $key => $classes) {
            $headerRow[] = "Class-" . $classes;
            $headerRow[] = "";
            $fgh[] = $key;
        }

        $output = implode("\t", $headerRow) . "\n";

        $headerRow2 = array("S.NO.", "Type of Disablity");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow2[] = "Boys";
            $headerRow2[] = "Girls";
        }

        $output .= implode("\t", $headerRow2) . "\n";

        $gender = array('Male', 'Female');
        $disablity_data = $this->Disabilitys->find('all')->order(['id' => 'ASC'])->toarray();
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        foreach ($disablity_data as $people) {
            $result = array();

            $str = "";

            $result[] = $cnt++;
            $result[] = $people["name"];
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {
                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'disability' => $people['id'], 'acedmicyear' => $acedmic])->count();
                    if ($find_data > 0) {
                        $result[] = $find_data;
                    } else {
                        $result[] = '0';
                    }
                }
            }
            //$class_id= $classes
            $output .= implode("\t", $result) . "\n";
        }

        $headerRow3[] = "";
        $headerRow3[] = "Total Enrollment";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result3 = 0;

                foreach ($disablity_data as $people) {

                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'disability' => $people['id'], 'acedmicyear' => $acedmic])->count();

                    $result3 += $find_data;
                }
                $headerRow3[] = $result3;
            }
        }
        $output .= implode("\t", $headerRow3) . "\n";

        //pr ($result); die;

        $filename = "Enrollment-Disabilitys-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        //pr ($result); die;
        echo $output;
        die;
    }

    public function cast()
    {
        $this->autoRender = false;

        ini_set('max_execution_time', 1600);
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id !=' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        $headerRow = array("", "");
        $fgh = array();
        foreach ($classes as $key => $classes) {
            $headerRow[] = "Class-" . $classes;
            $headerRow[] = "";
            $fgh[] = $key;
        }

        $output = implode("\t", $headerRow) . "\n";

        $headerRow2 = array("S.no.", "Enrollment By Caste");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow2[] = "Boys";
            $headerRow2[] = "Girls";
        }

        $output .= implode("\t", $headerRow2) . "\n";

        $gender = array('Male', 'Female');
        $cast_data = $this->Students->find('all')->select(['cast'])->where(['status' => 'Y'])->group(['cast'])->toarray();

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        foreach ($cast_data as $h => $people) {
            $result = array();

            $str = "";

            $result[] = $cnt++;
            $result[] = $people["cast"];
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {
                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'cast' => $people['cast'], 'acedmicyear' => $acedmic, 'status' => 'Y'])->count();
                    if ($find_data > 0) {
                        $result[] = $find_data;
                    } else {
                        $result[] = '0';
                    }
                }
            }
            //$class_id= $classes
            $output .= implode("\t", $result) . "\n";
        }

        $headerRow3[] = "";
        $headerRow3[] = "Total";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result3 = 0;

                foreach ($cast_data as $h => $people) {

                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'cast' => $people['cast'], 'acedmicyear' => $acedmic, 'status' => 'Y'])->count();

                    $result3 += $find_data;
                }
                $headerRow3[] = $result3;
            }
        }
        $output .= implode("\t", $headerRow3) . "\n";

        $headerRow21 = array("", "");

        $output .= implode("\t", $headerRow4) . "\n";

        $headerRow21 = array("S.no.", "Enrollment By Religion");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow21[] = "";
            $headerRow21[] = "";
        }

        $output .= implode("\t", $headerRow21) . "\n";

        $gender = array('Male', 'Female');
        $results = array("Hindu", "Muslim", "Christian", "Sikh", "Buddhist", "Parsi", "Jain", "Others");

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        foreach ($results as $people) {
            $result2 = array();

            $str = "";

            $result2[] = $cnt++;
            $result2[] = $people;
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {
                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'religion' => $people, 'acedmicyear' => $acedmic, 'status' => 'Y'])->count();
                    if ($find_data > 0) {
                        $result2[] = $find_data;
                    } else {
                        $result2[] = '0';
                    }
                }
            }
            //$class_id= $classes
            $output .= implode("\t", $result2) . "\n";
        }

        $headerRow31[] = "";
        $headerRow31[] = "Total";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result31 = 0;

                foreach ($results as $people) {

                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'religion' => $people, 'acedmicyear' => $acedmic, 'status' => 'Y'])->count();

                    $result31 += $find_data;
                }
                $headerRow31[] = $result31;
            }
        }
        $output .= implode("\t", $headerRow31) . "\n";

        //pr ($result); die;

        $filename = "Enrollment-By-Caste/Religion-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        //pr ($result); die;
        echo $output;
        die;
    }

    public function castrepeaters()
    {
        $this->autoRender = false;

        ini_set('max_execution_time', 1600);
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id <=' => '22'])->order(['Classes.sort' => 'ASC'])->toArray();
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id >=' => '23'])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        $headerRow = array("", "");
        $fgh = array();
        foreach ($classes as $key => $classes) {
            $headerRow[] = "Class-" . $classes;
            $headerRow[] = "";
            $fgh[] = $key;
        }

        $output = implode("\t", $headerRow) . "\n";

        $headerRow2 = array("S.no.", "Repeaters By Caste");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow2[] = "Boys";
            $headerRow2[] = "Girls";
        }

        $output .= implode("\t", $headerRow2) . "\n";

        $gender = array('Male', 'Female');
        $cast_data = $this->Students->find('all')->select(['cast'])->group(['cast'])->toarray();

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        foreach ($cast_data as $h => $people) {
            $result = array();

            $str = "";

            $result[] = $cnt++;
            $result[] = $people["cast"];
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {
                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'cast' => $people['cast'], 'acedmicyear' => $acedmic])->count();
                    if ($find_data > 0) {
                        $result[] = '0';
                    } else {
                        $result[] = '0';
                    }
                }
            }
            //$class_id= $classes
            $output .= implode("\t", $result) . "\n";
        }

        $headerRow3[] = "";
        $headerRow3[] = "Total";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result3 = 0;

                foreach ($cast_data as $h => $people) {

                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'cast' => $people['cast'], 'acedmicyear' => $acedmic])->count();

                    $result3 += '0';
                }
                $headerRow3[] = $result3;
            }
        }
        $output .= implode("\t", $headerRow3) . "\n";

        $headerRow21 = array("", "");

        $output .= implode("\t", $headerRow4) . "\n";

        $headerRow21 = array("S.no.", "Repeaters By Religion");

        foreach ($fgh as $key2 => $classes2) {
            $headerRow21[] = "";
            $headerRow21[] = "";
        }

        $output .= implode("\t", $headerRow21) . "\n";

        $gender = array('Male', 'Female');
        $results = array("Hindu", "Muslim", "Christian", "Sikh", "Buddhist", "Parsi", "Jain", "Others");

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $cnt = 1;
        foreach ($results as $people) {
            $result2 = array();

            $str = "";

            $result2[] = $cnt++;
            $result2[] = $people;
            foreach ($fgh as $lgh) {

                foreach ($gender as $gendervalue) {
                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'religion' => $people, 'acedmicyear' => $acedmic])->count();
                    if ($find_data > 0) {
                        $result2[] = '0';
                    } else {
                        $result2[] = '0';
                    }
                }
            }
            //$class_id= $classes
            $output .= implode("\t", $result2) . "\n";
        }

        $headerRow31[] = "";
        $headerRow31[] = "Total";

        foreach ($fgh as $lgh) {

            foreach ($gender as $gendervalue) {
                $result31 = 0;

                foreach ($results as $people) {

                    $find_data = $this->Students->find('all')->select(['id'])->where(['class_id' => $lgh, 'gender' => $gendervalue, 'religion' => $people, 'acedmicyear' => $acedmic])->count();

                    $result31 += '0';
                }
                $headerRow31[] = $result31;
            }
        }
        $output .= implode("\t", $headerRow31) . "\n";

        //pr ($result); die;

        $filename = "Repeaters-By-Caste/Religion-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        //pr ($result); die;
        echo $output;
        die;
    }

    public function prospectusreport()
    {

        $this->loadModel('Enquires');
        $this->loadModel('Documents');
        $enquires = $this->request->session()->read('studentsprospectus');

        if (isset($enquires)) {

            $this->set(compact('enquires'));
        } else {

            $classes_data = $this->Enquires->find('all')->order(['Enquires.id' => 'DESC'])->contain(['Classes', 'Modes'])->toarray();

            $this->set('enquires', $classes_data);
        }
    }

    public function drop_out_student_search()
    {
        $conn = ConnectionManager::get('default');

        //pr($this->request->data); die;
        $class = $this->request->data['class_id'];

        $section = $this->request->data['section_id'];
        $status = $this->request->data['status'];
        $d1 = $this->request->data['d1'];
        $d2 = $this->request->data['d2'];
        $academicyear = $this->request->data['acedmicyear'];

        // $detail = "SELECT DropOutStudent.id,DropOutStudent.month,DropOutStudent.dropcreated,DropOutStudent.laststudclass,DropOutStudent.date_application,DropOutStudent.date_issue,DropOutStudent.s_id,DropOutStudent.status_tc,DropOutStudent.fathername,DropOutStudent.enroll,DropOutStudent.fname,DropOutStudent.middlename,DropOutStudent.lname,DropOutStudent.mobile,DropOutStudent.acedmicyear,DropOutStudent.class_id,DropOutStudent.sms_mobile,
		// DropOutStudent.section_id, DropOutStudent.status,  Classes.title as classtitle , Sections.title as sectiontitle
		// FROM `drop_out_students` DropOutStudent LEFT JOIN classes Classes ON DropOutStudent.`class_id` = Classes.id
		// LEFT JOIN sections Sections ON DropOutStudent.`section_id` = Sections.id WHERE  1=1 ";
 //sanjay code

        $detail = "SELECT DropOutStudent.*,Classes.title as classtitle , Sections.title as sectiontitle
        FROM `drop_out_students` DropOutStudent LEFT JOIN classes Classes ON DropOutStudent.`class_id` = Classes.id
        LEFT JOIN sections Sections ON DropOutStudent.`section_id` = Sections.id WHERE  1=1 ";

        $cond = ' ';

        if (!empty($class)) {
            $cond .= " AND DropOutStudent.class_id LIKE '" . $class . "'";
        }

        if (!empty($section)) {
            $cond .= " AND DropOutStudent.section_id LIKE '" . $section . "'";
        }

        if (!empty($status) && $status != "Both") {
            $cond .= " AND DropOutStudent.status_tc LIKE '" . $status . "' ";
        }
        if (!empty($d1)) {

            $cond .= " AND DATE(DropOutStudent.dropcreated) >= '" . $d1 . "'";
        }
        if (!empty($d2)) {

            $cond .= " AND DATE(DropOutStudent.dropcreated) <= '" . $d2 . "'";
        }
        if (!empty($academicyear)) {

            $cond .= " AND DropOutStudent.updateacedemic = '" . $academicyear . "'";
            // $cond .= " AND DropOutStudent.updateacedemic  IS NULL";

        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        //~ if ($rolepresent == '5') {

        //~ $cond .= " AND DropOutStudent.board_id='1'";

        //~ } else if ($rolepresent == '8') {

        //~ $cond .= " AND DropOutStudent.board_id IN('2','3')";

        //~ }

        $detail = $detail . $cond;
        if ($status = "N") {

            $SQL = $detail . " ORDER BY DropOutStudent.dropcreated DESC";
        } elseif ($status = "Y") {

            $SQL = $detail . " ORDER BY DropOutStudent.date_issue DESC";
        } else {

            $SQL = $detail . " ORDER BY DropOutStudent.id DESC";
        }
        //echo $SQL; die;
        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->request->session()->delete('students');
        $this->request->session()->write('students', $results);

        $this->set('students', $results);
    }

    public function detainedreport_student_search()
    {
        $conn = ConnectionManager::get('default');

        //pr($this->request->data); die;
        $class = $this->request->data['class_id'];

        $section = $this->request->data['section_id'];

        $academicyear = $this->request->data['acedmicyear'];

        if (!empty($class)) {

            $stts = array('Studentshistory.class_id' => $class);
            $apk[] = $stts;
        }
        if (!empty($section)) {

            $stts = array('Studentshistory.section_id' => $section);
            $apk[] = $stts;
        }

        if (!empty($academicyear)) {

            $stts = array('Studentshistory.acedmicyear' => $academicyear);
            $apk[] = $stts;
        }
        $stts = array('Studentshistory.actionhistory' => 'REPEAT');
        $apk[] = $stts;

        $results = $this->Studentshistory->find('all')->contain(['Classes', 'Sections'])->where($apk)->order(['Classes.sort' => 'ASC'])->toarray();
        //pr($finede); die;

        $this->request->session()->delete('conditionerss');
        $this->request->session()->write('conditionerss', $results);
        $this->set('students', $results);
    }

    public function dropoutreports()
    {

        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        //pr($this->request->session()->read()); die;
        $this->loadModel('DropOutStudent');

        if ($rolepresent == '5') {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->where(['DropOutStudent.board_id IN' => ['1']])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        } elseif ($rolepresent == '8') {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->where(['DropOutStudent.board_id IN' => ['2', '3']])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        } else {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        }
        $session = $this->request->session();
        
        $studensession = $session->read('students');
       // pr($studensession); die;
        if ($studensession) {

            $this->set('studentsaftersearch', $studensession);
            // $this->set(compact('students'));
        } else {
            $this->set('studentsaftersearch', $studensession);
            $this->set(compact('students'));
        }
        //   $session = $this->request->session();
        //  $studensession = $session->read('students');
        // if ($students) {

        //     $this->set('studentsaftersearch', $students);

        // } else {
        //     $this->set(compact('students'));

        // }

    }

    public function detainedreports()
    {

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $this->loadModel('Studentshistory');

        if ($rolepresent == '5') {

            $students = $this->Studentshistory->find('all')->contain(['Classes', 'Sections'])->where(['Classes.board_id IN' => ['1'], 'Studentshistory.actionhistory' => 'REPEAT'])->order(['Classes.sort' => 'ASC'])->toarray();
        } elseif ($rolepresent == '8') {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->where(['Classes.board_id IN' => ['2', '3'], 'Studentshistory.actionhistory' => 'REPEAT'])->order(['Classes.sort' => 'ASC'])->toarray();
        }

        $session = $this->request->session();
        $studensession = $session->read('conditionerss');
        if ($studensession) {

            $this->set('students', $studensession);
        } else {
            $this->set(compact('students'));
        }
    }

    public function index($id = null)
    {
        $this->viewBuilder()->layout('admin');

        $classes = $this->Classes->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->order(['Classes.sort' => 'ASC'])->toArray();
        //pr($classes); die;
        $this->set(compact('classes'));
    }

    public function finereport()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Fine');
        $session = $this->request->session();
        $u_id = $session->read('Auth.User.id');

        $finede = $this->Fine->find('all')->where(['Fine.user_id' => $u_id])->order(['Fine.id' => 'DESC'])->toarray();
        //pr($finede); die;
        $this->set(compact('finede'));
    }

    public function finesearch()
    {

        $this->loadModel('Fine');
        //pr($this->request->data); die;
        $datefrom = $this->request->data['datefrom'];
        $dateto = $this->request->data['dateto'];
        $hold_id = $this->request->data['holder_type_id'];
        $fine_id = $this->request->data['fine_type'];
        $apk = array();

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $stts = array('Fine.sub_date >=' => $datefrom);
            $apk[] = $stts;
        }

        if (!empty($dateto) && $dateto2 != '1970-01-01') {

            $stts = array('Fine.sub_date <=' => $dateto);
            $apk[] = $stts;
        }
        $session = $this->request->session();
        $u_id = $session->read('Auth.User.id');

        if (!empty($u_id)) {

            $stts = array('Fine.user_id <=' => $u_id);
            $apk[] = $stts;
        }
        if (!empty($hold_id)) {

            $stts = array('Fine.holder_type_id' => $hold_id);
            $apk[] = $stts;
        }

        if (!empty($fine_id)) {

            $stts = array('Fine.fine_type' => $fine_id);
            $apk[] = $stts;
        }

        $this->request->session()->delete('condition');
        $this->request->session()->write('condition', $apk);

        $finede = $this->Fine->find('all')->where($apk)->order(['Fine.id' => 'DESC'])->toarray();
        //pr($finede); die;
        $this->set(compact('finede'));
    }

    public function fine_excel()
    {
        $this->autoRender = false;
        $this->loadModel('Fine');
        $this->loadModel('BookCopyDetail');
        $this->loadModel('Book');
        $cn = $this->request->session()->read('condition');
        $session = $this->request->session();
        $u_id = $session->read('Auth.User.id');
        if ($cn) {

            $finede = $this->Fine->find('all')->where($cn)->order(['Fine.id' => 'DESC'])->toarray();
        } else {
            $finede = $this->Fine->find('all')->where(['Fine.user_id' => $u_id])->order(['Fine.id' => 'DESC'])->toarray();
        }

        ini_set('max_execution_time', 1600);
        $headerRow = array("Book Name", "ASN No.", "Holder Name", "Holder Type.", "Fine Type", "Date", "Amount");
        $output = implode("\t", $headerRow) . "\n";

        foreach ($finede as $people) {
            //pr($people); die;
            $hol = explode('-', $people['holder_name']);
            $asn = $people['asn_no'];
            $bid = $this->BookCopyDetail->find('all')->select(['book_id'])->where(['BookCopyDetail.id' => $asn])->first();
            $biid = $bid['book_id'];
            $bn = $this->Book->find('all')->select(['name'])->where(['Book.id' => $biid])->first();
            $bname = $bn['name'];

            $result = array();
            //pr($people); die;
            $str = "";

            $result[] = $bname;
            $result[] = $people["asn_no"];
            $result[] = $people['holder_name'];

            $result[] = $people["holder_type_id"];
            $result[] = $people["fine_type"];
            $result[] = date('d-m-Y', strtotime($people["sub_date"]));
            $result[] = $people["amount"];
            $am += $people["amount"];

            $output .= implode("\t", $result) . "\n";
        }
        $rrr = array();

        $rrr[] = "";
        $rrr[] = "";
        $rrr[] = "";

        $rrr[] = "";
        $rrr[] = "";
        $rrr[] = "Total";
        $rrr[] = $am;

        $output .= implode("\t", $rrr) . "\n";

        //echo $output; die;
        $filename = "Finereport.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'prospect'));
    }


    // public function overduewithoutfine()
    // {
    //     $this->viewBuilder()->layout('admin');
    //     $conn = ConnectionManager::get('default');
    //     $stmt = $conn->execute("SELECT * FROM idsprime_sanskar.library_issue_books Where  DATE(dep_date) > DATE(DATE_ADD(due_date, INTERVAL 7 DAY))  and asn_no NOT IN (Select asn_no  FROM idsprime_sanskar.library_fines) and holder_type_id='Student'");
    //     $results = $stmt->fetchAll('assoc');

    //     $this->set('results', $results);
    // }

    //Overdue Without Fine Manager
    public function overduewithoutfine()
    {
        $this->viewBuilder()->layout('admin');
        $conn = ConnectionManager::get('default');
        $db = $_SESSION['Auth']['User']['db'];
        $stmt = $conn->execute("SELECT * FROM $db.library_issue_books Where  DATE(due_date) > DATE(DATE_ADD(issue_date, INTERVAL 7 DAY))  and asn_no NOT IN (Select asn_no  FROM $db.library_fines) and holder_type_id='Student'");
        // pr($stmt);die;
        $results = $stmt->fetchAll('assoc');

        $this->set('results', $results);
    }

    public function excelExportIssuedBooks()
    {

        $this->loadModel('Language');

        $session = $this->request->session();
        $issued_books = $session->read('results_issued_books');

        //pr($issued_books); die;
        $this->set('issued_books', $issued_books);

        ini_set('max_execution_time', 1600);
        //~ if($issued_books['0']['holder_type_id']=='Student'){
        //~ $headerRow = array("ASN No", "Book Name", "ISBN No.", "Holder Name", "Holder Type.", "Class-Section", "Language", "Contact No.", "Issue Date", "Due Date", "Duration");
        //~ }else{
        //~ $headerRow = array("ASN No", "Book Name", "ISBN No.", "Holder Name", "Holder Type.", "Language", "Contact No.", "Issue Date", "Due Date", "Duration");
        //~ }
        //~ $output = implode("\t", $headerRow)."\n";

        //~ foreach($issued_books as $service)
        //~ {
        //~ //pr($service); die;
        //~ $cid=$service['class_id'];
        //~ $sid=$service['section_id'];
        //~ $cname=$this->Classes->find('all')->select(['title','id'])->where(['Classes.id' => $cid])->first();
        //~ $sname=$this->Sections->find('all')->select(['title','id'])->where(['Sections.id' => $sid])->first();
        //~ $lan=$this->Language->find('all')->select(['id','language'])->where(['Language.id' => $service['lang']])->first();
        //~ // pr($cname);  pr($sname); die;
        //~ $csname=$cname['title'].' - '.$sname['title'];

        //~ $result=array();
        //~ $d1 = date('d-m-Y',strtotime($service['issue_date']));
        //~ $d2 = date('d-m-Y',strtotime($service['due_date']));

        //~ $result[]=$service['asn_no'];
        //~ $result[]=$service['name'];
        //~ $result[]=$service['ISBN_NO'];

        //~ if(isset($service['holder_id'])){
        //~ if($service['holder_type_id']!='Employee') {

        //~ $stu=$this->Students->find('all')->select(['enroll','fname','middlename','lname'])->where(['Students.enroll' =>$service['holder_id'],'Students.board_id' =>$service['board'],'Students.status'=>'Y'])->first();
        //~ if($stu) {
        //~ $result[]= $stu['enroll'].'-'.$stu['fname'].' '.$stu['middlename'].' '.$stu['lname'];
        //~ }else{
        //~ $result[]= ucfirst($service['holder_name']);
        //~ }  }else{

        //~ $result[]=ucfirst($service['holder_name']);
        //~ } }else{
        //~ $result[]='N/A';}

        //~ $result[]=$service['holder_type_id'];
        //~ if($service['holder_type_id']=='Student'){
        //~ $result[]=$csname;
        //~ }
        //~ $result[]=$lan['language'];
        //~ if($service['holder_type_id']!='Employee'){
        //~ $result[]=$service['sms_mobile'];
        //~ }else{
        //~ $result[]=$service['mobile'];
        //~ }
        //~ $result[]=$d1;
        //~ $result[]=$d2;
        //~ if(!isset($service['status'])) {
        //~ if( !empty( $d1 ) && !empty( $d2 ) )
        //~ {
        //~ if( $service['NumberOfDays'] <= 0 )
        //~ {
        //~ $result[]="Left: ".abs($service['NumberOfDays'])." day(s)";
        //~ }
        //~ else
        //~ {
        //~ $result[]="Overdue: ".$service['NumberOfDays']." day(s)";
        //~ }
        //~ }
        //~ else
        //~ {
        //~ $result[]="N/A";
        //~ }
        //~ } else {
        //~ $result[]="LOST";

        //~ }

        //~ $output .=  implode("\t", $result)."\n";
        //~ }

        //~ $filename = "Issuebookreport-".date('d-m-Y').".xls";
        //~ header('Content-type: application/ms-excel');
        //~ header('Content-Disposition: attachment; filename='.$filename);
        //~ echo $output;die;
    }

    public function prospect($id = null)
    {

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $this->loadModel('Boards');
        $this->loadModel('Applicant');
        $this->viewBuilder()->layout('admin');
        $acd = $this->academicyear();
        $this->set(compact('acd'));
        $rolepresentyear = $this->currentacademicyear();
        $this->set(compact('rolepresentyear'));
        if ($this->request->session()->read('openreg_recipt')) {
            $ids = $this->request->session()->read('openreg_recipt');

            $this->set(compact('ids'));
            $id = $this->request->session()->delete('openreg_recipt');
        }

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5' || $rolepresent == '15') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        if ($rolepresent == '1' || $rolepresent == '6'  || $rolepresent == '105') {
            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N'])->order(['Applicant.id' => 'DESC'])->toarray();
            //pr($t_enquiry);die;
            $this->set(compact('t_enquiry'));
        } elseif ($rolepresent == '5' || $rolepresent == '15') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id' => '1'])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Enquires.p_fees' => ''])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            //pr($t_enquiry); die;

            $this->set(compact('t_enquiry'));
        } elseif ($rolepresent == '8') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id IN' => ['2', '3']])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Enquires.mode1_id IN' => ['2', '3']])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            $this->set(compact('t_enquiry'));
        }
        //pr($t_enquiry);
        $this->request->session()->delete('condition');
        $this->request->session()->write('condition', $t_enquiry);
    }

    public function dropedstudent($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];
        $previous_year = $users['previous_year'];
        $this->set(compact('academicyear'));
        $this->set(compact('previous_year'));
        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);

            $sections = $this->Sections->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
            $this->set('sections', $sections);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $this->request->session()->delete('conditionlp');
        $this->request->session()->write('conditionlp', $classes);
    }

    public function admitstudent($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);

            $sections = $this->Sections->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
            $this->set('sections', $sections);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $this->request->session()->delete('conditionlp');
        $this->request->session()->write('conditionlp', $classes);
    }

    public function prospectussummary($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlprospectus');
        $this->request->session()->write('conditionlprospectus', $classes);
    }

    public function registrationsummary($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlregistration');
        $this->request->session()->write('conditionlregistration', $classes);
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];
        $previous_year = $users['previous_year'];
        $this->set(compact('academicyear'));
        $this->set(compact('previous_year'));
    }

    public function admitstudentcollection($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classes->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlp45');
        $this->request->session()->write('conditionlp45', $classes);

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        $acd = $this->academicyear();

        $this->set(compact('acd'));

        $this->set(compact('rolepresentyear'));
    }

    public function admitstudentcollectiondetail($id = null)
    {
        $this->loadModel('Students');
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlp4511');
        $this->request->session()->write('conditionlp4511', $classes);
    }

    public function registredstudents($id = null)
    {

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $this->loadModel('Boards');
        $this->loadModel('Applicant');
        $this->viewBuilder()->layout('admin');

        if ($this->request->session()->read('openreg_recipt')) {
            $ids = $this->request->session()->read('openreg_recipt');

            $this->set(compact('ids'));
            $id = $this->request->session()->delete('openreg_recipt');
        }

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        if ($rolepresent == '1') {
            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            $this->set(compact('t_enquiry'));
        } elseif ($rolepresent == '5') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id' => '1'])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->where(['Enquires.mode1_id' => 1])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            $this->set(compact('t_enquiry'));
        } elseif ($rolepresent == '8') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id IN' => ['2', '3']])->toArray();

            $this->set('bord', $brhy);

            $t_enquiry = $this->Applicant->find('all')->where(['Enquires.mode1_id IN' => ['2', '3']])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            $this->set(compact('t_enquiry'));
        }
        $acd = $this->academicyear();
        $this->set(compact('acd'));
        $rolepresentyear = $this->currentacademicyear();
        $this->set(compact('rolepresentyear'));
        $this->request->session()->delete('conditionl');
        $this->request->session()->write('conditionl', $t_enquiry);
    }

    public function prospectreport()
    {
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $this->loadModel('Boards');
        $this->loadModel('Applicant');
        $this->viewBuilder()->layout('admin');

        if ($this->request->session()->read('openreg_recipt')) {
            $ids = $this->request->session()->read('openreg_recipt');

            $this->set(compact('ids'));
            $id = $this->request->session()->delete('openreg_recipt');
        }

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        if ($rolepresent == '1') {
            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();

            $this->set('bord', $brhy);
        } elseif ($rolepresent == '5') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id' => '1'])->toArray();

            $this->set('bord', $brhy);
        } elseif ($rolepresent == '8') {

            $brhy = $this->Boards->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Boards.id IN' => ['2', '3']])->toArray();

            $this->set('bord', $brhy);
        }
        $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N'])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
        $this->set(compact('t_enquiry'));
    }

    public function prospectsearch()
    {
        $this->loadModel('Applicant');
        $conn = ConnectionManager::get('default');
        //pr($this->request->data); die;
        $from = date('Y-m-d', strtotime($this->request->data['from']));
        $to = date('Y-m-d', strtotime($this->request->data['to']));
        $bid = $this->request->data['b_id'];
        $class_id = $this->request->data['class_id'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $name = $this->request->data['name'];
        $stat = $this->request->data['s_id'];

        $apk = array();

        if (!empty($stat) && $stat == 1) {
            $st1 = array('Applicant.status' => 'Y');
            $apk[] = $st1;
        } elseif (!empty($stat) && $stat == 2) {
            $st2 = array('Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Applicant.status_i' => 'N');
            $apk[] = $st2;
        } elseif (!empty($stat) && $stat == 3) {
            $st3 = array('Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Applicant.status_i' => 'Y');
            $apk[] = $st3;
        } elseif (!empty($stat) && $stat == 4) {
            $st4 = array('Applicant.status' => 'N', 'Applicant.status_r' => 'Y');
            $apk[] = $st4;
        } elseif (!empty($stat) && $stat == 5) {
            $st5 = array();
            $apk[] = $st5;
        }

        if (!empty($from) && $from != '1970-01-01') {
            $stts = array('Applicant.created >=' => $from);
            $apk[] = $stts;
        }

        if (!empty($to) && $to != '1970-01-01') {
            $stst = array('Applicant.created <=' => $to);
            $apk[] = $stst;
        }

        if (!empty($class_id)) {
            $pii = array('Applicant.class_id' => $class_id);
            $apk[] = $pii;
        }

        if (!empty($bid)) {
            $pii = array('Enquires.mode1_id' => $bid);
            $apk[] = $pii;
        }

        if (!empty($acedmicyear)) {
            $pii = array('Enquires.acedmicyear' => $acedmicyear);
            $apk[] = $pii;
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '5') {
            $pii = array('Applicant.class_id <=' => '22');
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {
            $pii = array('Applicant.class_id >=' => '23');
            $apk[] = $pii;
        }

        $fname = explode(' ', $name);
        if (!empty($name)) {
            $pii = array('Applicant.fname' => $fname[0]);
            $apk[] = $pii;
        }

        $pii = array('Applicant.status' => 'N', 'Applicant.status_r' => 'N');
        $apk[] = $pii;

        $this->request->session()->delete('condition');
        $this->request->session()->write('condition', $apk);
        $classes_data = $this->Applicant->find('all')->order(['Applicant.id' => 'DESC'])->contain(['Enquires'])->where($apk)->toarray();
        //pr($classes_data); die;
        $this->set('t_enquiry', $classes_data);

        $this->request->session()->delete('condition');
        $this->request->session()->write('condition', $classes_data);
    }

    public function registerdsearch()
    {
        $this->loadModel('Applicant');
        $conn = ConnectionManager::get('default');

        $from = date('Y-m-d', strtotime($this->request->data['from']));
        $to = date('Y-m-d', strtotime($this->request->data['to']));
        $bid = $this->request->data['b_id'];
        $class_id = $this->request->data['class_id'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $name = $this->request->data['name'];
        $stat = $this->request->data['s_id'];

        $apk = array();

        if (!empty($from) && $from != '1970-01-01') {
            $stts = array('Applicant.created >=' => $from);
            $apk[] = $stts;
        }

        if (!empty($to) && $to != '1970-01-01') {
            $stst = array('Applicant.created <=' => $to);
            $apk[] = $stst;
        }

        if (!empty($class_id)) {
            $pii = array('Applicant.class_id' => $class_id);
            $apk[] = $pii;
        }

        if (!empty($bid)) {
            $pii = array('Enquires.mode1_id' => $bid);
            $apk[] = $pii;
        }

        if (!empty($acedmicyear)) {
            $pii = array('Enquires.acedmicyear' => $acedmicyear);
            $apk[] = $pii;
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '5') {
            $pii = array('Applicant.class_id <=' => '22');
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {
            $pii = array('Applicant.class_id >=' => '23');
            $apk[] = $pii;
        }

        $fname = explode(' ', $name);
        if (!empty($name)) {
            $pii = array('Applicant.fname' => $fname[0]);
            $apk[] = $pii;
        }

        $piir = array('Enquires.status' => 'Y');
        $apk[] = $piir;
        //$pii=array('Applicant.status' =>'N','Applicant.status_r' =>'N');
        //$apk[]=$pii;

        $classes_data = $this->Applicant->find('all')->order(['Applicant.id' => 'DESC'])->contain(['Enquires'])->where($apk)->toarray();

        $this->set('t_enquiry', $classes_data);

        $this->request->session()->delete('conditionl');
        $this->request->session()->write('conditionl', $classes_data);
    }

    public function prospectussearch()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');
        $academic = $this->request->data['acedmicyear'];
        $from = $this->request->data['from'];
        $to = $this->request->data['to'];

        $class_id = $this->request->data['class_id'];

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $currentyear = $users['academic_year'];
        $this->set(compact('currentyear'));

        if (!empty($from) && $from != '1970-01-01') {
            $from = $from;
            $this->set(compact('from'));
        } else {

            $from = '2000-01-01';
            $this->set('from', '2000-01-01');
        }

        if (!empty($to) && $to != '1970-01-01') {
            $to = date('Y-m-d', strtotime($to . ' +1 day'));
            $to = $to;
            $this->set(compact('to'));
        } else {
            $to = date('Y-m-d');
            $this->set('to', date('Y-m-d'));
        }

        $this->set(compact('academic'));
        $apk = array();

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlprospectus');
        $this->request->session()->write('conditionlprospectus', $classes);

        $this->request->session()->delete('conditionlprospectus1');
        $this->request->session()->write('conditionlprospectus1', $academic);

        $this->request->session()->delete('conditionlprospectus2');
        $this->request->session()->write('conditionlprospectus2', $from);

        $this->request->session()->delete('conditionlprospectus3');
        $this->request->session()->write('conditionlprospectus3', $to);
    }

    public function registrationsearch()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');
        $academic = $this->request->data['acedmicyear'];
        $from = $this->request->data['from'];
        $to = $this->request->data['to'];

        $class_id = $this->request->data['class_id'];

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $currentyear = $users['academic_year'];
        $this->set(compact('currentyear'));

        if (!empty($from) && $from != '1970-01-01') {
            $from = $from;
            $this->set(compact('from'));
        } else {

            $from = '2000-01-01';
            $this->set('from', '2000-01-01');
        }

        if (!empty($to) && $to != '1970-01-01') {
            $to = date('Y-m-d', strtotime($to . ' +1 day'));
            $to = $to;
            $this->set(compact('to'));
        } else {
            $to = date('Y-m-d');
            $this->set('to', date('Y-m-d'));
        }

        $this->set(compact('academic'));
        $apk = array();

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $this->request->session()->delete('conditionlregistration');
        $this->request->session()->write('conditionlregistration', $classes);

        $this->request->session()->delete('conditionlregistration1');
        $this->request->session()->write('conditionlregistration1', $academic);

        $this->request->session()->delete('conditionlregistration2');
        $this->request->session()->write('conditionlregistration2', $from);

        $this->request->session()->delete('conditionlregistration3');
        $this->request->session()->write('conditionlregistration3', $to);
    }
    public function admitsearch()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');
        $academic = $this->request->data['acedmicyear'];
        $from = $this->request->data['from'];
        $to = $this->request->data['to'];

        $class_id = $this->request->data['class_id'];

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $currentyear = $users['academic_year'];
        $this->set(compact('currentyear'));

        if (!empty($from) && $from != '1970-01-01') {
            $from = $from;
            $this->set(compact('from'));
        } else {

            $from = '2000-01-01';
            $this->set('from', '2000-01-01');
        }

        if (!empty($to) && $to != '1970-01-01') {
            $to = date('Y-m-d', strtotime($to . ' +1 day'));
            $to = $to;
            $this->set(compact('to'));
        } else {
            $to = date('Y-m-d');
            $this->set('to', date('Y-m-d'));
        }

        $this->set(compact('academic'));
        $apk = array();

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1', 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3'], 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $this->request->session()->delete('conditionlp');
        $this->request->session()->write('conditionlp', $classes);

        $this->request->session()->delete('conditionlp1');
        $this->request->session()->write('conditionlp1', $academic);

        $this->request->session()->delete('conditionlp2');
        $this->request->session()->write('conditionlp2', $from);

        $this->request->session()->delete('conditionlp3');
        $this->request->session()->write('conditionlp3', $to);
    }

    public function admitcollectionsearch()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');
        $academic = $this->request->data['acedmicyear'];

        $class_id = $this->request->data['class_id'];

        $this->set(compact('academic'));
        $apk = array();

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        $this->request->session()->delete('conditionlp145');
        $this->request->session()->write('conditionlp145', $academic);
    }

    public function admitcollectionsearchdetail()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');

        $academic = $this->request->data['acedmicyear'];

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        //~ $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        //~ $academic = $users['academic_year'];
        $this->set(compact('academic'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
        $this->request->session()->delete('fee_excel_from');
        $this->request->session()->delete('fee_excel_to');
        $this->request->session()->write('fee_excel_from', $datefrom);
        $this->request->session()->write('fee_excel_to', $dateto2);
    }

    public function droppedsearch()
    {
        $this->loadModel('Students');
        $conn = ConnectionManager::get('default');
        $academic = $this->request->data['acedmicyear'];

        $from = $this->request->data['from'];
        $to = $this->request->data['to'];

        $class_id = $this->request->data['class_id'];
        $status_tc = $this->request->data['status_tc'];
        //  pr($academic); die;

        //Print the textual representation of our month
        //out onto the page.

        $this->set(compact('academic', 'from', 'to', 'status_tc'));
        $apk = array();

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5' || $rolepresent == '6') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8' || $rolepresent == '6') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN(' . $stuc . ')'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $this->request->session()->delete('conditionlp2');
        $this->request->session()->write('conditionlp2', $classes);

        $this->request->session()->delete('conditionlp12');
        $this->request->session()->write('conditionlp12', $academic);

        $this->request->session()->delete('conditionlp22');
        $this->request->session()->write('conditionlp22', $from);

        $this->request->session()->delete('conditionlp32');
        $this->request->session()->write('conditionlp32', $to);

        $this->request->session()->delete('conditionlp322');
        $this->request->session()->write('conditionlp322', $status_tc);
    }

    public function findacedemicstudents212($classs = null, $acedmic = null, $from = null, $to = null, $status_tc = null)
    {

        if ($status_tc == "Y") {

            $articles = TableRegistry::get('DropOutStudent');
            return $articles->find('all')->where(['DropOutStudent.class_id' => $classs, 'DropOutStudent.updateacedemic' => $acedmic, 'DATE(DropOutStudent.dropcreated) >=' => $from, 'DATE(DropOutStudent.dropcreated) <=' => $to])->count();
        } else {

            $articles = TableRegistry::get('DropOutStudent');
            return $articles->find('all')->where(['DropOutStudent.class_id' => $classs, 'DropOutStudent.status_tc' => $status_tc, 'DropOutStudent.updateacedemic' => $acedmic, 'DATE(DropOutStudent.dropcreated) >=' => $from, 'DATE(DropOutStudent.dropcreated) <=' => $to])->count();
        }
    }
    public function dropped_excel()
    {
        $this->autoRender = false;
        $this->loadModel('Students');
        $this->loadModel('Boards');
        $cn = $this->request->session()->read('conditionlp2');
        $cn1 = $this->request->session()->read('conditionlp12');
        $cn2 = $this->request->session()->read('conditionlp22');
        $cn3 = $this->request->session()->read('conditionlp32');
        $cn4 = $this->request->session()->read('conditionlp322');
        if ($cn) {
            $classes = $cn;
            $academic = $cn1;
            $from = $cn2;
            $to = $cn3;
            $status_tc = $cn4;
        } else {

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '1' || $rolepresent == '6') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            } elseif ($rolepresent == '5' || $rolepresent == '6') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            } elseif ($rolepresent == '8' || $rolepresent == '6') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            }
        }

        ini_set('max_execution_time', 1600);

        foreach ($academic as $hh => $rg) {
            $aced[] = $rg;
        }
        $headerRow = array("S.No", "Classes");

        $headerRow1 = array_merge($headerRow, $aced);
        $output = implode("\t", $headerRow1) . "\n";

        $cnt = 1;
        $aced = 0;
        $aced1 = 0;

        foreach ($classes as $people => $service) {

            $result = array();

            $str = "";

            $result[] = $cnt++;
            $result[] = $service;

            foreach ($academic as $hh => $rg) {

                $class2014 = $this->findacedemicstudents212($people, $rg, $from, $to, $status_tc);
                $result[] = $class2014;
                if ($rg == '2020-21') {
                    $aced += $class2014;
                } elseif ($rg == '2022-23') {
                    $aced1 += $class2014;
                } else {
                    $acedmic2 += 0;
                }
            }
            $output .= implode("\t", $result) . "\n";
        }

        foreach ($academic as $hh => $rg) {
            if ($rg == '2020-21') {
                $asr[] = $aced;
            } elseif ($rg == '2021-22') {
                $asr[] = $aced1;
            } elseif ($rg == '2022-23') {
                $asr[] = $acedmic2;
            }
        }
        $headerRow31 = array("", "Total");
        $headerRow3 = array_merge($headerRow31, $asr);

        $output .= implode("\t", $headerRow3) . "\n";
        //echo $output; die;
        $filename = "Dropped_Summary_Record.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'admitstudent'));
    }

    public function registerd_excel()
    {
        //$this->autoRender=false;
        $this->loadModel('Applicant');
        $this->loadModel('Boards');
        $cn = $this->request->session()->read('conditionl');
        if ($cn) {

            $t_enquiry = $cn;
        } else {
            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '1') {

                $t_enquiry = $this->Applicant->find('all')->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            } elseif ($rolepresent == '5') {

                $t_enquiry = $this->Applicant->find('all')->where(['Enquires.mode1_id' => 1])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            } elseif ($rolepresent == '8') {

                $t_enquiry = $this->Applicant->find('all')->where(['Enquires.mode1_id IN' => ['2', '3']])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            }
        }

        $this->set('t_enquiry', $t_enquiry);
    }

    public function findacedemicstudents2($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $articles = TableRegistry::get('Students');
        return $articles->find('all')->where(['Students.class_id' => $classs, 'Students.admissionyear' => $acedmic, 'DATE(Students.created) >=' => $from, 'DATE(Students.created) <=' => $to, 'Students.id !=' => '333333'])->count();
    }

    public function findacedemicstudentshis2($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];
        //~ $stts=array('Studentfees.paydate >=' =>$from);
        //~ $apk[]=$stts;

        //~ $stts=array('Studentfees.paydate <=' =>$to);
        //~ $apk[]=$stts;

        //~ $stts=array("Studentfees.quarter LIKE '%Admission Fee%'");
        //~ $apk[]=$stts;

        //~ $stts=array("Studentfees.recipetno !=0");
        //~ $apk[]=$stts;
        //~ $stts=array("Studentfees.status !='N'");
        //~ $apk[]=$stts;

        $nextyear = $getterm['next_academic_year'];

        if ($currentyear == $acedmic) {

            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;
            $stts = array('Students.status' => 'Y');
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');
            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        } elseif ($acedmic != $currentyear) {

            $stts = array('Studentshistory.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Studentshistory.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Studentfees');
            return $articles->find('all')->contain(['Studentshistory'])->where($apk)->order(['Studentfees.id' => 'ASC'])->count();
        } else {

            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;
            $stts = array('Students.status' => 'Y');
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');
            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        }
    }

    public function findacedemicstudents21($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $articles = TableRegistry::get('DropOutStudent');
        return $articles->find('all')->where(['DropOutStudent.class_id' => $classs, 'DropOutStudent.admissionyear' => $acedmic, 'DATE(DropOutStudent.admissiondate) >=' => $from, 'DATE(DropOutStudent.admissiondate) <=' => $to])->count();
    }
    public function findacedemicstudentsdrop2($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];

        $stts = array('DropOutStudent.admissiondate >=' => $from);
        $apk[] = $stts;

        $stts = array('DropOutStudent.admissiondate <=' => $to);
        $apk[] = $stts;

        $stts = array('DropOutStudent.laststudclass' => $classs);
        $apk[] = $stts;

        $stts = array('DropOutStudent.admissionyear' => $acedmic);
        $apk[] = $stts;

        if ($acedmic != $currentyear) {
            $articles = TableRegistry::get('DropOutStudent');
            $ddd = $articles->find('all')->where($apk)->order(['DropOutStudent.id' => 'ASC'])->toarray();

            $cnust = 0;

            foreach ($ddd as $k => $kki) {

                $articles3 = TableRegistry::get('Studentshistory')->find('all')->where(['Studentshistory.stud_id' => $kki['s_id']])->order(['Studentshistory.id' => 'ASC'])->first();
                if ($articles3['id']) {
                } else {
                    $cnust++;
                }
            }
            return $cnust;
        } else {
            $articles = TableRegistry::get('DropOutStudent');
            return $articles->find('all')->where($apk)->order(['DropOutStudent.id' => 'ASC'])->count();
            //pr($Classections21); die;

        }
    }
    public function findacedemicyears()
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Users');

        // Start a new query.

        return $articles->find('all')->where(['Users.id' => '1'])->first()->toArray();
    }

    public function findacedemicstudentrte($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];
        $nextyear = $getterm['next_academic_year'];

        if ($currentyear == $acedmic) {

            $stts = array('Students.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Students.created <=' => $to);
            $apk[] = $stts;

            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Students.category' => 'RTE');
            $apk[] = $stts;

            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');

            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        } elseif ($acedmic != $currentyear) {

            $stts = array('Studentshistory.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Studentshistory.created <=' => $to);
            $apk[] = $stts;

            $stts = array('Studentshistory.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Studentshistory.category' => 'RTE');
            $apk[] = $stts;

            $stts = array('Studentshistory.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Studentshistory');

            return $articles->find('all')->where($apk)->order(['Studentshistory.id' => 'ASC'])->count();
        } else {

            $stts = array('Students.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Students.created <=' => $to);
            $apk[] = $stts;

            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Students.category' => 'RTE');
            $apk[] = $stts;

            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');

            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        }
    }
    public function findacedemicstudentshisa2($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];
        //~ $stts=array('Studentfees.paydate >=' =>$from);
        //~ $apk[]=$stts;

        //~ $stts=array('Studentfees.paydate <=' =>$to);
        //~ $apk[]=$stts;

        //~ $stts=array("Studentfees.quarter LIKE '%Admission Fee%'");
        //~ $apk[]=$stts;

        //~ $stts=array("Studentfees.recipetno !=0");
        //~ $apk[]=$stts;
        //~ $stts=array("Studentfees.status !='N'");
        //~ $apk[]=$stts;

        $nextyear = $getterm['next_academic_year'];

        if ($currentyear == $acedmic) {

            $stts = array('Students.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Students.created <=' => $to);
            $apk[] = $stts;
            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;
            $stts = array('Students.category !=' => 'RTE');
            $apk[] = $stts;

            $stts = array('Students.oldenroll' => '0');
            $apk[] = $stts;
            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');
            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        } elseif ($acedmic != $currentyear) {

            $stts = array('Studentshistory.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Studentshistory.created <=' => $to);
            $apk[] = $stts;

            $stts = array('Studentshistory.class_id' => $classs);
            $apk[] = $stts;

            $stts = array('Studentshistory.admissionyear' => $acedmic);
            $apk[] = $stts;

            $stts = array('Studentshistory.category !=' => 'RTE');
            $apk[] = $stts;

            $stts = array('Studentshistory.oldenroll' => '0');
            $apk[] = $stts;

            $articles = TableRegistry::get('Studentshistory');
            return $articles->find('all')->where($apk)->order(['Studentshistory.id' => 'ASC'])->count();
        } else {

            $stts = array('Students.created >=' => $from);
            $apk[] = $stts;

            $stts = array('Students.created <=' => $to);
            $apk[] = $stts;
            $stts = array('Students.class_id' => $classs);
            $apk[] = $stts;
            $stts = array('Students.category !=' => 'RTE');
            $apk[] = $stts;

            $stts = array('Students.oldenroll' => '0');
            $apk[] = $stts;
            $stts = array('Students.admissionyear' => $acedmic);
            $apk[] = $stts;

            $articles = TableRegistry::get('Students');
            return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
        }
    }
    public function prospectuss_excel()
    {

        $cn = $this->request->session()->read('conditionlprospectus');
        $cn1 = $this->request->session()->read('conditionlprospectus1');
        $cn2 = $this->request->session()->read('conditionlprospectus2');
        $cn3 = $this->request->session()->read('conditionlprospectus3');
        if ($cn) {
            $classes = $cn;
            $academic = $cn1;
            $from = $cn2;
            $to = $cn3;
        } else {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        ini_set('max_execution_time', 1600);

        foreach ($academic as $hh => $rg) {
            $aceds[] = $rg;
        }
        $headerRow = array("S.No", "Classes");

        $headerRow1 = array_merge($headerRow, $aceds);
        $output = implode("\t", $headerRow1) . "\n";

        $cnt = 1;
        $acedl = 0;

        foreach ($classes as $people => $service) {

            $result = array();
            $str = "";
            $result[] = $cnt++;
            $result[] = $service;

            foreach ($academic as $hh => $rg) {
                $class20186 = $this->findprospectussummary($people, $rg, $from, $to);
                $result[] = $class20186;
                $acedl += $class20186;
            }
            $output .= implode("\t", $result) . "\n";
        }

        $asr[] = "(+)" . $acedl;

        $headerRow31 = array("", "Total");
        $headerRow3 = array_merge($headerRow31, $asr);

        $output .= implode("\t", $headerRow3) . "\n";
        //echo $output; die;
        $filename = "Prospectus_Summary_Record.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'prospectussummary'));
    }

    public function registration_excel()
    {

        $cn = $this->request->session()->read('conditionlregistration');
        $cn1 = $this->request->session()->read('conditionlregistration1');
        $cn2 = $this->request->session()->read('conditionlregistration2');
        $cn3 = $this->request->session()->read('conditionlregistration3');
        if ($cn) {
            $classes = $cn;
            $academic = $cn1;
            $from = $cn2;
            $to = $cn3;
        } else {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        ini_set('max_execution_time', 1600);

        foreach ($academic as $hh => $rg) {
            $aceds[] = $rg;
        }
        $headerRow = array("S.No", "Classes");

        $headerRow1 = array_merge($headerRow, $aceds);
        $output = implode("\t", $headerRow1) . "\n";

        $cnt = 1;
        $acedl = 0;

        foreach ($classes as $people => $service) {

            $result = array();
            $str = "";
            $result[] = $cnt++;
            $result[] = $service;

            foreach ($academic as $hh => $rg) {
                $class20186 = $this->findregistrationsummary($people, $rg, $from, $to);
                $result[] = $class20186;
                $acedl += $class20186;
            }
            $output .= implode("\t", $result) . "\n";
        }

        $asr[] = "(+)" . $acedl;

        $headerRow31 = array("", "Total");
        $headerRow3 = array_merge($headerRow31, $asr);

        $output .= implode("\t", $headerRow3) . "\n";
        //echo $output; die;
        $filename = "Registration_Summary_Record.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'registrationsummary'));
    }
    public function findprospectussummary($class_id, $acedmicyear, $datefrom, $dateto2)
    {
        $articles = TableRegistry::get('Enquires');
        // Start a new query.
        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto2));
        return $articles->find('all')->where(['Enquires.class_id' => $class_id, 'Enquires.acedmicyear' => $acedmicyear, 'DATE(Enquires.created) >=' => $datefrom, 'DATE(Enquires.created) <=' => $dateto2, 'Enquires.status' => 'Y', 'Enquires.enquiry_mode' => '2'])->count();
    }

    public function findregistrationsummary($class_id, $acedmicyear, $datefrom, $dateto2)
    {
        $articles = TableRegistry::get('Applicant');
        // Start a new query.
        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto2));
        return $articles->find('all')->contain(['Enquires'])->where(['Applicant.class_id' => $class_id, 'Applicant.acedmicyear' => $acedmicyear, 'Enquires.status' => 'Y', 'DATE(Applicant.created) >=' => $datefrom, 'DATE(Applicant.created) <=' => $dateto2])->count();
    }

    public function admit_excel()
    {
        $this->autoRender = false;
        $this->loadModel('Students');
        $this->loadModel('Boards');
        $cn = $this->request->session()->read('conditionlp');
        $cn1 = $this->request->session()->read('conditionlp1');
        $cn2 = $this->request->session()->read('conditionlp2');
        $cn3 = $this->request->session()->read('conditionlp3');
        if ($cn) {
            $classes = $cn;
            $academic = $cn1;
            $from = $cn2;
            $to = $cn3;
        } else {

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '1') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            } elseif ($rolepresent == '5') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            } elseif ($rolepresent == '8') {
                $classes = $this->Classections->find('list', [
                    'keyField' => 'Classes.id',
                    'valueField' => 'Classes.title',
                ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            }
        }

        ini_set('max_execution_time', 1600);

        foreach ($academic as $hh => $rg) {
            $aceds[] = $rg . "(Admission Student)";
            $aceds[] = $rg . "(RTE Student)";
            $aceds[] = $rg . "(Drop Out Student)";
            $rolepresent = $this->request->session()->read('Auth.User.role_id');

            if ($rolepresent == '5') {
                $aceds[] = $rg . "(Migrated To International)";
                $aceds[] = $rg . "(Migrated From International)";
            } else {
                $aceds[] = $rg . "(Migrated To CBSE)";
                $aceds[] = $rg . "(Migrated From CBSE)";
            }
        }
        $headerRow = array("S.No", "Classes");

        $headerRow1 = array_merge($headerRow, $aceds);
        $output = implode("\t", $headerRow1) . "\n";

        $cnt = 1;
        $acedl = 0;
        $aced1 = 0;
        $aced2 = 0;
        $aced3 = 0;
        $aced4 = 0;

        foreach ($classes as $people => $service) {

            $result = array();

            $str = "";

            $result[] = $cnt++;
            $result[] = $service;

            foreach ($academic as $hh => $rg) {
                $class20186 = $this->findacedemicstudentshisa2($people, $rg, $from, $to);
                $result[] = $class20186;

                $acedl += $class20186;

                $class2014rte = $this->findacedemicstudentrte($people, $rg, $from, $to);
                $result[] = $class2014rte;

                $aced1 += $class2014rte;

                $class2014 = $this->findacedemicstudentsdrop2($people, $rg, $from, $to);
                $result[] = $class2014;

                $aced2 += $class2014;

                $servicess12 = $this->findacedemicstudentshisa21($people, $rg, $from, $to);

                $result[] = $servicess12;
                $aced3 += $servicess12;

                $servicess123 = $this->findacedemicstudentshisa213($people, $rg, $from, $to);

                $result[] = $servicess123;
                $aced4 += $servicess123;
            }
            $output .= implode("\t", $result) . "\n";
        }

        $asr[] = "(+)" . $acedl;
        $asr[] = "(+)" . $aced1;
        $asr[] = "(+)" . $aced2;
        $asr[] = "(-)" . $aced3;
        $asr[] = "(+)" . $aced4;

        $sum = $acedl - $aced3;
        $ss = $aced1 + $aced2 + $sum + $aced4;
        $ss2 = $aced1 + $sum + $aced4;
        $asr[] = "Net Total Admission :" . $ss;
        $asr[] = "Active Admission :" . $ss2;

        $headerRow31 = array("", "Total");
        $headerRow3 = array_merge($headerRow31, $asr);

        $output .= implode("\t", $headerRow3) . "\n";
        //echo $output; die;
        $filename = "Admission_Summary_Record.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'admitstudent'));
    }

    public function findacedemicstudentshisa213($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];

        $nextyear = $getterm['next_academic_year'];
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {
            $stts = array('Students.board_id !=' => 1);
            $apk[] = $stts;
        } else {

            $stts = array('Students.board_id' => 1);
            $apk[] = $stts;
        }

        $stts = array('Students.created >=' => $from);
        $apk[] = $stts;

        $stts = array('Students.created <=' => $to);
        $apk[] = $stts;

        $stts = array('Students.category' => 'Migration');
        $apk[] = $stts;

        $stts = array('Students.admissionyear' => $acedmic);
        $apk[] = $stts;
        $stts = array('Students.status' => 'N');
        $apk[] = $stts;

        $articles = TableRegistry::get('Students');
        $ddd = $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->toarray();

        $cnust = 0;
        foreach ($ddd as $k => $kki) {

            if ($nextyear == $acedmic) {

                $articles3 = TableRegistry::get('Students')->find('all')->where(['oldenroll' => $kki['enroll'], 'class_id' => $classs, 'admissionyear' => $acedmic])->order(['id' => 'ASC'])->first();
            } elseif ($acedmic != $currentyear) {

                $articles3 = TableRegistry::get('Studentshistory')->find('all')->where(['oldenroll' => $kki['enroll'], 'class_id' => $classs, 'admissionyear' => $acedmic])->order(['id' => 'ASC'])->first();
            } else {
                $articles3 = TableRegistry::get('Students')->find('all')->where(['oldenroll' => $kki['enroll'], 'class_id' => $classs, 'admissionyear' => $acedmic])->order(['id' => 'ASC'])->first();
            }

            if ($articles3['id']) {
                $cnust++;
            } else {
            }
        }

        return $cnust;
    }

    public function findacedemicstudentshisa21($classs = null, $acedmic = null, $from = null, $to = null)
    {

        $getterm = $this->findacedemicyears('1');
        $currentyear = $getterm['academic_year'];

        $stts = array('Students.created >=' => $from);
        $apk[] = $stts;

        $stts = array('Students.created <=' => $to);
        $apk[] = $stts;

        $stts = array('Students.class_id' => $classs);
        $apk[] = $stts;

        $stts = array('Students.category' => 'Migration');
        $apk[] = $stts;

        $stts = array('Students.admissionyear' => $acedmic);
        $apk[] = $stts;
        $stts = array('Students.status' => 'N');
        $apk[] = $stts;

        $articles = TableRegistry::get('Students');

        return $articles->find('all')->where($apk)->order(['Students.id' => 'ASC'])->count();
    }
    public function prospect_excel()
    {
        $this->autoRender = false;
        $this->loadModel('Applicant');
        $this->loadModel('Boards');
        $cn = $this->request->session()->read('condition');
        if ($cn) {

            $t_enquiry = $cn;
        } else {
            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '1' || $rolepresent == '6') {

                $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N'])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            } elseif ($rolepresent == '5') {

                $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Enquires.mode1_id' => 1])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            } elseif ($rolepresent == '8') {

                $t_enquiry = $this->Applicant->find('all')->where(['Applicant.status' => 'N', 'Applicant.status_r' => 'N', 'Enquires.mode1_id IN' => ['2', '3']])->contain(['Enquires'])->order(['Applicant.id' => 'DESC'])->toarray();
            }
        }

        ini_set('max_execution_time', 1600);
        $headerRow = array("S.No", "Form No", "Academic Year", "Pupil Name", "Father Mobile", "Mother Mobile", "Class", "Board", "Added On", "Status");
        $output = implode("\t", $headerRow) . "\n";

        $cnt = 1;
        foreach ($t_enquiry as $people) {

            $st1 = $people['status'];
            $st2 = $people['status_i'];
            $st3 = $people['status_r'];
            if ($st1 == 'Y') {
                $stat = "Approved";
            } elseif ($st2 == 'Y') {
                $stat = "Invited";
            } elseif ($st3 == 'Y') {
                $stat = "Rejected";
            } elseif ($st1 == 'N' && $st2 == 'N' && $st3 == 'N') {
                $stat = "Pending";
            }

            $cid = $people["class_id"];
            $class1 = $this->Classes->find('all')->where(['Classes.id' => $cid])->select(['title'])->order(['Classes.id' => 'DESC'])->first()->toarray();
            $bid = $people["enquire"]["mode1_id"];
            $bord = $this->Boards->find('all')->where(['Boards.id' => $bid])->select(['name'])->order(['Boards.id' => 'DESC'])->first()->toarray();
            //pr($people); die;

            $result = array();
            //pr($people); die;
            $str = "";

            $result[] = $cnt++;
            $result[] = $people["sno"];
            $result[] = $people["acedmicyear"];
            $result[] = $people["fname"] . ' ' . $people["middlename"] . ' ' . $people["lname"];
            $result[] = $people["f_phone"];
            $result[] = $people["m_phone"];

            $result[] = $class1["title"];
            $result[] = $bord["name"];
            $result[] = date('Y-m-d', strtotime($people["created"]));
            $result[] = $stat;

            $output .= implode("\t", $result) . "\n";
        }
        //echo $output; die;
        $filename = "Registration_Record.xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;

        $this->redirect(array('action' => 'prospect'));
    }

    public function invite()
    {
        $this->loadModel('Applicant');
        $id = $this->request->data['fetch'];
        $classes_data = $this->Applicant->find('all')->select(['sno', 'status_i'])->where(['Applicant.sno' => $id])->first()->toarray();

        echo $classes_data['status_i'];
        die;
    }

    public function approvedprospectus($sno = null)
    {

        //~ $conn = ConnectionManager::get('default');

        //~ $st="Y";
        //~ $detail1='UPDATE `applicants` SET `status` ="'.$st.'" WHERE `applicants`.`sno` = "'.$sno.'"';

        //~ $results2 = $conn->execute($detail1);

        return $this->redirect(['controller' => 'Students', 'action' => 'add/' . $sno]);
    }
    public function prospect_interaction()
    {
        //pr($this->request->data); die;
        $this->loadModel('Interaction');
        //pr($this->request->data); die;
        if ($this->request->data['Invite'] == 'Invite') {

            $time = $this->request->data['inter_time'];
            $t1 = explode('-', $time);
            $y1 = date('Y-m-d h:i:s', strtotime($t1[0]));
            $y2 = date('Y-m-d h:i:s', strtotime($t1[1]));

            //pr($this->request->data); die;

            $romm = sizeof($this->request->data['p_id']);

            for ($i = 0; $i < $romm; $i++) {
                $conn = ConnectionManager::get('default');
                $pros = $this->request->data['p_id'][$i];

                $userTable = TableRegistry::get('Applicant');
                $exists = $userTable->exists(['sno' => $pros, 'status_i' => "Y"]);
                if ($exists) {

                    $this->Flash->error(__("One of Your Prospectus is already Invited."));

                    return $this->redirect(['action' => 'prospect']);
                } else {

                    $query = "INSERT INTO `prospect_interactions`(`enquiry_id`,`i_stime`,`i_etime`) VALUES ('$pros','$y1','$y2')";
                    $results = $conn->execute($query);
                    $conn = ConnectionManager::get('default');
                    $st = "Y";
                    $detail = 'UPDATE `applicants` SET `status_i` ="' . $st . '" WHERE `applicants`.`sno` = "' . $pros . '"';

                    $results1 = $conn->execute($detail);
                }
            }
        } elseif ($this->request->data['Approve'] == 'Approve') {
            $romm = sizeof($this->request->data['p_id']);
            for ($i = 0; $i < $romm; $i++) {
                $conn = ConnectionManager::get('default');
                $pros = $this->request->data['p_id'][$i];
                $st = "Y";
                $detail1 = 'UPDATE `applicants` SET `status` ="' . $st . '" WHERE `applicants`.`sno` = "' . $pros . '"';
                //echo $detail1; die;
                $results2 = $conn->execute($detail1);
            }
        } else {
            $romm = sizeof($this->request->data['p_id']);
            for ($i = 0; $i < $romm; $i++) {
                $conn = ConnectionManager::get('default');
                $pros = $this->request->data['p_id'][$i];
                $st = "Y";
                $detail2 = 'UPDATE `applicants` SET `status_r` ="' . $st . '" WHERE `applicants`.`sno` = "' . $pros . '"';
                //echo $detail2; die;
                $results3 = $conn->execute($detail2);
            }
        }
        return $this->redirect(['action' => 'prospect']);
        //pr($this->request->data); die;

    }
    public function prospect_supportiv()
    {

        $this->autoRender = false;
        $conn = ConnectionManager::get('default');
        $detail = "SELECT Enquires.id,Enquires.s_name,Enquires.mobile,Enquires.username,Enquires.next_followup_date,Enquires.created,Enquires.mode1_id,Enquires.p_fees,Enquires.p_fees,Enquires.status,Enquires.enquiry,Enquires.mode_id,Enquires.acedmicyear,Enquires.class_id,Enquires.admissionyear, Modes.name as modename, Boards.name as boardname,  Classes.title as classtitle  FROM `enquires` Enquires LEFT JOIN classes Classes ON Enquires.`class_id` = Classes.id LEFT JOIN modes Modes ON Enquires.`mode_id` = Modes.id LEFT JOIN boards Boards ON Enquires.`mode1_id` = Boards.id WHERE  1=1 ";
        $cond = ' ';
        $session = $this->request->session();
        $from = $session->read('from');
        $to = $session->read('to');
        if (!empty($from || $to)) {

            $cond .= " AND (Enquires.created   >='" . $from . "' AND Enquires.created  <='" . $to . "')";
        }

        $bid = $session->read('bid');

        if (!empty($bid)) {

            $cond .= " AND Enquires.mode1_id LIKE '" . $bid . "%' ";
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
        $output .= '"Name",';
        $output .= '"Mobile",';
        $output .= '"Class",';
        $output .= '"Mode",';
        $output .= '"Board",';
        $output .= '"Prospectus Fee",';
        $output .= '"Follow Up Date",';
        $output .= '"Enquiry Date",';
        $output .= '"Remark",';
        $output .= "\n";

        foreach ($resultss as $people) {
            $str = "";
            $output .= $people["s_name"] . ",";
            $output .= $people["mobile"] . ",";
            $output .= $people["classtitle"] . ",";
            $output .= $people["modename"] . ",";
            $output .= $people["boardname"] . ",";
            $output .= $people["p_fees"] . ",";
            $output .= date('d-m-Y', strtotime($people['next_followup_date'])) . ",";
            $output .= date('d-m-Y', strtotime($people['created'])) . ",";
            $output .= $people["enquiry"] . ",";

            $output .= "\r\n";
        }

        //echo $output;    die;
        $filename = "Enquiry.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;

        die;
        $this->redirect($this->referer());
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
        $session->delete('status');
        $session->delete('acedmicyear');
        $session->delete('name');

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
        $cond .= " AND Enquires.enquiry_mode = '1'";
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
        $cond .= " AND Enquires.enquiry_mode = '1'";
        $name = $session->read('name');
        if (!empty($name)) {

            $cond .= " AND Enquires.s_name  LIKE  '" . $name . "%' || Enquires.username  LIKE  '" . $name . "%' || Enquires.id  LIKE  '" . $name . "%' ";
        }

        $detail = $detail . $cond;
        $resultss = $conn->execute($detail)->fetchAll('assoc');

        //pr($resultss); die;

        $output = "";
        $output .= '"Enquiry Date",';

        $output .= '"Name",';
        $output .= '"Mobile",';
        $output .= '"Academic Year",';
        $output .= '"Class",';

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

            $output .= $people["s_name"] . ",";
            $output .= $people["mobile"] . ",";
            $output .= $people["acedmicyear"] . ",";
            $output .= $people["classtitle"] . ",";

            $output .= $people["next_followup_date"] . ",";
            $output .= $status . ",";
            $output .= "\r\n";
        }

        //echo $output;    die;
        $filename = "Enquiry Report-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;

        die;
        $this->redirect($this->referer());
    }

    // Follow Up Index

    public function followup()
    {
        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classections.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toArray();
        //    pr($classes); die;
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

            $cond .= " AND temp.l_follow_date LIKE '" . $from . "%' ";
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
        //print_r($results); die;
        $this->set('f_enquiry', $results);
    }

    public function dropoutreport()
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('DropOutStudent');
        $academicyear = $this->DropOutStudent->find()->select(['DropOutStudent.acedmicyear'])->group(['DropOutStudent.acedmicyear'])->toarray();
        //pr($academicyear); die;
        $academic = array();
        foreach ($academicyear as $key => $value) {

            $academic[$value['acedmicyear']] = $value['acedmicyear'];
        }

        $this->set('academic1', $academic);

        $sections = $this->Sections->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['title' => 'ASC'])->toArray();

        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        // pr($rolepresent);die;

        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        // For view listing

        $this->loadModel('DropOutStudent');

        if ($rolepresent == '5') {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->where(['DropOutStudent.board_id IN' => ['1']])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        } elseif ($rolepresent == '8') {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->where(['DropOutStudent.board_id IN' => ['2', '3']])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        } else {

            $students = $this->DropOutStudent->find('all')->contain(['Classes', 'Sections'])->order(['DropOutStudent.dropcreated' => 'DESC'])->toarray();
        }

        $this->set(compact('sections', 'classes', 'students'));
       // pr($students); die;

    }
    public function detainedreport()
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Studentshistory');
        $academicyear = $this->Studentshistory->find('all')->select(['Studentshistory.acedmicyear'])->group(['Studentshistory.acedmicyear'])->toarray();
        //pr($academicyear); die;
        $academic = array();
        foreach ($academicyear as $key => $value) {

            $academic[$value['acedmicyear']] = $value['acedmicyear'];
        }

        $this->set('academic1', $academic);

        $sections = $this->Sections->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['title' => 'ASC'])->toArray();

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        // For view listing

        $this->loadModel('Studentshistory');

        if ($rolepresent == '5') {

            $students = $this->Studentshistory->find('all')->contain(['Classes', 'Sections'])->where(['Studentshistory.board_id IN' => ['1'], 'Studentshistory.actionhistory' => 'REPEAT'])->order(['Classes.sort' => 'ASC'])->toarray();
        } elseif ($rolepresent == '8') {

            $students = $this->Studentshistory->find('all')->contain(['Classes', 'Sections'])->where(['Studentshistory.board_id IN' => ['2', '3'], 'Studentshistory.actionhistory' => 'REPEAT'])->order(['Classes.sort' => 'ASC'])->toarray();
        }

        $this->set(compact('sections', 'classes', 'students'));
        //pr($academic); die;

    }

    public function sms_delivered_student_search()
    {

        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');

        $sms_temp_id = $this->request->data['sms_temp_id'];

        if ($this->request->data['fdatefrom'] != '1970-01-01' && $this->request->data['fdatefrom'] != '') {
            $fdatefrom = date('Y-m-d', strtotime($this->request->data['fdatefrom']));
        } else {

            $fdatefrom = '0';
        }

        if (!empty($sms_temp_id) && $fdatefrom != '0') {

            $students = $this->Smsdeliverydetails->find('all')->select(['Students.fname', 'Students.middlename', 'Students.lname', 'Students.class_id', 'Students.section_id', 'Students.enroll', 'Students.fathername', 'Students.sms_mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Students'])->where(['Smsmanager.status' => 'Y', 'Smsdeliverydetails.sms_temp_id' => $sms_temp_id, 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'S', 'DATE(Smsdeliverydetails.created)' => $fdatefrom])->order(['Smsdeliverydetails.id' => 'DESC', 'Students.fname' => 'ASC'])->toarray();
        } elseif ($fdatefrom != '0') {

            $students = $this->Smsdeliverydetails->find('all')->select(['Students.fname', 'Students.middlename', 'Students.lname', 'Students.class_id', 'Students.section_id', 'Students.enroll', 'Students.fathername', 'Students.sms_mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Students'])->where(['Smsmanager.status' => 'Y', 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'S', 'DATE(Smsdeliverydetails.created)' => $fdatefrom])->order(['Smsdeliverydetails.id' => 'DESC', 'Students.fname' => 'ASC'])->toarray();
        } elseif (!empty($sms_temp_id)) {

            $students = $this->Smsdeliverydetails->find('all')->select(['Students.fname', 'Students.middlename', 'Students.lname', 'Students.class_id', 'Students.section_id', 'Students.enroll', 'Students.fathername', 'Students.sms_mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Students'])->where(['Smsmanager.status' => 'Y', 'Smsdeliverydetails.type' => 'S', 'Smsdeliverydetails.sms_temp_id' => $sms_temp_id, 'Smsdelivery.status' => 'Y'])->order(['Smsdeliverydetails.id' => 'DESC', 'Students.fname' => 'ASC'])->toarray();
        }

        $this->set(compact('students'));
    }
    public function sms_delivered_staff_search()
    {

        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');

        $sms_temp_id = $this->request->data['sms_temp_id'];

        if ($this->request->data['fdatefrom'] != '1970-01-01' && $this->request->data['fdatefrom'] != '') {
            $fdatefrom = date('Y-m-d', strtotime($this->request->data['fdatefrom']));
        } else {

            $fdatefrom = '0';
        }

        if (!empty($sms_temp_id) && $fdatefrom != '0') {

            $employees = $this->Smsdeliverydetails->find('all')->select(['Employees.id', 'Employees.fname', 'Employees.middlename', 'Employees.lname', 'Employees.id', 'Employees.f_h_name', 'Employees.mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Employees'])->where(['Smsmanager.status' => 'Y', 'Smsdeliverydetails.sms_temp_id' => $sms_temp_id, 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'E', 'DATE(Smsdeliverydetails.created)' => $fdatefrom])->order(['Smsdeliverydetails.id' => 'DESC', 'Employees.fname' => 'ASC'])->toarray();
        } elseif ($fdatefrom != '0') {

            $employees = $this->Smsdeliverydetails->find('all')->select(['Employees.id', 'Employees.fname', 'Employees.middlename', 'Employees.lname', 'Employees.f_h_name', 'Employees.mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Employees'])->where(['Smsmanager.status' => 'Y', 'Smsdeliverydetails.type' => 'E', 'Smsdelivery.status' => 'Y', 'DATE(Smsdeliverydetails.created)' => $fdatefrom])->order(['Smsdeliverydetails.id' => 'DESC', 'Employees.fname' => 'ASC'])->toarray();
        } elseif (!empty($sms_temp_id)) {

            $employees = $this->Smsdeliverydetails->find('all')->select(['Employees.id', 'Employees.fname', 'Employees.middlename', 'Employees.lname', 'Employees.f_h_name', 'Employees.mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Employees'])->where(['Smsmanager.status' => 'Y', 'Smsdeliverydetails.sms_temp_id' => $sms_temp_id, 'Smsdeliverydetails.type' => 'E', 'Smsdelivery.status' => 'Y'])->order(['Smsdeliverydetails.id' => 'DESC', 'Employees.fname' => 'ASC'])->toarray();
        }

        $this->set(compact('employees'));
    }
    public function smsreport()
    {
        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');
        $this->viewBuilder()->layout('admin');

        $smsmanager = $this->Smsmanager->find('all')->select(['id', 'category'])->where(['status' => 'Y', 'sms_for IN' => ['S', 'B']])->order(['category' => 'ASC'])->toArray();

        $students = $this->Smsdeliverydetails->find('all')->select(['Students.fname', 'Students.middlename', 'Students.lname', 'Students.class_id', 'Students.section_id', 'Students.enroll', 'Students.fathername', 'Students.sms_mobile', 'Smsmanager.category', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Students'])->where(['Smsmanager.status' => 'Y', 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'S', 'Smsmanager.sms_for IN' => ['S', 'B']])->order(['Smsdeliverydetails.id' => 'DESC', 'Students.fname' => 'ASC'])->limit('100')->toarray();

        $this->set(compact('students', 'smsmanager'));
    }
    public function staff_smsreport()
    {
        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');
        $this->loadModel('Employees');

        $this->viewBuilder()->layout('admin');

        $smsmanager = $this->Smsmanager->find('all')->select(['id', 'category'])->where(['status' => 'Y', 'sms_for IN' => ['E', 'B']])->order(['category' => 'ASC'])->toArray();

        $employees = $this->Smsdeliverydetails->find('all')->select(['Employees.fname', 'Employees.middlename', 'Employees.lname', 'Employees.id', 'Employees.f_h_name', 'Employees.mobile', 'Smsmanager.category', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Employees'])->where(['Smsmanager.status' => 'Y', 'Smsdelivery.status' => 'Y', 'Smsmanager.sms_for IN' => ['E', 'B'], 'Smsdeliverydetails.type' => 'E'])->order(['Smsdeliverydetails.id' => 'DESC', 'Employees.fname' => 'ASC'])->toarray();

        $this->set(compact('employees', 'smsmanager'));
    }

    public function smsdeliverdreports()
    {
        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');
        $this->viewBuilder()->layout('admin');
        $session = $this->request->session();
        $studensession = $session->read('studentsgh');
        if ($studensession) {

            $this->set('students', $studensession);
        } else {

            $students = $this->Smsdeliverydetails->find('all')->select(['Students.fname', 'Students.middlename', 'Students.lname', 'Students.class_id', 'Students.section_id', 'Students.enroll', 'Students.fathername', 'Students.sms_mobile', 'Smsmanager.category', 'Smsdelivery.message', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Students'])->where(['Smsmanager.status' => 'Y', 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'S'])->order(['Smsdeliverydetails.id' => 'DESC', 'Students.fname' => 'ASC'])->toarray();

            $this->set(compact('students'));
        }
    }

    public function smsstaffdeliverdreports()
    {
        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');
        $this->loadModel('Smsdeliverydetails');
        $this->viewBuilder()->layout('admin');
        $session = $this->request->session();
        $employeesty = $session->read('employeesty');
        if ($employeesty) {

            $this->set('employees', $employeesty);
        } else {

            $employees = $this->Smsdeliverydetails->find('all')->select(['Employees.id', 'Employees.fname', 'Employees.middlename', 'Employees.lname', 'Employees.f_h_name', 'Employees.mobile', 'Smsmanager.category', 'Smsdeliverydetails.created', 'Smsdeliverydetails.status', 'Smsdeliverydetails.smsmobile'])->contain(['Smsdelivery', 'Smsmanager', 'Employees'])->where(['Smsmanager.status' => 'Y', 'Smsdelivery.status' => 'Y', 'Smsdeliverydetails.type' => 'E', 'Smsmanager.sms_for IN' => ['E', 'B']])->order(['Smsdeliverydetails.id' => 'DESC', 'Employees.fname' => 'ASC'])->limit('100')->toarray();

            $this->set(compact('employees'));
        }
    }
    // Follow Up
    public function rtestudent()
    {
        $this->request->session()->delete('search_rte');
        $this->request->session()->delete('rtestud');
        $this->loadModel('Sections');
        $this->loadModel('Classes');
        $this->viewBuilder()->layout('admin');
        //show all data in listing page
        $sections = $this->Sections->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['title' => 'ASC'])->toArray();

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $this->set(compact('sections', 'houses'));

        if ($id) {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['class_id' => $id, 'section_id' => $section, 'category' => 'RTE', 'Students.status' => 'Y'])->order(['Students.id' => 'DESC'])->toarray();
            $this->set('students', $student_data);
        } else {
            //$student_data = $this->Students->find('all')->contain(['Classes','Sections'])->order(['Students.id' => 'DESC'])->toarray();

            $student_data = $this->Students->find('all')->where(['category' => 'RTE', 'status' => 'Y'])->order(['Students.id' => 'DESC'])->toarray();
            $this->set('students', $student_data);
        }
    }

    public function optionalsubjectlist()
    {
        $this->viewBuilder()->layout('admin');
        $id = array('12', '13', '15', '17', '20', '22', '6');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $this->loadModel('Subjectclass');
        // $classes = $this->Classections->find('list', [
        //     'keyField' => 'Classes.id',
        //     'valueField' => 'Classes.title',
        // ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN' => $id])->order(['Classes.sort' => 'ASC'])->toArray();
        // $this->set('classes', $classes);
        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);

        $sections = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
        $this->set('sections', $sections);

        $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['class_id IN' => $id, 'Students.status' => 'Y', 'Students.acedmicyear' => $acedmic])->order(['Classes.sort' => 'ASC'])->order(['Sections.title' => 'ASC'])->order(['Students.fname' => 'ASC'])->order(['Students.lname' => 'ASC'])->toarray();
        $this->set('students', $student_data);

        $opt_sub = $this->Subjectclass->find('list', ['keyField' => 'Subjects.id', 'valueField' => 'Subjects.name'])->contain(['Subjects'])->where(['Subjectclass.is_optional' => '1'])->order(['Subjects.name' => 'ASC'])->toarray();

        //pr($opt_sub); die;
        $this->set('opt_sub', $opt_sub);
    }
    public function searchrte()
    {

        //show all data in listing page
        //connection
        $conn = ConnectionManager::get('default');

        $year = $this->request->data['acedmicyear'];
        $class = $this->request->data['class_id'];
        $admission = $this->request->data['admissionyear'];
        $section = $this->request->data['section_id'];
        $enroll = $this->request->data['enroll'];
        $fname = $this->request->data['fname'];

        $detail = "SELECT Students.id,Students.enroll,Students.fathername,Students.category,Students.sms_mobile,Students.fname,Students.mobile,Students.acedmicyear,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  Students.`category` = 'RTE' and 1=1 ";

        $cond = ' ';

        if (!empty($class)) {

            $cond .= " AND Students.class_id LIKE '" . $class . "' ";
        }

        if (!empty($section)) {

            $cond .= " AND Students.section_id LIKE '" . $section . "%' ";
        }

        if (!empty($enroll)) {

            $cond .= " AND Students.enroll LIKE '" . $enroll . "' ";
        }

        if (!empty($fname)) {

            $cond .= " AND UPPER(Students.fname) LIKE '" . strtoupper($fname) . "%' ";
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $cond .= " AND Students.board_id IN ('1')";
        } elseif ($rolepresent == '8') {

            $cond .= " AND Students.board_id IN ('2','3')";
        }
        $cond .= " AND Students.status = 'Y'";
        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY Students.id ASC";

        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->request->session()->write('search_rte', $results);
        $this->set('students', $results);
    }

    public function makingcardreport()
    {

        $this->loadModel('Students');

        $order = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.rf_id' => '0', 'Students.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toarray();
        if ($order != '') {

            $colNames = array_keys();
            $output = "";
            $output .= '"S.No",';
            $output .= '"Signature",';
            $output .= '"Web-URL",';
            $output .= '"Sch. No.",';
            $output .= '"Class",';
            $output .= '"Section",';
            $output .= '"First Name",';
            $output .= '"Father Name ",';
            $output .= '"Mother Name ",';
            $output .= '"Date of Birth(DD-MM-YY)",';
            $output .= '"Contact no",';
            $output .= '"Address",';

            $output .= "\n";
            $counter = 1;
            foreach ($order as $people) {

                if ($people['class_id']) {

                    $s_id = $people['class_id'];
                    $c_id = $people['section_id'];

                    $str = "";

                    $output .= $counter++ . ",";

                    if ($s_id == '23' || $s_id == '24' || $s_id == '25' || $s_id == '26' || $s_id == '27') {
                        $output .= "Mrs.Girdhar,";
                        $output .= "www.sanskarjpr.com,";
                    } else {

                        $output .= "Mrs.Neelam,";
                        $output .= "www.sanskarjaipur.com,";
                    }
                    $output .= $people["enroll"] . ",";
                    $output .= $people["class"]["title"] . ",";
                    $output .= $people["section"]["title"] . ",";
                    $output .= $people["fname"] . " " . $people["middlename"] . " " . $people["lname"] . ",";

                    $output .= $people["fathername"] . ",";
                    $output .= $people["mothername"] . ",";
                    $output .= date('d-m-Y', strtotime($people["dob"])) . ",";
                    $output .= $people["sms_mobile"] . ",";
                    $people['address'] = str_replace(",", "", $people['address']);
                    $result = str_replace(" ", " ", $people['address']);

                    $output .= $result . ",";
                    $output .= "\r\n";
                }
            }
            //echo $output; die;
            $filename = "MakingID_CARD." . date("d-m-Y") . ".xls";
            header('Content-type: application/xls');
            header('Content-Disposition: attachment; filename=' . $filename);
            echo $output;
            die;
        }
        $this->request->session()->delete('rtestud');
        $this->redirect(array('action' => 'rtestudent'));
    }

    public function absentstudents($class = null, $section = null)
    {

        $this->loadModel('Students');

        if ($class && $section) {

            $da = date('Y-m-d');

            $this->set(compact('class_id'));
            $this->set(compact('section_id'));

            if (!empty($class)) {
                $pii = array('Studattends.class_id' => $class);
                $apk[] = $pii;
            }

            if (!empty($section)) {
                $pii = array('Studattends.section_id' => $section);
                $apk[] = $pii;
            }

            $pii = array('Studattends.date' => $da);
            $apk[] = $pii;

            $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
            $acedmic = $users['academic_year'];
            $pii = array('Studattends.acedemic' => $acedmic);
            $pisi = array('Studattends.status' => 'A');
            $apk[] = $pii;
            $apk[] = $pisi;

            $order = $this->Studattends->find('all')->contain(['Students'])->where($apk)->order(['Students.fname' => 'ASC'])->toarray();

            if ($order) {

                $colNames = array_keys();
                $output = "";
                $output .= '"S.No",';
                $output .= '"Sch. No.",';
                $output .= '"Student Name",';
                $output .= '"Class",';
                $output .= '"Section",';
                $output .= '"Father Name ",';
                $output .= '"Mobile No.",';

                $output .= "\n";
                $counter = 1;
                foreach ($order as $people) {

                    if ($people['student']['class_id']) {

                        $s_id = $people['student']['class_id'];
                        $c_id = $people['student']['section_id'];

                        $str = "";

                        $output .= $counter++ . ",";

                        $output .= $people['student']["enroll"] . ",";
                        $output .= $people['student']["fname"] . " " . $people['student']["middlename"] . " " . $people['student']["lname"] . ",";
                        $classes = $this->Classes->find('all')->where(['Classes.id' => $people['student']["class_id"]])->first();
                        $ctitle = $classes['title'];
                        $section = $this->Sections->find('all')->where(['Sections.id' => $people['student']["section_id"]])->first();
                        $stitle = $section['title'];
                        $output .= $ctitle . ",";
                        $output .= $stitle . ",";

                        $output .= $people['student']["fathername"] . ",";
                        $output .= $people['student']["sms_mobile"] . ",";

                        $output .= "\r\n";
                    }
                }
                //echo $output; die;
                $classes = $this->Classes->find('all')->where(['Classes.id' => $order[0]['student']["class_id"]])->first();
                $ctitle = $classes['title'];
                $section = $this->Sections->find('all')->where(['Sections.id' => $order[0]['student']["section_id"]])->first();
                $stitle = $section['title'];
                if ($ctitle && $stitle) {

                    $filename = "AbsentReport." . date("d-m-Y_H:i:s_A") . "-" . $ctitle . "-" . $stitle . ".xls";
                } else {

                    $filename = "AbsentReport." . date("d-m-Y_H:i:s_A") . ".xls";
                }
                header('Content-type: application/xls');
                header('Content-Disposition: attachment; filename=' . $filename);
                echo $output;
                die;
            } else {

                $connss = ConnectionManager::get('default');

                $studentrfsidsd = $connss->execute("SELECT * FROM `students`, `classes` WHERE students.class_id =
				classes.id AND students.id
				NOT IN  (SELECT students.id FROM `attendreports` LEFT JOIN students
				ON attendreports.rfid = students.rf_id LEFT JOIN classes ON
				students.class_id = classes.id  WHERE students.rf_id !='0' AND
				students.status='Y' AND students.class_id ='" . $class . "' AND
				students.section_id ='" . $section . "' AND
				DATE(attendreports.resultdate)='" . date('Y-m-d') . "' GROUP BY
				attendreports.rfid  ORDER BY section_id ASC) AND students.class_id
				='" . $class . "' AND students.section_id ='" . $section . "' ORDER BY
				classes.sort ASC");

                $attendenceentry2 = $studentrfsidsd->fetchAll('assoc');

                $colNames = array_keys();
                $output = "";
                $output .= '"S.No",';
                $output .= '"Sch. No.",';
                $output .= '"Student Name",';
                $output .= '"Class",';
                $output .= '"Section",';
                $output .= '"Father Name ",';
                $output .= '"Mobile No.",';

                $output .= "\n";
                $counter = 1;
                foreach ($attendenceentry2 as $peoples) {

                    if ($peoples['class_id']) {

                        $s_id = $peoples['class_id'];
                        $c_id = $peoples['section_id'];

                        $str = "";

                        $output .= $counter++ . ",";

                        $output .= $peoples["enroll"] . ",";
                        $output .= $peoples["fname"] . " " . $peoples["middlename"] . " " . $peoples["lname"] . ",";
                        $classes = $this->Classes->find('all')->where(['Classes.id' => $peoples["class_id"]])->first();
                        $ctitle = $classes['title'];
                        $section = $this->Sections->find('all')->where(['Sections.id' => $peoples["section_id"]])->first();
                        $stitle = $section['title'];
                        $output .= $ctitle . ",";
                        $output .= $stitle . ",";

                        $output .= $peoples["fathername"] . ",";
                        $output .= $peoples["sms_mobile"] . ",";

                        $output .= "\r\n";
                    }
                }
                //echo $output; die;

                $classes = $this->Classes->find('all')->where(['Classes.id' => $attendenceentry2[0]["class_id"]])->first();
                $ctitle = $classes['title'];
                $section = $this->Sections->find('all')->where(['Sections.id' => $attendenceentry2[0]["section_id"]])->first();
                $stitle = $section['title'];
                if ($ctitle && $stitle) {

                    $filename = "AbsentReport." . date("d-m-Y_H:i:s_A") . "-" . $ctitle . "-" . $stitle . ".xls";
                } else {

                    $filename = "AbsentReport." . date("d-m-Y_H:i:s_A") . ".xls";
                }
                header('Content-type: application/xls');
                header('Content-Disposition: attachment; filename=' . $filename);
                echo $output;
                die;
            }
        } else {
            $connss = ConnectionManager::get('default');
            $allabsent = $connss->execute("SELECT * FROM `tmp_allattendence` ORDER BY
				sort,section_id ASC");

            $order = $allabsent->fetchAll('assoc');

            if ($order != '') {

                $colNames = array_keys();
                $output = "";
                $output .= '"S.No",';
                $output .= '"Sch. No.",';
                $output .= '"Class",';
                $output .= '"Section",';
                $output .= '"First Name",';
                $output .= '"Father Name ",';
                $output .= '"Mobile No.",';

                $output .= "\n";
                $counter = 1;
                foreach ($order as $people) {

                    if ($people['class_id']) {

                        $s_id = $people['class_id'];
                        $c_id = $people['section_id'];

                        $str = "";

                        $output .= $counter++ . ",";
                        $studentall = $this->studentreports($people['st_id'], $people['class_id'], $people['section_id']);

                        $output .= $studentall["enroll"] . ",";

                        $classes = $this->Classes->find('all')->where(['Classes.id' => $studentall["class_id"]])->first();
                        $ctitle = $classes['title'];
                        $section = $this->Sections->find('all')->where(['Sections.id' => $studentall["section_id"]])->first();
                        $stitle = $section['title'];
                        $output .= $ctitle . ",";
                        $output .= $stitle . ",";

                        $output .= $studentall["fname"] . " " . $studentall["middlename"] . " " . $studentall["lname"] . ",";

                        $output .= $studentall["fathername"] . ",";
                        $output .= $studentall["sms_mobile"] . ",";

                        $output .= "\r\n";
                    }
                }
                //echo $output; die;
                $filename = "AllClassesAbsentReport." . date("d-m-Y") . ".xls";
                header('Content-type: application/xls');
                header('Content-Disposition: attachment; filename=' . $filename);
                echo $output;
                die;
            }
        }
    }

    public function studentreports($id = null, $classs = null, $sectionid = null)
    {

        $articles = TableRegistry::get('Students');
        return $articles->find('all')->where(['Students.id' => $id, 'Students.class_id' => $classs, 'Students.section_id' => $sectionid, 'Students.status' => 'Y'])->first();
    }

    public function absentwithoutcardreport($class = null, $section = null)
    {

        $this->loadModel('Students');
        $connss = ConnectionManager::get('default');

        if ($class && $section) {
            $studentrfidsd = $connss->execute("SELECT *,sections.title AS section_t,classes.title AS classes_t FROM students LEFT JOIN classes ON students.class_id = classes.id LEFT JOIN sections ON students.section_id = sections.id WHERE students.rf_id !='0'  AND students.id  NOT IN (SELECT students.id AS studid FROM `attendreports` LEFT JOIN students ON attendreports.rfid = students.rf_id LEFT JOIN classes ON students.class_id = classes.id  WHERE DATE(resultdate)='" . date('Y-m-d') . "' AND students.rf_id !='0' GROUP BY attendreports.rfid) AND students.class_id ='" . $class . "' AND students.section_id ='" . $section . "' AND students.status = 'Y' ORDER BY fname ASC,section_id ASC");
        } else {

            $studentrfidsd = $connss->execute("SELECT *,sections.title AS section_t,classes.title AS classes_t FROM students LEFT JOIN classes ON students.class_id = classes.id LEFT JOIN sections ON students.section_id = sections.id WHERE students.rf_id !='0'  AND students.id  NOT IN (SELECT students.id AS studid FROM `attendreports` LEFT JOIN students ON attendreports.rfid = students.rf_id LEFT JOIN classes ON students.class_id = classes.id  WHERE DATE(resultdate)='" . date('Y-m-d') . "' AND students.status = 'Y' AND students.rf_id !='0' GROUP BY attendreports.rfid)  ORDER BY sort ASC,section_id ASC");
        }
        $order = $studentrfidsd->fetchAll('assoc');

        if ($order != '') {

            $colNames = array_keys();
            $output = "";
            $output .= '"S.No",';
            $output .= '"Sch. No.",';
            $output .= '"Class",';
            $output .= '"Section",';
            $output .= '"First Name",';
            $output .= '"Father Name ",';

            $output .= "\n";
            $counter = 1;
            foreach ($order as $people) {

                if ($people['class_id']) {

                    $s_id = $people['class_id'];
                    $c_id = $people['section_id'];

                    $str = "";

                    $output .= $counter++ . ",";

                    $output .= $people["enroll"] . ",";
                    $output .= $people["classes_t"] . ",";
                    $output .= $people["section_t"] . ",";
                    $output .= $people["fname"] . " " . $people["middlename"] . " " . $people["lname"] . ",";

                    $output .= $people["fathername"] . ",";

                    $output .= "\r\n";
                }
            }
            //echo $output; die;
            if ($class && $section) {
                $filename = "WithoutID_CARD." . date("d-m-Y") . "-" . $order[0]["classes_t"] . "-" . $order[0]["section_t"] . ".xls";
            } else {

                $filename = "WithoutID_CARD." . date("d-m-Y") . ".xls";
            }
            header('Content-type: application/xls');
            header('Content-Disposition: attachment; filename=' . $filename);
            echo $output;
            die;
        }
        $this->request->session()->delete('rtestud');
        $this->redirect(array('action' => 'rtestudent'));
    }

    public function optstudent_excel($class_id = null, $section_id = null, $opt_subject = null)
    {
        $this->loadModel('Students');

        $id = array('12', '13', '15', '17', '20', '22');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];

        if ($opt_subject && $class_id && $section_id) {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.class_id' => $class_id, 'Students.status' => 'Y', 'Students.section_id' => $section_id, 'FIND_IN_SET(\'' . $opt_subject . '\',Students.opt_sid)', 'Students.acedmicyear' => $acedmic])->order(['Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();

            $this->set('opt_subject', $opt_subject);
        } elseif ($class_id && $section_id) {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.class_id' => $class_id, 'Students.status' => 'Y', 'Students.section_id' => $section_id, 'Students.acedmicyear' => $acedmic])->order(['Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();
        } else {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['class_id IN' => $id, 'Students.status' => 'Y', 'Students.acedmicyear' => $acedmic])->order(['Classes.sort' => 'ASC', 'Sections.id' => 'ASC', 'Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();
        }
        $this->set('student_data', $student_data);
    }
    public function findsubjectsubs2($sub)
    {
        // pr($weekday); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Subjects');

        // Start a new query.

        return $articles->find('all')->select(['name'])->where(['Subjects.id' => $sub])->first();
    }

    public function rtestudent_excel()
    {
        $this->loadModel('Students');

        // $stud = $this->request->session()->read('search_rte');
        // if()

        $session = $this->request->session();

        $order = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.category' => 'RTE', 'Students.status' => 'Y'])->order(['Students.id' => 'DESC'])->toarray();
        // $this->set('order', $order);

        $studensessionrte = $session->read('search_rte');
        if ($studensessionrte) {
            $this->set('order', $studensessionrte);
        } else {
            $this->set('order', $order);
            //$this->set(compact('students'));
        }

        //         if ($stud == '') {
        //             $order = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.category' => 'RTE', 'Students.status' => 'Y'])->order(['Students.id' => 'DESC'])->toarray();
        //             $colNames = array_keys();
        //             $output = "";
        //             $output .= 'Sr.No.,';
        //             $output .= 'Student Name,';
        //             $output .= 'Fathers Name ,';
        //             $output .= 'Class,';
        //             $output .= 'Section,';
        //             $output .= 'Disc. Scheme,';
        //             $output .= "\n";

        //             foreach ($order as $people) {

        //                 $str = "";

        //                 $people["fname"] = str_replace(' ', '_', $people["fname"]);
        //                 $people["fathername"] = str_replace(' ', '_', $people["fathername"]);
        //                 $output .= $people["enroll"] . ",";
        //                 $output .= $people["fname"] . ",";
        //                 $output .= $people["fathername"] . ",";
        //                 $output .= $people["class"]["title"] . ",";
        //                 $output .= $people["section"]["title"] . ",";
        //                 $output .= $people["category"] . ",";

        //                 $output .= "\r\n";
        //             }
        // //echo $output; die;
        //             $filename = "Rte_Studentslist.xls";
        //             header('Content-type: application/xls');
        //             header('Content-Disposition: attachment; filename=' . $filename);
        //             echo $output;
        //             die;
        //         } else {
        //             $order = $this->request->session()->read('rtestud');
        //             $colNames = array_keys();
        //             $output = "";
        //             $output .= '"Sr.No.",';
        //             $output .= '"Student Name",';
        //             $output .= '"Fathers Name ",';
        //             $output .= '"Class",';
        //             $output .= '"Section",';
        //             $output .= '"Disc. Scheme",';
        //             $output .= "\n";

        //             foreach ($order as $people) {

        //                 $str = "";
        //                 $people["fname"] = str_replace(' ', '_', $people["fname"]);
        //                 $people["fathername"] = str_replace(' ', '_', $people["fathername"]);

        //                 $output .= $people["enroll"] . ",";
        //                 $output .= $people["fname"] . ",";
        //                 $output .= $people["fathername"] . ",";
        //                 $output .= $people["classtitle"] . ",";
        //                 $output .= $people["sectiontitle"] . ",";
        //                 $output .= $people["category"] . ",";

        //                 $output .= "\r\n";
        //             }
        // //echo $output; die;
        //             $filename = "Rte_Studentslist.xls";
        //             header('Content-type: application/xls');
        //             header('Content-Disposition: attachment; filename=' . $filename);
        //             echo $output;
        //             die;
        //         }
        //         $this->request->session()->delete('rtestud');
        //         $this->redirect(array('action' => 'rtestudent'));
    }

    public function user_supportiv2()
    {

        $this->autoRender = false;
        //    pr($results); die;
        $session = $this->request->session();
        $conn = ConnectionManager::get('default');
        $results = $session->read('f_enquiry');
        $output = "";
        $output .= '"Next Follow-up Date",';
        $output .= '"Last Follow-up Date",';
        $output .= '"Enquiry Date",';
        $output .= '"Name ",';
        $output .= '"Mobile",';

        $output .= '"Class",';
        $output .= '"Response",';
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
            $output .= date('d-m-Y', strtotime($people['l_follow_date'])) . ",";
            $output .= date('d-m-Y', strtotime($people['add_date'])) . ",";

            $output .= $people["s_name"] . ",";
            $output .= $people["mobile"] . ",";
            $output .= $class . ",";
            $output .= $people['f_responce'] . ",";
            $output .= $status . ",";

            $output .= "\r\n";
        }

        $filename = "Follow Up-" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;

        die;
        $this->redirect($this->referer());
    }

    // For Students

    public function student()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('DiscountCategory');
        $this->loadModel('Houses');
        $this->loadModel('Classections');
        $this->loadModel('Sections');



        $acd = $this->academicyear();
        $this->set(compact('acd'));
        $rolepresentyear = $this->currentacademicyear();
        $this->set(compact('rolepresentyear'));
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $this->request->session()->write('alldatass');
        $houses = $this->Houses->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['Houses.status' => '1'])->order(['Houses.id' => 'ASC'])->toArray();
        $this->set('houses', $houses);

        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status IN ' => ['Y', '1']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);

            $sections = $this->Sections->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
            $this->set('sections', $sections);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);

            $sections = $this->Classections->find('list', [
                'keyField' => 'Sections.id',
                'valueField' => 'Sections.title',
            ])->contain(['Sections'])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
            $this->set('sections', $sections);

            $discountcate = $this->DiscountCategory->find('list')->where(['DiscountCategory.status' => 'Y'])->order(['DiscountCategory.id' => 'ASC'])->toArray();
            $this->set('discountcate', $discountcate);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);

            $sections = $this->Classections->find('list', [
                'keyField' => 'Sections.id',
                'valueField' => 'Sections.title',
            ])->contain(['Sections'])->where(['Sections.status' => '1'])->order(['Sections.id' => 'ASC'])->toArray();
            $this->set('sections', $sections);

            $discountcate = $this->DiscountCategory->find('list')->where(['DiscountCategory.status' => 'Y'])->order(['DiscountCategory.id' => 'ASC'])->toArray();
            $this->set('discountcate', $discountcate);
        }

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $acedmsic = $user['academic_year'];
        $this->set('acedmsic', $acedmsic);
    }

    // For Students
    public function search3()
    {
        $session = $this->request->session();
        $conn = ConnectionManager::get('default');
        $admissionyear = $this->request->data['admissionyear'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $h_id = $this->request->data['h_id'];
        $is_special = $this->request->data['is_special'];
        $discountcategory = $this->request->data['discountcategory'];
        $category = $this->request->data['category'];

        $name = $this->request->data['name'];
        $ids = $this->request->data['ids'];

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));

        $dateto = date('Y-m-d', strtotime($this->request->data['dateto']));

        $personal = implode(",", $this->request->data['selectField']);

        $session->delete('category');
        $session->delete('is_special');
        $session->delete('datefrom');
        $session->delete('dateto');
        $session->delete('admissionyear');
        $session->delete('personal');
        $session->delete('alldata');
        $array = $this->request->data['selectField'];
        $this->set('allarray', $array);
        $session->write('personal', $personal);
        $session->write('alldata', $this->request->data['selectField']);
        $detail = "SELECT " . `echo $personal` . ", s.middlename,s.lname FROM `students` s, classes c  ,sections sec  WHERE s.class_id=c.id AND s.section_id=sec.id";

        $cond = ' ';

        $session->delete('acedmicyear');

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $session->write('datefrom', $datefrom);
            $cond .= " AND DATE(s.created) >='" . $datefrom . "'";
        }

        if (!empty($dateto) && $dateto != '1970-01-01') {

            $session->write('dateto', $dateto);
            $cond .= " AND DATE(s.created) <='" . $dateto . "'";
        }

        if (!empty($acedmicyear)) {
            $session->write('acedmicyear', $acedmicyear);
            $cond .= " AND s.acedmicyear ='" . $acedmicyear . "' ";
        }
        if (!empty($is_special)) {
            $session->write('is_special', $is_special);
            $cond .= " AND s.is_special ='" . $is_special . "' ";
        }

        if (!empty($category)) {
            $session->write('category', $category);
            $cond .= " AND s.category ='" . $category . "' ";
        }
        if (!empty($admissionyear)) {
            $session->write('admissionyear', $admissionyear);
            $cond .= " AND s.admissionyear ='" . $admissionyear . "' ";
        }

        $session->delete('class_id');
        $session->delete('h_id');

        if (!empty($h_id)) {
            $session->write('h_id', $h_id);
            foreach ($h_id as $ggs => $rts) {
                $condss[] = "'" . $rts . "'";
            }
            $stucs = implode(',', $condss);
            $cond .= " AND s.h_id IN(" . $stucs . ")";
        }

        $session->delete('discountcategory');

        if (!empty($discountcategory)) {
            $session->write('discountcategory', $discountcategory);
            foreach ($discountcategory as $sggs => $srts) {
                $condssj[] = "'" . $srts . "'";
            }
            $stucskh = implode(',', $condssj);
            $cond .= " AND s.discountcategory IN(" . $stucskh . ")";
        }
        if (!empty($class_id)) {
            $session->write('class_id', $class_id);
            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND s.class_id IN(" . $stuc . ")";
        }
        $session->delete('section_id');
        if (!empty($section_id)) {
            $session->write('section_id', $section_id);
            foreach ($section_id as $gsg => $rts) {
                $consds[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $consds);
            $cond .= " AND s.section_id IN(" . $stusc . ")";
        }

        $session->delete('name');
        if (!empty($name)) {
            $session->write('name', $name);
            $cond .= " AND  (s.fname  LIKE  '" . trim($name) . "%' OR s.lname  LIKE  '" . trim($name) . "%' OR s.username  LIKE '" . trim($name) . "%' OR s.mobile  LIKE '" . trim($name) . "' OR s.sms_mobile  LIKE '" . trim($name) . "' OR s.f_phone  LIKE '" . trim($name) . "' OR s.m_phone  LIKE '" . trim($name) . "')";
        }

        $session->delete('ids');
        if (!empty($ids)) {
            $session->write('ids', $ids);
            $cond .= " AND s.enroll ='" . $ids . "' ";
        }

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $cond .= " AND s.board_id IN ('1')";
        } elseif ($rolepresent == '8') {

            $cond .= " AND s.board_id IN ('2','3')";
        }
        $cond .= " AND s.status ='Y'";

        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY c.sort ASC, sec.title ASC, s.fname ASC ,s.middlename ASC ,s.lname ASC";

        $resul = $conn->execute($SQL)->fetchAll('assoc');
        $session->write('alldatass');
        $session->write('alldatass', $resul);
        $this->set('s_enquiry', $resul);
    }

    public function findhouse($id = null)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Houses');

        // Start a new query.

        return $articles->find('all')->where(['Houses.id' => $id])->first();
    }
    public function finddisability($id = null)
    {
        $articles = TableRegistry::get('Disabilitys');
        return $articles->find()->select(['name'])->where(['id' => $id, 'status' => 'Y'])->first();
    }

    public function showadmissionclasstitle($id = null)
    {
        $articles = TableRegistry::get('AdmissionClasses');
        return $articles->find()->select(['AdmissionClasses.title'])->where(['AdmissionClasses.id' => $id])->first();
    }
    public function user_supportiv3()
    {

        $this->viewBuilder()->layout('admin');
        $session = $this->request->session();
        $conn = ConnectionManager::get('default');
        $personal = $session->read('personal');
        $s_data = $session->read('alldata');

        $datefrom = $session->read('datefrom');
        $dateto = $session->read('dateto');
        $category = $session->read('category');
        $is_special = $session->read('is_special');

        $detail = "SELECT " . `echo $personal` . ", s.middlename,s.lname FROM `students` s, classes c ,sections sec  WHERE s.class_id=c.id AND s.section_id=sec.id";
        $cond = ' ';

        $acedmicyear = $session->read('acedmicyear');
        $admissionyear = $session->read('admissionyear');

        if (!empty($acedmicyear)) {

            $cond .= " AND s.acedmicyear ='" . $acedmicyear . "'";
        }

        if (!empty($category)) {

            $cond .= " AND s.category ='" . $category . "'";
        }

        if (!empty($is_special)) {

            $cond .= " AND s.is_special ='" . $is_special . "'";
        }
        if (!empty($admissionyear)) {

            $cond .= " AND s.admissionyear ='" . $admissionyear . "'";
        }

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $cond .= " AND DATE(s.created) >='" . $datefrom . "'";
        }

        if (!empty($dateto) && $dateto != '1970-01-01') {

            $cond .= " AND DATE(s.created) <='" . $dateto . "'";
        }

        $class_id = $session->read('class_id');
        $section_id = $session->read('section_id');
        $h_id = $session->read('h_id');
        $discountcategory = $session->read('discountcategory');

        if (!empty($discountcategory)) {

            foreach ($discountcategory as $sggs => $srts) {
                $condssj[] = "'" . $srts . "'";
            }
            $stucskh = implode(',', $condssj);
            $cond .= " AND s.discountcategory IN(" . $stucskh . ")";
        }

        if (!empty($h_id)) {

            foreach ($h_id as $ggs => $rts) {
                $condss[] = "'" . $rts . "'";
            }
            $stucs = implode(',', $condss);
            $cond .= " AND s.h_id IN(" . $stucs . ")";
        }

        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND s.class_id IN(" . $stuc . ")";
        }
        if (!empty($section_id)) {

            foreach ($section_id as $gg => $rts) {
                $condss[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $condss);
            $cond .= " AND s.section_id IN(" . $stusc . ")";
        }
        $name = $session->read('name');
        if (!empty($name)) {

            $cond .= " AND  (s.fname  LIKE  '" . trim($name) . "%' OR s.lname  LIKE  '" . trim($name) . "%' OR s.username  LIKE '" . trim($name) . "%' OR s.mobile  LIKE '" . trim($name) . "' OR s.sms_mobile  LIKE '" . trim($name) . "' OR s.f_phone  LIKE '" . trim($name) . "' OR s.m_phone  LIKE '" . trim($name) . "')";
        }

        $ids = $session->read('ids');
        if (!empty($ids)) {

            $cond .= " AND s.enroll ='" . $ids . "' ";
        }
        $cond .= " AND s.status ='Y'";

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $cond .= " AND s.board_id IN ('1')";
        } elseif ($rolepresent == '8') {

            $cond .= " AND s.board_id IN ('2','3')";
        }

        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY c.sort ASC, sec.title ASC, s.fname ASC ,s.middlename ASC ,s.lname";
        $resul = $conn->execute($SQL)->fetchAll('assoc');

        $this->set('resul', $resul);

        /*

                $output="";

                pr($resul); die;

                ini_set('max_execution_time', 1600);

                $headerRow1s[] ="Sr.No.";

                if(array_key_exists('enroll',$resul[0])) { $headerRow1s[] ="Scholar.No.";  }
                if(array_key_exists('fname',$resul[0])) { $headerRow1s[] ="Name";  }
                if(array_key_exists('gender',$resul[0])) { $headerRow1s[] ="Gender"; }
                if(array_key_exists('dob',$resul[0])) { $headerRow1s[] ="DOB"; }
                if(array_key_exists('height',$resul[0]))  { $headerRow1s[] ="Height"; }
                if(array_key_exists('weight',$resul[0]))  { $headerRow1s[] ="Weight"; }

                if(array_key_exists('adaharnumber',$resul[0]))  { $headerRow1s[] ="Adaharnumber"; }
                if(array_key_exists('cast',$resul[0]))  { $headerRow1s[] ="Cast"; }
                if(array_key_exists('religion',$resul[0]))  { $headerRow1s[] ="Religion"; }

                if(array_key_exists('category',$resul[0]))  { $headerRow1s[] ="Category"; }
                if(array_key_exists('bloodgroup',$resul[0]))  { $headerRow1s[] ="Bloodgroup"; }
                if(array_key_exists('disability',$resul[0]))  { $headerRow1s[] ="Disability"; }
                if(array_key_exists('mother_tounge',$resul[0]))  { $headerRow1s[] ="MotherTounge"; }
                if(array_key_exists('address',$resul[0]))  { $headerRow1s[] ="Address"; }

                if(array_key_exists('mobile',$resul[0])) { $headerRow1s[] ="Mobile"; }

                if(array_key_exists('sms_mobile',$resul[0])) { $headerRow1s[] ="SMSMobile"; }
                if(array_key_exists('f_phone',$resul[0])) { $headerRow1s[] ="FatherPhone"; }
                if(array_key_exists('m_phone',$resul[0])) { $headerRow1s[] ="MotherPhone"; }
                if(array_key_exists('admissionyear',$resul[0])) { $headerRow1s[] ="AdmissionYear"; }
                if(array_key_exists('acedmicyear',$resul[0])) { $headerRow1s[] ="AcademicYear"; }
                if(array_key_exists('created',$resul[0])) { $headerRow1s[] ="AdmissionDate"; }
                if(array_key_exists('formno',$resul[0])) { $headerRow1s[] ="FormNo."; }

                if(array_key_exists('board_id',$resul[0])) { $headerRow1s[] ="Board"; }
                if(array_key_exists('admissionclass',$resul[0]))  { $headerRow1s[] ="Admission Class"; }
                if(array_key_exists('class_id',$resul[0]))  { $headerRow1s[] ="Class"; }
                if(array_key_exists('section_id',$resul[0])) {  $headerRow1s[] ="Section"; }
                if(array_key_exists('h_id',$resul[0])) {  $headerRow1s[] ="House"; }

                if(array_key_exists('discountcategory',$resul[0])) { $headerRow1s[] ="Discount"; }
                if(array_key_exists('is_lc',$resul[0])) { $headerRow1s[] ="IsLearningCenter"; }
                if(array_key_exists('is_special',$resul[0])) { $headerRow1s[] ="IsSpecial"; }

                if(array_key_exists('fathername',$resul[0]))  { $headerRow1s[] ="FatherName"; }
                if(array_key_exists('mothername',$resul[0]))  { $headerRow1s[] ="MotherName"; }
                if(array_key_exists('fee_submittedby',$resul[0]))  { $headerRow1s[] ="FeeSubmittedBy"; }
                if(array_key_exists('f_qualification',$resul[0]))  { $headerRow1s[] ="FatherQualification"; }
                if(array_key_exists('m_qualification',$resul[0]))  { $headerRow1s[] ="MotherQualification"; }
                if(array_key_exists('f_occupation',$resul[0]))  { $headerRow1s[] ="FatherOccupation"; }
                if(array_key_exists('m_occupation',$resul[0]))  { $headerRow1s[] ="MotherOccupation"; }

                $headerRow1=implode(",", $headerRow1s);
                $headerRow1=array($headerRow1);

                $output.= implode("\t", $headerRow1)."\n";

                $counter=1;
                foreach($resul as $yu=>$people) {
                $classewrw=$this->findclassess($people['class_id']);
                $admissionclassclassewrw=$this->showadmissionclasstitle($people['admissionclass']);
                $admissioncls=$admissionclassclassewrw['title'];
                $cls=$classewrw[0]['title'];
                $sect=$this->sections($people['section_id']);
                $sections=$sect[0]['title'];
                $h_id =$people['h_id'];
                $hid=$this->findhouse($h_id);
                $house_id=$this->findhouse($people['house_id']);
                if($hid){ $house=$hid['name']; }else if($house_id){  $house=$house_id['name'];  }
                $totalsum=0;

                $result=array();
                $result[]=$counter;
                if(array_key_exists('enroll',$resul[0])){
                $result[]=$people['enroll'];

                }

                if(array_key_exists('fname',$resul[0])){
                $result[]=$people['fname']." ".$people['middlename']." ".$people['lname'];

                }

                if(array_key_exists('gender',$resul[0])){
                $result[]=$people['gender'];

                }
                if(array_key_exists('dob',$resul[0])){
                $result[]=date('d-m-Y',strtotime($people['dob']));

                }
                if(array_key_exists('height',$resul[0])){

                if($people['height']){
                $result[]=$people['height'];

                }else{
                $result[]='--';

                }

                }

                if(array_key_exists('weight',$resul[0])){

                if($people['weight']){
                $result[]=$people['weight'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('adaharnumber',$resul[0])){

                if($people['adaharnumber']){
                $result[]=$people['adaharnumber'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('cast',$resul[0])){

                if($people['cast']){
                $result[]=$people['cast'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('religion',$resul[0])){

                if($people['religion']){
                $result[]=$people['religion'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('category',$resul[0])){

                if($people['category']){
                $result[]=$people['category'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('bloodgroup',$resul[0])){

                if($people['bloodgroup']){
                $result[]=$people['bloodgroup'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('disability',$resul[0])){

                if($people['disability']){

                $disablity=$this->Comman->finddisability($people['disability']);
                $result[]=$disablity['name'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('mother_tounge',$resul[0])){

                if($people['mother_tounge']){
                $result[]=$people['mother_tounge'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('address',$resul[0])){

                if($people['address']){

                $people['address']=str_replace(",","-",$people['address']);
                $people['address']=str_replace(" ","-",$people['address']);
                $people['address']=str_replace("-"," ",$people['address']);
                $people['address']=str_replace("--"," ",$people['address']);
                $result[]=$people['address'];
                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('mobile',$resul[0])){

                if($people['mobile']){
                $result[]=$people['mobile'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('sms_mobile',$resul[0])){

                if($people['sms_mobile']){
                $result[]=$people['sms_mobile'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('f_phone',$resul[0])){

                if($people['f_phone']){
                $result[]=$people['f_phone'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('m_phone',$resul[0])){

                if($people['m_phone']){
                $result[]=$people['m_phone'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('admissionyear',$resul[0])){
                $result[]=$people['admissionyear'];

                }

                if(array_key_exists('acedmicyear',$resul[0])){
                $result[]=$people['acedmicyear'];

                }

                if(array_key_exists('created',$resul[0])){
                $result[]=date('d-m-Y',strtotime($people['created']));

                }

                if(array_key_exists('formno',$resul[0])){

                if($people['formno']){
                $result[]=$people['formno'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('board_id',$resul[0])){
                if($people['board_id']=='1'){

                $result[]= "CBSE"; }else if($service['board_id']=='2'){

                $result[]= "CAMBRIDGE"; } else if($people['board_id']=='3'){

                $result[]= "IBDP"; }

                }

                if(array_key_exists('admissionclass',$resul[0])){
                $result[]=$admissioncls;

                }
                if(array_key_exists('class_id',$resul[0])){
                $result[]=$cls;

                }
                if(array_key_exists('section_id',$resul[0])){
                $result[]=$sections;

                }
                if(array_key_exists('h_id',$resul[0])){

                if($house){
                $result[]=$house;

                }else{
                $result[]='--';

                }

                }
                if(array_key_exists('discountcategory',$resul[0])){

                if($people['discountcategory']){
                $result[]=$people['discountcategory'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('is_lc',$resul[0])){

                if($people['is_lc']){
                $result[]=$people['is_lc'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('is_special',$resul[0])){

                if($people['is_special']){
                $result[]=$people['is_special'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('fathername',$resul[0])){

                if($people['fathername']){
                $result[]=$people['fathername'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('mothername',$resul[0])){

                if($people['mothername']){
                $result[]=$people['mothername'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('fee_submittedby',$resul[0])){

                if($people['fee_submittedby']){
                $result[]=$people['fee_submittedby'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('f_qualification',$resul[0])){

                if($people['f_qualification']){

                $people['f_qualification']=str_replace(",","-",$people['f_qualification']);
                $result[]=$people['f_qualification'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('m_qualification',$resul[0])){

                if($people['m_qualification']){
                $people['m_qualification']=str_replace(",","-",$people['m_qualification']);
                $result[]=$people['m_qualification'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('f_occupation',$resul[0])){

                if($people['f_occupation']){

                $people['f_occupation']=str_replace(",","-",$people['f_occupation']);
                $result[]=$people['f_occupation'];

                }else{

                $result[]='--';
                }

                }

                if(array_key_exists('m_occupation',$resul[0])){

                if($people['m_occupation']){
                $people['m_occupation']=str_replace(",","-",$people['m_occupation']);
                $result[]=$people['m_occupation'];

                }else{

                $result[]='--';
                }

                }

                $output .=  implode("\t", $result)."\n";
                $counter++;
                }

                //echo $output; die;
                $filename = "Student-info".date('d-m-Y').".xlsx";
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$filename);
                echo $output;die;

                $this->redirect($this->referer());

                */
    }

    public function admit_studentcollectionreport()
    {
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
        //    pr($SQL); die;
        $resul = $conn->execute($SQL)->fetchAll('assoc');

        //    pr($resul); die;
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
                $output .= str_replace(" ", "", $value["fname"]) . ",";
            }

            if (in_array('middlename', $e_complete)) {
                $output .= str_replace(" ", "", $value["middlename"]) . ",";
            }
            if (in_array('lname', $e_complete)) {
                $output .= str_replace(" ", "", $value["lname"]) . ",";
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
        $classes = $this->Classections->find('list', ['keyField' => 'Classes.id', 'valueField' => 'Classes.title'])->contain(['Classes'])
            ->where(['Classections.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toArray();
        //pr($classes); die;

        $this->set(compact('classes'));

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));
    }

    public function searchdailyreportmonthly()
    {

        $mode = $this->request->data['mode'];
        $selectField = $this->request->data['selectField'];

        if (in_array('Tution Fee', $selectField)) {
            array_push($selectField, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        }

        $gh = array_flip($selectField);
        //$this->set(compact('selectField'));
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        //    echo $datefrom; echo $dateto; die;

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $this->set(compact('datefrom'));

            $stts = array('Studentfees.paydate >=' => $datefrom);
            $apk[] = $stts;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {
            $this->set(compact('dateto'));
            $this->set(compact('dateto2'));
            $stts = array('Studentfees.paydate <=' => $dateto2);
            $apk[] = $stts;
        }

        if (!empty($mode)) {
            $pii = array('Studentfees.mode IN' => $mode);
            $apk[] = $pii;
        }

        $pii = array('Studentfees.status' => 'Y');
        $apk[] = $pii;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii = array('Students.board_id' => '1');
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {

            $pii = array('Students.board_id IN' => ['2', '3']);
            $apk[] = $pii;
        }
        //$pii=array('Students.status'=>'Y');
        //$apk[]=$pii;

        $pii = array('Studentfees.recipetno !=' => '0');
        $apk[] = $pii;

        $Classections21 = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.recipetno' => 'ASC'])->toarray();

        if (!empty($datefrom) && $datefrom != '1970-01-01') {
            $stts2 = array('Studentfees.paydate >=' => $datefrom);
            $apk2[] = $stts2;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {

            $stts2 = array('Studentfees.paydate <=' => $dateto2);
            $apk2[] = $stts2;
        }

        if (!empty($mode)) {
            $pii2 = array('Studentfees.mode IN' => $mode);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.status' => 'Y');
        $apk2[] = $pii2;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii2 = array('DropOutStudent.board_id IN' => ['1']);
            $apk2[] = $pii2;
        } elseif ($rolepresent == '8') {

            $pii2 = array('DropOutStudent.board_id IN' => ['2', '3']);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.recipetno !=' => '0');
        $apk2[] = $pii2;

        $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk2)->order(['Studentfees.recipetno' => 'ASC'])->toarray();
        //    pr($Classections212); die;

        $Classections21 = array_merge($Classections21, $Classections212);

        $extra1 = array('Late Fee', 'Discount Fee', 'Other Discount');
        $extra = array_flip($extra1);
        $Classections = array();
        $sel = array();

        $findrecipiet = $this->checkregistration($datefrom, $dateto2);
        if ($findrecipiet[0]['regfee']) {
            //    if (in_array("CASH", $mode)) {
            if (in_array('Registration', $selectField)) {
                array_push($sel, "Registration");
            }

            //    }
        }

        $findprospectus = $this->checkprospectus($datefrom, $dateto2);

        if ($findprospectus[0]['p_fees']) {
            //    if (in_array("CASH", $mode)) {

            if (in_array('Prospectus', $selectField)) {
                array_push($sel, "Prospectus");
            }

            //    }
        }
        foreach ($Classections21 as $fgh) {

            $qua = unserialize($fgh['quarter']);
            $arr = array();
            foreach ($qua as $fg => $cg) { //pr($qua);

                if (ctype_digit(trim($fg, '"'))) {
                    $arr['Prev. Due'] = $cg;
                } else {
                    $arr[$fg] = $cg;
                }
            }

            //pr($arr);
            $rty = array_intersect_key($gh, $arr);
            if (!empty($rty)) {
                foreach ($rty as $yu => $rt) {
                    $sel[] = $yu;
                }
            }
            if (!empty($rty)) {

                $Classections[] = $fgh;
            }
        }

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Late Fee') {
                        if ($fgh['lfine'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }

        array_push($sel, "Prev. Access Amount");
        array_push($sel, "Access Amount");

        sort($sel);

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Discount Fee') {
                        if ($fgh['discount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } elseif ($ok == 'Other Discount') {
                        if ($fgh['addtionaldiscount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }
        array_push($sel, "Due Amount");
        array_push($sel, "Other Fees");

        $rk = array_unique($sel);

        $nm = array_unique($Classections);

        //array_push($rk,"Due Amount");

        $this->set('selectField', $rk);
        //die;

        $this->set('Classections', $nm);
        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmicyear = $users['academic_year'];
        $this->set(compact('acedmicyear'));
    }
    public function searchdailyreport()
    {

        $mode = $this->request->data['mode'];
        $selectField = $this->request->data['selectField'];

        if (in_array('Tution Fee', $selectField)) {
            array_push($selectField, "Quater1", "Quater2", "Quater3", "Quater4");
        }

        $gh = array_flip($selectField);
        //$this->set(compact('selectField'));
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        //    echo $datefrom; echo $dateto; die;

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $this->set(compact('datefrom'));

            $stts = array('Studentfees.paydate >=' => $datefrom);
            $apk[] = $stts;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {
            $this->set(compact('dateto'));
            $this->set(compact('dateto2'));
            $stts = array('Studentfees.paydate <=' => $dateto2);
            $apk[] = $stts;
        }

        if (!empty($mode)) {
            $pii = array('Studentfees.mode IN' => $mode);
            $apk[] = $pii;
        }

        $pii = array('Studentfees.status' => 'Y');
        $apk[] = $pii;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii = array('Students.board_id' => 1);
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {

            $pii = array('Students.board_id IN' => ['2', '3']);
            $apk[] = $pii;
        }
        //$pii=array('Students.status'=>'Y');
        //$apk[]=$pii;

        $pii = array('Studentfees.recipetno !=' => '0');
        $apk[] = $pii;

        $Classections21 = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.recipetno' => 'ASC'])->toarray();

        if (!empty($datefrom) && $datefrom != '1970-01-01') {
            $stts2 = array('Studentfees.paydate >=' => $datefrom);
            $apk2[] = $stts2;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {

            $stts2 = array('Studentfees.paydate <=' => $dateto2);
            $apk2[] = $stts2;
        }

        if (!empty($mode)) {
            $pii2 = array('Studentfees.mode IN' => $mode);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.status' => 'Y');
        $apk2[] = $pii2;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii2 = array('DropOutStudent.board_id IN' => ['1']);
            $apk2[] = $pii2;
        } elseif ($rolepresent == '8') {

            $pii2 = array('DropOutStudent.board_id IN' => ['2', '3']);
            $apk2[] = $pii2;
        }

        $pii2 = array('Studentfees.recipetno !=' => '0');
        $apk2[] = $pii2;

        $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk2)->order(['Studentfees.recipetno' => 'ASC'])->toarray();
        //    pr($Classections212); die;

        $Classections21 = array_merge($Classections21, $Classections212);

        $extra1 = array('Late Fee', 'Discount Fee', 'Other Discount');
        $extra = array_flip($extra1);
        $Classections = array();
        $sel = array();

        $findrecipiet = $this->checkregistration($datefrom, $dateto2);
        if ($findrecipiet[0]['regfee']) {
            //    if (in_array("CASH", $mode)) {
            if (in_array('Registration', $selectField)) {
                array_push($sel, "Registration");
            }

            //    }
        }

        $findprospectus = $this->checkprospectus($datefrom, $dateto2);

        if ($findprospectus[0]['p_fees']) {
            //    if (in_array("CASH", $mode)) {

            if (in_array('Prospectus', $selectField)) {
                array_push($sel, "Prospectus");
            }

            //    }
        }
        foreach ($Classections21 as $fgh) {

            $qua = unserialize($fgh['quarter']);
            $arr = array();
            foreach ($qua as $fg => $cg) { //pr($qua);

                if (ctype_digit(trim($fg, '"'))) {
                    $arr['Prev. Due'] = $cg;
                } else {
                    $arr[$fg] = $cg;
                }
            }

            //pr($arr);
            $rty = array_intersect_key($gh, $arr);
            if (!empty($rty)) {
                foreach ($rty as $yu => $rt) {
                    $sel[] = $yu;
                }
            }
            if (!empty($rty)) {

                $Classections[] = $fgh;
            }
        }

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Late Fee') {
                        if ($fgh['lfine'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }

        array_push($sel, "Prev. Access Amount");
        array_push($sel, "Access Amount");

        sort($sel);

        foreach ($Classections21 as $fgh) {
            $pty = array_intersect_key($gh, $extra);
            //pr($pty);
            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Discount Fee') {
                        if ($fgh['discount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } elseif ($ok == 'Other Discount') {
                        if ($fgh['addtionaldiscount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }
        }
        array_push($sel, "Due Amount");
        array_push($sel, "Other Fees");

        $rk = array_unique($sel);

        $nm = array_unique($Classections);

        //array_push($rk,"Due Amount");

        $this->set('selectField', $rk);
        //die;

        $this->set('Classections', $nm);
        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmicyear = $users['academic_year'];
        $this->set(compact('acedmicyear'));
    }

    public function checkprospectus($datefrom, $dateto2)
    {

        $articles = TableRegistry::get('Enquires');

        // Start a new query.
        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto2));

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            return $articles->find('all')->select(['p_fees' => $articles->find()->func()->sum('Enquires.p_fees')])->where(['Enquires.created >=' => $datefrom, 'Enquires.class_id IN' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31'], 'Enquires.created <=' => $dateto2])->toarray();
        } elseif ($rolepresent == '8') {

            return $articles->find('all')->select(['p_fees' => $articles->find()->func()->sum('Enquires.p_fees')])->where(['Enquires.created >=' => $datefrom, 'Enquires.class_id IN' => ['23', '24', '25', '26', '27', '28', '29'], 'Enquires.created <=' => $dateto2])->toarray();
        } else {

            return $articles->find('all')->select(['p_fees' => $articles->find()->func()->sum('Enquires.p_fees')])->where(['Enquires.created >=' => $datefrom, 'Enquires.created <=' => $dateto2])->toarray();
        }
    }

    public function checkregistration($datefrom, $dateto2)
    {

        $articles = TableRegistry::get('Applicant');

        // Start a new query.
        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto2));

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            return $articles->find('all')->select(['regfee' => $articles->find()->func()->sum('Applicant.reg_fee')])->where(['Applicant.created >=' => $datefrom, 'Applicant.class_id IN' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31'], 'Applicant.created <=' => $dateto2])->toarray();
        } elseif ($rolepresent == '8') {

            return $articles->find('all')->select(['regfee' => $articles->find()->func()->sum('Applicant.reg_fee')])->where(['Applicant.created >=' => $datefrom, 'Applicant.class_id IN' => ['23', '24', '25', '26', '27', '28', '29'], 'Applicant.created <=' => $dateto2])->toarray();
        } else {

            return $articles->find('all')->select(['regfee' => $articles->find()->func()->sum('Applicant.reg_fee')])->where(['Applicant.created >=' => $datefrom, 'Applicant.created <=' => $dateto2])->toarray();
        }
    }

    public function dailyreport()
    {

        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', ['keyField' => 'Classes.id', 'valueField' => 'Classes.title'])->contain(['Classes'])
            ->where(['Classections.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toArray();
        //pr($classes); die;

        $this->set(compact('classes'));
        $ar = array('2', '3');
        $feesheadstotal = $this->Feesheads->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'sort !=' => '0'])->order(['sort' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));
    }
    public function dailyreportmonthly()
    {

        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', ['keyField' => 'Classes.id', 'valueField' => 'Classes.title'])->contain(['Classes'])
            ->where(['Classections.status' => 'Y'])->order(['Classes.sort' => 'ASC'])->toArray();
        //pr($classes); die;

        $this->set(compact('classes'));
        $ar = array('2', '3');
        $feesheadstotal = $this->Feesheads->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'sort !=' => '0'])->order(['sort' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));
    }

    public function studentgenderhouse()
    {
        $this->viewBuilder()->layout('admin');

        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $db = $this->Users->find()->where(['role_id' => 1])->first();

        if ($rolepresent == '1' || $rolepresent == '6') {
            if ($db == "sanskar") {
                $art = ['18', '19'];
            } else {
                $art = ['0'];
            }

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);

            $sectionslist = $this->Sections->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
            $this->set('section_id2', $sectionslist);
        } elseif ($rolepresent == '5') {

            if ($db == "sanskar") {
                $art = ['18', '19'];
            } else {
                $art = ['0'];
            }
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1', 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '8') {

            if ($db == "sanskar") {
                $art = ['18', '19'];
            } else {
                $art = ['0'];
            }
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3'], 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        }

        $gender = "Both";

        $this->set(compact('gender'));

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('section_id2', $sectionslist);

        $houselist = $this->Houses->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => '1'])->order(['name' => 'ASC'])->toArray();
        $this->set('houselist', $houselist);
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        $rolepresentyear = $users['academic_year'];

        $acd = $this->academicyear();

        $this->set(compact('acd'));
        $this->set(compact('rolepresentyear'));
    }

    public function studentgender()
    {
        $this->viewBuilder()->layout('admin');

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);


            $sectionslist = $this->Sections->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
            $this->set('section_id2', $sectionslist);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        }
        $gender = "Both";

        $this->set(compact('gender'));

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('section_id2', $sectionslist);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        $rolepresentyear = $users['academic_year'];

        $acd = $this->academicyear();

        $this->set(compact('acd'));
        $this->set(compact('rolepresentyear'));
    }

    public function cancelledrecipiet()
    {
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6'  || $rolepresent == '105') {

            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));
    }

    public function collectionrecipiet()
    {

        $this->viewBuilder()->layout('admin');

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $ar = array('2', '3');
        $feesheadstotal = $this->Feesheads->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'sort !=' => '0'])->order(['sort' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        $acd = $this->academicyear();
        $this->set(compact('rolepresentyear'));
        $this->set(compact('acd'));
    }

    public function collectionrecipietmonthly()
    {

        $this->viewBuilder()->layout('admin');

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $ar = array('2', '3');
        $feesheadstotal = $this->Feesheads->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'sort !=' => '0'])->order(['sort' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        $acd = $this->academicyear();
        $this->set(compact('rolepresentyear'));
        $this->set(compact('acd'));
    }

    public function search5()
    {
        $s_id = $this->request->data['class_id'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $datefrom = $this->request->data['datefrom'];
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto']));
        //    echo $datefrom; echo $dateto; die;

        if ($s_id) {
            $Classections = $this->Classections->find('all', ['keyField' => 'class_id', 'valueField' => 'section_id'])->where(['class_id' => $s_id])->order(['Classes.sort' => 'ASC'])->toArray();
            // pr($Classections);die;
        } else {

            $Classections = $this->Classections->find('all', ['keyField' => 'class_id', 'valueField' => 'section_id'])->order(['Classes.sort' => 'ASC'])->toArray();
        }

        if (!empty($datefrom) && $datefrom != "1970-01-01") {

            $this->set(compact('datefrom'));
        }
        if (!empty($dateto) && $dateto != "1970-01-01") {
            $dateto = date('Y-m-d H:i:s', strtotime($dateto . ' +1 day'));
            $this->set(compact('dateto'));
        }

        $this->set(compact('Classections'));
        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $this->set(compact('acedmicyear'));
    }

    public function searchstudentgender()
    {

        $class_id = $this->request->data['class_id'];
        $acedmic = $this->request->data['acedmicyear'];
        $this->set(compact('acedmic'));

        $class_id = implode(',', $class_id);

        $this->set(compact('class_id'));
        if ($this->request->data['section_id']) {
            $section_id = $this->request->data['section_id'];

            $section_id = implode(',', $section_id);
        } else {
            $section_id = array();
        }
        $this->set(compact('section_id'));
        $gender = $this->request->data['gender'];

        $this->set(compact('gender'));
        $this->set(compact('acedmic'));

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $acedmicyear = $user['academic_year'];
        $next_academic_year = $user['next_academic_year'];

        $this->set('acedmicyear', $acedmicyear);
        $this->set('next_academic_year', $next_academic_year);
    }

    public function searchstudentgenderhouse()
    {
        //    pr($this->request->data); die;

        $class_id = $this->request->data['class_id'];

        $class_id = implode(',', $class_id);

        $this->set(compact('class_id'));

        $section_id = $this->request->data['section_id'];

        $section_id = implode(',', $section_id);

        $this->set(compact('section_id'));
        $gender = $this->request->data['gender'];

        $houselist = $this->Houses->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => '1'])->order(['name' => 'ASC'])->toArray();
        $this->set('houselist', $houselist);

        $acedmic = $this->request->data['acedmicyear'];
        $this->set(compact('acedmic'));
        $this->set(compact('gender'));
    }

    public function searchcancelled()
    {

        //pr($this->request->data); die;
        $class_id = $this->request->data['class_id'];
        $recipetno = $this->request->data['recipetno'];
        $status = $this->request->data['status'];
        $cheque_no = $this->request->data['cheque_no'];
        $ref_no = $this->request->data['ref_no'];
        $sr_no = $this->request->data['sr_no'];

        $this->set(compact('selectField'));
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        //    echo $datefrom; echo $dateto; die;

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $db = $users['db'];
        $this->set(compact('db'));
        $acedmic = $users['academic_year'];
        if (!empty($sr_no)) {
            $pii = array('Students.enroll' => $sr_no);
            $apk[] = $pii;
        }

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $this->set(compact('datefrom'));

            $stts = array('Studentfees.paydate >=' => $datefrom);
            $apk[] = $stts;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {
            $this->set(compact('dateto2'));
            $stts = array('Studentfees.paydate <=' => $dateto2);
            $apk[] = $stts;
        }

        if (!empty($class_id)) {
            $pii = array('Students.class_id' => $class_id);
            $apk[] = $pii;
        }
        if (!empty($cheque_no)) {
            $pii = array('Studentfees.cheque_no' => $cheque_no);
            $apk[] = $pii;
        }

        if (!empty($ref_no)) {
            $pii = array('Studentfees.ref_no LIKE' => $ref_no . '%');
            $apk[] = $pii;
        }
        if (!empty($recipetno)) {
            $pii = array('Studentfees.recipetno' => $recipetno);
            $apk[] = $pii;
        }

        if (!empty($status)) {
            $this->set(compact('status'));
            if ($status != "Both") {
                $pii = array('Studentfees.status' => $status);
                $apk[] = $pii;
            }
        }

        $pii = array('Studentfees.recipetno !=' => '0');
        $apk[] = $pii;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $pii = array('Students.board_id' => '1');
            $apk[] = $pii;
        } elseif ($rolepresent == '8') {

            $pii = array('Students.board_id IN' => ['2', '3']);
            $apk[] = $pii;
        }

        $Classectionsfees = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.paydate ASC'])->order(['Studentfees.recipetno ASC'])->toarray();
        $this->set('t_enquiry', '0');

        if (!empty($sr_no)) {
            $pii3 = array('DropOutStudent.enroll' => $sr_no);
            $apk2[] = $pii3;
        }

        if (!empty($datefrom) && $datefrom != '1970-01-01') {

            $sttss = array('Studentfees.paydate >=' => $datefrom);
            $apk2[] = $sttss;
        }

        if (!empty($dateto2) && $dateto2 != '1970-01-01') {

            $sttss = array('Studentfees.paydate <=' => $dateto2);
            $apk2[] = $sttss;
        }

        if (!empty($class_id)) {
            $piis = array('DropOutStudent.class_id' => $class_id);
            $apk2[] = $piis;
        }
        if (!empty($cheque_no)) {
            $piis = array('Studentfees.cheque_no' => $cheque_no);
            $apk2[] = $piis;
        }
        if (!empty($ref_no)) {
            $piis = array('Studentfees.ref_no LIKE' => $ref_no . '%');
            $apk2[] = $piis;
        }
        if (!empty($recipetno)) {
            $piis = array('Studentfees.recipetno' => $recipetno);
            $apk2[] = $piis;
        }

        if (!empty($status)) {
            $this->set(compact('status'));
            if ($status != "Both") {
                $piis = array('Studentfees.status' => $status);
                $apk2[] = $piis;
            }
        }

        $piis = array('Studentfees.recipetno !=' => '0');
        $apk2[] = $piis;

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '5') {

            $piis = array('DropOutStudent.class_id IN' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31']);
            $apk2[] = $piis;
        } elseif ($rolepresent == '8') {

            $piis = array('DropOutStudent.class_id IN' => ['23', '24', '25', '26', '27', '28', '29']);
            $apk2[] = $piis;
        }

        $Classectionsfees21 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk2)->order(['Studentfees.paydate ASC'])->order(['Studentfees.recipetno ASC'])->toarray();

        $Classectionsfees = array_merge($Classectionsfees, $Classectionsfees21);

        $this->set(compact('Classectionsfees'));

        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $this->set(compact('acedmicyear'));
    }

    public function prospectreportsearch()
    {

        $this->loadModel('Applicant');
        $this->loadModel('Enquires');
        $conn = ConnectionManager::get('default');
        //pr($this->request->data); die;
        $from = date('Y-m-d', strtotime($this->request->data['from']));
        $to = date('Y-m-d', strtotime($this->request->data['to']));
        $to2 = date('Y-m-d', strtotime($this->request->data['to'] . '+1 days'));

        $this->request->session()->delete('datefrom');
        $this->request->session()->write('datefrom', $from);
        $this->request->session()->delete('dateto2');
        $this->request->session()->write('dateto2', $to);
        $class_id = $this->request->data['class_id'];

        $stat = $this->request->data['s_id'];
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        $this->set(compact('stat'));

        if ($stat == '5') {

            $apk = array();

            if (!empty($from) && $from != '1970-01-01') {
                $stts = array('Enquires.created >=' => $from);
                $apk[] = $stts;
            }

            if (!empty($to) && $to != '1970-01-01') {
                $stst = array('Enquires.created <=' => $to2);
                $apk[] = $stst;
            }

            if (!empty($class_id)) {
                $pii = array('Enquires.class_id' => $class_id);
                $apk[] = $pii;
            }

            $pii = array('Enquires.enquiry_mode' => '2');
            $apk[] = $pii;

            $rolepresent = $this->request->session()->read('Auth.User.role_id');

            if ($rolepresent == '5') {

                $pii = array('Enquires.class_id IN' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31']);
                $apk[] = $pii;
            } elseif ($rolepresent == '8') {

                $pii = array('Enquires.class_id IN' => ['23', '24', '25', '26', '27', '28', '29']);
                $apk[] = $pii;
            }

            $classes_data = $this->Enquires->find('all')->order(['Enquires.created' => 'DESC'])->where($apk)->toarray();

            $this->set('t_enquiry', $classes_data);
            $this->request->session()->delete('condition');
            $this->request->session()->write('condition', $classes_data);
        } elseif ($stat == '1') {

            $apk = array();

            if (!empty($from) && $from != '1970-01-01') {
                $stts = array('Applicant.created >=' => $from);
                $apk[] = $stts;
            }

            if (!empty($to) && $to != '1970-01-01') {
                $stst = array('Applicant.created <=' => $to);
                $apk[] = $stst;
            }

            if (!empty($class_id)) {
                $pii = array('Applicant.class_id' => $class_id);
                $apk[] = $pii;
            }

            $rolepresent = $this->request->session()->read('Auth.User.role_id');

            if ($rolepresent == '5') {

                $pii = array('Applicant.class_id IN' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31']);
                $apk[] = $pii;
            } elseif ($rolepresent == '8') {

                $pii = array('Applicant.class_id IN' => ['23', '24', '25', '26', '27', '28', '29']);
                $apk[] = $pii;
            }

            $classes_data = $this->Applicant->find('all')->order(['Applicant.created' => 'DESC'])->contain(['Enquires'])->where($apk)->toarray();

            $this->set('t_enquiry', $classes_data);
            $this->request->session()->delete('condition');
            $this->request->session()->write('condition', $classes_data);
        }
    }

    public function cmp($a, $b)
    {
        return $a['recipetno'] - $b['recipetno'];
    }

    public function searchcollectionfeemonthly()
    {
        //pr($this->request->data); die;

        $class_id = $this->request->data['class_id'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $mode = $this->request->data['mode'];
        $ref_no = $this->request->data['ref_no'];

        $selectField = $this->request->data['selectField'];
        //pr($selectField); die;

        if (in_array('Tution Fee', $selectField)) {
            array_push($selectField, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        }

        $gh = array_flip($selectField);

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $currentyear = $users['academic_year'];
        $nextyear = $users['next_academic_year'];
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($acedmicyear != "") {

            // Student Fees Collection With Students With Acadamic Year

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk[] = $stts;
            }
            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk[] = $pii2;

            if (!empty($class_id)) {
                $pii = array('Students.class_id' => $class_id);
                $apk[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('Students.board_id' => 1);
                    $apk[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Students.board_id IN' => ['2', '3']);
                    $apk[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk[] = $pii;

            $Classections21g = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.id' => 'ASC'])->toarray();

            // Student Fees Collection With Students History With Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk2[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk2[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk2[] = $stts;
            }

            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk2[] = $pii2;

            $pii2 = array('Studentshistory.acedmicyear' => $acedmicyear);
            $apk2[] = $pii2;

            if (!empty($class_id)) {
                $pii = array('Studentshistory.class_id' => $class_id);
                $apk2[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('Studentshistory.board_id' => 1);
                    $apk2[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Studentshistory.board_id IN' => ['2', '3']);
                    $apk2[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk2[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk2[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk2[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk2[] = $pii;

            foreach ($Classections21g as $kk => $jj) {

                $iddsss[] = $jj['id'];
            }
            if (!empty($iddsss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddsss);
                $apk2[] = $pii2;
            }
            $Classections21a = $this->Studentfees->find('all')->contain(['Studentshistory'])->where($apk2)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections21a as $kk => $jj) {

                if ($rolepresent == '5') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21g, $Classections21a);

            // Student Fees Collection With Drop Out With Acadamic Year

            foreach ($Classections21 as $kk => $jj) {

                $iddss[] = $jj['id'];
            }

            if (!empty($iddss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddss);
                $apk3[] = $pii2;
            }

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk3[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk3[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk3[] = $stts;
            }

            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk3[] = $pii2;
            if (!empty($class_id)) {
                $pii = array('DropOutStudent.laststudclass' => $class_id);
                $apk3[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('DropOutStudent.board_id' => 1);
                    $apk3[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('DropOutStudent.board_id IN' => ['2', '3']);
                    $apk3[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk3[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk3[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk3[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk3[] = $pii;

            $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk3)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections212 as $kk => $jj) {

                if ($rolepresent == '5') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                }
            }
            $Classections21 = array_merge($Classections21, $Classections212);
        } else {
            // Student Fees Collection With Students without Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('Students.class_id' => $class_id);
                $apk[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('Students.board_id' => 1);
                    $apk[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Students.board_id IN' => ['2', '3']);
                    $apk[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk[] = $pii;

            $Classections21g = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.id' => 'ASC'])->toarray();

            //pr($Classections21g); die;
            // Student Fees Collection With Students History without Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk2[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk2[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk2[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('Studentshistory.class_id' => $class_id);
                $apk2[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('Studentshistory.board_id' => 1);
                    $apk2[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Studentshistory.board_id IN' => ['2', '3']);
                    $apk2[] = $pii;
                }
            }
            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk2[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk2[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk2[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk2[] = $pii;

            foreach ($Classections21g as $kk => $jj) {

                $iddsss[] = $jj['id'];
            }

            if (!empty($iddsss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddsss);
                $apk2[] = $pii2;
            }

            $Classections21a = $this->Studentfees->find('all')->contain(['Studentshistory'])->where($apk2)->group(['Studentshistory.stud_id'])->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections21a as $kk => $jj) {

                if ($rolepresent == '5') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21g, $Classections21a);

            // Student Fees Collection With Drop Out without Acadamic Year

            foreach ($Classections21 as $kk => $jj) {

                $iddss[] = $jj['id'];
            }
            if (!empty($iddss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddss);
                $apk3[] = $pii2;
            }
            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk3[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk3[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk3[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('DropOutStudent.laststudclass' => $class_id);
                $apk3[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5') {

                    $pii = array('DropOutStudent.board_id' => 1);
                    $apk3[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('DropOutStudent.board_id IN' => ['2', '3']);
                    $apk3[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk3[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk3[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk3[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk3[] = $pii;

            $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk3)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections212 as $kk => $jj) {

                if ($rolepresent == '5') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21, $Classections212);
        }

        usort($Classections21, function ($a, $b) { // anonymous function
            // compare numbers only
            return $a['recipetno'] - $b['recipetno'];
        });

        $extra1 = array('Late Fee', 'Discount Fee', 'Other Discount');
        $extra = array_flip($extra1);
        $Classections = array();
        $sel = array();

        //pr($Classections21); die;
        foreach ($Classections21 as $fgh) {

            $qua = unserialize($fgh['quarter']);
            $arr = array();
            foreach ($qua as $fg => $cg) {

                if (ctype_digit(trim($fg, '"'))) {
                    $arr['Prev. Due'] = $cg;
                } else {
                    $arr[$fg] = $cg;
                }
            }

            //pr($arr);
            $rty = array_intersect_key($gh, $arr);
            //pr($rty);

            if (!empty($rty)) {
                foreach ($rty as $yu => $rt) {
                    $sel[] = $yu;
                }
            }
            $pty = array_intersect_key($gh, $extra);

            //    if (in_array('CASH', $mode)) {
            if (in_array('Prospectus', $selectField)) {

                if ($fgh['type'] == "Prospectus") {
                    $sel[] = $fgh['type'];

                    $Classections[] = $fgh;
                }
            }

            if (in_array('Registration', $selectField)) {

                if ($fgh['type'] == "Registration") {
                    $sel[] = $fgh['type'];

                    $Classections[] = $fgh;
                }
            }

            //        }

            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Late Fee') {
                        if ($fgh['lfine'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } elseif ($ok == 'Discount Fee') {
                        if ($fgh['discount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } else {
                        if ($fgh['addtionaldiscount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }

            if (!empty($rty)) {

                $Classections[] = $fgh;
            }
        }

        $rk = array_unique($sel);
        sort($rk);
        $nm = array_unique($Classections);

        array_push($rk, "Due Amount");
        $this->set('selectField', $rk);
        //die;
        $this->set('Classections', $nm);

        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $this->set(compact('acedmicyear'));
    }
    public function searchcollectionfee()
    {
        // pr($this->request->data); die;

        $class_id = $this->request->data['class_id'];
        if (!empty($class_id)) {
            $this->set(compact('class_iddefine'));
        }
        $acedmicyear = $this->request->data['acedmicyear'];
        $mode = $this->request->data['mode'];
        $ref_no = $this->request->data['ref_no'];

        $selectField = $this->request->data['selectField'];
        // pr($selectField); die;

        if (in_array('Tution Fee', $selectField)) {
            array_push($selectField, "Quater1", "Quater2", "Quater3", "Quater4");
        }
        if (in_array('Transport Fee', $selectField)) {
            array_push($selectField, "Transport5", "Transport6", "Transport7", "Transport8");
        }

        $gh = array_flip($selectField);

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));
        //    echo $datefrom; echo $dateto; die;

        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        $dateto = date('Y-m-d', strtotime($this->request->data['dateto'] . '+1 days'));

        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $currentyear = $users['academic_year'];
        $nextyear = $users['next_academic_year'];
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($acedmicyear != "") {
            // Student Fees Collection With Students With Acadamic Year

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk[] = $stts;
            }
            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk[] = $pii2;

            if (!empty($class_id)) {
                $pii = array('Students.class_id' => $class_id);
                $apk[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');


                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('Students.board_id' => 1);
                    $apk[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Students.board_id IN' => ['2', '3']);
                    $apk[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk[] = $pii;

            $Classections21g = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.id' => 'ASC'])->toarray();
            // pr($Classections21g);die;


            // Student Fees Collection With Students History With Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk2[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk2[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk2[] = $stts;
            }

            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk2[] = $pii2;

            $pii2 = array('Studentshistory.acedmicyear' => $acedmicyear);
            $apk2[] = $pii2;

            if (!empty($class_id)) {
                $pii = array('Studentshistory.class_id' => $class_id);
                $apk2[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('Studentshistory.board_id' => 1);
                    $apk2[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Studentshistory.board_id IN' => ['2', '3']);
                    $apk2[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk2[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk2[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk2[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk2[] = $pii;

            foreach ($Classections21g as $kk => $jj) {

                $iddsss[] = $jj['id'];
            }
            if (!empty($iddsss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddsss);
                $apk2[] = $pii2;
            }
            $Classections21a = $this->Studentfees->find('all')->contain(['Studentshistory'])->where($apk2)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections21a as $kk => $jj) {

                if ($rolepresent == '5' || $rolepresent == '6') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21g, $Classections21a);

            // Student Fees Collection With Drop Out With Acadamic Year

            foreach ($Classections21 as $kk => $jj) {

                $iddss[] = $jj['id'];
            }

            if (!empty($iddss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddss);
                $apk3[] = $pii2;
            }

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk3[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk3[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk3[] = $stts;
            }

            $pii2 = array('Studentfees.acedmicyear' => $acedmicyear);
            $apk3[] = $pii2;
            if (!empty($class_id)) {
                $pii = array('DropOutStudent.laststudclass' => $class_id);
                $apk3[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('DropOutStudent.board_id' => 1);
                    $apk3[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('DropOutStudent.board_id IN' => ['2', '3']);
                    $apk3[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk3[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk3[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk3[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk3[] = $pii;

            $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk3)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections212 as $kk => $jj) {

                if ($rolepresent == '5' || $rolepresent == '6') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21, $Classections212);
        } else {
            // Student Fees Collection With Students without Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('Students.class_id' => $class_id);
                $apk[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('Students.board_id' => 1);
                    $apk[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Students.board_id IN' => ['2', '3']);
                    $apk[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk[] = $pii;

            // pr($apk);die;
            $Classections21g = $this->Studentfees->find('all')->contain(['Students'])->where($apk)->order(['Studentfees.id' => 'ASC'])->toarray();

            // pr($Classections21g); die;
            // Student Fees Collection With Students History without Acadamic Year

            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk2[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk2[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk2[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('Studentshistory.class_id' => $class_id);
                $apk2[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('Studentshistory.board_id' => 1);
                    $apk2[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('Studentshistory.board_id IN' => ['2', '3']);
                    $apk2[] = $pii;
                }
            }
            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk2[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk2[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk2[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk2[] = $pii;

            foreach ($Classections21g as $kk => $jj) {
                // pr($jj);
                $iddsss[] = $jj['id'];
            }
            // die;

            if (!empty($iddsss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddsss);
                $apk2[] = $pii2;
            }

            $Classections21a = $this->Studentfees->find('all')->contain(['Studentshistory'])->where($apk2)->group(['Studentshistory.stud_id'])->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections21a as $kk => $jj) {

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Students'])->where(['Students.id' => $jj['student_id'], 'Students.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections21a[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21g, $Classections21a);

            // Student Fees Collection With Drop Out without Acadamic Year

            foreach ($Classections21 as $kk => $jj) {

                $iddss[] = $jj['id'];
            }
            if (!empty($iddss)) {
                $pii2 = array('Studentfees.id NOT IN' => $iddss);
                $apk3[] = $pii2;
            }
            if (!empty($datefrom) && $datefrom != '1970-01-01') {

                $this->set(compact('datefrom'));

                $stts = array('Studentfees.paydate >=' => $datefrom);
                $apk3[] = $stts;
            }
            if (!empty($ref_no)) {
                $pii = array('Studentfees.ref_no LIKE ' => '%' . $ref_no . '%');
                $apk3[] = $pii;
            }

            if (!empty($dateto2) && $dateto2 != '1970-01-01') {
                $this->set(compact('dateto2'));
                $stts = array('Studentfees.paydate <=' => $dateto2);
                $apk3[] = $stts;
            }

            if (!empty($class_id)) {
                $pii = array('DropOutStudent.laststudclass' => $class_id);
                $apk3[] = $pii;
            } else {

                $rolepresent = $this->request->session()->read('Auth.User.role_id');

                if ($rolepresent == '5' || $rolepresent == '6') {

                    $pii = array('DropOutStudent.board_id' => 1);
                    $apk3[] = $pii;
                } elseif ($rolepresent == '8') {

                    $pii = array('DropOutStudent.board_id IN' => ['2', '3']);
                    $apk3[] = $pii;
                }
            }

            if (!empty($mode)) {
                $pii = array('Studentfees.mode IN' => $mode);
                $apk3[] = $pii;
            }

            $pii = array('Studentfees.status' => 'Y');
            $apk3[] = $pii;

            $pii = array('Studentfees.type !=' => 'Other');
            $apk3[] = $pii;

            $pii = array('Studentfees.recipetno !=' => '0');
            $apk3[] = $pii;

            $Classections212 = $this->Studentfees->find('all')->contain(['DropOutStudent'])->where($apk3)->order(['Studentfees.id' => 'ASC'])->toarray();

            foreach ($Classections212 as $kk => $jj) {

                if ($rolepresent == '5' || $rolepresent == '6') {
                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['2', '3']])->order(['Studentfees.id' => 'ASC'])->first();
                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                } elseif ($rolepresent == '8') {

                    $Classections2s1g = $this->Studentfees->find('all')->contain(['Studentshistory'])->where(['Studentshistory.stud_id' => $jj['s_id'], 'Studentshistory.board_id IN' => ['1']])->order(['Studentfees.id' => 'ASC'])->first();

                    if ($Classections2s1g['id'] != '') {
                        unset($Classections212[$kk]);
                    }
                }
            }

            $Classections21 = array_merge($Classections21, $Classections212);
        }
        // pr($Classections21);die;

        usort($Classections21, function ($a, $b) { // anonymous function
            // compare numbers only
            return $a['recipetno'] - $b['recipetno'];
        });

        $extra1 = array('Late Fee', 'Discount Fee', 'Other Discount');
        $extra = array_flip($extra1);
        $Classections = array();
        $sel = array();

        //pr($Classections21); die;
        foreach ($Classections21 as $fgh) {

            $qua = unserialize($fgh['quarter']);
            // pr($qua);
            $arr = array();
            foreach ($qua as $fg => $cg) {
                //  pr($qua);
                if (ctype_digit(trim($fg, '"'))) {
                    $arr['Prev. Due'] = $cg;
                } else {
                    $arr[$fg] = $cg;
                }
            }

            //pr($arr);
            $rty = array_intersect_key($gh, $arr);

            if (!empty($rty)) {
                foreach ($rty as $yu => $rt) {
                    $sel[] = $yu;
                }
            }
            $pty = array_intersect_key($gh, $extra);
            // pr($pty);

            //    if (in_array('CASH', $mode)) {
            if (in_array('Prospectus', $selectField)) {

                if ($fgh['type'] == "Prospectus") {
                    $sel[] = $fgh['type'];

                    $Classections[] = $fgh;
                }
            }

            if (in_array('Registration', $selectField)) {

                if ($fgh['type'] == "Registration") {
                    $sel[] = $fgh['type'];

                    $Classections[] = $fgh;
                }
            }

            //        }

            if (!empty($pty)) {
                foreach ($pty as $ok => $er) {
                    // pr($ok);
                    if ($ok == 'Late Fee') {
                        if ($fgh['lfine'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } elseif ($ok == 'Discount Fee') {
                        if ($fgh['discount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    } else {
                        if ($fgh['addtionaldiscount'] != '0') {
                            $sel[] = $ok;
                            $Classections[] = $fgh;
                        }
                    }
                }
            }

            if (!empty($rty)) {

                $Classections[] = $fgh;
            }
        }
        $rk = array_unique($sel);
        sort($rk);
        $nm = array_unique($Classections);
        // pr($Classections);die;
        array_push($rk, "Due Amount");
        $this->set('selectField', $rk);
        //die;
        $this->set('Classections', $nm);

        if ($mode) {
            $this->set(compact('mode'));
        }

        if ($s_id) {
            $this->set(compact('s_id'));
        }
        $this->set(compact('acedmicyear'));
    }

    public function array_qsort2(&$array, $column, $order = "ASC")
    {
        $oper = ($order == "ASC") ? ">" : "<";
        if (!is_array($array)) {
            return;
        }

        usort($array, create_function('$a,$b', "return (\$a['$column'] $oper \$b['$column']);"));
        reset($array);
    }

    public function students_allmonthly()
    {
        $this->viewBuilder()->layout('admin');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];
        $previous_year = $users['previous_year'];
        $this->set(compact('academicyear'));
        $this->set(compact('previous_year'));
        $this->set(compact('fees'));

        $classfee = $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->where(['Classfee.academic_year' => $academicyear])->order(['Classfee.id' => 'ASC'])->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find()->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find()->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->toarray();
        $this->set('classfee', $classfee);

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('sectionslist', $sectionslist);

        $this->set(compact('sectionslist'));

        $conn = ConnectionManager::get('default');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));

        $acedmicyear = $academicyear;

        /* $detail="SELECT Students.id,Students.fname,Students.fathername,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
            $cond = ' ';

            if(!empty($acedmicyear))
            {

            $cond.=" AND Students.acedmicyear LIKE '".$acedmicyear."%' ";

            }

            $quater[]="Admission Fee";
            $quater[]="Development Fee";
            $quater[]="Caution Money";
            $quater[]="Miscellaneous Fee";
            $quater[]="Quater1";
            //$quater[]="Quater2";
            //$quater[]="Quater3";
            //$quater[]="Quater4";

            $this->set('quaters', $quater);
            //    $cond.=" AND Students.section_id LIKE '".$section_id."%' ";

            $results=$this->Students->find('all')->where(['Students.acedmicyear' =>$acedmicyear,'Students.status' =>'Y'])->order(['Students.class_id' => 'ASC'])->limit('500')->toArray();
            $this->set(compact('results'));
            */
    }
    public function students_all()
    {
        $this->viewBuilder()->layout('admin');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $previous_year = $users['previous_year'];
        $this->set(compact('academicyear'));
        $this->set(compact('previous_year'));
        $this->set(compact('fees'));

        $classfee = $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->where(['Classfee.academic_year' => $academicyear])->order(['Classfee.id' => 'ASC'])->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find()->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find()->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->toarray();
        $this->set('classfee', $classfee);

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('sectionslist', $sectionslist);

        $this->set(compact('sectionslist'));

        $conn = ConnectionManager::get('default');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));

        $acedmicyear = $academicyear;

        // /* $detail="SELECT Students.id,Students.fname,Students.fathername,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
        //         $cond = ' ';

        //         if(!empty($acedmicyear))
        //         {

        //         $cond.=" AND Students.acedmicyear LIKE '".$acedmicyear."%' ";

        //         }

        //         $quater[]="Admission Fee";
        //         $quater[]="Development Fee";
        //         $quater[]="Caution Money";
        //         $quater[]="Miscellaneous Fee";
        //         $quater[]="Quater1";
        //         //$quater[]="Quater2";
        //         //$quater[]="Quater3";
        //         //$quater[]="Quater4";

        //         $this->set('quaters', $quater);
        //         //    $cond.=" AND Students.section_id LIKE '".$section_id."%' ";

        //         $results=$this->Students->find('all')->where(['Students.acedmicyear' =>$acedmicyear,'Students.status' =>'Y'])->order(['Students.class_id' => 'ASC'])->limit('500')->toArray();
        //         $this->set(compact('results'));
        //         */
    }

    public function findamountmonth($id, $a_year)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Classfee');
        $m = date('Y-m-d');
        // Start a new query.

        return $articles->find('all')->contain(['Classes'])->select(['qu4_fees' => $articles->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'DATE(Classfee.qu4_date) <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
    }
    public function findamount3month($id, $a_year)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Classfee');
        $m = date('Y-m-d');
        // Start a new query.

        return $articles->find('all')->contain(['Classes'])->select(['qu3_fees' => $articles->find()->func()->sum('Classfee.qu3_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'DATE(Classfee.qu3_date) <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
    }
    public function findamount2month($id, $a_year)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Classfee');
        $m = date('Y-m-d');
        // Start a new query.

        return $articles->find('all')->contain(['Classes'])->select(['qu2_fees' => $articles->find()->func()->sum('Classfee.qu2_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'DATE(Classfee.qu2_date) <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
    }
    public function findpaidamounts($a_year, $datefrom, $dateto)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Studentfees');

        // Start a new query.
        return $articles->find('all')->contain(['Students'])->where(['Studentfees.acedmicyear' => $a_year, 'Studentfees.paydate >=' => $datefrom, 'Studentfees.paydate <=' => $dateto, 'Studentfees.status' => 'Y', 'Students.status' => 'Y'])->order(['Studentfees.id' => 'ASC'])->toArray();
    }

    public function findamount1month($id, $a_year)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Classfee');
        $m = date('Y-m-d');
        // Start a new query.

        return $articles->find('all')->contain(['Classes'])->select(['qu1_fees' => $articles->find()->func()->sum('Classfee.qu1_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id'])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year, 'DATE(Classfee.qu1_date) <=' => $m])->order(['Classfee.id' => 'ASC'])->first()->toArray();
    }

    public function findpendingsfee($id, $rid)
    {

        $articles = TableRegistry::get('Studentfeepending');
        $query = $articles->find('all');
        return $query->select(['sum' => $query->func()->sum('Studentfeepending.amt')])->where(['Studentfeepending.s_id' => $id, 'Studentfeepending.r_id' => $rid])->toarray();
    }
    public function findpendingrefrencefees($id, $amt)
    {

        $articles = TableRegistry::get('Studentfeepending');
        return $articles->find('all')->where(['Studentfeepending.r_id' => $id, 'Studentfeepending.status' => 'Y', 'Studentfeepending.amt LIKE' => $amt . "%"])->first();
    }
    public function feeacknowledgement($id = null)
    {

        $student = $this->Students->find('all')->where(['Students.id' => $id, 'Students.status' => 'Y'])->first();
        $this->sitesetting('general');
        $this->set(compact('student'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
    }

    public function feeacknowledgementritesh($id = null)
    {

        $student = $this->Students->find('all')->where(['Students.id' => $id, 'Students.status' => 'Y'])->first();

        $this->set(compact('student'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
    }
    public function feeacknowledgementhistory($id = null, $aced = null)
    {

        $student = $this->Studentshistory->find('all')->where(['Studentshistory.stud_id' => $id, 'Studentshistory.status' => 'Y', 'Studentshistory.acedmicyear' => $aced])->first();
        $this->sitesetting('general');
        $this->set(compact('student'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
    }

    public function officecollection($id = null)
    {
        $session = $this->request->session();
        $this->sitesetting('general');
        $academic = $session->read('conditionlp145');
        $this->set(compact('academic'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
    }

    public function user_dailyfee()
    {
        $session = $this->request->session();
        $this->sitesetting('general');
        $datefrom = $session->read('datefrom');
        $this->set(compact('datefrom'));
        $mode = $session->read('mode');
        $dateto = $session->read('dateto');
        $this->set(compact('dateto'));
        $selectField = $session->read('selectField');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmicyear = $users['academic_year'];
        $this->set(compact('acedmicyear'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('mode', $mode);
        $this->set('selectField', $selectField);
    }

    public function findprospectus($recipetno, $formno)
    {

        $articles = TableRegistry::get('Enquires');

        return $articles->find('all')->select(['s_name', 'class_id'])->where(['Enquires.recipietno' => $recipetno, 'Enquires.formno' => $formno])->first();
    }
    public function findapplicant($recipetno, $formno)
    {

        $articles = TableRegistry::get('Applicant');

        return $articles->find('all')->select(['fname', 'middlename', 'lname', 'class_id'])->where(['Applicant.recipietno' => $recipetno, 'Applicant.sno' => $formno])->first();
    }
    public function findclass1232($id = null)
    {
        //return  $id; die;
        $articles = TableRegistry::get('Classes');
        return $articles->find('all')->select(['title', 'id'])->where(['Classes.id' => $id])->first();
    }
    public function convertString($date)
    {
        // convert date and time to seconds
        $sec = strtotime($date);

        // convert seconds into a specific format
        $date = date("d-m-Y", $sec);

        // append seconds to the date and time
        $date = $date;

        // print final date and time
        return $date;
    }

    public function findotherdetailsj($reciept = null)
    {
        $articles = TableRegistry::get('Otherfees');
        return $articles->find('all')->select(['s_id', 'pupilname'])->where(['receipt_no' => $reciept])->toarray();
    }

    public function findsection123($id = null)
    {
        //return  $id; die;
        $articles = TableRegistry::get('Sections');
        return $articles->find('all')->select(['title', 'id'])->where(['Sections.id' => $id])->first();
    }
    public function user_collectionfee()
    {

        //$this->loadModel('Otherfees');
        //$this->autoRender=false;
        $session = $this->request->session();
        $Classections = $session->read('Classectionss');
        $class_id = $session->read('class_id');
        $idds = $session->read('idds');
        $acedmicyear = $session->read('acedmicyear');
        //    echo $acedmicyear;
        $datefrom = $session->read('datefroms');
        //    echo $datefrom;
        $mode = $session->read('modes');
        $dateto = $session->read('datetos');
        $selectField = $session->read('selectFields');
        // pr($Classections); die;

        /* $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
         */
        $this->set('mode', $mode);
        $this->set('Classections', $Classections);
        $this->set('selectField', $selectField);

        $modes = implode('/', $mode);

        $rolepresents = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresents == '5') {
            $bordd = "Cbse";
        } elseif ($rolepresents == '8') {
            $bordd = "International";
        } else {

            $bordd = "CBSE-INTERNATIONAL";
        }
        $this->set('bordd', $bordd);
        ini_set('max_execution_time', 1600);

        $headerRow1 = array(
            "Fee Collection " . $bordd, "", "", "", "", "",
            "", "", "", "", ""
        );
        //$output.= implode("\t", $headerRow1)."\n";
        $headerRow2[] = "No.";
        $headerRow2[] = "Sr.No.";
        $headerRow2[] = "Receipt";
        $headerRow2[] = "PayDate";

        $headerRow2[] = "Name";
        $headerRow2[] = "Academic Year";

        if (in_array("CHEQUE", $mode)) {

            $headerRow2[] = "Cheque/DD No.";
            $headerRow2[] = "Bank";
        } elseif (in_array("DD", $mode)) {

            $headerRow2[] = "Cheque/DD No.";
            $headerRow2[] = "Bank";
        }

        if (in_array("NETBANKING", $mode)) {

            $headerRow2[] = "Ref. No.";
            $headerRow2[] = "Bank";
        } elseif (in_array("CREDIT CARD/DEBIT CARD", $mode)) {

            $headerRow2[] = "Ref. No";
            $headerRow2[] = "Bank";
        }

        foreach ($selectField as $j => $el) {
            $el = trim($el);
            $feeheadss = $this->Feesheads->find('all')->where(['Feesheads.name LIKE' => $el])->first();

            if ($feeheadss['alias'] != '') {
                $headerRow2[] = $feeheadss['alias'];
            } else {

                if ($el == "Prospectus") {
                    $headerRow2[] = "Pros. fee";
                } elseif ($el == "Registration") {
                    $headerRow2[] = "Regi. fee";
                } elseif ($el == "Quater1") {
                    $headerRow2[] = "Qtr1";
                } elseif ($el == "Quater2") {
                    $headerRow2[] = "Qtr2";
                } elseif ($el == "Quater3") {
                    $headerRow2[] = "Qtr3";
                } elseif ($el == "Quater4") {
                    $headerRow2[] = "Qtr4";
                } elseif ($el == "Discount Fee") {
                    $headerRow2[] = "Disc. fee";
                } elseif ($el == "Due Amount") {
                    $headerRow2[] = "(-)Due/ (+)Access amt.";
                } elseif ($el == "Late Fee") {
                    $headerRow2[] = "Late fee";
                } else {

                    $headerRow2[] = $el;
                }
            }
        }

        $headerRow2[] = "Cash";
        $headerRow2[] = "Cheque";
        $headerRow2[] = "DD";
        $headerRow2[] = "NetBanking";
        $headerRow2[] = "Credit/Debit";
        $headerRow2[] = "Total";
        //$output.= implode("\t", $headerRow2)."\n";

        $this->set('header', $headerRow2);

        $total = 0;
        $totalfee = 0;
        $out = 0;
        $total_discount = 0;
        $totaladmission = 0;
        $totalOther = 0;
        $totalapplicant = 0;
        $totalprospectus = 0;
        $sumtotal = 0;

        $conu = 1;
        $resultf = array();
        foreach ($Classections as $key => $element) {
            if (!in_array($element['id'], $idds)) {

                $totalsum = 0;
                $result = array();

                $result[] = $conu++;
                if ($element['type'] == "Fee") {

                    $stiiu = $this->getthisyearstudent($element['student_id'], $element['acedmicyear']);
                    $stiiu34 = $this->gethistoryyeardropstudent($element['student_id'], $element['acedmicyear']);

                    if ($stiiu['id']) {
                        $clsass = $this->findclass1232($element['student']['class_id']);
                        $section = $this->findsection123($element['student']['section_id']);
                        $result[] = $element['student']['enroll'] . " (" . $clsass['title'] . "-" . $section['title'] . ")";
                    } elseif ($stiiu34['id']) {

                        $clsasss = $this->findclass1232($stiiu34['class_id']);
                        $section = $this->findsection123($stiiu34['section_id']);
                        $result[] = $stiiu34['enroll'] . " (" . $clsasss['title'] . "-" . $section['title'] . ")";
                    } else {
                        $stiius = $this->gethistoryyearstudent($element['student_id'], $element['acedmicyear']);
                        if ($stiius['id']) {

                            if ($element['student']['class_id']) {
                                $clsass = $this->findclass1232($element['student']['class_id']);
                                $sections = $this->findsection123($element['student']['section_id']);
                            } else {

                                $clsass = $this->findclass1232($element['studentshistory']['class_id']);
                                $sections = $this->findsection123($element['student']['section_id']);
                            }
                            $result[] = $stiius['enroll'] . " (" . $clsass['title'] . "-" . $sections['title'] . ")";
                        }
                    }
                } else {

                    if ($element['type'] == "Prospectus") {

                        $prospect = $this->findprospectus($element['recipetno'], $element['formno']);
                        $cl = $this->findclass($prospect['class_id']);
                        $result[] = "Pros. (" . $cl['title'] . ")";
                    }
                    if ($element['type'] == "Registration") {

                        $applicant = $this->findapplicant($element['recipetno'], $element['formno']);
                        $cls = $this->findclass($applicant['class_id']);
                        $result[] = "Regi. (" . $cls['title'] . ")";
                    }
                }

                $result[] = $element['recipetno'];
                $result[] = $element['paydate'];

                if ($element['type'] == "Fee") {

                    if ($stiiu['id']) {
                        $t = "";
                        if ($element['student']['category'] == "Migration") {
                            $t = "(Migr.)";
                        } else {
                            $t = "";
                        }

                        $result[] = $element['student']['fname'] . " " . $element['student']['middlename'] . " " . $element['student']['lname'] . " " . $t;
                    } elseif ($stiiu34['id']) {

                        $t = "";
                        if ($stiiu34['category'] == "Migration") {
                            $t = "(Migr.)";
                        } else {
                            $t = "";
                        }

                        $result[] = $stiiu34['fname'] . " " . $stiiu34['middlename'] . " " . $stiiu34['lname'] . " " . $t;
                    } else {
                        if ($stiius['id']) {

                            $t = "";
                            if ($stiius['category'] == "Migration") {
                                $t = "(Migr.)";
                            } else {
                                $t = "";
                            }

                            $result[] = $stiius['fname'] . " " . $stiius['middlename'] . " " . $stiius['lname'] . " " . $t;
                        }
                    }
                } else {
                    if ($element['type'] == "Prospectus") {
                        $prospect = $this->findprospectus($element['recipetno'], $element['formno']);

                        $result[] = $prospect['s_name'];
                    }
                    if ($element['type'] == "Registration") {
                        $applicant = $this->findapplicant($element['recipetno'], $element['formno']);

                        $result[] = $applicant['fname'] . " " . $applicant['middlename'] . " " . $applicant['lname'];
                    }
                }
                if ($element['type'] == "Fee") {

                    if ($stiiu['id']) {
                        $result[] = $element['acedmicyear'];
                    } elseif ($stiiu34['id']) {

                        $result[] = $element['acedmicyear'];
                    } else {

                        if ($stiius['id']) {
                            $result[] = $element['acedmicyear'];
                        }
                    }
                } else {
                    $result[] = $element['acedmicyear'];
                }

                if (in_array("CHEQUE", $mode)) {

                    if ($element['cheque_no']) {
                        $result[] = $element['cheque_no'];
                    } else {

                        $result[] = "--";
                    }
                    if ($element['bank']) {
                        $result[] = $element['bank'];
                    } else {

                        $result[] = "--";
                    }
                } elseif (in_array("DD", $mode)) {
                    if ($element['cheque_no']) {
                        $result[] = $element['cheque_no'];
                    } else {

                        $result[] = "--";
                    }

                    if ($element['bank']) {
                        $result[] = $element['bank'];
                    } else {

                        $result[] = "--";
                    }
                }
                if (in_array("NETBANKING", $mode)) {
                    if ($element['ref_no']) {
                        $result[] = $element['ref_no'];
                    } else {

                        $result[] = "--";
                    }
                    if ($element['bank']) {
                        $result[] = $element['bank'];
                    } else {

                        $result[] = "--";
                    }
                } elseif (in_array("CREDIT CARD/DEBIT CARD", $mode)) {
                    if ($element['ref_no']) {
                        $result[] = $element['ref_no'];
                    } else {

                        $result[] = "--";
                    }
                    if ($element['bank']) {
                        $result[] = $element['bank'];
                    } else {

                        $result[] = "--";
                    }
                }

                $quas = array();
                $quas[] = unserialize($element['quarter']);
                $quaf = array();

                foreach ($quas as $h => $vale) {
                    $quaf = array_merge($quaf, $vale);
                }
                $qua = array();
                foreach ($quaf as $j => $t) {

                    $qua[$j] = $t;
                }

                $i = 0;
                foreach ($selectField as $sj => $el) {
                    $tj = '0';

                    $el = trim($el);

                    if ($el == "Due Amount") {

                        $findpending = $this->findpendingsfee($element['student_id'], $element['id']);

                        if ($findpending[0]['sum']) {

                            $tj -= $findpending[0]['sum'];
                            if ($findpending[0]['sum'] < 0) {
                                $totaladmissions += $findpending[0]['sum'];
                            } else {

                                $totaladmissions -= $findpending[0]['sum'];
                            }
                        } else {

                            $tj += "0";
                            $totaladmissions += '0';
                        }
                    } elseif ($el == "Prospectus") {

                        if ($element['type'] == "Fee") {
                            $tj += "0";
                            $totalprospectus += '0';
                        } elseif ($element['type'] == "Prospectus") {

                            $tj += $element['deposite_amt'];
                            $totalprospectus += $element['deposite_amt'];
                        }
                    } elseif ($el == "Registration") {

                        if ($element['type'] == "Fee") {
                            $tj += "0";
                            $totalapplicant += '0';
                        } elseif ($element['type'] == "Registration") {

                            $tj += $element['deposite_amt'];
                            $totalapplicant += $element['deposite_amt'];
                        }
                    } elseif ($el == "Quater1") {

                        foreach ($qua as $j => $te) {
                            if ($j == "Quater1") {
                                $tj += $te;

                                $totaltution1 += $te;
                            }
                        }
                    } elseif ($el == "Quater2") {

                        foreach ($qua as $j => $te) {
                            if ($j == "Quater2") {
                                $tj += $te;

                                $totaltution2 += $te;
                            }
                        }
                    } elseif ($el == "Quater3") {

                        foreach ($qua as $j => $te) {
                            if ($j == "Quater3") {
                                $tj += $te;

                                $totaltution3 += $te;
                            }
                        }
                    } elseif ($el == "Quater4") {

                        foreach ($qua as $j => $te) {
                            if ($j == "Quater4") {
                                $tj += $te;

                                $totaltution4 += $te;
                            }
                        }
                    } elseif ($el == "Late Fee") {

                        $tj += $element['lfine'];
                        $totallfine += $element['lfine'];
                    } elseif ($el == "Prev. Due") {

                        foreach ($qua as $j => $te) {

                            $iteam['quarter'] = str_replace('"', "", $j);

                            $findpendinsg = $this->findpendingrefrencefees($iteam['quarter'], $te);
                            if ($findpendinsg) {

                                $tj += $findpendinsg['amt'];
                                $totalOther += $findpendinsg['amt'];
                            }
                        }
                    } elseif ($el == "Discount Fee") {

                        $total2 = '0';
                        if ($element['discount'] != '0.00') {
                            $quasn = unserialize($element['quarter']);

                            foreach ($quasn as $jn => $tn) {

                                $quan += $tn;
                            }
                            //pr($quan);
                            $discounts = $element['discount'];

                            $tj -= $discounts;
                            $totaldiscount += $discounts;
                        }
                    } elseif ($el == "Other Discount") {

                        $adddiscount += $element['addtionaldiscount'];
                        $tj -= $element['addtionaldiscount'];
                        $totalOtherdiscount += $element['addtionaldiscount'];
                    } else {

                        $totalas = array();

                        $fgj = '';
                        $fsgj = '';
                        foreach ($qua as $j => $ted) {

                            $el = trim($el);
                            $j = trim($j);

                            if ($el == "Admission Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {

                                $fsgj = "OLD";
                            } elseif ($el == "Development Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {

                                $fsgj = "OLD";
                            } elseif ($el == "Caution Money" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {

                                $fsgj = "OLD";
                            } elseif ($element['recipetno'] == '0') {

                                $fsgj = "0";
                            }

                            if (strcasecmp($j, $el) == 0) {

                                if ($el == "Admission Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {
                                    $tj += 0;
                                    $fgj = "OLD";
                                    $element['deposite_amt'] = $element['deposite_amt'] - $ted;
                                    $totalas[$el] = 0;
                                    $totalass[] = $totalas;
                                } elseif ($el == "Development Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {
                                    $tj += 0;
                                    $fgj = "OLD";
                                    $element['deposite_amt'] = $element['deposite_amt'] - $ted;
                                    $totalas[$el] = 0;
                                    $totalass[] = $totalas;
                                } elseif ($el == "Caution Money" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {

                                    $tj += 0;
                                    $element['deposite_amt'] = $element['deposite_amt'] - $ted;
                                    $fgj = "OLD";
                                    $totalas[$el] = 0;
                                    $totalass[] = $totalas;
                                } elseif ($element['recipetno'] == '0') {

                                    $tj += 0;
                                    $fgj = "0";
                                    $element['deposite_amt'] = $element['deposite_amt'] - $ted;
                                    $totalas[$el] = 0;
                                    $totalass[] = $totalas;
                                } else {
                                    $tj += $ted;

                                    $totalas[$el] = $ted;
                                    $totalass[] = $totalas;
                                }
                            }
                        }
                        //    pr($totalas);

                    }

                    if ($fgj != '') {
                        $result[] = $fgj;
                    } elseif ($fsgj != '') {
                        $result[] = $fsgj;
                    } else {
                        $result[] = $tj;
                    }
                    $fgj = 0;
                    $fsgj = 0;
                    $totaladmission += $tj;
                    $i++;
                }
                // pr($mode);
                // pr($element); die;
                if ($element['mode'] == "CASH") {
                    $result[] = $element['deposite_amt'];
                    $totalcash += $element['deposite_amt'];
                } else {
                    $result[] = "0";
                }

                if ($element['mode'] == "CHEQUE") {
                    $result[] = $element['deposite_amt'];
                    $totalcheque += $element['deposite_amt'];
                } else {
                    $result[] = "0";
                }

                if ($element['mode'] == "DD") {
                    $result[] = $element['deposite_amt'];
                    $totaldd += $element['deposite_amt'];
                } else {
                    $result[] = "0";
                }

                if ($element['mode'] == "NETBANKING") {
                    $result[] = $element['deposite_amt'];
                    $total_netbanking += $element['deposite_amt'];
                } else {
                    $result[] = "0";
                }
                if ($element['mode'] == "CREDIT CARD/DEBIT CARD") {
                    $result[] = $element['deposite_amt'];
                    $total_credit_debit += $element['deposite_amt'];
                } else {
                    $result[] = "0";
                }

                if ($element['deposite_amt']) {
                    $result[] = $element['deposite_amt'];
                } else {

                    $result[] = '0';
                }
                $sumtotal += $element['deposite_amt'];

                $resultf[] = $result;
                //$output .=  implode("\t", $result)."\n";
            }
        }

        $this->set('resultf', $resultf);

        $result2 = array();

        $iio = 3;
        if (in_array("CHEQUE", $mode)) {
            $iio++;
            $iio++;
        } elseif (in_array("DD", $mode)) {
            $iio++;
            $iio++;
        }

        if (in_array("NETBANKING", $mode)) {
            $iio++;
            $iio++;
        } elseif (in_array("CREDIT CARD/DEBIT CARD", $mode)) {
            $iio++;
            $iio++;
        }
        $result2[] = "";
        $result2[] = "GRAND TOTAL";
        for ($x = 0; $x <= $iio; $x++) {

            $result2[] = "";
        }
        $k = 1;
        $dtotla = 0;
        foreach ($selectField as $j => $el) {

            $el = trim($el);

            if ($el == "Due Amount") {
                $dtotla -= $totaladmissions;
                $result2[] = $totaladmissions;
            } elseif ($el == "Prospectus") {
                $result2[] = $totalprospectus;
                $dtotla += $totalprospectus;
            } elseif ($el == "Registration") {
                $result2[] = $totalapplicant;
                $dtotla += $totalapplicant;
            } elseif ($el == "Quater1") {
                $result2[] = $totaltution1;
                $dtotla += $totaltution1;
            } elseif ($el == "Quater2") {
                $result2[] = $totaltution2;
                $dtotla += $totaltution2;
            } elseif ($el == "Quater3") {
                $result2[] = $totaltution3;
                $dtotla += $totaltution3;
            } elseif ($el == "Quater4") {
                $result2[] = $totaltution4;
                $dtotla += $totaltution4;
            } elseif ($el == "Late Fee") {

                $dtotla += $totallfine;
                $result2[] = $totallfine;
            } elseif ($el == "Prev. Due") {

                $dtotla += $totalOther;
                $result2[] = $totalOther;
            } elseif ($el == "Discount Fee") {

                $dtotla += "-" . $totaldiscount;
                $result2[] = "-" . $totaldiscount;
            } elseif ($el == "Other Discount") {

                $dtotla += "-" . $totalOtherdiscount;
                $result2[] = "-" . $totalOtherdiscount;
            } else {

                $res = array();
                $vsk = 0;
                foreach ($totalass as $k => $v) {
                    foreach ($v as $ks => $vs) {
                        if ($ks == $el) {

                            $vsk += $vs;
                        }
                    }
                }
                $dtotla += $vsk;
                $result2[] = $vsk;
            }

            $j++;
        }
        $result2[] = $totalcash;
        $result2[] = $totalcheque;
        $result2[] = $totaldd;
        $result2[] = $total_netbanking;
        $result2[] = $total_credit_debit;
        $result2[] = $sumtotal;

        $this->set('result2', $result2);
    }

    public function gethistoryyeardropstudent($id = null, $acedemic = null)
    {

        $articles = TableRegistry::get('DropOutStudent');
        return $articles->find('all')->where(['DropOutStudent.s_id' => $id, 'DropOutStudent.status' => 'Y'])->first();
    }
    public function getthisyearstudent($id = null, $acedemic = null)
    {

        $articles = TableRegistry::get('Students');
        return $articles->find('all')->where(['Students.id' => $id, 'Students.acedmicyear' => $acedemic])->first();
    }

    public function gethistoryyearstudent($id = null, $acedemic = null)
    {

        $articles = TableRegistry::get('Studentshistory');
        return $articles->find('all')->where(['Studentshistory.stud_id' => $id, 'Studentshistory.acedmicyear' => $acedemic, 'Studentshistory.status' => 'Y'])->first();
    }

    public function user_prospectus()
    {
        //$this->autoRender=false;
        $session = $this->request->session();
        $condition = $session->read('condition');
        $stat = $session->read('stat');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $acedmic = $users['academic_year'];
        $this->set(compact('acedmic'));

        //    echo $acedmicyear;
        $datefrom = $session->read('datefrom');

        $dateto2 = $session->read('dateto2');
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('stat', $stat);
        $this->set('t_enquiry', $condition);
        $this->set('datefrom', $datefrom);
        $this->set('dateto2', $dateto2);
    }

    public function user_supportivgenderinfo($acedmic = null)
    {
        //$this->autoRender=false;
        $session = $this->request->session();
        $gender = $session->read('gender');
        $class_id = $session->read('class_id');
        $section_id = $session->read('section_id');

        //    echo $acedmicyear;
        $this->sitesetting('general');

        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('gender', $gender);
        $this->set('class_id', $class_id);
        $this->set('acedmic', $acedmic);
        if ($session->read('section_id')) {
            $this->set('section_id', $section_id);
        } else {

            $section_id = array();
        }
        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $acedmicyear = $user['academic_year'];
        $next_academic_year = $user['next_academic_year'];

        $this->set('acedmicyear', $acedmicyear);
        $this->set('next_academic_year', $next_academic_year);
        // pr($Classections); die;
    }

    //~ public function user_supportivgenderinfo($acedmic=null){
    //~ //$this->autoRender=false;
    //~ $session = $this->request->session();

    //~ $gender=$session->read('gender');
    //~ $class_id2=$session->read('class_id');
    //~ $section_id2=$session->read('section_id');
    //~ $class_id2=explode(',',$class_id2);

    //~ //    echo $acedmicyear;

    //~ $this->viewBuilder()->layout('ajax');
    //~ $this->response->type('pdf');
    //~ $this->set('gender',$gender);
    //~ $this->set('class_id2',$class_id2);
    //~ $this->set('acedmic',$acedmic);
    //~ if($session->read('section_id')){
    //~ $section_id2=explode(',',$section_id2);

    //~ }else{

    //~ $section_id2=array();
    //~ }
    //~ $this->set('section_id2',$section_id2);
    //~ // pr($Classections); die;

    //~ }
    public function studentgenderhousepdf2($acedmic = null)
    {
        //$this->autoRender=false;
        $session = $this->request->session();
        $gender = $session->read('gender');
        $class_id = $session->read('class_id');

        $section_id = $session->read('section_id');

        $class_id = explode(',', $class_id);
        $section_id = explode(',', $section_id);

        //    echo $acedmicyear;

        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('gender', $gender);
        $this->set('class_id2', $class_id);
        $this->set('section_id2', $section_id);
        // pr($Classections); die;
        $houselist = $this->Houses->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => '1'])->order(['name' => 'ASC'])->toArray();
        $this->set('houselist', $houselist);
        $this->set('acedmic', $acedmic);
        $this->sitesetting('general');
    }
    public function user_supportivgenderinfo2($acedmic = null)
    {
        //$this->autoRender=false;
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $this->sitesetting('general');
        if ($rolepresent == '1') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        }
        $gender = "Both";

        $this->set('acedmic', $acedmic);
        $this->set(compact('gender'));

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('section_id2', $sectionslist);

        //    echo $acedmicyear;

        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');

        // pr($Classections); die;

    }

    public function studentgenderhousepdf($acedmic = null)
    {
        //$this->autoRender=false;
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $this->sitesetting('general');
        if ($rolepresent == '1') {
            $art = ['18', '19'];
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '5') {

            $art = ['18', '19'];
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1', 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        } elseif ($rolepresent == '8') {

            $art = ['18', '19'];
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3'], 'Classes.id NOT IN' => $art])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('class_id2', $classes);
        }
        $gender = "Both";

        $this->set(compact('gender'));
        $houselist = $this->Houses->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => '1'])->order(['name' => 'ASC'])->toArray();
        $this->set('houselist', $houselist);
        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('section_id2', $sectionslist);

        $this->set('acedmic', $acedmic);

        //    echo $acedmicyear;

        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');

        // pr($Classections); die;

    }

    public function user_supportividcardreport()
    {

        $session = $this->request->session();
        $classess = $session->read('classess');

        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('classess', $classess);
    }
    public function user_supportivcancelled()
    {

        ini_set('max_execution_time', 1600);
        //$this->autoRender=false;
        $session = $this->request->session();
        $Classections = $session->read('Classectionsfees');
        $class_id = $session->read('class_id');

        //    echo $acedmicyear;
        $datefrom = $session->read('datefrom');
        $status = $session->read('status');

        $dateto2 = $session->read('dateto2');
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $this->set('Classectionsfees', $Classections);
        $this->set('datefrom', $datefrom);
        $this->set('dateto2', $dateto2);

        if ($status == "Y") {

            $this->set('status', 'Deposit');
        }
        if ($status == "N") {
            $this->set('status', 'Cancelled');
        }
        // pr($Classections); die;
        /* $output="";

            ini_set('max_execution_time', 1600);

            $headerRow1 = array(
            "Cancelled Receipts From 01-04-2018 to 10-05-2018", "", "", "", "", "",
            "","","","","");
            $output.= implode("\t", $headerRow1)."\n";

            $headerRow2 = array('S.No','Recpt.No','Date','Sr.No.','Name of Student','Fathers Name','Class','Amt');
            $output.= implode("\t", $headerRow2)."\n";

            $counter=1;
            foreach($Classections as $key=>$element) {

            $s_id=$element['student']['class_id'];
            $totalsum=0;

            $result=array();
            $result[]=$counter;
            $result[]=$element['id'];
            $result[]=date('d-m-Y',strtotime($element['paydate']));
            $result[]=$element['student']['enroll'];
            $result[]=$element['student']['fname']." ".$element['student']['middlename']." ".$element['student']['lname'];
            $result[]=$element['student']['fathername'];
            $classewrw=$this->findclassess($s_id);
            $cls=$classewrw[0]['title'];
            $result[]=$cls;
            $result[]=$element['deposite_amt'];

            $output .=  implode("\t", $result)."\n";
            $counter++;
            }

            //echo $output; die;
            $filename = "CancelledReceipts.xls";
            header('Content-type: application/ms-excel');
            header('Content-Disposition: attachment; filename='.$filename);
            echo $output;die;

            $this->redirect($this->referer());

            */
    }

    public function user_supportiv5()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $Classections = $session->read('Classections');
        $class_id = $session->read('s_id');
        $acedmicyear = $session->read('acedmicyear');
        //    echo $acedmicyear;
        $datefrom = $session->read('datefrom');
        //    echo $datefrom;
        $dateto = $session->read('dateto');
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
            if (isset($datefrom)) {
                $paidamount = $this->findpaidamounts($acedmicyear, $datefrom, $dateto);
            } else {

                $paidamount = $this->findpaidamount($acedmicyear);
            }

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
            $output .= ((($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * $totalstudentcount) - ($total1) - ($total2));

            $out += (($amount[0]['qu1_fees'] + $amount[0]['qu2_fees'] + $amount[0]['qu3_fees'] + $amount[0]['qu4_fees']) * $totalstudentcount) - ($total1) - ($total2);

            $output .= "\r\n";
        }
        $output .= '"GRAND TOTAL",';
        $output .= $total . ",";
        $output .= $totalfee . ",";
        $output .= $discount . ",";
        $output .= $out . ",";
        $filename = "Fee-reportsection(" . $sec_name[0]["title"] . ").csv";
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

        // echo($class_id);
        //$classname=$this->Classes->find('all')->where(['Classes.id' =>$class_id])->first()->toArray();

        //$section_id=$session->read('section_id');
        //    $sectioname=$this->Sections->find('all')->where(['Sections.id' =>$section_id])->first()->toArray();
        //     echo($section_id);
        $fees = $session->read('fees');
        // echo($fees);
        $academicyear = $session->read('academicyear');
        //    echo($academicyear);
        $classfee = $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->where(['Classfee.academic_year' => $academicyear])->order(['Classfee.id' => 'ASC'])->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find()->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find()->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->toarray();

        $output = "";

        if ($classfee) {
            $output .= '"Student First Name",';
        }
        if ($classfee) {
            $output .= '"Last Name",';
        }
        if ($classfee) {
            $output .= '"Class",';
        }
        if ($classfee) {
            $output .= '"Section",';
        }
        if ($classfee) {
            $output .= '"Quater1",';
        }
        if ($classfee) {
            $output .= '"Quater2",';
        }
        if ($classfee) {
            $output .= '"Quater3",';
        }
        if ($classfee) {
            $output .= '"Quater4",';
        }
        if ($classfee) {
            $output .= '"Due Fees-Till-Date",';
        }
        if ($classfee) {
            $output .= '"Paid Fees",';
        }
        if ($classfee) {
            $output .= '"Discount",';
        }
        if ($classfee) {
            $output .= '"Outstanding",';
        }
        $output .= "\n";
        $feestotal = 0;
        $perticular_feestotal = 0;
        $perticular_feestotal1 = 0;
        $feestotalqus = 0;
        $feestotalqus2 = 0;
        $feestotalqus3 = 0;
        $feestotalqus4 = 0;
        $out = 0;
        $dueamt = 0;
        $total_dues_amount = 0;

        foreach ($student as $key => $value) {
            $output .= $value['fname'] . ",";

            $output .= $value['lname'] . ",";
            $class_name = $this->findclass($value['class_id']);
            $class = $class_name['title'];
            $output .= $class . ",";

            $section_name = $this->sections($value['section_id']);
            $section = $section_name[0]['title'];
            $output .= $section . ",";

            foreach ($classfee as $keys => $iteam) {

                if ($iteam['class_id'] == $value['class_id']) {
                    $output .= $iteam['qu1_fees'] . ",";
                    $output .= $iteam['qu2_fees'] . ",";
                    $output .= $iteam['qu3_fees'] . ",";
                    $output .= $iteam['qu4_fees'] . ",";
                    $feestotalqus += $iteam['qu1_fees'];
                    $feestotalqus2 += $iteam['qu2_fees'];
                    $feestotalqus3 += $iteam['qu3_fees'];
                    $feestotalqus4 += $iteam['qu4_fees'];
                }
            }

            $findamountmonth = $this->findamountmonth($value['class_id'], $academicyear); // pr($findamountmonth);
            $findamount3month = $this->findamount3month($value['class_id'], $academicyear);
            $findamount2month = $this->findamount2month($value['class_id'], $academicyear);
            $findamount1month = $this->findamount1month($value['class_id'], $academicyear);
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

                $output .= '0' . ",";
            }

            $perticularamounts = $this->findperticularamount($value['id'], $academicyear);
            $paidfeestotal = 0;
            foreach ($perticularamounts as $values) {

                $paidfeestotal += $values['fee'];
                $perticular_feestotal += $values['fee'];
            }
            $output .= $paidfeestotal . ",";

            $perticularamount = $this->findperticularamount($value['id'], $academicyear);

            if (!empty($perticularamount)) {
                $perticular_feestotal1 = 0;

                foreach ($perticularamount as $valued) {
                    $dis = $valued['amount'] - $valued['fee'];
                    $perticular_feestotal1 += $dis;
                }

                $total_discount += $perticular_feestotal1;

                $output .= $perticular_feestotal1 . ",";
            } else {
                $output .= '0' . ",";
            }

            foreach ($classfee as $keys => $iteam) {

                if ($iteam['class_id'] == $value['class_id']) {
                    $fees = $iteam['qu1_fees'] + $iteam['qu2_fees'] + $iteam['qu3_fees'] + $iteam['qu4_fees'];
                    $total_due = ($fees - $paidfeestotal - $perticular_feestotal1);

                    $output .= $total_due . ",";
                    $total_due_amount += $total_due;
                }
            }

            $output .= "\r\n";
        }

        $output .= '"GRAND TOTAL",';
        $output .= '"-",';
        $output .= '"-",';
        $output .= '"-",';
        $output .= '"-",';
        $output .= '"-",';
        $output .= '"-",';

        $output .= $feestotalqus + $feestotalqus2 + $feestotalqus3 + $feestotalqus4 . ",";
        $output .= $total_dues_amount . ",";
        $output .= $perticular_feestotal . ",";
        $output .= $total_discount . ",";
        $output .= $total_due_amount . ",";

        //die;
        $filename = "Fee-reportsection-allstudent-Till" . "(" . date('Y-m-d') . ").csv";
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
        $students = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.transportloc_id' => $id, 'Students.is_transport' => '1', 'Students.status' => 'Y'])->toArray();
        $this->set(compact('students'));
    }
    public function user_supportiv7()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $students = $session->read('students');
        $location = $session->read('location');
        $output .= '"Student Name",';
        $output .= '"Class",';
        $output .= '"Section",';
        $output .= '"Location",';
        $output .= '"Route",';
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
            $output .= $service['class']['title'] . ",";
            $output .= $service['section']['title'] . ",";
            $output .= str_replace(" ", "-", $location['name']) . ",";
            $route = $this->findroute($location['id']);
            $rty = explode(',', $route[0]['route']);
            foreach ($rty as $kew => $value) {
                $dfg = $this->findlocation($value);

                $output .= "-" . $dfg['name'];
            }
            $output .= ",";

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
        $output .= ",";
        $output .= ",";
        $output .= '"Total Amount",';
        $output .= $totaltransportamount . ",";
        $output .= $total_transport_paidamount . ",";

        $filename = "Transport-report(" . str_replace(" ", "-", $location['name']) . ").csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
        $this->redirect($this->referer());
    }

    public function hostelfee($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classections.status' => 'Y'])->order(['Classes.title' => 'ASC'])->toArray();
        //    pr($classes); die;
        $this->set(compact('classes'));

        $hostel = $this->Hostels->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Hostels.status' => 'Y'])->order(['name' => 'ASC'])->toArray();
        //    pr($classes); die;
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
        $detail = "SELECT Students.id,Students.fname,Students.mobile,Students.acedmicyear,Students.room_no,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
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

        $cond .= " AND Students.status='Y'";
        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY Students.id DESC";

        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->set('hostel', $results);
    }

    public function user_defaultermonthly()
    {

        $session = $this->request->session();

        $quaters = $session->read('quaters');

        $class_id = $session->read('class_id');

        $academicyear = $session->read('academicyear');
        $section_id = $session->read('section_id');
        $mode = $session->read('mode');
        $mode1 = $session->read('mode1');

        $totals = $session->read('totals');
        $conn = ConnectionManager::get('default');

        $detail = "SELECT Students.id,Students.fname,Students.discountcategory,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.address,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

        if (!empty($academicyear)) {

            $cond .= " AND Students.acedmicyear ='" . $academicyear . "'";
        }
        if (!empty($class_id)) {

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND Students.class_id IN(" . $stuc . ")";
        }

        if (!empty($section_id)) {

            foreach ($section_id as $gsg => $rts) {
                $consds[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $consds);
            $cond .= " AND Students.section_id IN(" . $stusc . ")";
        }

        $cond .= " AND Students.category ='NORMAL' AND Students.discountcategory !='FREE' ";
        $cond .= " AND Students.status ='Y'";

        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY csort ASC, sectiontitle ASC,Students.enroll ASC LIMIT 0,2500";

        $results = $conn->execute($SQL)->fetchAll('assoc');

        if ((!empty($academicyear) && $academicyear == "2018-19") || (!empty($academicyear) && $academicyear == "2019-20")) {

            $detail2 = "SELECT Studentshistory.stud_id,Studentshistory.fname,Studentshistory.address,Studentshistory.fathername,Studentshistory.board_id,Studentshistory.fee_submittedby,Studentshistory.sms_mobile,Studentshistory.middlename,Studentshistory.lname,Studentshistory.mobile,Studentshistory.discountcategory,Studentshistory.acedmicyear,Studentshistory.enroll,Studentshistory.actionhistory,Studentshistory.h_id,Studentshistory.class_id,Studentshistory.section_id,Studentshistory.admissionyear,Studentshistory.status,  Classes.title as classtitle ,Classes.sort as csort ,Sections.title as sectiontitle FROM `studentshistory` Studentshistory LEFT JOIN classes Classes ON Studentshistory.`class_id` = Classes.id LEFT JOIN sections Sections ON Studentshistory.`section_id` = Sections.id   WHERE  1=1 ";
            $cond2 = ' ';

            if (!empty($academicyear)) {

                $cond2 .= " AND Studentshistory.acedmicyear ='" . $academicyear . "'";
                $this->set('academicyear', $academicyear);
            }

            if (!empty($class_id)) {

                foreach ($class_id as $gg => $rt) {
                    $condsk[] = "'" . $rt . "'";
                }
                $stuck = implode(',', $condsk);
                $cond2 .= " AND Studentshistory.class_id IN(" . $stuck . ")";
            }

            if (!empty($section_id)) {

                foreach ($section_id as $gsg => $rts) {
                    $consdsl[] = "'" . $rts . "'";
                }
                $stusck = implode(',', $consdsl);
                $cond2 .= " AND Studentshistory.section_id IN(" . $stusck . ")";
            }

            $cond2 .= " AND Studentshistory.category ='NORMAL' AND Studentshistory.discountcategory !='FREE' AND Studentshistory.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Studentshistory.fname ASC";

            $SQL2 = $detail2 . $cond2;
            $results2 = $conn->execute($SQL2)->fetchAll('assoc');
        }

        //$results=$session->read('results');

        $this->set(compact('academicyear'));

        //~ $class_id=$session->read('class_id');
        //~ $section_id=$session->read('section_id');

        //~ $classes = $this->Classes->find('all')->select(['id', 'title'])->where(['id' =>$class_id])->order(['id' => 'ASC'])->first();

        //~ $sections = $this->Sections->find('all')->select(['id', 'title'])->where(['id' =>$section_id])->order(['title' => 'ASC'])->first();
        //~ $classname=$classes['title'];

        //~ if($sections['title']){
        //~ $classname.='('.$sections['title'].')';

        //~ }
        $classname = date('d-m-Y');

        $this->autoRender = false;

        ini_set('max_execution_time', 1600);

        $headerRow2[] = "S.No";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student Name";
        $headerRow2[] = "Class-Section";
        $headerRow2[] = "Father Name";
        $headerRow2[] = "Address";
        $headerRow2[] = "Mobile";
        $headerRow2[] = "Discount";

        foreach ($quaters as $j => $el) {
            $el = trim($el);

            $headerRow2[] = $el;
        }
        $headerRow2[] = "Previous Dues";
        $headerRow2[] = "(-)Discount Fee";
        $headerRow2[] = "Due Fees";
        $output .= implode("\t", $headerRow2) . "\n";

        $counter = '';
        $total_due_amount = '';

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                if ($mode1 == 1) {

                    $findpending = $this->findpendingssinglefee($service['id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                $result[] = $service['fname'] . " " . $service['middlename'] . " " . $service['lname'];

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];

                $service['address'] = str_replace(",", "-", $service['address']);
                $service['address'] = str_replace(" ", "-", $service['address']);
                $service['address'] = str_replace("-", " ", $service['address']);
                $service['address'] = str_replace("--", " ", $service['address']);

                $result[] = $service['address'];
                $result[] = $service['sms_mobile'];

                if ($service['discountcategory'] != '') {
                    $result[] = $service['discountcategory'];
                } else {

                    $result[] = '-';
                }

                $studentfees = $this->finddisountstudent($service['id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['id'], 'Students.acedmicyear' => $academicyear])->first();
                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }
                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }

                    if ($oldenrool == 1991) {
                        $student_datas35s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Quater1%'])->toarray();

                        $studentfees = array_merge($studentfees, $student_datas35s);
                    }
                }
                $studentfees2 = $this->finddisountstudent2($service['id']);

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                foreach ($quaters as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Admission Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }
                        if ($dff == '0') {

                            $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                            if (in_array($ty, $collect)) {
                                $ty = "Tution Fee";
                            } elseif ($ty == "Admission Fee") {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            } elseif ($ty == "Annual Charges") {

                                $ty = "Annual Charges";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            $divide = '3';
                            if ($ty == "April" || $ty == "May" || $ty == "June") {

                                $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                                $cui = $err[0]['qu1_fees'] / $divide;

                                $result[] = $cui;
                                $q1 += $cui;
                                $fedd += $cui;
                            } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                                $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                                $result[] = $cui2;
                                $q2 += $cui2;
                                $fedd += $cui2;
                            } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                                $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                                $result[] = $cui3;
                                $q3 += $cui3;
                                $fedd += $cui3;
                            } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                                $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                                $result[] = $cui4;
                                $q4 += $cui4;
                                $fedd += $cui4;
                            } elseif ($ty == "Admission Fee" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                                if (!in_array("Admission Fee", $qua2)) {

                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Admission Fee" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                                if (!in_array("Admission Fee", $qua2)) {

                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Admission Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q1 += $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];

                                    $resultss[] = $err[0]['qu1_fees'];
                                    $tysd++;
                                }
                            }
                        }
                    } else {
                        $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                        if (in_array($ty, $collect)) {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        } elseif ($ty == "Annual Charges") {

                            $tys = "Annual Charges";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                        $divide = '3';
                        if ($ty == "April" || $ty == "May" || $ty == "June") {
                            $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                            $cui = $err[0]['qu1_fees'] / $divide;

                            $result[] = $cui;
                            $q1 += $cui;
                            $fedd += $cui;
                        } elseif ($ty == "July" || $ty == "August" || $ty == "September") {
                            $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                            $result[] = $cui2;
                            $q2 += $cui2;
                            $fedd += $cui2;
                        } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                            $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;
                            $result[] = $cui3;
                            $q3 += $cui3;
                            $fedd += $cui3;
                        } elseif ($ty == "January" || $ty == "February" || $ty == "March") {
                            $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;
                            $result[] = $cui4;
                            $q4 += $cui4;
                            $fedd += $cui4;
                        } elseif ($ty == "Admission Fee" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                            if (!in_array("Admission Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $service['board_id'] == '1') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Admission Fee" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                            if (!in_array("Admission Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '5995' && $service['board_id'] != '1') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {
                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Admission Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = $err[0]['qu1_fees'];
                                $resultss[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                                $tysd++;
                            }
                        }
                    }
                }

                $findpending = $this->findpendingssinglefee($service['id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak);
                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2);
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = $fedd;
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = $fedd;
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > 0) {

                        $counter++;
                        $output .= implode("\t", $result) . "\n";
                    }
                }
            }
        }

        if (isset($results2) && !empty($results2)) {
            $fees = 0;

            if ($counter == '') {
                $counter = '1';
            }
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            if ($total_due_amount == '') {
                $total_due_amount = 0;
            }
            $total_dues_amount = 0;

            foreach ($results2 as $h => $service) {
                if ($mode1 == 1) {
                    $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                if ($service['actionhistory'] == "PROMOTE") {
                    $result[] = $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . " (PR)";
                } elseif ($service['actionhistory'] == "REPEAT") {
                    $result[] = $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . " (RE)";
                }
                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];

                $service['address'] = str_replace(",", "-", $service['address']);
                $service['address'] = str_replace(" ", "-", $service['address']);
                $service['address'] = str_replace("-", " ", $service['address']);
                $service['address'] = str_replace("--", " ", $service['address']);

                $result[] = $service['address'];
                $result[] = $service['sms_mobile'];

                if ($service['discountcategory'] != '') {
                    $result[] = $service['discountcategory'];
                } else {

                    $result[] = '-';
                }

                $studentfees = $this->finddisountstudent($service['stud_id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['stud_id']])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }

                    if ($oldenrool == 1991) {
                        $student_datas35s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Quater1%'])->toarray();

                        $studentfees = array_merge($studentfees, $student_datas35s);
                    }
                }

                $studentfees2 = $this->finddisountstudent2($service['stud_id']);
                //pr( $studentfees); die;
                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }
                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                foreach ($quaters as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }
                        if ($dff == '0') {

                            $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                            if (in_array($ty, $collect)) {
                                $ty = "Tution Fee";
                            } elseif ($ty == "Admission Fee") {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            } elseif ($ty == "Annual Charges") {

                                $tys = "Annual Charges";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            $divide = '3';
                            if ($ty == "April" || $ty == "May" || $ty == "June") {

                                $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                                $cui = $err[0]['qu1_fees'] / $divide;

                                $result[] = $cui;
                                $q1 += $cui;
                                $fedd += $cui;
                            } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                                $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                                $result[] = $cui2;
                                $q2 += $cui2;
                                $fedd += $cui2;
                            } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                                $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                                $result[] = $cui3;
                                $q3 += $cui3;
                                $fedd += $cui3;
                            } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                                $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                                $result[] = $cui4;
                                $q4 += $cui4;
                                $fedd += $cui4;
                            } elseif ($ty == "Admission Fee" && $service['enroll'] > '6454') {
                                if (!in_array("Admission Fee", $qua2)) {

                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q1 += $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];

                                    $resultss[] = $err[0]['qu1_fees'];
                                    $tysd++;
                                }
                            }
                        }
                    } else {
                        $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                        if (in_array($ty, $collect)) {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        } elseif ($ty == "Annual Charges") {

                            $tys = "Annual Charges";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                        $divide = '3';
                        if ($ty == "April" || $ty == "May" || $ty == "June") {

                            $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                            $cui = $err[0]['qu1_fees'] / $divide;

                            $result[] = $cui;
                            $q1 += $cui;
                            $fedd += $cui;
                        } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                            $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                            $result[] = $cui2;
                            $q2 += $cui2;
                            $fedd += $cui2;
                        } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                            $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                            $result[] = $cui3;
                            $q3 += $cui3;
                            $fedd += $cui3;
                        } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                            $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                            $result[] = $cui4;
                            $q4 += $cui4;
                            $fedd += $cui4;
                        } elseif ($ty == "Admission Fee" && $service['enroll'] > '6454') {
                            if (!in_array("Admission Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '6454') {
                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '6454') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {
                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = $err[0]['qu1_fees'];
                                $resultss[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                                $tysd++;
                            }
                        }
                    }
                }

                $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak);
                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2);
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = $fedd;
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = $fedd;
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = $fedd;
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > 0) {

                        $counter++;
                        $output .= implode("\t", $result) . "\n";
                    }
                }
            }
        }

        $headerRow3[] = "Total";
        $headerRow3[] = $totals;
        $output .= implode("\t", $headerRow3) . "\n";

        $filename = "DefaulterReports_" . $classname . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
    }

    public function bankuser_defaulterlatests()
    {

        $session = $this->request->session();

        $results = $session->read('resultssss');
        $headerRows2 = $session->read('headerRows2');
        $quaters = $session->read('quaterss');
        $totals = $session->read('totals');
        $quatersselected = $session->read('quatersselected');

        ini_set('max_execution_time', 1600);
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $latefee = $users['latefee'];

        $headerRow2[] = "S.No";
        $headerRow2[] = "Institute ID";
        $headerRow2[] = "Branch Name";
        $headerRow2[] = "Academic Year";
        $headerRow2[] = "StudentID";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student Name";
        $headerRow2[] = "Email";
        $headerRow2[] = "Class";
        $headerRow2[] = "Section";
        $headerRow2[] = "Father Name";
        $headerRow2[] = "Fee Submitted By";
        $headerRow2[] = "Mobile";
        $headerRow2[] = "MailId";
        $headerRow2[] = "DiscountCategory";

        if ($quaters) {
            foreach ($quaters as $j => $t) {
                $headerRow2[] = $t;
            }
        }

        $headerRow2[] = "Practical Fee";
        $headerRow2[] = "Previous Due";

        $headerRow2[] = "(-)Discount";
        $headerRow2[] = "Actual Tution Fee";
        $headerRow2[] = "Start Date";
        $headerRow2[] = "Due Date";
        $headerRow2[] = "Late Fee/day";
        $headerRow2[] = "Expiry Date";

        $this->set('headerRow2', $headerRow2);

        $counter = '';
        $total_due_amount = '';

        if (isset($results) && !empty($results)) {
            foreach ($results as $h => $service) {

                $rt = explode('-', $service[1]);
                $result = array();

                $findstud = $this->Students->find('all')->where(['Students.id' => $rt[1], 'Students.status' => 'Y'])->first();
                $result[] = $service[0];
                $result[] = "SANSKAR";
                $result[] = "JAIPUR";
                $result[] = $findstud['acedmicyear'];
                if ($findstud['board_id'] == '1') {
                    $result[] = 'C' . $findstud['enroll'];
                } else {
                    $result[] = 'IC' . $findstud['enroll'];
                }
                $result[] = $findstud['enroll'];
                $result[] = $findstud['fname'] . " " . $findstud['middlename'] . " " . $findstud['lname'];

                $result[] = $findstud['username'];

                $s_id = $findstud['class_id'];
                $c_id = $findstud['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'];
                $result[] = $sectionssss['title'];
                $result[] = $findstud['fathername'];
                $result[] = $findstud['fee_submittedby'];
                $result[] = $findstud['sms_mobile'];
                $result[] = "-";
                if ($findstud['discountcategory'] != '') {
                    $result[] = trim($findstud['discountcategory']);
                } else {
                    $result[] = "-";
                }

                if ($quatersselected == "Quater1") {
                    $result[] = $service[6];
                    $result[] = $service[7];
                    $result[] = $service[8];
                    $result[] = $service[9];

                    $discountd = explode('_(', $service[10]);
                    $result[] = abs($discountd[0]);
                    $result[] = strip_tags($service[11]);
                } elseif ($quatersselected == "Quater2") {
                    $result[] = $service[6];
                    $result[] = $service[7];
                    $result[] = $service[8];
                    $result[] = $service[9];
                    $result[] = $service[10];
                    $discountd = explode('_(', $service[11]);

                    $result[] = abs($discountd[0]);
                    $result[] = strip_tags($service[12]);
                } elseif ($quatersselected == "Quater3") {
                    $result[] = $service[6];
                    $result[] = $service[7];
                    $result[] = $service[8];
                    $result[] = $service[9];
                    $result[] = $service[10];
                    $result[] = $service[11];
                    $discountd = explode('_(', $service[12]);
                    $result[] = abs($discountd[0]);
                    $result[] = strip_tags($service[13]);
                } elseif ($quatersselected == "Quater4") {
                    $result[] = $service[6];
                    $result[] = $service[7];
                    $result[] = $service[8];
                    $result[] = $service[9];
                    $result[] = $service[10];
                    $result[] = $service[11];
                    $result[] = $service[12];
                    $discountd = explode('_(', $service[13]);
                    $result[] = abs($discountd[0]);
                    $result[] = strip_tags($service[14]);
                }

                if (in_array("Quater1", $quaters)) {
                    $result[] = '01-04-' . date('Y');
                } elseif (in_array("Quater2", $quaters)) {
                    $result[] = '01-07-' . date('Y');
                } elseif (in_array("Quater3", $quaters)) {
                    $result[] = '01-10-' . date('Y');
                } elseif (in_array("Quater4", $quaters)) {
                    $result[] = '01-01-' . date('Y', strtotime('+1 years'));
                }

                if (in_array("Quater1", $quaters)) {
                    $result[] = '10-04-' . date('Y');
                } elseif (in_array("Quater2", $quaters)) {
                    $result[] = '10-07-' . date('Y');
                } elseif (in_array("Quater3", $quaters)) {
                    $result[] = '10-10-' . date('Y');
                } elseif (in_array("Quater4", $quaters)) {
                    $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                }

                $result[] = $latefee;

                if (in_array("Quater1", $quaters)) {
                    $result[] = '10-04-' . date('Y');
                } elseif (in_array("Quater2", $quaters)) {
                    $result[] = '10-07-' . date('Y');
                } elseif (in_array("Quater3", $quaters)) {
                    $result[] = '10-10-' . date('Y');
                } elseif (in_array("Quater4", $quaters)) {
                    $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                }

                $counter++;
                $resultf2[] = $result;
            }
        }

        $this->set('result', $resultf2);
    }

    public function user_defaulter()
    {

        $session = $this->request->session();

        $results = $session->read('resultsss');
        $headerRows2 = $session->read('headerRow2');
        $totals = $session->read('totals');

        $this->autoRender = false;

        ini_set('max_execution_time', 1600);
        if ($headerRows2) {
            foreach ($headerRows2 as $j => $t) {
                $headerRow2[] = $t;
            }
        }
        $output .= implode("\t", $headerRow2) . "\n";

        $counter = '';
        $total_due_amount = '';

        if (isset($results) && !empty($results)) {
            foreach ($results as $h => $service) {

                $ss = sizeof($service);

                $rt = explode('-', $service[1]);
                $result = array();
                for ($i = 0; $i <= $ss; $i++) {

                    if ($service[$i]) {
                        if ($i == 1) {
                            $result[] = $rt[0];
                        } else {
                            $result[] = strip_tags($service[$i]);
                        }
                    } else {
                        $result[] = '-';
                    }
                }
                $output .= implode("\t", $result) . "\n";
            }
        }

        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "Total";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";
        $headerRow3[] = "";

        $headerRow3[] = $totals;
        $output .= implode("\t", $headerRow3) . "\n";

        $filename = "DefaulterReports_" . date('d-m-Y h:i:s A') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
    }

    public function findpendingssinglefee($id, $acedmicyear)
    {

        $articles = TableRegistry::get('Studentfeepending');
        $query = $articles->find('all');
        return $query->contain(['Studentfees'])->select(['sum' => $query->func()->sum('Studentfeepending.amt')])->where(['Studentfeepending.s_id' => $id, 'Studentfeepending.status' => 'N', 'Studentfees.acedmicyear' => $acedmicyear, 'Studentfeepending.amt >' => '0'])->toarray();
    }

    public function finddisountstudent($stid, $a_year)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Studentfees');

        // Start a new query.
        return $articles->find('all')->where(['Studentfees.student_id' => $stid, 'Studentfees.acedmicyear' => $a_year, 'Studentfees.status' => 'Y'])->order(['Studentfees.id' => 'ASC'])->toArray();
    }
    public function finddisountstudent2($stid)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Studentfees');

        // Start a new query.
        return $articles->find('all')->where(['Studentfees.student_id' => $stid, 'Studentfees.status' => 'Y'])->order(['Studentfees.id' => 'ASC'])->toArray();
    }
    public function findfeeheadsamount($id, $a_year, $feeheads)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Classfee');

        // Start a new query.

        return $articles->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->select([
            'qu1_fees' => $articles->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $articles->find('all')->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $articles->find('all')->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $articles->find('all')->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->where(['Classfee.class_id' => $id, 'Classfee.fee_h_id' => $feeheads, 'Classfee.academic_year' => $a_year])->order(['Classfee.id' => 'ASC'])->toArray();
    }

    public function findfeeheadsid($name)
    {

        $articles = TableRegistry::get('Feesheads');
        return $articles->find('all')->where(['Feesheads.name' => $name])->first();
    }
    public function defaultersearchbyid($id = null, $acedmicyear = null)
    {

        $conn = ConnectionManager::get('default');

        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = "Quater4";
        $mode = $this->request->data['mode'];

        $this->set(compact('mode'));
        $detail = "SELECT Students.id,Students.fname,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.discountcategory,Students.acedmicyear,Students.enroll,Students.oldenroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id WHERE  1=1 ";
        $cond = ' ';

        if (!empty($id)) {

            $cond .= " AND Students.id ='" . $id . "'";
        }

        if (!empty($acedmicyear)) {

            $cond .= " AND Students.acedmicyear LIKE '" . $acedmicyear . "' ";
        }
        if (!empty($class_id)) {

            $cond .= " AND Students.class_id LIKE '" . $class_id . "' ";

            $this->set('class_id', $class_id);
        }

        if (!empty($section_id)) {

            $cond .= " AND Students.section_id LIKE '" . $section_id . "' ";

            $this->set('section_id', $section_id);
        } else {
        }
        $cond .= " AND Students.category ='NORMAL' AND Students.discountcategory !='FREE' AND Students.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Students.fname ASC";

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');

        $academicyear = $acedmicyear;
        $this->set(compact('academicyear'));

        ini_set('max_execution_time', 1600);

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['id']])->first();

                $oldenrool = $service['oldenroll'];
                $rolepresents = $service['board_id'];

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                $tys = "Miscellaneous Fee";
                $feesheads = $this->findfeeheadsid($tys);
                $errs = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheads['id']);
                if (!in_array('Miscellaneous Fee', $qua)) {

                    $result[] = $errs[0]['qu1_fees'];
                    $q1 += $errs[0]['qu1_fees'];
                    $fedd += $errs[0]['qu1_fees'];
                }

                $ty = "Tution Fee";
                $feeshead = $this->findfeeheadsid($ty);
                $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                if (!in_array('Quater1', $qua)) {

                    $result[] = $err[0]['qu1_fees'];
                    $q1 = $err[0]['qu1_fees'];
                    $fedd += $err[0]['qu1_fees'];
                }
                if (!in_array('Quater2', $qua)) {

                    $result[] = $err[0]['qu2_fees'];
                    $q2 = $err[0]['qu2_fees'];
                    $fedd += $err[0]['qu2_fees'];
                }

                if (!in_array('Quater3', $qua)) {

                    $result[] = $err[0]['qu3_fees'];
                    $q3 = $err[0]['qu3_fees'];
                    $fedd += $err[0]['qu3_fees'];
                }

                if (!in_array('Quater4', $qua)) {

                    $result[] = $err[0]['qu4_fees'];
                    $q4 = $err[0]['qu4_fees'];
                    $fedd += $err[0]['qu4_fees'];
                }

                $findpending = $this->findpendingssinglefee($service['id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($fedd != 0) {

                        if ($ddf) {

                            if ($q1) {

                                $tak += $ddf * $q1 / 100;
                            }
                            if ($q2) {

                                $tak += $ddf * $q2 / 100;
                            }
                            if ($q3) {

                                $tak += $ddf * $q3 / 100;
                            }
                            if ($q4) {

                                $tak += $ddf * $q4 / 100;
                            }

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        if ($total_due_amount) {
            return "<span style='color:red;'>" . $total_due_amount . "*</span>";
        } else {

            return "--";
        }
    }

    public function defaultersearchbyidhistory($id = null, $acedmicyear = null)
    {

        $conn = ConnectionManager::get('default');

        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = "Quater4";
        $mode = $this->request->data['mode'];

        $this->set(compact('mode'));
        $detail = "SELECT Studentshistory.stud_id,Studentshistory.fname,Studentshistory.fathername,Studentshistory.board_id,Studentshistory.fee_submittedby,Studentshistory.sms_mobile,Studentshistory.middlename,Studentshistory.lname,Studentshistory.mobile,Studentshistory.discountcategory,Studentshistory.acedmicyear,Studentshistory.enroll,Studentshistory.oldenroll,Studentshistory.h_id,Studentshistory.class_id,Studentshistory.section_id,Studentshistory.admissionyear,Studentshistory.status,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `studentshistory` Studentshistory LEFT JOIN classes Classes ON Studentshistory.`class_id` = Classes.id LEFT JOIN sections Sections ON Studentshistory.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

        if (!empty($id)) {

            $cond .= " AND Studentshistory.stud_id ='" . $id . "'";
        }

        if (!empty($acedmicyear)) {

            $cond .= " AND Studentshistory.acedmicyear LIKE '" . $acedmicyear . "' ";
        }

        $cond .= " AND Studentshistory.category ='NORMAL' AND Studentshistory.discountcategory !='FREE' AND Studentshistory.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Studentshistory.fname ASC";

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');

        $academicyear = $acedmicyear;
        $this->set(compact('academicyear'));

        ini_set('max_execution_time', 1600);

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['stud_id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['stud_id']])->first();

                $oldenrool = $service['oldenroll'];
                $rolepresents = $service['board_id'];

                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.acedmicyear' => $acedmicyear, 'Studentfees.status' => 'Y'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }
                }

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                $tys = "Miscellaneous Fee";
                $feesheads = $this->findfeeheadsid($tys);
                $errs = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheads['id']);

                if (!in_array('Miscellaneous Fee', $qua)) {

                    $result[] = $errs[0]['qu1_fees'];
                    $q1 += $errs[0]['qu1_fees'];
                    $fedd += $errs[0]['qu1_fees'];
                }

                $ty = "Tution Fee";
                $feeshead = $this->findfeeheadsid($ty);
                $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                if (!in_array('Quater1', $qua)) {

                    $result[] = $err[0]['qu1_fees'];
                    $q1 = $err[0]['qu1_fees'];
                    $fedd += $err[0]['qu1_fees'];
                }
                if (!in_array('Quater2', $qua)) {

                    $result[] = $err[0]['qu2_fees'];
                    $q2 = $err[0]['qu2_fees'];
                    $fedd += $err[0]['qu2_fees'];
                }

                if (!in_array('Quater3', $qua)) {

                    $result[] = $err[0]['qu3_fees'];
                    $q3 = $err[0]['qu3_fees'];
                    $fedd += $err[0]['qu3_fees'];
                }

                if (!in_array('Quater4', $qua)) {

                    $result[] = $err[0]['qu4_fees'];
                    $q4 = $err[0]['qu4_fees'];
                    $fedd += $err[0]['qu4_fees'];
                }

                $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($fedd != 0) {

                        if ($ddf) {

                            if ($q1) {

                                $tak += $ddf * $q1 / 100;
                            }
                            if ($q2) {

                                $tak += $ddf * $q2 / 100;
                            }
                            if ($q3) {

                                $tak += $ddf * $q3 / 100;
                            }
                            if ($q4) {

                                $tak += $ddf * $q4 / 100;
                            }

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }
        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();

        $db = $user['db'];

        if ($total_due_amount && $db == "sanskar") {

            return $total_due_amount;
        } else {

            return "--";
        }
    }

    public function defaultersearchbyidhistoryssd($id = null, $acedmicyear = null)
    {

        $conn = ConnectionManager::get('default');

        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = "Quater4";
        $mode = $this->request->data['mode'];
        $this->set(compact('mode'));
        $detail = "SELECT Studentshistory.stud_id,Studentshistory.fname,Studentshistory.fathername,Studentshistory.board_id,Studentshistory.fee_submittedby,Studentshistory.sms_mobile,Studentshistory.middlename,Studentshistory.lname,Studentshistory.mobile,Studentshistory.discountcategory,Studentshistory.acedmicyear,Studentshistory.enroll,Studentshistory.h_id,Studentshistory.class_id,Studentshistory.section_id,Studentshistory.admissionyear,Studentshistory.status,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `studentshistory` Studentshistory LEFT JOIN classes Classes ON Studentshistory.`class_id` = Classes.id LEFT JOIN sections Sections ON Studentshistory.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

        if (!empty($id)) {

            $cond .= " AND Studentshistory.stud_id ='" . $id . "'";
        }

        if (!empty($acedmicyear)) {

            $cond .= " AND Studentshistory.acedmicyear LIKE '" . $acedmicyear . "' ";
        }
        if (!empty($class_id)) {

            $cond .= " AND Studentshistory.class_id LIKE '" . $class_id . "' ";

            $this->set('class_id', $class_id);
        }

        if (!empty($section_id)) {

            $cond .= " AND Studentshistory.section_id LIKE '" . $section_id . "' ";

            $this->set('section_id', $section_id);
        } else {

            //    $this->set('section_id','0');
        }
        $cond .= " AND Studentshistory.category ='NORMAL' AND Studentshistory.discountcategory !='FREE' AND Studentshistory.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Studentshistory.fname ASC";

        if (!empty($quarter)) {
            if ($quarter == "Quater1") {
                $quater[] = "Admission Fee";
                $quater[] = "Development Fee";
                $quater[] = "Caution Money";
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";

                $quaters[] = "Adm. Fee";
                $quaters[] = "Dev. Fee";
                $quaters[] = "Ca. Money";
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
            } elseif ($quarter == "Quater2") {

                $quater[] = "Admission Fee";
                $quater[] = "Development Fee";
                $quater[] = "Caution Money";
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";

                $quaters[] = "Adm. Fee";
                $quaters[] = "Dev. Fee";
                $quaters[] = "Ca. Money";
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater2") {

                $quater[] = "Admission Fee";
                $quater[] = "Development Fee";
                $quater[] = "Caution Money";
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";

                $quaters[] = "Adm. Fee";
                $quaters[] = "Dev. Fee";
                $quaters[] = "Ca. Money";
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater3") {

                $quater[] = "Admission Fee";
                $quater[] = "Development Fee";
                $quater[] = "Caution Money";
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";

                $quaters[] = "Adm. Fee";
                $quaters[] = "Dev. Fee";
                $quaters[] = "Ca. Money";
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
            } elseif ($quarter == "Quater4") {

                $quater[] = "Admission Fee";
                $quater[] = "Development Fee";
                $quater[] = "Caution Money";
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";
                $quater[] = "Quater4";

                $quaters[] = "Adm. Fee";
                $quaters[] = "Dev. Fee";
                $quaters[] = "Ca. Money";
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
                $quaters[] = "Quater4";
            }
        } else {

            $quater[] = "Admission Fee";
            $quater[] = "Development Fee";
            $quater[] = "Caution Money";
            $quater[] = "Miscellaneous Fee";
            $quater[] = "Quater1";
            $quater[] = "Quater2";
            $quater[] = "Quater3";
            $quater[] = "Quater4";

            $quaters[] = "Adm. Fee";
            $quaters[] = "Dev. Fee";
            $quaters[] = "Ca. Money";
            $quaters[] = "Misc. Fee";
            $quaters[] = "Quater1";
            $quaters[] = "Quater2";
            $quaters[] = "Quater3";
            $quaters[] = "Quater4";
        }

        $this->set('quaters', $quater);

        $this->set('quatersf', $quaters);

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');

        $academicyear = $acedmicyear;
        $this->set(compact('academicyear'));

        ini_set('max_execution_time', 1600);

        $headerRow2[] = "S.No";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student";
        $headerRow2[] = "Class-Section";
        $headerRow2[] = "Father";
        $headerRow2[] = "Mobile";

        foreach ($quaters as $j => $el) {
            $el = trim($el);

            $headerRow2[] = $el;
        }
        $headerRow2[] = "Previous Dues";
        $headerRow2[] = "(-)Discount Fee";
        $headerRow2[] = "Due Fees";

        $this->set(compact('headerRow2'));

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['stud_id'], $academicyear);

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                foreach ($quater as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }
                        if ($dff == '0') {

                            if ($ty == "Quater1") {

                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater2") {
                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater3") {

                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater4") {

                                $ty = "Tution Fee";
                            } elseif ($ty == "Admission Fee") {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            if ($ty == "Quater1") {

                                $result[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                            } elseif ($ty == "Quater2") {

                                $result[] = $err[0]['qu2_fees'];
                                $q2 += $err[0]['qu2_fees'];
                                $fedd += $err[0]['qu2_fees'];
                            } elseif ($ty == "Quater3") {

                                $result[] = $err[0]['qu3_fees'];
                                $q3 += $err[0]['qu3_fees'];
                                $fedd += $err[0]['qu3_fees'];
                            } elseif ($ty == "Quater4") {

                                $result[] = $err[0]['qu4_fees'];
                                $q4 += $err[0]['qu4_fees'];
                                $fedd += $err[0]['qu4_fees'];
                            } elseif ($ty == "Admission Fee" && $service['enroll'] > '6454') {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454') {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454') {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q1 += $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];

                                    $resultss[] = $err[0]['qu1_fees'];
                                    $tysd++;
                                }
                            }
                        }
                    } else {
                        if ($ty == "Quater1") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater2") {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater3") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater4") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                        if ($ty == "Quater1") {

                            $result[] = $err[0]['qu1_fees'];
                            $q1 += $err[0]['qu1_fees'];
                            $fedd += $err[0]['qu1_fees'];
                        } elseif ($ty == "Quater2") {

                            $result[] = $err[0]['qu2_fees'];
                            $q2 += $err[0]['qu2_fees'];
                            $fedd += $err[0]['qu2_fees'];
                        } elseif ($ty == "Quater3") {

                            $result[] = $err[0]['qu3_fees'];
                            $q3 += $err[0]['qu3_fees'];
                            $fedd += $err[0]['qu3_fees'];
                        } elseif ($ty == "Quater4") {

                            $result[] = $err[0]['qu4_fees'];
                            $q4 += $err[0]['qu4_fees'];
                            $fedd += $err[0]['qu4_fees'];
                        } elseif ($ty == "Admission Fee" && $service['enroll'] > '5974') {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '5974') {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '5974') {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {

                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = $err[0]['qu1_fees'];
                                $resultss[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                                $tysd++;
                            }
                        }
                    }
                }

                //~ $findpending=$this->findpendingssinglefee($service['stud_id'],$academicyear);

                //~ if($findpending[0]['sum']){     $result[]= $findpending[0]['sum'];   $fedd +=$findpending[0]['sum'];   }else{ $result[]="-";   }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        if ($total_due_amount) {
            return $total_due_amount;
        } else {

            return "--";
        }
    }

    public function banknewsearch()
    {

        $conn = ConnectionManager::get('default');

        $academicyear = $this->request->data['acedmicyear'];
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = $this->request->data['quarter'];
        $mode = $this->request->data['mode'];
        $mode1 = $this->request->data['mode1'];
        $this->set('quatersselected', $quarter);
        $this->set(compact('mode'));
        $this->set(compact('mode1'));
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $previous_year = $users['previous_year'];
        $detail = "SELECT Students.id,Students.fname,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.discountcategory,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,Students.comp_sid,Students.opt_sid,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id   WHERE  1=1 ";
        $cond = ' ';

        if (!empty($academicyear)) {

            $cond .= " AND Students.acedmicyear ='" . $academicyear . "'";
            $this->set('academicyear', $academicyear);
        }

        if (!empty($class_id)) {
            $this->set('class_id', $class_id);

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND Students.class_id IN(" . $stuc . ")";
        }

        if (!empty($section_id)) {
            $this->set('section_id', $section_id);
            foreach ($section_id as $gsg => $rts) {
                $consds[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $consds);
            $cond .= " AND Students.section_id IN(" . $stusc . ")";
        }

        $cond .= " AND Students.category ='NORMAL'  AND Students.discountcategory !='FREE' AND Students.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Students.fname ASC,Students.middlename ASC ,Students.lname ASC";

        $classfeehead = $this->Classfee->find('all')->contain(['Classes', 'Feesheads'])->group('Feesheads.id')->select(['Feesheads.name', 'Feesheads.alias'])->where(['Classfee.academic_year' => $academicyear, 'Feesheads.id !=' => 2])->order(['Classfee.id' => 'ASC'])->toarray();
        if (!empty($quarter)) {
            if ($quarter == "Quater1") {

                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";

                $quaters[] = "MISC.FEE";
                $quaters[] = "Quater1";
            } elseif ($quarter == "Quater2") {
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quaters[] = "MISC.FEE";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater2") {
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";

                $quaters[] = "MISC.FEE";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater3") {
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";

                $quaters[] = "MISC.FEE";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
            } elseif ($quarter == "Quater4") {

                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";
                $quater[] = "Quater4";

                $quaters[] = "MISC.FEE";
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
                $quaters[] = "Quater4";
            }
        } else {
            $quater[] = "Miscellaneous Fee";
            $quater[] = "Quater1";
            $quater[] = "Quater2";
            $quater[] = "Quater3";
            $quater[] = "Quater4";

            $quaters[] = "MISC.FEE";
            $quaters[] = "Quater1";
            $quaters[] = "Quater2";
            $quaters[] = "Quater3";
            $quaters[] = "Quater4";
        }

        $this->set('quaters', $quater);

        $this->set('quatersf', $quaters);

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');

        $this->set(compact('academicyear'));

        $headerRow2[] = "S.No";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student";
        $headerRow2[] = "Class-Section";
        $headerRow2[] = "Father";
        $headerRow2[] = "Mobile";

        foreach ($quaters as $j => $el) {
            $el = trim($el);

            $headerRow2[] = $el;
        }
        $headerRow2[] = "Practical Fees";
        $headerRow2[] = "Previous Dues";

        $headerRow2[] = "(-)Discount Fee";
        $headerRow2[] = "Due Fees";

        $this->set(compact('headerRow2'));

        $counter = '';
        $total_due_amount = '';

        if (isset($results) && !empty($results)) {

            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {

                $result = array();
                $fedd = 0;

                $result[] = $counter;
                // $fetchdetail = $this->defaultersearchbyidhistory($service['id'],$previous_year);
                $result[] = $service['enroll'] . '-' . $service['id'];
                /* if($fetchdetail !='--'){
                $result[] = "<a title='Previous Year Due' target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "<b style='color:red;' >(*)</b></a>";
                }else{ */
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";
                // }

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['id'], 'Students.acedmicyear' => $academicyear])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }
                }

                $studentfees2 = $this->finddisountstudent2($service['id']);

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                $qua = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                foreach ($quater as $h => $ty) {

                    if ($ty == "Miscellaneous Fee") {

                        $tys = "Miscellaneous Fee";
                        $feesheads = $this->findfeeheadsid($tys);
                        $errs = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheads['id']);
                        if (!in_array('Miscellaneous Fee', $qua)) {

                            $result[] = $errs[0]['qu1_fees'];

                            $fedd += $errs[0]['qu1_fees'];
                        } else {
                            $result[] = '-';
                        }
                    }

                    $tyl = "Tution Fee";
                    $feeshead = $this->findfeeheadsid($tyl);
                    $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                    if ($ty == "Quater1") {

                        if (!in_array('Quater1', $qua)) {

                            $result[] = $err[0]['qu1_fees'];
                            $q1 = $err[0]['qu1_fees'];
                            $fedd += $err[0]['qu1_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater2") {

                        if (!in_array('Quater2', $qua)) {

                            $result[] = $err[0]['qu2_fees'];
                            $q2 = $err[0]['qu2_fees'];
                            $fedd += $err[0]['qu2_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater3") {
                        if (!in_array('Quater3', $qua)) {

                            $result[] = $err[0]['qu3_fees'];
                            $q3 = $err[0]['qu3_fees'];
                            $fedd += $err[0]['qu3_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater4") {
                        if (!in_array('Quater4', $qua)) {

                            $result[] = $err[0]['qu4_fees'];
                            $q4 = $err[0]['qu4_fees'];
                            $fedd += $err[0]['qu4_fees'];
                        } else {
                            $result[] = '-';
                        }
                    }
                }

                if ($q1 != 0 || $q2 != 0 || $q3 != 0 || $q4 != 0) {
                    if (
                        $service['class_id'] == 12 || $service['class_id'] == 13
                        || $service['class_id'] == 15 || $service['class_id'] == 17 ||
                        $service['class_id'] == 20 || $service['class_id'] == 22 ||
                        $service['class_id'] == 26 || $service['class_id'] == 27
                    ) {
                        $taks = 0;
                        $practicalfee = 0;

                        $compsid = explode(',', $service['comp_sid']);
                        $opt_sid = explode(',', $service['opt_sid']);

                        foreach ($compsid as $k => $g) {

                            $subjectpracticals = $this->classspractical($g);
                            if ($subjectpracticals) {
                                $practicalfee += $subjectpracticals['is_practicalfee'];
                            }
                        }

                        foreach ($opt_sid as $ks => $gs) {

                            $subjectpracticalss = $this->classspractical($gs);
                            if ($subjectpracticalss) {
                                $practicalfee += $subjectpracticalss['is_practicalfee'];
                            }
                        }

                        if ($q1) {

                            $taks += $practicalfee;
                        }
                        if ($q2) {

                            $taks += $practicalfee;
                        }
                        if ($q3) {

                            $taks += $practicalfee;
                        }
                        if ($q4) {

                            $taks += $practicalfee;
                        }

                        $result[] = $taks;
                        $fedd += $taks;
                    } else {

                        $result[] = "-";
                    }
                } else {
                    $result[] = "-";
                }
                $findpending = $this->findpendingssinglefee($service['id'], $academicyear);
                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $tak = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($ddf != 0) {

                        if ($q1) {

                            $tak += $ddf * $q1 / 100;
                        }
                        if ($q2) {

                            $tak += $ddf * $q2 / 100;
                        }
                        if ($q3) {

                            $tak += $ddf * $q3 / 100;
                        }
                        if ($q4) {

                            $tak += $ddf * $q4 / 100;
                        }

                        $result[] = "-" . floor($tak) . "<br><strong
                     style='color: green;font-size: 12px;'>_(" . $service['discountcategory'] . ")</strong>";

                        $fedd -= floor($tak);
                    } elseif ($ddf2 != 0) {
                        $ddf2 = $ddf2 * $tysd;

                        $result[] = "-" . floor($ddf2) . "<br><strong
                     style='color: green;font-size: 12px;'>_(" . $service['discountcategory'] . ")</strong>";
                        $fedd -= floor($ddf2);
                    } else {

                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        $headerRow3 = $total_due_amount;

        $this->set('headerRow3', $headerRow3);

        $this->set('results', $resultsss);
    }

    public function defaultersearch()
    {

        $conn = ConnectionManager::get('default');

        $academicyear = $this->request->data['acedmicyear'];
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = $this->request->data['quarter'];
        $mode = $this->request->data['mode'];
        $mode1 = $this->request->data['mode1'];

        $this->set(compact('mode'));
        $this->set(compact('mode1'));
        $detail = "SELECT Students.id,Students.fname,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.discountcategory,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,Students.comp_sid,Students.opt_sid,   Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id   WHERE  1=1 ";
        $cond = ' ';
        // pr($class_id);die;

        if (!empty($academicyear)) {

            $cond .= " AND Students.acedmicyear ='" . $academicyear . "'";
            $this->set('academicyear', $academicyear);
        }

        if (!empty($class_id)) {
            $this->set('class_id', $class_id);

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND Students.class_id IN(" . $stuc . ")";
        }

        if (!empty($section_id)) {
            $this->set('section_id', $section_id);
            foreach ($section_id as $gsg => $rts) {
                $consds[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $consds);
            $cond .= " AND Students.section_id IN(" . $stusc . ")";
        }

        $cond .= " AND Students.category ='NORMAL'  AND Students.discountcategory !='FREE' AND Students.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Students.fname ASC,Students.middlename ASC ,Students.lname ASC";

        $classfeehead = $this->Classfee->find('all')->contain(['Classes', 'Feesheads'])->group('Feesheads.id')->select(['Feesheads.name', 'Feesheads.alias'])->where(['Classfee.academic_year' => $academicyear, 'Feesheads.id !=' => 2])->order(['Classfee.id' => 'ASC'])->toarray();
        if (!empty($quarter)) {
            if ($quarter == "Quater1") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                } else {

                    $quater[] = "Miscellaneous Fee";
                }

                $quater[] = "Quater1";
                if ($mode != '1') {

                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                } else {

                    $quaters[] = "MISC.FEE";
                }

                $quaters[] = "Quater1";
            } elseif ($quarter == "Quater2") {
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                } else {

                    $quater[] = "Miscellaneous Fee";
                }
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                } else {

                    $quaters[] = "MISC.FEE";
                }
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater3") {
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                } else {

                    $quater[] = "Miscellaneous Fee";
                }
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                } else {

                    $quaters[] = "MISC.FEE";
                }
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
            } elseif ($quarter == "Quater4") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                } else {

                    $quater[] = "Miscellaneous Fee";
                }
                $quater[] = "Quater1";
                $quater[] = "Quater2";
                $quater[] = "Quater3";
                $quater[] = "Quater4";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                } else {

                    $quaters[] = "MISC.FEE";
                }
                $quaters[] = "Quater1";
                $quaters[] = "Quater2";
                $quaters[] = "Quater3";
                $quaters[] = "Quater4";
            }
        } else {
            if ($mode != '1') {
                foreach ($classfeehead as $key => $item) {

                    $quater[] = $item['Feesheads']['name'];
                }
            } else {

                $quater[] = "Miscellaneous Fee";
            }
            $quater[] = "Quater1";
            $quater[] = "Quater2";
            $quater[] = "Quater3";
            $quater[] = "Quater4";

            if ($mode != '1') {

                foreach ($classfeehead as $key => $item) {

                    $quaters[] = $item['Feesheads']['alias'];
                }
            } else {

                $quaters[] = "MISC.FEE";
            }
            $quaters[] = "Quater1";
            $quaters[] = "Quater2";
            $quaters[] = "Quater3";
            $quaters[] = "Quater4";
        }

        $this->set('quaters', $quater);

        $this->set('quatersf', $quaters);

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $previous_year = $users['previous_year'];
        $academics_year = $users['academic_year'];
        if ((!empty($academicyear) && $academicyear == $previous_year)) {

            $detail2 = "SELECT Studentshistory.stud_id,Studentshistory.fname,Studentshistory.fathername,Studentshistory.board_id,Studentshistory.fee_submittedby,Studentshistory.sms_mobile,Studentshistory.middlename,Studentshistory.lname,Studentshistory.mobile,Studentshistory.discountcategory,Studentshistory.acedmicyear,Studentshistory.enroll,Studentshistory.actionhistory,Studentshistory.h_id,Studentshistory.class_id,Studentshistory.section_id,Studentshistory.admissionyear,Studentshistory.status,Studentshistory.comp_sid,Studentshistory.opt_sid,   Classes.title as classtitle ,Classes.sort as csort ,Sections.title as sectiontitle FROM `studentshistory` Studentshistory LEFT JOIN classes Classes ON Studentshistory.`class_id` = Classes.id LEFT JOIN sections Sections ON Studentshistory.`section_id` = Sections.id   WHERE  1=1 ";
            $cond2 = ' ';

            if (!empty($academicyear)) {

                $cond2 .= " AND Studentshistory.acedmicyear ='" . $academicyear . "'";
                $this->set('academicyear', $academicyear);
            }

            if (!empty($class_id)) {
                $this->set('class_id', $class_id);

                foreach ($class_id as $gg => $rt) {
                    $condsk[] = "'" . $rt . "'";
                }
                $stuck = implode(',', $condsk);
                $cond2 .= " AND Studentshistory.class_id IN(" . $stuck . ")";
            }

            if (!empty($section_id)) {
                $this->set('section_id', $section_id);
                foreach ($section_id as $gsg => $rts) {
                    $consdsl[] = "'" . $rts . "'";
                }
                $stusck = implode(',', $consdsl);
                $cond2 .= " AND Studentshistory.section_id IN(" . $stusck . ")";
            }

            $cond2 .= " AND Studentshistory.category ='NORMAL' AND Studentshistory.discountcategory !='FREE' AND Studentshistory.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Studentshistory.fname ASC,Studentshistory.middlename ASC ,Studentshistory.lname ASC";

            $SQL2 = $detail2 . $cond2;
            $results2 = $conn->execute($SQL2)->fetchAll('assoc');
        }
        //$users = $this->Users->find('all')->where(['Users.id' =>'1'])->first();

        //$academicyear=$users['academic_year'];
        $this->set(compact('academicyear'));

        ini_set('max_execution_time', 1600);

        $headerRow2[] = "S.No";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student";
        $headerRow2[] = "Class-Section";
        $headerRow2[] = "Father";
        $headerRow2[] = "Mobile";

        foreach ($quaters as $j => $el) {
            $el = trim($el);

            $headerRow2[] = $el;
        }
        $headerRow2[] = "Practical Fees";
        $headerRow2[] = "Previous Dues";
        if ($academicyear == $academics_year) {
            $headerRow2[] = "Prev.Year Dues";
        }

        $headerRow2[] = "(-)Discount Fee";
        $headerRow2[] = "Due Fees";

        $this->set(compact('headerRow2'));

        $counter = '';
        $total_due_amount = '';
        $classfeehead2 = $this->Classfee->find('all')->contain(['Classes', 'Feesheads'])->group('Feesheads.id')->select(['Feesheads.name', 'Feesheads.alias'])->where(['Classfee.academic_year' => $academicyear, 'Feesheads.id NOT IN' => ['2', '9']])->order(['Classfee.id' => 'ASC'])->toarray();
        if (isset($results) && !empty($results)) {

            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                if ($mode1 == 1) {
                    $findpending = $this->findpendingssinglefee($service['id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'] . '-' . $service['id'];
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['id'], 'Students.acedmicyear' => $academicyear])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                //  For Students Tution Fee including current year.
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                $qua = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }

                //  For Students Admisssion Fee  including previous year etc.
                $studentfees2 = $this->finddisountstudent2($service['id']);
                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                //  For Migrate Students Admisssion Fee etc.

                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }
                    $studentfees3 = $this->finddisountstudent2($studsentold['id']);
                }
                $quas3 = array();
                $qua3 = array();
                foreach ($studentfees3 as $k3 => $value3) {
                    $quas3[] = unserialize($value3['quarter']);
                }

                $quaf3 = array();

                foreach ($quas3 as $h3 => $vale3) {

                    $quaf3 = array_merge($quaf3, $vale3);
                }
                $rt3 = array();
                foreach ($quaf3 as $j3 => $t3) {

                    $qua2[] = $j3;
                }

                foreach ($quater as $h => $ty) {

                    foreach ($classfeehead2 as $key => $item) {
                        if ($ty == $item['Feesheads']['name']) {

                            $tys = $item['Feesheads']['name'];
                            $feesheads = $this->findfeeheadsid($tys);
                            $errs = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheads['id']);
                            if (!in_array($item['Feesheads']['name'], $qua2)) {

                                $result[] = $errs[0]['qu1_fees'];

                                $fedd += $errs[0]['qu1_fees'];
                                $tysd++;
                            } else {
                                $result[] = '-';
                            }
                        }
                    }

                    if ($ty == "Miscellaneous Fee") {

                        $tys = "Miscellaneous Fee";
                        $feesheads = $this->findfeeheadsid($tys);
                        $errs = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheads['id']);
                        if (!in_array('Miscellaneous Fee', $qua)) {

                            $result[] = $errs[0]['qu1_fees'];

                            $fedd += $errs[0]['qu1_fees'];
                        } else {
                            $result[] = '-';
                        }
                    }

                    $tyl = "Tution Fee";
                    $feeshead = $this->findfeeheadsid($tyl);
                    $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                    if ($ty == "Quater1") {

                        if (!in_array('Quater1', $qua)) {

                            $result[] = $err[0]['qu1_fees'];
                            $q1 = $err[0]['qu1_fees'];
                            $fedd += $err[0]['qu1_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater2") {

                        if (!in_array('Quater2', $qua)) {

                            $result[] = $err[0]['qu2_fees'];
                            $q2 = $err[0]['qu2_fees'];
                            $fedd += $err[0]['qu2_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater3") {
                        if (!in_array('Quater3', $qua)) {

                            $result[] = $err[0]['qu3_fees'];
                            $q3 = $err[0]['qu3_fees'];
                            $fedd += $err[0]['qu3_fees'];
                            $tysd++;
                        } else {
                            $result[] = '-';
                        }
                    }
                    if ($ty == "Quater4") {
                        if (!in_array('Quater4', $qua)) {

                            $result[] = $err[0]['qu4_fees'];
                            $q4 = $err[0]['qu4_fees'];
                            $fedd += $err[0]['qu4_fees'];
                        } else {
                            $result[] = '-';
                        }
                    }
                }

                if ($q1 != 0 || $q2 != 0 || $q3 != 0 || $q4 != 0) {
                    if (
                        $service['class_id'] == 12 || $service['class_id'] == 13
                        || $service['class_id'] == 15 || $service['class_id'] == 17 ||
                        $service['class_id'] == 20 || $service['class_id'] == 22 ||
                        $service['class_id'] == 26 || $service['class_id'] == 27
                    ) {
                        $taks = 0;
                        $practicalfee = 0;

                        $compsid = explode(',', $service['comp_sid']);
                        $opt_sid = explode(',', $service['opt_sid']);

                        foreach ($compsid as $k => $g) {

                            $subjectpracticals = $this->classspractical($g);
                            if ($subjectpracticals) {
                                $practicalfee += $subjectpracticals['is_practicalfee'];
                            }
                        }

                        foreach ($opt_sid as $ks => $gs) {

                            $subjectpracticalss = $this->classspractical($gs);
                            if ($subjectpracticalss) {
                                $practicalfee += $subjectpracticalss['is_practicalfee'];
                            }
                        }

                        if ($q1) {

                            $taks += $practicalfee;
                        }
                        if ($q2) {

                            $taks += $practicalfee;
                        }
                        if ($q3) {

                            $taks += $practicalfee;
                        }
                        if ($q4) {

                            $taks += $practicalfee;
                        }

                        $result[] = $taks;
                        $fedd += $taks;
                    } else {

                        $result[] = "-";
                    }
                } else {
                    $result[] = "-";
                }
                $findpending = $this->findpendingssinglefee($service['id'], $academicyear);
                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                $studsentold = $this->Students->find('all')->where(['Students.id' => $service['id']])->first();

                $fetchdetail = $this->defaultersearchbyidhistory($service['id'], $previous_year);
                if ($academicyear == $academics_year) {
                    if ($studsentold['due_fees'] != null && $studsentold['due_fees'] != '' && $studsentold['due_fees'] != '0') {

                        $result[] = $studsentold['due_fees'];
                        $fedd += $studsentold['due_fees'];
                    } elseif ($fetchdetail != '--') {
                        $result[] = (int)$fetchdetail;
                        $fedd += (int)$fetchdetail;
                    } else {
                        $result[] = "-";
                    }
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $tak = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($ddf != 0) {

                        if ($q1) {

                            $tak += $ddf * $q1 / 100;
                        }
                        if ($q2) {

                            $tak += $ddf * $q2 / 100;
                        }
                        if ($q3) {

                            $tak += $ddf * $q3 / 100;
                        }
                        if ($q4) {

                            $tak += $ddf * $q4 / 100;
                        }

                        $result[] = "-" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>_(" . $service['discountcategory'] . ")</strong>";

                        $fedd -= floor($tak);
                    } elseif ($ddf2 != 0) {
                        $ddf2 = $ddf2 * $tysd;

                        $result[] = "-" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>_(" . $service['discountcategory'] . ")</strong>";
                        $fedd -= floor($ddf2);
                    } else {

                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        if (isset($results2) && !empty($results2)) {
            $fees = 0;
            if ($counter == '') {
                $counter = 1;
            }

            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            if ($total_due_amount == '') {
                $total_due_amount = 0;
            }
            $total_dues_amount = 0;

            foreach ($results2 as $h => $service) {
                if ($mode1 == 1) {
                    $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                if ($service['actionhistory'] == "PROMOTE") {
                    $result[] = "<a  title='Promoted Student' target='_blank' href=" . ADMIN_URL . "studentfees/history/" . $service['stud_id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "<small style='color:red;'>(PR.)</small></a>";
                } elseif ($service['actionhistory'] == "REPEAT") {
                    $result[] = "<a  title='Repeat Student' target='_blank' href=" . ADMIN_URL . "studentfees/history/" . $service['stud_id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "<small style='color:red;'>(RE.)</small></a>";
                }

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['stud_id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['stud_id']])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }

                    if ($oldenrool == 1991) {
                        $student_datas35s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Quater1%'])->toarray();

                        $studentfees = array_merge($studentfees, $student_datas35s);
                    }
                }

                $studentfees2 = $this->finddisountstudent2($service['stud_id']);

                //pr( $studentfees); die;
                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }
                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                foreach ($quater as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }
                        if ($dff == '0') {

                            if ($ty == "Quater1") {

                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater2") {
                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater3") {

                                $ty = "Tution Fee";
                            } elseif ($ty == "Quater4") {

                                $ty = "Tution Fee";
                            } elseif (($ty == "Admission Fee")) {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            } elseif ($ty == "Annual Charges") {

                                $ty = "Annual Charges";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            if ($ty == "Quater1") {

                                $result[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                            } elseif ($ty == "Quater2") {

                                $result[] = $err[0]['qu2_fees'];
                                $q2 += $err[0]['qu2_fees'];
                                $fedd += $err[0]['qu2_fees'];
                            } elseif ($ty == "Quater3") {

                                $result[] = $err[0]['qu3_fees'];
                                $q3 += $err[0]['qu3_fees'];
                                $fedd += $err[0]['qu3_fees'];
                            } elseif ($ty == "Quater4") {

                                $result[] = $err[0]['qu4_fees'];
                                $q4 += $err[0]['qu4_fees'];
                                $fedd += $err[0]['qu4_fees'];
                            } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454')) {
                                if (!in_array("Admission Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if (($ty == "Admission Fee") && ($service['enroll'] <= '6454') && $this->request->session()->read('Auth.User.db') == "sanskar" && ($service['board_id'] == '1')) {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q1 += $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];

                                    $resultss[] = $err[0]['qu1_fees'];
                                    $tysd++;
                                }
                            }
                        }
                    } else {
                        if ($ty == "Quater1") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater2") {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater3") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Quater4") {

                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        } elseif ($tys == "Annual Charges") {

                            $tys = "Annual Charges";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                        if ($ty == "Quater1") {

                            $result[] = $err[0]['qu1_fees'];
                            $q1 += $err[0]['qu1_fees'];
                            $fedd += $err[0]['qu1_fees'];
                        } elseif ($ty == "Quater2") {

                            $result[] = $err[0]['qu2_fees'];
                            $q2 += $err[0]['qu2_fees'];
                            $fedd += $err[0]['qu2_fees'];
                        } elseif ($ty == "Quater3") {

                            $result[] = $err[0]['qu3_fees'];
                            $q3 += $err[0]['qu3_fees'];
                            $fedd += $err[0]['qu3_fees'];
                        } elseif ($ty == "Quater4") {

                            $result[] = $err[0]['qu4_fees'];
                            $q4 += $err[0]['qu4_fees'];
                            $fedd += $err[0]['qu4_fees'];
                        } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454')) {
                            if (!in_array("Admission Fee", $qua2)) {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '6454') {

                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '6454') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {

                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = $err[0]['qu1_fees'];
                                $resultss[] = $err[0]['qu1_fees'];
                                $q1 += $err[0]['qu1_fees'];
                                $fedd += $err[0]['qu1_fees'];
                                $tysd++;
                            }
                        }
                    }
                }

                if (
                    $service['class_id'] == 12 || $service['class_id'] == 13
                    || $service['class_id'] == 15 || $service['class_id'] == 17 ||
                    $service['class_id'] == 20 || $service['class_id'] == 22 ||
                    $service['class_id'] == 26 || $service['class_id'] == 27
                ) {
                    $taks = 0;
                    $practicalfee = 0;

                    $compsid = explode(',', $service['comp_sid']);
                    $opt_sid = explode(',', $service['opt_sid']);

                    foreach ($compsid as $k => $g) {

                        $subjectpracticals = $this->classspractical($g);
                        if ($subjectpracticals) {
                            $practicalfee += $subjectpracticals['is_practicalfee'];
                        }
                    }

                    foreach ($opt_sid as $ks => $gs) {

                        $subjectpracticalss = $this->classspractical($gs);
                        if ($subjectpracticalss) {
                            $practicalfee += $subjectpracticalss['is_practicalfee'];
                        }
                    }

                    if ($q1) {

                        $taks += $practicalfee;
                    }
                    if ($q2) {

                        $taks += $practicalfee;
                    }
                    if ($q3) {

                        $taks += $practicalfee;
                    }
                    if ($q4) {

                        $taks += $practicalfee;
                    }

                    $result[] = $taks;
                    $fedd += $taks;
                } else {

                    $result[] = "-";
                }

                $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {

                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        $headerRow3 = $total_due_amount;

        $this->set('headerRow3', $headerRow3);

        $this->set('results', $resultsss);
    }

    public function studentfeesget($student_id = null)
    {
        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        $studentfeesdetails = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $student_id, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.acedmicyear' => $rolepresentyear])->toarray();
        $alldiscount = 0;
        $allDeposite = 0;
        foreach ($studentfeesdetails as $k => $value) {
            // pr($value);die;
            $quas[] = unserialize($value['quarter']);
            $alldiscount += $value['discount'];
            $alldiscount += $value['addtionaldiscount'];
            $quas[$k]['deposite_amt'] = $value['deposite_amt'];
        }
        foreach ($quas as $key => $val) {
            $data_keys =  array_keys($val);
            // pr($data_keys);
            foreach ($data_keys as $valddd) {
                if ($valddd == 'deposite_amt') {
                    continue;
                }
                $lst_st_data[$valddd]   = round($val['deposite_amt'] / (count($data_keys) - 1));
                // $lst_st_data[$valddd]   =  $val[$valddd]/(count($data_keys)-1);
            }
            $allDeposite += $val['deposite_amt'];
        }
        $lst_st_data['totalDeposite'] = $allDeposite;
        $lst_st_data['discount'] = $alldiscount;
        $discount_cat = ($value['discountcategory'] == '') ? 'Additional Discount' : $value['discountcategory'];
        $lst_st_data['discountcategory']   =  preg_replace('/\s+/', '', $discount_cat);
        // pr($lst_st_data);
        return $lst_st_data;
    }


    public function studentpendingfees($student_id = null)
    {
        // pr($student_id);exit;

        $articles = TableRegistry::get('Studentfeepending');
        $query = $articles->find('all');
        return $query->select(['sum' => $query->func()->sum('Studentfeepending.amt')])->where(['Studentfeepending.s_id' => $student_id, 'Studentfeepending.status' => 'N'])->toarray();
    }

    //Excel file
    public function feesreport($class = null, $academic = null)
    {

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        // pr($academic);exit;

        $decodedata = base64_decode($class);
        $decodeacademic = base64_decode($academic);
        $cond = "Students.class_id IN ('" . $decodedata . "')";
        if (!empty($decodeacademic)) {
            $cond = "Students.class_id IN ('" . $decodedata . "')";
        } else {
            $decodeacademic =  $rolepresentyear;
        }

        $students = $this->Students->find('all')->contain(['Classes', 'Sections'])->where([$cond])->order(['Classes.sort' => 'ASC'])->toarray();

        // $students = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.id IN'=>[300]])->order(['Classes.sort' => 'ASC'])->toarray();
        // pr($students);exit;

        $this->set('students', $students);
        $quarterone = 0;
        $quartertwo = 0;
        $quarterthree = 0;
        $quarterfour = 0;
        $student_rec  = [];
        $total_q1_data  = [];
        $pendingquater = 0;
        foreach ($students as $value) {

            $class_id  =  $value['class_id'];
            $classfee = $this->Classfee->find('all')->where(['class_id' => $class_id, 'academic_year' => $decodeacademic])->toarray();
            // pr($classfee);exit;

            $transport_fees = $this->Transportfees->find('all')->where(['loc_id' => $value['location_id'], 'academic_year' => $decodeacademic])->first();
            $fee_heads = $this->Feesheads->find('all')->first();

            if ($transport_fees) {
                $transport_fees_total = $transport_fees['quarter1'] + $transport_fees['quarter2'] + $transport_fees['quarter3'] + $transport_fees['quarter4'];
            } else {
                $transport_fees_total = 0;
            }
            // Tution_fees
            $tution_fees = $classfee[0]['qu1_fees'] + $classfee[0]['qu2_fees'] + $classfee[0]['qu3_fees'] + $classfee[0]['qu4_fees'];
            // Admission_fees
            $admission_fees = $classfee[1]['qu1_fees'] + $classfee[1]['qu2_fees'] + $classfee[1]['qu3_fees'] + $classfee[1]['qu4_fees'];

            $caution_fees = $classfee[2]['qu1_fees'] + $classfee[2]['qu2_fees'] + $classfee[2]['qu3_fees'] + $classfee[2]['qu4_fees'];

            $other_fees = $classfee[3]['qu1_fees'] + $classfee[3]['qu2_fees'] + $classfee[3]['qu3_fees'] + $classfee[3]['qu4_fees'];

            $student_rec['enrollno'] = $value['enroll'];
            $student_rec['studentname'] =  ucwords(strtolower($value['fname'] . ' ' . $value['middlename'] . '' . $value['lname']));
            $student_rec['classtitle'] = $value['class']['title'] . "-" . $value['section']['title'];
            $student_rec['fathername'] = ucfirst($value['fathername']);
            $student_rec['mobile'] = $value['mobile'];
            $student_fees_record = $this->studentfeesget($value['id']);
            // pr($student_fees_record);

            if ($rolepresentyear != $value['admissionyear']) {
                if (array_key_exists("Admission Fee", $student_fees_record)) {
                    $student_rec['totalfees'] =  $tution_fees + $caution_fees + $admission_fees;
                } else {

                    $student_rec['totalfees'] =  $tution_fees + $caution_fees;
                }
                $student_rec['totaltransportfees'] =  $transport_fees_total;
                $student_rec['package'] =   $student_rec['totalfees'] + $caution_fees + $transport_fees_total;
                $admission_fee = 'N/A';
                $caution_money_pending =  "N/A";
                $admission_pending = 0;
                $caution_money = 0;
            } else {
                // Admission Fee
                if (array_key_exists("Admission Fee", $student_fees_record)) {
                    $admission_fee = ($student_fees_record['Admission Fee'] == 0) ? 'N/A' : $student_fees_record['Admission Fee'];
                    // $admission_pending =$student_fees_record['Admission Fee'];
                    $admission_feeadd = $admission_fees;
                } else {
                    $admission_feeadd = $admission_fees;
                    $admission_fee = "--";
                }

                // Caution Money
                if (array_key_exists("Caution Money", $student_fees_record)) {
                    $caution_money_pending = ($student_fees_record['Caution Money'] == 0) ? 'N/A' : $student_fees_record['Caution Money'];
                    $caution_money = $student_fees_record['Caution Money'];
                } else {
                    $caution_money = $caution_fees;
                    $caution_money_pending = '--';
                }

                // pr($transport_fees_total);exit;
                $student_rec['totalfees'] =  $tution_fees + $admission_feeadd + $caution_money + $transport_fees_total;
                $student_rec['package'] =    $tution_fees + $admission_feeadd + $caution_money + $transport_fees_total;
                $student_rec['totaltransportfees'] =  $transport_fees_total;
            }
            // pr($student_rec['totalfees']);exit;

            $studentpending = $this->studentpendingfees($value['id']);

            if (array_key_exists("Quater1", $student_fees_record)) {
                $qu1_fees =  $student_fees_record['Quater1'];
                $qu1pendingfees = 0;
            } else {
                $qu1pendingfees =  $classfee[0]['qu1_fees'];
                $qu1_fees = "--";
            }
            if (array_key_exists("Quater2", $student_fees_record)) {
                $qu2_fees =   $student_fees_record['Quater2'];
                $qu2pendingfees = 0;
            } else {
                $qu2pendingfees =  $classfee[0]['qu2_fees'];
                $qu2_fees = "--";
            }
            if (array_key_exists("Quater3", $student_fees_record)) {
                $qu3_fees =   $student_fees_record['Quater3'];
                $qu3pendingfees = 0;
            } else {
                $qu3pendingfees =  $classfee[0]['qu3_fees'];
                $qu3_fees = "--";
            }
            if (array_key_exists("Quater4", $student_fees_record)) {
                $qu4_fees =   $student_fees_record['Quater4'];
                $qu4pendingfees = 0;
            } else {
                $qu4pendingfees =  $classfee[0]['qu4_fees'];
                $qu4_fees = "--";
            }


            if (array_key_exists("Transport5", $student_fees_record)) {
                $transport1_fees =  $student_fees_record['Transport5'];
                $tranport1pendingfees = 0;
            } else {
                $tranport1pendingfees =  $transport_fees['quarter1'];
                $transport1_fees = "--";
            }
            if (array_key_exists("Transport6", $student_fees_record)) {
                $transport2_fees =   $student_fees_record['Transport6'];
                $tranport2pendingfees = 0;
            } else {
                $tranport2pendingfees =  $transport_fees['quarter2'];
                $transport2_fees = "--";
            }
            if (array_key_exists("Transport7", $student_fees_record)) {
                $transport3_fees =   $student_fees_record['Transport7'];
                $tranport3pendingfees = 0;
            } else {
                $tranport3pendingfees =  $transport_fees['quarter3'];
                $transport3_fees = "--";
            }
            if (array_key_exists("Transport8", $student_fees_record)) {
                $transport4_fees =   $student_fees_record['Transport8'];
                $tranport4pendingfees = 0;
            } else {
                $tranport4pendingfees =  $transport_fees['quarter4'];
                $transport4_fees = "--";
            }

            // pr($admission_fee);exit;
            $student_rec['admissionfee'] =  $admission_fee;
            $student_rec['caution_money'] =   $caution_money_pending;
            $student_rec['qu1_fees'] =   $qu1_fees;
            $student_rec['qu2_fees'] =   $qu2_fees;
            $student_rec['qu3_fees'] =   $qu3_fees;
            $student_rec['qu4_fees'] =  $qu4_fees;
            if ($transport_fees) {

                $student_rec['transport1_fees'] =   $transport1_fees;
                $student_rec['transport2_fees'] =   $transport2_fees;
                $student_rec['transport3_fees'] =   $transport3_fees;
                $student_rec['transport4_fees'] =   $transport4_fees;
            } else {
                $student_rec['transport1_fees'] =   '--';
                $student_rec['transport2_fees'] =   '--';
                $student_rec['transport3_fees'] =   '--';
                $student_rec['transport4_fees'] =   '--';
            }

            $transport_pending = $tranport1pendingfees + $tranport2pendingfees + $tranport3pendingfees + $tranport4pendingfees;

            $student_rec['pending_fees'] = $studentpending[0]['sum'] + $qu1pendingfees + $qu2pendingfees + $qu3pendingfees + $qu4pendingfees + $admission_pending + $caution_money + $transport_pending;
            $student_rec['pending_transport_fees'] = $transport_pending;
            $student_rec['total_deposite_amount'] = $student_fees_record['totalDeposite'];
            // pr($student_fees_record);exit;

            if (!empty($student_fees_record['discount'] || $student_fees_record['addtionaldiscount'])) {

                if (!empty($student_fees_record['discount'])) {

                    // $student_rec['discount'] =  $student_fees_record['discount'] . '-(' . $student_fees_record['discountcategory'] . ')';
                    $student_rec['discount'] =  $student_fees_record['discount'];
                } else if (!empty($student_fees_record['addtionaldiscount'])) {

                    // $student_rec['discount'] =  $student_fees_record['addtionaldiscount'] . '-(Additonal Discount)';
                    $student_rec['discount'] =  $student_fees_record['addtionaldiscount'];
                }
            } else {
                // $student_rec['discount'] = '--';
                $student_rec['discount'] = 0;
            }

            foreach ($fee_heads as $val_heads) {
                $student_rec[$val_heads['name']] =   $val_heads['cbse_fee'];
            }

            $student_rec_all[] =  $student_rec;
            // pr($student_rec_all);
        }
        // exit;

        $this->set('student_rec_all', $student_rec_all);
    }

    public function defaultersearchmonthly()
    {

        $conn = ConnectionManager::get('default');

        $academicyear = $this->request->data['acedmicyear'];
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = $this->request->data['quarter'];
        $mode = $this->request->data['mode'];
        $mode1 = $this->request->data['mode1'];

        $this->set(compact('mode'));
        $this->set(compact('mode1'));
        $detail = "SELECT Students.id,Students.fname,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.discountcategory,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle ,Classes.sort as csort ,  Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id   WHERE  1=1 ";
        $cond = ' ';

        if (!empty($academicyear)) {

            $cond .= " AND Students.acedmicyear ='" . $academicyear . "'";
            $this->set('academicyear', $academicyear);
        }

        if (!empty($class_id)) {
            $this->set('class_id', $class_id);

            foreach ($class_id as $gg => $rt) {
                $conds[] = "'" . $rt . "'";
            }
            $stuc = implode(',', $conds);
            $cond .= " AND Students.class_id IN(" . $stuc . ")";
        }

        if (!empty($section_id)) {
            $this->set('section_id', $section_id);
            foreach ($section_id as $gsg => $rts) {
                $consds[] = "'" . $rts . "'";
            }
            $stusc = implode(',', $consds);
            $cond .= " AND Students.section_id IN(" . $stusc . ")";
        }

        $cond .= " AND Students.category ='NORMAL'  AND Students.discountcategory !='FREE' AND Students.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Students.fname ASC";
        $classfeehead = $this->Classfee->find('all')->contain(['Classes', 'Feesheads'])->group('Feesheads.id')->select(['Feesheads.name', 'Feesheads.alias'])->where(['Classfee.academic_year' => $academicyear, 'Feesheads.id !=' => 2])->order(['Classfee.id' => 'ASC'])->toarray();

        if (!empty($quarter)) {
            if ($quarter == "April") {

                if ($mode != '1') {

                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                if ($mode != '1') {

                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
            } elseif ($quarter == "May") {
                if ($mode != '1') {

                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
            } elseif ($quarter == "May") {
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
            } elseif ($quarter == "June") {
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
            } elseif ($quarter == "July") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
            } elseif ($quarter == "August") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
            } elseif ($quarter == "September") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
            } elseif ($quarter == "October") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
            } elseif ($quarter == "November") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";
                $quater[] = "November";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
                $quaters[] = "November";
            } elseif ($quarter == "December") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";
                $quater[] = "November";
                $quater[] = "December";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
                $quaters[] = "November";
                $quaters[] = "December";
            } elseif ($quarter == "January") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";
                $quater[] = "November";
                $quater[] = "December";
                $quater[] = "January";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
                $quaters[] = "November";
                $quaters[] = "December";
                $quaters[] = "January";
            } elseif ($quarter == "February") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";
                $quater[] = "November";
                $quater[] = "December";
                $quater[] = "January";
                $quater[] = "February";

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
                $quaters[] = "November";
                $quaters[] = "December";
                $quaters[] = "January";
                $quaters[] = "February";
            } elseif ($quarter == "March") {

                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quater[] = $item['Feesheads']['name'];
                    }
                }
                $quater[] = "April";
                $quater[] = "May";
                $quater[] = "June";
                $quater[] = "July";
                $quater[] = "August";
                $quater[] = "September";
                $quater[] = "October";
                $quater[] = "November";
                $quater[] = "December";
                $quater[] = "January";
                $quater[] = "February";
                $quater[] = "March";
                if ($mode != '1') {
                    foreach ($classfeehead as $key => $item) {

                        $quaters[] = $item['Feesheads']['alias'];
                    }
                }
                $quaters[] = "April";
                $quaters[] = "May";
                $quaters[] = "June";
                $quaters[] = "July";
                $quaters[] = "August";
                $quaters[] = "September";
                $quaters[] = "October";
                $quaters[] = "November";
                $quaters[] = "December";
                $quaters[] = "January";
                $quaters[] = "February";
                $quaters[] = "March";
            }
        } else {
            if ($mode != '1') {
                foreach ($classfeehead as $key => $item) {

                    $quater[] = $item['Feesheads']['name'];
                }
            }
            $quater[] = "April";
            $quater[] = "May";
            $quater[] = "June";
            $quater[] = "July";

            if ($mode != '1') {

                foreach ($classfeehead as $key => $item) {

                    $quaters[] = $item['Feesheads']['alias'];
                }
            }
            $quaters[] = "April";
            $quaters[] = "May";
            $quaters[] = "June";
            $quaters[] = "July";
        }

        $this->set('quaters', $quater);

        $this->set('quatersf', $quaters);

        $SQL = $detail . $cond;

        $results = $conn->execute($SQL)->fetchAll('assoc');

        if ((!empty($academicyear) && $academicyear == "2018-19") || (!empty($academicyear) && $academicyear == "2019-20")) {

            $detail2 = "SELECT Studentshistory.stud_id,Studentshistory.fname,Studentshistory.fathername,Studentshistory.board_id,Studentshistory.fee_submittedby,Studentshistory.sms_mobile,Studentshistory.middlename,Studentshistory.lname,Studentshistory.mobile,Studentshistory.discountcategory,Studentshistory.acedmicyear,Studentshistory.enroll,Studentshistory.actionhistory,Studentshistory.h_id,Studentshistory.class_id,Studentshistory.section_id,Studentshistory.admissionyear,Studentshistory.status,  Classes.title as classtitle ,Classes.sort as csort ,Sections.title as sectiontitle FROM `studentshistory` Studentshistory LEFT JOIN classes Classes ON Studentshistory.`class_id` = Classes.id LEFT JOIN sections Sections ON Studentshistory.`section_id` = Sections.id   WHERE  1=1 ";
            $cond2 = ' ';

            if (!empty($academicyear)) {

                $cond2 .= " AND Studentshistory.acedmicyear ='" . $academicyear . "'";
                $this->set('academicyear', $academicyear);
            }

            if (!empty($class_id)) {
                $this->set('class_id', $class_id);

                foreach ($class_id as $gg => $rt) {
                    $condsk[] = "'" . $rt . "'";
                }
                $stuck = implode(',', $condsk);
                $cond2 .= " AND Studentshistory.class_id IN(" . $stuck . ")";
            }

            if (!empty($section_id)) {
                $this->set('section_id', $section_id);
                foreach ($section_id as $gsg => $rts) {
                    $consdsl[] = "'" . $rts . "'";
                }
                $stusck = implode(',', $consdsl);
                $cond2 .= " AND Studentshistory.section_id IN(" . $stusck . ")";
            }

            $cond2 .= " AND Studentshistory.category ='NORMAL' AND Studentshistory.discountcategory !='FREE' AND Studentshistory.status ='Y' ORDER BY csort ASC, sectiontitle ASC,Studentshistory.fname ASC";

            $SQL2 = $detail2 . $cond2;
            $results2 = $conn->execute($SQL2)->fetchAll('assoc');
        }
        //$users = $this->Users->find('all')->where(['Users.id' =>'1'])->first();

        //$academicyear=$users['academic_year'];
        $this->set(compact('academicyear'));

        ini_set('max_execution_time', 1600);

        $headerRow2[] = "S.No";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student";
        $headerRow2[] = "Class-Section";
        $headerRow2[] = "Father";
        $headerRow2[] = "Mobile";

        foreach ($quaters as $j => $el) {
            $el = trim($el);

            $headerRow2[] = $el;
        }
        $headerRow2[] = "Previous Dues";
        $headerRow2[] = "(-)Discount Fee";
        $headerRow2[] = "Due Fees";

        $this->set(compact('headerRow2'));

        $counter = '';
        $total_due_amount = '';

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {
                if ($mode1 == 1) {
                    $findpending = $this->findpendingssinglefee($service['id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'] . '-' . $service['id'];
                $result[] = "<a  target='_blank' href=" . ADMIN_URL . "studentfees/index/" . $service['id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "</a>";

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['id'], 'Students.acedmicyear' => $academicyear])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }
                }

                $studentfees2 = $this->finddisountstudent2($service['id']);

                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }
                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                foreach ($quater as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Admission Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }

                        if ($dff == '0') {
                            $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                            if (in_array($ty, $collect)) {
                                $ty = "Tution Fee";
                            } elseif (($ty == "Admission Fee")) {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            } elseif ($ty == "Annual Charges") {

                                $ty = "Annual Charges";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            $divide = '3';
                            if ($ty == "April" || $ty == "May" || $ty == "June") {

                                $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                                $cui = $err[0]['qu1_fees'] / $divide;

                                $result[] = $cui;
                                $q1 += $cui;
                                $fedd += $cui;
                            } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                                $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                                $result[] = $cui2;
                                $q2 += $cui2;
                                $fedd += $cui2;
                            } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                                $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                                $result[] = $cui3;
                                $q3 += $cui3;
                                $fedd += $cui3;
                            } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                                $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                                $result[] = $cui4;
                                $q4 += $cui4;
                                $fedd += $cui4;
                            } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454') && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                if (!in_array("Admission Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif (($ty == "Admission Fee") && ($service['enroll'] > '5995') && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                if (!in_array("Admission Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if (($ty == "Admission Fee") && ($service['enroll'] <= '6454') && $this->request->session()->read('Auth.User.db') == "sanskar" && ($service['board_id'] == '1')) {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif (($ty == "Admission Fee") && ($service['enroll'] <= '5995') && $this->request->session()->read('Auth.User.db') == "sanskar" && ($service['board_id'] != '1')) {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $divide = '3';
                                    $cui2 = (int)$err[0]['qu1_fees'];
                                    $result[] = $cui2;
                                    $q1 += $cui2;
                                    $fedd += $cui2;
                                    $resultss[] = $cui2;

                                    $tysd++;
                                }
                            }
                        }
                    } else {

                        $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                        if (in_array($ty, $collect)) {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        } elseif ($ty == "Annual Charges") {

                            $tys = "Annual Charges";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);
                        $divide = '3';
                        if ($ty == "April" || $ty == "May" || $ty == "June") {
                            $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                            $cui = $err[0]['qu1_fees'] / $divide;

                            $result[] = $cui;
                            $q1 += $cui;
                            $fedd += $cui;
                        } elseif ($ty == "July" || $ty == "August" || $ty == "September") {
                            $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                            $result[] = $cui2;
                            $q2 += $cui2;
                            $fedd += $cui2;
                        } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                            $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;
                            $result[] = $cui3;
                            $q3 += $cui3;
                            $fedd += $cui3;
                        } elseif ($ty == "January" || $ty == "February" || $ty == "March") {
                            $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;
                            $result[] = $cui4;
                            $q4 += $cui4;
                            $fedd += $cui4;
                        } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454') && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                            if (!in_array("Admission Fee", $qua2)) {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {

                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif (($ty == "Admission Fee") && ($service['enroll'] > '5995') && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                            if (!in_array("Admission Fee", $qua2)) {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {

                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {

                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Admission Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '5995' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] != '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $divide = '3';
                                $cui2 = (int)$err[0]['qu1_fees'];
                                $result[] = $cui2;
                                $q1 += $cui2;
                                $fedd += $cui2;
                                $resultss[] = $cui2;

                                $tysd++;
                            }
                        }
                    }
                }

                $findpending = $this->findpendingssinglefee($service['id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        if (isset($results2) && !empty($results2)) {
            $fees = 0;
            if ($counter == '') {
                $counter = 1;
            }

            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            if ($total_due_amount == '') {
                $total_due_amount = 0;
            }
            $total_dues_amount = 0;

            foreach ($results2 as $h => $service) {
                if ($mode1 == 1) {
                    $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);
                    if ($findpending[0]['sum'] == '') {
                        continue;
                    }
                }
                $result = array();
                $fedd = 0;

                $result[] = $counter;
                $result[] = $service['enroll'];
                if ($service['actionhistory'] == "PROMOTE") {
                    $result[] = "<a  title='Promoted Student' target='_blank' href=" . ADMIN_URL . "studentfees/history/" . $service['stud_id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "<small style='color:red;'>(PR.)</small></a>";
                } elseif ($service['actionhistory'] == "REPEAT") {
                    $result[] = "<a  title='Repeat Student' target='_blank' href=" . ADMIN_URL . "studentfees/history/" . $service['stud_id'] . "/" . $service['acedmicyear'] . ">" . $service['fname'] . " " . $service['middlename'] . " " . $service['lname'] . "<small style='color:red;'>(RE.)</small></a>";
                }

                $s_id = $service['class_id'];
                $c_id = $service['section_id'];

                $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                $result[] = $classssr['title'] . "-" . $sectionssss['title'];
                $result[] = $service['fathername'];
                $result[] = $service['sms_mobile'];

                $studentfees = $this->finddisountstudent($service['stud_id'], $academicyear);

                $studentold = $this->Students->find('all')->where(['Students.id' => $service['stud_id']])->first();

                $oldenrool = $studentold['oldenroll'];
                $rolepresents = $studentold['board_id'];
                if ($oldenrool) {

                    if ($rolepresents == '1') {
                        $boardzs = ['2', '3'];
                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id IN' => $boardzs])->first();
                    } else {

                        $studsentold = $this->Students->find('all')->where(['Students.enroll' => $oldenrool, 'Students.board_id' => '1'])->first();
                    }

                    $ols = $studsentold['id'];

                    $student_datas3s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Admission Fee%', 'Studentfees.quarter NOT LIKE' => '%Quater1%'])->toarray();

                    $student_datas32s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Caution Money%'])->toarray();

                    if (isset($student_datas3s)) {

                        $studentfees = array_merge($studentfees, $student_datas3s);
                    }

                    if (isset($student_datas32s)) {

                        $studentfees = array_merge($studentfees, $student_datas32s);
                    }

                    if ($oldenrool == 1991) {
                        $student_datas35s = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $ols, 'Studentfees.refrencepending' => '0', 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%Quater1%'])->toarray();

                        $studentfees = array_merge($studentfees, $student_datas35s);
                    }
                }

                $studentfees2 = $this->finddisountstudent2($service['stud_id']);
                //pr( $studentfees); die;
                $q1 = 0;
                $q2 = 0;
                $q3 = 0;
                $q4 = 0;
                $resultd = 0;
                $resultd2 = 0;
                $tysd = 0;
                $quas = array();

                foreach ($studentfees as $k => $value) {
                    $quas[] = unserialize($value['quarter']);
                }

                $quaf = array();

                foreach ($quas as $h => $vale) {

                    $quaf = array_merge($quaf, $vale);
                }
                $rt = array();
                foreach ($quaf as $j => $t) {

                    $qua[] = $j;
                }
                $quas2 = array();

                $qua2 = array();
                foreach ($studentfees2 as $k2 => $value2) {
                    $quas2[] = unserialize($value2['quarter']);
                }

                $quaf2 = array();

                foreach ($quas2 as $h2 => $vale2) {

                    $quaf2 = array_merge($quaf2, $vale2);
                }
                $rt2 = array();
                foreach ($quaf2 as $j2 => $t2) {

                    $qua2[] = $j2;
                }

                foreach ($quater as $h => $ty) {

                    if (!empty($quaf)) {
                        $dff = 0;
                        foreach ($quaf as $t => $h) {
                            if ($t == $ty) {

                                //   echo $h;

                                if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } else {
                                    $result[] = "-";
                                }
                                $dff++;
                            } else {
                            }
                        }
                        if ($dff == '0') {
                            $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                            if (in_array($ty, $collect)) {
                                $ty = "Tution Fee";
                            } elseif (($ty == "Admission Fee")) {

                                $ty = "Admission Fee";
                            } elseif ($ty == "Development Fee") {

                                $ty = "Development Fee";
                            } elseif ($ty == "Caution Money") {

                                $ty = "Caution Money";
                            } elseif ($ty == "Miscellaneous Fee") {

                                $ty = "Miscellaneous Fee";
                            } elseif ($ty == "Annual Charges") {

                                $ty = "Annual Charges";
                            }

                            $feeshead = $this->findfeeheadsid($ty);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            $divide = '3';
                            if ($ty == "April" || $ty == "May" || $ty == "June") {

                                $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                                $cui = $err[0]['qu1_fees'] / $divide;

                                $result[] = $cui;
                                $q1 += $cui;
                                $fedd += $cui;
                            } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                                $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                                $result[] = $cui2;
                                $q2 += $cui2;
                                $fedd += $cui2;
                            } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                                $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                                $result[] = $cui3;
                                $q3 += $cui3;
                                $fedd += $cui3;
                            } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                                $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                                $result[] = $cui4;
                                $q4 += $cui4;
                                $fedd += $cui4;
                            } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454') && $this->request->session()->read('Auth.User.db') == "sanskar") {
                                if (!in_array("Admission Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar") {
                                if (!in_array("Development Fee", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar") {
                                if (!in_array("Caution Money", $qua2)) {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {

                                    $result[] = '-';
                                }
                            } elseif ($ty == "Miscellaneous Fee") {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                if (($ty == "Admission Fee") && ($service['enroll'] <= '6454') && $this->request->session()->read('Auth.User.db') == "sanskar" && ($service['board_id'] == '1')) {
                                    $result[] = "OLD";
                                } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                    $result[] = "OLD";
                                } elseif ($ty == "Miscellaneous Fee") {
                                    $result[] = $err[0]['qu1_fees'];

                                    $fedd += $err[0]['qu1_fees'];
                                } else {
                                    $divide = '3';
                                    $cui2 = (int)$err[0]['qu1_fees'];
                                    $result[] = $cui2;
                                    $q1 += $cui2;
                                    $fedd += $cui2;
                                    $resultss[] = $cui2;

                                    $tysd++;
                                }
                            }
                        }
                    } else {

                        $collect = array('April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December', 'January' => 'January', 'February' => 'February', 'March' => 'March');
                        if (in_array($ty, $collect)) {
                            $tys = "Tution Fee";
                        } elseif ($ty == "Admission Fee") {

                            $tys = "Admission Fee";
                        } elseif ($ty == "Development Fee") {

                            $tys = "Development Fee";
                        } elseif ($ty == "Caution Money") {

                            $tys = "Caution Money";
                        } elseif ($ty == "Miscellaneous Fee") {

                            $tys = "Miscellaneous Fee";
                        } elseif ($ty == "Annual Charges") {

                            $tys = "Annual Charges";
                        }

                        $feeshead = $this->findfeeheadsid($tys);
                        $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                        $divide = '3';
                        if ($ty == "April" || $ty == "May" || $ty == "June") {

                            $cui = (int)$err[0]['qu1_fees'] / (int)$divide;
                            $cui = $err[0]['qu1_fees'] / $divide;

                            $result[] = $cui;
                            $q1 += $cui;
                            $fedd += $cui;
                        } elseif ($ty == "July" || $ty == "August" || $ty == "September") {

                            $cui2 = (int)$err[0]['qu2_fees'] / (int)$divide;
                            $result[] = $cui2;
                            $q2 += $cui2;
                            $fedd += $cui2;
                        } elseif ($ty == "October" || $ty == "November" || $ty == "December") {
                            $cui3 = (int)$err[0]['qu3_fees'] / (int)$divide;

                            $result[] = $cui3;
                            $q3 += $cui3;
                            $fedd += $cui3;
                        } elseif ($ty == "January" || $ty == "February" || $ty == "March") {

                            $cui4 = (int)$err[0]['qu4_fees'] / (int)$divide;

                            $result[] = $cui4;
                            $q4 += $cui4;
                            $fedd += $cui4;
                        } elseif (($ty == "Admission Fee") && ($service['enroll'] > '6454') && $this->request->session()->read('Auth.User.db') == "sanskar") {
                            if (!in_array("Admission Fee", $qua2)) {

                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $result[] = '-';
                            }
                        } elseif ($ty == "Development Fee" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar") {

                            if (!in_array("Development Fee", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Caution Money" && $service['enroll'] > '6454' && $this->request->session()->read('Auth.User.db') == "sanskar") {
                            if (!in_array("Caution Money", $qua2)) {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {
                                $result[] = '-';
                            }
                        } elseif ($ty == "Miscellaneous Fee") {

                            $result[] = $err[0]['qu1_fees'];

                            $fedd += $err[0]['qu1_fees'];
                        } else {

                            if ($ty == "Admission Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Development Fee" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Caution Money" && $service['enroll'] <= '6454' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                $result[] = "OLD";
                            } elseif ($ty == "Miscellaneous Fee") {
                                $result[] = $err[0]['qu1_fees'];

                                $fedd += $err[0]['qu1_fees'];
                            } else {

                                $divide = '3';
                                $cui2 = (int)$err[0]['qu1_fees'];
                                $result[] = $cui2;
                                $q1 += $cui2;
                                $fedd += $cui2;
                                $resultss[] = $cui2;

                                $tysd++;
                            }
                        }
                    }
                }

                $findpending = $this->findpendingssinglefee($service['stud_id'], $academicyear);

                if ($findpending[0]['sum']) {
                    $result[] = $findpending[0]['sum'];
                    $fedd += $findpending[0]['sum'];
                } else {
                    $result[] = "-";
                }

                if ($service['discountcategory'] != '') {

                    $ddf = 0;
                    $ddf2 = 0;
                    $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                    $whid = unserialize($classes_data['fh_id']);
                    $swhid = unserialize($classes_data['discount']); // pr($whid);
                    foreach ($whid as $h => $dd) {

                        if ($h == "2" && $dd != '0') {
                            $ddf = $dd . "%";
                        }
                    }
                    if ($ddf == 0) {
                        // pr($whid);
                        foreach ($swhid as $hs => $dds) {

                            if ($hs == "2" && $dds != '0') {
                                $ddf2 = $dds;
                            }
                        }
                    }

                    if ($q1 != 0) {

                        if ($ddf) {
                            $tak = $ddf * $q1 / 100;

                            $result[] = "(-)" . floor($tak) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";

                            $fedd -= floor($tak);
                        } else {

                            if ($tysd != 0) {
                                $ddf2 = $ddf2 * $tysd;
                            }
                            $result[] = "(-)" . floor($ddf2) . "<br><strong
                 style='color: green;font-size: 12px;'>" . $service['discountcategory'] . "</strong>";
                            $fedd -= floor($ddf2);
                        }
                    } else {
                        $result[] = '-';
                    }
                } else {
                    $result[] = '-';
                }

                $findamountmonth = $this->findamountmonth($service['class_id'], $academicyear);
                $findamount3month = $this->findamount3month($service['class_id'], $academicyear);
                $findamount2month = $this->findamount2month($service['class_id'], $academicyear);
                $findamount1month = $this->findamount1month($service['class_id'], $academicyear);
                $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

                $perticularamounts = $this->findperticularamount($service['stud_id'], $academicyear);
                $paidfeestotal = 0;

                $discount = 0;
                foreach ($perticularamounts as $values) {

                    $paidfeestotal += $values['fee'];
                }

                foreach ($perticularamounts as $values) {

                    $discount += $values['discount'];
                }

                if ($findsum > $paidfeestotal) {

                    $dueamt = $findsum - $paidfeestotal;
                    $total_dues_amount += $dueamt;

                    if ($discount > 0) {

                        // echo $dueamt=$dueamt-$discount;

                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    } else {

                        //echo $dueamt;
                        if ($fedd != '0') {
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                            $ert = 0;
                        } else {
                            $ert = 1;
                            $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                            $total_due_amount += $fedd;
                        }
                    }
                } else {
                    if ($fedd != '0') {
                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;
                        $ert = 0;
                    } else {

                        $result[] = "<strong style='color:red'>" . $fedd . "</strong>";
                        $total_due_amount += $fedd;

                        $ert = 1;
                    }
                }

                if ($mode == '1') {
                    $ght = 0;
                    if ($q1 != '0') {
                        $ght++;
                    } elseif ($q2 != '0') {
                        $ght++;
                    } elseif ($q3 != '0') {
                        $ght++;
                    } elseif ($q4 != '0') {
                        $ght++;
                    }
                } else {

                    $ght = 1;
                }

                if ($ert != '1') {

                    if ($ght > '0') {
                        $resultsss[] = $result;
                        $counter++;
                    }
                }
            }
        }

        $headerRow3 = $total_due_amount;

        $this->set('headerRow3', $headerRow3);

        $this->set('results', $resultsss);
    }

    public function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function defaultersendsms()
    {

        if ($this->request->is(['post', 'put'])) {

            $mesg = $this->request->data['message'];
            $category = $this->request->data['category'];
            $smstemp = $this->Smsmanager->find('all')->select(['id'])->where(['category' => $category])->order(['id' => 'ASC'])->first();
            $smstemp_id = $smstemp['id'];

            $romsm = sizeof($this->request->data['p_id']);
            //pr($romsm); die;
            //---------------sms count and manage------------------------//

            $this->loadModel('Users');
            $dbname = $this->request->session()->read('Auth.User.db');
            $branch = explode("_", $dbname);
            $db = $branch[0];

            if ($db == 'canvas') {
                $connss = ConnectionManager::get('default');
                $query2 = "SELECT * FROM $db.users ";
                $res = $connss->execute($query2)->fetchAll('assoc');
            } else {
                $id = $this->request->session()->read('Auth.User.c_id');
                $res = $this->Users->find('all')->where(['c_id' => $id])->toArray();
            }
            $user_id = $res[0]['c_id'];
            $connss = ConnectionManager::get('default');
            $query2 = "SELECT * FROM store_erp.schools where id= $user_id ";
            $resss = $connss->execute($query2)->fetchAll('assoc');
            // pr($resss); die;

            $sent_msg = $resss[0]['msg_count'];
            $clint_id = $resss[0]['id'];
            $total_sent = $sent_msg - $romsm;

           // agere whatsapp api nhi hoto error show code
            if (empty($resss[0]['whatsapp_token'])) {

                $this->Flash->error(__('Kindly contact to administrator.'));
                return $this->redirect(['action' => 'students_all']);
            }


            if ($sent_msg >= $romsm) {

                $conn = ConnectionManager::get('default');
                $conn->execute("UPDATE `store_erp`.`schools` SET `msg_count`='$total_sent' WHERE id='$clint_id'");
            }
            //---------------sms count and manage code end------------------------//


            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            if ($rolepresent == '1'  || $rolepresent == '6') {

                $baord = array('1', '2', '3');
            } elseif ($rolepresent == '5') {
                $baord = array('1');
            } elseif ($rolepresent == '8') {

                $baord = array('2', '3');
            }

            if ($mesg) {

                $connsssks = ConnectionManager::get('default');

                $mesg1 = addslashes($mesg);
                $connsssks->execute("INSERT INTO
				`sms_deliveries`(`sms_temp_id`, `message`, `total_students`,`status`) VALUES
				('" . $smstemp_id . "','" . $mesg1 . "','" . $romsm . "','Y')");

                $smsdelivery = $this->Smsdelivery->find('all')->select(['id'])->order(['id' => 'DESC'])->first();

                // msg low balance count and validate
                if ($sent_msg >= $romsm) {

                    for ($is = 0; $is < $romsm; $is++) {
                        $result = '';
                        $students = $this->Students->find('all')->select(['sms_mobile', 'id', 'token'])->where(['Students.id' => $this->request->data['p_id'][$is], 'Students.board_id IN' => $baord, 'Students.status' => 'Y'])->order(['Students.id DESC'])->first();
                        $mobile = $students['sms_mobile'];
                        // $mobile='9694027991';

                        $smsconfig = $this->Sms->find('all')->where(['id' => '1'])->order(['id' => 'ASC'])->first();
                        $workingkey = $smsconfig['workingkey'];
                        $sender = $smsconfig['sender'];
                        // $result = $this->file_get_contents_curl('http://alerts.prioritysms.com/api/web2sms.php?workingkey=' . $workingkey . '&to=' . $mobile . '&sender=' . $sender . '&message=' . urlencode($mesg));
                        $mobiless = '+91' . $mobile;
                        $result = $this->whatsappmsg($mobiless, $mesg);


                        if ($result == "Invalid Input Data") {
                            $connsssks1 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');
                            $connsssks1->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`,`status`) VALUES
				('" . $students['id'] . "','" . $students['sms_mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N')");
                        } elseif ($result == "Invalid Mobile Numbers") {
                            $connsssks2 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');
                            $connsssks2->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`,`status`) VALUES
				('" . $students['id'] . "','" . $students['sms_mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N')");
                        } elseif ($result == "Insufficient credits") {

                            $connsssks12 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');
                            $connsssks12->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`,`status`) VALUES
				('" . $students['id'] . "','" . $students['sms_mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N')");

                            $this->Flash->error(__('Insufficient credits, Please Contact to Web Administrator !!!!.'));
                            return $this->redirect(['action' => 'students_all']);
                        } else {

                            $connsssks = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');
                            $connsssks->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`) VALUES
				('" . $students['id'] . "','" . $students['sms_mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "')");
                        }
                    }
                } else {
                    $this->Flash->error(__('Insufficient credits !!!!.'));
                    return $this->redirect(['action' => 'students_all']);
                }
                $_POST['title'] = 'Sanskar Campus';
                $_POST['text'] = $mesg;
                $push = null;
                $push = new \Push(
                    $_POST['title'],
                    $_POST['text']
                );
                $title =    $_POST['title'];
                $message  = $_POST['text'];
                //getting the push from push object
                $mPushNotification = $push->getPush();

                $toke = array();
                //pr($devicetok);
                $toke[] = $students['token'];

                //creating firebase class object
                $firebase = new \Firebase();

                //sending push notification and displaying result
                $title = 'PurchaseOrder';
                foreach($toke as $tok){
                    $this->sendNotification($tok, $title, $message);
                }
                // $firebase->send($toke, $mPushNotification);

                $this->Flash->success(__('Send SMS to Student sucessfully.'));
                return $this->redirect(['action' => 'students_all']);
            }
        }
    }

    public function bankdefaultersearch()
    {

        $conn = ConnectionManager::get('default');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $previous_year = $users['previous_year'];
        $academicyear = $this->request->data['acedmicyear'];
        $this->set(compact('previous_year'));
        $this->set(compact('academicyear'));

        $acedmicyear = $this->request->data['acedmicyear'];
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $quarter = $this->request->data['quarter'];
        $mode = $this->request->data['mode'];
        $this->set('mode', $mode);
        $detail = "SELECT Students.id,Students.fname,Students.comp_sid,Students.opt_sid,Students.discountcategory,Students.fathername,Students.board_id,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status, Classes.title as classtitle ,Classes.sort as csort , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

        if (!empty($acedmicyear)) {

            $cond .= " AND Students.acedmicyear LIKE '" . $acedmicyear . "' ";
        }
        if (!empty($class_id)) {

            $cond .= " AND Students.class_id LIKE '" . $class_id . "' ";

            $this->set('class_id', $class_id);
        }

        if (!empty($section_id)) {

            $cond .= " AND Students.section_id LIKE '" . $section_id . "' ";

            $this->set('section_id', $section_id);
        } else {

            //    $this->set('section_id','0');
        }
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '5') {

            $cond .= " AND Students.board_id ='1'";
        } else {

            $cond .= " AND Students.board_id!='1'";
        }
        $cond .= " AND Students.category ='NORMAL' AND Students.discountcategory !='FREE'";
        $cond .= " AND Students.status ='Y'";

        if (!empty($quarter)) {
            if ($quarter == "Quater1") {
                /*$quater[]="Admission Fee";
                $quater[]="Development Fee";
                $quater[]="Caution Money";*/
                $quater[] = "Miscellaneous Fee";
                $quater[] = "Quater1";

                $q = "Quater1";

                /*
                $quaters[]="Adm. Fee";
                $quaters[]="Dev. Fee";
                $quaters[]="Ca. Money";*/
                $quaters[] = "Misc. Fee";
                $quaters[] = "Quater1";
            } elseif ($quarter == "Quater2") {
                /*
            $quater[]="Admission Fee";
            $quater[]="Development Fee";
            $quater[]="Caution Money";*/
                //$quater[]="Miscellaneous Fee";
                //$quater[]="Quater1";
                $quater[] = "Quater2";
                $q = "Quater2";
                /*
                $quaters[]="Adm. Fee";
                $quaters[]="Dev. Fee";
                $quaters[]="Ca. Money";*/
                //$quaters[]="Misc. Fee";
                //$quaters[]="Quater1";
                $quaters[] = "Quater2";
            } elseif ($quarter == "Quater3") {
                /*
            $quater[]="Admission Fee";
            $quater[]="Development Fee";
            $quater[]="Caution Money";*/
                //$quater[]="Miscellaneous Fee";
                //$quater[]="Quater1";
                //$quater[]="Quater2";
                $quater[] = "Quater3";
                $q = "Quater3";

                /*$quaters[]="Adm. Fee";
            $quaters[]="Dev. Fee";
            $quaters[]="Ca. Money";*/
                //$quaters[]="Misc. Fee";
                //$quaters[]="Quater1";
                //$quaters[]="Quater2";
                $quaters[] = "Quater3";
            } elseif ($quarter == "Quater4") {

                /*$quater[]="Admission Fee";
            $quater[]="Development Fee";
            $quater[]="Caution Money";*/
                //$quater[]="Miscellaneous Fee";
                /*$quater[]="Quater1";
                            $quater[]="Quater2";
                 */
                $quater[] = "Quater4";
                $q = "Quater4";

                /*$quaters[]="Adm. Fee";
            $quaters[]="Dev. Fee";
            $quaters[]="Ca. Money";*/
                //$quaters[]="Misc. Fee";
                /*$quaters[]="Quater1";
                            $quaters[]="Quater2";
                            */
                $quaters[] = "Quater4";
            }
        }

        $this->set('quaters', $quater);

        $this->set('quatersf', $quaters);
        //    $cond.=" AND Students.section_id LIKE '".$section_id."%' ";

        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY csort ASC, sectiontitle ASC,Students.fname ASC LIMIT 0,2500";

        $results = $conn->execute($SQL)->fetchAll('assoc');

        ini_set('max_execution_time', 1600);

        if (isset($results) && !empty($results)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;

            foreach ($results as $h => $service) {

                $student_datas35as = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $service['id'], 'Studentfees.type' => 'Fee', 'Studentfees.acedmicyear' => $acedmicyear, 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%' . $q . '%'])->first();

                if ($student_datas35as['id'] == "" && $mode != '1') {

                    $result = array();
                    $fedd = 0;

                    $result[] = $service['id'];

                    $result[] = $service['enroll'];
                    $result[] = $service['fname'] . " " . $service['middlename'] . " " . $service['lname'];
                    // $result[]=$service['fname']." ".$service['middlename']." ".$service['lname'];

                    $s_id = $service['class_id'];
                    $c_id = $service['section_id'];

                    $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                    $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                    $result[] = $classssr['title'] . "-" . $sectionssss['title'];

                    $result[] = $service['fathername'];
                    $result[] = $service['sms_mobile'];

                    if ($service['discountcategory'] != '') {
                        $result[] = $service['discountcategory'];
                    } else {
                        $result[] = "-";
                    }
                    $studentfees = $this->finddisountstudent($service['id'], $academicyear);
                    //pr($studentfees);
                    $q1 = 0;
                    $q2 = 0;
                    $q3 = 0;
                    $q4 = 0;
                    $quas = array();

                    foreach ($studentfees as $k => $value) {
                        $quas[] = unserialize($value['quarter']);
                    }

                    $quaf = array();

                    foreach ($quas as $h => $vale) {

                        $quaf = array_merge($quaf, $vale);
                    }
                    $rt = array();
                    foreach ($quaf as $j => $t) {

                        $qua[] = $j;
                    }

                    foreach ($quaters as $h => $ty) {

                        if (!empty($quaf)) {
                            $dff = 0;
                            foreach ($quaf as $t => $h) {
                                if ($t == $ty) {

                                    if ($ty == "Admission Fee" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        //  $result[]="OLD";
                                    } elseif ($ty == "Development Fee" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        //   $result[]="OLD";
                                    } elseif ($ty == "Caution Money" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        // $result[]="OLD";
                                    } else {

                                        $result[] = "-";
                                    }
                                    $dff++;
                                } else {
                                }
                            }
                            if ($dff == '0') {

                                if ($ty == "Quater1") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater2") {
                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater3") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater4") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Misc. Fee") {

                                    $tys = "Miscellaneous Fee";
                                }

                                $feeshead = $this->findfeeheadsid($tys);
                                $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                                if ($ty == "Misc. Fee") {
                                    if ($err[0]['qu1_fees'] != '') {
                                        $result[] = $err[0]['qu1_fees'];
                                        $fedd += $err[0]['qu1_fees'];
                                        $q1 = $err[0]['qu1_fees'];
                                        $feesheadd = $this->findfeeheadsid("Tution Fee");
                                        $errd = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheadd['id']);
                                        $result[] = $errd[0]['qu1_fees'];
                                        $q1 = $errd[0]['qu1_fees'];
                                        $fedd += $errd[0]['qu1_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } else {
                                    $result[] = '-';
                                }
                                //~ if($ty=="Quater1"){
                                //~ if($err[0]['qu1_fees'] !=''){
                                //~ $result[]=$err[0]['qu1_fees'];
                                //~ $fedd +=$err[0]['qu1_fees'];
                                //~ $q1=$err[0]['qu1_fees'];

                                //~ }else{
                                //~ $result[]='-';

                                //~ }
                                //~ }else
                                if ($ty == "Quater2") {

                                    if ($err[0]['qu2_fees'] != '') {
                                        $result[] = $err[0]['qu2_fees'];
                                        $fedd += $err[0]['qu2_fees'];
                                        $q2 = $err[0]['qu2_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } elseif ($ty == "Quater3") {

                                    if ($err[0]['qu3_fees'] != '') {
                                        $result[] = $err[0]['qu3_fees'];
                                        $fedd += $err[0]['qu3_fees'];
                                        $q3 = $err[0]['qu3_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } elseif ($ty == "Quater4") {

                                    if ($err[0]['qu4_fees'] != '') {
                                        $result[] = $err[0]['qu4_fees'];
                                        $fedd += $err[0]['qu4_fees'];
                                        $q4 = $err[0]['qu4_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } else {
                                    $result[] = '-';
                                }
                            } else {

                                $result[] = "-";
                            }
                        } else {

                            if ($ty == "Quater1") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater2") {
                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater3") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater4") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Misc. Fee") {

                                $tys = "Miscellaneous Fee";
                            }

                            $feeshead = $this->findfeeheadsid($tys);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            if ($ty == "Misc. Fee") {
                                if ($err[0]['qu1_fees'] != '') {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q0 = $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];
                                    $feesheadd = $this->findfeeheadsid("Tution Fee");
                                    $errd = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheadd['id']);
                                    $result[] = $errd[0]['qu1_fees'];
                                    $q1 = $errd[0]['qu1_fees'];
                                    $fedd += $errd[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } else {
                                $result[] = '-';
                            }

                            //~ if($ty=="Quater1"){

                            //~ if($err[0]['qu1_fees'] !=''){
                            //~ $result[]=$err[0]['qu1_fees'];
                            //~ $q1=$err[0]['qu1_fees'];
                            //~ $fedd +=$err[0]['qu1_fees'];

                            //~ }else{
                            //~ $result[]='-';

                            //~ }

                            //~ }else
                            if ($ty == "Quater2") {

                                if ($err[0]['qu2_fees'] != '') {
                                    $result[] = $err[0]['qu2_fees'];
                                    $q2 = $err[0]['qu2_fees'];
                                    $fedd += $err[0]['qu2_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Quater3") {

                                if ($err[0]['qu3_fees'] != '') {
                                    $result[] = $err[0]['qu3_fees'];
                                    $q3 = $err[0]['qu3_fees'];
                                    $fedd += $err[0]['qu3_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Quater4") {

                                if ($err[0]['qu4_fees'] != '') {
                                    $result[] = $err[0]['qu4_fees'];
                                    $q4 = $err[0]['qu4_fees'];
                                    $fedd += $err[0]['qu4_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } else {
                                $result[] = '-';
                            }
                        }
                    }

                    if (
                        $service['class_id'] == 12 || $service['class_id'] == 13
                        || $service['class_id'] == 15 || $service['class_id'] == 17 ||
                        $service['class_id'] == 20 || $service['class_id'] == 22 ||
                        $service['class_id'] == 26 || $service['class_id'] == 27
                    ) {

                        $practicalfee = 0;

                        $compsid = explode(',', $service['comp_sid']);
                        $opt_sid = explode(',', $service['opt_sid']);

                        foreach ($compsid as $k => $g) {

                            $subjectpracticals = $this->classspractical($g);
                            if ($subjectpracticals) {
                                $practicalfee += $subjectpracticals['is_practicalfee'];
                            }
                        }

                        foreach ($opt_sid as $ks => $gs) {

                            $subjectpracticalss = $this->classspractical($gs);
                            if ($subjectpracticalss) {
                                $practicalfee += $subjectpracticalss['is_practicalfee'];
                            }
                        }

                        $result[] = $practicalfee;
                        // $fedd +=$practicalfee;
                    } else {

                        $result[] = "-";
                    }

                    $findpending = $this->findpendingssinglefee($service['id'], $academicyear);

                    if ($findpending[0]['sum']) {
                        $result[] = $findpending[0]['sum'];
                        // $fedd +=$findpending[0]['sum'];
                    } else {
                        $result[] = "-";
                    }

                    if ($service['discountcategory'] != '') {

                        $ddf = 0;
                        $ddf2 = 0;
                        $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                        $whid = unserialize($classes_data['fh_id']);
                        $swhid = unserialize($classes_data['discount']); // pr($whid);
                        foreach ($whid as $h => $dd) {

                            if ($h == "2" && $dd != '0') {
                                $ddf = $dd . "%";
                            }
                        }
                        if ($ddf == 0) {
                            // pr($whid);
                            foreach ($swhid as $hs => $dds) {

                                if ($hs == "2" && $dds != '0') {
                                    $ddf2 = $dds;
                                }
                            }
                        }

                        if (in_array("Quater1", $quaters)) {

                            // $result[]=$err[0]['qu1_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q1 / 100;

                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater2", $quaters)) {

                            //$result[]=$err[0]['qu2_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q2 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater3", $quaters)) {

                            // $result[]=$err[0]['qu3_fees'];
                            if ($ddf) {

                                $tak = $ddf * $q3 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater4", $quaters)) {

                            //  $result[]=$err[0]['qu4_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q4 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        }
                    } else {
                        $result[] = "0";
                    }

                    $result[] = $fedd;

                    $counter++;

                    if ($result[8] != '-') {
                        $res[] = $result;
                    } else {
                    }
                } elseif ($mode == '1') {

                    $result = array();
                    $fedd = 0;

                    $result[] = $service['id'];

                    $result[] = $service['enroll'];
                    $result[] = $service['fname'] . " " . $service['middlename'] . " " . $service['lname'];
                    // $result[]=$service['fname']." ".$service['middlename']." ".$service['lname'];

                    $s_id = $service['class_id'];
                    $c_id = $service['section_id'];

                    $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                    $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                    $result[] = $classssr['title'] . "-" . $sectionssss['title'];

                    $result[] = $service['fathername'];
                    $result[] = $service['sms_mobile'];

                    if ($service['discountcategory'] != '') {
                        $result[] = $service['discountcategory'];
                    } else {
                        $result[] = "-";
                    }
                    $studentfees = $this->finddisountstudent($service['id'], $academicyear);
                    //pr($studentfees);
                    $q1 = 0;
                    $q2 = 0;
                    $q3 = 0;
                    $q4 = 0;
                    $quas = array();

                    foreach ($studentfees as $k => $value) {
                        $quas[] = unserialize($value['quarter']);
                    }

                    $quaf = array();

                    foreach ($quas as $h => $vale) {

                        $quaf = array_merge($quaf, $vale);
                    }
                    $rt = array();
                    foreach ($quaf as $j => $t) {

                        $qua[] = $j;
                    }

                    foreach ($quaters as $h => $ty) {

                        if (!empty($quaf)) {
                            $dff = 0;
                            foreach ($quaf as $t => $h) {
                                if ($t == $ty) {

                                    if ($ty == "Admission Fee" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        //  $result[]="OLD";
                                    } elseif ($ty == "Development Fee" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        //   $result[]="OLD";
                                    } elseif ($ty == "Caution Money" && $service['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $service['board_id'] == '1') {
                                        // $result[]="OLD";
                                    } else {

                                        $result[] = "-";
                                    }
                                    $dff++;
                                } else {
                                }
                            }
                            if ($dff == '0') {

                                if ($ty == "Quater1") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater2") {
                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater3") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Quater4") {

                                    $tys = "Tution Fee";
                                } elseif ($ty == "Misc. Fee") {

                                    $tys = "Miscellaneous Fee";
                                }

                                $feeshead = $this->findfeeheadsid($tys);
                                $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                                if ($ty == "Misc. Fee") {
                                    if ($err[0]['qu1_fees'] != '') {
                                        $result[] = $err[0]['qu1_fees'];
                                        $fedd += $err[0]['qu1_fees'];
                                        $q1 = $err[0]['qu1_fees'];
                                        $feesheadd = $this->findfeeheadsid("Tution Fee");
                                        $errd = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheadd['id']);
                                        $result[] = $errd[0]['qu1_fees'];
                                        $q1 = $errd[0]['qu1_fees'];
                                        $fedd += $errd[0]['qu1_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } else {
                                    $result[] = '-';
                                }
                                //~ if($ty=="Quater1"){
                                //~ if($err[0]['qu1_fees'] !=''){
                                //~ $result[]=$err[0]['qu1_fees'];
                                //~ $fedd +=$err[0]['qu1_fees'];
                                //~ $q1=$err[0]['qu1_fees'];

                                //~ }else{
                                //~ $result[]='-';

                                //~ }
                                //~ }else
                                if ($ty == "Quater2") {

                                    if ($err[0]['qu2_fees'] != '') {
                                        $result[] = $err[0]['qu2_fees'];
                                        $fedd += $err[0]['qu2_fees'];
                                        $q2 = $err[0]['qu2_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } elseif ($ty == "Quater3") {

                                    if ($err[0]['qu3_fees'] != '') {
                                        $result[] = $err[0]['qu3_fees'];
                                        $fedd += $err[0]['qu3_fees'];
                                        $q3 = $err[0]['qu3_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } elseif ($ty == "Quater4") {

                                    if ($err[0]['qu4_fees'] != '') {
                                        $result[] = $err[0]['qu4_fees'];
                                        $fedd += $err[0]['qu4_fees'];
                                        $q4 = $err[0]['qu4_fees'];
                                    } else {
                                        $result[] = '-';
                                    }
                                } else {
                                    $result[] = '-';
                                }
                            } else {

                                $result[] = "-";
                            }
                        } else {

                            if ($ty == "Quater1") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater2") {
                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater3") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Quater4") {

                                $tys = "Tution Fee";
                            } elseif ($ty == "Misc. Fee") {

                                $tys = "Miscellaneous Fee";
                            }

                            $feeshead = $this->findfeeheadsid($tys);
                            $err = $this->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);

                            if ($ty == "Misc. Fee") {
                                if ($err[0]['qu1_fees'] != '') {
                                    $result[] = $err[0]['qu1_fees'];
                                    $q0 = $err[0]['qu1_fees'];
                                    $fedd += $err[0]['qu1_fees'];
                                    $feesheadd = $this->findfeeheadsid("Tution Fee");
                                    $errd = $this->findfeeheadsamount($service['class_id'], $academicyear, $feesheadd['id']);
                                    $result[] = $errd[0]['qu1_fees'];
                                    $q1 = $errd[0]['qu1_fees'];
                                    $fedd += $errd[0]['qu1_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } else {
                                $result[] = '-';
                            }

                            //~ if($ty=="Quater1"){

                            //~ if($err[0]['qu1_fees'] !=''){
                            //~ $result[]=$err[0]['qu1_fees'];
                            //~ $q1=$err[0]['qu1_fees'];
                            //~ $fedd +=$err[0]['qu1_fees'];

                            //~ }else{
                            //~ $result[]='-';

                            //~ }

                            //~ }else
                            if ($ty == "Quater2") {

                                if ($err[0]['qu2_fees'] != '') {
                                    $result[] = $err[0]['qu2_fees'];
                                    $q2 = $err[0]['qu2_fees'];
                                    $fedd += $err[0]['qu2_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Quater3") {

                                if ($err[0]['qu3_fees'] != '') {
                                    $result[] = $err[0]['qu3_fees'];
                                    $q3 = $err[0]['qu3_fees'];
                                    $fedd += $err[0]['qu3_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } elseif ($ty == "Quater4") {

                                if ($err[0]['qu4_fees'] != '') {
                                    $result[] = $err[0]['qu4_fees'];
                                    $q4 = $err[0]['qu4_fees'];
                                    $fedd += $err[0]['qu4_fees'];
                                } else {
                                    $result[] = '-';
                                }
                            } else {
                                $result[] = '-';
                            }
                        }
                    }

                    if (
                        $service['class_id'] == 12 || $service['class_id'] == 13
                        || $service['class_id'] == 15 || $service['class_id'] == 17 ||
                        $service['class_id'] == 20 || $service['class_id'] == 22 ||
                        $service['class_id'] == 26 || $service['class_id'] == 27
                    ) {

                        $practicalfee = 0;

                        $compsid = explode(',', $service['comp_sid']);
                        $opt_sid = explode(',', $service['opt_sid']);

                        foreach ($compsid as $k => $g) {

                            $subjectpracticals = $this->classspractical($g);
                            if ($subjectpracticals) {
                                $practicalfee += $subjectpracticals['is_practicalfee'];
                            }
                        }

                        foreach ($opt_sid as $ks => $gs) {

                            $subjectpracticalss = $this->classspractical($gs);
                            if ($subjectpracticalss) {
                                $practicalfee += $subjectpracticalss['is_practicalfee'];
                            }
                        }

                        $result[] = $practicalfee;
                        // $fedd +=$practicalfee;
                    } else {

                        $result[] = "-";
                    }

                    $findpending = $this->findpendingssinglefee($service['id'], $academicyear);

                    if ($findpending[0]['sum']) {
                        $result[] = $findpending[0]['sum'];
                        // $fedd +=$findpending[0]['sum'];
                    } else {
                        $result[] = "-";
                    }

                    if ($service['discountcategory'] != '') {

                        $ddf = 0;
                        $ddf2 = 0;
                        $classes_data = $this->DiscountCategory->find('all')->where(['name' => $service['discountcategory']])->first();
                        $whid = unserialize($classes_data['fh_id']);
                        $swhid = unserialize($classes_data['discount']); // pr($whid);
                        foreach ($whid as $h => $dd) {

                            if ($h == "2" && $dd != '0') {
                                $ddf = $dd . "%";
                            }
                        }
                        if ($ddf == 0) {
                            // pr($whid);
                            foreach ($swhid as $hs => $dds) {

                                if ($hs == "2" && $dds != '0') {
                                    $ddf2 = $dds;
                                }
                            }
                        }

                        if (in_array("Quater1", $quaters)) {

                            // $result[]=$err[0]['qu1_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q1 / 100;

                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater2", $quaters)) {

                            //$result[]=$err[0]['qu2_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q2 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater3", $quaters)) {

                            // $result[]=$err[0]['qu3_fees'];
                            if ($ddf) {

                                $tak = $ddf * $q3 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        } elseif (in_array("Quater4", $quaters)) {

                            //  $result[]=$err[0]['qu4_fees'];
                            if ($ddf) {
                                $tak = $ddf * $q4 / 100;
                                $result[] = floor($tak);
                                $fedd -= floor($tak);
                            } else {

                                $result[] = $ddf2;
                                $fedd -= $ddf2;
                            }
                        }
                    } else {
                        $result[] = "0";
                    }

                    $result[] = $fedd;

                    $counter++;

                    if ($result[8] != '-') {
                        $res[] = $result;
                    } else {
                    }
                }
            }
        }

        $this->set('res', $res);
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
            $totalhostelamount += $amount[0]['amount'];
            $total_hostel_paidamount += $amount[0]['fee'];
            $discount += $amount[0]['amount'] - $amount[0]['fee'];
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

        $filename = "HostelFee-report" . $service['classtitle'] . "(" . $service['sectiontitle'] . ").csv";
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
        $from = date('Y-m-d', strtotime($this->request->data['from']));
        $response = date('Y-m-d', strtotime($this->request->data['response']));
        //    echo $from;
        //    echo $response; die;

        //$detail="SELECT * FROM `leaves` Leaves WHERE 1=1  LEFT JOIN employees Employees ON Leaves.`emp_id` = Employees.id";
        $detail = "SELECT Leaves.date_from,Leaves.date_to,Leaves.t_days,Leaves.narration,Leavetype.name,Employees.id,Employees.fname,Employees.mobile,Employees.department_id,Employees.designation_id FROM `leaves` Leaves LEFT JOIN employees Employees ON Leaves.`emp_id` = Employees.id  LEFT JOIN  leavetype Leavetype ON Leaves.`leave_type_id` = Leavetype.id WHERE 1=1";
        $cond = ' ';
        if (!empty($emp_id)) {

            $cond .= " AND Leaves.emp_id =" . $emp_id;
        }

        if (!empty($from)) {

            //    $cond.=" AND Leaves.date_from LIKE '".$from."%' ";
            $cond .= " AND Leaves.date_from   >='" . $from . "'";
        }
        if (!empty($response) && $response != "1970-01-01") {

            $cond .= "AND Leaves.date_to  <='" . $response . "'";
        }

        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY Leaves.id DESC";
        //echo $SQL; die;
        $leav = $conn->execute($SQL)->fetchAll('assoc');
        //pr($leav); die;
        $this->set('leav', $leav);
    }

    public function user_supportiv9()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $leav = $session->read('leav');
        $output .= '"Emp-Id",';
        $output .= '"E-Name",';
        $output .= '"E-Mobile",';
        $output .= '"Father",';
        $output .= '"Department",';
        $output .= '"Designation",';
        $output .= '"Leave Type",';
        $output .= '"Start Date",';
        $output .= '"End Date",';
        $output .= '"Total Days",';
        $output .= "\n";
        foreach ($leav as $service) {
            $guardian = $this->find_guardiannames($service['id']);
            $guardian_name = $guardian[0]['fullname'];
            $department_id = $this->finddepartment($service['department_id']);
            $designation_id = $this->finddesignation($service['designation_id']);
            $output .= $service['id'] . ",";
            $output .= $service['fname'] . ",";
            $output .= $service['mobile'] . ",";
            $output .= $guardian_name . ",";
            $output .= str_replace(" ", "-", $department_id[0]['name']) . ",";
            $output .= str_replace(" ", "-", $designation_id[0]['name']) . ",";
            $output .= str_replace(" ", "-", $service['name']) . ",";
            $output .= $service['date_from'] . ",";
            $output .= $service['date_to'] . ",";
            $output .= $service['t_days'] . ",";
            $output .= "\r\n";
        }
        $filename = "Leave-report.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
        $this->redirect($this->referer());
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

        $data0 = $this->Studattends->find()->select([
            'Studattends.stud_id'
        ])->where(['Studattends.class_id' => $classid, 'Studattends.section_id' => $sectionid, 'Studattends.acedemic' => $acedmicyear])->group([
            'Studattends.stud_id'
        ])->toarray();

        $days = date("t", mktime(0, 0, 0, $month, 1, $rff['0']));
        $is = 0;

        foreach ($data0 as $key => $value) {
            $is++;
            $cntpresent = 0;
            $cntabsent = 0;
            $datas[$is][0] = $value['stud_id'];

            for ($i = 1; $i <= $days; $i++) {

                $d = $rff[0] . "-" . $month . "-" . $i;
                $datal = $this->Studattends->find('all')->select([
                    'Studattends.status', 'Studattends.date'
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
    public function find_section($id = null)
    {

        $classid = $this->request->data['id'];
        $this->viewBuilder()->layout('admin');
        $sections = $this->Classections->find('list', [
            'keyField' => 'Sections.id',
            'valueField' => 'Sections.title',
        ])->contain(['Sections'])->where(['Classections.class_id' => $classid])->order(['Sections.title' => 'ASC'])->toArray();

        echo "<option value=''>Select Section</option>";
        foreach ($sections as $sections => $value) {
            echo "<option value=" . $sections . ">" . $value . "</option>";
        }
        die;
    }

    public function sattendance($studid = null)
    {
        $this->viewBuilder()->layout('admin');
        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classections.status' => 'Y'])->order(['Classes.title' => 'ASC'])->toArray();
        //    pr($classes); die;
        $this->set(compact('classes'));

        if ($studid) {

            $students = $this->Students->find('all')->where(['id' => $studid, 'status' => 'Y'])->order(['id' => 'DESC'])->first()->toArray();
            $class_id = $students['class_id'];
            $section_id = $students['section_id'];
            $acedmicyear = $students['acedmicyear'];
            $this->set('class_id', $class_id);
            $this->set('section_id', $section_id);
            $this->set('acedmicyear', $acedmicyear);
            $sections = $this->Classections->find('list', [
                'keyField' => 'Sections.id',
                'valueField' => 'Sections.title',
            ])->contain(['Sections'])->where(['Classections.class_id' => $class_id])->order(['Sections.title' => 'ASC'])->toArray();
            $this->set('sections', $sections);
            $conn = ConnectionManager::get('default');
            $mid = base64_encode($class_id . '/' . $section_id . '/' . $acedmicyear);
            $this->set('mid', $mid);
            $name = $this->request->data['name'];
            $detail = "SELECT Students.id,Students.fname,Students.middlename,Students.acedmicyear,Students.lname,Studattends.day,Studattends.remark,Studattends.date,Studattends.status, Classes.title as classtitle , Sections.title as sectiontitle FROM `studattends` Studattends LEFT JOIN students Students ON Studattends.`stud_id` = Students.id  LEFT JOIN  classes Classes ON Studattends.`class_id` = Classes.id  LEFT JOIN sections Sections ON Studattends.`section_id` = Sections.id  WHERE 1=1";
            $cond = ' ';
            if (!empty($class_id)) {

                $cond .= " AND Studattends.class_id =" . $class_id;
            }

            if (!empty($section_id)) {

                $cond .= " AND Studattends.section_id =" . $section_id;
            }
            if (!empty($acedmicyear)) {
                $this->set('acedmicyear', $acedmicyear);
                $cond .= " AND Studattends.acedemic LIKE '" . $acedmicyear . "%' ";
            }

            $cond .= "AND Students.id LIKE '" . $studid . "%' ";
            $cond .= "AND Students.status = 'Y' ";
            $detail = $detail . $cond;
            $SQL = $detail . " GROUP BY Students.id ORDER BY Studattends.id DESC";
            //    echo $SQL; die;
            $attend = $conn->execute($SQL)->fetchAll('assoc');
            //pr($attend); die;
            $this->set('attend', $attend);
        }
    }
    public function search10()
    {
        $conn = ConnectionManager::get('default');
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $acedmicyear = $this->request->data['acedmicyear'];
        $mid = base64_encode($class_id . '/' . $section_id . '/' . $acedmicyear);
        $this->set('mid', $mid);
        $name = $this->request->data['name'];
        $detail = "SELECT Students.id,Students.fname,Students.middlename,Students.acedmicyear,Students.lname,Studattends.day,Studattends.remark,Studattends.date,Studattends.status, Classes.title as classtitle , Sections.title as sectiontitle FROM `studattends` Studattends LEFT JOIN students Students ON Studattends.`stud_id` = Students.id  LEFT JOIN  classes Classes ON Studattends.`class_id` = Classes.id  LEFT JOIN sections Sections ON Studattends.`section_id` = Sections.id  WHERE 1=1";
        $cond = ' ';
        if (!empty($class_id)) {

            $cond .= " AND Studattends.class_id =" . $class_id;
        }

        if (!empty($section_id)) {

            $cond .= " AND Studattends.section_id =" . $section_id;
        }
        if (!empty($acedmicyear)) {
            $this->set('acedmicyear', $acedmicyear);
            $cond .= " AND Studattends.acedemic LIKE '" . $acedmicyear . "%' ";
        }
        $cond .= "AND Students.status = 'Y' ";

        $detail = $detail . $cond;
        $SQL = $detail . " GROUP BY Students.id ORDER BY Studattends.id DESC";
        //    echo $SQL; die;
        $attend = $conn->execute($SQL)->fetchAll('assoc');
        //pr($attend); die;
        $this->set('attend', $attend);
    }

    public function user_supportiv10()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $attend = $session->read('attend');
        $output .= '"Student-Enroll",';
        $output .= '"April",';
        $output .= '"May",';
        $output .= '"June",';
        $output .= '"July",';
        $output .= '"August",';
        $output .= '"September",';
        $output .= '"October",';
        $output .= '"November",';
        $output .= '"December",';
        $output .= '"January",';
        $output .= '"February",';
        $output .= '"March",';

        $year = date("Y");
        $tdays = 0;
        $holicount = $this->findholicount('8');
        foreach ($holicount as $key => $value) {
            //pr($value);
            $endDate = strtotime($value['endtime']);
            //  echo $endDate."<br>";
            $startDate = strtotime($value['starttime']);
            //   echo $startDate;
            $days = ($endDate - $startDate) / 86400 + 1;
            $tdays += number_format($days);
        }
        $cnt = $this->getWorkingDays($year . "-04-01", $year + '1' . "-04-01") - $tdays;
        $output .= '"Total"(' . $cnt . '),';
        $output .= "\n";
        foreach ($attend as $service) {
            $total = 0;
            $output .= $service['id'] . ",";
            $output .= $this->findcount($service['id'], '04', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '04', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '05', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '05', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '06', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '06', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '07', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '07', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '08', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '08', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '09', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '09', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '10', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '10', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '11', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '11', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '12', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '12', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '01', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '01', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '02', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '02', $service['acedmicyear']);
            $output .= $this->findcount($service['id'], '03', $service['acedmicyear']) . ",";
            $total += $this->findcount($service['id'], '03', $service['acedmicyear']);
            $output .= $total . ",";

            $output .= "\r\n";
        }
        $filename = "Student Attendance-report" . $service['acedmicyear'] . ".csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
        $this->redirect($this->referer());
    }

    public function findholicount($id = null)
    {
        $articles = TableRegistry::get('Events');
        return $articles->find('all')->where(['Events.eventt' => $id])->toarray();
    }

    public function getWorkingDays($startDate, $endDate)
    {
        $begin = strtotime($startDate);
        $end = strtotime($endDate);
        if ($begin > $end) {
            echo "startdate is in the future! <br />";

            return 0;
        } else {
            $no_days = 0;
            $weekends = 0;
            while ($begin <= $end) {
                $no_days++; // no of days in the given interval
                $what_day = date("N", $begin);
                if ($what_day > 6) { // 6 and 7 are weekend days
                    $weekends++;
                };
                $begin += 86400; // +1 day
            };
            $working_days = $no_days - $weekends;

            return $working_days;
        }
    }

    public function orooms()
    {
        $this->viewBuilder()->layout('admin');

        $hostel = $this->Hostels->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Hostels.status' => 'Y'])->order(['name' => 'ASC'])->toArray();
        //    pr($classes); die;
        $this->set(compact('hostel'));
    }

    public function search11()
    {
        $conn = ConnectionManager::get('default');
        $h_id = $this->request->data['h_id'];
        $detail = "SELECT Students.id,Students.fname,Students.mobile,Students.acedmicyear,Students.room_no,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle, Hostels.name as hostelname FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id  LEFT JOIN hostels Hostels ON Students.`h_id` = Hostels.id    WHERE  1=1";
        $cond = ' ';
        if (!empty($h_id)) {

            $cond .= " AND Students.h_id =" . $h_id;
        }
        $cond .= " AND Students.is_hostel=" . '1';
        $cond .= "AND Students.status = 'Y' ";
        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY Students.id DESC";

        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->set('student_hostel', $results);
    }

    public function user_supportiv11()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $student_hostel = $session->read('student_hostel');
        $output .= '"Student Id",';
        $output .= '"Student Name",';
        $output .= '"Class",';
        $output .= '"Section",';
        $output .= '"Hostel Name",';
        $output .= '"Occupied Room",';
        $output .= '"EPBX",';
        $output .= "\n";

        foreach ($student_hostel as $service) {
            $output .= $service['id'] . ",";
            $output .= $service['fname'] . ",";
            $output .= $service['classtitle'] . ",";
            $output .= $service['sectiontitle'] . ",";
            $output .= $service['hostelname'] . ",";
            $output .= "Room-" . $service['room_no'] . ",";
            $epbx = implode("", $this->findepbx($service['h_id']));
            $output .= $epbx . ",";
            $output .= "\r\n";
        }
        $filename = "Occupied Rooms-report.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
        $this->redirect($this->referer());
    }

    public function findepbx($id = null)
    {
        $articles = TableRegistry::get('Hostelrooms');
        return $articles->find('list', ['keyField' => 'id', 'valueField' => 'epax'])->where(['Hostelrooms.h_id' => $id])->toArray();
    }

    public function findcount($id = null, $month = null, $acedmicyear = null)
    {

        $articles = TableRegistry::get('Studattends');
        return $articles->find('all')->where(['Studattends.stud_id' => $id, 'MONTH(Studattends.date)' => $month, 'Studattends.status' => 'P', 'Studattends.acedemic' => $acedmicyear])->count();
    }

    public function find_guardiannames($id)
    {
        // pr ($rout); die;
        // Use the HTML helper to output
        // Formatted data:
        $articles = TableRegistry::get('Guardians');

        // Start a new query.

        return $articles->find('all')->where(['Guardians.user_id' => $id, 'Guardians.relation' => 'Father'])->toArray();
    }

    public function findclassess($id = null)
    {
        $clsasses = $this->Classes->find('all')->where(['Classes.id' => $id])->toArray();
        return $clsasses;
    }
    public function findclass($id = null)
    {
        $clsasses = $this->Classes->find('all')->where(['Classes.id' => $id])->first();
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

        return $this->Departments->find('all')->where(['Departments.id' => $id])->toArray();
    }
    public function finddesignation($id)
    {

        return $this->Designations->find('all')->where(['Designations.id' => $id])->toArray();
    }

    public function findamount($id, $a_year)
    {

        return $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find('all')->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->where(['Classfee.class_id' => $id, 'Classfee.academic_year' => $a_year])->order(['Classfee.id' => 'ASC'])->toArray();
    }

    public function findstudentcount($id, $a_year, $section_id)
    {
        return $this->Students->find('all')->where(['Students.acedmicyear' => $a_year, 'Students.status' => 'Y', 'Students.class_id' => $id, 'Students.section_id' => $section_id])->count();
    }

    public function findpaidamount($a_year)
    {
        return $this->Studentfees->find('all')->contain(['Students'])->where(['Studentfees.acedmicyear' => $a_year, 'Studentfees.acedmicyear' => $a_year, 'Studentfees.status' => 'Y'])->order(['Studentfees.id' => 'ASC'])->toArray();
    }

    public function findperticularamount($id, $a_year)
    {

        return $this->Studentfees->find('all')->select(['id', 'fee', 'discount', 'deposite_amt', 'lfine'])->where(['Studentfees.student_id' => $id, 'Studentfees.acedmicyear' => $a_year, 'Studentfees.status' => 'Y'])->toArray();
    }

    public function findroute($id)
    {
        $articles = TableRegistry::get('Transports');

        //  $arrayOfIds =['3'];
        return $this->Transports->find('all')->where(['FIND_IN_SET(\'' . $id . '\',Transports.route)'])->toarray();
    }

    public function findlocation($id)
    {
        $articles = TableRegistry::get('Locations');

        //  $arrayOfIds =['3'];
        return $this->Locations->find('all')->where(['Locations.id' => $id])->first()->toarray();
    }
    public function findtransportamount($id, $a_year)
    {
        return $this->Transportfees->find('all')->group('Transportfees.academic_year')->group('Transportfees.loc_id')->select(['qu1_fees' => $this->Transportfees->find()->func()->sum('Transportfees.qu1_fees'), 'qu2_fees' => $this->Transportfees->find('all')->func()->sum('Transportfees.qu2_fees'), 'qu3_fees' => $this->Transportfees->find('all')->func()->sum('Transportfees.qu3_fees'), 'qu4_fees' => $this->Transportfees->find('all')->func()->sum('Transportfees.qu4_fees')])->where(['Transportfees.loc_id' => $id, 'Transportfees.academic_year' => $a_year])->order(['Transportfees.id' => 'ASC'])->toArray();
    }

    public function findpaidtransportamount($id, $a_year)
    {

        return $this->StudentTransfees->find('all')->where(['StudentTransfees.student_id' => $id, 'StudentTransfees.acedmicyear' => $a_year])->toArray();
    }
    public function findhostelamount($s_id, $a_year, $h_id)
    {

        return $this->StudentHostalfees->find('all')->where(['StudentHostalfees.student_id' => $s_id, 'StudentHostalfees.acedmicyear' => $a_year, 'StudentHostalfees.h_id' => $h_id])->toArray();
    }

    //------------------------------------------------------------
    public function issuedbooksreport()
    {
        // echo 'Rupam';die;
        $this->loadModel('Language');
        $this->viewBuilder()->layout('admin');
        $lahu = $this->Language->find('list', [
            'keyField' => 'id',
            'valueField' => 'language',
        ])->order(['Language.id' => 'asc'])->toArray();
        //pr($lahu); die;
        $this->set('lahu', $lahu);

        $holder_type = ['Student' => 'Student', 'Employee' => 'Employee'];
        $this->set('holder_type', $holder_type);

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.id' => 'asc'])->toArray();
        $this->set('classes', $classes);

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('sectionslist', $sectionslist);

        $status_data = $this->BookStatus->find()->select('name')->where(['BookStatus.status' => 'Y', 'BookStatus.id !=' => '3'])->order(['BookStatus.name' => 'Asc'])->toarray();

        foreach ($status_data as $value) {
            $element = $value['name'];

            $b_status[$element] = $element;
        }

        $this->set('b_status', $b_status);
    }

    //---------------------------------------------------------------------
    public function searchIssuedBook()
    {
        $this->loadModel('Boards');
        $session = $this->request->session();
        // pr($this->request->data); die;

        $conn = ConnectionManager::get('default');

        $isbn_no = $this->request->data['isbn_no'];
        $b_name = $this->request->data['b_name'];
        $holder_type_id = $this->request->data['holder_type_id'];
        $holder_name = $this->request->data['holder_name'];
        $holder_info = explode(' - ', $holder_name);
        //pr($holder_info); die;

        $bname = trim($holder_info['2']);
        //pr($bname); die;
        $hjk = $this->Boards->find()->select('id')->where(['Boards.name' => $bname])->first();
        //pr($hjk); die;
        $bhj = $hjk['id'];
        $issue_date = $this->request->data['issue_date'];
        $due_date = $this->request->data['due_date'];
        $from = $this->request->data['from'];
        $to = $this->request->data['to'];
        $lkj = $this->request->data['langu'];
        $clsid = $this->request->data['class_id'];
        $slid = $this->request->data['section_id'];
        $vbn = $this->request->data['type'];
        $status = $this->request->data['status'];
        $userid = $session->read('Auth.User.id');

        if ($status == 'Lost') {

            $detail = "SELECT IssueBooks.asn_no,IssueBooks.holder_name,IssueBooks.holder_id,Students.sms_mobile,BookCopyDetails.status,Students.class_id,Students.section_id,Books.lang,IssueBooks.holder_type_id,IssueBooks.board,IssueBooks.issue_date,IssueBooks.due_date,
				  DATEDIFF(now(), IssueBooks.due_date) as NumberOfDays, BookCopyDetails.book_id, Books.ISBN_NO,Books.name,
				  CASE WHEN Students.mobile IS NOT NULL THEN Students.mobile ELSE Employees.mobile END AS mobile
				  FROM `library_issue_books` IssueBooks

				  LEFT JOIN `library_book_copy_details` BookCopyDetails ON IssueBooks.`asn_no2` = BookCopyDetails.`id`
				  LEFT JOIN `library_books` Books ON Books.`id` = BookCopyDetails.`book_id`
				  LEFT JOIN `library_cup_boards` CupBoard ON CupBoard.`id` = Books.`cup_board_id`
				  LEFT JOIN `students` Students ON (IssueBooks.`holder_id` = Students.`enroll` AND IssueBooks.holder_type_id = 'Student' AND IssueBooks.board = Students.`board_id` )
				  LEFT JOIN `employees` Employees ON IssueBooks.`holder_id` = Employees.`id` AND IssueBooks.holder_type_id = 'Employee' AND Students.status = 'Y' WHERE  IssueBooks.status='N' AND IssueBooks.book_status='2'";
        } else {

            $detail = "SELECT IssueBooks.asn_no,IssueBooks.holder_name,IssueBooks.holder_id,Students.sms_mobile,Students.class_id,Students.section_id,Books.lang,IssueBooks.holder_type_id,IssueBooks.board,IssueBooks.issue_date,IssueBooks.due_date,DATEDIFF(now(), IssueBooks.due_date) as NumberOfDays, BookCopyDetails.book_id, Books.ISBN_NO,Books.name,CASE WHEN Students.mobile IS NOT NULL THEN Students.mobile ELSE Employees.mobile END AS mobile FROM `library_issue_books` IssueBooks LEFT JOIN `library_book_copy_details` BookCopyDetails ON IssueBooks.`asn_no2` = BookCopyDetails.`id` LEFT JOIN `library_books` Books ON Books.`id` = BookCopyDetails.`book_id` LEFT JOIN `library_cup_boards` CupBoard ON CupBoard.`id` = Books.`cup_board_id` LEFT JOIN `students` Students ON (IssueBooks.`holder_id` = Students.`enroll` AND IssueBooks.holder_type_id = 'Student' AND IssueBooks.board = Students.`board_id` )
            LEFT JOIN `employees` Employees ON IssueBooks.`holder_id` = Employees.`id` AND IssueBooks.holder_type_id = 'Employee' AND Students.status = 'Y' WHERE  IssueBooks.status='Y'";
        }

        $datefrom = date('Y-m-d', strtotime($this->request->data['from']));
        $dateto = date('Y-m-d', strtotime($this->request->data['to']));

        $cond = ' ';

        if (!empty($vbn)) {
            $cond .= " AND Books.typ LIKE '" . $vbn . "%' ";
        }
        if (!empty($isbn_no)) {
            $cond .= " AND Books.ISBN_NO LIKE '" . $isbn_no . "%' ";
        }

        if (!empty($lkj)) {
            $cond .= " AND Books.lang = '" . $lkj . "'";
        }

        if (!empty($bhj)) {
            $cond .= " AND IssueBooks.board = '" . $bhj . "'";
        }
        if ($userid == '8') {
            $a = 2;
        }
        if ($userid == '9') {
            $a = 1;
        }
        $a = 1;
        $cond .= " AND CupBoard.roomid = '" . $a . "'";

        if (!empty($clsid)) {
            $cond .= " AND Students.class_id = '" . $clsid . "'";
        }

        if (!empty($slid)) {
            $cond .= " AND Students.section_id = '" . $slid . "'";
        }

        if (!empty($b_name)) {
            $cond .= " AND UPPER(Books.name) LIKE '" . addslashes($b_name) . "%' ";
        }

        if (!empty($holder_type_id)) {
            $cond .= " AND IssueBooks.holder_type_id = '" . $holder_type_id . "' ";
        }

        if (!empty($holder_name)) {

            $cond .= " AND IssueBooks.holder_id ='" . trim($holder_info[0]) . "'";
        }

        if (!empty($status)) {
            if ($status != 'Overdue') {
                $cond .= " AND UPPER(BookCopyDetails.status) = '" . strtoupper($status) . "' ";
            } elseif ($status == 'Overdue') {
                $cond .= " AND IssueBooks.due_date < cast(now() as date) ";

                $this->set('srch_status', $status);
                $session->write('srch_status', $status);
            }
        }

        if (!empty($issue_date) && empty($due_date)) {
            $cond .= " AND IssueBooks.issue_date = '" . date('Y-m-d', strtotime($issue_date)) . "' ";
        }

        if (!empty($datefrom) && $datefrom != '1970-01-01') {
            $cond .= " AND IssueBooks.issue_date >='" . date('Y-m-d', strtotime($datefrom)) . "' ";
        }

        if (!empty($dateto) && $dateto != '1970-01-01') {
            $cond .= " AND IssueBooks.issue_date <='" . date('Y-m-d', strtotime($dateto)) . "' ";
        } elseif (empty($issue_date) && !empty($due_date)) {
            $cond .= " AND IssueBooks.due_date <= '" . date('Y-m-d', strtotime($due_date)) . "' ";
        } elseif (!empty($issue_date) && !empty($due_date)) {
            $cond .= " AND IssueBooks.issue_date >= '" . date('Y-m-d', strtotime($issue_date)) . "' ";
            $cond .= " AND IssueBooks.due_date <= '" . date('Y-m-d', strtotime($due_date)) . "' ";
        }

        // pr($cond);
        $detail = $detail . $cond;
        //    pr($detail);die;

        $SQL = $detail . " ORDER BY IssueBooks.due_date ASC";
        // pr($SQL);die;

        $results = $conn->execute($SQL)->fetchAll('assoc');
        // pr($results);die;

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
            } elseif ($h_type == 'Employee') {
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

    //------------------------------------------------------------
    public function booksReport()
    {
        $this->viewBuilder()->layout('admin');

        $b_category_data = $this->BookCategory->find()->select('name')->where(['BookCategory.status' => 'Y'])->order(['BookCategory.name' => 'Asc'])->toarray();

        foreach ($b_category_data as $value) {
            $element = $value['name'];

            $b_category[$element] = $element;
        }

        $this->set('b_category', $b_category);

        //---------------------------------------------

        $status_data = $this->BookStatus->find()->select('name')->where(['BookStatus.status' => 'Y'])->order(['BookStatus.name' => 'Asc'])->toarray();

        foreach ($status_data as $value) {
            $element = $value['name'];

            $b_status[$element] = $element;
        }

        $this->set('b_status', $b_status);
    }

    //------------------------------------------------------------
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

        $detail = "SELECT Book.ISBN_NO,Book.name as b_name,Book.author, BookCategory.name as b_category, Cupboard.name as cupboard,
					CupboardShelf.name as shelf, BookCopyDetail.id as asn_no, BookCopyDetail.status,
					DATEDIFF(now(), IssueBook.due_date) as NumberOfDays FROM `library_books` Book
					LEFT JOIN `library_book_categories` BookCategory ON Book.`book_category_id` = BookCategory.`id`
					LEFT JOIN `library_cup_boards` Cupboard ON Book.`cup_board_id` = Cupboard.`id`
					LEFT JOIN `library_cup_board_shelves` CupboardShelf ON Book.`cup_board_shelf_id` = CupboardShelf.`id`
					LEFT JOIN `library_book_copy_details` BookCopyDetail ON Book.`id` = BookCopyDetail.`book_id`
					LEFT JOIN `library_issue_books` IssueBook ON BookCopyDetail.id = IssueBook.`asn_no` WHERE  1=1";

        $cond = ' ';
        if (!empty($asn_no)) {
            $cond .= " AND BookCopyDetail.id LIKE '" . $asn_no . "%' ";
        }
        if (!empty($isbn_no)) {
            $cond .= " AND Book.ISBN_NO LIKE '" . $isbn_no . "%' ";
        }

        if (!empty($b_name)) {
            $cond .= " AND UPPER(Book.name) LIKE '" . strtoupper($b_name) . "%' ";
        }

        if (!empty($b_category)) {
            $cond .= " AND UPPER(BookCategory.name) = '" . strtoupper($b_category) . "' ";
        }

        if (!empty($author)) {
            $cond .= " AND UPPER(Book.author) LIKE '" . strtoupper($author) . "%' ";
        }

        if (!empty($status)) {
            if ($status != 'Overdue') {
                $cond .= " AND UPPER(BookCopyDetail.status) = '" . strtoupper($status) . "' ";
            } elseif ($status == 'Overdue') {
                $cond .= " AND IssueBook.due_date < cast(now() as date) ";

                $this->set('srch_status', $status);
                $session->write('srch_status', $status);
            }
        }

        $detail = $detail . $cond;

        if (!empty($status) && $status == 'Overdue') {
            $SQL = $detail . " ORDER BY IssueBook.due_date ASC";
        } else {
            $SQL = $detail . " ORDER BY BookCopyDetail.id ASC";
        }

        $results = $conn->execute($SQL)->fetchAll('assoc');

        $this->set('books', $results);

        $session->delete('results_books');
        $session->write('results_books', $results);
    }

    //---------------------------------------------------------------------
    public function excelExportBooks()
    {
        $this->autoRender = false;

        $session = $this->request->session();
        $books_data = $session->read('results_books');
        $srch_status = $session->read('srch_status');

        $output .= '"ASN No.",';
        $output .= '"ISBN No.",';
        $output .= '"Book Name",';
        $output .= '"Book Category",';
        $output .= '"Cupboard",';
        $output .= '"Cupboard Shelf",';
        $output .= '"Author",';
        $output .= '"Status",';
        $output .= "\n";

        foreach ($books_data as $service) {
            $output .= $service['asn_no'] . ",";
            $output .= $service['ISBN_NO'] . ",";
            $output .= $service['b_name'] . ",";
            $output .= $service['b_category'] . ",";
            $output .= $service['cupboard'] . ",";
            $output .= $service['shelf'] . ",";
            $output .= $service['author'] . ",";

            if ($srch_status == 'Overdue') {
                $output .= "Overdue: " . $service['NumberOfDays'] . " day(s),";
            } else {
                $output .= $service['status'] . ",";
            }

            $output .= "\r\n";
        }

        $filename = "books-report.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo $output;

        exit();
    }

    //----------------------------------------------------------------------------------------
    public function employee_attendance()
    {
        $this->viewBuilder()->layout('admin');

        $departments = $this->Departments->find('list')->where(['Departments.status' => 'Y'])->order(['Departments.name' => 'Asc'])->toarray();
        $designations = $this->Designations->find('list')->where(['Designations.status' => 'Y'])->order(['Designations.name' => 'Asc'])
            ->toarray();

        $month_list = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June',
            '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December',
        ];

        $date = date('Y', strtotime('-5 years'));
        for ($i = 0; $i < 11; $i++) {
            $year_list[$date + $i] = $date + $i;
        }

        $this->set(compact('departments', 'designations', 'month_list', 'year_list'));
    }

    //----------------------------------------------------------------------------------------
    public function employee_attendance_search()
    {
        $session = $this->request->session();

        $req_data = $this->request->data;

        $month = $req_data['month'];
        $year = $req_data['year'];

        $department = $req_data['department'];
        $designation = $req_data['designation'];
        $e_name = $req_data['e_name'];
        $e_id = $req_data['e_id'];

        $cond = [];

        if (!empty($department)) {
            $cond['Departments.id'] = $department;
        }

        if (!empty($designation)) {
            $cond['Employees.designation_id'] = $designation;
        }

        if (!empty($e_name)) {
            $cond['Employees.fname'] = $e_name;
        }

        if (!empty($e_id)) {
            $cond['Employees.id'] = $e_id;
        }

        $cond['MONTH(EmployeeAttendance.date)'] = $month;
        $cond['YEAR(EmployeeAttendance.date)'] = $year;

        $employees = $this->EmployeeAttendance->find()->select([
            'id', 'date',
            'Employees.id', 'Employees.fname', 'Employees.middlename', 'Employees.lname',
        ])->contain(['Employees'])->where($cond)->order(['EmployeeAttendance.id' => 'ASC'])->toArray();

        //----------------------- Processing array for generating Attendance Register: start --------------------------
        $emp_attendance_record = [];

        foreach ($employees as $value) {
            $present_date = date('j', strtotime($value['date']));

            if (array_key_exists($value['employee']['id'], $emp_attendance_record)) {
                $emp_attendance_record[$value['employee']['id']]['present_date'][] = $present_date;
            } else {
                $name = ucfirst($value['employee']['fname']) . " " . ucfirst($value['employee']['middlename']) . " " .
                    ucfirst($value['employee']['lname']);

                // Removing trailing and in between extra spaces
                $name = preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $name);

                $emp_attendance_record[$value['employee']['id']] = [
                    'name' => $name,
                    'present_date' => [$present_date],
                ];
            }
        }
        //----------------------- Processing array for generating Attendance Register: end ----------------------------

        $this->set(compact('emp_attendance_record', 'month', 'year'));

        $session->delete($attendance_month);
        $session->delete($attendance_year);
        $session->delete($emp_attendance_record);

        $session->write('attendance_month', $month);
        $session->write('attendance_year', $year);
        $session->write('emp_attendance_record', $emp_attendance_record);
    }

    //----------------------------------------------------------------------------------------
    public function excel_export_employee_attendance()
    {
        $this->autoRender = false;

        $session = $this->request->session();
        $month = $session->read('attendance_month');
        $year = $session->read('attendance_year');
        $attendance = $session->read('emp_attendance_record');

        $output .= '"Employee Name",';
        //------------------------------------------------------------------
        $no_of_col = 0;

        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);

        $end_time = strtotime("+1 month", $start_time);

        for ($i = $start_time; $i < $end_time; $i += 86400, $no_of_col++) {
            $output .= '"' . date('d D', $i) . '",';
        }

        //------------------------------------------------------------------
        $output .= "\n";

        foreach ($attendance as $service) {
            $output .= $service['name'] . ",";

            for ($i = 1; $i <= $no_of_col; $i++) {
                if (in_array($i, $service['present_date'])) {
                    $output .= '"P",';
                } else {
                    $output .= '"-",';
                }
            }

            $output .= "\r\n";
        }

        $filename = "employee-attendance-report(" . $month . "-" . $year . ").csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo $output;

        exit();
    }

    public function bankreport()
    {
        $this->viewBuilder()->layout('admin');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));
        $this->set(compact('fees'));

        $classfee = $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->where(['Classfee.academic_year' => $academicyear])->order(['Classfee.id' => 'ASC'])->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find()->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find()->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->toarray();
        $this->set('classfee', $classfee);

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1' || $rolepresent == '6' || $rolepresent == '105') {
            $classes = $this->Classes->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('sectionslist', $sectionslist);

        $this->set(compact('sectionslist'));

        $conn = ConnectionManager::get('default');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));

        $acedmicyear = $academicyear;

        /* $detail="SELECT Students.id,Students.fname,Students.fathername,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

        if(!empty($acedmicyear))
        {

        $cond.=" AND Students.acedmicyear LIKE '".$acedmicyear."%' ";

        }

        $quater[]="Admission Fee";
        $quater[]="Development Fee";
        $quater[]="Caution Money";
        $quater[]="Miscellaneous Fee";
        $quater[]="Quater1";
        //$quater[]="Quater2";
        //$quater[]="Quater3";
        //$quater[]="Quater4";

        $this->set('quaters', $quater);
        //    $cond.=" AND Students.section_id LIKE '".$section_id."%' ";

        $results=$this->Students->find('all')->where(['Students.acedmicyear' =>$acedmicyear,'Students.status' =>'Y'])->order(['Students.class_id' => 'ASC'])->limit('500')->toArray();
        $this->set(compact('results'));
        */
    }
    public function bankreportlatest()
    {
        $this->viewBuilder()->layout('admin');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));
        $this->set(compact('fees'));

        $classfee = $this->Classfee->find('all')->contain(['Classes'])->group('Classfee.academic_year')->group('Classfee.class_id')->where(['Classfee.academic_year' => $academicyear])->order(['Classfee.id' => 'ASC'])->select([
            'qu1_fees' => $this->Classfee->find()->func()->sum('Classfee.qu1_fees'), 'qu2_fees' => $this->Classfee->find()->func()->sum('Classfee.qu2_fees'), 'qu3_fees' => $this->Classfee->find()->func()->sum('Classfee.qu3_fees'), 'qu4_fees' => $this->Classfee->find()->func()->sum('Classfee.qu4_fees'), 'Classes.title', 'Classfee.academic_year', 'Classfee.id', 'Classfee.status', 'Classfee.class_id',
        ])->toarray();
        $this->set('classfee', $classfee);

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == '1') {
            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '5') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id' => '1'])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        } elseif ($rolepresent == '8') {

            $classes = $this->Classections->find('list', [
                'keyField' => 'Classes.id',
                'valueField' => 'Classes.title',
            ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.board_id IN' => ['2', '3']])->order(['Classes.sort' => 'ASC'])->toArray();
            $this->set('classes', $classes);
        }

        $sectionslist = $this->Sections->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->where(['status' => '1'])->order(['title' => 'ASC'])->toArray();
        $this->set('sectionslist', $sectionslist);

        $this->set(compact('sectionslist'));

        $conn = ConnectionManager::get('default');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $this->set(compact('academicyear'));

        $acedmicyear = $academicyear;

        /* $detail="SELECT Students.id,Students.fname,Students.fathername,Students.fee_submittedby,Students.sms_mobile,Students.middlename,Students.lname,Students.mobile,Students.acedmicyear,Students.enroll,Students.h_id,Students.class_id,Students.section_id,Students.admissionyear,Students.status,  Classes.title as classtitle , Sections.title as sectiontitle FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";
        $cond = ' ';

            if(!empty($acedmicyear))
            {

            $cond.=" AND Students.acedmicyear LIKE '".$acedmicyear."%' ";

            }

            $quater[]="Admission Fee";
            $quater[]="Development Fee";
            $quater[]="Caution Money";
            $quater[]="Miscellaneous Fee";
            $quater[]="Quater1";
            //$quater[]="Quater2";
            //$quater[]="Quater3";
            //$quater[]="Quater4";

            $this->set('quaters', $quater);
            //    $cond.=" AND Students.section_id LIKE '".$section_id."%' ";

            $results=$this->Students->find('all')->where(['Students.acedmicyear' =>$acedmicyear,'Students.status' =>'Y'])->order(['Students.class_id' => 'ASC'])->limit('500')->toArray();
            $this->set(compact('results'));
            */
    }
    public function findfeeheadsname($id)
    {

        $articles = TableRegistry::get('Feesheads');
        return $articles->find('all')->where(['Feesheads.id' => $id])->first();
    }

    public function classspractical($id = null)
    {

        $articles = TableRegistry::get('Subjects');

        return $articles->find('all')->where(['id' => $id])->select(['id', 'name', 'is_practicalfee'])->where(['is_practicalfee !=' => 0])->first();
    }

    public function bankuser_defaulter()
    {

        $session = $this->request->session();

        $quaters = $session->read('quaters');
        $class_id = $session->read('class_id');
        $section_id = $session->read('section_id');
        $res = $session->read('res');
        $mode = $session->read('mode');

        //$results=$session->read('results');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();

        $academicyear = $users['academic_year'];

        $latefee = $users['latefee'];
        $this->set(compact('academicyear'));

        $class_id = $session->read('class_id');
        $section_id = $session->read('section_id');

        $classes = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $class_id])->order(['id' => 'ASC'])->first();

        $sections = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $section_id])->order(['title' => 'ASC'])->first();
        $classname = $classes['title'];

        if ($sections['title']) {
            $classname .= '(' . $sections['title'] . ')';
        }

        //$this->autoRender = false;

        ini_set('max_execution_time', 1600);

        $headerRow2[] = "S.No";
        $headerRow2[] = "Institute ID";
        $headerRow2[] = "Branch Name";
        $headerRow2[] = "Academic Year";
        $headerRow2[] = "StudentID";
        $headerRow2[] = "Sr.No";
        $headerRow2[] = "Student Name";
        $headerRow2[] = "Email";
        $headerRow2[] = "Class";
        $headerRow2[] = "Section";
        $headerRow2[] = "Father Name";
        $headerRow2[] = "Fee Submitted By";
        $headerRow2[] = "Mobile";
        $headerRow2[] = "MailId";
        $headerRow2[] = "DiscountCategory";
        $headerRow2[] = "Misc. FEE";

        if ($quaters[1]) {
            $quatrr = $quaters[1];
        } else {
            $quatrr = $quaters[0];
        }
        $headerRow2[] = "Actual " . $quatrr . " Tution Fee";
        $headerRow2[] = "(-)Discount";
        $headerRow2[] = "Net Tution Fees";

        $headerRow2[] = "Practical Fee";
        $headerRow2[] = "Previous Due";

        $headerRow2[] = "Start Date";
        $headerRow2[] = "Due Date";
        $headerRow2[] = "Late Fee/day";
        $headerRow2[] = "Expiry Date";

        $this->set('headerRow2', $headerRow2);

        if (isset($res) && !empty($res)) {
            $fees = 0;

            $counter = 1;
            $feestotalqus = 0;
            $feestotalqus2 = 0;
            $feestotalqus3 = 0;
            $feestotalqus4 = 0;
            $perticular_feestotal = 0;
            $total_discount = 0;
            $total_due_amount = 0;
            $total_dues_amount = 0;
            $resultf2 = array();

            foreach ($res as $h => $service) {
                $result = array();
                $student_datas35as = $this->Studentfees->find('all')->where(['Studentfees.student_id' => $service[0], 'Studentfees.type' => 'Fee', 'Studentfees.acedmicyear' => $academicyear, 'Studentfees.status' => 'Y', 'Studentfees.quarter LIKE' => '%' . $quatrr . '%'])->first();
                if ($student_datas35as['id'] == "" && $mode != '1') {

                    $studentfees = $this->finddisountstudent($service[0], $academicyear);
                    $quas = array();
                    $testpass = 0;
                    $quaf = array();
                    $qua = array();
                    $rt = array();
                    foreach ($studentfees as $k => $value) {
                        $quas[] = unserialize($value['quarter']);
                    }

                    foreach ($quas as $h => $vale) {

                        $quaf = array_merge($quaf, $vale);
                    }
                    foreach ($quaf as $j => $t) {

                        $qua[] = $j;
                    }

                    if ($quaters[0] == 'Quater4') {

                        if (in_array('Quater3', $qua)) {

                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[0] == 'Quater3') {

                        if (in_array('Quater2', $qua)) {
                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[0] == 'Quater2') {
                        if (in_array('Quater1', $qua)) {
                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[1] == 'Quater1') {

                        $testpass = 1;
                    }
                    if ($testpass != '0') {

                        $fedd = 0;

                        $fetchdetail = '--';

                        //$fetchdetail = $this->defaultersearchbyidhistoryssd($service[0],'2018-19');
                        if ($fetchdetail == '--') {

                            $findstud = $this->Students->find('all')->where(['Students.id' => $service[0], 'Students.status' => 'Y'])->first();
                            $result[] = $counter;
                            $result[] = "SANSKAR";
                            $result[] = "JAIPUR";
                            $result[] = $findstud['acedmicyear'];
                            if ($findstud['board_id'] == '1') {
                                $result[] = 'C' . $service[1];
                            } else {
                                $result[] = 'IC' . $service[1];
                            }
                            $result[] = $service[1];
                            $result[] = $findstud['fname'] . " " . $findstud['middlename'] . " " . $findstud['lname'];

                            $result[] = $findstud['username'];

                            $s_id = $findstud['class_id'];
                            $c_id = $findstud['section_id'];

                            $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                            $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                            $result[] = $classssr['title'];
                            $result[] = $sectionssss['title'];
                            $result[] = $findstud['fathername'];
                            $result[] = $findstud['fee_submittedby'];
                            $result[] = $findstud['sms_mobile'];
                            $result[] = "-";
                            if ($findstud['discountcategory'] != '') {
                                $result[] = trim($findstud['discountcategory']);
                            } else {
                                $result[] = "-";
                            }

                            $result[] = $service[7];
                            $result[] = $service[8];

                            if ($quaters[1]) {
                                $result[] = $service[14];
                                $result[] = $service[15];
                                $result[] = $service[12];
                            } else {
                                $result[] = $service[11];
                                $result[] = $service[12];
                                $result[] = $service[9];
                            }

                            $findpending = $this->findpendingssinglefee($service[0], $academicyear);
                            if ($findpending[0]['sum']) {
                                $result[] = $findpending[0]['sum'];
                            } else {
                                $result[] = "--";
                            }

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '01-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '01-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '01-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '01-01-' . date('Y', strtotime('+1 years'));
                            }

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '10-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '10-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '10-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                            }

                            $result[] = $latefee;

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '10-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '10-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '10-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                            }

                            $counter++;
                            $resultf2[] = $result;
                            //  $output .= implode("\t", $result) . "\n";

                        }
                    }
                } elseif ($mode == '1') {

                    $studentfees = $this->finddisountstudent($service[0], $academicyear);
                    $quas = array();
                    $testpass = 0;
                    $quaf = array();
                    $qua = array();
                    $rt = array();
                    foreach ($studentfees as $k => $value) {
                        $quas[] = unserialize($value['quarter']);
                    }

                    foreach ($quas as $h => $vale) {

                        $quaf = array_merge($quaf, $vale);
                    }
                    foreach ($quaf as $j => $t) {

                        $qua[] = $j;
                    }

                    if ($quaters[0] == 'Quater4') {

                        if (in_array('Quater3', $qua)) {

                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[0] == 'Quater3') {

                        if (in_array('Quater2', $qua)) {
                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[0] == 'Quater2') {
                        if (in_array('Quater1', $qua)) {
                            $testpass = 1;
                        } else {
                            $testpass = 0;
                        }
                    } elseif ($quaters[1] == 'Quater1') {

                        $testpass = 1;
                    }
                    if ($testpass != '0') {

                        $fedd = 0;

                        $fetchdetail = '--';

                        //$fetchdetail = $this->defaultersearchbyidhistoryssd($service[0],'2018-19');
                        if ($fetchdetail == '--') {

                            $findstud = $this->Students->find('all')->where(['Students.id' => $service[0], 'Students.status' => 'Y'])->first();
                            $result[] = $counter;
                            $result[] = "SANSKAR";
                            $result[] = "JAIPUR";
                            $result[] = $findstud['acedmicyear'];
                            if ($findstud['board_id'] == '1') {
                                $result[] = 'C' . $service[1];
                            } else {
                                $result[] = 'IC' . $service[1];
                            }
                            $result[] = $service[1];
                            $result[] = $findstud['fname'] . " " . $findstud['middlename'] . " " . $findstud['lname'];

                            $result[] = $findstud['username'];

                            $s_id = $findstud['class_id'];
                            $c_id = $findstud['section_id'];

                            $classssr = $this->Classes->find('all')->select(['id', 'title'])->where(['id' => $s_id])->order(['id' => 'ASC'])->first();

                            $sectionssss = $this->Sections->find('all')->select(['id', 'title'])->where(['id' => $c_id])->order(['title' => 'ASC'])->first();

                            $result[] = $classssr['title'];
                            $result[] = $sectionssss['title'];
                            $result[] = $findstud['fathername'];
                            $result[] = $findstud['fee_submittedby'];
                            $result[] = $findstud['sms_mobile'];
                            $result[] = "-";
                            if ($findstud['discountcategory'] != '') {
                                $result[] = trim($findstud['discountcategory']);
                            } else {
                                $result[] = "-";
                            }

                            $result[] = $service[7];
                            $result[] = $service[8];

                            if ($quaters[1]) {
                                $result[] = $service[14];
                                $result[] = $service[15];
                                $result[] = $service[12];
                            } else {
                                $result[] = $service[11];
                                $result[] = $service[12];
                                $result[] = $service[9];
                            }

                            $findpending = $this->findpendingssinglefee($service[0], $academicyear);
                            if ($findpending[0]['sum']) {
                                $result[] = $findpending[0]['sum'];
                            } else {
                                $result[] = "--";
                            }

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '01-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '01-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '01-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '01-01-' . date('Y', strtotime('+1 years'));
                            }

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '10-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '10-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '10-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                            }

                            $result[] = $latefee;

                            if (in_array("Quater1", $quaters)) {
                                $result[] = '10-04-' . date('Y');
                            } elseif (in_array("Quater2", $quaters)) {
                                $result[] = '10-07-' . date('Y');
                            } elseif (in_array("Quater3", $quaters)) {
                                $result[] = '10-10-' . date('Y');
                            } elseif (in_array("Quater4", $quaters)) {
                                $result[] = '10-01-' . date('Y', strtotime('+1 years'));
                            }

                            $counter++;
                            $resultf2[] = $result;
                            //  $output .= implode("\t", $result) . "\n";

                        }
                    }
                }
            }
        }
        $this->set('result', $resultf2);
    }
    public function optsubjectedit()
    {
        $this->loadModel('Classections');
        $this->viewBuilder()->layout('admin');
        $id = array('12', '13', '15', '17', '20', '22');

        $classes = $this->Classections->find('list', [
            'keyField' => 'Classes.id',
            'valueField' => 'Classes.title',
        ])->contain(['Classes'])->where(['Classes.status' => '1', 'Classes.id IN' => $id])->order(['Classes.sort' => 'ASC'])->toArray();
        $this->set('classes', $classes);
    }
    public function optionalsubjectsedit()
    {

        //pr($this->request->data);
        //die;
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $this->loadModel('Students');
        $this->loadModel('Subjectclass');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];
        $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.class_id' => $class_id, 'Students.status' => 'Y', 'Students.section_id' => $section_id, 'Students.acedmicyear' => $acedmic])->order(['Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();
        //pr($student_data); die;
        $this->set('students', $student_data);
        $opt_sub = $this->Subjectclass->find('list', ['keyField' => 'Subjects.id', 'valueField' => 'Subjects.name'])->contain(['Subjects'])->where(['Subjectclass.class_id' => $class_id, 'Subjectclass.is_optional' => '1'])->order(['Subjects.name' => 'ASC'])->toarray();

        //pr($opt_sub); die;
        $this->set('opt_sub', $opt_sub);
    }

    public function optionalsubjectslistsearch()
    {

        //pr($this->request->data);
        //die;
        $class_id = $this->request->data['class_id'];
        $section_id = $this->request->data['section_id'];
        $opt_subject = $this->request->data['opt_sub'];
        $this->loadModel('Students');
        $this->loadModel('Subjectclass');
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $acedmic = $users['academic_year'];

        if ($opt_subject) {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.class_id' => $class_id, 'Students.status' => 'Y', 'Students.section_id' => $section_id, 'FIND_IN_SET(\'' . $opt_subject . '\',Students.opt_sid)', 'Students.acedmicyear' => $acedmic])->order(['Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();

            $this->set('opt_subject', $opt_subject);
        } else {
            $student_data = $this->Students->find('all')->contain(['Classes', 'Sections'])->where(['Students.class_id' => $class_id, 'Students.status' => 'Y', 'Students.section_id' => $section_id, 'Students.acedmicyear' => $acedmic])->order(['Students.fname' => 'ASC', 'Students.middlename' => 'ASC', 'Students.lname' => 'ASC'])->toarray();
        }

        //pr($student_data); die;
        $this->set('students', $student_data);
        $opt_sub = $this->Subjectclass->find('list', ['keyField' => 'Subjects.id', 'valueField' => 'Subjects.name'])->contain(['Subjects'])->where(['Subjectclass.class_id' => $class_id, 'Subjectclass.is_optional' => '1'])->order(['Subjects.name' => 'ASC'])->toarray();

        //pr($opt_sub); die;
        $this->set('opt_sub', $opt_sub);
    }
    public function savesubjsanskar()
    {
        $this->autoRender = false;
        $this->loadModel('Students');

        $comp_id = $this->request->data['comp_id'];

        foreach ($comp_id as $key => $value) {

            $student = $this->Students->get($key);
            $comp_sid = '';
            $s_ids = array();
            foreach ($value as $s_id) {

                if (isset($s_id) && !empty($s_id)) {
                    $s_ids[] = $s_id;
                }
            }
            $comp_sid = implode(',', $s_ids);

            $opt_id = $this->request->data['opt_id'][$key];
            $opt_sid = '';
            if ((isset($opt_id[0]) && !empty($opt_id[0])) && (isset($opt_id[1]) && !empty($opt_id[1]))) {
                $opt_sid = implode(',', $this->request->data['opt_id'][$key]);
            } else {
                if (isset($opt_id[0]) && !empty($opt_id[0])) {
                    $opt_sid = $opt_id[0];
                } elseif (isset($opt_id[1]) && !empty($opt_id[1])) {
                    $opt_sid = $opt_id[1];
                }
            }
            //pr($opt_sid);
            // pr($comp_sid);
            $this->request->data['comp_sid'] = $comp_sid;
            $this->request->data['opt_sid'] = $opt_sid;
            $entity = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($entity)) {
                $this->redirect(array('action' => 'optsubjectedit'));
            }
        }
    }


    public function savesubj()
    {
        $this->autoRender = false;
        $this->loadModel('Students');

        $comp_id = $this->request->data['comp_id'];

        foreach ($comp_id as $key => $value) {

            $student = $this->Students->get($key);
            $comp_sid = '';
            $s_ids = array();
            foreach ($value as $s_id) {

                if (isset($s_id) && !empty($s_id)) {
                    $s_ids[] = $s_id;
                }
            }
            $comp_sid = implode(',', $s_ids);

            $opt_id = $this->request->data['opt_id'][$key];
            $opt_sid = '';
            if ((isset($opt_id[0]) && !empty($opt_id[0])) && (isset($opt_id[1]) && !empty($opt_id[1]))) {

                if (is_array($this->request->data['opt_id'][$key])) {

                    $opt_sid = implode(',', array_filter($this->request->data['opt_id'][$key]));
                }
            } else {
                if (isset($opt_id[0]) && !empty($opt_id[0])) {
                    $opt_sid = $opt_id[0];
                } elseif (isset($opt_id[1]) && !empty($opt_id[1])) {
                    $opt_sid = $opt_id[1];
                }
            }

            $this->request->data['comp_sid'] = $comp_sid;
            $this->request->data['opt_sid'] = $opt_sid;
            $entity = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($entity)) {
                $this->redirect(array('action' => 'optsubjectedit'));
            }
        }
    }
    public function otherfeereport()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Otherfees');
        $this->loadModel('Feesheads');
        $this->loadModel('Classes');
        $class_id = $this->Classes->find('list', ['keyField' => 'id', 'valueField' => 'title'])->order(['sort' => 'ASC'])->toarray();
        $this->set(compact('class_id'));
        $feesheadstotal = $this->Feesheads->find('list', [
            'keyField' => 'name',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'type IN' => ['3', '0'], 'sort !=' => '0'])->order(['name' => 'ASC'])->toArray();
        $this->set('feesheadstotal', $feesheadstotal);
        $acadmicyr2 = $this->Otherfees->find()->select(['academicyear'])->group(['academicyear'])->order(['academicyear' => 'DESC'])->toarray();
        //pr($acadmicyr2);die;
        $enquires['acedmicyear'] = $acadmicyr2[0]['academicyear'];
        //pr($enquires); die;
        $this->set(compact('enquires'));
        $acadmicyr = array();
        foreach ($acadmicyr2 as $value) {
            $acadmicyr[$value['academicyear']] = $value['academicyear'];
        }
        $this->set(compact('acadmicyr'));

        $user = $this->Users->find('all')->where(['Users.role_id' => '1'])->first();
        $rolepresentyear = $user['academic_year'];
        $acd = $this->academicyear();

        $this->set(compact('acd'));

        $this->set(compact('rolepresentyear'));

        //pr($acadmicyr); die;
        $detail = $this->Otherfees->find('all')->where(['status' => 'Y'])->toarray();
        //pr($detail);die;
        $acadyr = $this->Otherfees->find('list', ['keyField' => 'academicyear', 'valueField' => 'academicyear'])->group(['academicyear'])->toarray();
        $this->set(compact('acadyr'));

        $this->set(compact('detail'));
    }
    public function otherfeeselection()
    {
        $this->loadModel('Otherfees');
        if ($this->request->is('post')) {
            //pr($this->request->data);die;
            $acdyr = $this->request->data['acedmicyear'];
            $from = $this->request->data['from'];
            $to = $this->request->data['to'];
            $mode = $this->request->data['mode'];
            $class_id = $this->request->data['class_id'];
            $title = $this->request->data['selectField'];

            $con = array();
            $con['status'] = 'Y';
            if (isset($acdyr) && !empty($acdyr)) {
                $con['academicyear'] = $acdyr;
            }
            if (isset($from) && !empty($from)) {
                $con['paydate >='] = date('Y-m-d', strtotime($from));
            }
            if (isset($to) && !empty($to)) {
                $con['paydate <='] = date('Y-m-d', strtotime($to));
            }
            if (isset($mode) && !empty($mode)) {

                $con['mode IN'] = $mode;
            }
            if (isset($title) && !empty($title)) {

                $con['title IN'] = $title;
            }
            if (isset($class_id) && !empty($class_id)) {

                $con['class_id'] = $class_id;
            }
            //pr($con);die;
            $res = $this->Otherfees->find('all')->where($con)->toarray();
            $this->set(compact('res'));

            $restitle = $this->Otherfees->find('all')->select(['title'])->where($con)->group(['title'])->toarray();

            $this->set(compact('restitle'));
            //  pr($restitle);die;
            $this->request->session()->delete('ofexcel');
            $this->request->session()->write('ofexcel', $res);
            $this->request->session()->delete('tilofexcel');
            $this->request->session()->write('tilofexcel', $restitle);
        }
    }
    public function otherfeesexcel()
    {
        $this->viewBuilder()->layout('admin');
        $res = $this->request->session()->read('ofexcel');
        $this->request->session()->delete('ofeexcel');
        $restitle = $this->request->session()->read('tilofexcel');

        $this->set(compact('res'));
        $this->set(compact('restitle'));
    }
    public function officecollection_excel($academic = null)
    {
        $datefrom = $this->request->session()->read('fee_excel_from');
        $dateto2 = $this->request->session()->read('fee_excel_to');
        $this->set(compact('academic'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
    }
    public function drop_feeacknowledgement($id = null)
    {
        $this->loadModel('DropOutStudent');
        $student = $this->DropOutStudent->find('all')->where(['DropOutStudent.id' => $id])->first();
        $this->sitesetting('general');
        $this->set(compact('student'));
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
    }
    public function employee_monthly_attn_report()
    {
        $this->viewBuilder()->Layout('admin');
        $this->loadModel('PayrollDepartments');
        $this->loadModel('Leavetype');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        // pr($dept);die;
        $Leaves = $this->Leavetype->find('all')->select(['id'])->where(['status' => 'Y'])->toarray();
        $leave_type = $this->Leavetype->find('list', ['keyField' => 'id', 'valueField' => 'short_name'])->select(['id'])->where(['status' => 'Y'])->toarray();
        // pr($leave_type);die;

        foreach ($Leaves as $leave) {
            $leave_id[] = $leave['id'];
        }
        $acd = $this->currentacademicyear();
        $this->set(compact('dept', 'leave_id', 'leave_type', 'acd'));
        if ($this->request->is('post')) {
            $date = $this->request->data['date'];
            $this->set(compact('date'));
        }
    }

    public function monthly_staff_report()
    {

        $this->viewBuilder()->Layout('admin');
        $this->loadModel('Staffattends');
        $this->loadModel('PayrollDepartments');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        // pr($dept);die;
        $acd = $this->currentacademicyear();
        $this->set(compact('dept', 'acd'));
        if ($this->request->is('post')) {
            $date = $this->request->data['date'];
            $this->set(compact('date'));
        }
    }
    public function employee_monthly_search_report()
    {
        $this->loadModel('Employees');
        $this->loadModel('PayrollDepartments');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        $this->set(compact('dept'));
        $date = "05-2019";
        $this->set(compact('date'));
        if ($this->request->is('post')) {
            $date = $this->request->data['date'];
            $this->set(compact('date'));
        }
    }
    public function update_row()
    {
        $this->loadModel('Leavetype');
        $id = $this->request->data['id'];
        $date = $this->request->data['date'];
        $sno = $this->request->data['sno'];
        $Leaves = $this->Leavetype->find('all')->select(['id'])->where(['status' => 'Y'])->toarray();
        foreach ($Leaves as $leave) {
            $leave_id[] = $leave['id'];
        }
        $this->set(compact('id', 'date', 'sno', 'leave_id'));
    }
    public function employee_monthly_report_excel($date)
    {
        $this->loadModel('PayrollDepartments');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        // pr($dept);die;
        $this->set(compact('dept'));
        // $this->loadModel('Employees');
        // $emp = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.fname' => 'ASC'])->toarray();
        // $this->set(compact('emp'));
        $this->set(compact('date'));
    }

    public function employee_monthly_report($date)
    {
        $this->loadModel('PayrollDepartments');

        $data = explode("-", $date);
        $total_days = cal_days_in_month(CAL_GREGORIAN, $data[0], $data[1]);

        $this->loadModel('PayrollDepartments');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        $this->set(compact('dept'));
        $this->set(compact('date', 'total_days'));
        // $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        // pr($dept);die;
        // $this->set(compact('dept'));
        // $this->loadModel('Employees');
        // $emp = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.fname' => 'ASC'])->toarray();
        // $this->set(compact('emp'));
        // $this->set(compact('date'));
    }

    public function employee_monthly_reports_excel($date = null)
    {
        $this->loadModel('PayrollDepartments');
        $dept = $this->PayrollDepartments->find('all')->order(['name' => 'ASC'])->toarray();
        // pr($dept);die;
        $this->set(compact('dept'));
        // $this->loadModel('Employees');
        $emp = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.fname' => 'ASC'])->toarray();
        // pr($emp);die;
        $this->set(compact('emp'));
        $this->set(compact('date'));
    }
    public function mismatchreport()
    {
        $this->viewBuilder()->layout('admin');
    }
    public function mismatchReportSearch()
    {
        if ($this->request->is('post')) {
            $date = $_POST['date'];
            //pr($date);; die;
            $res = $this->Studattends->find('all')->contain(['Students'])->where(['Studattends.status' => 'A', 'Studattends.status_m' => 'P', 'date' => $date])->order(['Studattends.class_id' => 'DESC', 'Studattends.section_id' => 'DESC'])->toarray();
            $this->set(compact('res'));
            $this->request->session()->delete('mismatch_excel');
            $this->request->session()->write('mismatch_excel', $res);
        }
    }
    public function mismatch_excel()
    {
        $res = $this->request->session()->read('mismatch_excel');
        $this->set(compact('res'));
        // pr($res); die;

    }
    public function salartyreport()
    {
        $this->viewBuilder()->Layout('admin');
    }
    public function advance_salary_report()
    {
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '16') {
            $this->viewBuilder()->Layout('admin');
            $this->loadModel('Employees');
            $staff = $this->Employees->find('list', ['keyField' => 'id', 'valueField' => array('fname', 'middlename', 'lname', 'id')])->order(['fname' => 'ASC'])->toarray();
            $this->set(compact('staff'));
            $this->loadModel('Ledger');
            $pay_mode = $this->Ledger->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['gid IN' => ['3', '4']])->toarray();
            $this->set(compact('pay_mode'));
        } else {
            $this->Auth->logout();
            return $this->redirect(['controller' => 'logins', 'action' => 'index']);
        }
    }
    public function advance_report_search()
    {
        $this->loadModel('Advancesalary');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '16') {
            if ($this->request->is('post')) {
                //pr($this->request->data);die;
                $empId = $this->request->data['employee_id'];
                $from = $this->request->data['from'];
                $to = $this->request->data['to'];
                $mode = $this->request->data['pay_mode'];
                $con = array();
                $con1 = array();

                if (isset($empId) && !empty($empId)) {
                    $con['employee_id'] = $empId;
                    $con1['employee_id'] = $empId;
                }
                if (isset($from) && !empty($from)) {
                    $con['paydate >='] = date('Y-m-d', strtotime($from));
                    $con1['deposit_date >='] = date('Y-m-d', strtotime($from));
                }
                if (isset($to) && !empty($to)) {
                    $con['paydate <='] = date('Y-m-d', strtotime($to));
                    $con1['deposit_date <='] = date('Y-m-d', strtotime($to));
                }
                if (isset($mode) && !empty($mode)) {

                    $con['pay_mode'] = $mode;
                    $con1['pay_mode'] = $mode;
                }

                //pr($con);die;
                $advance_det = $this->Advancesalary->find('all')->where($con)->orWhere($con1)->toarray();
                $this->set(compact('advance_det'));
                //pr($res);die;
                $this->request->session()->delete('adv_pdf');
                $this->request->session()->write('adv_pdf', $advance_det);
            }
        } else {
            $this->Auth->logout();
            return $this->redirect(['controller' => 'logins', 'action' => 'index']);
        }
    }
    public function advance_pdf()
    {
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == '16') {

            $pdfreport = $this->request->session()->read('adv_pdf');
            $this->set(compact('pdfreport'));
        } else {
            $this->Auth->logout();
            return $this->redirect(['controller' => 'logins', 'action' => 'index']);
        }
    }
    public function po_pdf()
    {
    }
    public function holiday_allowance_report()
    {
        $this->viewBuilder()->Layout('admin');
    }
    public function holiday_allowance_search()
    {
        if ($this->request->is('post')) {
            // pr($_POST);die;
            $date = explode('/', $this->request->data['fdate']);
            $mon = $date[0];
            $year = $date[1];
            $this->loadModel('Holidayallowance');
            $res = $this->Holidayallowance->find('all')->where(['MONTH(date)' => $mon, 'YEAR(date)' => $year])->toarray();
            $this->set(compact('res'));
            //pr($res);die;
        }
    }
    public function leave_report()
    {
        $this->viewBuilder()->Layout('admin');
        $this->loadModel('Leaves');
        $employees = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['fname' => 'ASC'])->toarray();
        $this->set('employees', $employees);

        $date = date("Y-m-d");
        $emp_leave = $this->Leaves->find('all')->contain(['Employees'])->where(['date' => $date])->toarray();
        $this->set(compact('emp_leave'));
    }
    public function employeesattnreport()
    {
        $this->autoRender = false;
        return $this->redirect(['action' => 'employee_monthly_attn_report']);
    }
    public function leave_report_search()
    {
        $this->loadModel('Leaves');
        if ($this->request->is('post')) {

            $employee_id = $this->request->data['employee_id'];
            $from_date = $this->request->data['from'];
            $to_date = $this->request->data['to'];
            //pr($from_date);die;

            $Leaves = $this->Leaves->find('all')->contain(['Employees'])->where(['Leaves.date >=' => $from_date, 'Leaves.date <=' => $to_date])->toarray();

            //pr($Leaves);die;
            $this->set(compact('Leaves'));
        }
    }
    public function monthlysummary()
    {

        $this->viewBuilder()->Layout('admin');
    }
    public function monthlysummarydetail()
    {

        if ($this->request->is('post')) {
            extract($this->request->data);
            $datefrom = "1-" . str_replace('/', '-', $datefrom);
            $dateto = "1-" . str_replace('/', '-', $dateto);
            $from = date('Y-m-d', strtotime($datefrom));
            $to = date('Y-m-t', strtotime($dateto));
            $articles = TableRegistry::get('Studentfees');
            while (strtotime($from) <= strtotime($to)) {
                $sum['cbse'] = 0;
                $sum['int'] = 0;
                $actcbse_fee = "";
                $drpcbse_fees = "";
                $actint_fees = "";
                $drpint_fees = "";
                $mon = date('m', strtotime($from));
                $year = date('Y', strtotime($from));
                $actcbse_fees = $articles->find('all')->select(['cbse_fees' => $articles->find()->func()->sum('Studentfees.deposite_amt')])->contain(['Students'])->where(['MONTH(Studentfees.paydate)' => $mon, 'YEAR(Studentfees.paydate)' => $year, 'Studentfees.status' => 'Y', 'Studentfees.recipetno !=' => '0', 'Students.board_id' => 1, 'Studentfees.type !=' => 'Other'])->first();
                //  pr($actcbse_fees); die;
                $drpcbse_fees = $articles->find('all')->select(['cbse_fees' => $articles->find()->func()->sum('Studentfees.deposite_amt')])->contain(['DropOutStudent'])->where(['MONTH(Studentfees.paydate)' => $mon, 'YEAR(Studentfees.paydate)' => $year, 'Studentfees.status' => 'Y', 'Studentfees.recipetno !=' => '0', 'DropOutStudent.board_id' => 1, 'Studentfees.type !=' => 'Other'])->first();

                $actint_fees = $articles->find('all')->select(['int_fees' => $articles->find()->func()->sum('Studentfees.deposite_amt')])->contain(['Students'])->where(['MONTH(Studentfees.paydate)' => $mon, 'YEAR(Studentfees.paydate)' => $year, 'Studentfees.status' => 'Y', 'Studentfees.recipetno !=' => '0', 'Students.board_id !=' => 1, 'Studentfees.type !=' => 'Other'])->first();

                $drpint_fees = $articles->find('all')->select(['int_fees' => $articles->find()->func()->sum('Studentfees.deposite_amt')])->contain(['DropOutStudent'])->where(['MONTH(Studentfees.paydate)' => $mon, 'YEAR(Studentfees.paydate)' => $year, 'Studentfees.status' => 'Y', 'Studentfees.recipetno !=' => '0', 'DropOutStudent.board_id !=' => 1, 'Studentfees.type !=' => 'Other'])->first();

                $sum['cbse'] = $actcbse_fees['cbse_fees'] + $drpcbse_fees['cbse_fees'];
                $sum['int'] = $actint_fees['int_fees'] + $drpint_fees['int_fees'];
                $total[$from] = $sum;
                $from = date('Y-m-d', strtotime($from . '+1 months'));
            }
            $this->set(compact('total', 'datefrom', 'to'));
            $this->request->session()->write('total_mon', $total);
        }
    }
    public function monthlysum_pdf()
    {
        $total = $this->request->session()->read('total_mon');
        $this->set(compact('total'));
        $this->sitesetting('general');
        $this->response->type('pdf');
    }
    public function feedbackreport()
    {
        $this->loadModel('FeedBacks');
        $feedback = $this->FeedBacks->find('all')->contain(['FeedbackCat'])->order(['FeedBacks.id' => 'DESC'])->toarray();
        $this->set(compact(['feedback']));
    }
    public function shufflingReport()
    {
        $this->viewBuilder()->layout('false');
        $this->loadModel('StudentShuffles');
        $shuffles = $this->StudentShuffles->find()->contain(['StudentOne', 'StudentTwo'])->order(['StudentShuffles.id' => 'ASC'])->toarray();
        $this->set(compact('shuffles'));
    }
    public function tcReport()
    {
        $droppedStudents = $this->DropOutStudent->find()->where(['status_tc' => 'Y'])->order(['id' => 'ASC'])->toarray();
        $classes = $this->Classes->find('list')->toarray();
        $sections = $this->Sections->find('list')->toarray();
        $subjects = $this->ExamSubjects->find('list')->toarray();
        $students = [];
        foreach ($droppedStudents as $student) {
            $data = [];
            $data['bookNo'] = !empty($student['bookno']) ? $student['bookno'] : '-';
            $data['enroll'] = $student['enroll'];
            $data['name'] = $student['full_name'];
            $data['fatherName'] = $student['fathername'];
            $data['motherName'] = $student['mothername'];
            $data['lastStudiedClass'] = $classes[$student['laststudclass']];
            $data['applicationDate'] = date('d-m-Y', strtotime($student['date_application']));
            $data['issueDate'] = date('d-m-Y', strtotime($student['date_issue']));
            $data['academicYear'] = $student['acedmicyear'];
            $data['qualifiedToPromote'] = $student['qualified_to_promote'];
            $data['promotedToClass'] = $classes[$student['promoted_to_class_id']];
            $data['NCCcadet'] = $student['NCC_cadet'];
            $data['extraCurricularActivities'] = $student['extra_curricular_activities'];
            $data['achievementLevel'] = $student['achievement_level'];
            $data['generalConduct'] = $student['general_conduct'];
            $data['leavingReason'] = $student['leaving_reason'];
            $data['charcterCertificate'] = $student['charctercertificate'];
            $data['remarks'] = $student['remarks'];
            $data['schoolLastresult'] = $student['school_lastresult'];
            $data['workingDays'] = $student['workingdays'];
            $data['presentDays'] = $student['presentdays'];
            $data['struckOffDate'] = date('d-m-Y', strtotime($student['struck_off_date']));
            $data['subjects'] = "-";
            $tcSubjects = explode(',', $student['tcsubject']);
            if (!empty($tcSubjects)) {
                $sub = [];
                foreach ($tcSubjects as $tcSubject) {
                    $sub[] = $subjects[$tcSubject];
                }
                $data['subjects'] = implode(',', $sub);
            }

            $data['fee_submittedby'] = $student['fee_submittedby'];
            $data['board_id'] = $student['board_id'];
            $data['sms_mobile'] = $student['sms_mobile'];
            $data['gender'] = $student['gender'];
            $data['admissionyear'] = $student['admissionyear'];
            $admissionclassclassewrw = $this->showadmissionclasstitle($student['admissionclass']);
            $data['admissionclass'] = $admissionclassclassewrw['title'];
            $data['admissiondate'] = date('d-m-Y', strtotime($student['admissiondate']));

            $students[] = $data;
        }
        $this->set(compact('students'));
    }
    public function restoreReport()
    {
        $this->loadModel('StudentRestores');
        $restores = $this->StudentRestores->find()->contain(['Students', 'DropOutStudents', 'Users'])->order(['StudentRestores.id' => 'DESC'])->toarray();
        $this->set(compact('restores'));
    }
}
