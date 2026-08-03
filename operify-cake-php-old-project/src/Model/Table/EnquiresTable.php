<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class EnquiresTable extends Table {

    public $name = 'Enquires';
				
public function initialize(array $config)
    {
       
	 $this->table('enquires');
        $this->primaryKey('id');
			 $this->belongsTo('Classes', [
            'foreignKey' => 'class_id',
            'joinType' => 'LEFT',
        ]);
        
        	 $this->belongsTo('Modes', [
            'foreignKey' => 'mode_id',
            'joinType' => 'LEFT',
        ]);
        	

        $this->belongsTo('States', [
            'foreignKey' => 'state',
            'joinType' => 'LEFT',
        ]);
        	
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);

}


  /*  public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('title')
	    ->notEmpty('title', 'Please fill title')
	   
	    ->requirePresence('type')
	     ->notEmpty('type', 'Please fill description');

	    
		
	    
  
	return $validator;
	 
	}
*/
}
?>
