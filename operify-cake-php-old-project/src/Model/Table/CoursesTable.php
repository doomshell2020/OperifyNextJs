<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CoursesTable extends Table {

    public $name = 'Courses';
    
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please Fill Course Name')
	   
	    ->requirePresence('type')
	     ->notEmpty('type', 'Please fill Type')
        
         ->requirePresence('alias')
	     ->notEmpty('alias', 'Please fill Alias');
	    
		
	    
  
	return $validator;
	 
	}

}
?>
