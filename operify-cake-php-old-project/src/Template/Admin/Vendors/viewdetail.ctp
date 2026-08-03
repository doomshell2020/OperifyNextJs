<style>
  .tableContainer p {
    margin-bottom: 5px;
  }

  .tableContainer table thead {
    background: #fff;
    color: #333;
  }

  .tableContainer .tableHeader {
    padding: 10px;
  }
</style>

<div class="tableContainer " style=" border:1px solid #ccc !important;">

<?php
$role_permissions = $this->Permission->permissioncheck();
$fileurl = "admin/vendors/add";
if (in_array($fileurl, $role_permissions)) { ?>
  <a target="_blank"
    href="<?php echo ADMIN_URL; ?>vendors/add/<?php echo $vendor['id']; ?>"
    class="btn btn-success pull-right m-top10" style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="fas fa-edit"></i>&nbsp;Edit</a>
<?php }?>
  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Vendor Details</b></p>
    <table>
      <tr>
        <td><b>Vendor Name :-</b>
          <?php echo $vendor['name']; ?>
        </td>
        <td><b>Contact No. :-</b>
          <?php echo $vendor['contact_no']; ?>
        </td>
      </tr>
      <tr>
        <td><b>Contact Person Name :-</b>
          <?php echo $vendor['contact_person']; ?>
        </td>
        <td><b>Email Id :-</b>
          <?php echo $vendor['email']; ?>
        </td>
      </tr>
      <tr>
        <td><b>PAN NO. :-</b>
          <?php echo $vendor['pancard_number']; ?>
        </td>
        <td><b>State :-</b>
          <?php echo $vendor['state']['name']; ?>
        </td>
      </tr>
      <tr>
        <td><b>GST No. :-</b>
          <?php echo $vendor['gst_number']; ?>
        </td>
        <td><b>Address :-</b>
          <?php echo $vendor['address']; ?>
        </td>
      </tr>
    </table>
  </div>





