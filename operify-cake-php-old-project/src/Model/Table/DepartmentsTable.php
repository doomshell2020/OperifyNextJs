<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DepartmentsTable extends Table {

    public $name = 'Departments';
	
	public function initialize(array $config)
    {

        $this->table('departments');
        $this->primaryKey('id');



	}
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please fill Department Name')
	   
	    ->requirePresence('n_name')
	     ->notEmpty('n_name', 'Please fill Alias');

	    
		
	    
  
	return $validator;
	 
	}

}
?>
