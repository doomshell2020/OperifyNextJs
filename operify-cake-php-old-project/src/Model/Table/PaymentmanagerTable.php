<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class PaymentmanagerTable extends Table
{
	public $name = 'Paymentmanager';
	public function initialize(array $config)
	{
		$this->table('st_paymentterms');
		$this->primaryKey('id');
	}

	
}

?>
