# WP Custom Group Restriction Demo With Better-Messages, Ultimate Members Plugin

This repository demonstrates custom code for managing group reply permissions in a WordPress environment using the Ultimate Member (UM) and Better Messages plugins. The functionality includes:
- Creation of a custom "Group Creator" role.
- Addition of a checkbox to control reply permissions in groups.
- Restriction of group replies based on the creator-only setting.

## Overview

The modifications here allow a group creator to control whether only they, or all members, can reply within the group. This is managed through a custom checkbox on the group creation form and enforced via backend restrictions.

## File Contents

### `custom-group-functions.php`
This file includes all custom functions required for:
- Creating the "Group Creator" role.
- Saving the "Enable Feature" setting on group creation.
- Restricting replies based on the "Enable Feature" setting.

## Usage

1. **Add the Custom Code**: Place `custom-group-functions.php` in your theme's `functions.php` file or within a custom plugin.

2. **Feature Control**: 
   - A checkbox titled "Allow others to reply" will appear on the group creation form.
   - When checked, all group members can reply. When unchecked, only the group creator can reply.

3. **Testing**: 
   - Log in as the group creator to verify that reply access works as expected.
   - Test with other group members to confirm restriction enforcement.

## Compatibility

- **WordPress**: Version 5.8 and above.
- **Plugins**: 
  - Ultimate Member (UM)
  - Better Messages
  
This demo is designed for educational and portfolio purposes, showing best practices in WordPress customization for user role and permissions management.

## License

This code is for demonstration purposes only and may require additional customization in a live environment.

---

## Contact

For further inquiries or assistance, feel free to reach out.

---

Enjoy customizing WordPress with enhanced group control features!
