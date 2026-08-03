<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class EmployeesTable extends Table
{

    public $name = 'Employees';

    public function initialize(array $config)
    {

        $this->table('employees');
        $this->primaryKey('id');
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Designations', [
            'foreignKey' => 'designation_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('PayrollDepartments', [
            'foreignKey' => 'p_department',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PayrollDesignations', [
            'foreignKey' => 'p_designation',
            'joinType' => 'INNER',
        ]);
        $this->hasMany(
            'Employeeattendance',
            ['foreignKey' => 'employee_id', 'joinType' => 'LEFT']
        );
        $this->hasone(
            'Users',
            ['foreignKey' => 'tech_id', 'joinType' => 'INNER']
        );
        $this->hasone(
            'Employeesalary',
            ['foreignKey' => 'employee_id', 'joinType' => 'LEFT']
        );
        $this->hasone(
            'Users',
            ['foreignKey' => 'tech_id', 'joinType' => 'LEFT']
        );

       
    }

    public function validationDefault(Validator $validator)
    {
        $validator = new Validator();

        $validator
        //  ->requirePresence('fname')
        ->notEmpty('fname', 'Please Fill First Name')

        //  ->requirePresence('lname')
            ->notEmpty('lname', 'Please Fill Last Name');

        // ->requirePresence('dob')
        // ->notEmpty('dob', 'Please Fill Date Of Birth')

        //   ->requirePresence('f_h_name')
        // ->notEmpty('f_h_name', 'Please Fill Father/husband name')

        //  ->requirePresence('gender')
        //->notEmpty('gender', 'Please Choose Gender')

        // ->requirePresence('mobile')
        //->notEmpty('mobile', 'Please Enter Mobile No')

        //  ->requirePresence('department_id')
        //->notEmpty('department_id', 'Please Select Department')

        // ->requirePresence('joiningdate')
        //->notEmpty('joiningdate', 'Please Select Joining Date')

        //     ->requirePresence('email')
        //   ->notEmpty('email', 'Please Fill Email')

        //    ->requirePresence('acedmicyear')
        //    ->notEmpty('acedmicyear', 'Please Select Academic Year')

        //   ->requirePresence('experience')
        // ->notEmpty('experience', 'Please Select Experience')
        //->notEmpty('designation_id', 'Please Select Designation');

        return $validator;

    }

}
