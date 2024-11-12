<?php
/**
 * Custom modifications for group creation and reply restrictions.
 * Demonstration file for portfolio purposes.
 * 
 * This file showcases code added for managing a new 'Group Creator' role, a feature to control reply permissions within groups, and saving custom meta data in a custom WordPress setup using Ultimate Member (UM) and Better Messages plugins.
 */

// PART 1: Create a custom role called "Group Creator"
function add_group_creator_role() {
    add_role(
        'group_creator',
        'Group Creator',
        array(
            'read' => true,        // Basic capability for reading
            'edit_posts' => true,  // Allows editing posts (optional)
            // Add other capabilities as needed for group management
        )
    );
}
add_action('init', 'add_group_creator_role');

// PART 2: Save the "Enable Feature" Checkbox Value for Groups
// Hook into the group creation process after the form submission to save custom field data.
add_action('um_groups_after_front_insert', 'save_enable_feature_checkbox', 10, 2);

function save_enable_feature_checkbox($formdata, $group_id) {
    // Check if the 'enable_feature' field is set and set it to '1' if checked, '0' if unchecked
    $enable_feature = isset($_POST['enable_feature']) ? '1' : '0';
    
    // Save the 'enable_feature' setting in the group post meta
    update_post_meta($group_id, '_um_groups_enable_feature', $enable_feature);
}

// PART 3: Custom Function for Reply Permissions Based on Group Feature
// This function restricts group replies based on the 'enable_feature' setting.
add_filter('better_messages_can_user_reply', 'restrict_reply_based_on_group_feature', 10, 2);

function restrict_reply_based_on_group_feature($can_reply, $thread_id) {
    // Fetch the current user and group ID
    $user_id = Better_Messages()->functions->get_current_user_id();
    $group_id = Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
    
    // Retrieve custom meta value for 'enable_feature' (1 or 0)
    $enable_feature = get_post_meta($group_id, '_um_groups_enable_feature', true);
    
    // Retrieve the group creator ID
    $group_creator_id = get_post_field('post_author', $group_id);

    // Apply restriction: if 'enable_feature' is 1, only the group creator can reply
    if ($enable_feature === '1' && $user_id !== $group_creator_id) {
        // Restrict access for non-creators
        return new WP_Error(
            'rest_forbidden',
            _x('Sorry, only the group creator can reply in this group', 'Rest API Error', 'bp-better-messages'),
            array('status' => rest_authorization_required_code())
        );
    }

    return $can_reply;
}

/**
 * NOTE:
 * - Ensure each section is properly configured within its respective context in the theme or plugin files if using this in a real environment.
 * - This file is for demonstration purposes; in a live setup, these functions should be split into appropriate files for organization and functionality.
 */
?>
