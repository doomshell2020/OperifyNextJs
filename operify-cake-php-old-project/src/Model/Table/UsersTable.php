<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class UsersTable extends Table
{
    public $name = 'Users';

    public function initialize(array $config)
    {
        $this->primaryKey('id');
        $this->primaryKey('id');
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',

        ]);
        $this->belongsTo('Schools', [
            'foreignKey' => 'c_id',
            'joinType' => 'INNER',

        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'Left',

        ]);

        $this->belongsTo(
            'Employees',
            ['className' => 'Employees', 'foreignKey' => 'tech_id', 'joinType' => 'INNER']
        );

        $this->belongsTo('Device', [
            'foreignKey' => 'device_id',
            'joinType' => 'INNER',
        ]);
    }
}
