<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StaffattendsTable extends Table {

    public $name = 'Staffattends';
	
	
	public function initialize(array $config)
    {
		$this->table('staffattends');
        $this->primaryKey('id');

		$this->belongsTo('Employees', [
	    'foreignKey' => 'emp_id',
	    'joinType' => 'INNER',
        ]);
		
		     
        
	}
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('stud_id')
	    ->notEmpty('stud_id', 'Please fill name');
	   
	   
	return $validator;
	 
	}

}
?>
