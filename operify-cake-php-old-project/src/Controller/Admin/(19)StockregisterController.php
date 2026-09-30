<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

include '../vendor/PHPExcel/Classes/PHPExcel.php';
include '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class StockregisterController extends AppController
{


    // public function connection($dbs)
    // {
    //     //echo $dbs; die;
    //     ConnectionManager::config($dbs, [
    //         'className' => 'Cake\Database\Connection',
    //         'driver' => 'Cake\Database\Driver\Mysql',
    //         'persistent' => false,
    //         'host' => 'localhost',
    //         'username' => 'tpplerp',
    //         'password' => 'tpplerp@23~',
    //         'database' => $dbs,
    //         'encoding' => 'utf8mb4',
    //         'timezone' => 'UTC',
    //         'cacheMetadata' => true,
    //     ]);
    //     ConnectionManager::drop('default');
    //     ConnectionManager::get($dbs);
    //     \Cake\Datasource\ConnectionManager::alias($dbs, 'default');
    // }

    public function initialize()
    {
        parent::initialize();
    }
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Stockregister');
        $this->loadModel('Itemcategory');

        $categories = $this->Itemcategory->find('list', ['keyField' => 'id', 'valueField' => 'category_name'])->toArray();
        $this->set(compact('categories'));
    }


    public function receivedstock($date, $item_id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('Goodsreceived');
        $this->loadModel('Purchaseorder');
        $this->loadModel('JobChallanReceives');
        $dateto2 = date('Y-m-d', strtotime($date));

        $stockregister = $this->Stockregister->find('all')->contain(['Goodsreceived', 'Purchaseorder'])->where(['DATE(Stockregister.created)' => $dateto2, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '1', 'Stockregister.item_id' => $item_id])->order(['Stockregister.id' => 'DESC'])->toarray();

        if (empty($stockregister)) {

            $jcstockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created)' => $dateto2, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '1', 'Stockregister.item_id' => $item_id])->order(['Stockregister.id' => 'DESC'])->toarray();
            // pr($jcstockregister); die;

        }

        $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
        // pr($additem); die;
        $this->set(compact('additem'));
        $this->set(compact('stockregister'));
        $this->set(compact('jcstockregister'));
    }



    public function dispatchedstock($date, $item_id)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('JobChallanReceives');
        $dateto2 = date('Y-m-d', strtotime($date));

        $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created)' => $dateto2, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '2', 'Stockregister.item_id' => $item_id])->order(['Stockregister.id' => 'DESC'])->toarray();


        $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
        $this->set(compact('additem'));
        $this->set(compact('stockregister'));
    }


    public function getitemname()
    {


        $this->loadModel('Additem');
        $this->loadModel('Sizemanager');
        //pr($this->request->data); die;
        $stsearch = $this->request->data['fetch'];
        $check = $this->request->data['check'];
        //echo $stsearch; die;       
        $searchst = $this->Additem->find('all')->where(['Additem.item_name LIKE' => '%' . $stsearch . '%', 'Additem.status' => 'Y'])->toarray();
        // pr($searchst); die;        

        foreach ($searchst as $value) {
            if ($value['size_id'] != "") {
                $sizedetail = $this->Sizemanager->find('all')->select(['id', 'size_name'])->where(['Sizemanager.id' => $value['size_id']])->first();
                //pr($sizedetail); die
                if ($sizedetail['id'] == 6) {
                    if ($check == 0) {
                        echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $value['size_id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
                    } else {

                        echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['size_id'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . '</a></li>';
                    }
                } else {

                    if ($check == 0) {
                        echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . ' (' . $sizedetail['size_name'] . ')' . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $value['size_id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . ' (' . $sizedetail['size_name'] . ')' . '</a></li>';
                    } else {
                        echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['item_name'] . ' (' . $sizedetail['size_name'] . ')' . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $value['size_id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . ' (' . $sizedetail['size_name'] . ')' . '</a></li>';
                    }
                }
            } else {
                if ($check == 0) {
                    echo '<li onclick="cllbckretail(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)">' . $value['item_name'] . '</a></li>';
                } else {
                    echo '<li style="padding: 5px 8px; border: 1px solid lightgray;" onclick="cllbckretail' . $check . '(' . "'" . $value['item_name'] . "'" . ',' . "'" . $value['id'] . "'" . ',' . "'" . $i . "'" . ')"><a href="javascript:void(0)" style="color: black;">' . $value['item_name'] . '</a></li>';
                }
            }
        }

        die;
    }

    public function searchstockregister()
    {
        Configure::write('debug', false);
        $this->loadModel('Stockregister');

        $item_id = isset($this->request->data['item_id']) ? $this->request->data['item_id'] : '';
        $category_ids = isset($this->request->data['category_id']) ? $this->request->data['category_id'] : [];
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));

        // Server-side Date Validation
        $d1 = new \DateTime($datefrom);
        $d2 = new \DateTime($dateto2);
        $diff = $d1->diff($d2)->days;
        // Also check if dateto is before datefrom
        $isNegative = ($d1 > $d2);

        if ($isNegative) {
            echo '<tr><td colspan="10" align="center" style="color:red; font-weight:bold;">Date To cannot be earlier than Date From.</td></tr>';
            die;
        }

        if (empty($item_id)) {
            if ($diff > 0) {
                echo '<tr><td colspan="10" align="center" style="color:red; font-weight:bold;">Only a single date is allowed for the Consolidated Stock Register.</td></tr>';
                die;
            }
            if ($diff == 0 && empty($category_ids)) {
                echo '<tr><td colspan="10" align="center" style="color:red; font-weight:bold;">Please choose at least one category to generate the Consolidated report.</td></tr>';
                die;
            }
            $consolidatedData = $this->_getConsolidatedStock($datefrom, $dateto2, $category_ids);
            $this->set(compact('consolidatedData'));
        } else {
            if ($diff > 31) {
                echo '<tr><td colspan="10" align="center" style="color:red; font-weight:bold;">The maximum allowed date range for the Product-wise Stock Register is one month.</td></tr>';
                die;
            }
            $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->first();
        }

        $this->set(compact('item_id'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
        $this->set(compact('category_ids'));
    }


    public function summaryexcel($datefrom = null, $dateto = null, $item_id = null)
    {
        Configure::write('debug', false);
        //   $this->viewBuilder()->layout('admin'); 
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('SitesettingsDetails');


        if ($this->request->is('post')) {
            $datefrom = $this->request->data['datefrom'] ?? $datefrom;
            $dateto = $this->request->data['dateto'] ?? $dateto;
            $item_id = $this->request->data['item_id'] ?? $item_id;
            $category_ids = $this->request->data['category_id'] ?? [];
        } else {
            $category_ids = [];
        }

        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto));

        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact('site_details'));

        if (empty($item_id)) {
            $consolidatedData = $this->_getConsolidatedStock($datefrom, $dateto2, $category_ids);
            $this->set(compact('consolidatedData'));
        } else {
            $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->first();
            $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
            $this->set(compact('additem'));
        }

        $this->set(compact('item_id'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
    }

    public function detailedexcel($datefrom = null, $dateto = null, $item_id = null)
    {
        Configure::write('debug', false);
        //   $this->viewBuilder()->layout('admin'); 
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');
        $this->loadModel('SitesettingsDetails');


        if ($this->request->is('post')) {
            $datefrom = $this->request->data['datefrom'] ?? $datefrom;
            $dateto = $this->request->data['dateto'] ?? $dateto;
            $item_id = $this->request->data['item_id'] ?? $item_id;
            $category_ids = $this->request->data['category_id'] ?? [];
        } else {
            $category_ids = [];
        }

        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto));

        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact('site_details'));

        if (empty($item_id)) {
            $consolidatedData = $this->_getConsolidatedStock($datefrom, $dateto2, $category_ids);
            $this->set(compact('consolidatedData'));
        } else {
            $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'DATE(Stockregister.created) <=' => $dateto, 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['1', '2'], 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->toarray();
            $this->set(compact('stockregister'));
            $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
            $this->set(compact('additem'));
        }

        $this->set(compact('item_id'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
    }


    //    public function daily_stockreport()
    //    {

    //     $this->loadModel('StockAvailable'); 
    //     $dbname = $this->request->session()->read('Auth.User.db'); 
    //     $branch = explode("_",$dbname);
    //     if($dbname != $branch[0]){
    //         $this->connection(trim($branch[0]));
    //     }



    //       $this->loadModel('Additem'); 

    //         $additem=$this->Additem->find('all')->toarray();

    //         $stock_avl =  [];
    //         foreach($additem as $key=>$value){
    //             $additem=$this->StockAvailable->find('all')->where(['StockAvailable.item_id'=>$value['id']])->first();
    //             $stock_avl['item_name'] =  $value['item_name'];
    //             $stock_avl['stock_available'] =  $additem['stock_available'];
    //             $stock_val_data[] = $stock_avl;
    //         }
    //          //pr($stock_val_data); die;

    //      $this->set('stock_report',$stock_val_data);


    //    }

    public function daily_stockreport()
    {

        $this->loadModel('Stockregister');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        // pr($branch); die;
        if ($dbname != $branch[0]) {
            $this->connection(trim($branch[0]));
        }
        $this->loadModel('Additem');


        $additem = $this->Additem->find('all')->toarray();
        // pr($additem); die;
        $stock_avl = [];
        foreach ($additem as $key => $value) {

            $opening_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type IN' => ['0']])->first();

            $goodsreceived_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type IN' => ['1']])->first();

            $added_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type IN' => ['0', '1']])->first();

            $sold_stock = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type' => '2'])->first();

            $sale_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type' => 3])->first();

            if ($dbname == "canvas") {
                $purchase_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type' => 5])->first();
            } else {
                $purchase_retrun = $this->Stockregister->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $value['id'], 'Stockregister.store_type' => 6])->first();
            }


            $avlstock = $added_stock['sum'] - $sold_stock['sum'];
            $final_stock = $avlstock - $sale_retrun['sum'];
            $stock_avl['item_id'] = $value['id'];
            $stock_avl['item_name'] = $value['item_name'];
            $stock_avl['stock_available'] = $final_stock;
            $stock_avl['stock_sold'] = $sold_stock['sum'];
            $stock_avl['opening_stock'] = $opening_stock['sum'];
            $stock_avl['sale_return'] = $sale_retrun['sum'];
            $stock_avl['gr_stock'] = $goodsreceived_stock['sum'];
            $stock_avl['purchase_return'] = $purchase_retrun['sum'];

            $stock_val_data[] = $stock_avl;
        }

        // pr($stock_val_data); die;

        $this->set('stock_report', $stock_val_data);
    }

    public function dailystock()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Itemcategory');

        $categortyname =  $this->Itemcategory->find('all')->select(['keyField' => 'id', 'valueField' => 'category_name'])->where(['id !=' => 25])->order(['Itemcategory.category_name' => 'ASC'])->toarray();
        // pr($categortyname);exit;
        $this->set('categortyname', $categortyname);
    }


    // public function searchstock()
    // {
    //     $this->loadModel('Additem');
    //     $req_data = $_GET;
    //     $searchdate = [];
    //     $searchdate[] = $req_data['datefrom'];
    //     $searchdate[] = $req_data['product'];
    //     $this->request->session()->write('searchdate', $searchdate);
    //     $additem = $this->Additem->find('all')->where(['Additem.itemtype' => 'RawMaterial', 'Additem.status' => 'Y'])->order(['Additem.item_name' => 'ASC'])->toarray();
    //     $this->set('additem', $additem);
    // }


    public function searchstock()
    {
        $this->loadModel('Additem');
        $req_data = $_GET;
        $searchdate = [];
        $datefrom = $req_data['datefrom'] ?? date('Y-m-d');
        $searchdate[] = $datefrom;
        $category_ids = $req_data['product'] ?? [];
        $searchdate[] = $category_ids;
        $this->request->session()->write('searchdate', $searchdate);

        $dailyStockData = $this->_getDailyStockAsOfDate(date('Y-m-d', strtotime($datefrom)), $category_ids);

        $this->set(compact('dailyStockData', 'datefrom', 'category_ids'));
    }

    private function _getDailyStockAsOfDate($date, $category_ids = [])
    {
        $this->loadModel('Additem');
        $conditions = [
            'Additem.itemtype' => 'RawMaterial',
            'Additem.status' => 'Y',
            'Additem.category_id NOT IN' => [25]
        ];

        if (!empty($category_ids) && !in_array('All', $category_ids) && !in_array(1, $category_ids)) {
            $conditions['Additem.category_id IN'] = $category_ids;
        }

        $products = $this->Additem->find('all')
            ->contain(['Itemcategory', 'Measurementunit'])
            ->where($conditions)
            ->order(['Additem.item_name' => 'ASC'])
            ->toArray();

        if (empty($products)) {
            return [];
        }

        $productIds = [];
        foreach ($products as $p) {
            $productIds[] = $p->id;
        }

        $conn = \Cake\Datasource\ConnectionManager::get('default');
        $itemIdsStr = implode(',', $productIds);

        // 1. Opening Balances on $date (Matches CommanHelper::stockregisteropening2 / _getConsolidatedStock)
        $sqlOpening = "
            SELECT item_id, 
            SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END) as grn_sum,
            SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END) as indent_sum
            FROM st_stock_register 
            WHERE item_id IN ($itemIdsStr) AND issue_date < :date
            GROUP BY item_id
        ";
        $openingData = $conn->execute($sqlOpening, ['date' => $date])->fetchAll('assoc');
        $openingLookup = [];
        foreach ($openingData as $row) {
            $openingLookup[$row['item_id']] = round((float)$row['grn_sum'] - (float)$row['indent_sum'], 2);
        }

        // 2. Today's Received & Reverse (Uses DATE(issue_date) like CommanHelper::stockregisteropeningrecivied)
        $sqlReceived = "
            SELECT item_id, 
            SUM(CASE WHEN store_type IN ('0','1') THEN quantity ELSE 0 END) as received_qty,
            SUM(CASE WHEN store_type IN ('3') THEN quantity ELSE 0 END) as reverse_qty
            FROM st_stock_register
            WHERE status != 'N' AND item_id IN ($itemIdsStr) AND store_type IN ('0','1','3') 
              AND DATE(issue_date) = :date
            GROUP BY item_id
        ";
        $receivedData = $conn->execute($sqlReceived, ['date' => $date])->fetchAll('assoc');
        $receivedLookup = [];
        foreach ($receivedData as $row) {
            $receivedLookup[$row['item_id']] = $row;
        }

        // 3. Today's Issued & Return (Uses DATE(created) like CommanHelper::stockregisteropeningdispatched)
        $sqlIssued = "
            SELECT item_id, 
            SUM(CASE WHEN store_type IN ('2') THEN quantity ELSE 0 END) as issued_qty,
            SUM(CASE WHEN store_type IN ('4') THEN quantity ELSE 0 END) as return_qty
            FROM st_stock_register
            WHERE status != 'N' AND item_id IN ($itemIdsStr) AND store_type IN ('2','4') 
              AND DATE(created) = :date
            GROUP BY item_id
        ";
        $issuedData = $conn->execute($sqlIssued, ['date' => $date])->fetchAll('assoc');
        $issuedLookup = [];
        foreach ($issuedData as $row) {
            $issuedLookup[$row['item_id']] = $row;
        }

        $dailyStockData = [];
        foreach ($products as $product) {
            $pId = $product->id;
            
            $opening = $openingLookup[$pId] ?? 0.0;
            $received = isset($receivedLookup[$pId]) ? (float)$receivedLookup[$pId]['received_qty'] : 0.0;
            $reverse = isset($receivedLookup[$pId]) ? (float)$receivedLookup[$pId]['reverse_qty'] : 0.0;
            $issued = isset($issuedLookup[$pId]) ? (float)$issuedLookup[$pId]['issued_qty'] : 0.0;
            $return = isset($issuedLookup[$pId]) ? (float)$issuedLookup[$pId]['return_qty'] : 0.0;

            // Closing formula matching Stock Register EXACTLY
            // Stock Register: Opening + (Received + Reverse) - (Issued + Return)
            $closing = $opening + $received + $reverse - $issued - $return;

            if ($opening == 0 && $received == 0 && $issued == 0 && $reverse == 0 && $return == 0 && $closing == 0) {
                if (empty($category_ids) || in_array('All', $category_ids)) {
                    continue; 
                }
            }

            $dailyStockData[] = [
                'item_id' => $pId,
                'item_name' => $product->item_name,
                'category_name' => isset($product->itemcategory) ? $product->itemcategory->category_name : '',
                'opening_stock' => number_format(round($opening, 2), 2, '.', ''),
                'received_stock' => number_format(round($received, 2), 2, '.', ''),
                'issued_stock' => number_format(round($issued, 2), 2, '.', ''),
                'reverse_stock' => number_format(round($reverse, 2), 2, '.', ''),
                'return_stock' => number_format(round($return, 2), 2, '.', ''),
                'closing_stock' => number_format(round($closing, 2), 2, '.', '')
            ];
        }

        return $dailyStockData;
    }



    public function dailystockexcel()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemcategory');
        $this->loadModel('SitesettingsDetails');

        $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
        $this->set(compact('site_details'));

        if ($this->request->is('post')) {
            $date = $this->request->data['datefrom'] ?? date('Y-m-d');
            $categoryIds = $this->request->data['category_ids'] ?? [];
            $searchdate = [$date, $categoryIds];
        } else {
            $searchdate = $this->request->session()->read('searchdate');
            $date = !empty($searchdate) ? $searchdate[0] : date('Y-m-d');
            $categoryIds = !empty($searchdate) ? $searchdate[1] : [];
        }

        $dailyStockData = $this->_getDailyStockAsOfDate(date('Y-m-d', strtotime($date)), $categoryIds);
        $this->set(compact('dailyStockData', 'searchdate'));
    }




    public function required_stock()
    {
        $this->viewBuilder()->layout('admin');

        $this->loadModel('Additem');
        $this->loadModel('Productionorder');

        if ($_GET['datefrom'] && $_GET['dateto']) {
            $startDate = $_GET['datefrom'];
            $endDate = $_GET['dateto'];
        } else {
            $currentMonth = date('m');
            $currentYear = date('Y');
            $startDate = date('Y-m-d', strtotime("$currentYear-$currentMonth-01"));
            $endDate = date('Y-m-d', strtotime("$currentYear-$currentMonth-" . date('t', strtotime($startDate))));
        }

        $contracts = $this->Productionorder->find('all')->where(['startdate >=' => $startDate, 'startdate <=' => $endDate])->group(['contract_id'])->toarray();
        $products =  $this->Additem->find('all')->where(['itemtype' => 'RawMaterial', 'Additem.status' => 'Y'])->order(['item_name' => 'ASC']);
        $products = $this->paginate($products)->toarray();

        $this->set(compact('contracts', 'startDate', 'endDate', 'products'));
    }


    public function search_required_stock()
    {
        $this->loadModel('Additem');
        $this->loadModel('Productionorder');
        $startDate = date('Y-m-d', strtotime($_GET['datefrom']));
        $endDate = date('Y-m-d', strtotime($_GET['dateto']));
        $item_id = $_GET['item_id'];
        $contracts = $this->Productionorder->find('all')->where(['startdate >=' => $startDate, 'startdate <=' => $endDate])->group(['contract_id'])->toarray();
        $products =  $this->Additem->find('all')->where(['itemtype' => 'RawMaterial', 'id' => $item_id, 'Additem.status' => 'Y'])->order(['item_name' => 'ASC']);
        $products = $this->paginate($products)->toarray();
        $this->set(compact('contracts', 'startDate', 'endDate', 'products'));
    }

    public function weeklystockexcel()
    {

        $this->loadModel('Additem');
        $this->loadModel('Itemcategory');


        $additem = $this->Additem->find('all')->contain(['Itemcategory'])->where(['Additem.itemtype' => 'RawMaterial', 'Additem.status' => 'Y', 'Itemcategory.status' => 'Y', 'Itemcategory.is_print' => 'Y'])->order(['Additem.item_name' => 'ASC'])->toarray();
        $this->set('additem', $additem);
    }

    private function _getConsolidatedStock($datefrom, $dateto2, $category_ids = [])
    {
        $this->loadModel('Additem');

        $conditions = [
            'Additem.itemtype' => 'RawMaterial',
            'Additem.status' => 'Y',
            'Additem.category_id NOT IN' => [25]
        ];

        if (!empty($category_ids)) {
            $conditions['Additem.category_id IN'] = $category_ids;
        }

        // Fetch eligible products
        $products = $this->Additem->find('all')
            ->contain(['Itemcategory', 'Measurementunit'])
            ->where($conditions)
            ->order(['Additem.item_name' => 'ASC'])
            ->toArray();

        if (empty($products)) {
            return [];
        }

        $productIds = [];
        foreach ($products as $p) {
            $productIds[] = $p->id;
        }

        $conn = \Cake\Datasource\ConnectionManager::get('default');
        $itemIdsStr = implode(',', $productIds);

        // 1. Opening Balances on $datefrom (Matches CommanHelper::stockregisteropening2)
        $sqlOpening = "
            SELECT item_id, 
            SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END) as grn_sum,
            SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END) as indent_sum
            FROM st_stock_register 
            WHERE item_id IN ($itemIdsStr) AND issue_date < :datefrom
            GROUP BY item_id
        ";
        $openingData = $conn->execute($sqlOpening, ['datefrom' => $datefrom])->fetchAll('assoc');
        $openingLookup = [];
        foreach ($openingData as $row) {
            $openingLookup[$row['item_id']] = round((float)$row['grn_sum'] - (float)$row['indent_sum'], 2);
        }

        // 2. Received Stock (Matches CommanHelper::stockregisteropeningrecivied)
        $sqlReceived = "
            SELECT item_id, DATE(issue_date) as t_date, SUM(quantity) as qty
            FROM st_stock_register
            WHERE status != 'N' AND item_id IN ($itemIdsStr) AND store_type IN ('0','1','3') 
              AND DATE(issue_date) >= :datefrom AND DATE(issue_date) <= :dateto
            GROUP BY item_id, DATE(issue_date)
        ";
        $receivedData = $conn->execute($sqlReceived, ['datefrom' => $datefrom, 'dateto' => $dateto2])->fetchAll('assoc');
        $receivedLookup = [];
        foreach ($receivedData as $row) {
            $receivedLookup[$row['item_id']][$row['t_date']] = round((float)$row['qty'], 2);
        }

        // 3. Dispatched Stock (Matches CommanHelper::stockregisteropeningdispatched)
        $sqlDispatched = "
            SELECT item_id, DATE(created) as t_date, SUM(quantity) as qty
            FROM st_stock_register
            WHERE status != 'N' AND item_id IN ($itemIdsStr) AND store_type IN ('2','4') 
              AND DATE(created) >= :datefrom AND DATE(created) <= :dateto
            GROUP BY item_id, DATE(created)
        ";
        $dispatchedData = $conn->execute($sqlDispatched, ['datefrom' => $datefrom, 'dateto' => $dateto2])->fetchAll('assoc');
        $dispatchedLookup = [];
        foreach ($dispatchedData as $row) {
            $dispatchedLookup[$row['item_id']][$row['t_date']] = round((float)$row['qty'], 2);
        }

        $date_from_time = strtotime($datefrom);
        $date_to_time = strtotime($dateto2);

        $consolidatedData = [];
        $previousClosingStock = [];

        for ($i = $date_from_time; $i <= $date_to_time; $i += 86400) {
            $currDate = date('Y-m-d', $i);

            foreach ($products as $product) {
                $pId = $product->id;

                if ($i == $date_from_time) {
                    $openingStock = $openingLookup[$pId] ?? 0.0;
                } else {
                    $openingStock = $previousClosingStock[$pId] ?? 0.0;
                }

                $receivedStock = $receivedLookup[$pId][$currDate] ?? 0.0;
                $dispatchedStock = $dispatchedLookup[$pId][$currDate] ?? 0.0;

                $closingStock = round($openingStock + $receivedStock - $dispatchedStock, 2);
                $previousClosingStock[$pId] = $closingStock;

                if ($openingStock == 0 && $receivedStock == 0 && $dispatchedStock == 0 && $closingStock == 0) {
                    continue;
                }

                $consolidatedData[] = [
                    'item_id' => $pId,
                    'date' => $currDate,
                    'product_code' => $product->item_isbn,
                    'product_name' => $product->item_name,
                    'category' => isset($product->itemcategory) ? $product->itemcategory->category_name : '',
                    'unit' => isset($product->measurementunit) ? $product->measurementunit->measurement_name : '',
                    'opening' => $openingStock,
                    'received' => $receivedStock,
                    'dispatched' => $dispatchedStock,
                    'closing' => $closingStock
                ];
            }
        }

        return $consolidatedData;
    }
}
