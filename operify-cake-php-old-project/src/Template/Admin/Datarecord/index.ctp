<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<style>


	  /* IE 6 doesn't support max-height
	   * we use height instead, but this forces the menu to always be this tall
	   */
	   * html .ui-autocomplete {
	   	height: 100px;
	   }

	</style>
 <div class="content-wrapper">

  <section class="content-header">
    <h1>
      <i class="fa fa-th-list"></i>
      Data Record Manager
    </h1>
     <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>Datarecord/"><i class="fa fa-home"></i>Home</a></li>

	      </ol>
  </section>
  



  <!-- Main content -->
  <section class="content">

    <div class="row">
      <div class="col-xs-12">

       <div class="box">

        <div>
         <?php echo $this->Flash->render(); ?>
       </div>

       <div class="box-header">
        <h3 class="box-title"><i class="fa fa-search"></i> Data Record Manager</h3>
<!--
        <a id="" style="position: absolute;
top: 122px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>report/user_prospectus"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a>
-->
      </div>
      <!-- /.box-header -->

      <div class="box-body">
      <div id="srch-rslt">
        <table id="example1" class="table table-bordered table-striped">
          <thead>

            <tr>
              <th>S.No.</th>
              <th>Name</th>
              <th>Import</th>
              <th>Export</th>
              <th>Download Sample Excel</th>

            </tr>
          </thead>
          <tbody>
       
<tr>
    <td>1</td>
    <td>Students</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importstudents"></i>Import Students </a></td>

   
    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportstudents"></i>Export Students </a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/Student_Excel_Format.xls"></i>Download Sample Excel </a></td>

</tr>
<tr>
    <td>2</td>
    <td>Employees/Teacher</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importemployee"></i>Import Employees/Teacher</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportemployee"></i>Export Employees/Teacher</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/Employee_Excel_Format.xls"></i>Download Sample Excel </a></td>
</tr>

<tr>
    <td>3</td>
    <td>Library</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importlibrarydata"></i>Import Library</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportlibrarydata"></i>Export Library</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/Library_Excel.xls"></i>Download Sample Excel </a></td>
</tr>
<tr>
    <td>4</td>
    <td>Class/Co-Class Teacher</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importclass_coclass_teacher"></i>Import Class/Co-Class Teacher</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportclass_coclass_teacher"></i>Export Class/Co-Class Teacher</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/ClassTeacher_CoClassTeacher_Details_Sample.xls"></i>Download Sample Excel </a></td>
</tr>

<tr>
    <td>5</td>
    <td>Class Section Relations</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importclass_coclass_teacher"></i>Import Class Section Relations</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportclass_section_relations"></i>Export Class Section Relations</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/Class_Section_Relations_Sample.xls"></i>Download Sample Excel </a></td>
</tr>

<tr>
    <td>6</td>
    <td>School Calender</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importschool_calender"></i>Import School Calender</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportschool_calender"></i>Export School Calender</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/School_Calender_Detail_Sample.xls"></i>Download Sample Excel </a></td>
</tr>

<tr>
    <td>7</td>
    <td>Discount Scheme</td>

    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importdiscountscheme"></i>Import Discount Scheme</a></td>

    <td><a href="<?php echo ADMIN_URL ;?>Datarecord/exportdiscountscheme"></i>Export Discount Scheme</a></td>
    <td><a href="<?php echo SITE_URL ;?>Sample/Discount_Scheme_Sample.xls"></i>Download Sample Excel </a></td>
</tr>

<tr>
    <td>8</td>
    <td>Item Categroy</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importitem_category"></i>Import Item Categroy</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Item Categroy</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>


   
</tr>
     
<tr>
      <td>9</td>
    <td>Import Items</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/import_items"></i>Items Import</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Items Import</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>10</td>
    <td>Import Outstanding Fees</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/import_outstanding_fees"></i>Import Outstanding Fees</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Outstanding Fees</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>11</td>
    <td>Import Due Fees</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/import_due_fees"></i>Import Due Fees</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Due Fees</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>12</td>
    <td>Import Enquirys</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/impot_enquiry"></i>Import Enquirys</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Enquirys</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>13</td>
    <td>Import Drop Students</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/import_drop_students"></i>Import Drop Students</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Drop Students</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>14</td>
    <td>Import Stockregister</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/stockregister"></i>Import Stockregister</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Stockregister</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

<tr>
      <td>15</td>
    <td>Import Vendors</td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/importvendors"></i>Import Vendors</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Export Vendors</a></td>
    <td><a href="<?php echo ADMIN_URL;?>Datarecord/"></i>Download Sample Excel</a></td>

</tr>

          </tbody>

          </table>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

</section>
<!-- /.content -->


