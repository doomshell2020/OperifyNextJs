<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransportsTable extends Table {

    public $name = 'Transports';
	
    
   public function initialize(array $config)
    {
       
       
       
			  $this->table('transports');
        $this->primaryKey('id');
			 $this->belongsTo('Locations', [
            'foreignKey' => 'route',
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
