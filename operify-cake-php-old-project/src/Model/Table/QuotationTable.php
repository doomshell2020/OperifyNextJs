<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class QuotationTable extends Table
{
	public $name = 'Quotation';
	public function initialize(array $config)
	{
		$this->table('st_quotations');
		$this->primaryKey('id');

		
	}

	
}

?>
