# Group-Specific Reply Restrictions with Role Management in WordPress

This repository demonstrates how to implement group-specific reply restrictions and temporary role management in WordPress using the **Ultimate Member (UM) Groups** plugin and **Better Messages** plugin.

## Objective

- **Restrict reply permissions** in group chat threads based on a custom checkbox.
- Allow group creators to **delete other users' messages** when the "Allow Replies" feature is enabled by temporarily upgrading their role to Administrator.

---

## Features

1. **Custom Role Creation**: Introduces a new `group_creator` role for managing group-specific permissions.
2. **Reply Restrictions**: Limits reply permissions in group threads based on a custom checkbox.
3. **Temporary Role Upgrades**: Temporarily upgrades group creators to administrators when "Allow Replies" is enabled.
4. **Seamless Role Reversion**: Automatically downgrades roles to their previous state when the feature is disabled.

---

## Files Modified

### 1. `functions.php`
**Path**: `wp-content/themes/your-theme/functions.php`  
**Purpose**:  
- Create and manage a custom `group_creator` role.
- Save and manage the `enable_feature` checkbox value.
- Implement role upgrade and downgrade functions dynamically.

### 2. `create-group-form.php`
**Path**: `wp-content/plugins/um-groups/templates/create-group-form.php`  
**Purpose**:  
- Add a custom "Enable Feature" checkbox to the group creation form.

### 3. `rest-api.php`
**Path**: `wp-content/plugins/better-messages/includes/rest-api.php`  
**Purpose**:  
- Restrict reply permissions based on the group meta value (`enable_feature`).
- Return appropriate error messages for unauthorized users.

---

## Step-by-Step Implementation

### 1. Role Management
- Adds a custom `group_creator` role in `functions.php`.
- Provides role upgrade and downgrade logic for group creators dynamically.

```php
// Add a custom role called "Group Creator"
Adds a custom "Enable Feature" checkbox in the group creation form via create-group-form.php.
function add_group_creator_role() {
    add_role('group_creator', 'Group Creator', array('read' => true, 'edit_posts' => true));
}
add_action('init', 'add_group_creator_role');

### 2. Group Creation Form
Adds a custom "Enable Feature" checkbox in the group creation form via create-group-form.php
<div class="um-group-field" data-key="enable_feature">
    <label for="um_groups_enable_feature">
        <input type="checkbox" name="enable_feature" value="1" <?php checked($enable_feature, '1'); ?> />
        <?php _e("Allow others to reply (Creator-Only Reply)", "um-groups"); ?>
    </label>
</div>

### 3. Reply Restriction Logic
Restricts reply permissions based on the enable_feature checkbox value via rest-api.php.
add_filter('better_messages_can_user_reply', 'restrict_reply_based_on_group_feature', 10, 2);
function restrict_reply_based_on_group_feature($can_reply, $thread_id) {
    $user_id = Better_Messages()->functions->get_current_user_id();
    $group_id = Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
    $enable_feature = get_post_meta($group_id, '_um_groups_enable_feature', true);
    $group_creator_id = get_post_field('post_author', $group_id);

    if ($enable_feature === '1' && $user_id !== $group_creator_id) {
        return new WP_Error('rest_forbidden', 'Sorry, only the group creator can reply in this group');
    }

    return $can_reply;
}
### 4. Database Updates
Group Meta Data: Stored in the wp_postmeta table under the key _um_groups_enable_feature.
User Roles: Temporarily saved in the wp_usermeta table under the key _previous_roles.

### 5. Testing the Implementation
Group Creation Form:

Verify the "Enable Feature" checkbox appears and saves correctly.
Role Management:

Confirm dynamic role upgrades and downgrades based on feature activation.
Reply Restrictions:

Test with different configurations to ensure permissions work as expected.

### How to Use
Clone this repositor:
Place the modified files in their respective directories within your WordPress installation.
Follow the testing guidelines to validate the implementation.

### Contributions
Feel free to open issues or submit pull requests if you encounter any bugs or have ideas for enhancements.
