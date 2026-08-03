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
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Client;
use Cake\Utility\Security;
use Cake\Validation\Validator;

class PurchasereturnController extends AppController
{
  public function index()
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Purchasereturn');
    $this->loadModel('Vendor');

    $req_data = $_GET;
    $vendor_id = $req_data['vendor_id'];
   
    $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
    $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));
  
    $apk = [];

    if (!empty ($vendor_id)) {
      $apk['Purchasereturn.vendor_id'] = $vendor_id;
    }
    if ($datefrom != '1970-01-01') {
      $apk['DATE(Purchasereturn.retrundate) >='] = $datefrom;
    }
    if ($dateto2 != '1970-01-01') {
      $apk['DATE(Purchasereturn.retrundate) <='] = $dateto2;
    }
    if($req_data){
      $purchasereturn = $this->Purchasereturn->find('all')->where([$apk])->order(['Purchasereturn.id' => 'DESC']);
    }else{
      $purchasereturn = $this->Purchasereturn->find('all')->order(['Purchasereturn.id' => 'desc']);
    }
    $purchasereturn = $this->paginate($purchasereturn)->toarray();

    $this->set(compact('purchasereturn'));
    
   
  }


  public function add()
  {
    $this->viewBuilder()->layout('admin');
    $this->loadModel('Purchasereturn');
    $this->loadModel('Stockregister');
    $this->loadModel('Taxmaster');

    if ($this->request->is(['post'])) {
      foreach ($this->request->data['return_qty'] as $key => $value) {
        if ($value > 0) {
          $chechQty[] = $value;
        }
      }
      if (!empty ($chechQty) && isset ($chechQty)) {
        $item_details['retrundate'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
        $item_details['vendor_id'] = $this->request->data['vendor_id'];
        $item_details['bill_no'] = $this->request->data['bill_no'];
        $item_details['bill_date'] = date('Y-m-d', strtotime($this->request->data['bill_date']));
        $item_details['grn_no'] = $this->request->data['goods_id'];
        $item_details['purchaseorder_id'] = $this->request->data['purchaseorder_id'];
        $item_details['amount'] = $this->request->data['totalBillAmount'];
        $item_details['description'] = $this->request->data['description'];
        $newpurchasedetail = $this->Purchasereturn->patchEntity($this->Purchasereturn->newEntity(), $item_details);

        if ($purchasedetail = $this->Purchasereturn->save($newpurchasedetail)) {
          $id = $purchasedetail->id;
          foreach ($this->request->data['return_qty'] as $key => $value) {
            if ($value > 0) {
              $taxpercant = $this->Taxmaster->find('all')->where(['Taxmaster.tax' => $this->request->data['taxrate'][$key]])->first();
              $newsrentity['return_id'] = $id;
              $newsrentity['po_id'] = $this->request->data['purchaseorder_id'];
              $newsrentity['goods_id'] = $this->request->data['goods_id'];
              $newsrentity['vendor_id'] = $this->request->data['vendor_id'];
              $newsrentity['item_id'] = $this->request->data['item_id'][$key];
              $newsrentity['quantity'] = $this->request->data['return_qty'][$key];
              $newsrentity['rate'] = $this->request->data['rate'][$key];
              $newsrentity['issue_date'] = date('Y-m-d', strtotime($this->request->data['inwarddate']));
              $newsrentity['cost_price'] = $this->request->data['cost_price'][$key];
              $newsrentity['amount'] = $this->request->data['total'][$key];
              $newsrentity['tax_id'] = $taxpercant['id'];
              $newsrentity['store_type'] = '4';
              $newsrentity['tax'] = $this->request->data['taxamount'][$key];
              $newrecord = $this->Stockregister->patchEntity($this->Stockregister->newEntity(), $newsrentity);
              $purchase_detail = $this->Stockregister->save($newrecord);
            }
          } 
          $this->Flash->success(__('Purchasereturn has been saved.'));
          return $this->redirect(['action' => 'index']);
        }
      } else {
        $this->Flash->error(__('Atleast add one product for return.'));
        return $this->redirect(['action' => 'add']);
      }
    }
  }

public function delete($id)
{
  $this->viewBuilder()->layout('admin');
  $this->loadModel('Purchasereturn');
  $this->loadModel('Stockregister');
    $delete = $this->Purchasereturn->get($id);
    if ($delete) {
        $this->Purchasereturn->deleteAll(['Purchasereturn.id' => $id]);
        $this->Flash->success(('Purchasereturn has been deleted successfully.'));
        return $this->redirect(['action' => 'index']);
    }
}
// public function status($id){
//   $this->loadModel('Purchasereturn');
//       $user = $this->Purchasereturn->get($id); 
//       $user->status = $status; 
//       if ($this->Purchasereturn->save($user)) {
//        $this->Flash->success('Purchasereturn status has been updated.');
//        return $this->redirect(['action' => 'index']);
//       } 
//       //  pr($status);die;
// }

public function status($id ,$null)
{
  $this->loadModel('Purchasereturn');
      $user = $this->Purchasereturn->get($id); 
      $user->status = $status; 
      if ($this->Purchasereturn->save($user)) {
       $this->Flash->success('Purchasereturn status has been updated.');
       return $this->redirect(['action' => 'index']);
      } 
}

// public function viewdetails($id= null)
// {
//     $this->loadModel('Purchasereturn');
//     $this->loadModel('Stockregister');
//     $purchasereturn = $this->Purchasereturn->find('all')->where(['Purchasereturn.id' => $id])->first();
//     $stockregister = $this->Stockregister->find('all')->where(['Stockregister.id' => $id])->toarray();
//     $this->set(compact('purchasereturn', 'stockregister'));
//   }

  public function viewdetails($id = null)
  {
      $this->loadModel('Purchasereturn');
      $this->loadModel('Stockregister');
      
      // Find the purchase return record by its ID
      $purchasereturn = $this->Purchasereturn->get($id, [
          'contain' => [], // Adjust contain conditions as needed
      ]);
  
      // Find the stock register records related to the purchase return
      $stockregister = $this->Stockregister->find('all')
          ->where(['Stockregister.purchasereturn_id' => $id])
          ->toArray(); // Use toArray() instead of toarray()
  
      // Pass the data to the view
      $this->set(compact('purchasereturn', 'stockregister'));
  }
  

  // public function viewdetails($id = null)
  // {
  //     $this->loadModel('Purchasereturn');
  //     $this->loadModel('Stockregister');
  //     $this->loadModel('Measurementunit');
  //     $this->loadModel('Vendors');
  //     $this->loadModel('Goodsreceived');
   
 
  //     $this->viewBuilder()->layout('ajax');
  //     $Purchasereturn = $this->Purchasereturns->find('all')->first();
  //     $stockregister_view= $this->Stockregister->find('all')->where(['status' => 'Y'])->first();
  //     $this->set(compact('Purchasereturn', 'stockregister_view'));
  //     $users = $this->Purchasereturn->find('all')->where(['Purchasereturn.id' => $id])->order(['Purchasereturn.id' => 'DESC'])->first();
  //     $sup = $this->Vendors->find('all')->select(['retrundate ', ' vendor_id', 'bill_no '])->where(['Vendors.id' => $users['vendor_id']])->first();
 
  //     $this->set(compact('users', 'sup', 'co'));
  //     pr($sitesetting);die;
  // }

//   public function viewdetails($id = null)
// {
//     $this->loadModel('Purchasereturn');
//     $this->loadModel('Stockregister');
//     $this->loadModel('Vendors');

//     // Set layout to 'ajax' if you want to render only the content of the popup
//     $this->viewBuilder()->layout('ajax');

//     // Query data for the popup content
//     $Purchasereturn = $this->Purchasereturn->get($id, [
//         'contain' => [], // Adjust contain conditions as needed
//     ]);

//     // Query additional data if required
//     $stockregister_view = $this->Stockregister->find('all')
//         ->where(['status' => 'Y'])
//         ->first();

//     $user = $this->Purchasereturn->find('all')
//         ->where(['Purchasereturn.id' => $id])
//         ->order(['Purchasereturn.id' => 'DESC'])
//         ->contain(['Vendors'])
//         ->first();

//     // Pass data to the view
//     $this->set(compact('Purchasereturn', 'stockregister_view', 'user'));

//     // Optionally, you can directly pass the queried data to the view without setting individual variables
//     // $this->set('data', compact('Purchasereturn', 'stockregister_view', 'user'));
// }


  public function view($id = null)
  {
    $this->loadModel('Purchasereturn');
    $this->loadModel('Stockregister');
    $this->loadModel('Measurementunit');
    $this->loadModel('Vendor');
    $this->loadModel('Additem');
    $this->loadModel('Sitesettings');
    $this->loadModel('SitesettingsDetails');
    $this->loadModel('States');
    $this->loadModel('Cities');
    $this->viewBuilder()->layout('ajax');

    $sitesetting = $this->Sitesettings->find('all')->first();

    $site_details = $this->SitesettingsDetails->find('all')->where(['status' => 'Y'])->first();
   
    $this->set(compact(['sitesetting', 'site_details']));
    
    $users = $this->Purchasereturn->find('all')->where(['Purchasereturn.id' => $id])->first();
   
    $sup = $this->Vendor->find('all')->contain(['States'])->where(['Vendor.id' => $users['vendor_id']])->first();
 
    $puritems = $this->Stockregister->find('all')->contain(['Additem' => ['Measurementunit']])->where(['Stockregister.return_id' => $id])->toarray();
   
    $this->set(compact('users', 'sup', 'puritems', 'co')); 

     }

  public function searchitem()
  {
    $this->loadModel('Purchasereturn');
    $req_data = $_GET;
    $vendor_id = $req_data['vendor_id'];
    $datefrom = date('Y-m-d', strtotime($req_data['datefrom']));
    $dateto2 = date('Y-m-d', strtotime($req_data['dateto']));
    // $purchaseorder_id = $req_data['purchaseorder_id'];
    // pr($this->request->data);die;
    $apk = [];
    // if (!empty ($purchaseorder_id)) {
    //     $apk['Purchasereturn.purchaseorder_id'] = $purchaseorder_id;
    // }
    if (!empty ($vendor_id)) {
      $apk['Purchasereturn.vendor_id'] = $vendor_id;
    }
    if ($datefrom != '1970-01-01') {
      $apk['DATE(Purchasereturn.retrundate) >='] = $datefrom;
    }
    if ($dateto2 != '1970-01-01') {
      $apk['DATE(Purchasereturn.retrundate) <='] = $dateto2; 
    }
    $this->request->session()->write('apk', $apk);
    $purchasereturn = $this->Purchasereturn->find('all')->where([$apk])->order(['Purchasereturn.id' => 'DESC']);
    $purchasereturn = $this->paginate($purchasereturn)->toarray();
    $this->set(compact('purchasereturn'));
  }

}
