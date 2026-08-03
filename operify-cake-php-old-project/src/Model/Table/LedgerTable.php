<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class LedgerTable extends Table
{

    public $name = 'Ledger';

    public function initialize(array $config)
    {

        $this->table('py_ledger');
        $this->primaryKey('id');
        $this->belongsTo('Ledgerg', [
            'foreignKey' => 'gid',
            'joinType' => 'INNER',
        ]);

    }

}
