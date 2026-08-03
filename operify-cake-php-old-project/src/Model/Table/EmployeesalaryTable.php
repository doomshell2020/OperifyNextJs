<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class EmployeesalaryTable extends Table
{

    public $name = 'Employeesalary';

    public function initialize(array $config)
    {

        $this->table('py_employee_salary_setting');
        $this->primaryKey('id');
        $this->belongsTo('employees', [
            'foreignKey' => 'employee_id',
            'joinType' => 'INNER',
        ]);

    }

}
