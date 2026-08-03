<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TestimonialsTable extends Table {

    public $name = 'Testimonials';
	
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('title')
	    ->notEmpty('title', 'Please fill title')
	   
	    ->requirePresence('description')
	     ->notEmpty('description', 'Please fill description');  
	return $validator;
	 
	}

}
?>
