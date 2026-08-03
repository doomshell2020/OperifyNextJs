<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class JobChallansTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('job_challans');
        $this->primaryKey('id');

        $this->hasMany('JobChallanItems', [
            'foreignKey' => 'challan_id',
            'dependent' => true,
            'saveStrategy' => 'replace'
        ]);

        // $this->belongsTo('SubContractors');
        $this->belongsTo('SubContractors', [
            'foreignKey' => 'sub_contractors_id',
            'dependent' => true,
            'saveStrategy' => 'replace'
        ]);
    }
}
