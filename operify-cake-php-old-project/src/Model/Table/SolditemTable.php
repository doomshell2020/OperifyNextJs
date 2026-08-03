<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SolditemTable extends Table
{
    public $name = 'Solditem';
    public function initialize(array $config)
    {
        $this->table('solditems');
		$this->primaryKey('id');

        $this->hasMany('Solditemdetails', [
			'foreignKey' => 'sold_id',
			'joinType' => 'Left'
		]);

        $this->belongsto('Students', [
			'foreignKey' => 'customer_name',
			'joinType' => 'Left'
		]);

        $this->belongsto('Students', [
			'foreignKey' => 'student_id',
			'joinType' => 'Left'
		]);

    }
  

}
