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


include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class JobseekerController extends AppController
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
        $this->loadModel('Jobseeker'); 
        $job=$this->Jobseeker->find('all')->order(['Jobseeker.id' => 'DESC'])->toarray();
        $this->set('job',$job);
    }




    //add function in additem
    public function add()
    {
        $this->viewBuilder()->layout('admin');
		$this->loadModel('Jobseeker');
		

		if ($this->request->is('post'))  {
            $jobs = $this->Jobseeker->newEntity();

            $item['name'] = $this->request->data['name'];
            $item['mobile'] = $this->request->data['mobile'];
            $item['country'] = $this->request->data['country'];
            $item['address'] = $this->request->data['address'];
            $item['gender'] = $this->request->data['gender'];
            $item['desprition'] = $this->request->data['desprition'];
            $item['skills'] = $this->request->data['skills'];
            
            $savepack = $this->Jobseeker->patchEntity($jobs,$item);
        		$results=$this->Jobseeker->save($savepack);
		if ($results){
		  $this->Flash->success(__('Jobseeker has been saved.'));
		  return $this->redirect(['action' => 'index']);  
		}else{
		  $this->Flash->error(__('Jobseeker not saved.'));
		  return $this->redirect(['action' => 'index']);  
		}
		 }
        
        }



    
    public function edit($id)
    { 
        $this->viewBuilder()->layout('admin');
		$this->loadModel('Jobseeker');
		
        $job = $this->Jobseeker->get($id);
		$this->set(compact('job'));
        
        if ($this->request->is('post')) 
        { 
            $item['name'] = $this->request->data['name'];
            $item['mobile'] = $this->request->data['mobile'];
            $item['country'] = $this->request->data['country'];
            $item['address'] = $this->request->data['address'];
            $item['gender'] = $this->request->data['gender'];
            $item['desprition'] = $this->request->data['desprition'];
            $item['skills'] = $this->request->data['skills'];
            $item['updated_at'] = date("Y-m-d") ;

            
            $savepack = $this->Jobseeker->patchEntity($job,$item);
            $results=$this->Jobseeker->save($savepack);

            if ($results){
                $this->Flash->success(__('Jobseeker has been updated.'));
                return $this->redirect(['action' => 'index']);  
            }else{
                  $this->Flash->error(__('Jobseeker not Updated.'));
                return $this->redirect(['action' => 'index']);  
              } 
        }
    }



 
    public function delete($id)
    {
        $this->loadModel('Jobseeker');
        $delete = $this->Jobseeker->get($id);
        // pr($delete);die;
		  if($delete){
			$this->Jobseeker->deleteAll(['Jobseeker.id' => $id]); 
			// $this->Jobseeker->delete([$delete]); 

			$this->Flash->success(__('Jobseeker has been deleted successfully.'));
			return $this->redirect(['action' => 'index']);
		  }
    }


    public function searchitem()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemlocation');
        $this->loadModel('Itemcategory');

        $reqdata = $_GET;
        $item = $reqdata['item_name'];
        $location = $reqdata['location_name'];
        $itemtype = $reqdata['itemtype'];
        $category = $reqdata['category_id'];
        $company = $reqdata['cname'];
        $cond = [];
        if (isset($item) && $item != '') {
            $cond['Additem.item_name LIKE'] = '%' . trim($item) . '%';
        }
        if (isset($location) && $location != '') {

            $cond['Additem.location_name'] = $location;
        }
        if (isset($itemtype) && $itemtype != '') {
            $cond['Additem.itemtype'] = $itemtype;
        }
        if (isset($category) && $category != '') {
            $cond['Additem.category_id'] = $category;
        }

        if (isset($company) && $company != '') {
            $cond['Additem.cname'] = $company;
        }

        $this->request->session()->write('cond', $cond);
        $user = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where(['Additem.status' => 'Y', $cond])->order(['Additem.item_name' => 'asc']);
        $user = $this->paginate($user)->toarray();
        $this->set('users', $user);
    }


    public function view()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemname');
        $this->loadModel('Itemcategory');
        $this->loadModel('Vendors');

        $where = $this->request->session()->read('cond');
        if (isset($where)) {
            $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where([$where])->order(['Additem.item_name' => 'asc'])->toarray();
            $this->request->session()->delete('cond');
        } else {
            $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where(['Additem.status' => "Y",'Additem.itemtype' => 'RawMaterial'])->order(['Additem.item_name' => 'asc'])->toarray();
        }
        $this->set(compact('users'));
        $this->response->type('pdf');
    }

   

    public function viewitemexcel()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemname');
        $this->loadModel('Itemcategory');
        $this->loadModel('Vendors');


        $where = $this->request->session()->read('cond');
        if (isset($where)) {
            $users = $this->Additem->find('all')->contain(['Measurementunit', 'Itemcategory', 'Sizemanager', 'Taxmaster', 'Companymaster', 'Itemlocation', 'Itemcategory'])->where([$where])->order(['Additem.category_id' => 'asc'])->toarray();
            $this->request->session()->delete('cond');
        } else {
            $users = $this->Additem->find('all')->contain(['Itemcategory'])->where(['Additem.itemtype' => 'RawMaterial'])->order(['Additem.category_id' => 'desc','Additem.item_name' => 'asc'])->toarray();
        }
        $this->set(compact('users'));
    }

    public function getitemdetail()
    {
        $this->loadModel('Additem');
        $this->loadModel('Measurementunit');
        $unitid = $this->Additem->find('all')->where(['Additem.id' => $this->request->data['fetch']])->first();

        echo json_encode($unitid);
        die;
    }


    public function getitemname()
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {

            $this->connection(trim($branch[0]));
        }

        $this->loadModel('Itemcategory');
        $this->loadModel('Additem');
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y', 'Additem.itemtype' => 'RawMaterial'])->toarray();
        foreach ($searchst as $value) {
            echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
        }

        die;
    }

}
