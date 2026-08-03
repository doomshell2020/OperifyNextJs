<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StatesTable extends Table {

    public $name = 'States';
    
	public function initialize(array $config)
    {
		$this->table('states');
        $this->primaryKey('id');
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
	   
	    ->requirePresence('name')
	     ->notEmpty('name', 'Please fill state');

	    
		
	    
  
	return $validator;
	 
	}

}
?>
