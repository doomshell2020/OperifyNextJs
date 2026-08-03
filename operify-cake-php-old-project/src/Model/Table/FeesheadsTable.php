<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class FeesheadsTable extends Table {

    public $name = 'Feesheads';
    
    			  public function initialize(array $config)
    {
       
       
       
			  $this->table('fees_heads');
        $this->primaryKey('id');

		
	}
	
	

    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	 ->requirePresence('name')
	     ->notEmpty('name', 'Please fill name');

	    
		
	    
  
	return $validator;
	 
	}

}
?>
