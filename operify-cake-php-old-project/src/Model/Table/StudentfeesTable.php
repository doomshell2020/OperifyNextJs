<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentfeesTable extends Table
{

    public $name = 'Studentfees';

    public function initialize(array $config)
    {

        $this->table('student_feeallocations');
        $this->primaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'left',
        ]);


        $this->belongsTo('Studentshistory', [
            'foreignKey' => false,
            'conditions' => array(
                'Studentfees.student_id = Studentshistory.stud_id'
            )
        ]);

        $this->belongsTo('DropOutStudent', [

            'foreignKey' => false,
            'conditions' => array(
                'Studentfees.student_id = DropOutStudent.s_id'
            ),
            'propertyName' => 'student'

        ]);

        $this->belongsTo('Banks', [
            'foreignKey' => 'bank_id',
            'joinType' => 'INNER',
        ]);

        // $this->belongsTo('Studentfeepending', [
        //     'foreignKey' => 'recipetno',
        //     'joinType' => 'INNER',
        // ]);
    }



    public function validationDefault(Validator $validator)
    {
        //  $validator = new Validator();

    }
}
