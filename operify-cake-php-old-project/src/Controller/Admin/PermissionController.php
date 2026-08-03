<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\View\Helper\CommanHelper;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;

class PermissionController extends AppController
{
    //initialize component
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('PermissionModules');
        $this->loadModel('PermissionAccess');
        $this->loadModel('PermissionLabel');
        $this->loadModel('Manager');
        $this->loadModel('Roles');

        $this->loadModel('Users');
    }

    public function index()
    {

        $this->viewBuilder()->layout('admin');
        $this->loadModel('Manager');

        $users_role = $this->Roles->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])
        ->where(['Roles.status' => 'Y','Roles.id not in' => '101'])
        ->order(['Roles.name' => 'ASC'])
        ->toArray();
        

        $manager_list = $this->Manager->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['Manager.name' => 'ASC'])->toArray();

        $this->set(compact('users_role', 'manager_list'));


        if (!empty($this->request->is(['post'], ['put']))) {

            $data = $this->request->data;
            if (!empty($data)) {
                $menuName = $data['manager_id'];
                $menuURL = $data['url'];

                // Check if the module already exists
                $moduleExist = $this->PermissionLabel->find('all')
                    ->where(['PermissionLabel.manager_id' => $menuName, 'PermissionLabel.url' => $menuURL])
                    ->first();
                if (!$moduleExist) {
                    $datas = [
                        'manager_id' => $data['manager_id'] ?? null,
                        'label_name' => $data['label_name'] ?? null,
                        'created' => date("Y-m-d H:i:s"),
                        'url' => $data['url'] ?? null,
                    ];

                    // Create a new entity and patch it with the data
                    $newEntity = $this->PermissionLabel->newEntity();
                    $patchedEntity = $this->PermissionLabel->patchEntity($newEntity, $datas);

                    // Attempt to save the entity
                    if ($this->PermissionLabel->save($patchedEntity)) {
                        $this->Flash->success(__('Url added successfully.'));
                    } else {
                        $this->Flash->error(__('The Url could not be added. Please try again.'));
                    }
                } else {
                    $this->Flash->error(__('The Url already exists.'));
                }
            } else {
                $this->Flash->error(__('Invalid data received.'));
            }

            return $this->redirect(['action' => 'index']);
        }
    }


    public function search()
    {

        $role_id = $this->request->data['role_id'];

        // Fetch all modules
        $modules = $this->PermissionLabel->find()->contain('Manager')
            ->select(['manager_id', 'label_name', 'id','url'])
            ->order(['Manager.name' => 'ASC', 'PermissionLabel.label_name' => 'ASC'])
            ->toArray();
        // Fetch permissions for the given role
        $grantedPermissions = $this->PermissionAccess->find()
            ->select(['p_lable_id', 'is_permission'])
            ->where(['PermissionAccess.role_id' => $role_id])
            ->toArray();

        // Create an associative array for quick lookup of permissions
        $grantedPermissionMap = [];
        foreach ($grantedPermissions as $permission) {
            $grantedPermissionMap[$permission->p_lable_id] = $permission->is_permission;
        }
        // Group modules by manager_id and set checked state based on is_permission
        $groupedModules = [];
        foreach ($modules as $module) {
            $isChecked = isset($grantedPermissionMap[$module->id]) && $grantedPermissionMap[$module->id] == 1;
            $groupedModules[$module->manager_id][] = [
                'label_name' => $module->label_name,
                'p_lable_id' => $module->id,
                'url'=> $module->url,
                'checked' => $isChecked
            ];
        }
// pr($groupedModules);die;
        $this->set('groupedModules', $groupedModules);
        $this->set('_serialize', ['groupedModules']);
    }


    public function updaterights()
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->data;
            $userRole = $data['role_id'];
            $permissions = $data['permissions'];


            foreach ($permissions as $permissionId => $actions) {

                $is_permission = $actions['is_permission'];

                // Find existing permission record
                $existingPermission = $this->PermissionAccess->find()
                    ->where([
                        'role_id' => $userRole,
                        'p_lable_id' => $permissionId
                    ])
                    ->first();

                if ($existingPermission) {
                    // Update the permission in the database
                    $existingPermission->is_permission = $is_permission;
                    $existingPermission->updated = date('Y-m-d');
                    $this->PermissionAccess->save($existingPermission);
                } else {
                    // Insert a new permission record if none exists
                    $newPermission = $this->PermissionAccess->newEntity([
                        'role_id' => $userRole,
                        'p_lable_id' => $permissionId,
                        'is_permission' => $is_permission,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => null
                    ]);
                    $this->PermissionAccess->save($newPermission);
                }
            }

            $this->Flash->success(__('Permissions updated successfully.'));
            return $this->redirect(['action' => 'index']);
        }
    }
    public function add()
    {
        $this->loadModel('Manager');

        if (!empty($this->request->is(['post'], ['put']))) {

            $managerName = $this->request->data['name'];
            $managerExist = $this->Manager->find('all')
                ->where(['Manager.name' => $managerName])
                ->first();

            if (!$managerExist) {
                $datas = [
                    'name' => $managerName,
                    'created' => date("Y-m-d H:i:s")
                ];

                $newEntity = $this->Manager->newEntity();
                $patchedEntity = $this->Manager->patchEntity($newEntity, $datas);

                if ($this->Manager->save($patchedEntity)) {
                    $this->Flash->success(__('Manager added successfully'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Failed to add manager. Please try again.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('Manager already exists.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function dup_name()
    {
        $name = $this->request->data['name'];
        // $e_id = $this->request->data['e_id'];
        echo $Employees = $this->Permission_manager->find('all')->where(['Permission_manager.name' => $name])->count();
        die;
    }
}
