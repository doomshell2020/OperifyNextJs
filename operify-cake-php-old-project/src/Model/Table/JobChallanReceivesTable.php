<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class JobChallanReceivesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('job_challan_receives');
        $this->primaryKey('id');

        $this->belongsTo('JobChallans', [
            'foreignKey' => 'challan_id'
        ]);

        $this->belongsTo('Additem', [
            'foreignKey' => 'item_id'
        ]);

        $this->belongsTo('FinishedProducts', [
            'className' => 'Additem',
            'foreignKey' => 'finished_product_id'
        ]);

        $this->hasMany('JobChallanReceiveMaterials', [
            'foreignKey' => 'receive_id',
            'dependent' => true,
            'saveStrategy' => 'replace'
        ]);
    }
}
