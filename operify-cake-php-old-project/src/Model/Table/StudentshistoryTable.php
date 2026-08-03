<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentshistoryTable extends Table {

    public $name = 'Studentshistory';
			
	public function initialize(array $config)
    {
		$this->table('studentshistory');
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
	    



}
?>
