<?php
// Save feature to allow other to reply or not in group by Kamal

// Hook into 'um_groups_after_front_insert' to save the checkbox value after the group is created
add_action('um_groups_after_front_insert', 'save_enable_feature_checkbox', 10, 2);

// Function to save the checkbox value for enable_feature
function save_enable_feature_checkbox($formdata, $group_id) {
    // Log the call to confirm the function is being executed
    error_log("save_enable_feature_checkbox called");

    // Check if 'enable_feature' is set in the form submission and set it to 1 if checked
    $enable_feature = isset($_POST['enable_feature']) ? '1' : '0';
    error_log("Enable Feature: " . $enable_feature);

    // Save or update the value in the post meta for the group
    update_post_meta($group_id, '_um_groups_enable_feature', $enable_feature);
}




function add_delete_capabilities_to_group_creator() {
    $role = get_role('group_creator');

    if ($role) {
        // Add capabilities to allow message deletion
        $role->add_cap('delete_posts'); // Allows deleting own posts/messages
        $role->add_cap('delete_others_posts'); // Allows deleting posts/messages from others
        $role->add_cap('delete_published_posts'); // Allows deleting published posts/messages if applicable

        // Optionally, add other capabilities if needed
        // $role->add_cap('manage_options'); // Use cautiously as it grants broad access
    }
}
add_action('admin_init', 'add_delete_capabilities_to_group_creator');


// Start session to track user roles
add_action('init', function () {
    if (!session_id()) {
        session_start();
    }
});




function upgrade_user_role($user_id, $new_role = 'administrator') {
    // Get the current user roles
    $user = get_userdata($user_id);
    $current_roles = $user->roles;

    // Save current roles in user meta for later restoration
    update_user_meta($user_id, '_previous_roles', $current_roles);

    // Remove all current roles
    foreach ($current_roles as $role) {
        $user->remove_role($role);
    }

    // Add the new role
    $user->add_role($new_role);
}

function downgrade_user_role($user_id) {
    // Retrieve the previously saved roles
    $previous_roles = get_user_meta($user_id, '_previous_roles', true);

    if ($previous_roles && is_array($previous_roles)) {
        $user = get_userdata($user_id);

        // Remove all current roles
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }

        // Reassign the previous roles
        foreach ($previous_roles as $role) {
            $user->add_role($role);
        }

        // Delete the meta after restoring
        delete_user_meta($user_id, '_previous_roles');
    }
}


add_action('wp', function () {
    if (!is_user_logged_in()) {
        error_log("User is not logged in.");
        return;
    }

    global $post;

    // Ensure the $post object exists
    if (!$post) {
        error_log("No post available.");
        return;
    }

    // Get the current user
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;

    error_log("Current user ID: $current_user_id");

    // Check if on a group page (Replace 'um_groups' with your actual post type)
    if (isset($post->post_type) && $post->post_type === 'um_groups') {
        $group_id = $post->ID; // Assuming the current group page corresponds to the post ID
        error_log("Group ID: $group_id");

        // Retrieve 'enable_feature' value, ensure a fallback to '0'
        $enable_feature = get_post_meta($group_id, '_um_groups_enable_feature', true);
        if ($enable_feature === '') {
            $enable_feature = '0'; // Fallback to '0' if meta value is not set
        }

        error_log("Enable feature value: $enable_feature");

        $group_creator_id = get_post_field('post_author', $group_id);
        error_log("Group creator ID: $group_creator_id");

        if ($group_creator_id == $current_user_id && $enable_feature === '0') {
            error_log("Upgrading user role to administrator.");
            upgrade_user_role($current_user_id, 'administrator');
        } else {
            error_log("User ID does not match group creator or feature is not enabled.");
        }
    } else {
        // On non-group pages, revert to the original role
        error_log("downgrade_user_role user role to the original role.");
        downgrade_user_role($current_user_id);
    }
});
