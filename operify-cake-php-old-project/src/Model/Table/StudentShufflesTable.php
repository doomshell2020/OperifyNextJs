<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class StudentShufflesTable extends Table
{

    public $name = 'StudentShuffles';

    public function initialize(array $config)
    {
        $this->belongsTo('StudentOne', [
            'foreignKey' => 'from_student_id',
            'joinType' => 'INNER',
            'className'=>'Students',
        ]);
        $this->belongsTo('StudentTwo', [
            'foreignKey' => 'to_student_id',
            'joinType' => 'INNER',
            'className'=>'Students',
        ]);
    }

}
