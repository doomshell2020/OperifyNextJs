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


class SalesController extends AppController
{


    function customerorder()
    {
        $this->viewBuilder()->layout('admin');
    }

    function customerorderadd()
    {
        $this->viewBuilder()->layout('admin');
        
        if ($this->request->is(['post'])) {
         
        }
        
    }

    public function getname()
    {
        $this->loadModel('Vendor');
        $stsearch = $this->request->data['fetch'];
        $searchst = $this->Vendor->find('all')->where(['Vendor.name LIKE' => $stsearch . '%', 'Vendor.status' => 'Y'])->toarray();

        foreach ($searchst as $value) {

            echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail(' . "'" . $value['name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['name'] . '</a></li>';
        }
        die;
    }

    function addcounterparty() {
        $this->viewBuilder()->layout('admin');
    }

    function customerinvoice()
    {
        $this->viewBuilder()->layout('admin');
    }

    function addinvoices()
    {
        $this->viewBuilder()->layout('admin');
    }

    function shipments()
    {
        $this->viewBuilder()->layout('admin');
    }

    function addshipment()
    {
        $this->viewBuilder()->layout('admin');
    }

    function salesreturn(){
        $this->viewBuilder()->layout('admin');
        
    }

    function salesreturnadd(){
        $this->viewBuilder()->layout('admin');
        
    }

    function profit(){
        $this->viewBuilder()->layout('admin');
        
    }


    function salesfunnel(){
        $this->viewBuilder()->layout('admin');
        
    }


}
