<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class VideosTable extends Table
{

    public $name = 'Videos';

    public function initialize(array $config)
    {
        $this->table('videos');
        $this->primaryKey('id');
        $this->belongsTo('Classes', [
            'className' => 'Classes',
            'foreignKey' => 'class_id',
            'propertyName' => 'Classes',
            'joinType' => 'INNER',

        ]);

        $this->belongsTo('Subjects', [
            'className' => 'Subjects',
            'foreignKey' => 'subject_id',
            'propertyName' => 'Subjects',
            'joinType' => 'INNER',

        ]);
        $this->belongsTo('Sections', [
            'className' => 'Sections',
            'foreignKey' => 'section_id',
            'propertyName' => 'Sections',
            'joinType' => 'INNER',

        ]);

    }
    public function beforeSave()
    {
        if (isset($this->data['description'])) {
            $this->data['description'] = strip_tags($this->data['description']);
        }
        return true;
    }

}
