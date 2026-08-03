<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class AdditemTable extends Table
{
	public $name = 'Additem';
	public function initialize(array $config)
	{
		$this->table('st_additem');
		$this->primaryKey('id');

		$this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);

		$this->belongsTo('Measurementunit', [
			'foreignKey' => 'uom',
			'joinType' => 'Left',
		]);

		$this->belongsTo('Sizemanager', [
			'foreignKey' => 'size_id',
			'joinType' => 'Left',
		]);

		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'tax',
			'joinType' => 'Left',
		]);
		$this->belongsTo('Companymaster', [
			'foreignKey' => 'cname',
			'joinType' => 'Left',
		]);

		$this->belongsTo('Itemlocation', [
			'foreignKey' => 'location_name',
			'joinType' => 'Left',
		]);

		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'tax',
			'joinType' => 'Left',
		]);
		$this->belongsTo('Categorywise', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left',
		]);

		$this->hasone('StockAvailable', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left',
		]);

	}

	
}

?>
