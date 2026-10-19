<?php
// Execute the unmodified legacy calculation method against SELECT-only snapshots.
namespace Cake\Datasource {
    class ConnectionManager {
        public static $rows;
        public static function get($name) { return new \StockReferenceConnection(); }
    }
}
namespace {
    class StockReferenceConnection {
        public function execute($sql) { return $this; }
        public function fetchAll($mode) { return \Cake\Datasource\ConnectionManager::$rows; }
    }
    class StockReferenceProducts {
        public $products;
        public function __construct($products) { $this->products = $products; }
        public function find($type) { return $this; }
        public function contain($models) { return $this; }
        public function where($conditions) { return $this; }
        public function order($order) { return $this; }
        public function toArray() { return $this->products; }
    }
    $source = file_get_contents(__DIR__ . '/../../operify-cake-php-old-project/src/Controller/Admin/StockregisterController.php');
    $start = strpos($source, 'private function _getConsolidatedStock(');
    if ($start === false) throw new \Exception('Legacy calculation method not found');
    $method = substr($source, $start, strrpos($source, '}') - $start);
    eval('class StockReferenceHarness {
        public $Additem;
        public function loadModel($name) {}
        public function run($from, $to) { return $this->_getConsolidatedStock($from, $to); }
        ' . $method . '}');
    $input = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    \Cake\Datasource\ConnectionManager::$rows = $input['movements'];
    $harness = new StockReferenceHarness();
    $harness->Additem = new StockReferenceProducts(array_map(function ($p) {
        $p['itemcategory'] = (object)['category_name' => $p['category_name']];
        $p['measurementunit'] = (object)['measurement_name' => $p['unit']];
        return (object)$p;
    }, $input['products']));
    echo json_encode($harness->run($input['from'], $input['to']), JSON_THROW_ON_ERROR);
}
