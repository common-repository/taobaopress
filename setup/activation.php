<?php 

function taobaopress_activate_plugin() {
    global $wp_roles;
    $roles = $wp_roles->get_names();
    foreach ($roles as $role_name => $name) {
        $role_object = get_role($role_name);
        $role_object->add_cap('use taobaopress');
    }
}

function taobaopress_deactivate_plugin() {
	
}

?>