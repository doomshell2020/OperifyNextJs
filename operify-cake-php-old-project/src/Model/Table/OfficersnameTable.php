<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class OfficersnameTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->table('officers_name'); 

        
    }
}

