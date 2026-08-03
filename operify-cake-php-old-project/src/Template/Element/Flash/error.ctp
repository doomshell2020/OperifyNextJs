<section class="content-header">
    <div class="alert alert-danger alert-dismissible alert-fade" style="width: 100%;">
        <i class="icon fa fa-ban"></i> <?=__('Alert')?>!
        <?=h($message)?>
        <button class="pull-right" aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
</section>

<script>
    $(document).ready(function() {
       
        setTimeout(function() {
            $('.alert-fade').fadeOut();
        }, 4000);

        $('.alert-fade .close').on('click', function() {
            $(this).closest('.alert-fade').fadeOut();
        });
    });
</script>