<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class StockAvailableTable extends Table
{
	public $name = 'StockAvailable';
	public function initialize(array $config)
	{
	
		$this->table('st_stock_available');
		$this->primaryKey('id');


		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'left'
		]);

	}

	
}

?>
