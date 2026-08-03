<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ClassesTable extends Table {

    public $name = 'Classes';
	
	

    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('title')
	    ->notEmpty('title', 'Please fill title')
	   
	    ->requirePresence('type')
	     ->notEmpty('type', 'Please fill description');

	    
		
	    
  
	return $validator;
	 
	}

}
?>
