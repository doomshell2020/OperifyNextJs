<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class JobChallanItemsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

         $this->table('job_challan_items');

        $this->belongsTo('JobChallans', [
            'foreignKey' => 'challan_id'
        ]);
    }
}

?>