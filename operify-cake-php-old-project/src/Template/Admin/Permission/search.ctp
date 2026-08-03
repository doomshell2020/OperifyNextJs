<table class="permissions-table">
    <thead>
        <tr>
            <th>MANAGERS NAME</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($groupedModules as $manager_id => $permissions) {
            $managers = $this->Permission->managerName($manager_id);
            $manager_name = $managers['name'];
            ?>
            <tr class="controller-row">
                <td><?php echo ucfirst($manager_name); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tr>
                            <?php
                            $totalChecked = 0;

                            foreach ($permissions as $permission) {
                                if (!empty($permission['checked'])) {
                                    $totalChecked++;
                                }
                            }
                            $Count = count($permissions);

                            ?>
                           
                                <td>

                                    <input type="checkbox" <?php if($Count == $totalChecked){?> checked <?php } ?>class="select-all" data-controller="<?php echo htmlspecialchars($manager_id); ?>" />
                                    Select All
                                </td>
                           
                            <?php foreach ($permissions as $permission) {
                                ?>
                                <td>
                                    <input type="hidden"
                                        name="permissions[<?php echo $permission['p_lable_id']; ?>][is_permission]" value="0" />
                                    <input type="checkbox"
                                        name="permissions[<?php echo $permission['p_lable_id']; ?>][is_permission]"
                                        class="permission-checkbox"
                                        data-controller="<?php echo htmlspecialchars($manager_id); ?>" value="1" <?php echo $permission['checked'] ? 'checked' : ''; ?> /> 
                                        <!-- <a href="#" class="label-name" onmouseover="showMessage(this)" onmouseout="hideMessage(this)"><?php echo ucfirst($permission['label_name']); ?>
                                             </a> <span
                                        class="hover-message" style="display: none;"><?php echo $permission['url']; ?></span> -->

                                        <span title="<?php echo htmlspecialchars($permission['url']); ?>" style="cursor: pointer;">
                                        <?php echo htmlspecialchars($permission['label_name']); ?>
                                    </span>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    // Toggle all checkboxes for a given controller's actions
    document.querySelectorAll('.select-all').forEach(selectAllCheckbox => {
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            const controller = this.getAttribute('data-controller');

            const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-controller="${controller}"]`);

            permissionCheckboxes.forEach(permissionCheckbox => {
                permissionCheckbox.checked = isChecked;
            });
        });
    });
</script>

