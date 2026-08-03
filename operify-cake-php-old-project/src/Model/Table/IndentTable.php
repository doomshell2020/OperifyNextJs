<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class IndentTable extends Table
{
	public $name = 'Indent';
	public function initialize(array $config)
	{
		$this->table('st_indentmaster');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left',
		]);


		$this->belongsTo('Sizemanager', [
					'foreignKey' => 'size_id',
					'joinType' => 'INNER',
				]);
				
		$this->belongsTo('Users', [
			'foreignKey' => 'added_by',
			'joinType' => 'INNER',
		]);
	}

	
}

?>
