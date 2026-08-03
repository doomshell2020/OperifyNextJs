<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class CounterpartiesController extends AppController
{

    function customers()
    {
        $this->viewBuilder()->layout('admin');
    }

    function contract()
    {
        $this->viewBuilder()->layout('admin');
    }

    function contractadd()
    {
        $this->viewBuilder()->layout('admin');
    }
}
