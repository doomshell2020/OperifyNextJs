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

// if(in_array($Not_featured, $cond)){  
?>

<script>
    window.onload = function() {
        var element = document.getElementByClass(".tabs-menu.leftMenu");
    };
</script>

<?php
$rolepresent = $this->request->session()->read('Auth.User.role_id');



$role_permissions = $this->Permission->permissioncheck(); ?>


<section class="tabs-menu leftMenu ">
    <ul class=" nav-tabs leftMenu ">

        <?php if ($rolepresent == 101) { ?>


            <li>
                <a href="<?php echo SITE_URL; ?>admin/users/add">

                    <img src="<?php echo SITE_URL; ?>images/subMenu/Companys-sub.png" alt="submenu" class="submenuIcon"><br>
                    <span>Companys</span>
                </a>
            </li>

            <li>
                <a href="<?php echo SITE_URL; ?>admin/Permission/index">

                    <img src="<?php echo SITE_URL; ?>images/subMenu/Add-sub.png" alt="submenu" class="submenuIcon"><br>
                    <span>Add</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/Sitesettings/index">

                    <img src="<?php echo SITE_URL; ?>images/subMenu/Sitesetting-sub.png" alt="submenu" class="submenuIcon"><br>
                    <span>Sitesetting</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/roles/index">

                    <img src="<?php echo SITE_URL; ?>images/subMenu/Users-sub.png" alt="submenu" class="submenuIcon"><br>
                    <span>Users</span>
                </a>
            </li>

        <?php   } else { ?>

            <?php $fileurl = "admin/reverseindent/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/reverseindent/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Reverse-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Reverse</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/inspection/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/inspection/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Inspections-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Inspections</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/employees/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/employees/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Employees-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Employees</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/transporter/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/transporter/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Dispatch-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Dispatch</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/stockregister/dailystock";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/stockregister/dailystock">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Daily Stock-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Daily Stock</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/payments/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/payments/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Payments-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Payments</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/purchasereturn/index";
            if (in_array($fileurl, $role_permissions)) {  ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>admin/purchasereturn/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Return-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Return</span>
                    </a>
                </li>
            <?php } ?>

            <?php $fileurl = "admin/maintenance/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/maintenance/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Maintenance-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Maintenance</span>
                    </a>
                </li>
            <?php } ?>

            <?php $fileurl = "admin/designsheet/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/designsheet/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Design Sheet-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Design Sheet</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/production/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/production/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Daily Sheet-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Daily Sheet</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/production/billsofmaterials";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/production/billsofmaterials">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/BOM-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>BOM</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/production/productionorders";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/production/productionorders">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Production-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Production</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/indentpo/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/indentpo/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Indent-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Indent</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/purchaseorder/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/purchaseorder/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/PO-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>PO</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/goodsreceived/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/goodsreceived/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/GRN-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>GRN</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/vendors/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/vendors/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Suppliers-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Suppliers</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/measurementunit/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/measurementunit/index">
                        <img src="<?php echo SITE_URL; ?>images/subMenu/View All Prospectus-sub.png" alt="Registration Manager" class="submenuIcon"><br>
                        <span>UOM</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/additem/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/additem/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Products-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Products</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/stockregister/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/stockregister/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Stock-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Stock</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/stockregister/daily_stockreport";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/stockregister/daily_stockreport">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Stock Report-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Stock Report</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/itemcategory/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/itemcategory/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Categories-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Categories</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/companymaster/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/companymaster/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Company-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Company</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/machine/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/machine/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Machines-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Machines</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/contracts/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/contracts/index">

                        <img src="<?php echo SITE_URL; ?>images/subMenu/Contracts-sub.png" alt="submenu" class="submenuIcon"><br>
                        <span>Contracts</span>
                    </a>
                </li>
            <?php } ?>
            <?php $fileurl = "admin/taxmaster/index";
            if (in_array($fileurl, $role_permissions)) {  ?> <li>
                    <a href="<?php echo SITE_URL; ?>admin/taxmaster/index">
                        <img src="<?php echo SITE_URL; ?>images/subMenu/View All Prospectus-sub.png" alt="Registration Manager" class="submenuIcon"><br>
                        <span>Tax Manager</span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>

</section>