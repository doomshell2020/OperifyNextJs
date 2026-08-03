<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransportstudentlistTable extends Table {

    public $name = 'Transportstudentlist';	
	
	public function initialize(array $config)
    {     
		$this->table('tbl_tranport_student_list');
        $this->primaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]); 

		$this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
            'joinType' => 'INNER',
        ]);     

        // $this->belongsTo('Transports', [
        //     'foreignKey' => 'bus_id',
        //     'joinType' => 'INNER',
        // ]); 

        $this->belongsTo('Transportfees', [
            'foreignKey' => 'location_id',
            'joinType' => 'INNER',
        ]); 
	}
 
}
