<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class HelpTable extends Table
{
     
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Table('help');

       
        $this->PrimaryKey('id');

        $this->addBehavior('Timestamp');

        
    }

}