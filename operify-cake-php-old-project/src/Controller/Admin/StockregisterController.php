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
        //   $this->viewBuilder()->layout('admin'); 


        //     $dbname = $this->request->session()->read('Auth.User.db'); 

        //     if($dbname == "canvas"){
        // 	    $this->loadModel('Stockregister'); 

        //     }else{

        //     $branch = explode("_",$dbname);
        //     if($dbname != $branch[0]){

        //     }

        //        $this->connection(trim($branch[0]));

        // }
        $this->loadModel('Stockregister');

        $item_id = $this->request->data['item_id'];
        $datefrom = date('Y-m-d', strtotime($this->request->data['datefrom']));
        $dateto2 = date('Y-m-d', strtotime($this->request->data['dateto']));
        // pr($item_id);

        // pr($datefrom);
        // pr($dateto2);die;
        $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->first();
        // $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'DATE(Stockregister.created) <=' => $dateto, 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['1', '2'], 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->toarray();
        // $this->set(compact('stockregister'));
        // pr($stockregister);die;
        // $datefrom = date('Y-m-d', strtotime($stockregister['created']));
        $this->set(compact('item_id'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));
    }


    public function summaryexcel($datefrom, $dateto, $item_id)
    {
        //   $this->viewBuilder()->layout('admin'); 
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');

        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto));


        $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->first();

        // $datefrom = date('Y-m-d', strtotime($stockregister['created']));
        $this->set(compact('item_id'));
        $this->set(compact('datefrom'));
        $this->set(compact('dateto2'));


        $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
        $this->set(compact('additem'));
    }

    public function detailedexcel($datefrom, $dateto, $item_id)
    {
        //   $this->viewBuilder()->layout('admin'); 
        $this->loadModel('Stockregister');
        $this->loadModel('Additem');

        $datefrom = date('Y-m-d', strtotime($datefrom));
        $dateto2 = date('Y-m-d', strtotime($dateto));

        $stockregister = $this->Stockregister->find('all')->where(['DATE(Stockregister.created) >=' => $datefrom, 'DATE(Stockregister.created) <=' => $dateto, 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['1', '2'], 'Stockregister.status !=' => 'N'])->order(['Stockregister.id' => 'ASC'])->toarray();
        $this->set(compact('stockregister'));
        $this->set(compact('item_id'));
        $additem = $this->Additem->find('all')->where(['Additem.id' => $item_id])->first();
        $this->set(compact('additem'));
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

        $categortyname =  $this->Itemcategory->find('all')->select(['keyField' => 'id', 'valueField' => 'category_name'])->order(['Itemcategory.category_name' => 'ASC'])->toarray();
        // pr($categortyname);exit;
        $this->set('categortyname', $categortyname);
    }


    public function searchstock()
    {
        $this->loadModel('Additem');
        $req_data = $_GET;
        $searchdate = [];
        $searchdate[] = $req_data['datefrom'];
        $searchdate[] = $req_data['product'];
        $this->request->session()->write('searchdate', $searchdate);
        $additem = $this->Additem->find('all')->where(['Additem.itemtype' => 'RawMaterial', 'Additem.status' => 'Y'])->order(['Additem.item_name' => 'ASC'])->toarray();
        $this->set('additem', $additem);
    }

    // old code
    // public function dailystockexcel()
    // {
    //     $this->loadModel('Additem');
    //     $this->loadModel('Itemcategory');
    //     $searchdate = $this->request->session()->read('searchdate');

    //     $cond = [];

    //     $cond['Itemcategory.id'] = $searchdate[1];
    //     if ($searchdate[1] == 1) {
    //         $categortyname =  $this->Itemcategory->find('all')->order(['Itemcategory.category_name' => 'ASC'])->toarray();
    //     } else {
    //         $categortyname =  $this->Itemcategory->find('all')->where([$cond])->order(['Itemcategory.category_name' => 'ASC'])->toarray();
    //     }


    //     if ($searchdate != '') {
    //         $this->set('categortyname', $categortyname);
    //         $this->set('searchdate', $searchdate);
    //         $this->request->session()->delete('searchdate');
    //     } else {
    //         $searchdate = date('Y-m-d');
    //         $this->set('categortyname', $categortyname);
    //     }
    // }


    public function dailystockexcel()
    {
        $this->loadModel('Additem');
        $this->loadModel('Itemcategory');

        // $searchdate = $this->request->getSession()->read('searchdate');
        $searchdate = $this->request->session()->read('searchdate');


        if (!empty($searchdate)) {

            $date        = $searchdate[0];   // 03-02-2026
            $categoryIds = $searchdate[1];   // [25,144,33]

            if (in_array(1, $categoryIds)) {
                $categortyname = $this->Itemcategory
                    ->find('all')
                    ->order(['Itemcategory.category_name' => 'ASC'])
                    ->toArray();
            } else {
                $categortyname = $this->Itemcategory
                    ->find('all')
                    ->where(['Itemcategory.id IN' => $categoryIds])
                    ->order(['Itemcategory.category_name' => 'ASC'])
                    ->toArray();
            }

            $this->set(compact('categortyname', 'searchdate'));
            $this->request->session()->delete('searchdate');
        } else {

            $searchdate = date('Y-m-d');

            $categortyname = $this->Itemcategory
                ->find('all')
                ->order(['Itemcategory.category_name' => 'ASC'])
                ->toArray();

            $this->set(compact('categortyname', 'searchdate'));
        }
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
}
