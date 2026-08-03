<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class InspectionGrnTable extends Table
{
	public $name = 'InspectionGrn';
	public function initialize(array $config)
	{
		$this->table('grn_inspection');
		$this->primaryKey('id');

		
	}

	
}

?>
