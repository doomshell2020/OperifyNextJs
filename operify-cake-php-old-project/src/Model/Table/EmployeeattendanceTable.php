<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Creating Model for Employee Attendance module
 */
class EmployeeattendanceTable extends Table
{
    public $name = 'Employeeattendance';

    //---------------------------------------------------------
    public function initialize(array $config)
    {
        $this->table('py_employee_attendance');
        $this->primaryKey('id');

        $this->belongsTo('Employees', [
            'foreignKey' => 'employee_id',
            'joinType' => 'INNER',
        ]);

    }
}
