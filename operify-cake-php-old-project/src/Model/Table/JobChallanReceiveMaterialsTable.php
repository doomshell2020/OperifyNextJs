<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class JobChallanReceiveMaterialsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('job_challan_receive_materials');
        $this->primaryKey('id');

        $this->belongsTo('JobChallanReceives', [
            'foreignKey' => 'receive_id'
        ]);

        $this->belongsTo('Additem', [
            'foreignKey' => 'material_id'
        ]);
    }
}
