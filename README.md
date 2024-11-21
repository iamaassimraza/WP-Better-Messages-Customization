# Group-Specific Reply Restrictions with Role Management in WordPress

This repository showcases the implementation of dynamic group reply restrictions and temporary role management using the **Better Messages** and **UM Groups** WordPress plugins. It also demonstrates how to use meta fields to control reply permissions and dynamic role upgrades/downgrades for group creators.

---

## **Features Implemented**

### 1. **Custom Group Reply Restrictions**
   - **Objective**: Allow group creators to control whether only they or all members can reply in group chats.
   - A custom checkbox, `Enable Feature`, was added to the group creation form.
   - This checkbox value is saved in the WordPress meta table and retrieved to enforce reply restrictions.

---

### 2. **Temporary Role Management**
   - **Objective**: Allow group creators to temporarily gain administrative privileges to delete other users' messages.
   - **How It Works**:
     - If a group allows all members to reply, the group creator's role is temporarily upgraded to `Administrator` when they access the group chat page.
     - The creator's original role is saved in the database and restored when they leave the group chat page.

---

### 3. **Meta Field Creation and Usage**
   - A new meta field, `_um_groups_enable_feature`, is created in the WordPress database to store the reply permissions for each group.
   - This field is checked dynamically in API functions to restrict or allow replies.

---

## **Key Code Highlights**

### 1. **Saving Group Settings**
File: `functions.php`
```php
add_action('um_groups_after_front_insert', 'save_enable_feature_checkbox', 10, 2);

function save_enable_feature_checkbox($formdata, $group_id) {
    $enable_feature = isset($_POST['enable_feature']) ? '1' : '0';
    update_post_meta($group_id, '_um_groups_enable_feature', $enable_feature);
}
