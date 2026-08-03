<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   
    <meta name="author" content="">
    <?= $this->Html->meta('icon') ?>
  
    <?= $this->Html->css([ 'bootstrap.min', 'font-awesome.min',
                          'datatables-extensions/dataTables.bootstrap.min',
                          'datatables-extensions/responsive.bootstrap',
                          'datatables-extensions/fixedHeader.bootstrap.min',
                          'datatables-extensions/scroller.bootstrap.min',
                          'style' ]) ?>
    <?=
        $this->Html->script([ 'jquery-1.12.3','bootstrap.min', 'jquery.dataTables.min',
                             'datatables-extensions/dataTables.bootstrap.min',
                             'datatables-extensions/dataTables.responsive.min',
                             'datatables-extensions/dataTables.fixedHeader',
                             'datatables-extensions/dataTables.scroller.min',
                            ])
    ?>
    <style>
    #adBlockerPopup .modal-header{background: #e74c3c;color: #fff;}
    #adBlockerPopup .modal-header h4{color: #fff;}
    #adBlockerPopup .modal-footer{text-align: center;}
    #adBlockerPopup .modal-footer i{color:#fff;padding-right: 5px;}
    #adBlockerPopup .modal-footer .btn{border-radius:0px;width: 200px;height: 40px;font-size: 17px;}
    </style>
</head>

<body>
    <input id="base_path" value="<?= SITE_URL; ?>" type="hidden"/>
    <script>
        var base_path = $('#base_path').val();
    </script>
    <?= $this->element('top-navigation') ?>

    <div class="container">
       <?= $this->Flash->render() ?>
       <div class="clearfix"></div>
       <?= $this->fetch('content') ?>
    </div>
    <!-- /container -->

    <script>
    /* Adblock */
    $(document).ready(function(){
        (function(){
            var adBlockFlag = document.createElement('div');
            adBlockFlag.innerHTML = '&nbsp;';
            adBlockFlag.className = 'adsbox';
            $('body').append(adBlockFlag);
            window.setTimeout(function() {
              if (adBlockFlag.offsetHeight === 0) {
                showAdBlockPopUp();
                $('body').addClass('adblock');
              }
              adBlockFlag.remove();
            }, 100);

            function showAdBlockPopUp(){
                var adBlockerPopup = $('#adBlockerPopup');
                adBlockerPopup.modal({
                    backdrop: 'static',
                    keyboard: false
                });
                adBlockerPopup.modal('show');
            }

            $(document).on('click', '#adBlockerPopupRefresh', function(){
                location.reload();
            });

        })();
    });
    /* Adblock */
    </script>

    <div class="modal fade" id="adBlockerPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                    <h4 class="modal-title text-center" id="myModalLabel">AD BLOCKER DETECTED</h4>
                </div>
                <div class="modal-body">
                    <div>
                       <p class="text-center">
                        <img src="https://1.bp.blogspot.com/-SsYfwjp2WjE/WBg1PZFwywI/AAAAAAAAHWA/MElz2W1KVT8Hdi7xRAc_Pi9VWRiUs6CsQCLcB/s1600/adblock.png" alt="AD BLOCKER DETECTED">
                       </p>
                        <p>
                            We have noticed that you have an ad blocker enabled which restricts ads served on the site.
                        </p>
                        <p>
                            Please support us by disabling ad blocker for <a href="http://www.smarttutorials.net/">smarttutorials.net </a> ...
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="adBlockerPopupRefresh">
                        <i class="fa fa-refresh" aria-hidden="true"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
