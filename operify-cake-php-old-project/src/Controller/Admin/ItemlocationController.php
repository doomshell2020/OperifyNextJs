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
use Cake\Routing\Router;

class ItemlocationController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
    }

    // index page itemlocation manager   
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        $users = $this->Itemlocation->find('all')->where(['Itemlocation.parent' => 0])->order(['Itemlocation.id' => 'DESC'])->toarray();
        $this->set(compact('users'));
    }
    // Add item location
    public function add()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        $cat = $this->Itemlocation->newEntity();
        if ($this->request->is(['post'])) {
            //pr($this->request->data); die;
            $item['location_name'] = $this->request->data['location_name'];
            $item['parent'] = 0;
            $item['description'] = $this->request->data['description'];
            $item['added_time'] = date('Y-m-d H:i:s');
            $pnewdetail = $this->Itemlocation->patchEntity($cat, $item);
            if ($resustnew = $this->Itemlocation->save($pnewdetail)) {
                $this->Flash->success(__('Item location successfully added.'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }



    //edit item location
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        $itemcat = $this->Itemlocation->find('All')->where(['Itemlocation.id' => $id])->first();
        $this->set('itemlocation', $itemcat);
        $cat = $this->Itemlocation->get($id);
        $conn = ConnectionManager::get('default');
        if ($this->request->is(['put'])) {
            $item['location_name'] = $this->request->data['location_name'];
            $item['parent'] = $this->request->data['parent'];
            $item['description'] = $this->request->data['description'];
            $item['updated_time'] = date('Y-m-d H:i:s');

            $cats = $this->Itemlocation->patchEntity($cat, $item);
            if ($resust = $this->Itemlocation->save($cats)) {
                $this->Flash->success(__('Item location successfully updated.'));
                return $this->redirect(['action' => 'index']);
            }
            //die;


        }
    }

    //add sub location item location 
    public function addsublocation($id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        $this->set('id', $id);
        $itemcat = $this->Itemlocation->find('All')->where(['Itemlocation.id' => $id])->toarray();
        $cat = $this->Itemlocation->newEntity();
        if ($this->request->is(['post'])) {
            $item['location_name'] = $this->request->data['location_name'];
            $item['parent'] = $this->request->data['parent'];
            $item['description'] = $this->request->data['description'];
            $item['added_time'] = date('Y-m-d H:i:s');
            $pnewdetail = $this->Itemlocation->patchEntity($cat, $item);
            if ($resustnew = $this->Itemlocation->save($pnewdetail)) {
                $this->Flash->success(__('Item location successfully added.'));
                //$this->redirect( Router::url( $this->referer(), true ));
                return $this->redirect(['action' => 'viewsublocation/'.$this->request->data['parent']]);
              //  return $this->redirect(['action' => 'index']);
            }
        }
    }
    //Edit sub location item location 
    public function editsublocation($id = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        
        $parent_id=$_GET['p_id'];
        $itemcat = $this->Itemlocation->find('All')->where(['Itemlocation.id' => $id])->first();
        $this->set('itemlocation', $itemcat);
        $location = $this->Itemlocation->find('list', ['keyField' => 'id', 'valueField' => 'location_name'])->where(['Itemlocation.parent' => 0])->order(['Itemlocation.id' => 'asc'])->toarray();
        $this->set('location', $location);
        $cat = $this->Itemlocation->get($id);
        if ($this->request->is(['put'])) {
            //   pr($this->request->data); die;
            $item['location_name'] = $this->request->data['location_name'];
            $item['parent'] = $parent_id;
            $item['description'] = $this->request->data['description'];
            $item['updated_time'] = date('Y-m-d H:i:s');
            $cats = $this->Itemlocation->patchEntity($cat, $item);
            if ($resust = $this->Itemlocation->save($cats)) {
                $this->Flash->success(__('Item location successfully updated.'));
                return $this->redirect(['action' => 'viewsublocation/', $parent_id]);
            }
        }
    }

    // item location status
    public function status($id, $status)
    {
        $this->loadModel('Itemlocation');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Itemlocation->get($id);
                $user->status = $status;
                if ($this->Itemlocation->save($user)) {
                    $this->Flash->success(__('Item location status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Itemlocation->get($id);
                $user->status = $status;
                if ($this->Itemlocation->save($user)) {
                    $this->Flash->success(__('Item location status has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }
    //delete itemlocation records
    public function delete($id = null)
    {
        $this->loadModel('Itemlocation');
        try {
            $user = $this->Itemlocation->get($id);
            if ($this->Itemlocation->delete($user)) {
                $this->Flash->success(__('The Item location with id: {0} has been deleted.', h($id)));
                return $this->redirect(['action' => 'index']);
            }
        } catch (\PDOException $e) {
            $this->Flash->error(__('This Item location is used so you cannot delete this Item location detail'));
            $this->set('error', $error);
            return $this->redirect(['action' => 'index']);
        }
        die;
    }
    //view subitem location manager
    public function viewsublocation($id)
    {
        // echo "hello" ; die; 
        $this->set('id', $id);
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemlocation');
        $users = $this->Itemlocation->find('all')->where(['Itemlocation.parent' => $id])->order(['Itemlocation.id' => 'DESC'])->toarray();
        // pr($users); die;
        $this->set(compact('users'));
    }
    // subitem status 
    public function substatus($id, $status)
    {
        $this->loadModel('Itemlocation');
        if (isset($id) && !empty($id)) {
            if ($status == 'Y') {
                $status = 'N';
                $user = $this->Itemlocation->get($id);
                $user->status = $status;
                if ($this->Itemlocation->save($user)) {
                    $this->Flash->success(__('Item location status has been updated.'));
                    $this->redirect( Router::url( $this->referer(), true ));
                   // return $this->redirect(['action' => 'index']);
                }
            } else {
                $status = 'Y';
                $user = $this->Itemlocation->get($id);
                $user->status = $status;
                if ($this->Itemlocation->save($user)) {
                    $this->Flash->success(__('Item location status has been updated.'));
                    $this->redirect( Router::url( $this->referer(), true ));
                   // return $this->redirect(['action' => 'viewsublocation']);
                }
            }
        }
    }
    //delete subitem
    public function subdelete($id = null,$sub_id)
    {
        $this->loadModel('Itemlocation');
        try {
            $user = $this->Itemlocation->get($id);
            if ($this->Itemlocation->delete($user)) {
                $this->Flash->success(__('The Item location with id: {0} has been deleted.', h($id)));
                //return $this->redirect(['action' => 'viewsublocation']);
                return $this->redirect(['action' => 'viewsublocation/', $sub_id]);
            }
        } catch (\PDOException $e) {

            $this->Flash->error(__('This Item location is used so you cannot delete this Item location detail'));
            $this->set('error', $error);
           // return $this->redirect(['action' => 'index']);
           return $this->redirect(['action' => 'viewsublocation/', $sub_id]);
        }
        die;
    }


    public function supplertoheadoffice()
    {

        $this->response->type('pdf');
    }

    public function headofficetofranchise()
    {
        $this->response->type('pdf');
    }

    public function franchisetoparnogst()
    {
        $this->response->type('pdf');
    }

    public function franchisetocaparentbill()
    {

        $this->response->type('pdf');
    }

    public function newfeesrecipt()
    {

        $this->response->type('pdf');
    }



}
