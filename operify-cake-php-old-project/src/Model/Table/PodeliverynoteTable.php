<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class PodeliverynoteTable extends Table
{
	public $name = 'Podeliverynote';
	public function initialize(array $config)
	{
		$this->table('po_delivery_note');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

		$this->belongsTo('Purchaseorder', [
			'foreignKey' => 'poprimary_id',
			'joinType' => 'Left'
		]);

	}

	
}

?>
