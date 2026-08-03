<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CityTable extends Table {

    public $name = 'City';
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
       
       
       
		$this->table('city');
		
        $this->primaryKey('id');
			 $this->belongsTo('State', [
            'foreignKey' => 's_id',
            'joinType' => 'INNER',
        ]);  
         $this->primaryKey('id');
			 $this->belongsTo('Country', [
            'foreignKey' => 'c_id',
            'joinType' => 'INNER',
        ]); 
	}
}
?>
