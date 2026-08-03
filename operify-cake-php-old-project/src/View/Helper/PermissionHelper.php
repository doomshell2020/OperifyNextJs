<?php

namespace App\View\Helper;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;
use Cake\View\View;
use Firebase\JWT\JWT;

class PermissionHelper extends Helper
{

    // initialize() hook is available since 3.2. For prior versions you can
    // override the constructor if required.
    public function initialize(array $config)
    {
    }
    public function permissioncheck($url = null)
    {
        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        TableRegistry::clear();
        $primary_users = '';
        $this->setPrimaryDatabase();
        $primary_connection = ConnectionManager::get('primary_db');

        // $primary_label= TableRegistry::get('PermissionLabel', ['connection' => $primary_connection])
        // ->find('all')->where(['url LIKE' => '%'.$url.'%'])
        // ->first();

        $primary_users = TableRegistry::get('PermissionAccess', ['connection' => $primary_connection])
            ->find('all')->where(['PermissionAccess.role_id' => $rolepresent, 'PermissionAccess.is_permission' => 1])
            ->toarray();
        foreach ($primary_users as $key => $value) {
            $primary_label = TableRegistry::get('PermissionLabel', ['connection' => $primary_connection])
                ->find('all')->where(['id' => $value['p_lable_id']])
                ->first();

            $urlfetch[] = $primary_label['url'];

        }

        return $urlfetch;




    }
    public function managerName($id = '')
    {
    
        $articles = TableRegistry::get('Permission_manager');
        return $articles->find('all')->where(['Permission_manager.id' => $id])->order(['Permission_manager.name' => 'ASC'])->first();
    }

    public function permissioncount($name = null)
    {

        $rolepresent = $this->request->session()->read('Auth.User.role_id');

        TableRegistry::clear();

        $this->setPrimaryDatabase();
        $primary_connection2 = ConnectionManager::get('primary_db');

        if ($name === 'Store' || $name === 'Subject') {
            $manager1 = TableRegistry::get('Manager', ['connection' => $primary_connection2])
                ->find()
                ->where(['Manager.name LIKE' => $name . '%'])
                ->toArray();
            $man_id = [];
            foreach ($manager1 as $mang) {

                $man_id[] = $mang->id;
            }

            $parmis_label = TableRegistry::get('PermissionLabel', ['connection' => $primary_connection2])
                ->find('all')
                ->where(['manager_id IN' => $man_id])
                ->toArray();
            $p_id = [];

            foreach ($parmis_label as $par) {
                if (isset($par->id) && is_numeric($par->id)) {
                    $p_id[] = (int) $par->id;
                }
            }
            if (!empty($p_id)) {
                $parmis_access = TableRegistry::get('PermissionAccess', ['connection' => $primary_connection2])
                    ->find('all')->where(['PermissionAccess.role_id' => $rolepresent, 'PermissionAccess.is_permission' => 1, 'PermissionAccess.p_lable_id IN' => $p_id])->toArray();

            } else {
                $parmis_access = '';
            }
        } else {
            $manager = TableRegistry::get('Manager', ['connection' => $primary_connection2])
                ->find()
                ->where(['Manager.name LIKE' => $name . '%'])
                ->first();
            if ($manager) {
                $manager_id = $manager->id;

                $parmis_label = TableRegistry::get('PermissionLabel', ['connection' => $primary_connection2])
                    ->find('all')
                    ->where(['manager_id' => $manager_id])
                    ->toArray();

                $p_id = [];

                foreach ($parmis_label as $par) {
                    if (isset($par->id) && is_numeric($par->id)) {
                        $p_id[] = (int) $par->id;
                    }
                }



                if (!empty($p_id)) {
                    $parmis_access = TableRegistry::get('PermissionAccess', ['connection' => $primary_connection2])
                        ->find('all')->where(['PermissionAccess.role_id' => $rolepresent, 'PermissionAccess.is_permission' => 1, 'PermissionAccess.p_lable_id IN' => $p_id])->toArray();



                } else {
                    $parmis_access = '';
                }
            } else {

                $parmis_access = '';
            }
        }
        return $parmis_access;





    }

    public function setPrimaryDatabase()
    {
        // Check if the connection already exists
        if (ConnectionManager::config('primary_db')) {
            // Drop the existing connection
            ConnectionManager::drop('primary_db');
        }
        // Primary Database connection
        ConnectionManager::Config('primary_db', [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => DBHOSTNAME,
            'username' => MYSQLUSERNAME,
            'password' => MYSQLPASSWORD,
            'database' => 'operify',
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ]);
        // return;
    }

}