<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TemplateCategoryTable extends Table
{

    public $name = 'Template';

    public function initialize(array $config)
    {
        $this->table('template_category');
        $this->primaryKey('id');

    }

}