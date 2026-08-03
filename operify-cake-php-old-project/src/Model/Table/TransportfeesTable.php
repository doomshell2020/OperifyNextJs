<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransportfeesTable extends Table {

    public $name = 'Transportfees';	
	
	public function initialize(array $config)
    {     
		$this->table('transportfees');
        $this->primaryKey('id');
		$this->belongsTo('Locations', [
            'foreignKey' => 'loc_id',
            'joinType' => 'INNER',
        ]);      
	}
 
}
