<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class PermissionmodulesController extends AppController
{
    //initialize component
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Classes');
        $this->loadModel('Sections');
        $this->loadModel('Subjects');
        $this->loadModel('Classections');
        $this->loadModel('Subjectclass');
        $this->loadModel('Assignments');
        $this->loadModel('Employees');
        $this->loadModel('PermissionModules');
        $this->loadModel('Users');
    }

    public function index($id = null)
    {
        // $ids = $this->request->session()->read('Auth.User.db');
        $this->viewBuilder()->layout('admin');
        $employees = $this->Users->find('all')->find('list', ['keyField' => 'id', 'valueField' => 'email'])->where(['Users.role_id !=' => '2'])->order(['Users.user_name' => 'ASC'])->toarray();
        $this->set(compact('employees'));
        if ($id) {
            $employees = $this->Users->find('all')->find('all')->where(['Users.id' => $id])->order(['Users.user_name' => 'ASC'])->first();
            $employees_rollid = $this->Users->find('all')->select(['role_id'])->where(['Users.id' => $id])->order(['Users.user_name' => 'ASC'])->first();
            $username = $employees['user_name'];
            $this->set(compact('username'));
            $this->set(compact('id'));
            $this->set(compact('employees_rollid'));
        }

        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data);

            $modules = sizeof($this->request->data['module']);

            $user_id = $this->request->data['user_id'];
            $userTable = TableRegistry::get('PermissionModules');
            $exists = $userTable->exists(['user_id' => $user_id]);

            // DELETE exists permission
            if ($exists) {
                $conns = ConnectionManager::get('default');
                $quersy = "DELETE FROM `permission_module` WHERE `user_id`='$user_id'";
                $conns->execute($quersy);
            }

            // Prepare the query outside the loop
            $queryTemplate = "INSERT INTO `permission_module` (`user_id`, `module`, `menu`, `controller`, `action`, `featured`, `status`, `edit`, `delete`, `sort_no`, `featured_sort`) VALUES (:user_id, :module, :menu, :controller, :action, :featured, 'Y', :edit_user, :delete_user, :sort_no, :featured_sort)";
            
            
            $queryParams = [];
            for ($i = 1; $i <= $modules; $i++) {
                $mod = $this->request->data['module' . $i];
                $menu = $this->request->data['menu' . $i];
                $user_edit_permission = $this->request->data['permission' . $i];
                $user_delete_permission = $this->request->data['permission_delete' . $i];
                $featured = $this->request->data['featured' . $i];
                $sort_no = $this->request->data['sort' . $i];
                $featured_sort = $this->request->data['featuredno' . $i];
                // pr($featured_sort);

                foreach ($menu as $kty => $ddd) {
                    $ter = explode('^', $ddd);
                    $edit_user = !empty($user_edit_permission[$kty]) ? $user_edit_permission[$kty] : '0';
                    $delete_user = !empty($user_delete_permission[$kty]) ? $user_delete_permission[$kty] : '0';
                    $featuredValue = !empty($featured[$kty]) ? $featured[$kty] : '0';

                    $sort = !empty($sort_no[$kty]) ? $sort_no[$kty] :null;
                    $featuredno = ($featuredValue == '1') ? $featured_sort[$kty] :null;
                  
                    // Set parameters for the prepared statement
                    $queryParams[] = [
                        'user_id' => $user_id,
                        'module' => $mod[0],
                        'menu' => $ter[0],
                        'controller' => $ter[1],
                        'action' => $ter[2],
                        'featured' => $featuredValue,
                        'edit_user' => $edit_user,
                        'delete_user' => $delete_user,
                        'sort_no' => $sort,
                        'featured_sort' => $featuredno,
                    ];
                    
                }
            }
            // pr($queryParams);die;
            // Execute the prepared statement after the loop
            $conn = ConnectionManager::get('default');
            foreach ($queryParams as $params) {
                $results = $conn->execute($queryTemplate, $params);
            }
            // pr($queryParams);die;

            if ($this->request->data['naction']) {
                $this->Flash->success(__('Rights Update to User sucessfully.'));
                return $this->redirect(['controller' => 'roles', 'action' => 'index']);
            } else {
                $this->Flash->success(__('Rights Update to User sucessfully.'));
                return $this->redirect(['action' => 'index']);
            }

            // for save permission for show icon in headra
            // $conn = ConnectionManager::get('default');
            // foreach ($queryPermission as $params1) {
            //     $results1 = $conn->execute($queryTemplate_Permission, $params1);
            // }

            // for ($i = 1; $i <= $modules; $i++) {
            //     $menu = array();
            //     $featured = array();
            //     $mod = array();
            //     $user_edit_permission = array();
            //     $mod = $this->request->data['module' . $i];
            //     $menu = $this->request->data['menu' . $i];
            //     // $permission = $this->request->data['permission' . $i];
            //     $user_edit_permission = $this->request->data['permission'];
            //     $featured = $this->request->data['menu' . $i . 'a'];
            //     foreach ($menu as $kty => $ddd) {
            //         $ter = array();
            //         $ter = explode('^', $ddd);
            //         $conn = ConnectionManager::get('default');
            //         if ($featured[$kty]) {
            //             if (!empty($user_edit_permission[$kty])) {
            //                 $edit_user = $user_edit_permission[$kty];
            //             } else {
            //                 $edit_user = '0';
            //             }
            //             $query = "INSERT INTO `permission_module`(`user_id`, `module`, `menu`, `controller`, `action`, `featured`,`status`,`edit`) VALUES ('$user_id','$mod[0]','$ter[0]','$ter[1]','$ter[2]','$featured[0]','Y','$edit_user')";
            //             $module = explode(' ', $mod[0]);
            //             $short_name = $module[0];
            //         } else {
            //             $query = "INSERT INTO `permission_module`(`user_id`, `module`, `menu`, `controller`, `action`,`status`,`edit`) VALUES ('$user_id','$mod[0]','$ter[0]','$ter[1]','$ter[2]','Y','$edit_user')";
            //         }
            //         // pr($query);
            //         $results = $conn->execute($query);
            //     }
            // } //die;
        }
    }

    public function calculatepermission()
    {
        if ($this->request->is(['post', 'put'])) {
            $empid = $this->request->data['empid'];
            $employees_rollid = $this->request->data['emp_roleid'];
            $this->set(compact('empid'));
            $this->set(compact('employees_rollid'));

            $userTable = TableRegistry::get('PermissionModules');
            $exists = $userTable->exists(['user_id' => $empid]);
            if ($exists) {
                $employees = $this->PermissionModules->find('all')->find('all')->where(['PermissionModules.user_id' => $empid])->order(['PermissionModules.id' => 'ASC'])->toarray();
                $module = array();
                $menu = array();
                $featured = array();
                
                foreach ($employees as $k => $ty) {
                    if (!in_array($ty['module'], $module)) {
                        $module[] = $ty['module'];
                    }
                    if (!in_array($ty['menu'], $menu)) {
                        $menu[] = $ty['menu'];
                        $featured[] = $ty['featured'];
                    }
                }
                $this->set(compact('module', 'menu', 'featured'));
            } else {
                $module = array();
                $menu = array();
                $this->set(compact('module', 'menu'));
            }
        }
    }



    // public function adjustassign()
    // {
    //     $storeId = "Senior";
    //     $getdata = $this->Employees->find('all')->where(['FIND_IN_SET(\'' . $storeId . '\',slab_type)'])->toArray();

    //     $getdata = $this->Employees->find('all')->where(['FIND_IN_SET(\'' . $storeId . '\',slab_type)'])->toArray();
    //     foreach ($getdata as $kj => $ll) {
    //         $employees = $this->Users->find('all')->where(['Users.role_id' => '3', 'Users.email' => $ll['username']])->order(['Users.user_name' => 'ASC'])->first();
    //         $employeesh = $employees['id'];
    //         if ($employeesh != '') {

    //             $userTable = TableRegistry::get('PermissionModules');
    //             $exists = $userTable->exists(['user_id' => $employeesh]);
    //             if ($exists) {
    //             } else {

    //                 $conn = ConnectionManager::get('default');

    //                 $query = "INSERT INTO `permission_module` (`user_id`, `module`, `menu`, `controller`, `action`, `status`) VALUES ('$employeesh', 'Higher Classes Exam Mangement (VI-XII)', 'Upload Results', 'studentexamresult', 'examcontrolviewsubject', 'Y'), ('$employeesh', 'Assignment Managment', 'Post Home Work', 'assignments', 'index', 'Y'), ('$employeesh', 'Timetable Management', 'Teacher Timetable', 'ClasstimeTabs', 'teachertimetable', 'Y')";
    //                 $results = $conn->execute($query);
    //             }
    //         }
    //     }
    // }
}
