<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GatepassesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('gatepasses');
        $this->displayField('gatepass_no');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SubContractors', [
            'foreignKey' => 'sub_contractor_id',
        ]);
        $this->belongsTo('JobChallans', [
            'foreignKey' => 'jc_id',
        ]);
        $this->hasMany('GatepassItems', [
            'foreignKey' => 'gatepass_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->maxLength('gatepass_no', 50)
            ->allowEmpty('gatepass_no');

        $validator
            ->date('date')
            ->allowEmpty('date');

        return $validator;
    }
}
