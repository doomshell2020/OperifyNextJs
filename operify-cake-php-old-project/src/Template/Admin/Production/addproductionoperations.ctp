<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Production Operations

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/addoutings">Production Operations</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <table>
                        <div class="sls_invc_hd">
                            <div class="dropdown-center">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit
                                </button>
                                <ul class="dropdown-menu" style="left: 43px;">
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                    <li><a class="dropdown-item" href="#">Copy</a></li>
                                </ul>
                            </div>


                        </div>


                        <tr>
                            <th>

                                <span>Production Operations</span>


                            </th>




                        </tr>
                    </table>





                    <div class="box-body">
                        <div class="form-group">
                            <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Name<strong style='color:red;'>*</strong></label>
                                <?php echo $this->Form->input('name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Name ', 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>


                            <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Description<strong style='color:red;'>*</strong></label>
                                <?php echo $this->Form->input('folder', array('class' => 'form-control', 'type' => 'textarea', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>










                </div>
            </div>
        </div>
    </section>
</div>