<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StateTable extends Table {

    public $name = 'State';
      public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('name')
	    ->notEmpty('name', 'Please fill title');

	    
		
	    
  
	return $validator;
	 
	}
	  public function initialize(array $config)
    {
       
       
       
		$this->table('state');
		
        $this->primaryKey('id');
			 $this->belongsTo('Country', [
            'foreignKey' => 'c_id',
            'joinType' => 'INNER',
        ]);  
	}
}
?>
