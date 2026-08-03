<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class VendorTable extends Table
{
	public $name = 'Vendor';

	//---------------------------------------------------------
	public function initialize(array $config)
	{
		$this->table('vendors');
		$this->primaryKey('id');


		$this->belongsTo('States', [
            'foreignKey' => 'state_id',
            'joinType' => 'Left',
        ]);
	}

	//---------------------------------------------------------
	public function validationDefault(Validator $validator)
	{
		$validator = new Validator();

		$validator
		->requirePresence('name')
		->notEmpty('name', 'Please enter a valid name.');

		return $validator;
	}
}

?>
