Group-Specific Reply Restrictions with Role Management in WordPress
This repository demonstrates how to implement group-specific reply restrictions and temporary role management in WordPress using the Ultimate Member (UM) Groups plugin and the Better Messages plugin.

Objective
Restrict Reply Permissions: Control reply permissions in group chat threads based on a custom checkbox.
Role Management: Temporarily upgrade the group creator's role to Administrator to enable message deletion when "Allow Replies" is enabled.
Files Modified
1. functions.php
Path: wp-content/themes/your-theme/functions.php
Purpose:
Create a new group_creator role.
Save and manage the enable_feature checkbox value.
Implement role upgrade and downgrade functions.
Handle dynamic role management during page load.
2. create-group-form.php
Path: wp-content/plugins/um-groups/templates/create-group-form.php
Purpose:
Add a custom "Enable Feature" checkbox to the group creation form.
3. rest-api.php
Path: wp-content/plugins/better-messages/includes/rest-api.php
Purpose:
Restrict reply permissions based on the group meta value (enable_feature).
Return appropriate error messages for unauthorized users.
Implementation
1. Role Management
Code Location: functions.php

Create a New Role:

php
Copy code
function add_group_creator_role() {
    add_role(
        'group_creator',
        'Group Creator',
        array(
            'read' => true,
            'edit_posts' => true,
        )
    );
}
add_action('init', 'add_group_creator_role');
Temporarily Upgrade User Role:

php
Copy code
function upgrade_user_role($user_id, $new_role = 'administrator') {
    $user = get_userdata($user_id);
    $current_roles = $user->roles;
    update_user_meta($user_id, '_previous_roles', $current_roles);

    foreach ($current_roles as $role) {
        $user->remove_role($role);
    }
    $user->add_role($new_role);
}
Revert User Role:

php
Copy code
function downgrade_user_role($user_id) {
    $previous_roles = get_user_meta($user_id, '_previous_roles', true);
    if ($previous_roles && is_array($previous_roles)) {
        $user = get_userdata($user_id);
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }
        foreach ($previous_roles as $role) {
            $user->add_role($role);
        }
        delete_user_meta($user_id, '_previous_roles');
    }
}
2. Group Creation Form
Code Location: create-group-form.php

Add the "Enable Feature" Checkbox:
php
Copy code
<div class="um-group-field" data-key="enable_feature">
    <label for="um_groups_enable_feature">
        <input type="checkbox" name="enable_feature" value="1" <?php checked($enable_feature, '1'); ?> />
        <?php _e("Allow others to reply (Creator-Only Reply)", "um-groups"); ?>
    </label>
</div>
3. Reply Restriction Logic
Code Location: rest-api.php

Restrict Replies:
php
Copy code
add_filter('better_messages_can_user_reply', 'restrict_reply_based_on_group_feature', 10, 2);

function restrict_reply_based_on_group_feature($can_reply, $thread_id) {
    $user_id = Better_Messages()->functions->get_current_user_id();
    $group_id = Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
    $enable_feature = get_post_meta($group_id, '_um_groups_enable_feature', true);
    $group_creator_id = get_post_field('post_author', $group_id);

    if ($enable_feature === '1' && $user_id !== $group_creator_id) {
        return new WP_Error(
            'rest_forbidden',
            _x('Sorry, only the group creator can reply in this group', 'Rest API Error', 'bp-better-messages'),
            array('status' => rest_authorization_required_code())
        );
    }
    return $can_reply;
}
4. Role Management on Page Load
Code Location: functions.php

Manage Roles Dynamically:
php
Copy code
add_action('wp', function () {
    if (!is_user_logged_in()) {
        return;
    }

    global $post;

    if (!$post || $post->post_type !== 'um_groups') {
        return;
    }

    $current_user_id = wp_get_current_user()->ID;
    $group_id = $post->ID;
    $enable_feature = get_post_meta($group_id, '_um_groups_enable_feature', true);
    $group_creator_id = get_post_field('post_author', $group_id);

    if ($group_creator_id == $current_user_id && $enable_feature === '0') {
        upgrade_user_role($current_user_id, 'administrator');
    } else {
        downgrade_user_role($current_user_id);
    }
});
Database Updates
Group Metadata:
The enable_feature value is stored as a custom meta field (_um_groups_enable_feature) in the wp_postmeta table.
User Roles:
Temporary user roles are stored in the wp_usermeta table under the _previous_roles key.
Testing
Group Creation Form:

Ensure the "Enable Feature" checkbox appears during group creation.
Verify the checkbox value is stored in the database.
Role Management:

Confirm the group creator's role is upgraded and reverted dynamically.
Reply Restrictions:

Test with both "Allow Replies" enabled and disabled to ensure proper functionality.
How to Use This Repository
Clone the repository.
Copy the modified files to their respective paths in your WordPress setup.
Test the implementation as outlined above.
Feedback and Contributions
Feel free to submit issues or contribute improvements via pull requests. For further support, contact me at +92 3205914007.

You can now copy and upload this README file to your repository. It’s well-structured and reader-friendly!






You said:
when i will copy you details, then i have to make heading ans sub heading - by myslf. correct?
ChatGPT said:
ChatGPT
No, the headings and subheadings in the README file I provided are already properly formatted using Markdown syntax. If you copy and paste the file directly into your repository (e.g., as a README.md file), GitHub or any Markdown-compatible viewer will automatically render the headings and subheadings correctly.

Here’s a quick breakdown of how the formatting works:

Markdown Syntax Overview:
# creates a main heading (H1).
## creates a subheading (H2).
### creates a smaller subheading (H3).
Lists are created using - or *.
When you copy the content I provided into a README.md file, it will look like this:

Group-Specific Reply Restrictions with Role Management in WordPress
This repository demonstrates...

Objective
Restrict Reply Permissions...
Role Management...
