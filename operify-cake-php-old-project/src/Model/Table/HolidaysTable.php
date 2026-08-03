<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class HolidaysTable extends Table
{

    public $name = 'Holidays';

    public function initialize(array $config)
    {
        $this->table('py_holidays');
        $this->primaryKey('id');

    }

}
