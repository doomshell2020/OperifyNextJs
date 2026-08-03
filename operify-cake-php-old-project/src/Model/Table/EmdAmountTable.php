<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class EmdAmountTable extends Table
{
	public $name = 'EmdAmount';
	public function initialize(array $config)
	{
		$this->table('emd_amount');
		$this->primaryKey('id');

		
	}

	
}

?>
