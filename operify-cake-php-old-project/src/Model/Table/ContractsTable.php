<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ContractsTable extends Table
{
	public $name = 'Contracts';
	public function initialize(array $config)
	{
		$this->table('contracts');
		$this->primaryKey('id');

		$this->belongsTo('Vendor', [
			'foreignKey' => 'supplier_id',
			'joinType' => 'INNER',
		]);
	}

	
}

?>
