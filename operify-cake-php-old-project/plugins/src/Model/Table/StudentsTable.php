<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentsTable extends Table {

    public $name = 'Students';
			
			  public function initialize(array $config)
    {
       
       
       
			  $this->table('students');
        $this->primaryKey('id');
			 $this->belongsTo('Classes', [
            'foreignKey' => 'class_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);
        
	}
	    
	 public function validationDefault(Validator $validator)
    {
       $validator = new Validator();

	$validator
	    ->notEmpty('fname', 'Please fill Name');
			$validator
	    ->notEmpty('lname', 'Please fill Last Name');
	 
	     $validator
	    ->notEmpty('username', 'Please fill Email')
	    ->add('username', [
            'emailValid' => [
                'rule' => ['email', true],
                'message' => 'You must provide a valid email'
            ]
        ]);
	     $validator
	    ->notEmpty('mobile', 'Please fill Mobile')
	     ->add('mobile', [
        'minLength' => [
            'rule' => ['minLength', 10],
            'last' => true,
            'message' => 'Mobile No. need to be at least 10 digit long.'
        ],
        'maxLength' => [
            'rule' => ['maxLength', 10],
            'message' => 'Mobile No. cannot be too long.'
        ]
    ]);
 $validator
	    ->notEmpty('admissionyear', 'Please fill Admission');
	     $validator
	    ->notEmpty('acedmicyear', 'Please fill Acedmic');
	     $validator
	    ->notEmpty('class_id', 'Please fill Class');
	        $validator
	    ->notEmpty('section_id', 'Please fill Section');
	     $validator
	    ->notEmpty('dob', 'Please fill Date');
	     $validator
	    ->notEmpty('adaharnumber', 'Please fill Adaharno')
	 ->add('adaharnumber', [
        'minLength' => [
            'rule' => ['minLength', 12],
            'last' => true,
            'message' => 'Adahar Number need to be at least 12 characters long.'
        ],
        'maxLength' => [
            'rule' => ['maxLength', 12],
            'message' => 'Adahar Number cannot be too long.'
        ]
    ]);
	    
	    


        return $validator;
    }  


}
?>
