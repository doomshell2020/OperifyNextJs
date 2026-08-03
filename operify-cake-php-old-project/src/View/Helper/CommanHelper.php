<?php

namespace App\View\Helper;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;
use Cake\View\View;
use Firebase\JWT\JWT;

class CommanHelper extends Helper
{
    // initialize() hook is available since 3.2. For prior versions you can
    // override the constructor if required.
    public function initialize(array $config) {}

    public function findsub_location($id)
    {
        $articles = TableRegistry::get('Itemlocation');
        return $articles->find('all')->where(['Itemlocation.parent' => $id])->count();
    }

    public function findrolemenucontent($module = null)
    {
        $articles = TableRegistry::get('PermissionModules');
        $ids = $this->request->session()->read('Auth.User.id');
        $role_id = $this->request->session()->read('Auth.User.role_id');

        return $articles->find('all')->where(['PermissionModules.user_id' => $ids, 'PermissionModules.module' => $module, 'PermissionModules.featured' => '0', 'PermissionModules.status' => 'Y'])->order(['PermissionModules.sort_no' => 'asc'])->toarray();
    }


    public function findrolemenu()
    {

        $articles = TableRegistry::get('PermissionModules');
        $ids = $this->request->session()->read('Auth.User.id');
        $role_id = $this->request->session()->read('Auth.User.role_id');

        return $articles->find('all')->where(['PermissionModules.user_id' => $ids, 'PermissionModules.featured' => '0', 'PermissionModules.status' => 'Y'])->group(['PermissionModules.module'])->order(['PermissionModules.sort_no' => 'asc'])->toarray();
    }

    public function findstatus($id = null, $date = null)
    {
        $articles = TableRegistry::get('States');
        return $articles->find()->select(['EmployeeAttendance.employee_id'])->where(['EmployeeAttendance.employee_id' => $id, 'EmployeeAttendance.status' => 'P', 'EmployeeAttendance.date' => $date])->first();
    }
    // get
    public function getuser($id = null)
    {
        $articles = TableRegistry::get('Users');
        return $articles->find('all')->where(['Users.id' => $id])->first();
    }
    public function stockavailable($id = null)
    {
        $articles = TableRegistry::get('Stockregister');
        $added_stock = $articles->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type IN' => ['0', '1']])->first();

        $sold_stock = $articles->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type' => '2'])->first();

        $sale_retrun = $articles->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type' => 3])->first();

        $purchase_retrun = $articles->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.item_id' => $id, 'Stockregister.store_type' => 5])->first();

        $avlstock = $added_stock['sum'] - $sold_stock['sum'];
        $final_stock = $avlstock - $sale_retrun['sum'];
        $total_finel_result = $final_stock - $purchase_retrun['sum'];

        return $total_finel_result;
    }

    public function findlogo()
    {
        $articles = TableRegistry::get('SitesettingsDetails');
        return  $articles->find('all')->first();
    }

    public function getHTML($url, $timeout)
    {
        $ch = curl_init($url); // initialize curl with given url
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
        return @curl_exec($ch);
    }
    ///////////////////////////////////////////////////

    public function mrncheck($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $connss = ConnectionManager::get($dbname);
        $studentrfidsd = $connss->execute("SELECT * FROM `st_mrn` where `purchase_order_no`=" . $id);
        return $studentrfidsd->fetchAll('assoc');
    }

    public function findcities($id)
    {
        $articles = TableRegistry::get('Cities');
        return $articles->find('all')->select(['id', 'name'])->where(['id' => $id])->toArray();
    }

    public function findstates($id)
    {
        $articles = TableRegistry::get('States');
        return $articles->find('all')->select(['id', 'name'])->where(['id' => $id])->first();
    }
    public function findimages($id)
    {
        $articles = TableRegistry::get('imagees');
        return $articles->find('all')->select(['id', 'name'])->where(['JOBSEEKER_id' => $id])->toArray();
    }



    public function findcountries($id)
    {
        $articles = TableRegistry::get('Country');
        return $articles->find('all')->select(['id', 'name'])->where(['id' => $id])->toArray();
    }
    public function indentdetail($id)
    {
        $articles = TableRegistry::get('Indent');
        return $articles->find('all')->contain(['Additem'])->where(['Indent.indent_id' => $id])->toarray();
    }
    public function indentdetails($id, $item)
    {
        $articles = TableRegistry::get('indentpo');
        return $articles->find('all')->where(['indentpo.indent_id' => $id])->first();
    }
    public function indentitemquantity($id)
    {
        $articles = TableRegistry::get('Indent');
        return $articles->find('all')->select(['quantity' => $articles->find('all')->func()->sum('Indent.quantity')])->where(['Indent.indent_id' => $id])->order(['Indent.id' => 'DESC'])->toarray();
    }
    public function getindentw($id, $status)
    {
        $articles = TableRegistry::get('Indent');
        return $articles->find('all')->contain(['Additem'])->where(['Indent.indent_id' => $id, 'Indent.status' => $status])->toarray();
    }
    public function getindent($id, $status)
    {
        $articles = TableRegistry::get('Indenttemp');
        return $articles->find('all')->contain(['Additem'])->where(['Indenttemp.indent_id' => $id, 'Indenttemp.status' => $status])->toarray();
    }
    public function getitemcatcom($id)
    {
        $articles = TableRegistry::get('Additem');
        return $articles->find('all')->contain(['Itemcategory', 'Measurementunit'])->where(['Additem.id' => $id])->first();
    }
    public function getitembycategory($categoryid)
    {
        // $articles = TableRegistry::get('Additem');
        // return $articles->find('all')->where(['Additem.category_id' => $categoryid, 'Additem.itemtype' => 'RawMaterial', 'Additem.status' => 'Y'])->order(['Additem.item_name' => 'ASC'])->toarray();
        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_additem')->where(['category_id' => $categoryid, 'itemtype' => 'RawMaterial', 'status' => 'Y'])->order(['item_name' => 'ASC']);
        return $query->execute()->fetchAll('assoc');
    }
    public function getitemname($id)
    {
        // $articles = TableRegistry::get('Additem');
        // return $articles->find('all')->where(['Additem.id' => $id])->first();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_additem')->where(['id' => $id]);
        return $query->execute()->fetch('assoc');
    }

    public function getdesignsheetitemname($item_id, $designsheet_id)
    {
        // $articles = TableRegistry::get('Additem');
        // return $articles->find('all')->where(['Additem.id' => $id])->first();
        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('designsheetdetails')->where(['item_id' => $item_id, 'designsheet_id' => $designsheet_id]);
        return $query->execute()->fetch('assoc');
    }
    public function getcategorynmae($id)
    {
        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_categorymaster')->where(['id' => $id]);
        return $query->execute()->fetch('assoc');
    }
    public function getsizename($id)
    {
        $articles = TableRegistry::get('Sizemanager');
        return $articles->find('all')->where(['Sizemanager.id' => $id])->first();
    }
    public function poitemquantity($poid, $isrevise, $id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.po_id' => $poid, 'Stockregister.purchaseorder_id' => $id, 'Stockregister.is_revised' => $isrevise])->toarray();
    }
    public function podetail($poid, $isrevise, $id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.po_id' => $poid, 'Stockregister.purchaseorder_id' => $id, 'Stockregister.store_type' => 0, 'Stockregister.is_revised' => $isrevise])->toarray();
    }
    public function podetailupdated($poid, $isrevise)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.purchaseorder_id' => $poid, 'Stockregister.store_type' => 1, 'Stockregister.is_revised' => $isrevise])->toarray();
    }
    // for check grn in  stockregister
    public function checkgrn($poid, $id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.po_id' => $poid, 'Stockregister.purchaseorder_id' => $id, 'Stockregister.store_type' => 1])->toarray();
    }
    // to get reverse indent details
    public function reverseindent($reverse_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.reverse_id' => $reverse_id])->toarray();
    }
    public function findvendornames($vendorid)
    {
        $articles = TableRegistry::get('Vendor');
        return $articles->find('all')->where(['Vendor.id' => $vendorid])->first();
    }

    public function lprcost($item_id)
    {
        $articles = TableRegistry::get('PurchaseorderDetails');
        $rsull = $articles->find('all')->select('item_amt')->where(['PurchaseorderDetails.item_id' => $item_id])->group('PurchaseorderDetails.purchaseorder_id')->order(['PurchaseorderDetails.inward_date' => 'DESC', 'PurchaseorderDetails.id' => 'DESC'])->first();
        return $rsull['item_amt'];
    }
    public function getPurchaseOrder($poid)
    {
        $articles = TableRegistry::get('Purchaseorder');
        return $articles->find('all')->where(['Purchaseorder.purchaseorder_id' => $poid])->order(['Purchaseorder.id' => 'DESC'])->first();
    }
    public function PurchaseOrderDetails($id, $item_id)
    {
        // $articles = TableRegistry::get('PurchaseorderDetails');
        // return $articles->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $id, 'PurchaseorderDetails.item_id' => $item_id])->first();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_purchaseorderDetails')->where(['purchaseorder_id' => $id, 'item_id' => $item_id]);
        return $query->execute()->fetch('assoc');
    }

    public function vendorbilltodetail($id)
    {
        $articles = TableRegistry::get('Vendorbillto');
        return $articles->find('all')->contain(['States', 'Cities'])->where(['Vendorbillto.vendor_id' => $id])->order(['Vendorbillto.id' => 'ASC'])->toarray();
    }

    public function vendorshipfromdetail($id)
    {
        $articles = TableRegistry::get('Vendorshipfrom');
        return $articles->find('all')->contain(['States', 'Cities'])->where(['Vendorshipfrom.vendor_id' => $id])->order(['Vendorshipfrom.id' => 'ASC'])->toarray();
    }
    public function getbeforerevisedpo($id)
    {
        // $articles = TableRegistry::get('Purchaseorder');
        // return $articles->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.status' => 'R'])->order(['Purchaseorder.id' => 'DESC'])->first();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_purchaseorder')->where(['purchaseorder_id' => $id, 'status' => 'R'])->order(['id' => 'DESC']);
        return $query->execute()->fetch('assoc');
    }

    public function pogett($id)
    {
        $articles = TableRegistry::get('Purchaseorder');
        return $articles->find('all')->where(['Purchaseorder.id' => $id])->order(['Purchaseorder.id' => 'DESC'])->first();
    }
    public function gettaxnameparent($id)
    {
        $articles = TableRegistry::get('Taxmaster');
        return $articles->find('all')->where(['Taxmaster.id' => $id, 'Taxmaster.parent' => '0'])->order(['Taxmaster.id' => 'DESC'])->toarray();
    }
    public function gettaxname($id)
    {
        $articles = TableRegistry::get('Taxmaster');
        return $articles->find('all')->where(['Taxmaster.id' => $id])->order(['Taxmaster.id' => 'DESC'])->first();
    }

    public function findtaxname($id)
    {
        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('st_taxmaster')->where(['id' => $id]);
        return $query->execute()->fetchAll('assoc');
    }
    public function gettaxname2($id)
    {
        $articles = TableRegistry::get('Taxmaster');
        return $articles->find('all')->where(['Taxmaster.id' => $id])->order(['Taxmaster.id' => 'DESC'])->toarray();
    }
    public function paymenttermsdetail()
    {
        $articles = TableRegistry::get('Paymentterms');
        return $articles->find('all')->where(['Paymentterms.status' => 'Y'])->order(['Paymentterms.id' => 'ASC'])->toarray();
    }
    public function getpoqty($id)
    {

        $articles = TableRegistry::get('Purchaseorder');
        return $articles->find('all')->where(['Purchaseorder.purchaseorder_id' => $id, 'Purchaseorder.status !=' => 'N'])->order(['Purchaseorder.id' => 'DESC'])->first();
    }

    public function getpostockitem($id, $item_id)
    {
        // $articles = TableRegistry::get('Stockregister');
        // return $articles->find('all')->where(['Stockregister.po_id' => $id, 'Stockregister.item_id' => $item_id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '0'])->order(['Stockregister.id' => 'DESC'])->first();
        $articles = TableRegistry::get('PurchaseorderDetails');
        return $articles->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $id, 'PurchaseorderDetails.item_id' => $item_id])->order(['PurchaseorderDetails.id' => 'DESC'])->first();
    }

    public function goodsrecivied($id, $sst)
    {
        $articles = TableRegistry::get('Goodsreceived');
        return $articles->find('all')->select(['quantity' => $articles->find('all')->func()->sum('Goodsreceived.total_qty')])->where(['Goodsreceived.purchaseorder_id' => $id, 'Goodsreceived.id <=' => $sst])->order(['Goodsreceived.id' => 'DESC'])->toarray();
    }

    public function findgoodsrecivied($poid)
    {
        $articles = TableRegistry::get('Goodsreceived');
        return $articles->find('all')->where(['Goodsreceived.purchaseorder_id' => $poid])->order(['Goodsreceived.id' => 'DESC'])->toarray();
    }
    public function findgoodsrecivieddate($id)
    {
        $articles = TableRegistry::get('Goodsreceived');
        return $articles->find('all')->where(['Goodsreceived.id' => $id])->order(['Goodsreceived.id' => 'DESC'])->first();
    }
    public function findrevisedno($id)
    {
        $articles = TableRegistry::get('Purchaseorder');
        return $articles->find('all')->where(['Purchaseorder.purchaseorder_id' => $id])->order(['Purchaseorder.id' => 'DESC'])->first();
    }

    public function findstock($goodsid)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Stockregister.status !=' => 'N', 'Stockregister.goods_id' => $goodsid, 'Stockregister.store_type' => '1'])->toarray();
    }

    public function stockregisteropening($date, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['DATE(Stockregister.created) <=' => $date, 'Stockregister.status !=' => 'N', 'Stockregister.item_id' => $item_id, 'Stockregister.store_type' => '1'])->order(['Stockregister.id' => 'DESC'])->toarray();
    }



    function stockregisteropening2($date, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created <' => $date, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created <' => $date, 'Stockregister.store_type IN' => ['2', '4']])->first();
        // pr($indentStock);die;
        return $currentStock = $grnStock['sum'] - $indentStock['sum'];
    }


    public function stockregisteritems($po_id, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.po_id' => $po_id, 'Stockregister.status !=' => 'N', 'Stockregister.item_id' => $item_id, 'Stockregister.store_type' => '1'])->order(['Stockregister.id' => 'DESC'])->first();
    }

    public function stockregisteropeningrecivied($date, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['DATE(Stockregister.created)' => $date, 'Stockregister.status !=' => 'N', 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->order(['Stockregister.id' => 'DESC'])->toarray();
    }

    public function stockregisteropeningdispatched($date, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $item_id, 'DATE(Stockregister.created)' => $date, 'Stockregister.status !=' => 'N', 'Stockregister.store_type IN' => ['2', '4']])->order(['Stockregister.id' => 'DESC'])->toarray();
    }

    public function stockregisteropeningdispatchedall($date, $item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['quantity' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['DATE(Stockregister.created) <' => $date, 'Stockregister.item_id' => $item_id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '2'])->order(['Stockregister.id' => 'DESC'])->toarray();
    }

    public function totalstockregisteropeningrecivied($item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['quantity' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.status !=' => 'N', 'Stockregister.item_id' => $item_id, 'Stockregister.store_type' => '4'])->order(['Stockregister.id' => 'DESC'])->toarray();
    }

    public function totalstockregisteropeningdispatched($item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $item_id, 'Stockregister.status !=' => 'N', 'Stockregister.store_type' => '2'])->order(['Stockregister.id' => 'DESC'])->toarray();
    }

    public function findheadermenu()
    {
        $articles = TableRegistry::get('PermissionModules');
        $ids = $this->request->session()->read('Auth.User.id');
        $data = $articles->find('all')->where(['PermissionModules.user_id' => $ids, 'PermissionModules.featured' => '0'])->order(['PermissionModules.featured_sort' => 'ASC'])->toarray();
        // pr($data);die;
        foreach ($data as $key => $value) {

            $data[$key]['short_name'] = $value['menu'];
        }
        return $data;
    }

    public function findstatuspermission($user = null, $menu = null, $controller = null, $action = null)
    {
        $articles = TableRegistry::get('PermissionModules');
        return $articles->find('all')->select(['PermissionModules.featured', 'PermissionModules.edit', 'PermissionModules.delete'])->where(['PermissionModules.user_id' => $user, 'PermissionModules.menu' => $menu, 'PermissionModules.controller' => $controller, 'PermissionModules.action' => $action])->order(['PermissionModules.id' => 'ASC'])->first();
    }


    public function branchcity($dbname = null)
    {
        $this->connection('operify');
        $connss = ConnectionManager::get('operify');
        $studentrfidsd = $connss->execute("SELECT * FROM `schools` where `school_database`='" . $dbname . "'");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function billtaxdatanew($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount += $total_amount - $total_discount;
        }
        return $taxable_amount;
    }

    public function billgst($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "'and item_tax ='" . $tax . "'");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;

            $taxable_amountdata += $total_amount - $total_discount;
            $tax_amountdata += $taxable_amount * $value['item_tax'] / 100;
            $totalamount_data += $taxable_amount + $tax_amount;
        }

        return $tax_amountdata;
    }

    public function billtotalamount($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);

        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `branchrequestdetail` where `branchrequest_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;
            $tax_amount = $taxable_amount * $value['item_tax'] / 100;
            $totalamount += $taxable_amount + $tax_amount;
        }

        return $totalamount;
    }


    public function soldtaxdatanew($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $connss = ConnectionManager::get($dbname);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount += $total_amount - $total_discount;
        }

        return $taxable_amount;
    }


    public function soldgst($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $connss = ConnectionManager::get($dbname);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;

            $taxable_amountdata += $total_amount - $total_discount;
            $tax_amountdata += $taxable_amount * $value['item_tax'] / 100;
            $totalamount_data += $taxable_amount + $tax_amount;
        }

        return $tax_amountdata;
    }

    public function soldtotalamount($tax, $id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $connss = ConnectionManager::get($dbname);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `solditemsdetail` where `sold_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;
            $tax_amount = $taxable_amount * $value['item_tax'] / 100;
            $totalamount += $taxable_amount + $tax_amount;
        }

        return $totalamount;
    }

    public function findschoolinformation($school_name)
    {
        $connss = ConnectionManager::get('operify');
        $school_information = $connss->execute("SELECT * FROM `schools` where `school_database`='" . $school_name . "' ");
        return $school_information->fetchAll('assoc');
    }

    public function schoollogo($school_name)
    {
        // pr($school_name);die;
        $connss = ConnectionManager::get($school_name);
        $school_information = $connss->execute("SELECT * FROM `sitesettings_details`");
        // pr($school_information);die;
        return $school_information->fetchAll('assoc');
    }

    public function finditems($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        return $studentrfidsd->fetchAll('assoc');
    }
    public function connection_query($dbs)
    {
        ConnectionManager::config($dbs, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => DBHOSTNAME,
            'username' => MYSQLUSERNAME,
            'password' => MYSQLPASSWORD,
            'database' => $dbs,
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ]);
        ConnectionManager::drop('default');
        ConnectionManager::get($dbs);
        \Cake\Datasource\ConnectionManager::alias($dbs, 'default');
    }

    public function connection($dbname)
    {
        ConnectionManager::config($dbname, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => DBHOSTNAME,
            'username' => MYSQLUSERNAME,
            'password' => MYSQLPASSWORD,
            'database' => $dbname,
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ]);
    }

    public function findgroupitemstore($category_id, $group_type, $branch_name)
    {

        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);

        $studentrfidsd = $connss->execute("SELECT * FROM `tempbranchrequest` where `category_id`='" . $category_id . "' and `group_type`='" . $group_type . "' and `branch_name`='" . $branch_name . "' ");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function branchdataget($dbnamese = null)
    {
        if ($this->request->session()->read('Auth.User.db') == $dbnamese) {
        } else {

            $connss = $this->connection($dbnamese);
        }
        $connss = ConnectionManager::get($dbnamese);
        $studentrfidsd = $connss->execute("SELECT * FROM `sitesettings`");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function branchdataget_detail($dbnamese = null)
    {

        $connss = ConnectionManager::get($dbnamese);
        $studentrfidsd = $connss->execute("SELECT * FROM `sitesettings_details`");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function checkUserEmail($email)
    {
        $token = array(
            "iss" => API_KEY,
            "exp" => time() + 3600 //60 seconds as suggested
        );
        $getJWTKey = JWT::encode($token, API_SECRET);
        $curl = curl_init();
        // $email = "yogesh@doomshell.com";
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.zoom.us/v2/users/email?email=' . $email,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $getJWTKey,
                ),
            )
        );

        $json = curl_exec($curl);

        $jp = (string) json_encode($json);
        $acc = explode(":", $jp);
        return substr($acc[1], 0, 4);
    }

    public function cashstore($date)
    {
        $this->loadModel('Branchrequest');
        $articles = TableRegistry::get('branchrequest');
        $mode = "Cash";
        $date_refine = date('Y-m-d', strtotime($date));
        //   echo $date_refine; //die;
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('branchrequest.payamount')])->where(['branchrequest.mode_payment' => $mode, 'DATE(branchrequest.approved_date)' => $date_refine])->first();
    }

    public function chequestore($date)
    {
        $this->loadModel('Studentfees');
        $articles = TableRegistry::get('branchrequest');
        $mode = "Cheque";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('branchrequest.payamount')])->where(['branchrequest.mode_payment' => $mode, 'DATE(branchrequest.approved_date)' => $date_refine])->first();
    }

    public function onlinestore($date)
    {
        $this->loadModel('Studentfees');
        $articles = TableRegistry::get('branchrequest');
        $mode = "Online";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('branchrequest.payamount')])->where(['branchrequest.mode_payment' => $mode, 'DATE(branchrequest.approved_date)' => $date_refine])->first();
    }

    public function getsection($id)
    {
        $articles = TableRegistry::get('Sections');
        return $articles->find('all')->where(['Sections.id IN' => $id])->toarray();
    }

    public function vendorgst($id)
    {
        // $articles = TableRegistry::get('Vendor');
        // return  $articles->find('all')->contain(['States'])->where(['Vendor.id' => $id])->first();

        $connection = ConnectionManager::get('default');
        $databaseName = $connection->config()['database'];
        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('vendors')->where(['id' => $id])->order(['id' => 'ASC']);
        return $query->execute()->fetch('assoc');
    }

    public function cash_store($id)
    {

        $articles = TableRegistry::get('branchrequest');
        return $articles->find('all')->where(['branchrequest.id' => $id])->order(['branchrequest.id' => 'ASC'])->toarray();
    }

    public function cashsolditems($date)
    {

        $articles = TableRegistry::get('solditems');
        $mode = "Cash";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('solditems.payamount')])->where(['solditems.mode_payment' => $mode, 'solditems.status' => 'Approved', 'DATE(solditems.pay_date)' => $date_refine])->first();
    }

    public function chequesolditems($date)
    {

        $articles = TableRegistry::get('solditems');
        $mode = "Cheque";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('solditems.payamount')])->where(['solditems.mode_payment' => $mode, 'solditems.status' => 'Approved', 'DATE(solditems.pay_date)' => $date_refine])->first();
    }
    public function discountsolditems($date)
    {

        $articles = TableRegistry::get('solditems');
        $mode = "Online";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('solditems.discount')])->where(['solditems.status' => 'Approved', 'DATE(solditems.pay_date)' => $date_refine])->first();
    }

    public function onlinesolditems($date)
    {

        $articles = TableRegistry::get('solditems');
        $mode = "Online";
        $date_refine = date('Y-m-d', strtotime($date));
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('solditems.payamount')])->where(['solditems.mode_payment' => $mode, 'solditems.status' => 'Approved', 'DATE(solditems.pay_date)' => $date_refine])->first();
    }

    public function findcompanyname($id)
    {

        $dbname = 'canvas_' . $id;
        $connss = $this->connection($dbname);
        $connss = ConnectionManager::get($dbname);
        $studentrfidsd = $connss->execute("SELECT * FROM `sitesettings_details`");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function branchsales($dbnamese = null)
    {
        if ($this->request->session()->read('Auth.User.db') == $dbnamese) {
        } else {

            $connss = $this->connection($dbnamese);
        }
        $connss = ConnectionManager::get($dbnamese);
        $studentrfidsd = $connss->execute("SELECT * FROM `sitesettings`");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function branchsales_detail($dbnamese = null)
    {

        $connss = ConnectionManager::get($dbnamese);
        $studentrfidsd = $connss->execute("SELECT * FROM `sitesettings_details`");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function billtaxdatasale($tax, $id)
    {
        //$this->connection('canvas');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount += $total_amount - $total_discount;
        }

        return $taxable_amount;
    }

    public function billgstsale($tax, $id)
    {
        //$this->connection('canvas');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "'and item_tax ='" . $tax . "'");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;

            $taxable_amountdata += $total_amount - $total_discount;
            $tax_amountdata += $taxable_amount * $value['item_tax'] / 100;
            $totalamount_data += $taxable_amount + $tax_amount;
        }

        return $tax_amountdata;
    }

    public function billtotalamountsale($tax, $id)
    {
        //$this->connection('canvas');
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);

        if ($tax == 0) {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "' and item_tax ='" . $tax . "' ");
        } else {
            $studentrfidsd = $connss->execute("SELECT * FROM `salesreturndetails` where `salereturn_id`='" . $id . "' and `item_tax`='" . $tax . "' ");
        }
        $data = $studentrfidsd->fetchAll('assoc');

        foreach ($data as $value) {
            $total_amount = $value['item_qty'] * $value['item_amount'];
            $total_discount = $value['item_qty'] * $value['discount'];
            $taxable_amount = $total_amount - $total_discount;
            $tax_amount = $taxable_amount * $value['item_tax'] / 100;
            $totalamount += $taxable_amount + $tax_amount;
        }

        return $totalamount;
    }

    // public function getfranchise()
    // {

    //     $dbname = $this->request->session()->read('Auth.User.db');
    //     $branch = explode("_", $dbname);
    //     $dbnames = $branch[0];
    //     $connss = ConnectionManager::get('default');
    //     if ($dbname == 'operify') {
    //         $studentrfidsd = $connss->execute("SELECT * FROM $dbname.`users` where `role_id`= 101");
    //         return $studentrfidsd->fetchAll('assoc');
    //     } else {
    //         $studentrfidsd = $connss->execute("SELECT * FROM $dbnames.`users` where `role_id`= 105");
    //         return $studentrfidsd->fetchAll('assoc');
    //     }
    // }
    public function getfranchise()
    {

        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);

        $dbnames = $branch[0];
        // $dbnames = '%' . $dbnames . '%'; 
        $connss = ConnectionManager::get('default');
        if ($dbname == 'operify') {
            $studentrfidsd = $connss->execute("SELECT * FROM $dbname.`users` where `role_id`= 101");
            return $studentrfidsd->fetchAll('assoc');
        } else {
            $dbnames =  $dbnames . '%';
            $query = "SELECT * FROM `operify`.`users` WHERE `db` LIKE :dbnames GROUP BY `db`";

            $studentrfidsd = $connss->execute($query, ['dbnames' => $dbnames]);
            return  $studentrfidsd->fetchAll('assoc');
        }
    }
    public function schooladdress($school_name)
    {
        $connss = ConnectionManager::get($school_name);
        $school_information = $connss->execute("SELECT * FROM `sitesettings_details`");
        return $school_information->fetchAll('assoc');
    }

    public function findtopitems($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function findbottomitems($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function findsocksitems($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        return $studentrfidsd->fetchAll('assoc');
    }

    public function finditeamnames($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        // pr($studentrfidsd); die;
        return $studentrfidsd->fetchAll('assoc');
    }

    public function findbillitemname($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        // pr($studentrfidsd); die;
        return $studentrfidsd->fetchAll('assoc');
    }

    public function findbilltax($id)
    {
        $dbname = $this->request->session()->read('Auth.User.db');
        $branch = explode("_", $dbname);
        if ($dbname != $branch[0]) {
        }

        $connss = ConnectionManager::get($branch[0]);
        $studentrfidsd = $connss->execute("SELECT stadd.*,taxmaster.id as tax_id,taxmaster.tax as tax_name  FROM `st_additem` stadd LEFT JOIN `st_taxmaster` taxmaster ON stadd.tax = taxmaster.id WHERE stadd.id ='" . $id . "'");
        // pr($studentrfidsd); die;
        return $studentrfidsd->fetchAll('assoc');
    }


    public function getpurchasereturncount($item_id)
    {

        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => 'SUM(Stockregister.quantity)'])->where(['Stockregister.store_type' => '5', 'Stockregister.item_id' => $item_id])->first();
        //  ->find('all')->where(['Stockregister.item_id'=$item_id,'Stockregister.store_type'=>5])->count();


    }
    //sanjay code 28-12-2022
    public function helpcount($slug)
    {
        $conns = ConnectionManager::get('default');
        $query1 = "SELECT count(*) as count FROM operify.help where help.slug='$slug' ";
        return $resultall = $conns->execute($query1)->fetchAll('assoc');
    }

    public function findschoolname($id)
    {
        // pr($id); die;//
        $connss = ConnectionManager::get('default');
        $query2 = "SELECT * FROM `schools` where id = $id";
        return $connss->execute($query2)->fetchAll('assoc');
    }

    public function getgroupentrydata($group_data)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->where(['Stockregister.group_entry' => $group_data])->toarray();
    }

    public function getgroupentryexcel($group_data)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->where(['Stockregister.group_entry' => $group_data])->toarray();
    }


    public function DelivernoteQty($ItemId, $PoId)
    {
        $articles = TableRegistry::get('Podeliverynote');
        return $articles->find('all')->where(['Podeliverynote.item_id' => $ItemId, 'Podeliverynote.po_id' => $PoId, 'Podeliverynote.status' => 'Y'])->order(['Podeliverynote.delivery_date' => 'ASC'])->first();
    }

    public function deliverydata($PoId, $delivery_date)
    {
        $articles = TableRegistry::get('Podeliverynote');
        return $articles->find('all')->contain(['Additem'])->where(['Podeliverynote.poprimary_id' => $PoId, 'Podeliverynote.delivery_date' => $delivery_date])->toarray();
    }

    function InhandStock($item_id)
    {
        $articles = TableRegistry::get('Stockregister');
        $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
        // pr($indentStock);die;
        return $currentStock = $grnStock['sum'] - $indentStock['sum'];
    }

    public function CheckdeliverynoteQty($PoId, $ItemId)
    {
        $articles = TableRegistry::get('Podeliverynote');
        return $articles->find('all')->select(['item_qty'])->contain(['Additem'])->where(['Podeliverynote.po_id' => $PoId, 'Podeliverynote.item_id' => $ItemId])->first();
    }

    public function findcopperstock($id)
    {
        $articles = TableRegistry::get('Copperstock');
        $curdate = date("Y-m-d");
        return $articles->find('all')->where(['Copperstock.product_id' => $id, 'DATE(Copperstock.created_at)' => $curdate])->first();
    }
    public function findopeningstock($id)
    {
        $articles = TableRegistry::get('Copperstock');
        return $articles->find('all')->where(['Copperstock.product_id' => $id])->order(['Copperstock.id' => 'Desc'])->toarray();
    }
    public function findcontractname($id)
    {
        $articles = TableRegistry::get('Contracts');
        return $articles->find('all')->where(['Contracts.id' => $id])->first();
    }

    public function getprocessname($id)
    {
        $articles = TableRegistry::get('Process');
        return $articles->find('all')->where(['Process.id' => $id])->first();
    }
    public function getMachineName($id)
    {
        $articles = TableRegistry::get('Machinemaster');
        return $articles->find('all')->where(['Machinemaster.id' => $id])->first();
    }

    public function findfinishedqty($contraid, $itemid)
    {
        // $articles = TableRegistry::get('Productionorder');
        // return $articles->find('all')->where(['Productionorder.contract_id' => $contraid, 'Productionorder.item_id' => $itemid])->toarray();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('productionorder')->where(['contract_id' => $contraid, 'item_id' => $itemid]);
        return $query->execute()->fetchAll('assoc');
    }
    public function checkdailysheet($po_id, $process_id)
    {

        // $articles = TableRegistry::get('Production');
        // return  $articles->find('all')->where(['Production.po_id' => $po_id, 'Production.is_completed' => 'Y'])->order(['Production.id' => 'asc'])->toarray();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('production')->where(['po_id' => $po_id, 'productprocess_id' => $process_id])->order(['id' => 'ASC']);
        return $query->execute()->fetchAll('assoc');
    }
    public function getdailysheet($contract_id, $item_id, $process_id)
    {
        $articles = TableRegistry::get('Production');
        return $articles->find('all')->where(['Production.contract_id' => $contract_id, 'Production.item_id' => $item_id, 'Production.productprocess_id' => $process_id])->order(['Production.id' => 'asc'])->toarray();
    }
    //for check production base on contract and finishedproduct 13-02-24
    public function checkproduction($contract_id, $item_id)
    {
        $articles = TableRegistry::get('Production');
        return $articles->find('all')->where(['Production.item_id' => $item_id, 'Production.contract_id' => $contract_id])->order(['Production.id' => 'asc'])->toarray();
    }
    //for check productionorder has started yet for delete right 
    public function checkdproductionstart($po_id)
    {
        $articles = TableRegistry::get('Production');
        return $articles->find('all')->where(['Production.po_id' => $po_id])->order(['Production.id' => 'asc'])->toarray();
    }
    public function findbomdetails($contractid)
    {
        $articles = TableRegistry::get('Bom');
        return $articles->find('all')->where(['Bom.contract_id' => $contractid])->first();
    }
    public function findproductionorder($poid)
    {
        $articles = TableRegistry::get('Productionorder');
        return $articles->find('all')->where(['Productionorder.po_id' => $poid])->first();
    }
    public function checkproductionorder($contractid)
    {
        $articles = TableRegistry::get('Productionorder');
        return $articles->find('all')->where(['Productionorder.contract_id' => $contractid])->toarray();
    }
    public function checkdesignsheet($contractid)
    {
        $articles = TableRegistry::get('Designsheet');
        return $articles->find('all')->where(['Designsheet.contract_id' => $contractid])->first();
    }
    public function checkindentpo($contractid, $finisheditemid)
    {
        $articles = TableRegistry::get('Indentpo');
        return $articles->find('all')->where(['Indentpo.contract_id' => $contractid, 'Indentpo.finishedproduct_id' => $finisheditemid])->toarray();
    }

    public function getdesignsheetno($contractid, $finisheditemid)
    {
        // $articles = TableRegistry::get('Designsheet');
        // return $articles->find('all')->where(['Designsheet.contract_id' => $contractid, 'Designsheet.item_id' => $finisheditemid])->first();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('designsheet')->where(['contract_id' => $contractid, 'item_id' => $finisheditemid]);
        return $query->execute()->fetch('assoc');
    }

    public function getdesignsheet($designsheetnoid, $itemid)
    {
        $articles = TableRegistry::get('Designsheetdetails');
        return $articles->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetnoid, 'Designsheetdetails.item_id' => $itemid])->first();
    }

    public function getdesignmaterialqty($designsheetno, $itemid)
    {
        // $articles = TableRegistry::get('Designsheetdetails');

        // return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Designsheetdetails.item_qty')])->where(['Designsheetdetails.contract_id' => $contractid, 'Designsheetdetails.item_id' => $itemid])->order(['Designsheetdetails.id' => 'DESC'])->first();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*', 'sum' => $query->func()->sum('item_qty')])->from('designsheetdetails')->where(['designsheetno' => $designsheetno, 'item_id' => $itemid])
            ->order(['id' => 'DESC']);
        return $query->execute()->fetch('assoc');
    }

    public function getdesignmaterials($designsheetno)
    {
        // $articles = TableRegistry::get('Designsheetdetails');
        // return $articles->find('all')->where(['Designsheetdetails.designsheetno' => $designsheetno])->order(['Designsheetdetails.id' => 'Asc'])->toarray();

        $conn = ConnectionManager::get('default');
        $query = $conn->newQuery();
        $query->select(['*'])->from('designsheetdetails')->where(['designsheetno' => $designsheetno])->order(['id' => 'Asc']);
        return $query->execute()->fetchAll('assoc');
    }
    public function find_po_item__qty($item_id, $po_id)
    {
        $articles = TableRegistry::get('PurchaseorderDetails');
        return $articles->find('all')->select(['item_qty' => 'ROUND(SUM(PurchaseorderDetails.item_qty), 2)'])->where(['PurchaseorderDetails.item_id' => $item_id, 'PurchaseorderDetails.purchaseorder_id' => $po_id])->first();
    }

    // public function find_grn_details($item_id,$purchase_id)
    // {
    //     $articles = TableRegistry::get('Stockregister');
    //     return $articles->find('all')->contain(['Goodsreceived','Additem'])->where(['Stockregister.item_id' => $item_id,'Stockregister.po_id' => $purchase_id,'Stockregister.store_type' => 1])->toarray();
    // }
    // public function find_po_item_name_and_qty($purchase_id)
    // {
    //     $articles = TableRegistry::get('Stockregister');
    //     return $articles->find('all')->contain(['Goodsreceived','Additem'])->where(['Stockregister.po_id' => $purchase_id,'Stockregister.store_type' => 1])->group(['Stockregister.purchaseorder_id'])->toarray();
    // }
    public function find_po_item_name_and_qty($purchase_id, $itemid)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Goodsreceived', 'Additem'])->where(['Stockregister.po_id' => $purchase_id, 'Stockregister.store_type' => 1, 'Stockregister.item_id' => $itemid])->toarray();
    }

    public function poitem_qty($purchase_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['po_item_qty_sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->contain(['Goodsreceived', 'Additem'])->where(['Stockregister.po_id' => $purchase_id, 'Stockregister.store_type' => 1])->first();
    }


    public function getpurchaseorderdetails($id, $poprimary_id)
    {
        $articles = TableRegistry::get('PurchaseorderDetails');
        return $articles->find('all')->where(['PurchaseorderDetails.purchaseorder_id' => $id, 'PurchaseorderDetails.poprimary_id' => $poprimary_id])->order(['PurchaseorderDetails.poprimary_id' => 'DESC'])->toarray();
    }



    // to get issued quantity in contract view base on category
    public function rawitempendingqty($rawitem_id, $finisheditem_id, $contractid, $isgroup)
    {
        $articles = TableRegistry::get('Stockregister');
        if ($isgroup == 1) {
            // $additem = TableRegistry::get('Additem');
            // $itemscategory =  $additem->find('all')->where(['Additem.id' => $rawitem_id])->first();
            // $items =  $additem->find('all')->where(['Additem.category_id' =>  $itemscategory['category_id']])->toarray();
            // foreach($items as $item){
            //     $itemId[] = $item['id'];
            // }


            $conn = ConnectionManager::get('default');
            $query1 = $conn->newQuery();
            $query1->select(['*'])->from('st_additem')->where(['id' => $rawitem_id]);
            $itemscategory = $query1->execute()->fetch('assoc');

            $conn = ConnectionManager::get('default');
            $query2 = $conn->newQuery();
            $query2->select(['*'])->from('st_additem')->where(['category_id' => $itemscategory['category_id']]);
            $items = $query2->execute()->fetchAll('assoc');

            foreach ($items as $item) {
                $itemId[] = $item['id'];
            }

            return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id IN' => $itemId, 'Stockregister.finishedproduct_id' => $finisheditem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.store_type' => 2])->order(['Stockregister.id' => 'DESC'])->first();
        } else {
            return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $rawitem_id, 'Stockregister.finishedproduct_id' => $finisheditem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.store_type' => 2])->order(['Stockregister.id' => 'DESC'])->first();
        }
    }

    public function rawitemreverseqty($rawitem_id, $finisheditem_id, $contractid, $isgroup)
    {
        $articles = TableRegistry::get('Stockregister');
        if ($isgroup == 1) {
            // $additem = TableRegistry::get('Additem');
            // $itemscategory =  $additem->find('all')->where(['Additem.id' => $rawitem_id])->first();
            // $items =  $additem->find('all')->where(['Additem.category_id' =>  $itemscategory['category_id']])->toarray();
            // foreach($items as $item){
            //     $itemId[] = $item['id'];
            // }

            $conn = ConnectionManager::get('default');
            $query1 = $conn->newQuery();
            $query1->select(['*'])->from('st_additem')->where(['id' => $rawitem_id]);
            $itemscategory = $query1->execute()->fetch('assoc');

            $conn = ConnectionManager::get('default');
            $query2 = $conn->newQuery();
            $query2->select(['*'])->from('st_additem')->where(['category_id' => $itemscategory['category_id']]);
            $items = $query2->execute()->fetchAll('assoc');

            foreach ($items as $item) {
                $itemId[] = $item['id'];
            }

            return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id IN' => $itemId, 'Stockregister.finishedproduct_id' => $finisheditem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.store_type' => 3])->order(['Stockregister.id' => 'DESC'])->first();
        } else {
            return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $rawitem_id, 'Stockregister.finishedproduct_id' => $finisheditem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.store_type' => 3])->order(['Stockregister.id' => 'DESC'])->first();
        }
    }

    public function rawreverseitemqty($contractid, $rawitem_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->where(['Stockregister.item_id' => $rawitem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.store_type' => 3])->order(['Stockregister.id' => 'DESC'])->toArray();
    }
    public function designitempendingqty($rawitem_id, $contractid, $finisheditem_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $rawitem_id, 'Stockregister.contract_id' => $contractid, 'Stockregister.finishedproduct_id' => $finisheditem_id, 'Stockregister.store_type' => 2])->order(['Stockregister.id' => 'DESC'])->first();
    }

    public function getindentdetails($indentid)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->where(['Stockregister.indent_id' => $indentid])->order(['Stockregister.id' => 'DESC'])->toarray();
    }
    // to find reverse indent id
    public function findreverseindent($contract_id)
    {
        $articles = TableRegistry::get('Reverseindent');
        return $articles->find('all')->where(['Reverseindent.contract_id' => $contract_id])->toarray();
    }
    public function findreverseindentid($contract_id, $finished_id)
    {
        $articles = TableRegistry::get('Reverseindent');
        return $articles->find('all')->where(['Reverseindent.contract_id' => $contract_id, 'Reverseindent.finishedproduct_id' => $finished_id])->toarray();
    }

    // to grt reverse indent item details
    public function reverseindentdetails($rawitem_id, $reverse_id)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->where(['Stockregister.reverse_id' => $reverse_id, 'Stockregister.item_id' => $rawitem_id])->first();
    }


    public function finishedproductprocess($id)
    {
        $articles = TableRegistry::get('Finishedprocess');
        return $articles->find('all')->where(['Finishedprocess.id' => $id])->first();
    }

    // Sanjay Code 
    public function finduserpermisson($user_id = null, $controllerName = null, $actionName = null)
    {
        $articles = TableRegistry::get('PermissionModules');
        return $articles->find('all')->where(['PermissionModules.user_id' => $user_id, 'PermissionModules.controller' => $controllerName, 'PermissionModules.action' => $actionName])->order(['PermissionModules.id' => 'asc'])->first();
    }
    // // rajesh code
    // public function finduserpermisson($user_id = null, $controllerName = null, $actionName = null)
    // {
    //     $articles = TableRegistry::get('PermissionModules');
    //     // return $articles->find('all')->where(['PermissionModules.user_id' => $user_id, 'PermissionModules.controller' => $controllerName, 'PermissionModules.action' => $actionName])->order(['PermissionModules.id' => 'asc'])->first();
    //     return $articles->find('all')->where([
    //         'PermissionModules.user_id' => $user_id,
    //         'PermissionModules.controller' => $controllerName,
    //         'OR' => [
    //             ['PermissionModules.action' => $actionName],
    //             ['PermissionModules.search' => $actionName]
    //         ]
    //     ])->order(['PermissionModules.id' => 'asc'])->first();
    // }

    function reciveitem($item_id, $date)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Taxmaster'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.issue_date <=' => $date, 'Stockregister.store_type IN' => ['1']])->order(['Stockregister.id' => 'desc'])->first();
    }

    //to generate excel
    function todayopeningstock($item_id, $date)
    {

        // cmt by sanjay
        $articles = TableRegistry::get('Stockregister');

        $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created <=' => $date, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();

        $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.created <=' => $date, 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['2', '4']])->first();
        return $currentStock = $grnStock['sum'] - $indentStock['sum'];

        // $articles = TableRegistry::get('Stockregister');
        // $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created <=' => $date, 'Stockregister.store_type IN' => ['0', '1', '3']])->first();
        // $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created <=' => $date, 'Stockregister.store_type IN' => ['2', '4']])->first();
        // // pr($indentStock);die;
        // return  $grnStock['sum'] - $indentStock['sum'];
    }

    function todayrecivedstock($item_id, $date)
    {
        // cmt by sanjay

        // $articles = TableRegistry::get('Stockregister');

        // $grnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created < ' => $date, 'Stockregister.store_type IN' => ['1']])->first();
        // return $grnStock = $grnStock['sum'] ? $grnStock['sum'] : '0';
        $articles = TableRegistry::get('Stockregister');
        $grnStock = $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['DATE(Stockregister.created) <= ' => $date, 'Stockregister.status !=' => 'N', 'Stockregister.item_id' => $item_id, 'Stockregister.store_type IN' => ['0', '1', '3']])->order(['Stockregister.id' => 'DESC'])->first();
        return $grnStock = $grnStock['sum'] ? $grnStock['sum'] : '0';
    }


    function todayissuedtock($item_id, $date)
    {


        // $articles = TableRegistry::get('Stockregister');

        // $indentStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created < ' => $date, 'Stockregister.store_type IN' => ['2']])->first();
        // return $indentStock = $indentStock['sum'] ? $indentStock['sum'] : '0';

        $articles = TableRegistry::get('Stockregister');
        $indentStock = $articles->find('all')->select(['sum' => $articles->find('all')->func()->sum('Stockregister.quantity')])->where(['Stockregister.item_id' => $item_id, 'DATE(Stockregister.created) <= ' => $date, 'Stockregister.status !=' => 'N', 'Stockregister.store_type IN' => ['2', '4']])->order(['Stockregister.id' => 'DESC'])->first();
        return $indentStock = $indentStock['sum'] ? $indentStock['sum'] : '0';
    }
    function todayreversestock($item_id, $date)
    {
        $articles = TableRegistry::get('Stockregister');

        $reverseStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created < ' => $date, 'Stockregister.store_type IN' => ['3']])->first();
        return $reverseStock = $reverseStock['sum'] ? $reverseStock['sum'] : '0';
    }

    function todayreturnstock($item_id, $date)
    {
        $articles = TableRegistry::get('Stockregister');

        $returnStock = $articles->find('all')->select(['sum' => 'ROUND(SUM(Stockregister.quantity), 2)'])->where(['Stockregister.item_id' => $item_id, 'Stockregister.created < ' => $date, 'Stockregister.store_type IN' => ['4'], 'Stockregister.status !=' => 'N'])->first();
        return $returnStock = $returnStock['sum'] ? $returnStock['sum'] : '0';
    }

    function getvendorbalance($vendor_id, $date)
    {
        $articles = TableRegistry::get('Payments');

        $creditamt = $articles->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.bill_date <' => $date, 'Payments.store_type IN' => ['1']])->first();
        $debitamt = $articles->find('all')->select(['sum' => 'ROUND(SUM(Payments.total_amt), 2)'])->where(['Payments.vendor_id' => $vendor_id, 'Payments.bill_date <' => $date, 'Payments.status' => 'Y', 'Payments.store_type IN' => ['2']])->first();

        $amount = $creditamt['sum'] - $debitamt['sum'];
        return $balance = $amount ? $amount : '0';
    }


    // get last five purchase item price
    public function lastitemcost($item_id, $date)
    {
        $articles = TableRegistry::get('PurchaseorderDetails');
        $rsull = $articles->find('all')->where(['PurchaseorderDetails.item_id' => $item_id, 'DATE(PurchaseorderDetails.inward_date) <=' => $date])->order(['PurchaseorderDetails.inward_date' => 'DESC', 'PurchaseorderDetails.id' => 'DESC'])->toarray();
        $podata = [];
        $poid = [];
        foreach ($rsull as $value) {
            if (!in_array($value['purchaseorder_id'], $poid)) {
                $poid[] = $value['purchaseorder_id'];
                $podata[] = $value;
            }
        }
        $podata = array_slice($podata, 0, 5);
        return $podata;
    }

    // get all purchaseorder
    public function getAllPurchaseOrder($poid)
    {
        $articles = TableRegistry::get('Purchaseorder');
        return $articles->find('all')->where(['Purchaseorder.purchaseorder_id' => $poid])->order(['Purchaseorder.id' => 'DESC'])->toarray();
    }

    // get Delivery dates
    public function getDeliverydates($poid)
    {
        $articles = TableRegistry::get('Podeliverynote');
        $results = $articles->find('all')->where(['poprimary_id' => $poid])->group('delivery_date')->toarray();
        usort($results, function ($a, $b) {
            return strtotime($a['delivery_date']) - strtotime($b['delivery_date']);
        });
        // if(empty($results)){
        //     $connss = ConnectionManager::get('default');
        //     $studentrfidsd = $connss->execute("SELECT * FROM `st_purchaseorder` where `id`=  $poid  order by id desc");
        //     $results = $studentrfidsd->fetchAll('assoc');
        // }
        return  $results;
    }


    // get item qty of delivery note base on date
    public function DeliveritemQty($ItemId, $PoId, $delivery_date)
    {
        $articles = TableRegistry::get('Podeliverynote');
        return $articles->find('all')->where(['item_id' => $ItemId, 'poprimary_id' => $PoId, 'DATE(delivery_date)' => $delivery_date])->first();
    }
    public function findrolename($id)
    {
        $articles = TableRegistry::get('roles');
        return $articles->find('all')->where(['roles.id' => $id])->first();
    }


    public function quotationSendCount($quotationId)
    {
        $articles = TableRegistry::get('QuotationSend');
        return $articles->find('all')->where(['QuotationSend.quotation_id' => $quotationId])->group('vendor_id')->count();
    }

    public function quotationReceivedCount($quotationId)
    {
        $articles = TableRegistry::get('QuotationReceived');
        return $articles->find('all')->where(['quotation_id' => $quotationId])->count();
    }

    public function getBidDetails($vendorId, $quotationId)
    {
        $articles = TableRegistry::get('QuotationReceived');
        return $articles->find('all')->where(['quotation_id' => $quotationId, 'vendor_id' => $vendorId])->first();
    }


    public function getQuotation($quotationId)
    {
        $articles = TableRegistry::get('Quotation');
        return $articles->find('all')->where(['quotation_id' => $quotationId])->first();
    }

    public function getproductionDeatil($date, $machineId)
    {
        $articles = TableRegistry::get('Production');
        return $articles->find('all')->where(['production_date' => $date, 'machine_id' => $machineId])->first();
    }


    public function getmachinereading($machineId)
    {
        $articles = TableRegistry::get('Production');
        return $articles->find('all')->where(['Production.machine_id ' => $machineId, 'Production.nextday8am IS NOT NULL'])->order(['Production.id ' => 'Desc'])->first();
    }

    public function checkRawmaterial($contractid, $itemId)
    {
        $articles = TableRegistry::get('Designsheetdetails');
        return $articles->find('all')->where(['contract_id' => $contractid, 'item_id' => $itemId])->first();
    }


    public function getContractFinished($contractid, $startDate, $endDate)
    {
        $articles = TableRegistry::get('Productionorder');
        // return $articles->find('all')->where(['contract_id' => $contractid, 'startdate >=' => $startDate, 'startdate <=' => $endDate])->toarray();

        return $articles->find('all')
            ->where([
                'contract_id' => $contractid,
                'startdate >=' => $startDate,
                'startdate <=' => $endDate
            ])
            ->toArray();
    }


    public function getemdremark($id)
    {
        $articles = TableRegistry::get('emd_remarks');
        return $articles->find('all')->where(['bank_guarantee_id' => $id])->order(['emd_remarks.id ' => 'Desc'])->first();
    }


    public function getReceivedTotalAmount($id)
    {
        $articles = TableRegistry::get('particular_pay_receive');

        $query = $articles->find();
        $total = $query
            ->select(['total' => $query->func()->sum('recive_amount')])
            ->where(['particular_id' => $id])
            ->first();
        return $total->total ?? 0;
    }


    // sanjay code
    public function getgrBaseddata($poid)
    {
        $articles = TableRegistry::get('Stockregister');
        return $articles->find('all')->contain(['Additem'])->where(['Stockregister.po_id' => $poid, 'Stockregister.store_type' => '1'])->toarray();
    }

    public function getdeliveryscheduledata($poid, $delivery_schedule_id)
    {
        $articles = TableRegistry::get('Podeliverynote');
        return $articles->find('all')->contain(['Additem'])->where(['Podeliverynote.po_id' => $poid, 'Podeliverynote.id' => $delivery_schedule_id])->first();
    }

    public function subContractorsDetails($id)
    {
        $articles = TableRegistry::get('SubContractors');
        return $articles->find('all')->where(['SubContractors.id' => $id])->first();
    }
}
