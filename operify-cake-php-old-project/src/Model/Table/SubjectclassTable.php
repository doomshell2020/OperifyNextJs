<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SubjectclassTable extends Table {

    public $name = 'Subjectclass';
	
	
 public function initialize(array $config)
    {
       
       
       
			  $this->table('subjectclasses');
			
			
        $this->primaryKey('id');
			 $this->belongsTo('Classes', [
           'className' => 'Classes',
           'foreignKey' => 'class_id',
           'propertyName' => 'Classes',
            'joinType' => 'INNER',
           
        ]);
        
        $this->belongsTo('Subjects', [
            'className' => 'Subjects',
             'foreignKey' => 'subject_id',
             'propertyName' => 'Subjects',
              'joinType' => 'INNER',
           
        ]);
         
        
        
	}
    public function validationDefault(Validator $validator)
    {
    $validator = new Validator();
				
	$validator
	  //  ->requirePresence('class_id')
	    ->notEmpty('class_id', 'Please Add Class')
	   
	    //->requirePresence('subject_id')
	     ->notEmpty('subject_id', 'Please Add Subject');
	   

	    
		
	    
  
	return $validator;
	 
	}

}
?>
