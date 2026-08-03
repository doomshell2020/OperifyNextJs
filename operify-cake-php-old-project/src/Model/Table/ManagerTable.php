<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class ManagerTable extends Table
{

    public $name = 'Manager';

    public function initialize(array $config)
    {
        $this->table('permission_manager');
        $this->primaryKey('id');
    }
}
