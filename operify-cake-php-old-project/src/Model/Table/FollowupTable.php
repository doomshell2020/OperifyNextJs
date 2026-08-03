<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class FollowupTable extends Table {

    public $name = 'Followup';
	
	public function initialize(array $config)
    {
       
	 $this->table('followup');
        	 $this->belongsTo('Enquires', [
            'foreignKey' => 'enq_id',
            'joinType' => 'INNER',
        ]);
        
		
        


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
