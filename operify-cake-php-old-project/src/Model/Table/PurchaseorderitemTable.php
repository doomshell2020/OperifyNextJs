<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class PurchaseorderitemTable extends Table
{
	public $name = 'Purchaseorderitem';
	public function initialize(array $config)
	{
		$this->table('st_purchaseorder_items');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Measurementunit', [
			'foreignKey' => 'unit_id',
			'joinType' => 'INNER',
		]);
	}

	
}

?>
