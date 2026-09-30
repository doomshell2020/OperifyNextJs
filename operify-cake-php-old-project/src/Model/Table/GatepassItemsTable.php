<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GatepassItemsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('gatepass_items');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Gatepasses', [
            'foreignKey' => 'gatepass_id',
        ]);
        $this->belongsTo('Additem', [
            'foreignKey' => 'item_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->decimal('quantity')
            ->allowEmpty('quantity');

        return $validator;
    }
}
