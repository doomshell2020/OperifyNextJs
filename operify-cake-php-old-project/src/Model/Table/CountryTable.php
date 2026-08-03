<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CountryTable extends Table {

    public $name = 'Country';
	
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please fill title');
	   
	   
	return $validator;
	 
	}

}
?>
