<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DepartmentTable extends Table {

    public $name = 'department';
      public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please fill title')
	   
	    ->requirePresence('n_name')
	     ->notEmpty('n_name', 'Please fill Alias');

	    
		
	    
  
	return $validator;
	 
	}
}
?>
