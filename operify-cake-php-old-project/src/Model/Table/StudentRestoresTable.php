<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentRestoresTable extends Table
{

    public $name = 'StudentRestores';

    public function initialize(array $config)
    {
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('DropOutStudents', [
            'foreignKey' => 'student_id',
            'bindingKey' => 's_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
    }
}