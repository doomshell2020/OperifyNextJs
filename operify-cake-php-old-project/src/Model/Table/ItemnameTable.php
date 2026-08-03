<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ItemnameTable extends Table
{
	public $name = 'Itemname';
	public function initialize(array $config)
	{
		$this->table('st_itemmaster');
		$this->primaryKey('id');

		$this->belongsTo('Maincategory', [
			'className' => 'Itemcategory',
			'foreignKey' => 'main_category_id',
			'propertyName' => 'Maincategory'
		]);

		$this->belongsTo('Subcategory', [
			'className' => 'Itemcategory',
			'foreignKey' => 'category_id',
			'propertyName' => 'Subcategory'
		]);

		$this->belongsTo('Mainlocation', [
			'className' => 'Itemlocation',
			'foreignKey' => 'main_location_id',
			'propertyName' => 'Mainlocation',
		]);

		$this->belongsTo('Sublocation', [
			'className' => 'Itemlocation',
			'foreignKey' => 'location_id',
			'propertyName' => 'Sublocation',
		]);

		$this->belongsTo('Measurementunit', [
			'foreignKey' => 'unit_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Companymaster', [
			'foreignKey' => 'company_id',
			'joinType' => 'INNER',
		]);





	}

	
}

?>
