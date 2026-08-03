<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;

class RolesController extends AppController
{
    //initialize component
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Roles');
        $this->loadModel('Users');
        // $this->loadModel('Board');
    }

    public function index($id = null)
    {
        $this->viewBuilder()->layout('admin');

        // Show data in listing
        $roles = $this->Roles->find('list')->where(['id NOT IN' => ['101', '1','105','6']])->order(['name' => 'ASC'])->toArray();
        $this->set('roles', $roles);

        $allusers = $this->Users->find('all')->where(['Users.role_id NOT IN' => ['101', '1','105','6']])->toArray();
        $this->set('allusers', $allusers);

        $ems = $this->Users->find('all')->where(['is_admin' => 'Y', 'role_id IN' => ['1', '105']])->first();
        $academic_year = $ems['academic_year'];
        $this->set('board', $board);
        $this->set('roles', $roles);
        $this->set('academic_year', $academic_year);

        if (isset($id) && !empty($id)) {
            // Using for edit
            $rolesnew = $this->Users->get($id);
        } else {
            $rolesnew = $this->Users->newEntity();
        }
        $this->set('rolesnew', $rolesnew);

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['id']) {
                $id = $this->request->data['id'];
            }

            // Check if mobile number already exists
            $mobileExists = $this->Users->find()
                ->where(['mobile' => $this->request->data['mobile']])
                ->first();

            if ($mobileExists && (!$id || $mobileExists->id != $id)) {
                // If the mobile number exists and it's not the current user, show an error
                $this->Flash->error(__("The mobile number already exists. Please use a different number."));
                return $this->redirect(['action' => 'index']);
            }

            if ($this->request->data['password'] == $this->request->data['confirm_pass']) {
                $ems = $this->request->session()->read('Auth.User');
                $this->request->data['c_id'] = $ems['c_id'];
                $this->request->data['academic_year'] = $ems['academic_year'];
                $this->request->data['db'] = $ems['db'];

                if ($this->request->data['board'] == '') {
                    $this->request->data['board'] = $ems;
                }

                $this->request->data['password'] = (new DefaultPasswordHasher)->hash($this->request->data['password']);
                $modes = $this->Users->patchEntity($rolesnew, $this->request->data);

                $users_add = $this->Users->save($modes);

                if ($users_add) {
                    $username = $users_add['user_name'];
                    $cid = $users_add['c_id'];
                    $academic_year = $users_add['academic_year'];
                    $email = $users_add['email'];
                    $password = $users_add['password'];
                    $c_password = $users_add['confirm_pass'];
                    $cdate = date('Y-m-d H:i:s');
                    $roles = $users_add['role_id'];
                    $database_name = $users_add['db'];
                    $Bord = $users_add['board'];
                    $mobile = $users_add['mobile'];
                    $db = DB_NAME;

                    $conn = ConnectionManager::get('default');
                    $inserts = "INSERT INTO `$db`.`users` (`user_name`,`c_id`,`academic_year`, `email`,`tech_id`, `password`, `confirm_pass`, `created`,  `role_id`, `db`,`board`,`mobile`) VALUES
                     ('$username','$cid','$academic_year','$email', '0','$password','$c_password', '$cdate',$roles,'$database_name','$Bord','$mobile')";
                    $conn->execute($inserts);

                    $this->Flash->success(__('Role Associated User has been updated, we can now login with this saved role login credentials.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Unable to save user. Please try again.'));
                }
            } else {
                $this->Flash->error(__("Your password and confirm password don't match, try again."));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    // public function add($id = null)
    // {
    //     $this->viewBuilder()->layout('admin');
    //     if (isset($id) && !empty($id)) {
    //         //using for edit
    //         $modes = $this->Roles->get($id);
    //     } else {
    //         //using for new entry
    //         $modes = $this->Roles->newEntity();
    //     }
    //     if ($this->request->is(['post', 'put'])) {
    //         // save all data in database
    //         $modes = $this->Roles->patchEntity($modes, $this->request->data);
    //         //pr($locations); die;
    //         if ($this->Roles->save($modes)) {
    //             $this->Flash->success(__('Roles User has been saved.'));
    //             return $this->redirect(['action' => 'index']);
    //         } else { //pr($classes->errors());
    //             //validation error
    //             if ($modes->errors()) {
    //                 $error_msg = [];
    //                 foreach ($modes->errors() as $errors) {
    //                     if (is_array($errors)) {
    //                         foreach ($errors as $error) {
    //                             $error_msg[] = $error;
    //                         }
    //                     } else {
    //                         $error_msg[] = $errors;
    //                     }
    //                 }
    //                 if (!empty($error_msg)) {
    //                     $this->Flash->error(
    //                         __("Please fix the following error(s): " . implode("\n \r", $error_msg))
    //                     );
    //                 }
    //             }
    //         }
    //     }

    //     $this->set('modes', $modes);
    // }



    //     public function add($id = null)
    // {
    //     $this->viewBuilder()->setLayout('admin');

    //     if (isset($id) && !empty($id)) {
    //         $modes = $this->Roles->get($id);
    //     } else {
    //         $modes = $this->Roles->newEntity();
    //     }

    //     if ($this->request->is(['post', 'put'])) {
    //         $data = $this->request->getData();

    //         $this->loadModel('Users');

    //         $existingUser = $this->Users->find()
    //             ->where(['mobile' => $data['mobile']])
    //             ->first();
    //             pr($existingUser);die;

    //         if (!empty($existingUser) && (!$id || $existingUser->id != $id)) {
    //             $this->Flash->error(__('This mobile number is already registered with another user. Please use a different number.'));
    //         } else {
    //             $modes = $this->Roles->patchEntity($modes, $data);

    //             if ($this->Roles->save($modes)) {
    //                 $this->Flash->success(__('Roles User has been saved.'));
    //                 return $this->redirect(['action' => 'index']);
    //             } else {
    //                 if ($modes->getErrors()) {
    //                     $error_msg = [];
    //                     foreach ($modes->getErrors() as $errors) {
    //                         if (is_array($errors)) {
    //                             foreach ($errors as $error) {
    //                                 $error_msg[] = $error;
    //                             }
    //                         } else {
    //                             $error_msg[] = $errors;
    //                         }
    //                     }
    //                     if (!empty($error_msg)) {
    //                         $this->Flash->error(
    //                             __("Please fix the following error(s): " . implode("\n", $error_msg))
    //                         );
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     $this->set('modes', $modes);
    // }






    public function sort()
    {
        $this->viewBuilder()->layout('admin');
        $id = $this->request->data[id];
        if (isset($id) && !empty($id)) {
            //using for edit
            $modes = $this->Roles->get($id);
        } else {
            //using for new entry
            $modes = $this->Roles->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            $modes->sort = $this->request->data['sort'];

            if ($this->Roles->save($modes)) {
                echo $modes['sort'];
            } else {
                echo 'wrong';
            }
        }
        die;
    }

    //delete functionality
    public function delete($id)
    {
        //$this->request->allowMethod(['post', 'delete']);
        $modes = $this->Users->get($id);
        //delete pariticular entry
        try {

            if ($this->Users->delete($modes)) {

                $this->Flash->success(__('Users with id: {0} has been deleted.', ($id)));
                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {
            //  $error = 'The item you are trying to delete is associated with other records';

            //$this->Session->setFlash(__(' Lader all ready exists), 'flash/Error');
            return $this->redirect(['action' => 'index']);
        }
    }
    //status update functionality
    public function status($id, $status)
    {
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                //status update
                $modes = $this->Roles->get($id);
                $modes->status = $status;
                if ($this->Roles->save($modes)) {
                    $this->Flash->success(__('Roles status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                //status update
                $modes = $this->Roles->get($id);
                $modes->status = $status;
                if ($this->Roles->save($modes)) {
                    $this->Flash->success(__('Roles status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }
}
