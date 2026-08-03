<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Creating Model for Library cup board module
 */
class HolidayallowanceTable extends Table
{

    //---------------------------------------------------------
    public function initialize(array $config)
    {
        $this->table('py_holidayallowance');
        $this->primaryKey('id');
        $this->belongsTo(
            'Advancesalary',
            ['foreignKey' => 'adv_id', 'joinType' => 'INNER']
        );
    }

    //---------------------------------------------------------

}
