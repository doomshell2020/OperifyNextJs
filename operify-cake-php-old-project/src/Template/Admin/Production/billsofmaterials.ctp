<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Bills of Materials
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/billsofmaterials">
                    Bills of Materials
                </a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <!-- <div class="top_row">
                                <input type="checkbox">
                                 <h3>Sales Invoices</h3> 
                                <a href="<?php echo SITE_URL; ?>admin/Production/addbom"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>BOM</button></a>
                                <a href="<?php echo SITE_URL; ?>admin/Production/billsofmaterials"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Folder</button></a>
                                <button>Filter</button>
                                <input type="text" placeholder="Search by numbers and comments">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <input type="text" value="0">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Edit
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <button><i class="fa fa-cog" aria-hidden="true"></i></button>
                            </div> -->
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body"  style="padding-top:0px;">
                        <table class="table table-bordered table-striped" width = "100%">
                            <thead>
                                <tr>
                                    <th width="3%">S.No.</th>
                                    <th width="20%">Contract Name</th>
                                    <th width="10%">Project Cost</th>
                                    <th width="10%">Operational Cost</th>
                                    <th width="10%">Labour Cost</th>
                                    <th width="30%">Comment</th>
                                    <th width="9%">Generated Date</th>
                                    <th width="9%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php
                               $i=1;
                                foreach($bills_data as $value){
                                $contractname =$this->comman->findcontractname($value['contract_id']);
                                ?>
                                <tr>
                                    <td><?php echo $i; ?>.</td>
                                    <td><a  href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $value['contract_id']; ?>" class="viewdetails"><?php echo $contractname['title'].'('.$contractname['workorder'].')'; ?></a></td>

                                    <!-- <td><a target="_blank" href="<?php echo SITE_URL; ?>admin/production/viewdetail/<?php echo $value['id']; ?>"><?php echo $contractname['title']; ?></a></td> -->
                                    <td style="text-align:end;"><?php echo sprintf('%.2f',$contractname['cost']); ?></td>
                                    <td style="text-align:end;"><?php echo sprintf('%.2f',$value['operation_cost']); ?></td>
                                    <td style="text-align:end;"><?php echo sprintf('%.2f',$value['labour_cost']); ?></td>
                                    <td ><?php echo $value['comment']; ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($value['created'])); ?></td>
                                    <td>
                                    <!-- <a style="font-size: 20px;"  target="_blank" href="<?php echo ADMIN_URL;?>production/viewcontractdetailspdf/<?php echo $value['contract_id']; ?>"><i class="fa fa-file-pdf-o" style="font-size: 20px;"></i></a>&nbsp;&nbsp;&nbsp; -->
                                    <?php
                                    $getpro = $this->comman->checkproductionorder($value['contract_id']);
                                    // pr($getpro);
                                    // if (is_array($getpro) && empty($getpro)) {
                                        echo $this->Html->link('', [
                                            'action' => 'editaddbom',
                                            $value->id,
                                         ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                    //    }
                                          ?>
                                </td>
                                </tr>
                                <?php
                            $i++; }?>
                            </tbody>
                        </table>
                  <?php echo $this->element('admin/pagination'); ?>
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
</div>
<!-- /.   content-wrapper -->
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:100% !important;">
        <div class="modal-content personal">
            <div class="loader">
                <div class="es-spinner">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".globalModals").click(function(event) {
            // alert($(this).attr("href"));
            $('.modal-content').load($(this).attr("href")); //load content from href of link
        });
    });
</script>
<script>
$('.viewdetails').click(function(e) {
   e.preventDefault();
   $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
});
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>