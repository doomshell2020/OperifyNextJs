<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentTransfeesTable extends Table {

    public $name = 'StudentTransfees';
	
	
			  public function initialize(array $config)
    {
       
       
       
			  $this->table('s_transportfees');
        $this->primaryKey('id');
			 $this->belongsTo('Banks', [
            'foreignKey' => 'bank_id',
            'joinType' => 'INNER',
        ]); 
			 $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
