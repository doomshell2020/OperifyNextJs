<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DocumentcategoryTable extends Table {

    public $name = 'Documentcategory';
	
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('categoryname')
	    ->notEmpty('categoryname', 'Please fill title');
	   
	   
	return $validator;
	 
	}

}
?>
