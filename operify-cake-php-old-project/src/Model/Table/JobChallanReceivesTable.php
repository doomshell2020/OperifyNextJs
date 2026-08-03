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

        // 🔗 Relations
        $this->belongsTo('JobChallans', [
            'foreignKey' => 'challan_id'
        ]);

        $this->belongsTo('Additem', [
            'foreignKey' => 'item_id'
        ]);
    }
}
?>