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


class inventoryController extends AppController
{
    //$this->loadcomponent('Session');
    public function initialize()
    {
        //load all models
        parent::initialize();
    }

    function stockadjustments()
    {

        $this->viewBuilder()->layout('admin');
    }
    function writeoffs()
    {

        $this->viewBuilder()->layout('admin');
    }
    function counts()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addcounts()
    {

        $this->viewBuilder()->layout('admin');
    }
    function internalorders()
    {


        $this->viewBuilder()->layout('admin');
    }
    function addinternalorders()
    {


        $this->viewBuilder()->layout('admin');
    }
    function transfers()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addtransfers()
    {

        $this->viewBuilder()->layout('admin');
    }
    function stockmovement()
    {



        $this->viewBuilder()->layout('admin');
    }
    function addwriteoffs()
    {

        $this->viewBuilder()->layout('admin');
    }
    function serialnumbers()
    {

        $this->viewBuilder()->layout('admin');
    }
    function addstock()
    {

        $this->viewBuilder()->layout('admin');
    }
}
