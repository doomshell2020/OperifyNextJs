<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentsubjectTable extends Table {

    public $name = 'Studentsubject';
	
	
 public function initialize(array $config)
    {
       
       
       
			  $this->table('studentsubjects');
			
			
        $this->primaryKey('id');
			 $this->belongsTo('Students', [
           'className' => 'Students',
           'foreignKey' => 'student_id',
           'propertyName' => 'Students',
            'joinType' => 'INNER',
           
        ]);
        
       
        
        
        $this->belongsTo('CompSubjects', [
    'className' => 'Subjects',
    'foreignKey' => 'comp_sid',
    'propertyName' => 'CompSubjects',
]);
$this->belongsTo('OptSubjects', [
    'className' => 'Subjects',
    'foreignKey' => 'opt_sid',
    'propertyName' => 'OptSubjects'
]);
         
        
        
	}
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	    ->requirePresence('student_id')
	    ->notEmpty('class_id', 'Please Add Class')
	   
	    ->requirePresence('comp_sid')
	     ->notEmpty('comp_sid', 'Please Compulsory  Subject')
	     ->requirePresence('opt_sid')
	     ->notEmpty('opt_sid', 'Please Optional Subject');
	   

	    
		
	    
  
	return $validator;
	 
	}

}
?>
