<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TemplateTable extends Table
{

    public $name = 'Template';

    public function initialize(array $config)
    {
        $this->table('report_template');
        $this->primaryKey('id');

        $this->belongsTo('TemplateCategory', [
            'foreignKey' => 'category',
            'joinType' => 'INNER',
        ]);

    }

}