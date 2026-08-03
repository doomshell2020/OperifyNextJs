<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class SuppliersTable extends Table
{
	public $name = 'Suppliers';
	public function initialize(array $config)
	{
		$this->table('st_supplier');
		$this->primaryKey('id');
	}

	
}

?>
