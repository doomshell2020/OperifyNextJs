<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class QuotationReceivedTable extends Table
{
	public $name = 'QuotationReceived';
	public function initialize(array $config)
	{
		$this->table('st_received_quotations');
		$this->primaryKey('id');

		
	}

	
}

?>
