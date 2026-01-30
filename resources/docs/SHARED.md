# ACL Module - Shared Layer Documentation

## Overview

The Shared layer contains data contracts (transfers) and constants used across all application layers (Zed, Yves, Client, Glue, Service). This layer defines the common vocabulary and data structures for ACL functionality.

## Constants

### AclConstants

**Location**: `src/Spryker/Shared/Acl/AclConstants.php`

**Purpose**: Declares global environment configuration keys and ACL system constants.

| Constant | Type | Value | Purpose |
|----------|------|-------|---------|
| ACL_DEFAULT_RULES | string | 'ACL_DEFAULT_RULES' | Configuration key for default ACL rules applied during installation |
| ACL_DEFAULT_CREDENTIALS | string | 'ACL_DEFAULT_CREDENTIALS' | Configuration key for default credentials configuration |
| ACL_USER_RULE_WHITELIST | string | 'ACL_USER_RULE_WHITELIST' | Configuration key for user-specific rules that bypass ACL checks |
| VALIDATOR_WILDCARD | string | '*' | Wildcard character for bundle/controller/action matching in rules |
| ACL_SESSION_KEY | string | 'acl' | Session storage key for ACL data |
| ACL_CREDENTIALS_KEY | string | 'credentials' | Session storage key for user credentials |
| ACL_DEFAULT_KEY | string | 'default' | Key for default ACL configuration section |
| ACL_DEFAULT_RULES_KEY | string | 'rules' | Key for rules within default configuration |
| ROOT_GROUP | string | 'root_group' | Name identifier for root administrator group |
| ROOT_ROLE | string | 'root_role' | Name identifier for root administrator role with full permissions |
| ALLOW | string | 'allow' | Rule type constant for allowing access |

**Note**: `DENY` constant is not defined but the value `'deny'` is used in code and database schema as the counterpart to `ALLOW`.

### Messages

**Location**: `src/Spryker/Shared/Acl/Messages/Messages.php`

**Purpose**: Defines standardized error message keys for translation.

| Constant | Type | Value | Purpose |
|----------|------|-------|---------|
| GROUP_EXISTS_ERROR | string | 'acl.error.group_exists' | Translation key for "group already exists" error message |
| GROUP_NOT_FOUND_ERROR | string | 'acl.error.group_not_found' | Translation key for "group not found" error message |
| CHOOSE_GROUP_ERROR | string | 'acl.error.choose_group' | Translation key for "must choose a group" validation error |

## Transfer Definitions

**Location**: `src/Spryker/Shared/Acl/Transfer/acl.transfer.xml`

### Core Entity Transfers

#### GroupTransfer

**Purpose**: Represents an ACL group entity that contains users and roles.

**Properties**:
- `idAclGroup` (int, optional) - Primary key of the group
- `name` (string, optional) - Human-readable group name, must be unique
- `reference` (string, optional) - External reference identifier for the group

**Usage**: Groups organize users and assign them collections of roles. Users inherit all permissions from their groups' roles.

---

#### GroupsTransfer

**Purpose**: Collection wrapper for multiple group entities.

**Properties**:
- `groups` (Group[], optional, singular: group) - Array of GroupTransfer objects

**Usage**: Used when retrieving multiple groups (e.g., all groups for a user, all system groups).

---

#### RoleTransfer

**Purpose**: Represents an ACL role entity that defines a set of access rules.

**Properties**:
- `idAclRole` (int, optional) - Primary key of the role
- `name` (string, optional) - Human-readable role name, must be unique
- `idGroup` (int, optional) - Foreign key reference to associated group (deprecated usage)
- `rules` (string, optional) - Serialized rules data (legacy field)
- `aclGroup` (Group, optional) - Associated group entity
- `aclRules` (Rule[], optional, singular: aclRule) - Collection of bundle/controller/action rules
- `aclEntityRules` (AclEntityRule[], optional, singular: aclEntityRule) - Collection of entity-level ACL rules (for AclEntity module integration)

**Associations**:
- References `GroupTransfer`
- References `RuleTransfer` (array)
- References `AclEntityRuleTransfer` (array, external module)

**Usage**: Roles are assigned to groups and contain collections of access rules. Multiple roles can be assigned to a single group.

---

#### RolesTransfer

**Purpose**: Collection wrapper for multiple role entities.

**Properties**:
- `roles` (Role[], optional, singular: role) - Array of RoleTransfer objects

**Usage**: Used when retrieving multiple roles (e.g., all roles for a group, all roles for a user).

---

#### RuleTransfer

**Purpose**: Represents an individual ACL rule defining access permissions for a specific resource.

**Properties**:
- `idAclRule` (int, optional) - Primary key of the rule
- `bundle` (string, optional) - Spryker bundle/module name (e.g., 'Product', 'Customer'). Use '*' for wildcard.
- `controller` (string, optional) - Controller name within the bundle (e.g., 'EditController'). Use '*' for wildcard.
- `action` (string, optional) - Action method name (e.g., 'indexAction'). Use '*' for wildcard.
- `type` (string, optional) - Access type: 'allow' or 'deny'
- `fkAclRole` (int, optional) - Foreign key reference to the role this rule belongs to

**Usage**: Rules define granular permissions at the bundle/controller/action level. Wildcard matching is supported. More specific rules override general wildcard rules.

**Examples**:
- `{bundle: 'Product', controller: '*', action: '*', type: 'allow'}` - Allow all Product module access
- `{bundle: 'Customer', controller: 'Edit', action: 'deleteAction', type: 'deny'}` - Deny customer deletion

---

#### RulesTransfer

**Purpose**: Collection wrapper for multiple rule entities.

**Properties**:
- `rules` (Rule[], optional, singular: rule) - Array of RuleTransfer objects

**Usage**: Used when retrieving multiple rules (e.g., all rules for a role, all rules for a group).

---

### User Integration Transfers

#### UserTransfer (Extended)

**Purpose**: Extends User module's UserTransfer with ACL-specific properties.

**Properties Added by ACL Module**:
- `idUser` (int, optional) - User primary key
- `username` (string, optional) - User login name
- `aclGroups` (Group[], optional, singular: aclGroup) - ACL groups the user belongs to
- `isMerchantAgent` (bool, required, strict) - Flag indicating if user is a merchant agent (for merchant-specific ACL logic)

**Note**: This is an extension to the User module's transfer. Other properties exist in the base UserTransfer definition.

**Usage**: UserTransfer is passed to access control checks. The `aclGroups` property is populated with the user's ACL group memberships for permission evaluation.

---

### Criteria Transfers

#### AclRoleCriteria

**Purpose**: Search criteria for querying roles.

**Properties**:
- `names` (string[], optional, singular: name) - Filter by multiple role names
- `name` (string, optional) - Filter by single role name
- `reference` (string, optional) - **Deprecated**: Filter by reference. Will be removed in next major release.

**Usage**: Used in repository queries to find roles by name or reference.

---

#### GroupCriteria

**Purpose**: Search criteria for querying groups.

**Properties**:
- `idAclGroup` (int, optional) - Filter by group ID
- `names` (string[], optional, singular: name) - Filter by multiple group names

**Usage**: Used in repository queries to find groups by ID or name.

---

#### AclUserHasGroupCriteria

**Purpose**: Search criteria for querying user-group associations.

**Properties**:
- `aclUserHasGroupConditions` (AclUserHasGroupConditions, required, strict) - Filter conditions for user-group query

**Usage**: Used to retrieve user-group associations with specific filtering criteria.

---

#### AclUserHasGroupConditions

**Purpose**: Specific filter conditions for user-group association queries.

**Properties**:
- `userIds` (int[], optional, singular: idUser) - Filter by user IDs
- `groupNames` (string[], optional, singular: groupName) - Filter by group names

**Usage**: Embedded in AclUserHasGroupCriteria to define filtering logic.

---

### Collection Transfers

#### UserCollection

**Purpose**: Strict collection wrapper for multiple user entities.

**Properties**:
- `users` (User[], optional, singular: user) - Array of UserTransfer objects

**Transfer Mode**: Strict mode enabled - all properties must be explicitly set.

**Usage**: Used when retrieving filtered collections of users with ACL data.

---

#### AclUserHasGroupCollection

**Purpose**: Collection of user-group associations.

**Properties**:
- `aclUserHasGroups` (AclUserHasGroup[], optional, singular: aclUserHasGroup) - Array of user-group association transfers

**Transfer Mode**: Strict mode enabled.

**Usage**: Result transfer for queries retrieving user-group memberships.

---

#### AclUserHasGroup

**Purpose**: Represents a single user-group association.

**Properties**:
- `user` (User, optional) - User entity in the association
- `group` (Group, optional) - Group entity in the association

**Transfer Mode**: Strict mode enabled.

**Usage**: Links a user to a group, used in collection results.

---

### User Criteria Transfers

#### UserCriteria

**Purpose**: Search criteria for querying users with conditions.

**Properties**:
- `userConditions` (UserConditions, required, strict) - Conditions for user filtering

**Usage**: Used when querying users with ACL-specific criteria.

---

#### UserConditions

**Purpose**: Specific filter conditions for user queries.

**Properties**:
- `usernames` (string[], optional, singular: username) - Filter by usernames

**Transfer Mode**: Strict mode enabled.

**Usage**: Embedded in UserCriteria to define user filtering logic.

---

### Navigation Integration Transfers

#### NavigationItem (Extended)

**Purpose**: Extends ZedNavigation module's NavigationItem with ACL-relevant properties.

**Properties Used by ACL Module**:
- `module` (string, optional) - Bundle/module name for the navigation item
- `controller` (string, optional) - Controller name for the navigation item
- `action` (string, optional) - Action name for the navigation item

**Usage**: ACL uses these properties to check if user has access to the navigation item's target route. Inaccessible items are filtered out.

---

#### NavigationItemCollection (Extended)

**Purpose**: Collection of navigation items.

**Properties**:
- `navigationItems` (NavigationItem[], optional, associative: true, singular: navigationItem) - Associative array of navigation items

**Usage**: Passed to ACL navigation filter plugin. Items without access are removed from collection.

---

### Router Integration Transfers

#### RouterBundleCollection

**Purpose**: Collection of available bundle names from routing configuration.

**Properties**:
- `bundles` (string[], optional, singular: bundle) - Array of bundle names

**Usage**: Used to populate bundle dropdown in ACL rule form. Provides list of all available modules in the system.

---

#### RouterControllerCollection

**Purpose**: Collection of available controller names for a specific bundle.

**Properties**:
- `controllers` (string[], optional, singular: controller) - Array of controller names

**Usage**: Used to populate controller dropdown in ACL rule form after bundle selection.

---

#### RouterActionCollection

**Purpose**: Collection of available action names for a specific controller.

**Properties**:
- `actions` (string[], optional, singular: action) - Array of action method names

**Usage**: Used to populate action dropdown in ACL rule form after controller selection.

---

### External Module Integration Transfers

#### AclEntityRule

**Purpose**: Placeholder transfer for AclEntity module integration.

**Properties**: None defined in ACL module.

**Usage**: Used as a reference type for entity-level ACL rules. Actual properties defined in AclEntity module.

---

#### AclEntityMetadataConfig (Extended)

**Purpose**: Configuration for entity-level ACL metadata.

**Properties Extended by ACL Module**:
- `aclEntityAllowList` (string[], optional, singular: aclEntityAllowListItem) - List of entity class names that bypass entity-level ACL

**Usage**: Used by AclMerchantPortal integration to configure which entities should skip ACL checks.

---

## Transfer Relationships

### Hierarchical Structure
```
User
  └─> aclGroups[] (Group)
        └─> Role (via Group-Role association)
              └─> aclRules[] (Rule)
```

### Access Control Flow
1. **User** has multiple **Groups** (via `aclGroups` property)
2. **Groups** have multiple **Roles** (via many-to-many association)
3. **Roles** have multiple **Rules** (via `aclRules` property)
4. **Rules** define bundle/controller/action permissions

### Criteria Pattern
- **Criteria transfers** (e.g., AclRoleCriteria, GroupCriteria) define search parameters
- **Conditions transfers** (e.g., AclUserHasGroupConditions) define specific filter logic
- **Collection transfers** (e.g., AclUserHasGroupCollection) wrap result sets

### Strict Mode Usage

Transfers with strict mode enabled:
- `UserTransfer::isMerchantAgent` - Must be explicitly set
- `UserCollection` - Entire transfer is strict
- `AclUserHasGroupCollection` - Entire transfer is strict
- `AclUserHasGroup` - Entire transfer is strict
- `AclUserHasGroupCriteria` - Entire transfer is strict
- `AclUserHasGroupConditions` - Entire transfer is strict
- `UserConditions` - Entire transfer is strict

**Strict mode impact**: Properties must be explicitly set using setter methods before the transfer is used. Attempting to access unset strict properties will throw an exception.

## Integration Points

### User Module
- Extends `UserTransfer` with ACL groups
- Provides `UserCriteria` and `UserConditions` for user queries
- Uses `UserCollection` for bulk user operations

### ZedNavigation Module
- Extends `NavigationItem` to check access permissions
- Filters `NavigationItemCollection` to remove inaccessible menu items

### Router Module
- Uses router collection transfers to populate rule form dropdowns
- Provides available bundles, controllers, and actions from routing configuration

### AclEntity Module
- References `AclEntityRule` for entity-level permissions
- Extends `AclEntityMetadataConfig` for entity allowlist configuration

### AclMerchantPortal Module
- Uses ACL transfers for merchant-specific permission logic
- Integrates `isMerchantAgent` flag for conditional access control

## Deprecation Notes

### Deprecated Properties

| Transfer | Property | Reason | Alternative |
|----------|----------|--------|-------------|
| AclRoleCriteria | reference | Unused in current implementation | Use `name` or `names` properties |

**Removal Timeline**: Will be removed in next major release.

## Configuration Keys Reference

The following constants should be configured in project config files (e.g., `config/Shared/config_default.php`):

```php
use Spryker\Shared\Acl\AclConstants;

// Default rules applied during installation
$config[AclConstants::ACL_DEFAULT_RULES] = [
    [
        'bundle' => 'auth',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
    // Additional rules...
];

// Default credentials configuration
$config[AclConstants::ACL_DEFAULT_CREDENTIALS] = [
    // Credentials configuration...
];

// User-specific whitelist (bypasses ACL checks)
$config[AclConstants::ACL_USER_RULE_WHITELIST] = [
    [
        'bundle' => 'health-check',
        'controller' => '*',
        'action' => '*',
    ],
];
```

## Best Practices

### Transfer Usage
- **Always use transfers for data passing** between layers
- **Never pass arrays** where transfers are expected
- **Validate required properties** before using transfers
- **Use strict mode** for critical data integrity

### Rule Definition
- **Use wildcards sparingly** - More specific rules provide better security
- **Follow least privilege principle** - Start with deny, explicitly allow
- **Document rule purpose** - Add comments explaining why rules exist
- **Test rule combinations** - Ensure specific rules override wildcards correctly

### Group Organization
- **Group by role function** (e.g., "editors", "viewers", "admins")
- **Avoid user-specific groups** - Use roles instead
- **Use root_group only for setup** - Don't modify after installation
- **Document group purpose** - Maintain clear group descriptions

## Related Modules

This module's shared layer is used by:

- **Acl Zed Layer** (`src/Spryker/Acl/Zed/`) - Backend business logic for access control
- **User Module** (`src/Spryker/User/`) - User management system that integrates with ACL for permissions
