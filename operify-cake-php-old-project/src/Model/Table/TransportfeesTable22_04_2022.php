<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransportfeesTable extends Table {

    public $name = 'Transportfees';
	
	
			  public function initialize(array $config)
    {
       
       
       
			  $this->table('transportfees');
        $this->primaryKey('id');
			 $this->belongsTo('locations', [
            'foreignKey' => 'loc_id',
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
