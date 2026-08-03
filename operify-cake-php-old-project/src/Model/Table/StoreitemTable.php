<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class StoreitemTable extends Table
{
	public $name = 'Storeitem';
	public function initialize(array $config)
	{
		$this->table('store_items');
		$this->primaryKey('id');

		$this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'INNER'
		]);

	}

	
}

?>
