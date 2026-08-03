<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class StorelocationpermissionTable extends Table
{
	public $name = 'Storelocationpermission';
	public function initialize(array $config)
	{
		$this->table('st_store_location_permission');
		$this->primaryKey('id');

		$this->belongsTo('Users', [
			'foreignKey' => 'staff_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Itemlocation', [
			'foreignKey' => 'location_id',
			'joinType' => 'INNER',
		]);


	}

	
}

?>
