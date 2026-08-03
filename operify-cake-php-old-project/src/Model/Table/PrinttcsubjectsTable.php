<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PrinttcsubjectsTable extends Table {

    public $name = 'Printtcsubjects';
				
	    
	 public function validationDefault(Validator $validator)
    {
       $validator = new Validator();

	$validator
	    ->notEmpty('name', 'Please fill Name');
		
	  

        return $validator;
    }  


}
?>
