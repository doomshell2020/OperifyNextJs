<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class NewmemberTable extends Table {

    public $name = 'Newmember';
	
    
   public function initialize(array $config)
    {
       
       
       
			  $this->table('newmember');
        $this->primaryKey('id');
			
	}
	

  /*  public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('')
	    ->notEmpty('title', 'Please fill title')
	   
	    ->requirePresence('type')
	     ->notEmpty('type', 'Please fill description');

	    
		
	    
  
	return $validator;
	 
	}
*/
}
?>
