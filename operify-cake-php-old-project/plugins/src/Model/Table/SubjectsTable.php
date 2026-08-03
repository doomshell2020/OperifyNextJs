<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SubjectsTable extends Table {

    public $name = 'Subjects';
				
	    
	 public function validationDefault(Validator $validator)
    {
       $validator = new Validator();

	$validator
	    ->notEmpty('name', 'Please fill Name');
		
	  

        return $validator;
    }  


}
?>
