<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class InspectionReportTable extends Table
{
	public $name = 'InspectionReport';
	public function initialize(array $config)
	{
		$this->table('st_inspection_report');
		$this->primaryKey('id');

		
	}

	
}

?>
