<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class RoutemasterTable extends Table
{
	public $name = 'route_master';
	public function initialize(array $config)
	{
		$this->table('route_master');
		$this->primaryKey('id');
		// $this->belongsTo('Locations', [
        //     'foreignKey' => 'location_id',
        //     'joinType' => 'INNER',
        // ]);
	}

	
}

?>