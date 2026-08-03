<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SubContractorsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('sub_contractors');
        $this->primaryKey('id');

        $this->hasMany('JobChallans', [
            'foreignKey' => 'sub_contractors_id'
        ]);
    }
}

?>