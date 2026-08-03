<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TimestablesTable extends Table {

    public $name = 'Timestables';
	
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please fill name');
	   
	   
	return $validator;
	 
	}

}
?>
