<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Creating Model for Shift module
 */
class ShiftTable extends Table
{
	public $name = 'Shift';

	//---------------------------------------------------------
	public function initialize(array $config)
	{
		$this->table('shifts');
		$this->primaryKey('id');
	}

	//---------------------------------------------------------
	public function validationDefault(Validator $validator)
	{
		$validator = new Validator();

		$validator
		->requirePresence('name')
		->notEmpty('name', 'Please enter a valid name.')

		->requirePresence('start_time')
		->notEmpty('start_time', 'Please enter a valid start time.')

		->requirePresence('end_time')
		->notEmpty('end_time', 'Please enter a valid end time.');

		return $validator;
	}
}

?>