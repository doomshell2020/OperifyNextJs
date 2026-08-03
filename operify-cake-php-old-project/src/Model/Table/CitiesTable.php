<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CitiesTable extends Table {

    public $name = 'Cities';
    
    public function initialize(array $config)
    {
			  $this->table('cities');
        $this->primaryKey('id');
			 $this->belongsTo('States', [
            'foreignKey' => 's_id',
            'joinType' => 'INNER',
        ]);
       	 $this->belongsTo('Country', [
            'foreignKey' => 'c_id',
            'joinType' => 'INNER',
        ]);
	}
	
	

    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('c_id')
	    ->notEmpty('c_id', 'Please Select Country')
	    ->requirePresence('s_id')
	    ->notEmpty('s_id', 'Please Select State')
	   
	    ->requirePresence('name')
	     ->notEmpty('name', 'Please fill City');

	    
		
	    
  
	return $validator;
	 
	}

}
?>
