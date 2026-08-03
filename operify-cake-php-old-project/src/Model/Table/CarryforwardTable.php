<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CarryforwardTable extends Table {

    public $name = 'Carryforward';
    public function initialize(array $config)
    {
		$this->table('carryforward');
        $this->primaryKey('id');
			 $this->belongsTo('employees', [
            'foreignKey' => 'Eid',
            'joinType' => 'INNER',
        ]);
    	
	}
 
}
?>