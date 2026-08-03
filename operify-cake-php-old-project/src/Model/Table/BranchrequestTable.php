<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class BranchrequestTable extends Table
{
    public $name = 'Branchrequest';
    public function initialize(array $config)
    {
        $this->hasMany('Branchrequestdetail', [
			'foreignKey' => 'branchrequest_id',
			'joinType' => 'Left'
		]);

   
    }
  

}
