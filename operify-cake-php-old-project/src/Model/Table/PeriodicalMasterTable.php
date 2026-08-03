<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PeriodicalMasterTable extends Table {

    public $name = 'PeriodicalMaster';
    
    public function initialize(array $config)
    {
			  $this->table('library_periodical_master');
        $this->primaryKey('id');
		 $this->hasMany('PeriodicalMasterDetails', [
            'foreignKey' => 'periodic_id',
            'joinType' => 'INNER',
        ]);
	}
	
	

 

}
?>
