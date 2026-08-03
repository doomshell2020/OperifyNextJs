<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PurchaseTable extends Table {

    public $name = 'Purchase';
    public function initialize(array $config)
    {
        $this->belongsTo('Vendor', [
            'foreignKey' => 'vendor_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Purchasedetails', [
        'foreignKey' => 'purchase_id',
    ]);

    }

}
?>
