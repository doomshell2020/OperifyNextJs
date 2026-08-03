<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ParticularpaymentsTable extends Table
{
	public $name = 'Particularpayments';
	public function initialize(array $config)
	{
		$this->table('particular_payments');
		$this->primaryKey('id');

		
	}

	
}

?>
