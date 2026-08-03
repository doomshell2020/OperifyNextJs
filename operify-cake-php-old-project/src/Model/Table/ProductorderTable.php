<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class ProductionorderTable extends Table
{

    public $name = 'Productionorder';

    public function initialize(array $config)
    {
        $this->table('productionorder');
    }
}
