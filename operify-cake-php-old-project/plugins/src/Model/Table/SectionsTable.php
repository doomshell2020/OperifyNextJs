<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SectionsTable extends Table {

    public $name = 'Sections';
	
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('title')
	    ->notEmpty('title', 'Please fill title');
	   
	   
	return $validator;
	 
	}

}
?>
