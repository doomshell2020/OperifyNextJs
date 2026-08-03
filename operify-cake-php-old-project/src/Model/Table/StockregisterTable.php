<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StockregisterTable extends Table
{
	public $name = 'Stockregister';
	public function initialize(array $config)
	{
		$this->table('st_stock_register');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Goodsreceived', [
			'foreignKey' => 'goods_id',
			'joinType' => 'INNER',
		]);
		$this->belongsTo('Purchaseorder', [
			'foreignKey' => 'purchaseorder_id',
			'joinType' => 'INNER',
		]);
		
		$this->belongsTo('Vendor', [
			'foreignKey' => 'vendor_id',
			'joinType' => 'left',
		]);

		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'tax_id',
			'joinType' => 'left',
		]);
		
	}
}

?>
