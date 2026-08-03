<style>
    .tabs-menu.leftMenu .sideMenu {
        overflow-y: scroll;
        height: 100%;
    }

    .tabs-menu ul li a,
    .tabs-menu .nav-tabs>li.active>a {
        color: #1a1a1a;
        padding: 10px;
        display: block;
        align-items: center;
    }

    .tabs-menu ul li {
        list-style: none;
        height: 55px;
    }
</style>


<?php
// $findmenumodule = $this->Comman->findrolemenu();  
// $Not_featured=array();
// foreach($findmenumodule as $val){
//     $Not_featured[] = $val['featured'];
// }
// $cond=['0'];

// if(in_array($Not_featured, $cond)){  ?>

<?php
$findmenumodule = $this->Comman->findrolemenu();

$Not_featured = [];
foreach ($findmenumodule as $val) {
    $Not_featured[] = $val['featured'];
}
$cond = 0;
?>

<style>
    <?php echo (in_array($cond, $Not_featured)) ? '.tabs-menu.leftMenu { display:block;}' : '.tabs-menu.leftMenu { display:none; !important; }'; ?>
</style>


<script>
    window.onload = function () {
        var element = document.getElementByClass(".tabs-menu.leftMenu");
    };
</script>



<section class="tabs-menu leftMenu ">
    <ul class=" nav-tabs leftMenu ">
        <?php $menu = 0;
        $i = 0;
        foreach ($findmenumodule as $key => $itemd) {
            $findmenumodulejk = $this->Comman->findrolemenucontent($itemd['module']);
            foreach ($findmenumodulejk as $jki => $rty) { 
                
                ?>
                <li>
                    <a href="<?php echo ADMIN_URL; ?><?php echo $rty['controller']; ?>/<?php echo $rty['action']; ?>">
                        <?php
                      $file = SITE_URL . 'images/subMenu/' . $rty['menu'] . '-sub.png';
                //  pr($file);die;
                   
                        // if (file_exists($file)) { ?>

                            <img src="<?php echo SITE_URL; ?>images/subMenu/<?php echo $rty['menu']; ?>-sub.png" alt="submenu"
                                class="submenuIcon"><br>
                        <?php// } else { ?>
                            <!-- <img src="<?php echo SITE_URL; ?>images/subMenu/View All Prospectus-sub.png" alt="Registration Manager"
                                alt="submenu" class="submenuIcon"><br> -->
                        <?php// } ?>
                        <span><?php echo $rty['menu']; ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php $menu++;
            $i++;
        } ?>

    </ul>

</section>


<script>
    $(document).ready(function () {
        $(".registrationManagerMenu a").click(function () {
            $(".registrationManagerMenu").addClass("step2");
        });
        $(".admissionMenu a").click(function () {
            $(".admissionMenu").addClass("step2");
        });
        $(".FeesMasterTab a").click(function () {
            $(".FeesMasterTab").addClass("step2");
        });
        $(".reportCenterTab a").click(function () {
            $(".reportCenterTab").addClass("step2");
        });
        $(".schoolStaffTab a").click(function () {
            $(".schoolStaffTab").addClass("step2");
        });
        $(".galleryTab a").click(function () {
            $(".galleryTab").addClass("step2");
        });
        $(".notificationsTab a").click(function () {
            $(".notificationsTab").addClass("step2");
        });
        $(".homeworkTab a").click(function () {
            $(".homeworkTab").addClass("step2");
        });

        $(".feesReport a").click(function () {
            $(".feesReport").addClass("step3");
        });
        $(".academicReport a").click(function () {
            $(".academicReport").addClass("step3");
        });
        $(".academicReport2 a").click(function () {
            $(".academicReport2").addClass("step3");
        });
    });
</script>

<script>
    $(document).ready(function () {
        $(".tabHeading").click(function () {
            $(".registrationManagerMenu").removeClass("step2");
        });
        $(".tabHeading").click(function () {
            $(".admissionMenu").removeClass("step2");
        });
        $(".tabHeading").click(function () {
            $(".FeesMasterTab").removeClass("step2");
        });
        $(".tabHeading").click(function () {
            $(".reportCenterTab").removeClass("step2");
        });
        $(".tabHeading").click(function () {
            $(".schoolStaffTab").removeClass("step2");
        });

        $(".tabHeading").click(function () {
            $(".galleryTab").removeClass("step2");
        });

        $(".tabHeading").click(function () {
            $(".notificationsTab").removeClass("step2");
        });

        $(".tabHeading").click(function () {
            $(".homeworkTab").removeClass("step2");
        });


        $(".tabHeading2 ").click(function () {
            $(".feesReport").removeClass("step3");
        });
        $(".tabHeading2 ").click(function () {
            $(".academicReport").removeClass("step3");
        });
        $(".tabHeading2 ").click(function () {
            $(".academicReport2").removeClass("step3");
        });
    });
</script>