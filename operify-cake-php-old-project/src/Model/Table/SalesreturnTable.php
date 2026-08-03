<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SalesreturnTable extends Table
{
    public $name = 'Salesreturn';
    public function initialize(array $config)
    {
        $this->hasMany('Salesreturndetils', [
			'foreignKey' => 'salereturn_id',
			'joinType' => 'Left'
		]);

   
    }
  

}
