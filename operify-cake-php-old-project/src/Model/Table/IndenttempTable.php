<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class IndenttempTable extends Table
{
	public $name = 'Indenttemp';
	public function initialize(array $config)
	{
		$this->table('st_indentmaster_temp');
		$this->primaryKey('id');

		$this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Companymaster', [
			'foreignKey' => 'company_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Sizemanager', [
			'foreignKey' => 'size_id',
			'joinType' => 'INNER',
		]);
	}

	
}

?>
