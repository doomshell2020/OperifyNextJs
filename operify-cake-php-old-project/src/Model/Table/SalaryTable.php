<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SalaryTable extends Table
{

    public $name = 'salary';

    public function initialize(array $config)
    {

        $this->table('py_salary');
        $this->primaryKey('id');
        $this->belongsTo('Employees', [
            'foreignKey' => 'Eid',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DropOutEmployee', [
            'foreignKey' => 'Eid',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Employeesalary', [

            'foreignKey' => false,
            'conditions' => array(
                'Salary.Eid = Employeesalary.employee_id',
            ),
            'propertyName' => 'Employeesalary',

        ]);

    }

}
