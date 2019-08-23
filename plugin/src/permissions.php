<?php

function addAdminRoles($roleSlug, $wp_roles) {
    $wp_roles->add_cap($roleSlug, "tv_editOptions");
    $wp_roles->add_cap($roleSlug, "tv_addTables");
    $wp_roles->add_cap($roleSlug, "tv_editTables");
    $wp_roles->add_cap($roleSlug, "tv_deleteTables");
}

function addSuperEmployeeRoles($roleSlug, $wp_roles) {
    $wp_roles->add_cap($roleSlug, "tv_exportReservations");
    $wp_roles->add_cap($roleSlug, "tv_editReservations");
    $wp_roles->add_cap($roleSlug, "tv_deleteReservations");
}

function addEmployeeRoles($roleSlug, $wp_roles) {
    $wp_roles->add_cap($roleSlug, "tv_viewReservations");
    $wp_roles->add_cap($roleSlug, "tv_viewTables");
    $wp_roles->add_cap($roleSlug, "tv_addReservations");

}

function initPermissions() {
    global $wp_roles;

    $isset = get_option("areTvRolesSet");

    if(! $isset) {
        add_role("owner", "Betreiber", array(
            "read" => true
        ));
        add_role("superEmployee", "Super-Angestellter", array(
            "read" => true
        ));
        add_role("employee", "Angesteller", array(
            "read" => true
        ));

        addAdminRoles("owner", $wp_roles);

        addSuperEmployeeRoles("owner", $wp_roles);
        addSuperEmployeeRoles("superEmployee", $wp_roles);

        addEmployeeRoles("owner", $wp_roles);
        addEmployeeRoles("superEmployee", $wp_roles);
        addEmployeeRoles("employee", $wp_roles);

        update_option("areTvRolesSet", true);
    }
}
add_action("after_setup_theme", "initPermissions");





?>