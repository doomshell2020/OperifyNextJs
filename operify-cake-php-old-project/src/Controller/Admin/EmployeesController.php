<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class EmployeesController extends AppController
{

    public $helpers = ['CakeJs.Js'];
    //initialize component
    public function initialize()
    {

        parent::initialize();
        $this->loadModel('Employees');
        $this->loadModel('Departments');
        $this->loadModel('Designations');
        $this->loadModel('Guardians');
        $this->loadModel('Documentcategory');
        $this->loadModel('Documents');
        $this->loadModel('Country');
        $this->loadModel('States');
        $this->loadModel('Cities');
        $this->loadModel('Address');
        $this->loadModel('Otherinfos');
        $this->loadModel('Users');
        $this->loadModel('Salary');
        $this->loadModel('PayrollDepartments');
        $this->loadModel('PayrollDesignations');
        $this->loadModel('Ledger');
        $this->loadModel('Payroll');
        $this->loadModel('DropOutEmployee');

        require_once 'Firebase.php';
        require_once 'Push.php';
    }
    //-------------------------------------------------------------------------//
    public function finddesignation()
    {
        $id = $this->request->data['id'];
        $des = $this->Designations->find('all')->where(['depart_id' => $id])->toarray();

        echo "<option>Select Designation</option>";
        foreach ($des as $bj) {
            echo "<option value=" . $bj['id'] . ">" . $bj['name'] . "</option>";
        }
        die;
    }

    //-------------------------------------------------------------------------//
    public function add_excel()
    {
        $this->loadModel('Employeesalary');
        $this->loadModel('Employees');
        $this->loadModel('Users');
        if ($this->request->is(['post'])) {
            try {
                if ($this->request->data['file']['tmp_name']) {

                    $empexcel = $this->request->data['file'];
                    $excel_array = $this->get_excel_data($empexcel['tmp_name']);

                    if ($excel_array == "null") {
                        $this->Flash->error(__('Please Fill Mandatory Fields'));
                        $this->set('error', $error);
                        return $this->redirect(['action' => 'addcsv']);
                    }

                    if (!empty($excel_array['message'])) {
                        $this->Flash->error(__($excel_array['message']));
                        $this->set('error', $error);
                        return $this->redirect(['action' => 'addcsv']);
                    }

                    foreach ($excel_array as $refer_data) {
                        $refer_data['joiningdate'] = date('Y-m-d', strtotime($refer_data['joiningdate']));
                        $refer_data['dob'] = date('Y-m-d', strtotime($refer_data['dob']));
                        if (strtolower($refer_data['employee_id']) == "new") {
                            //   echo "ok"; die;
                            $emp = $this->Employees->newEntity();
                            $emp = $this->Employees->patchEntity($emp, $refer_data);
                            $emp = $this->Employees->save($emp);
                            $user = $this->Users->newEntity();
                            $id = $emp->id;
                            $username = $emp->email;
                            $password = $emp->password;
                            $mobile = $emp->mobile;
                            $this->request->data['email'] = $username;
                            $this->request->data['academic_year'] = '2021-22';
                            $this->request->data['mobile'] = $mobile;
                            $this->request->data['password'] = $this->_setPassword(12345);
                            $this->request->data['confirm_pass'] = '12345';
                            $this->request->data['role_id'] = '3';
                            $this->request->data['tech_id'] = $id;
                            $this->request->data['user_name'] = $this->request->data['fname'];
                            $this->request->data['fkey'] = "NULL";
                            $this->request->data['latefee'] = "NULL";
                            $this->request->data['attendenceupdate'] = "NULL";
                            $user = $this->Users->patchEntity($user, $this->request->data);
                            $this->Users->save($user);
                            $employee = $this->Employeesalary->newEntity();
                            $refer_data['employee_id'] = $emp->id;
                            echo date('m', strtotime($refer_data['joiningdate']));
                            $refer_data['joiningdate'] = date('Y-m-d', strtotime($refer_data['joiningdate']));
                        }
                        $employee = $this->Employeesalary->patchEntity($employee, $refer_data);
                        $employee = $this->Employeesalary->save($employee);
                    }
                    $this->Flash->success(__('Employees list  has been saved.'));
                    return $this->redirect(['controller' => 'payroll', 'action' => 'emplist']);
                }
                $this->Flash->error(__('Employeesalary  has not been saved.'));
                return $this->redirect(['controller' => 'payroll', 'action' => 'emplist']);
            } catch (\PDOException $e) {
                pr($e);
                die;
                $this->Flash->error(__('Employeesalary updation Failed' . $error));
                $this->set('error', $error);
                return $this->redirect(['action' => 'addcsv']);
            }
        }
    }

    //-------------------------------------------------------------------------//
    public function get_excel_data($inputfilename)
    {

        if ($_POST) {

            try {
                $objPHPExcel = \PHPExcel_IOFactory::load($inputfilename);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputfilename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestDataRow();
            $highestColumn = $sheet->getHighestDataColumn();
            $c = 0;

            $firstrow = 1;
            $firstsop = $sheet->rangeToArray('A' . $firstrow . ':' . $highestColumn . $firstrow, null, true, false);
            $dept = array_map('strtolower', $this->PayrollDepartments->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])->toarray());
            //-------------------------------------------------------------------------//
            function isEmptyRow($row)
            {
                foreach ($row as $cell) {
                    if (null !== $cell) {
                        return false;
                    }
                }
                return true;
            }

            for ($row = 2; $row <= $highestRow; $row++) {
                $exceldata = array();
                $exceldata['basic_salary'] = '0';
                $filesop = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                if (isEmptyRow(reset($filesop))) {
                    continue;
                }
                $colB = $objPHPExcel->getActiveSheet()->getCell('A' . $row)->getValue();
                if ($colB == null || $colB == '') {
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $objPHPExcel->getActiveSheet()->getCell('A' . ($row - 1))->getValue());
                }
                if (strtolower($filesop[0][0]) == "sample") {
                    continue;
                }
                $exceldata['employee_id'] = $filesop[0][1];
                $name = explode(" ", $filesop[0][2]);
                if (count($name) == 3) {
                    $exceldata['fname'] = $name[0];
                    $exceldata['middlename'] = $name[1];
                    $exceldata['lname'] = $name[2];
                } else {
                    $exceldata['fname'] = $name[0];
                    $exceldata['lname'] = $name[1];
                }
                $exceldata['f_h_name'] = $filesop[0][3];
                echo $filesop[0][9];
                $exceldata['dob'] = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($filesop[0][4]));
                $filesop[0][5];
                $filesop[0][6];
                $exceldata['p_department'] = array_search(strtolower($filesop[0][5]), $dept);
                $desg = array_map('strtolower', $this->PayrollDesignations->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name',
                ])->toarray());
                $exceldata['p_designation'] = array_search(strtolower($filesop[0][6]), $desg);
                $exceldata['mobile'] = $filesop[0][7];
                $exceldata['emp_status'] = strtolower($filesop[0][8]);
                $exceldata['joiningdate'] = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($filesop[0][9]));
                // pr($exceldata['joiningdate']); die;
                $exceldata['fsalary'] = $filesop[0][10];
                $exceldata['total'] = $filesop[0][19];
                $exceldata['basic_salary'] = $filesop[0][11];
                $exceldata['da_amt'] = $filesop[0][12];
                $exceldata['hra_amt'] = $filesop[0][13];
                $exceldata['HRA'] = 1;
                $exceldata['LTA'] = 1;
                $exceldata['leve'] = 1;
                $exceldata['cca_amt'] = $filesop[0][14];
                $exceldata['spl_all'] = $filesop[0][15];
                $exceldata['lta_amt'] = $filesop[0][16];
                $exceldata['other_all'] = $filesop[0][17];
                $exceldata['grade_pay'] = $filesop[0][18];
                $exceldata['total'] = $filesop[0][19];
                $subjeledger = $this->Ledger->find('all')->where(['lower(name)' => strtolower($filesop[0][20])])->first();
                $exceldata['payment_mode'] = $subjeledger['id'];
                $exceldata['bank_account_no'] = (int) $filesop[0][21];
                $exceldata['bank_name'] = $filesop[0][22];

                $empsalary = $this->Payroll->find('all')->where(['id' => 1])->first();

                $exceldata['bank_ifsc'] = $filesop[0][23];
                $exceldata['uan_no'] = $filesop[0][24];
                $exceldata['esi_no'] = $filesop[0][25];
                if ($filesop[0][26] == '1') {
                    $exceldata['PF'] = 1;
                } else {
                    $exceldata['PF'] = 0;
                }
                if ($filesop[0][26] == '1') {
                    if ($exceldata['basic_salary'] <= $empsalary['pfabpamt']) {
                        $exceldata['pfdeduction'] = round(($exceldata['basic_salary'] + $exceldata['da_amt'] + $exceldata['grade_pay']) * $empsalary['employeesharepf'] / 100, 0);
                    } else {
                        $exceldata['pfdeduction'] = 1800;
                    }
                } else {
                    $exceldata['pfdeduction'] = 0;
                }
                if ($filesop[0][27] == '1') {
                    $exceldata['ESI_choice'] = 1;
                } else {
                    $exceldata['ESI_choice'] = 0;
                }
                if ($filesop[0][27] == '1') {
                    if ($filesop[0][19] <= $empsalary['esiamtap']) {
                        $exceldata['esideduction'] = round($filesop[0][16] * $empsalary['employeeshareeesi'] / 100, 0);
                    } else {
                        $exceldata['esideduction'] = '0';
                    }
                } else {
                    $exceldata['esideduction'] = '0';
                }
                if ($filesop[0][28] == '1') {
                    $exceldata['sd'] = '0';
                    $exceldata['sd_perc'] = $filesop[0][29];
                } else {
                    $exceldata['sd'] = '0';
                    $exceldata['sd_perc'] = '0';
                }
                $sum = 0;
                $sum = $exceldata['pfdeduction'] + $exceldata['esideduction'];
                $exceldata['netpay'] = $filesop[0][19] - $sum;
                if (empty($exceldata['p_department'])) {
                    $ret['message'] = $filesop[0][6] . " doesnot exist";
                    return $ret;
                }
                if (empty($exceldata['p_designation'])) {
                    $ret['message'] = $filesop[0][6] . " doesnot exist";
                    return $ret;
                }
                $csv_data[] = $exceldata;
            }
            // pr($csv_data);die;
            return $csv_data;
        }
    }

    //-------------------------------------------------------------------------//
    public function classsection_excel()
    {
        $this->autoRender = false;
        $this->loadModel('Sections');
        $this->loadModel('Classections');
        $this->loadModel('Employees');
        $this->loadModel('Classes');
        $this->loadModel('Timetables');

        ini_set('max_execution_time', 1600);
        $headerRow = array("S.No.", "Firstname", "Middlename", "Lname", "Email", "Mobile", "JoiningDate", "Slab", "Dob", "Father/HusbandName");
        $output = implode("\t", $headerRow) . "\n";
        $t_enquiry = $this->Employees->find('all')->order(['Employees.fname' => 'ASC'])->toarray();
        $s = '1';
        $tid = '';
        foreach ($t_enquiry as $people) {
            $result = array();
            $str = "";
            $result[] = $s++;
            $result[] = $people['fname'];
            $result[] = $people['middlename'];
            $result[] = $people['lname'];
            $result[] = $people['username'];
            $result[] = $people['mobile'];
            $cid = array();
            $result[] = $people['joiningdate'];
            $result[] = $people['slab_type'];
            $result[] = $people['dob'];
            $result[] = $people['f_h_name'];
            $output .= implode("\t", $result) . "\n";
        }
        $filename = "AllTeachers_" . date('d-m-Y') . ".xls";
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        die;
    }

    //-------------------------------------------------------------------------//
    public function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
    //-------------------------------------------------------------------------//
    public function dup_mobile()
    {
        $mobile = $this->request->data['mobile'];
        echo $Employees = $this->Users->find('all')->where(['Users.mobile' => $mobile])->count();
        die;
    }

    //-------------------------------------------------------------------------//
    public function edit_dup_mobile()
    {
        $mobile = $this->request->data['mobile'];
        $e_id = $this->request->data['e_id'];
        echo $Employees = $this->Employees->find('all')->where(['Employees.mobile' => $mobile, 'Employees.id' => $e_id])->count();
        die;
    }

    //-------------------------------------------------------------------------//
    public function find_email($username = null)
    {

        $username = $this->request->data['username'];
        $students = $this->Employees->find('all')->where(['Employees.email' => $username])->toArray();
        echo $students[0]['id'];
        die;
    }

    //-------------------------------------------------------------------------//
    public function pdf_view($schedule_id = null)
    {
        $this->viewBuilder()->layout('ajax');
        $this->response->type('pdf');
        $employees = $this->Employees->find()->where(['Employees.id' => $schedule_id])->contain(['Departments', 'Designations'])->first()->toarray();
        $this->set(compact('employees'));
        $classessss = $this->Guardians->find()->where(['Guardians.user_id' => $schedule_id])->first();
        $this->set(compact('classessss'));
        $address = $this->Address->find('all')->contain(['CurCountry', 'PerCountry', 'CurStates', 'PerStates', 'CurStates', 'PerStates', 'CurCity', 'PerCity'])->where(['Address.type' => 1, 'Address.user_id' => $schedule_id])->first();
        $this->set(compact('address'));
        $otherinfo = $this->Otherinfos->find()->where(['Otherinfos.user_id' => $schedule_id])->first();
        $this->set(compact('otherinfo'));
    }
    //----------------------------------------------------------------------------//
    public function search()
    {
        //connection
        $conn = ConnectionManager::get('default');
        $department_id = $this->request->data['department_id'];
        $designation_id = $this->request->data['designation_id'];
        $name = $this->request->data['name'];
        $mobile = $this->request->data['mobile'];
        $detail = "SELECT Employees.id,Employees.fname,Employees.username,Employees.lname,Employees.mobile,Employees.f_h_name,Employees.joiningdate,Employees.email,Employees.designation_id,Employees.dob,Employees.status, Departments.name as departmentname , Designations.name as designationname FROM `employees` Employees LEFT JOIN departments Departments ON Employees.`department_id` = Departments.id LEFT JOIN designations Designations ON Employees.`designation_id` = Designations.id WHERE 1=1 ";
        $cond = ' ';
        if (!empty($name)) {
            $cond .= " AND Employees.fname LIKE '" . $name . "%' || Employees.email LIKE '" . $name . "%' || Employees.lname LIKE '" . $name . "%'";
        }
        if (!empty($mobile)) {
            $cond .= " AND Employees.mobile LIKE '" . $mobile . "%' ";
        }
        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY Employees.id ASC";
        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->set('students', $results);
    }
    //-------------------------------------------------------------------//
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Employees');
        $this->loadModel('Employeeattendance');
        $date = date('Y-m-d');
        if (!empty($eid)) {
            $classes_data3 = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id IN' => $eid])->order(['Employees.fname' => 'ASC'])->toarray();
            $classes_data = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id NOT IN' => $eid])->order(['Employees.fname' => 'ASC'])->toarray();
            $students = array_merge($classes_data3, $classes_data);
        } else {
            $students = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.fname' => 'ASC'])->toarray();
        }
        $this->set('students', $students);
    }
    //------------------------------------------------------------------------------//
    public function employee_excel()
    {
        //   $this->autoRender = false;
        $this->loadModel('Employeesalary');
        $this->loadModel('Employees');

        $employee = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.id' => 'ASC'])->toarray();
        $this->set('employee', $employee);
    }

    //--------------------------------------------------------------------------------------------\\
    public function add($id = null)
    {

        $this->loadModel('Designations');
        $this->loadModel('Departments');
        $this->loadModel('Employeesalary');
        $role_id = $this->request->session()->read('Auth.User.role_id');
        $this->set(compact('role_id'));
        $db = $this->request->session()->read('Auth.User.db');
        $academic_year = $this->request->session()->read('Auth.User.academic_year');
        $this->set(compact('db'));
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Ledger');
        // $mode = $this->Ledger->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['gid' => 3])->toarray();
        $this->set(compact('mode'));
        $depa = $this->Departments->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toarray();
        $desi = $this->Designations->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toarray();
        $this->set('depa', $depa);
        $this->set('desi', $desi);
        if (isset($id) && !empty($id)) {
            //using for edit
            $classes = $this->Employees->get($id);
            $mobile = $classes['mobile'];
            $user = $this->Users->newEntity();
        } else {
            //using for new entry
            $classes = $this->Employees->newEntity();
            $user = $this->Users->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            if (isset($id) && !empty($id)) {
                $update = 1;
            } else {
                $this->request->data['update_emp'] = 1;
            }
            $this->request->data['slab_type'] = implode(',', $this->request->data['slab_type']);
            $this->request->data['fname'] = ucfirst(strtolower($this->request->data['fname']));
            $this->request->data['middlename'] = ucfirst(strtolower($this->request->data['middlename']));
            $this->request->data['lname'] = ucfirst(strtolower($this->request->data['lname']));
            $this->request->data['dob'] = date('Y-m-d', strtotime($this->request->data['dob']));
            $this->request->data['joiningdate'] = date('Y-m-d', strtotime($this->request->data['joiningdate']));
            $this->request->data['username'] = $this->request->data['email'];
            $this->request->data['f_h_name'] = $this->request->data['f_h_name'];
            $this->request->data['basic_salary'] = $this->request->data['basic_salary'];
            $this->request->data['department_id'] = $this->request->data['p_department'];
            $this->request->data['designation_id'] = $this->request->data['p_designation'];
            $this->request->data['title'] = $this->request->data['title'];
            $this->request->data['blood_group'] = $this->request->data['blood_group'];
            $db = $this->request->session()->read('Auth.User.db');

            $tmp_name = $this->request->data['file']['tmp_name'];
            $image_name = $this->request->data['file']['name'];
            $pext = pathinfo($image_name, PATHINFO_EXTENSION);
            $imagenewname = md5(time($filename)) . '.' . $pext;
            $dest = WWW_ROOT . $db . '_image' . '/' . "employees/";
            $newfile = $dest . $imagenewname;
            if (move_uploaded_file($tmp_name, $newfile)) {
                $this->request->data['file'] = $imagenewname;
            }
            $classes = $this->Employees->patchEntity($classes, $this->request->data);

            //This data for School_ERP Users Table
            $first_name = $this->request->data['fname'] . $this->request->data['middlename'] . $this->request->data['lname'];
            $p_designation = $this->request->data['p_designation'];
            $emp_email = $this->request->data['email'];
            $emp_mobile = $this->request->data['mobile'];
            $emp_mobile = $mobile;
            // pr($emp_mobile); die;
            $emp_db = $this->request->data['db'];
            $conn = ConnectionManager::get('default');
            $check = "SELECT * FROM store_erp.users where `mobile`='$emp_mobile' and `db`='$db'";
            $conn->execute($check)->fetchAll('assoc');
            // pr($check);die;
            if ($result = $this->Employees->save($classes)) {
                $id = $result->id;
                $username = $result->email;
                $password = $result->password;
                $mobile = $result->mobile;

                if ($update != 1) {
                    if ($this->request->data['p_designation'] == 21) {
                        $roles = $this->request->data['p_designation'];
                    } else {
                        $roles = 3;
                    }
                    // pr($roles);die;
                    $this->request->data['email'] = $username;
                    $this->request->data['c_id'] = '1';
                    $this->request->data['board'] = '1';
                    $this->request->data['academic_year'] = $academic_year;
                    $this->request->data['mobile'] = $mobile;
                    $this->request->data['password'] = $this->_setPassword(12345);
                    $this->request->data['confirm_pass'] = '12345';
                    $this->request->data['role_id'] = $roles;
                    $this->request->data['tech_id'] = $id;
                    $this->request->data['user_name'] = $this->request->data['fname'];
                    $this->request->data['fkey'] = "NULL";
                    $this->request->data['latefee'] = "NULL";
                    $this->request->data['attendenceupdate'] = "NULL";
                    $user = $this->Users->patchEntity($user, $this->request->data);

                    $this->Users->save($user);

                    //Insert Data in School_ERP Users Table
                    $fnames = $this->request->data['fname'];
                    $academic_year = $academic_year;
                    $database_name = $this->request->data['db'];
                    if ($this->request->data['p_designation'] == 21) {
                        $roles = $this->request->data['p_designation'];
                    } else {
                        $roles = 3;
                    }
                    $cdate = date('Y-m-d H:i:s');
                    $Bord = 1;
                    $conn = ConnectionManager::get('default');
                    $inserts = "INSERT INTO `store_erp`.`users` (`user_name`,`c_id`,`academic_year`, `email`,`tech_id`, `password`, `confirm_pass`, `created`,  `role_id`, `db`,`board`,`mobile`) VALUES
                      ('$fnames','0','$academic_year','$username', '$id','$2y$10$.vwir1Fyl5EkpHbNfEYFl.vU7raO0gFMuM.QnwVqpLM77Mjlot1Zm','12345', '$cdate',$roles,'$database_name','$Bord','$mobile')";
                    $conn->execute($inserts);
                    $this->Flash->success(__('Employee Added Successfully'));
                    return $this->redirect(['controller' => 'employees', 'action' => 'index']);
                }
            } else {
                //validation error
                if ($classes->errors()) {
                    $error_msg = [];
                    foreach ($classes->errors() as $errors) {
                        if (is_array($errors)) {
                            foreach ($errors as $error) {
                                $error_msg[] = $error;
                            }
                        } else {
                            $error_msg[] = $errors;
                        }
                    }
                    if (!empty($error_msg)) {
                        $this->Flash->error(
                            __("Please fix the following error(s): " . implode("\n \r", $error_msg))
                        );
                    }
                }
            }
        }
        $this->set('classes', $classes);
    }

    public function sort()
    {
        $this->viewBuilder()->layout('admin');
        $id = $this->request->data['id'];
        if (isset($id) && !empty($id)) {
            //using for edit
            $classes = $this->Employees->get($id);
        } else {
            //using for new entry
            $classes = $this->Employees->newEntity();
        }

        if ($this->request->is(['post', 'put'])) {
            //$this->request->data = $this->request->data['sort'];
            $classes->sort = $this->request->data['sort'];
            if ($this->Employees->save($classes)) {
                echo $classes['sort'];
            } else {
                echo 'wrong';
            }
        }
        die;
    }
    public function relieving_certificate_pdf($id = null)
    {
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $session = $users['academic_year'];
        $this->set(compact('session'));
        $student = $this->DropOutEmployee->find('all')->where(['DropOutEmployee.id' => $id])
            ->first();
        $this->set(compact('student'));
    }
    public function nodues_pdf($id = null)
    {
        $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
        $session = $users['academic_year'];
        $this->set(compact('session'));
        $student = $this->Employees->find('all')->where(['Employees.id' => $id])->first();
        $this->set(compact('student'));
    }
    public function relieving_certificate_info($id = null)
    {
        if (!empty($id)) {
            $student = $this->DropOutEmployee->get($id);
            $this->set(compact('student'));
        }
        if ($this->request->is(['post', 'put'])) {
            $req_data = $this->request->data;
            $req_data['date_application'] = date('Y-m-d', strtotime($req_data['date_application']));
            $req_data['relevingdate'] = date('Y-m-d', strtotime($req_data['relevingdate']));
            $req_data['date_issue'] = date('Y-m-d');

            $student = $this->DropOutEmployee->patchEntity($student, $req_data);

            if ($this->DropOutEmployee->save($student)) {
                return $this->redirect(['action' => 'relieving_certificate_pdf/' . $id]);
            } else {
                //check validation error
                if ($student->errors()) {
                    $error_msg = [];

                    foreach ($student->errors() as $errors) {
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
    }
    public function drop_out_employee_search()
    {
        $conn = ConnectionManager::get('default');
        $department_id = $this->request->data['department_id'];
        $designation_id = $this->request->data['designation_id'];
        $name = explode(' ', $this->request->data['name']);
        $mobile = $this->request->data['mobile'];
        $detail = "SELECT DropOutEmployee.id,DropOutEmployee.fname,DropOutEmployee.middlename,DropOutEmployee.lname,DropOutEmployee.nodues,DropOutEmployee.username,DropOutEmployee.lname,DropOutEmployee.mobile,DropOutEmployee.f_h_name,DropOutEmployee.joiningdate,DropOutEmployee.email,DropOutEmployee.designation_id,DropOutEmployee.dob,DropOutEmployee.status, Departments.name as departmentname , Designations.name as designationname FROM `drop_out_employees` DropOutEmployee LEFT JOIN departments Departments ON DropOutEmployee.`department_id` = Departments.id LEFT JOIN designations Designations ON DropOutEmployee.`designation_id` = Designations.id WHERE 1=1 ";
        $cond = ' ';
        if (!empty($department_id)) {
            $cond .= " AND DropOutEmployee.department_id = '" . $department_id . "'";
        }
        if (!empty($designation_id)) {
            $cond .= " AND DropOutEmployee.designation_id = '" . $designation_id . "'";
        }
        if (!empty($name[0])) {
            $cond .= " AND DropOutEmployee.fname LIKE '" . $name[0] . "'";
        }
        if (!empty($name[1])) {
            $cond .= " AND (DropOutEmployee.middlename LIKE '" . $name[1] . "' OR DropOutEmployee.lname LIKE '" . $name[1] . "')";
        }
        if (!empty($mobile)) {
            $cond .= " AND DropOutEmployee.mobile LIKE '" . $mobile . "' ";
        }
        $detail = $detail . $cond;
        $SQL = $detail . " ORDER BY DropOutEmployee.id ASC";

        $results = $conn->execute($SQL)->fetchAll('assoc');
        $this->set('students', $results);
    }

    //----------------------------------------------------------------------///
    public function dropsubmit($id = null)
    {


        $this->loadModel('Classections');

        $emp_count = $this->Classections->find('all')->where(['FIND_IN_SET(\'' . $id . '\',Classections.teacher_id)'])->count();
        if ($emp_count > 0) {
            $this->Flash->error('This Employee Cant be Dropout as it is already used in the system');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post')) {
            $s_id = $this->request->data['empId'];
            $employee = $this->Employees->get($s_id);
            try {
                $conn = ConnectionManager::get('default');
                $detail = 'DELETE FROM `users` WHERE `users`.`tech_id` = ' . $s_id;
                $results = $conn->execute($detail);
                $data['status'] = 'N';
                $data['is_drop'] = 'Y';
                $data['drop_date'] = date('Y-m-d', strtotime($this->request->data['drop_date']));
                $drop_out_student = $this->Employees->patchEntity($employee, $data);
                $this->Employees->save($drop_out_student);
                $this->Flash->success(__('Employee Dropped Successfully'));

                echo "hello";
                die;
            } catch (\PDOException $ex) {
                if ($ex->getCode() == 23000) {
                    $this->Flash->error(__("Please clear dues before dropping the student with id: " . $s_id . '.'));
                } else {
                    $this->Flash->error(__($ex->getMessage()));
                }
                echo "hello";
                die;
            }
        } else {
            $this->Flash->error(__("kindly Try Again For DropOut !!!"));
            echo "hello";
            die;
        }
    }

    //------------------------------------------------------------------------------//
    public function restoreemployee($id = null)
    {

        if ($id) {
            $s_id = $id;
            $employee = $this->Employees->get($s_id)->toArray();
            try {
                $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
                $conn = ConnectionManager::get('default');
                $name = $employee['fname'] . " " . $employee['middlename'] . " " . $employee['lname'];
                $detail = 'INSERT INTO `users`(`user_name`, `academic_year`, `email`, `tech_id`, `password`, `confirm_pass`, `mobile`, `role_id`) VALUE("' . $name . '","' . $users['academic_year'] . '","' . $employee['username'] . '","' . $employee['id'] . '","$2y$10$SyTJj3yWREeMDftQwWlXQeV8GlYQx4PqZfWoOh5isB6VxSO7QaICS","12345","' . $employee['mobile'] . '",3)';
                $results = $conn->execute($detail);
                $conns = ConnectionManager::get('default');
                $details = 'INSERT INTO `school_erp`.`users`(`user_name`, `academic_year`, `email`, `tech_id`, `password`, `confirm_pass`, `mobile`, `role_id`) VALUE("' . $name . '","' . $users['academic_year'] . '","' . $employee['username'] . '","' . $employee['id'] . '","$2y$10$SyTJj3yWREeMDftQwWlXQeV8GlYQx4PqZfWoOh5isB6VxSO7QaICS","12345","' . $employee['mobile'] . '",3)';
                $resultss = $conns->execute($details);
                $employ = $this->Employees->get($s_id);
                $dataemployee['status'] = 'Y';
                $dataemployee['drop_date'] = '';
                $dataemployee['is_drop'] = 'N';
                $drop_out_student = $this->Employees->patchEntity($employ, $dataemployee);
                if ($this->Employees->save($drop_out_student)) :
                endif;
                $this->Flash->success(__('Employee has been restored successfully !!'));
                return $this->redirect(['action' => 'drop']);
            } catch (\PDOException $ex) {
                if ($ex->getCode() == 23000) {
                    $this->Flash->error(__('.'));
                    return $this->redirect(['action' => 'drop']);
                } else {
                    $this->Flash->error(__($ex->getMessage()));
                    return $this->redirect(['action' => 'drop']);
                }
            }
        } else {
            $this->Flash->error(__("kindly Try Again For restored DropOut Employee!!!"));
            return $this->redirect(['action' => 'drop']);
        }
    }
    public function restore($id = null)
    {
        if ($id) {
            $s_id = $id;
            $employee = $this->DropOutEmployee->get($s_id)->toArray();
            try {
                $users = $this->Users->find('all')->where(['Users.id' => '1'])->first();
                // pr($users); die;
                $conn = ConnectionManager::get('default');
                $name = $employee['fname'] . " " . $employee['middlename'] . " " . $employee['lname'];
                $detail = 'INSERT INTO `users`(`user_name`, `academic_year`, `email`, `tech_id`, `password`, `confirm_pass`, `mobile`, `role_id`) VALUE("' . $name . '","' . $users['academic_year'] . '","' . $employee['username'] . '","' . $employee['id'] . '","$2y$10$SyTJj3yWREeMDftQwWlXQeV8GlYQx4PqZfWoOh5isB6VxSO7QaICS","12345","' . $employee['mobile'] . '",3)';
                $results = $conn->execute($detail);

                $drop_out_student = $this->Employees->newEntity();
                if ($employee['experience'] != "") {
                    $employee['experience'] = $employee['experience'];
                } else {
                    $employee['experience'] = '0';
                }
                $drop_out_student = $this->Employees->patchEntity($drop_out_student, $employee);

                if ($this->Employees->save($drop_out_student)) :
                    $this->DropOutEmployee->delete($this->DropOutEmployee->get($s_id));
                endif;

                $this->Flash->success(__('Employee has been restored successfully !!'));
                return $this->redirect(['action' => 'drop']);
            } catch (\PDOException $ex) {
                if ($ex->getCode() == 23000) {
                    $this->Flash->error(__('.'));
                    return $this->redirect(['action' => 'drop']);
                } else {
                    $this->Flash->error(__($ex->getMessage()));
                    return $this->redirect(['action' => 'drop']);
                }
            }
        } else {
            $this->Flash->error(__("kindly Try Again For restored DropOut Employee!!!"));
            return $this->redirect(['action' => 'drop']);
        }
    }
    ///-----------------------------------------------------------------------//
    public function drop()
    {
        $this->viewBuilder()->layout('admin');
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        $Departments = $this->PayrollDepartments->find('list')->where(['PayrollDepartments.status' => 'Y'])->order(['PayrollDepartments.name' => 'Asc'])->toarray();
        $this->set('Departments', $Departments);
        $Designations = $this->PayrollDesignations->find('list')->where(['PayrollDesignations.status' => 'Y'])->order(['PayrollDesignations.name' => 'Asc'])->toarray();
        $this->set('Designations', $Designations);
        $students = $this->Employees->find('all')->where(['Employees.is_drop' => 'Y', 'Employees.status' => 'N'])->order(['drop_date' => 'DESC'])->toarray();
        $this->set(compact('students'));
        $this->request->session()->delete('conditionlps');
        $this->request->session()->write('conditionlps', $students);
    }

    //----------------------------------------------------------------------------//
    //view functionality
    public function view($id = null)
    {
        $this->viewBuilder()->layout('admin');
        //pr($this->Auth->user());die;
        $role_id = $this->Auth->user('role_id');
        $this->set(compact('role_id'));

        if ($_GET['id']) {

            $this->set('selectid', $_GET['id']);
        }
        //  echo $id;
        $employees = $this->Employees->find()->where(['Employees.id' => $id])->contain(['Employeesalary', 'PayrollDepartments', 'PayrollDesignations'])->first();
        //pr($employees);die;
        $this->set(compact('employees'));
        $this->set('ids', $id);
        if (!empty($id)) {
            $classessss = $this->Guardians->find()->where(['Guardians.user_id' => $id])->first();
            //pr($classessss); die;
            $this->set(compact('classessss'));
        }
        $students = $this->Otherinfos->find()->where(['Otherinfos.user_id' => $id])->first();
        //pr($classessss); die;
        $this->set(compact('students'));
        $doc_img = $this->Documents->find('all')->where(['Documents.type' => 1, 'Documents.user_id' => $id])->contain(['Documentcategory'])->toarray();
        $this->set(compact('doc_img'));

        $address = $this->Address->find('all')->contain(['CurCountry', 'PerCountry', 'CurStates', 'PerStates', 'CurStates', 'PerStates', 'CurCity', 'PerCity'])->where(['Address.type' => 1, 'Address.user_id' => $id])->first();
        $this->set(compact('address'));
        $this->loadModel('Employeesalary');
        $salary = $this->Employeesalary->find('all')->where(['employee_id' => $id])->first();
        $this->set(compact('salary'));
        //pr($id);die;
        $this->loadModel('Leaves');
        $leaves = $this->Leaves->find('all')->where(['emp_id' => $id])->toarray();
        $this->set(compact('leaves'));
        $this->loadModel('Advancesalary');
        $advance_det = $this->Advancesalary->find('all')->where(['employee_id' => $id])->toarray();
        $this->set(compact('advance_det'));
        $salary_det = $this->Salary->find('all')->where(['Eid' => $id, 'status' => 'Y'])->order(['id' => 'DESC'])->toarray();
        $this->set(compact('salary_det'));
    }

    //-----------------------------------------------------------------------------------//
    //delete functionality
    public function delete($id)
    {
        //$this->request->allowMethod(['post', 'delete']);
        $this->loadModel('Employeesalary');
        $this->loadModel('Classections');
        $this->loadModel('Employees');

        $emp_count = $this->Classections->find('all')->where(['FIND_IN_SET(\'' . $id . '\',Classections.teacher_id)'])->count();
        if ($emp_count > 0) {
            $this->Flash->error('This Employee Cant be Deleted as it is already used in the system');
            return $this->redirect(['action' => 'index']);
        }
        $classes = $this->Employees->get($id);
        //delete pariticular entry
        try {
            $classes['status'] = 'N';
            if ($this->Employees->save($classes)) {

                $classectsion = $this->Classections->find('all')->where(['FIND_IN_SET(\'' . $id . '\',Classections.teacher_id)'])->toarray();
                foreach ($classectsion as $k => $y) {

                    $teacher_id = explode(',', $y['teacher_id']);

                    if ($findid = array_search($id, $teacher_id)) {

                        unset($teacher_id[$findid]);
                    }

                    $teachers_id = implode(',', $teacher_id);

                    $conn = ConnectionManager::get('default');
                    $details = 'UPDATE `classections` SET `teacher_id` ="' . $teachers_id . '" WHERE `classections`.`id` = ' . $y['id'];
                    $conn->execute($details);
                }
                $conns = ConnectionManager::get('default');
                $detailss = 'DELETE FROM `classtime_tabs`  WHERE `classtime_tabs`.`employee_id` = ' . $id;
                $conns->execute($detailss);

                $rty = $this->Employeesalary->find('all')->select(['update_emp'])->where(['employee_id' => $id])->first();
                //pr($rty); die;
                $ad = $rty['update_emp'] + 1;
                $conn = ConnectionManager::get('default');
                $detail = 'UPDATE `employee_salary_setting` SET `update_emp` ="' . $ad . '" WHERE `employee_salary_setting`.`employee_id` = ' . $id;
                $results = $conn->execute($detail);

                $this->Flash->success(__('Employee deleted Successfully.'));

                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {
            //  $error = 'The item you are trying to delete is associated with other records';
            $this->Flash->error(__('You can not delete this record because it is used in another table.'));
            $this->set('error', $error);
            //$this->Session->setFlash(__(' Lader all ready exists), 'flash/Error');
            return $this->redirect(['action' => 'index']);
        }
    }

    //----------------------------------------------//
    public function delete_guardian($id = null)
    {
        $id = $_GET['did'];
        $classes = $this->Guardians->get($id);
        $students = $this->Guardians->find()->where(['Guardians.id' => $id])->first()->toarray();
        $t = $students['user_id'];
        //delete pariticular entry
        if ($this->Guardians->delete($classes)) {
            $this->Flash->success(__('Employee with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'view/' . $t]);
        }
    }

    //------------------------------------------------------------//
    //status update functionality
    public function status($id, $status)
    {

        $statusquery = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->count();
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                //status update
                $classes = $this->Employees->get($id);
                $classes->status = $status;
                if ($this->Employees->save($classes)) {
                    $this->Flash->success(__('Employee status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                if ($statusquery) {
                    $status = 1;
                    //status update
                    $classes = $this->Employees->get($id);
                    $classes->status = $status;
                    if ($this->Employees->save($classes)) {
                        $this->Flash->success(__('Employee status has been updated.'));
                        return $this->redirect(['action' => 'index']);
                    }
                    $this->viewBuilder()->layout('admin');
                    $Departments = $this->Departments->find('list')->where(['Departments.status' => 'Y'])->order(['Departments.name' => 'Asc'])->toarray();
                    $this->set('Departments', $Departments);
                    $Designations = $this->Designations->find('list')->where(['Designations.status' => 'Y'])->order(['Designations.name' => 'Asc'])->toarray();
                    $this->set('Designations', $Designations);
                    if (isset($id) && !empty($id)) {
                        //using for edit
                        $classes = $this->Employees->get($id);
                    } else {
                        //using for new entry
                        $classes = $this->Employees->newEntity();
                        $statusquery = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->count();
                        if ($statusquery < 8) {
                            $this->request->data['status'] = 'Y';
                        } else {
                            $this->request->data['status'] = 'N';
                        }
                    }

                    if ($this->request->is(['post', 'put'])) {
                        // save all data in database
                        $classes = $this->Employees->patchEntity($classes, $this->request->data);
                        if ($this->Employees->save($classes)) {
                            $this->Flash->success(__('Employee has been saved.'));
                            return $this->redirect(['action' => 'index']);
                        } else {
                            //validation error
                            if ($classes->errors()) {
                                $error_msg = [];
                                foreach ($classes->errors() as $errors) {
                                    if (is_array($errors)) {
                                        foreach ($errors as $error) {
                                            $error_msg[] = $error;
                                        }
                                    } else {
                                        $error_msg[] = $errors;
                                    }
                                }
                                if (!empty($error_msg)) {
                                    $this->Flash->error(
                                        __("Please fix the following error(s): " . implode("\n \r", $error_msg))
                                    );
                                }
                            }
                        }
                    }
                    $this->set('classes', $classes);
                } else {
                    $this->Flash->error(__('8 Entries all ready activate. Please deactivate one of activate'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    //-----------------------------------------------------------//
    public function addguardian($id = null)
    {

        if (isset($_GET['id'])) {
            $this->set('ids', $_GET['id']);
        }
        if (isset($id) && !empty($id)) {
            $classes = $this->Guardians->get($id);
            $this->set('ids', $classes->user_id);
        } else {
            $classes = $this->Guardians->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            if (!empty($this->request->data['hide'])) {
                $this->request->data['user_id'] = $this->request->data['hide'];
            } else {
                $students = $this->Guardians->find()->where(['Guardians.id' => $id])->first()->toarray();
                $this->request->data['user_id'] = $students['user_id'];
            }
            $this->request->data['emails'] = $this->request->data['email'];
            $this->request->data['type'] = '0';
            $classes = $this->Guardians->patchEntity($classes, $this->request->data);
            //    pr($classes); die;
            if ($this->Guardians->save($classes)) {
                $this->Flash->success(__('Employee Guardian has been saved.'));

                return $this->redirect(['action' => 'view/' . $this->request->data['user_id'] . '?id=guardians']);
            }
        } else {
            //validation error
            if ($classes->errors()) {
                $error_msg = [];
                foreach ($classes->errors() as $errors) {
                    if (is_array($errors)) {
                        foreach ($errors as $error) {
                            $error_msg[] = $error;
                        }
                    } else {
                        $error_msg[] = $errors;
                    }
                }
                if (!empty($error_msg)) {
                    $this->Flash->error(
                        __("Please fix the following error(s): " . implode("\n \r", $error_msg))
                    );
                }
            }
        }
        $this->set('classes', $classes);
    }
    //-------------------------------------------------\\
    public function change_email($id = null)
    {

        $name = $this->Employees->find()->where(['Employees.id' => $id])->first()->toarray();
        //pr($name); die;
        $this->set('name', $name);
        if (isset($id) && !empty($id)) {
            $classes = $this->Employees->get($id);
        } else {
            $classes = $this->Employees->newEntity();
        }
        $this->set('classes', $classes);
        if ($this->request->is(['post', 'put'])) {

            $classes = $this->Employees->patchEntity($classes, $this->request->data);
            $email = $this->request->data['email'];
            $ids = $classes->id;
            $conn = ConnectionManager::get('default');
            $detail = 'UPDATE `schoolproject`.`employees` SET `email` ="' . $email . '" WHERE `employees`.`id` = ' . $ids;
            $results = $conn->execute($detail);
            $this->Flash->success(__('Employee Personal Information  has been saved.'));
            return $this->redirect(['action' => 'view/' . $ids]);
        }
    }
    //-----------------------------------------------------------//
    public function editaddress($id = null)
    {
        $cities = $this->Cities->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'c_id' => '5'])->order(['name' => 'ASC'])->toArray();
        $states = $this->States->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y', 'c_id' => '5'])->order(['name' => 'ASC'])->toArray();
        $country = $this->Country->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->where(['status' => 'Y'])->order(['name' => 'ASC'])->toArray();
        $this->set('cities', $cities);
        $this->set('states', $states);
        $this->set('country', $country);
        $this->set('cities', $cities);
        if ($_GET['ids']) {
            $this->set('ids', $_GET['ids']);
        }
        if (isset($id) && !empty($id)) {
            $address = $this->Address->get($id);
            $this->set('ids', $address->user_id);
        } else {
            $address = $this->Address->newEntity();
        }
        $this->set('address', $address);
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['type'] = "1";
            $documents = $this->Address->patchEntity($address, $this->request->data);
            $user_id = $documents->user_id;
            if ($this->Address->save($documents)) {
                $this->Flash->success(__('Employee Address has been saved.'));
                return $this->redirect(['action' => 'view/' . $user_id . '?id=address']);
            }
        }
    }
    //--------------------------------------------------------//

    //----------------------------------------------------------------\\
    public function addocument($id = null)
    {

        if (isset($_GET['did'])) {
            $ids = $_GET['did'];
            $this->set('ids', $ids);
        }
        $documentcategory = $this->Documentcategory->find('list', [
            'keyField' => 'id',
            'valueField' => 'categoryname',
        ])->where(['Documentcategory.type !=' => 1])->order(['id' => 'DESC'])->toArray();
        $this->set('documentcategory', $documentcategory);
        if (isset($id) && !empty($id)) {
            $documents = $this->Documents->get($id);
            $ids = $documents->user_id;
            //  pr($ids); die;
            $this->set('ids', $ids);
            $sections = $this->Documents->find()->select(['photo'])->where(['Documents.id' => $id])->order(['id' => 'ASC'])->first()->toArray();
            $rt = '1';
        } else {
            $documents = $this->Documents->newEntity();
            $rt = '0';
        }
        $this->set('documents', $documents);

        $this->set('rt', $rt);

        if ($this->request->is(['post', 'put'])) {


            $filename = $this->request->data['photo']['name'];
            $item = $this->request->data['photo']['tmp_name'];
            $ext = end(explode('.', $filename));
            $name = md5(time($filename));
            $imagename = $name . '.' . $ext;
            $this->request->data['created'] = date('Y-m-d', strtotime($this->request->data['created']));
            if (move_uploaded_file($item, "img/" . $imagename)) {
                $this->request->data['photo'] = $imagename;
                $this->request->data['ext'] = $ext;
            } else {
                $this->request->data['photo'] = $sections['photo'];
            }
            $this->request->data['type'] = "1";
            $this->request->data['user_id'] = $ids;
            $documents = $this->Documents->patchEntity($documents, $this->request->data);
            if ($this->Documents->save($documents)) {
                $this->Flash->success(__('Documents has been saved.'));
                return $this->redirect(['action' => 'view/' . $ids . '?id=documents']);
            }
        }
    }
    //---------------------------------------------------------\\
    public function addcsv()
    {
        $this->viewBuilder()->layout('admin');
        if ($this->request->is('post') || $this->request->is('put')) {

            if ($this->data['file']['type'] == 'application/csv' || 'application/vnd.ms-excel' || ' text/csv') {
                $filenam = $this->request->data['file']['name'];
                $item = $this->request->data['file']['tmp_name'];
                $mkr1 = rand(1, 13000000);
                $filename = $mkr1 . $filenam;
                if (move_uploaded_file($item, "excel_file/" . $filename)) {
                    $this->csv($filename);
                    $this->Flash->success(__('Empoyee added successfully done'), 'flash/sucess');
                }
                $directory = WWW_ROOT . 'excel_file/' . $filename;
                unlink($directory);
                $directory2 = WWW_ROOT . 'excel_file/' . $mkr1;
                unlink($directory2);
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__(' File type must be csv only'), 'flash/Error');
            }
        }
    }

    //---------------------------------------------------------\\
    public function csv($filename)
    {
        $documents = $this->Employees->newEntity();
        $filenames = SITE_URL . 'excel_file/' . $filename;
        // open the file
        $handle = fopen($filenames, "r");
        $header = fgetcsv($handle);
        // create a message container
        $return = array(
            'messages' => array(),
            'errors' => array(),
        );
        // Remove any invalid or hidden characters
        if (sizeof($header) == 1) {
            $field = preg_replace('/[\x00-\x1F\x80-\xFF]/', ',', $header[0]);

            $header = explode(",", $field);
        }
        // read each data row in the file
        while (($row = fgetcsv($handle)) != false) {
            if (sizeof($row) == 1) {
                $row = preg_replace('/[\x00-\x1F\x80-\xFF]/', ',', $row[0]);
                $row = explode(",", $row);
            }
            $data = array();
            foreach ($header as $k => $head) {
                $data[$head] = isset($row[$k]) ? $row[$k] : ',';
            }
            $this->request->data['file'] = $filename;
            $documents = $this->Employees->newEntity();
            $documents = $this->Employees->patchEntity($documents, $data);
            $this->Employees->save($documents);
        }
        fclose($handle);
        return $return;
    }
    //---------------------------------------------------------\\
    public function otherinfos($id = null)
    {

        if (isset($_GET['idss'])) {
            $ids = $_GET['idss'];
            $this->set('ids', $ids);
        }
        if (isset($id) && !empty($id)) {
            $classes = $this->Otherinfos->get($id);
            $ids = $classes->user_id;
            $this->set('ids', $ids);
        } else {
            //using for new entry
            $classes = $this->Otherinfos->newEntity();
        }
        $this->set('classes', $classes);

        if ($this->request->is(['post', 'put'])) {

            $documents = $this->Otherinfos->patchEntity($classes, $this->request->data);
            $user_id = $documents->user_id;
            if ($this->Otherinfos->save($documents)) {
                $this->Flash->success(__('Otherinfo has been saved.'));
                return $this->redirect(['action' => 'view/' . $user_id . '?id=history']);
            }
        }
    }

    public function documentstatus($id, $status)
    {

        $statusquery = $this->Documents->find('all')->where(['Documents.status' => 'Y'])->count();
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {

                $status = 'N';
                //status update
                $classes = $this->Documents->get($id);
                $classes->status = $status;
                if ($this->Documents->save($classes)) {
                    $this->Flash->success(__('Your document status has been updated.'));
                    return $this->redirect(['action' => 'view/' . $classes->user_id . '?id=documents']);
                }
            } else {

                $status = 'Y';
                //status update
                $classes = $this->Documents->get($id);
                $classes->status = $status;
                if ($this->Documents->save($classes)) {
                    $this->Flash->success(__('Your documents status has been updated.'));
                    return $this->redirect(['action' => 'view/' . $classes->user_id . '?id=documents']);
                }
            }
        }
    }

    public function deletedocument($id)
    {
        $contact = $this->Documents->get($id);
        $sections = $this->Documents->find('all')->select(['photo'])->where(['Documents.id' => $id])->toArray();
        $userid = $contact->user_id;
        foreach ($sections as $img) {

            $imglink = $img['photo'];
            unlink('img/' . $imglink);
        }
        //delete particular entry
        if ($this->Documents->delete($contact)) {
            $this->Flash->success(__('The documents with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'view/' . $userid . '?id=documents']);
        }
    }

    public function employeesimage($id = null)
    {
        $this->viewBuilder()->layout('admin');
        if (isset($id) && !empty($id)) {
            $Employees = $this->Employees->find()->where(['Employees.id' => $id])->first()->toarray();
            $this->set(compact('Employees'));
            $employees = $this->Employees->get($id);
        } else {
            $employees = $this->Employees->newEntity();
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->request->data['file'] == "") {
                $filename = $this->request->data['photo']['name'];
                $item = $this->request->data['photo']['tmp_name'];
                $ext = end(explode('.', $filename));
                $name = md5(time($filename));
                $imagename = $name . '.' . $ext;
                if (move_uploaded_file($item, "uploads/" . $imagename)) {
                    $this->request->data['file'] = $imagename;
                }
            }
            $employees = $this->Employees->patchEntity($employees, $this->request->data);
            if ($this->Employees->save($employees)) {
                $ids = $employees->get(id);
                $this->Flash->success(__('Profile Picture  has been saved.'));
                return $this->redirect(['action' => 'view/' . $ids]);
            }
        }
    }
    public function take_attendance()
    {
        $this->autoRender = false;
        $this->loadModel('Employeeattendance');
        // $this->viewBuilder()->layout('admin');
        if ($this->request->is('post')) {
            try {
                $absent_periods = array();
                $absent_periods = $this->request->data['absent_periods'];
                if ($this->request->data['status'] == 'Absent') {
                    for ($i = 0; $i <= 7; $i++) {
                        if (empty($absent_periods[$i])) {
                            $present_periods[$i] = "A" . $i;
                        }
                    }
                } else {
                    $present_periods = ["A0", "A1", "A2", "A3", "A4", "A5", "A6", "A7"];
                    $absent_periods = null;
                }
                $date = date('Y-m-d');
                $remark = $this->request->data['remark'];
                $present = implode(',', $present_periods);
                $absent = implode(',', $absent_periods);
                $id = $this->request->data['id'];
                if ($this->request->data['status'] == 'Absent') {
                    if (!empty($present_periods)) {
                        $status = 'P';
                    } else {
                        $status = 'A';
                    }
                } else {
                    $status = 'P';
                }
                //pr($id); die;
                foreach ($id as $employee_id) {

                    $detail = array();
                    $detail['status'] = $status;
                    $detail['employee_id'] = $employee_id;
                    $detail['date'] = $date;
                    $detail['remark'] = $remark;
                    $exist = $this->Employeeattendance->find('all')->where(['employee_id' => $employee_id, 'DATE(date)' => $date])->first();
                    if (!empty($exist)) {
                        $detail['present_periods'] = $present;
                        $detail['absent_periods'] = $absent;
                        $query = $this->Employeeattendance->patchEntity($exist, $detail);
                        $this->Employeeattendance->save($query);
                    } else {
                        $detail['present_periods'] = $present;
                        $detail['absent_periods'] = $absent;
                        //pr($detail);die;
                        $new = $this->Employeeattendance->newEntity();
                        $patch = $this->Employeeattendance->patchEntity($new, $detail);
                        $this->Employeeattendance->save($patch);
                    }
                }
                $this->Flash->success(__('Attendance Updated Successfully'));
                return $this->redirect(['action' => 'employeeattendance']);
            } catch (\PDOException $e) {
                $this->Flash->error(__('Error in updating attendance'));
                $this->set('error', $error);
                return $this->redirect(['action' => 'employeeattendance']);
            }
        }
    }
    public function sendsms_staff()
    {

        // send sms count fatch from main school table
        $this->loadModel('Users');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        $db = $branch[0];

        if ($db == 'canvas') {
            $connss = ConnectionManager::get('default');
            $query2 = "SELECT * FROM $db.users  ";
            $res = $connss->execute($query2)->fetchAll('assoc');
        } else {
            $id = $this->request->session()->read('Auth.User.c_id');
            $res = $this->Users->find('all')->where(['c_id' => $id])->toArray();
        }
        $user_id = $res[0]['c_id'];
        $connss = ConnectionManager::get('default');
        $query2 = "SELECT * FROM school_erp.schools where id= $user_id ";
        $resss = $connss->execute($query2)->fetchAll('assoc');
        // pr($res); die;
        $this->set('balance', $resss);

        //-------------------------------- code end ---------------//


        $this->loadModel('Smsmanager');
        $smslist = $this->Smsmanager->find('all')->order(['id' => 'ASC'])->toArray();
        $this->set('smslist', $smslist);

        $smscategoryslist = $this->Smsmanager->find('list', [
            'keyField' => 'category',
            'valueField' => 'category',
        ])->where(['status' => 'Y', 'sms_for IN ' => ['E', 'B']])->order(['id' => 'ASC'])->toArray();
        $this->set('smscategoryslist', $smscategoryslist);
    }

    public function staff_sms()
    {
        $this->loadModel('Employees');
        $this->loadModel('Smsmanager');
        $this->loadModel('Smsdelivery');

        $this->autoRender = false;
        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data); die;
            $mesg = $this->request->data['message'];
            $category = $this->request->data['category'];
            $smstemp = $this->Smsmanager->find('all')->select(['id'])->where(['category' => $category])->order(['id' => 'ASC'])->first();
            $smstemp_id = $smstemp['id'];
            $id = $this->request->data['id'];

            //---------------sms count and manage------------------------//
            $cnt = 0;
            for ($i = 0; $i < count($id); $i++) {
                $cnt++;
            }
            //   pr($cnt); die;
            $this->loadModel('Users');
            $dbname = $this->request->session()->read('Auth.User.db');
            $branch = explode("_", $dbname);
            $db = $branch[0];

            if ($db == 'canvas') {
                $connss = ConnectionManager::get('default');
                $query2 = "SELECT * FROM $db.users ";
                $res = $connss->execute($query2)->fetchAll('assoc');
            } else {
                $ids = $this->request->session()->read('Auth.User.c_id');
                $res = $this->Users->find('all')->where(['c_id' => $ids])->toArray();
            }
            $user_id = $res[0]['c_id'];
            $connss = ConnectionManager::get('default');
            $query2 = "SELECT * FROM school_erp.schools where id= $user_id ";
            $resss = $connss->execute($query2)->fetchAll('assoc');
            // pr($resss); die;

            $sent_msg = $resss[0]['msg_count'];
            $clint_id = $resss[0]['id'];
            $total_sent = $sent_msg - $cnt;

            //  agere whatsapp api nhi hoto error show code
            if (empty($resss[0]['whatsapp_token'])) {

                $this->Flash->error(__('Kindly contact to administrator.'));
                return $this->redirect(['action' => 'index']);
            }


            if ($sent_msg >= $cnt) {

                $conn = ConnectionManager::get('default');
                $conn->execute("UPDATE `school_erp`.`schools` SET `msg_count`='$total_sent' WHERE id='$clint_id'");
            }

            //-------------------------------- code end ---------------//

            $rolepresent = $this->request->session()->read('Auth.User.role_id');
            $romsm = sizeof($id);
            if ($mesg) {
                $connsssks = ConnectionManager::get('default');
                $mesg1 = addslashes($mesg);
                $connsssks->execute("INSERT INTO
				`sms_deliveries`(`sms_temp_id`, `message`, `total_students`,`status`) VALUES
				('" . $smstemp_id . "','" . $mesg1 . "','" . $romsm . "','Y')");
                $smsdelivery = $this->Smsdelivery->find('all')->select(['id'])->order(['id' => 'DESC'])->first();
                $cnt = 0;
                $toke = array();

                if ($sent_msg > $cnt) {

                    foreach ($id as $s => $id_val) {  //pr($id_val); die;

                        //echo 'hellosss'; die;

                        $result = '';
                        $employees = $this->Employees->find('all')->select(['mobile', 'id', 'token'])->where(['Employees.status' => 'Y', 'id' => $id_val])->order(['id' => 'ASC'])->first();
                        if (empty($employees['mobile'])) {
                            continue;
                        }
                        $mobile = $employees['mobile'];

                        // $result = $this->file_get_contents_curl('http://alerts.prioritysms.com/api/web2sms.php?workingkey=Afb7ede974e22367003ac66f8514bd152&to=' . $mobile . '&sender=SNSKAR&message=' . urlencode($mesg));


                        $mobiless = '+91' . $mobile;
                        $whatsapp_token = $resss[0]['whatsapp_token'];
                        $instance_id = $resss[0]['instance_id'];
                        $result = $this->whatsappmsg($mobiless, $mesg, $whatsapp_token, $instance_id);

                        if ($result == "Invalid Input Data") {
                            $connsssks1 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');

                            $connsssks1->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`,`smsmobile`, `sms_deliv_id`, `sms_temp_id`,`created`,`status`,`type`) VALUES
				('" . $employees['id'] . "','" . $employees['mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N','E')");
                        } else if ($result == "Invalid Mobile Numbers") {
                            $connsssks2 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');

                            $connsssks2->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`,`status`,`type`) VALUES
				('" . $employees['id'] . "','" . $employees['mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N','E')");
                        } else if ($result == "Insufficient credits") {

                            $connsssks3 = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');

                            $connsssks3->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`,`sms_deliv_id`, `sms_temp_id`,`created`,`status`,`type`) VALUES
				('" . $employees['id'] . "','" . $employees['mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','N','E')");

                            $this->Flash->error(__('Insufficient credits, Please Contact to Web Administrator !!!!.'));
                            return $this->redirect(['action' => 'index']);
                        } else {

                            $connsssks = ConnectionManager::get('default');
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d H:i:s');

                            $connsssks->execute("INSERT INTO
				`sms_deliveries_details`(`stud_id`, `smsmobile`, `sms_deliv_id`, `sms_temp_id`,`created`,`type`) VALUES
				('" . $employees['id'] . "','" . $employees['mobile'] . "','" . $smsdelivery['id'] . "','" . $smstemp_id . "','" . $date . "','E')");
                        }
                        $toke[] = $employees['token'];
                    }
                    $this->Flash->success(__('Send SMS to All Staff sucessfully.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('insufficient credits balance please recharge to continue!!.'));
                    return $this->redirect(['action' => 'index']);
                }


                // $_POST['title'] = 'Sanskar Campus';
                // $_POST['body'] = $mesg;

                // $push = null;
                // $push = new \Push(
                //     $_POST['title'],
                //     $_POST['body']
                // );

                // $mPushNotification = $push->getPush();
                // $firebase = new \Firebase();
                // $_POST['data'] = ['payload' => ["title" => $_POST['title'], 'body' => $_POST['body']]];
                // $firebase->send($toke, $mPushNotification, $_POST['data']);
                // $this->Flash->success(__('Send SMS to All Staff sucessfully.'));
                // return $this->redirect(['action' => 'index']);
            }
        }
    }
    public function employeeattendance()
    {
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        if ($rolepresent == "16") {
            $this->viewBuilder()->Layout('admin');
            $this->loadModel('Employees');
            $this->loadModel('Departments');
            $dept = $this->Departments->find('all')->toarray();
            $this->set(compact('dept'));

            $this->loadModel('Employeeattendance');
            $date = date('Y-m-d');

            $conn = ConnectionManager::get('default');
            $SQL = "SELECT * FROM `employees` INNER JOIN `employee_attendance` ON `employees`.`id`=`employee_attendance`.`employee_id`  WHERE `employees`.`designation_id`=6 AND `employee_attendance`.`date`= ' $date '   AND `employee_attendance`.`absent_periods`!='' ORDER BY `employee_attendance`.`absent_periods`  DESC";

            $classes_data2 = $conn->execute($SQL)->fetchAll('assoc');
            $NTSQL = "SELECT * FROM `employees` INNER JOIN `employee_attendance` ON `employees`.`id`=`employee_attendance`.`employee_id`  WHERE `employees`.`designation_id`!=6 AND `employee_attendance`.`date`= ' $date ' AND `employee_attendance`.`status` IN('A','PR','PL')";
            $staff_data = $conn->execute($NTSQL)->fetchAll('assoc');

            $staff_det = array();
            foreach ($staff_data as $key => $iteam) {
                $staff_det[$iteam['employee_id']] = $iteam['status'];
                $ntid[] = $iteam['employee_id'];
            }

            foreach ($classes_data2 as $key => $iteam) {

                $eid[] = $iteam['employee_id'];
            }

            if (!empty($eid)) {
                $classes_data3 = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id IN' => $eid, 'Employees.designation_id' => 6])->order(['Employees.fname' => 'ASC'])->toarray();
                $classes_data = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id NOT IN' => $eid, 'Employees.designation_id' => 6])->order(['Employees.fname' => 'ASC'])->toarray();
                $students = array_merge($classes_data3, $classes_data);
            } else {
                $students = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.designation_id' => 6])->order(['Employees.fname' => 'ASC'])->toarray();
            }

            if (!empty($ntid)) {
                $this->set(compact('staff_det'));

                $staff_data3 = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id IN' => $ntid, 'Employees.designation_id !=' => 6])->order(['Employees.fname' => 'ASC'])->toarray();
                $staff_data = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.id NOT IN' => $ntid, 'Employees.designation_id !=' => 6])->order(['Employees.fname' => 'ASC'])->toarray();
                $staff = array_merge($staff_data3, $staff_data);
            } else {
                $staff = $this->Employees->find('all')->where(['Employees.status' => 'Y', 'Employees.designation_id !=' => 6])->order(['Employees.designation_id' => 'ASC'])->toarray();
            }
            $this->set(compact('staff'));
            $this->set('students', $students);
        } else {
            $this->Auth->logout();
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }
    public function take_other_attendance()
    {
        $this->autoRender = false;
        //pr($_POST);die;
        $this->loadModel('Employeeattendance');
        $this->loadModel('Leaves');
        if ($this->request->is('post')) {

            $empid = $_POST['oid'];
            $status = $_POST['status'];
            try {
                $dates = explode(',', $this->request->data['abs_date']);
                $staffStatus = $this->request->data['status'];
                $adates = $this->request->data['abs_date'];
                $remarks = $this->request->data['remarks'];
                foreach ($adates as $id => $adate) {
                    if ($id == "m_atn") {
                        $eid = $empid[0];
                    }
                    $dates = explode(',', $adate);
                    foreach ($dates as $date) {
                        $date = date('Y-m-d', strtotime($date));
                        $detail = array();
                        $detail['employee_id'] = $eid;
                        $detail['date'] = $date;
                        $detail['remark'] = $remarks;
                        $exist = $this->Employeeattendance->find('all')->where(['employee_id' => $eid, 'DATE(date)' => $date])->first();
                        if (!empty($exist)) {
                            $detail['status'] = $status;
                            $query = $this->Employeeattendance->patchEntity($exist, $detail);
                            $this->Employeeattendance->save($query);
                        } else {
                            $detail['status'] = $status;
                            //pr($detail);die;
                            $new = $this->Employeeattendance->newEntity();
                            $patch = $this->Employeeattendance->patchEntity($new, $detail);
                            $this->Employeeattendance->save($patch);
                        }
                        if ($status == 'A' && !empty($this->request->data['leave_type'])) {
                            $leave = $this->Leaves->find('all')->where(['emp_id' => $eid, 'date' => $date])->toarray();
                            if (empty($leave)) {
                                $leave = $this->Leaves->newEntity();
                            }
                            $data = array();
                            $data['emp_id'] = $eid;
                            $data['date'] = $date;
                            $data['leave_type'] = $this->request->data['leave_type'];
                            $patchleave = $this->Leaves->patchEntity($leave, $data);
                            $this->Leaves->save($patchleave);
                        }
                    }
                }
                if ($id == "m_atn") {
                    $updata['empId'] = $empid[0];
                    $updata['sno'] = $this->request->data['sno'];
                    $my = explode('-', $dates[0]);
                    $mon = $my[1];
                    $year = $my[2];
                    $updata['date'] = $mon . '-' . $year;
                    echo json_encode($updata);
                }
                $updata['empId'] = $this->request->data['emp_id'];
                $updata['sno'] = $this->request->data['sno'];
                $updata['date'] = $this->request->data['month'] . '-' . $this->request->data['year'];
            } catch (\PDOException $e) {

                $this->Flash->error(__('Attendance Updated Failed'));
                $this->set('error', $error);
                return $this->redirect(['action' => 'employeeattendance']);
            }
        }
    }

    public function delete_atn()
    {
        $id = $this->request->data['id'];
        $remark = $this->request->data['remarks'];
        $date = $this->request->data['date'];
        $empId = $this->request->data['empId'];
        $sno = $this->request->data['sno'];
        $this->autoRender = false;
        $rolepresent = $this->request->session()->read('Auth.User.role_id');
        if ($rolepresent == "16" || $rolepresent == "17") {
            try {

                $this->loadModel('Employeeattendance');
                $res = $this->Employeeattendance->find('all')->where(['id' => $id])->first();
                $data['status'] = 'P';
                $data['remark'] = $remark;
                $result = $this->Employeeattendance->patchEntity($res, $data);
                $results = $this->Employeeattendance->save($result);
                $updata['empId'] = $empId;
                $updata['sno'] = $sno;
                $date = explode('-', $date);
                $mon = $date[1];
                $year = $date[0];
                $updata['date'] = $mon . '-' . $year;
                echo json_encode($updata);
                die;
            } catch (\PDOException $e) {
                $this->Flash->error(__('Attendance updation Failed'));
                $this->set('error', $error);
            }
        }
    }


    public function add_dept()
    {
        $new = $this->PayrollDepartments->newEntity();
        $patch = $this->PayrollDepartments->patchEntity($new, $this->request->data);
        $this->PayrollDepartments->save($patch);
        $this->Flash->Success(__('Department Updated Successfully'));
        return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
    }


    public function add_desg()
    {
        $new = $this->PayrollDesignations->newEntity();
        $patch = $this->PayrollDesignations->patchEntity($new, $this->request->data);
        $this->PayrollDesignations->save($patch);
        $this->Flash->Success(__('Designation Updated Successfully'));
        return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
    }


    public function dept_status($id = "", $status = "")
    {

        if (isset($id) && !empty($id)) {
            if ($status == 'N') {

                $status = 'Y';
                //status update
                $classes = $this->PayrollDepartments->get($id);
                $classes->status = $status;
                if ($this->PayrollDepartments->save($classes)) {
                    $this->Flash->success(__('Department status has been updated.'));
                    return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
                }
            } else {

                $status = 'N';
                //status update
                $classes = $this->PayrollDepartments->get($id);
                $classes->status = $status;
                if ($this->PayrollDepartments->save($classes)) {
                    $this->Flash->success(__('Department status has been updated.'));
                    return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
                }
            }
        }
    }


    public function desg_status($id = "", $status = "")
    {

        if (isset($id) && !empty($id)) {
            if ($status == 'N') {

                $status = 'Y';
                //status update
                $classes = $this->PayrollDesignations->get($id);
                $classes->status = $status;
                if ($this->PayrollDesignations->save($classes)) {
                    $this->Flash->success(__('Designation status has been updated.'));
                    return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
                }
            } else {
                $status = 'N';
                //status update
                $classes = $this->PayrollDesignations->get($id);
                $classes->status = $status;
                if ($this->PayrollDesignations->save($classes)) {
                    $this->Flash->success(__('Designation status has been updated.'));
                    return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
                }
            }
        }
    }
    public function desg_delete($id = "")
    {
        $classes = $this->PayrollDesignations->get($id);
        $this->PayrollDesignations->delete($classes);
        $this->Flash->success(__('Designation Deleted Successfully'));
        return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
    }
    public function dept_delete($id = "")
    {
        $classes = $this->PayrollDepartments->get($id);
        $this->PayrollDepartments->delete($classes);
        $this->Flash->success(__('Designation Deleted Successfully'));
        return $this->redirect(['controller' => 'Leavetype', 'action' => 'index']);
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


    public function export_det()
    {
        $this->loadModel('Employeesalary');
        $this->loadModel('Employees');
        $employee = $this->Employees->find('all')->where(['Employees.status' => 'Y'])->order(['Employees.id' => 'ASC'])->toarray();
        $this->set('employee', $employee);
    }

    public function importemployee()
    {
        $this->loadModel('Users');
        $this->loadModel('Employees');
        $user = $this->Employees->find('all')->toarray();
        $count = 1;
        foreach ($user as $employ) {

            $fnames = $employ['fname'];
            $username = $employ['username'];
            $mobile = $employ['mobile'];
            $academic_year = '2021-22';
            $db = $this->request->session()->read('Auth.User.db');
            $database_name = $db;
            $cdate = date('Y-m-d H:i:s');
            $Bord = 1;
            $id = $employ['id'];
            $conn = ConnectionManager::get('default');
            $NTSQL = "SELECT * FROM `school_erp`.`users` WHERE `mobile`= '$mobile' and `db`='$db' and `role_id` = '3'";
            $staff_data = $conn->execute($NTSQL)->fetchAll('assoc');
            if ($staff_data) {
                //  pr($staff_data); die;
                $dd[] = $staff_data[0]['mobile'];
                // 
            } else {
                $inserts = "INSERT INTO `school_erp`.`users` (`user_name`,`c_id`,`academic_year`, `email`,`tech_id`, `password`, `confirm_pass`, `created`, `modified`, `role_id`, `db`,`board`,`mobile`) VALUES
              ('$fnames','0','$academic_year','$username', '$id','$2y$10$.vwir1Fyl5EkpHbNfEYFl.vU7raO0gFMuM.QnwVqpLM77Mjlot1Zm','12345', '$cdate', '0000-00-00 00:00:00', 3,'$database_name','$Bord','$mobile')";
                $exicute = $conn->execute($inserts);
            }
        }
        pr($dd);
        die;
        die;
    }

    /// employee edit new function
    public function emp_edit($id = null)
    {

        $this->viewBuilder()->Layout('admin');
        $this->loadModel('Designations');
        $this->loadModel('Departments');
        $this->loadModel('Employees');
        $this->loadModel('Employeesalary');

        $role_id = $this->request->session()->read('Auth.User.role_id');
        $this->set(compact('role_id'));
        $db = $this->request->session()->read('Auth.User.db');
        $academic_year = $this->request->session()->read('Auth.User.academic_year');
        $this->set(compact('db'));
        $this->viewBuilder()->layout('admin');

        $depa = $this->Departments->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toarray();
        $desi = $this->Designations->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toarray();

        $this->set('depa', $depa);
        $this->set('desi', $desi);

        $classes1 = $this->Employees->get($id);


        $mobiles = $classes1['mobile'];

        $this->set('classes', $classes1);
        $user = $this->Users->newEntity();

        if ($this->request->is(['post', 'put'])) {


            $this->request->data['slab_type'] = implode(',', $this->request->data['slab_type']);
            $this->request->data['fname'] = ucfirst(strtolower($this->request->data['fname']));
            $this->request->data['middlename'] = ucfirst(strtolower($this->request->data['middlename']));
            $this->request->data['lname'] = ucfirst(strtolower($this->request->data['lname']));
            $this->request->data['dob'] = date('Y-m-d', strtotime($this->request->data['dob']));
            $this->request->data['joiningdate'] = date('Y-m-d', strtotime($this->request->data['joiningdate']));
            $this->request->data['username'] = $this->request->data['email'];
            $this->request->data['f_h_name'] = $this->request->data['f_h_name'];
            $this->request->data['basic_salary'] = $this->request->data['basic_salary'];
            $this->request->data['department_id'] = $this->request->data['p_department'];
            $this->request->data['designation_id'] = $this->request->data['p_designation'];
            $this->request->data['title'] = $this->request->data['title'];
            $this->request->data['blood_group'] = $this->request->data['blood_group'];
            $this->request->data['gender'] = $this->request->data['gender'];

            $db = $this->request->session()->read('Auth.User.db');

            $filename = $this->request->data['file']['name'];
            $item = $this->request->data['file']['tmp_name'];
            $ext = end(explode('.', $filename));
            $name = md5(time($filename));
            $imagename = $name . '.' . $ext;
            $dest = $db . '_image' . '/' . "employees/";
            $newfile = $dest . $imagename;

            if (move_uploaded_file($item, $newfile)) {

                $this->request->data['file'] = $imagename;
            }
            $classes = $this->Employees->patchEntity($classes1, $this->request->data);
            // pr($classes); die;

            $id = $classes->id;
            $username = $classes->email;
            $password = $classes->password;
            $mobile = $classes->mobile;
            $roles = 3;

            $this->request->data['email'] = $username;
            $this->request->data['academic_year'] = $academic_year;
            $this->request->data['mobile'] = $mobile;
            $this->request->data['password'] = $this->_setPassword(12345);
            $this->request->data['confirm_pass'] = '12345';
            $this->request->data['role_id'] = $roles;
            $this->request->data['tech_id'] = $id;
            $this->request->data['user_name'] = $this->request->data['fname'];
            $this->request->data['fkey'] = "NULL";
            $this->request->data['latefee'] = "NULL";
            $this->request->data['attendenceupdate'] = "NULL";
            $user = $this->Users->patchEntity($user, $this->request->data);
            $this->Users->save($user);

            $emp_mobile = $mobiles;
            $emp_db = $this->request->data['db'];
            $conn = ConnectionManager::get('default');
            $check = "SELECT * FROM school_erp.users where `mobile`='$emp_mobile' and `db`='$db'";
            $results = $conn->execute($check)->fetchAll('assoc');
            if (!empty($results[0]['id'])) {
                $first_name = $classes1['fname'];
                $emp_email = $classes1['username'];
                $roles = $results[0]['role_id'];
                $emp_mobile = $classes['mobile'];
                $conn = ConnectionManager::get('default');
                $Emp_update = "UPDATE `school_erp`.`users` SET `user_name`='$first_name', `email`='$emp_email', `mobile`='$emp_mobile', `role_id`= $roles WHERE `tech_id`='$id' and `db`='$emp_db' ";

                $conn->execute($Emp_update);
                $classessss = $this->Employees->save($classes);
                $this->Flash->success(__('Employee Edit Successfully'));
                return $this->redirect(['controller' => 'employees', 'action' => 'index']);
            }
        }
    }
}
