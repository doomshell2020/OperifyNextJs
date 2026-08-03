<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudattendsTable extends Table {

    public $name = 'Studattends';
	
	
	public function initialize(array $config)
    {
		$this->table('studattends');
        $this->primaryKey('id');

		$this->belongsTo('Students', [
	    'foreignKey' => 'stud_id',
	    'joinType' => 'INNER',
        ]);
		
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
	    ->requirePresence('stud_id')
	    ->notEmpty('stud_id', 'Please fill name');
	   
	   
	return $validator;
	 
	}

}
?>
