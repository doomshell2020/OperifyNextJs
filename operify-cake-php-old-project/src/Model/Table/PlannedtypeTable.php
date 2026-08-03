<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PlannedtypeTable extends Table {

    public $name = 'st_planned_type';
	
	public function initialize(array $config)
    {     
		$this->table('st_planned_type');
        $this->primaryKey('id');

        // $this->belongsTo('Production', [
		// 	'foreignKey' => 'machine_id',
		// 	'joinType' => 'INNER',
		// ]);
        
	}
}
?>
