<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class MeasurementunitTable extends Table
{
	public $name = 'Measurementunit';
	public function initialize(array $config)
	{
		$this->table('st_measurementunits');
		$this->primaryKey('id');
	}

	
}

?>
