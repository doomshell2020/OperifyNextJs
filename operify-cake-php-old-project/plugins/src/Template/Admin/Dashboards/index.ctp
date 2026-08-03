<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
<?php echo $this->Flash->render(); ?>
    <!-- Content Header (Page header) -->
	    <section class="content-header">
	      <h1>
		Dashboard
		<small>Control panel</small>
	      </h1>
	      <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Dashboard</li>
	      </ol>
	    </section>

    <!-- Main content -->
	    <section class="content">
	      <!-- Small boxes (Stat box) -->
	      <div class="row">

		<div class="col-lg-3 col-xs-6">
		  <!-- small box -->
			  <div class="small-box bg-aqua">
				<div class="inner">
				   <h3><?php echo $count_subscribe;?></h3>
					<p>Total Subscriptions</p>
				</div>
				    <div class="icon">
				      <i class="ion ion-bag"></i>
				    </div>
		    		<a href="<?php echo $this->Url->build('/admin/subscribes/index'); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
		  	</div>
		</div>
		<!-- ./col -->
		<div class="col-lg-3 col-xs-6">
		  <!-- small box -->
		  <div class="small-box bg-green">
			    <div class="inner">
			      <h3><?php echo $count_testimonials;?></h3>

			      <p>Total Testimonials</p>
			    </div>
			    <div class="icon">
			      <i class="ion ion-stats-bars"></i>
			    </div>
		    	<a href="<?php echo $this->Url->build('/admin/testimonials/index'); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
		  </div>
		</div>
		<!-- ./col -->
		<div class="col-lg-3 col-xs-6">
		  <!-- small box -->
		  <div class="small-box bg-yellow">
			    <div class="inner">
			      <h3><?php echo $count_contact;?></h3>

			      <p>Total Contacts</p>
			    </div>
			    <div class="icon">
			      <i class="ion ion-person-add"></i>
			    </div>
		    <a href="<?php echo $this->Url->build('/admin/contacts/index'); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
		  </div>
		</div>
		<!-- ./col -->
		<div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $count_services;?></h3>

              <p>Services</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="<?php echo $this->Url->build('/admin/services/index'); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
		
		<!-- ./col -->
	      </div>
	      <!-- /.row -->
	      

	    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
