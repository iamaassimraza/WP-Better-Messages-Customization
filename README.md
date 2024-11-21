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
function add_group_creator_role() {
    add_role('group_creator', 'Group Creator', array('read' => true, 'edit_posts' => true));
}
add_action('init', 'add_group_creator_role');
