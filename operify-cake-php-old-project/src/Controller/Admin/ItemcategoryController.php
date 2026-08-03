<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;


class ItemcategoryController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemcategory');
        $users = $this->Itemcategory->find('all')->where(['Itemcategory.status' => 'Y'])->order(['Itemcategory.category_name' => 'Asc'])->toarray();
        //pr($users); die;
        $this->set(compact('users'));
    }

    public function add($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemcategory');
        $cat = $this->Itemcategory->newEntity();
        if ($this->request->is(['post'])) {

            $attnExist = $this->Itemcategory->exists(['category_name' => trim($this->request->data['category_name'])]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Itemcategory already exists.'));
                return $this->redirect(['action' => 'Add']);
            }

            $item['category_name'] = $this->request->data['category_name'];
            $item['description'] = $this->removeEmojis($this->request->data['description']);
            $item['added_time'] = date('Y-m-d H:i:s');

            $pnewdetail = $this->Itemcategory->patchEntity($cat, $item);
            if ($resustnew = $this->Itemcategory->save($pnewdetail)) {
                $this->Flash->success(__('Item category successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
            //pr($resustnew);  die;
        }
    }




    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemcategory');

        $itemcat = $this->Itemcategory->find('All')->where(['Itemcategory.id' => $id])->first();
        //pr($purid); die;
        $this->set('itemcategory', $itemcat);

        $cat = $this->Itemcategory->get($id);
        //pr($cat); die;
        $conn = ConnectionManager::get('default');
        if ($this->request->is(['put'])) {


            $attnExist = $this->Itemcategory->exists(['category_name' => trim($this->request->data['category_name']), 'NOT' => ['id' => $id]]);
            if ($attnExist) {
                $this->Flash->error(__('Your entered Itemcategory already exists.'));
                return $this->redirect(['action' => 'Edit/', $id]);
            }
            $item['category_name'] = $this->request->data['category_name'];
            $item['description'] = $this->removeEmojis($this->request->data['description']);
            $item['updated_time'] = date('Y-m-d H:i:s');

            $cats = $this->Itemcategory->patchEntity($cat, $item);
            if ($resust = $this->Itemcategory->save($cats)) {
                $this->Flash->success(__('Item category successfully updated.'));
                return $this->redirect(['action' => 'index']);
            }
            //die;


        }
    }


    // public function addsubcategory(){
    //         $this->viewBuilder()->layout('admin');       
    //         $this->loadModel('Itemcategory');
    //         $categary = $this->Itemcategory->find('list',['keyField' => 'id','valueField' => 'category_name'])->where(['Itemcategory.parent'=>'0'])->order(['Itemcategory.id' => 'asc'])->toarray();
    //         //pr($categary); die;
    //         $this->set('categary', $categary);

    //         $cat = $this->Itemcategory->newEntity();
    //         if ($this->request->is(['post'])) {
    //             //pr($this->request->data); die;
    //             $item['category_name'] = $this->request->data['category_name'];
    //             $item['parent'] = $this->request->data['parent'];   
    //             $item['description'] = $this->request->data['description'];
    //             $item['added_time'] = date('Y-m-d H:i:s');

    //             $pnewdetail = $this->Itemcategory->patchEntity($cat, $item);
    //             if($resustnew = $this->Itemcategory->save($pnewdetail)){
    //                 $this->Flash->success(__('Item category successfully added.'));
    //                 return $this->redirect(['action' => 'index']);
    //             } 
    //         }

    //     }    

    //     public function editsubcategory($id=null){ 
    //     $this->viewBuilder()->layout('admin');
    //     $this->loadModel('Itemcategory');    
    //     $itemcat = $this->Itemcategory->find('All')->where(['Itemcategory.id' => $id])->first();
    //     //pr($purid); die;
    //     $this->set('itemcategory', $itemcat);

    //     $categary = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->where(['Itemcategory.parent' => 0])->order(['Itemcategory.id' => 'asc'])->toarray();
    //     //pr($categary); die;
    //     $this->set('categary', $categary);


    //     $cat = $this->Itemcategory->get($id);
    //     //pr($cat); die;
    //      $conn = ConnectionManager::get('default');
    //      if ($this->request->is(['put'])) {
    //         $item['category_name'] = $this->request->data['category_name'];
    //         $item['parent'] = $this->request->data['parent'];
    //         $item['description'] = $this->request->data['description'];
    //         $item['updated_time'] = date('Y-m-d H:i:s');

    //         $cats = $this->Itemcategory->patchEntity($cat, $item);
    //         if ($resust = $this->Itemcategory->save($cats)) {
    //             $this->Flash->success(__('Item category successfully updated.'));
    //             return $this->redirect(['action' => 'index']);
    //         }
    //             //die;


    //         }
    //     }


    public function status($id, $status)
    {
        $this->loadModel('Itemcategory');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Itemcategory->get($id);

                $user->status = $status;
                if ($this->Itemcategory->save($user)) {
                    $this->Flash->success(__('Item category status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Itemcategory->get($id);
                $user->status = $status;
                if ($this->Itemcategory->save($user)) {
                    $this->Flash->success(__('Item category status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $this->loadModel('Itemcategory');

        try {
            $user = $this->Itemcategory->get($id);
            $user->status = 'N';
            if ($this->Itemcategory->save($user)) {
                $this->Flash->success(__('The Item category with id: {0} has been deleted.', h($id)));
                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {

            $this->Flash->error(__('This Item category is used so you cannot delete this Item category detail'));
            $this->set('error', $error);
            return $this->redirect(['action' => 'index']);
        }
        die;
    }

    public function searchitem()
    {

        $this->loadModel('Itemcategory');

        $name = $this->request->data['category_name'];
        $cond = [];
        if (isset($name) && $name != '') {
            $cond['Itemcategory.category_name LIKE'] = '%' . trim($name) . '%';
        }

        $users = $this->Itemcategory->find('all')->where([$cond])->order(['Itemcategory.category_name' => 'ASC'])->toarray();
        $this->set('user', $users);
    }



    public function printstatus($id, $status)
    {
        $this->loadModel('Itemcategory');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Itemcategory->get($id);

                $user->is_print = $status;
                if ($this->Itemcategory->save($user)) {
                    $this->Flash->error(__('Not added in weekly stock excel report.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Itemcategory->get($id);
                $user->is_print = $status;
                if ($this->Itemcategory->save($user)) {
                    $this->Flash->success(__('Added in weekly stock excel report.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }
}
