<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransportsTable extends Table
{

    public $name = 'Transports';
    public function initialize(array $config)
    {
        $this->table('transports');
        $this->primaryKey('id');
        $this->belongsTo('Locations', [
            'foreignKey' => 'route',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Routemaster', [
            'foreignKey' => 'route',
            'joinType' => 'INNER',

        ]);
    }
}
