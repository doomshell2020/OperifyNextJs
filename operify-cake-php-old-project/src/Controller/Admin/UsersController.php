<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;

class UsersController extends AppController
{

    public function login()
    {
        $this->viewBuilder()->layout('login');
        return $this->redirect('/logins');
    }

    // public function connection($dbname)
    // {
    //     ConnectionManager::config($dbname, [
    //         'className' => 'Cake\Database\Connection',
    //         'driver' => 'Cake\Database\Driver\Mysql',
    //         'persistent' => false,
    //         'host' => DBHOSTNAME,
    //         'username' => 'tpplerp',
    //         'password' => 'tpplerp@23~',
    //         'database' => $dbname,
    //         'encoding' => 'utf8mb4',
    //         'timezone' => 'UTC',
    //         'cacheMetadata' => true,
    //     ]);
    // }
    public function connection($dbname)
    {
        ConnectionManager::config($dbname, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => MYSQLHOST,
            'username' => MYSQLUSERNAME,
            'password' => MYSQLPASSWORD,
            'database' => $dbname,
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ]);
    }
    public function changepassword()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Users');
        $this->loadModel('Classfee');

        $role_id = $this->request->session()->read('Auth.User.role_id');
        $id = $this->request->session()->read('Auth.User.id');
        $ems = $this->Users->find('all')->where(['id' => '1'])->first();
        $academic_year = $ems['academic_year'];
        $email_exists = $this->Users->find('all')->where(['role_id' => $role_id, 'id' => $id])->first();
        $id = $email_exists['id'];
        $this->set('userssid', $id);
        $this->set('role_id', $role_id);

        $users = $this->Users->get($this->Auth->user('id'));

        $this->set('latefee', $users['latefee']);
        if (isset($id) && !empty($id)) {
            //using for edit
            $user = $this->Users->get($id);
        }
        if ($this->request->is(['post', 'put'])) {
            //check old password and new password
            $conn = ConnectionManager::get('default');
            if ((isset($this->request->data['new_password']) && !empty($this->request->data['new_password'])) && (isset($this->request->data['confirm_pass']) && !empty($this->request->data['confirm_pass']))) {
                if ($this->request->data['new_password'] == $this->request->data['confirm_pass']) {
                    $role_id = $this->request->session()->read('Auth.User.role_id');
                    $newpass['password'] = (new DefaultPasswordHasher)->hash($this->request->data['new_password']);
                    $newpass['confirm_pass'] = $this->request->data['new_password'];



                    //change password
                    // $newpass['role_id']=$role_id;
                    $this->request->data['role_id'] = $role_id;
                    $user = $this->Users->patchEntity($user, $newpass);
                    $this->Users->save($user);

                    $mobile = $user['mobile'];
                    $role_id = $user['role_id'];

                    $conn = ConnectionManager::get('default');
                    $detail = 'UPDATE `operify`.`users` SET `password` ="' . $newpass['password'] . '",`confirm_pass` ="' . $newpass['confirm_pass'] . '" WHERE `mobile` = "' . $mobile . '" and `role_id` = "' . $role_id . '"';
                    $conn->execute($detail);


                    $this->Flash->success(__('Your Password Successfully Changed !!!!'));
                    return $this->redirect(['controller' => 'users', 'action' => 'changepassword']);
                } else {
                    $this->Flash->error(__("Your new password and confirm password doesn't match, try again."));
                    return $this->redirect(['action' => 'changepassword']);
                }
            }else {
                return $this->redirect(['controller' => 'users', 'action' => 'changepassword']);
            }
        } // edit site setting

        $this->set('users', $users);
    }

    public function newdbcreate($mysqlUserName, $mysqlPassword, $clondedbname, $newdbname)
    {
        /********************* START CONFIGURATION *********************/
        $DB_SRC_HOST = DBHOSTNAME;
        $DB_SRC_USER = $mysqlUserName;
        $DB_SRC_PASS = $mysqlPassword;
        $DB_SRC_NAME = $clondedbname;
        $DB_DST_HOST = DBHOSTNAME;
        $DB_DST_USER = $mysqlUserName;
        $DB_DST_PASS = $mysqlPassword;
        $DB_DST_NAME = $newdbname;
        /*********************** GRAB OLD SCHEMA ***********************/
        $db1 = mysqli_connect($DB_SRC_HOST, $DB_SRC_USER, $DB_SRC_PASS) or die($db1->error);
        mysqli_select_db($db1, $DB_SRC_NAME) or die($db1->error);
        $result = mysqli_query($db1, "SHOW TABLES;") or die($db1->error);
        $buf = "set foreign_key_checks = 0;\n";
        $constraints = '';
        while ($row = mysqli_fetch_array($result)) {
            $row[0];
            $result2 = mysqli_query($db1, "SHOW CREATE TABLE " . $row[0] . ";") or die($db1->error);
            $res = mysqli_fetch_array($result2);
            if (preg_match("/[ ]*CONSTRAINT[ ]+.*\n/", $res[1], $matches)) {
                $res[1] = preg_replace("/,\n[ ]*CONSTRAINT[ ]+.*\n/", "\n", $res[1]);
                $constraints .= "ALTER TABLE " . $row[0] . " ADD " . trim($matches[0]) . ";\n";
            }
            $buf .= $res[1] . ";\n";
        }
        $buf .= $constraints;
        $buf .= "set foreign_key_checks = 1";
        $db2 = mysqli_connect($DB_DST_HOST, $DB_DST_USER, $DB_DST_PASS) or die($db2->error);
        mysqli_select_db($db2, $DB_DST_NAME) or die($db2->error);
        $queries = explode(';', $buf);
        foreach ($queries as $query) {
            if (!mysqli_query($db2, $query))
                die($db2->error);
        }
        return;
    }


    public function clonedatatables()
    {
        $this->viewBuilder()->layout('admin');
        $connection = ConnectionManager::get('default');
        $results = $connection->execute('SHOW DATABASES')->fetchAll('assoc');
        $this->set('results', $results);
        if ($this->request->is(['post', 'put'])) {
            $clone_from = $this->request->data['clonefrom'];
            $clone_to = $this->request->data['cloneto'];

            foreach ($this->request->data['datatables'] as $value) {
                $copy_qry = "INSERT $clone_to.`$value` SELECT * FROM $clone_from.`$value`";
                //
                // echo $copy_qry; die;
                $connection->execute($copy_qry);
            }
            return $this->redirect(['controller' => 'users', 'action' => 'clonedatatables']);
        }
    }


    public function tablesfetcheddata()
    {
        $dbname = $this->request->data['dbname'];
        //$dbname = "canvas";
        $this->connection($dbname);
        $tables = ConnectionManager::get($dbname)->schemaCollection()->listTables();
        $this->set('tables', $tables);
    }








    public function add($id = null)
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Schools');
        // $this->loadModel('Board');
        $this->loadModel('States');
        $companies = $this->Schools->find('all')->order(['id' => 'DESC'])->toarray();
        $franchise_schools = $this->Schools->find('list', array('keyField' => 'school_database', 'valueField' => 'school_name'))->where(['franchise_type' => '0'])->toarray();

        $allfranchise_schools = $this->Schools->find('list', array('keyField' => 'school_database', 'valueField' => 'school_name'))->toarray();

        $states = $this->States->find('list', array('keyField' => 'id', 'valueField' => 'name'))->toarray();

        // $boards = $this->Board->find('list', array('keyField' => 'id', 'valueField' => 'name'))->toarray();

        $this->set(compact(['companies', 'boards', 'franchise_schools', 'states', 'allfranchise_schools']));
        // $this->loadModel('Schools');
        if (!empty($id)) {
            $school = $this->Schools->get($id);
            $cmp_users = $this->Users->find('all')->where(['c_id' => $id])->first();
            $school->user_name = $cmp_users['user_name'];
            $school->email = $cmp_users['email'];
            $school->password = $cmp_users['confirm_pass'];
            $school->school_contact = $cmp_users['mobile'];
            $school->db = $cmp_users['db'];
            $school->city = $cmp_users['city'];
            $school->state = $cmp_users['state'];
            $school->username = $cmp_users['user_name'];
            $school->is_hostel = $cmp_users['is_hostel'];
            $school->is_transport = $cmp_users['is_transport'];
            $school->is_payroll = $cmp_users['is_payroll'];
            $school->is_store = $cmp_users['is_store'];
            $boardss = explode(',', $cmp_users->board);
            $school->boards = $boardss;
            // pr($school);die;
            $this->set('school', $school);
        }
        if ($this->request->is(['post', 'put'])) {


            try {
                $superadmin = $this->Users->find('all')->where(['role_id' => 101])->first();
                $connection = ConnectionManager::get('default');
                $results = $connection->execute("SHOW DATABASES LIKE '" . $this->request->data['school_database'] . "'")->fetchAll('assoc');


                if (empty($results)) {
                    $dbName = $this->request->data['school_database'];
                    $clondedbname = $this->request->data['clondedbname'];
                    $newdbname = $this->request->data['school_database'];
                    $connection->execute('CREATE DATABASE ' . $dbName);
                    $mysqlUserName = MYSQLUSERNAME;
                    $mysqlPassword = MYSQLPASSWORD;
                    $mysqlDatabaseName = $dbName;
                    $command = 'mysql -u' . $mysqlUserName . ' -p' . $mysqlPassword . ' ' . $mysqlDatabaseName;

                    exec($command, $output = array(), $worked);
                    if ($worked !== 0) {
                        $this->Flash->error(__('Error in importing Company'));
                        return $this->redirect(['action' => 'add']);
                    }
                    $this->newdbcreate($mysqlUserName, $mysqlPassword, $clondedbname, $newdbname);
                } else {
                    $this->Flash->error(__('Sub Domain Already Register With Company.'));
                    return $this->redirect(['action' => 'add']);
                }
                $cmp_data['school_name'] = $this->request->data['school_name'];

                if ($this->request->data['franchise_school']) {
                    $get_franchise_id = $this->Schools->find('all')->where(['school_database' => $this->request->data['franchise_school']])->first();
                    $cmp_data['franchise_type'] = $get_franchise_id['id'];
                } else {
                    $cmp_data['franchise_type'] = 0;
                }

                $cmp_data['school_name'] = $this->request->data['school_name'];
                $cmp_data['school_contact'] = $this->request->data['school_contact'];
                $cmp_data['school_address'] = $this->request->data['school_address'];
                $cmp_data['school_database'] = $this->request->data['school_database'];
                $cmp_data['city'] = $this->request->data['city'];
                $cmp_data['state'] = $this->request->data['state'];

                if (empty($id)) {
                    $patch_cmp = $this->Schools->patchEntity($this->Schools->newEntity(), $cmp_data);
                } else {
                    $school_edit = $this->Schools->get($id);
                    $patch_cmp = $this->Schools->patchEntity($school_edit, $cmp_data);
                }
                if ($result = $this->Schools->save($patch_cmp)) {

                    $data['c_id'] = $result->id;
                    $data['is_payroll'] = 'N';
                    $data['is_store'] = 'N';
                    $data['is_hostel'] = 'N';
                    $data['is_transport'] = 'N';
                    $data['user_name'] = $this->request->data['username'];
                    $data['email'] = $this->request->data['email'];
                    $data['password'] = (new DefaultPasswordHasher)->hash($this->request->data['password']);
                    $data['mobile'] = $this->request->data['school_contact'];
                    $data['board'] = implode(',', $this->request->data['boards']);
                    $data['confirm_pass'] = $this->request->data['password'];
                    $data['state'] = $this->request->data['state'];
                    $data['city'] = $this->request->data['city'];
                    $data['is_admin'] = 'Y';

                    if ($this->request->data['franchise_school_type'] == "franchise") {
                        if ($this->request->data['franchise_school']) {
                            $data['role_id'] = '6';
                        } else {
                            $data['role_id'] = '105';
                        }
                    } else {
                        if ($this->request->data['franchise_school']) {
                            $data['role_id'] = '6';
                        } else {
                            $data['role_id'] = '1';
                        }
                    }

                    $data['db'] = $this->request->data['school_database'];
                    $data['academic_year'] = $superadmin['academic_year'];
                    $email_exist = $this->Users->exists(['email' => $data['email']]);
                    if ($email_exist) {
                        $this->Flash->error(__('Email ' . $data['email'] . ' already exist'));
                        return $this->redirect(['controller' => 'users', 'action' => 'add']);
                    }
                    if ($data['school_database'] == 'test' || $data['school_database'] == 'default') {
                        $this->Flash->error(__('Please enter different database name'));
                        return $this->redirect(['controller' => 'users', 'action' => 'add']);
                    }

                    if (empty($id)) {
                        $new_user = $this->Users->newEntity();
                        $patch_user = $this->Users->patchEntity($new_user, $data);

                        if ($this->Users->save($patch_user)) {

                            $passwordss = (new DefaultPasswordHasher)->hash($this->request->data['password']);
                            $this->connection($cmp_data['school_database']);
                            $conect_new = ConnectionManager::get($cmp_data['school_database']);

                            if ($this->request->data['franchise_school_type'] == "franchise") {
                                if ($this->request->data['franchise_school']) {
                                    $role_data = '6';
                                } else {
                                    $role_data = '105';
                                }
                            } else {
                                if ($this->request->data['franchise_school']) {
                                    $role_data = '6';
                                } else {
                                    $role_data = '1';
                                }
                            }

                            $fracnchise_school_rec = $this->Schools->find('all')->where(['franchise_type' => $get_franchise_id['id']])->toarray();

                            if ($fracnchise_school_rec) {
                                foreach ($fracnchise_school_rec as $fracnchise_school_rec_value) {
                                    $franchise_data[] = $fracnchise_school_rec_value['school_database'];
                                }
                                $franchise_data_db = implode(",", $franchise_data);

                                $sub_db = $this->request->data['franchise_school'];
                                $this->connection($sub_db);
                                $conn = ConnectionManager::get($sub_db);

                                $conn->execute("UPDATE `$sub_db`.`users` SET `franchise_db`='$franchise_data_db' WHERE id=1");
                            } else {
                                $franchise_data = '';
                            }
                            $examterm = 1;

                            $conect_new->execute("Insert into users(c_id,user_name,email,password,role_id,db,mobile,is_payroll,is_store,is_hostel,is_transport,academic_year,is_admin,board,confirm_pass,city,state,examterm) VALUES('" . $data['c_id'] . "','" . $data['user_name'] . "','" . $data['email'] . "','" . $passwordss . "','" . $role_data . "','" . $data['db'] . "','" . $data['mobile'] . "','" . $data['is_payroll'] . "','" . $data['is_store'] . "','" . $data['is_hostel'] . "','" . $data['is_transport'] . "','" . $data['academic_year'] . "','Y','" . $data['board'] . "','" . $data['confirm_pass'] . "','" . $data['city'] . "','" . $data['state'] . "','" . $examterm . "')");


                            $role_datas = '6';
                            $email = 'admin@' . $data['db'] . '.com';
                            $conect_new->execute("Insert into users(c_id,user_name,email,password,role_id,db,mobile,is_payroll,is_store,is_hostel,is_transport,academic_year,is_admin,board,confirm_pass,city,state,examterm) VALUES('" . $data['c_id'] . "','Admin','" . $email . "','" . $passwordss . "','" . $role_datas . "','" . $data['db'] . "','" . $data['mobile'] . "','" . $data['is_payroll'] . "','" . $data['is_store'] . "','" . $data['is_hostel'] . "','" . $data['is_transport'] . "','" . $data['academic_year'] . "','Y','" . $data['board'] . "','" . $data['confirm_pass'] . "','" . $data['city'] . "','" . $data['state'] . "','" . $examterm . "')");

                            $this->Flash->success(__('Company Created successfully'));
                            return $this->redirect(['controller' => 'users', 'action' => 'add']);
                        }
                    } else {
                        $users_cid = $this->Users->find('all')->where(['c_id' => $id])->first();
                        if (empty($users_cid)) {
                            $users_cid = $this->Users->newEntity();
                        }
                        $patch_user = $this->Users->patchEntity($users_cid, $data);
                        if ($this->Users->save($patch_user)) {
                            $passwordss = (new DefaultPasswordHasher)->hash($this->request->data['password']);
                            $this->connection($cmp_data['school_database']);
                            $conn = ConnectionManager::get($cmp_data['school_database']);
                            $conn->execute("UPDATE users SET user_name='" . $data['user_name'] . "',email='" . $data['email'] . "',password='" . $passwordss . "',db='" . $data['db'] . "',mobile='" . $data['mobile'] . "',is_payroll='" . $data['is_payroll'] . "',is_store='" . $data['is_store'] . "',is_hostel='" . $data['is_hostel'] . "',is_transport='" . $data['is_transport'] . "',academic_year='" . $data['academic_year'] . "',is_admin='" . $data['is_admin'] . "',board='" . $data['board'] . "',confirm_pass='" . $data['confirm_pass'] . "',state='" . $data['state'] . "',city='" . $data['city'] . "' WHERE role_id='6' AND c_id ='" . $id . "'");

                            $this->Flash->success(__('Company Upadted successfully'));
                            return $this->redirect(['controller' => 'users', 'action' => 'add']);
                        }
                    }
                }
            } catch (\PDOException $e) {
                $this->Flash->error(__($e));
                return $this->redirect(['controller' => 'users', 'action' => 'add']);
            }
        }
    }

    public function gettable()
    {
        $this->loadModel('Examtemplates');
        $this->loadModel('Examtemplategroup');


        // pr($this->request->data);die;
        $examname = $this->request->data['examname'];
        $sch_bord = $this->request->data['sch_bord'];
        $arra = count($sch_bord);

        $group_name = $this->Examtemplategroup->find('all')->where(['status' => 1])->toarray();

        if ($examname == 1) {

            $grname = $this->Examtemplates->find('all')->where(['term' => $examname])->toarray();
        } else {

            $grname = $this->Examtemplates->find('all')->toarray();
        }
        // pr($term2grname);die;
        $this->set(compact('grname', 'examname', 'arra', 'group_name'));

    }


    public function school_status($id, $currentStatus)
    {
        $school = $this->Schools->get($id);

        $school->status = ($currentStatus == 'Y') ? 'N' : 'Y';

        if ($this->Schools->save($school)) {
            $this->Flash->success(__('Status has been updated successfully.'));
        } else {
            $this->Flash->error(__('Unable to update status. Please try again.'));
        }

        return $this->redirect($this->referer());
    }

}
