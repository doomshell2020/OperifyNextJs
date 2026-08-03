<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class EmdRemarksTable extends Table
{
	public $name = 'EmdRemarks';
	public function initialize(array $config)
	{
		$this->table('emd_remarks');
		$this->primaryKey('id');

		
	}

	
}

?>
