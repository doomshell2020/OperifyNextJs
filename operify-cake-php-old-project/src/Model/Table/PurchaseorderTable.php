<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class PurchaseorderTable extends Table
{
	public $name = 'Purchaseorder';
	public function initialize(array $config)
	{
		$this->table('st_purchaseorder');
		$this->primaryKey('id');

		
	}

	
}

?>
