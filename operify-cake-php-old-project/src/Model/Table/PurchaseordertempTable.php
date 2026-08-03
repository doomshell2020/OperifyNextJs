<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class PurchaseordertempTable extends Table
{
	public $name = 'Purchaseordertemp';
	public function initialize(array $config)
	{
		$this->table('st_purchaseorder_temp');
		$this->primaryKey('id');

		$this->belongsTo('Itemname', [
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
