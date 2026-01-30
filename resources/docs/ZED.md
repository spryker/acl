# ACL Module - Zed Layer Documentation

## Configuration

**Location**: `src/Spryker/Zed/Acl/AclConfig.php`

### Public Methods

| Method Name | Return Type | Description |
|-------------|-------------|-------------|
| getRules() | array | Returns ACL rules configuration. Merges default rules from `AclConstants::ACL_DEFAULT_RULES` with custom rules set via `setRules()`. |
| setRules(string $bundle, string $controller, string $action, string $type) | void | Adds a custom ACL rule to the configuration. Parameters define bundle, controller, action, and type (allow/deny). |
| getCredentials() | array | Returns default credentials configuration from `AclConstants::ACL_DEFAULT_CREDENTIALS`. |
| getAccessDeniedUri() | string | Returns URI for access denied page. Default: '/acl/index/denied' |
| getInstallerRules() | array<array<string, mixed>> | Returns rules to be installed during setup. Includes wildcard rule for root role granting full access. |
| getInstallerRoles() | array<int, array<string, mixed>> | Returns roles to be installed during setup. Includes root role configuration. |
| getInstallerGroups() | array<array> | Returns groups to be installed during setup. Includes root group with name and reference. |
| getInstallerUsers() | array<string, array<string, mixed>> | Returns users to be assigned to groups during setup. Default: 'admin@spryker.com' assigned to root group. |
| getUserRuleWhitelist() | array<array> | Returns user rule whitelist from configuration `AclConstants::ACL_USER_RULE_WHITELIST`. Returns empty array if not configured. |

## Dependency Provider

**Location**: `src/Spryker/Zed/Acl/AclDependencyProvider.php`

### Dependency Injection Points

| Method Name | Return Type | Purpose | Required |
|-------------|-------------|---------|----------|
| addFacadeUser(Container) | Container | Provides User facade for user management operations | Yes |
| addRouterFacade(Container) | Container | Provides Router facade for routing integration | Yes |
| addAclQueryContainer(Container) | Container | Provides ACL query container (deprecated, use Repository/EntityManager) | No |
| addAclInstallerPlugins(Container) | Container | Provides ACL installer plugins stack | No |
| addAclRolesExpanderPlugins(Container) | Container | Provides plugins to expand ACL roles | No |
| addAclRolePostSavePlugins(Container) | Container | Provides plugins executed after role save operations | No |
| addAclAccessCheckerStrategyPlugins(Container) | Container | Provides plugins for custom access checking strategies | No |
| getAclInstallerPlugins() | array<AclInstallerPluginInterface> | Returns installer plugin implementations. Default: empty array. Projects can override to add custom installation logic. | No |
| getAclRolesExpanderPlugins() | array<AclRolesExpanderPluginInterface> | Returns role expander plugin implementations. Default: empty array. Projects can override to modify roles before saving. | No |
| getAclRolePostSavePlugins() | array<AclRolePostSavePluginInterface> | Returns post-save plugin implementations. Default: empty array. Projects can override for post-processing after role save. | No |
| getAclAccessCheckerStrategyPlugins() | array<AclAccessCheckerStrategyPluginInterface> | Returns access checker strategy plugin implementations. Default: empty array. Projects can override to implement custom access control logic. | No |

### Constants

| Constant | Value | Purpose |
|----------|-------|---------|
| FACADE_USER | 'user facade' | Dependency key for User facade |
| FACADE_ACL | 'acl facade' | Dependency key for ACL facade |
| FACADE_ROUTER | 'FACADE_ROUTER' | Dependency key for Router facade |
| QUERY_CONTAINER_USER | 'user query container' | Dependency key for User query container |
| QUERY_CONTAINER_ACL | 'acl query container' | Dependency key for ACL query container (deprecated) |
| SERVICE_DATE_FORMATTER | 'date formatter service' | Dependency key for date formatter service |
| ACL_INSTALLER_PLUGINS | 'ACL_INSTALLER_PLUGINS' | Dependency key for installer plugins |
| PLUGINS_ACL_ROLES_EXPANDER | 'PLUGINS_ACL_ROLES_EXPANDER' | Dependency key for role expander plugins |
| PLUGINS_ACL_ROLE_POST_SAVE | 'PLUGINS_ACL_ROLE_POST_SAVE' | Dependency key for role post-save plugins |
| PLUGINS_ACL_ACCESS_CHECKER_STRATEGY | 'PLUGINS_ACL_ACCESS_CHECKER_STRATEGY' | Dependency key for access checker strategy plugins |

## Business Layer

### Facade

**Location**: `src/Spryker/Zed/Acl/Business/AclFacade.php`

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| install() | - | void | Installs ACL system with initial data (groups, roles, users, rules) from configuration. |
| addGroup(string $groupName, RolesTransfer $rolesTransfer) | string, RolesTransfer | GroupTransfer | **Deprecated**: Creates group and assigns roles. Use `createGroup()` instead. |
| createGroup(GroupTransfer $groupTransfer, RolesTransfer $rolesTransfer) | GroupTransfer, RolesTransfer | GroupTransfer | Creates new ACL group with provided data and assigns roles. Returns created group with generated ID. |
| updateGroup(GroupTransfer $transfer, RolesTransfer $rolesTransfer) | GroupTransfer, RolesTransfer | GroupTransfer | Updates existing group data and reassigns roles. Returns updated group transfer. |
| getGroup(int $id) | int | GroupTransfer | Retrieves group by ID. Throws exception if not found. |
| getGroupByName(string $name) | string | GroupTransfer | Retrieves group by name. Throws exception if not found. |
| findGroup(GroupCriteriaTransfer $groupCriteriaTransfer) | GroupCriteriaTransfer | GroupTransfer\|null | Finds group by criteria. Returns null if not found (safe version of getGroup). |
| getAllGroups() | - | GroupsTransfer | Retrieves all ACL groups from database. |
| removeGroup(int $idGroup) | int | bool | Deletes group by ID. Cascade deletes group-role and user-group associations. Returns success status. |
| hasGroupByName(string $name) | string | bool | Checks if group with given name exists. |
| hasCurrentUser() | - | bool | Checks if there is a currently logged-in user in session. Delegates to User module. |
| getCurrentUser() | - | UserTransfer | Retrieves currently logged-in user from session. Delegates to User module. |
| addRole(string $name) | string | RoleTransfer | **Deprecated**: Creates role with name. Use `createRole()` instead. |
| createRole(RoleTransfer $roleTransfer) | RoleTransfer | RoleTransfer | Creates new ACL role. Validates uniqueness, triggers post-save plugins. Returns created role with generated ID. |
| updateRole(RoleTransfer $roleTransfer) | RoleTransfer | RoleTransfer | Updates existing role. Returns updated role transfer. |
| getRoleById(int $id) | int | RoleTransfer | Retrieves role by ID. Throws exception if not found. |
| findRoleById(int $id) | int | RoleTransfer\|null | Finds role by ID. Returns null if not found (safe version of getRoleById). |
| getRoleByName(string $name) | string | RoleTransfer | Retrieves role by name. Throws exception if not found. |
| existsRoleByName(string $name) | string | bool | Checks if role with given name exists. |
| removeRole(int $idRole) | int | bool | Deletes role by ID. Cascade deletes associated rules and group-role associations. Returns success status. |
| getRule(int $id) | int | RuleTransfer | Retrieves rule by ID. Throws exception if not found. |
| addRule(RuleTransfer $ruleTransfer) | RuleTransfer | RuleTransfer | Creates new ACL rule (bundle/controller/action permission). Validates and persists rule. |
| removeRule(int $idRule) | int | bool | Deletes rule by ID. Returns success status. |
| existsRoleRule(int $idAclRole, string $bundle, string $controller, string $action, string $type) | int, string, string, string, string | bool | Checks if specific rule already exists for role with given bundle/controller/action/type combination. |
| getGroupRoles(int $idGroup) | int | RolesTransfer | Retrieves all roles assigned to a group. |
| getGroupRules(int $idGroup) | int | RulesTransfer | Retrieves all rules for a group (aggregated from all group roles). |
| getRoleRules(int $idRole) | int | RulesTransfer | Retrieves all rules assigned to a specific role. |
| addUserToGroup(int $idUser, int $idGroup) | int, int | int | Adds user to group. Returns ID of created user-group association. Throws exception if already associated. |
| removeUserFromGroup(int $idUser, int $idGroup) | int, int | void | Removes user from group. Deletes user-group association. |
| userHasGroupId(int $idUser, int $idGroup) | int, int | bool | Checks if user is member of specified group. |
| getUserGroups(int $idUser) | int | GroupsTransfer | Retrieves all groups that user is member of. |
| getUserRoles(int $idUser) | int | RolesTransfer | Retrieves all roles assigned to user (aggregated from all user groups). |
| addRoleToGroup(int $idRole, int $idGroup) | int, int | int | Adds role to group. Returns ID of created group-role association. |
| addRolesToGroup(GroupTransfer $groupTransfer, RolesTransfer $rolesTransfer) | GroupTransfer, RolesTransfer | void | Bulk adds multiple roles to group. Iterates through roles transfer and creates associations. |
| checkAccess(UserTransfer $user, string $bundle, string $controller, string $action) | UserTransfer, string, string, string | bool | Checks if user has access to specific bundle/controller/action. Core access control method. Returns true if allowed. |
| isIgnorable(string $bundle, string $controller, string $action) | string, string, string | bool | Checks if bundle/controller/action should bypass ACL checks (whitelisted). |
| filterNavigationItemCollectionByAccessibility(NavigationItemCollectionTransfer $navigationItemCollectionTransfer) | NavigationItemCollectionTransfer | NavigationItemCollectionTransfer | Filters navigation items based on current user's access rights. Removes inaccessible menu items. |
| getAclUserHasGroupCollection(AclUserHasGroupCriteriaTransfer $aclUserHasGroupCriteriaTransfer) | AclUserHasGroupCriteriaTransfer | AclUserHasGroupCollectionTransfer | Retrieves user-group associations filtered by criteria (group names, user IDs). |

### Model Classes

**Location**: `src/Spryker/Zed/Acl/Business/`

| Class Name | Responsibility | Implements | Pattern |
|------------|----------------|------------|---------|
| Model/Group | Manages group CRUD operations, user-group associations, and group-role assignments | GroupInterface | Model |
| Model/Role | Manages role CRUD operations, retrieves user/group roles | RoleInterface | Model |
| Model/Rule | Manages rule CRUD operations, performs access checks, validates permissions | RuleInterface | Model |
| Model/RuleValidator | Validates ACL rules against configured patterns and user whitelist | RuleValidatorInterface | Validator |
| Model/Installer | Installs initial ACL data (groups, roles, users, rules) during setup | InstallerInterface | Installer |
| Writer/GroupWriter | Persists new group entities using EntityManager | GroupWriterInterface | Writer |
| Writer/RoleWriter | Persists new role entities, triggers post-save plugins | RoleWriterInterface | Writer |
| Filter/NavigationItemFilter | Filters navigation menu items based on user access permissions | NavigationItemFilterInterface | Filter |
| Acl/AclConfigReader | Reads and processes ACL configuration from config | AclConfigReaderInterface | Reader |

### Exception Classes

**Location**: `src/Spryker/Zed/Acl/Business/Exception/`

| Exception Class | Usage |
|-----------------|-------|
| EmptyEntityException | Thrown when required entity data is missing |
| GroupAlreadyHasRoleException | Thrown when attempting to add role to group that already has it |
| GroupAlreadyHasUserException | Thrown when attempting to add user to group that already has them |
| GroupExistsException | Thrown when attempting to create duplicate group |
| GroupNameExistsException | Thrown when group name already exists |
| GroupNotFoundException | Thrown when requested group is not found |
| RoleExistsException | Thrown when attempting to create duplicate role |
| RoleNameEmptyException | Thrown when role name is empty |
| RoleNameExistsException | Thrown when role name already exists |
| RoleNotFoundException | Thrown when requested role is not found |
| RoleNotSavedException | Thrown when role save operation fails |
| RootNodeModificationException | Thrown when attempting to modify protected root group/role |
| RuleNotFoundException | Thrown when requested rule is not found |
| TransferNotFoundException | Thrown when required transfer object is missing |
| UserAndGroupNotFoundException | Thrown when user-group association is not found |

## Communication Layer

### Controllers

**Location**: `src/Spryker/Zed/Acl/Communication/Controller/`

| Controller | Action Method | Route Pattern | Description |
|------------|---------------|---------------|-------------|
| IndexController | indexAction() | /acl/ | Main ACL index page. Redirects to groups or roles listing. |
| IndexController | deniedAction() | /acl/index/denied | Access denied error page. Displayed when user lacks permissions. |
| GroupController | indexAction() | /acl/group/ | Lists all ACL groups in data table. |
| GroupController | tableAction() | /acl/group/table | AJAX endpoint for group data table pagination/sorting. |
| GroupController | addAction(Request) | /acl/group/add | Shows group creation form. POST: validates and creates group with roles. |
| GroupController | editAction(Request) | /acl/group/edit | Shows group edit form. POST: validates and updates group data and roles. |
| GroupController | usersAction(Request) | /acl/group/users | Shows users assigned to group in data table. |
| GroupController | deleteUserFromGroupAction(Request) | /acl/group/delete-user-from-group | AJAX endpoint to remove user from group. |
| GroupController | rolesAction(Request) | /acl/group/roles | AJAX endpoint returning roles data for group. |
| RoleController | indexAction() | /acl/role/ | Lists all ACL roles in data table. |
| RoleController | tableAction() | /acl/role/table | AJAX endpoint for role data table pagination/sorting. |
| RoleController | createAction(Request) | /acl/role/create | Shows role creation form. POST: validates and creates role. |
| RoleController | updateAction(Request) | /acl/role/update | Shows role edit form with rules. POST: validates and updates role. |
| RoleController | deleteAction(Request) | /acl/role/delete | Shows role deletion confirmation. POST: deletes role and cascade deletes rules. |
| RoleController | rulesetTableAction(Request) | /acl/role/ruleset-table | AJAX endpoint for role rules data table. |
| RulesetController | deleteAction(Request) | /acl/ruleset/delete | AJAX endpoint to delete individual rule from role. |
| RulesController | controllerChoicesAction(Request) | /acl/rules/controller-choices | AJAX endpoint returning available controllers for selected bundle (for rule form). |
| RulesController | actionChoicesAction(Request) | /acl/rules/action-choices | AJAX endpoint returning available actions for selected controller (for rule form). |

### Plugins Provided to Other Modules

**Location**: `src/Spryker/Zed/Acl/Communication/Plugin/`

#### For EventDispatcher Module

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| EventDispatcher/AccessControlEventDispatcherPlugin | EventDispatcherPluginInterface | extend(EventDispatcherInterface): EventDispatcherInterface | Registers ACL access control event subscriber. Listens to kernel controller events and validates user access before controller execution. | EventDispatcherDependencyProvider::getEventDispatcherPlugins() |

#### For ZedNavigation Module

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| Navigation/AclNavigationItemFilterPlugin | NavigationItemFilterPluginInterface | filter(NavigationItemTransfer): bool | **Deprecated**: Filters single navigation item based on user access. Use AclNavigationItemCollectionFilterPlugin instead. | ZedNavigationDependencyProvider::getNavigationItemFilterPlugins() |
| Navigation/AclNavigationItemCollectionFilterPlugin | NavigationItemCollectionFilterPluginInterface | filter(NavigationItemCollectionTransfer): NavigationItemCollectionTransfer | Filters navigation item collection. Removes menu items user lacks access to. Recommended approach. | ZedNavigationDependencyProvider::getNavigationItemCollectionFilterPlugins() |

#### For AclMerchantPortal Module

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| AclMerchantPortal/AclEntityConfigurationExpanderPlugin | AclEntityConfigurationExpanderPluginInterface | expand(AclEntityMetadataConfigTransfer): AclEntityMetadataConfigTransfer | Expands ACL entity configuration for Merchant Portal. Adds ACL-specific entity metadata. | AclMerchantPortalDependencyProvider::getAclEntityConfigurationExpanderPlugins() |

#### For User Module

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| GroupPlugin | GroupPluginInterface | getGroups(): GroupsTransfer | Provides all ACL groups to User module for user management interface. | UserDependencyProvider::getGroupPlugin() |

#### For Installer Module

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| AclInstallerPlugin | InstallerPluginInterface | install(): void | Installs initial ACL data during system setup. Creates root group, root role, and assigns admin user. | InstallerDependencyProvider::getInstallerPlugins() |

#### Deprecated - For Bootstrap (Legacy)

| Plugin Class | Interface | Public Methods | Description | Registration |
|--------------|-----------|----------------|-------------|--------------|
| Bootstrap/AclBootstrapProvider | ServiceProviderInterface | register(Application): void; boot(Application): void | **Deprecated**: Legacy Silex service provider for ACL. Use AccessControlEventDispatcherPlugin instead. | Application::getServiceProviders() |

### Forms

**Location**: `src/Spryker/Zed/Acl/Communication/Form/`

| Form Class | Used By Controller | Purpose | Key Fields |
|------------|-------------------|---------|------------|
| GroupForm | GroupController::addAction, editAction | ACL group creation/editing | name (text), idAclGroup (hidden), roles (choice) |
| RoleForm | RoleController::createAction, updateAction | ACL role creation/editing with rules | name (text), aclRules (collection of RuleForm) |
| RuleForm | RoleForm (embedded collection) | Individual ACL rule definition | bundle (text), controller (text), action (text), type (choice: allow/deny) |
| DeleteRoleForm | RoleController::deleteAction | Role deletion confirmation | idAclRole (hidden) |

### Tables

**Location**: `src/Spryker/Zed/Acl/Communication/Table/`

| Table Class | Used By Controller | Purpose | Key Columns |
|-------------|-------------------|---------|-------------|
| GroupTable | GroupController::indexAction, tableAction | Lists all ACL groups with pagination | ID, Name, Roles Count, Users Count, Actions (Edit, Users) |
| GroupUsersTable | GroupController::usersAction | Lists users assigned to specific group | ID, Username, First Name, Last Name, Status, Actions (Remove from Group) |
| RoleTable | RoleController::indexAction, tableAction | Lists all ACL roles with pagination | ID, Name, Rules Count, Actions (Edit, Delete) |
| RulesetTable | RoleController::rulesetTableAction | Lists rules assigned to specific role | ID, Bundle, Controller, Action, Type (Allow/Deny), Actions (Delete) |

### Console Commands

**Location**: `src/Spryker/Zed/Acl/Communication/Console/`

**Status**: No console commands are present in this module.

## Persistence Layer

### Repository

**Location**: `src/Spryker/Zed/Acl/Persistence/AclRepository.php`

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| findGroup(GroupCriteriaTransfer) | GroupCriteriaTransfer | GroupTransfer\|null | Finds group by criteria (ID, name, etc.). Returns null if not found. Uses mapper to transform entity to transfer. |
| findRole(AclRoleCriteriaTransfer) | AclRoleCriteriaTransfer | RoleTransfer\|null | Finds role by criteria (ID, name, etc.). Returns null if not found. Uses mapper to transform entity to transfer. |
| getAclUserHasGroupCollection(AclUserHasGroupCriteriaTransfer) | AclUserHasGroupCriteriaTransfer | AclUserHasGroupCollectionTransfer | Retrieves user-group associations filtered by criteria. Supports filtering by group names and user IDs. Returns collection of associations. |

### Entity Manager

**Location**: `src/Spryker/Zed/Acl/Persistence/AclEntityManager.php`

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| createGroup(GroupTransfer) | GroupTransfer | GroupTransfer | Creates new ACL group entity. Validates that name is provided. Persists to database. Returns transfer with generated ID. |
| createRole(RoleTransfer) | RoleTransfer | RoleTransfer | Creates new ACL role entity. Validates that name is provided. Persists to database. Returns transfer with generated ID. |

### Query Container (Legacy)

**Location**: `src/Spryker/Zed/Acl/Persistence/AclQueryContainer.php`

**Note**: Query Container is a legacy pattern. New code should use Repository and EntityManager instead.

The Query Container provides query methods for complex ACL queries including:
- Group queries by name, with role associations, with user associations
- Role queries by name, with rule associations
- Rule queries by role, by group
- User queries with group and role joins
- System user queries for specific user lookups

Key constants used in queries:
- `ROLE_NAME` - Alias for role name column
- `TYPE` - Alias for rule type (allow/deny)
- `BUNDLE`, `CONTROLLER`, `ACTION` - Aliases for rule resource columns
- `HAS_ROLE` - Alias for role existence check
- `GROUP_NAME` - Alias for group name column

### Mapper

**Location**: `src/Spryker/Zed/Acl/Persistence/Propel/Mapper/AclMapper.php`

| Mapper Class | Transformation | Description |
|--------------|----------------|-------------|
| AclMapper | Entity ↔ Transfer | Bidirectional transformation between Propel entities (SpyAclGroup, SpyAclRole, SpyAclUserHasGroup) and transfer objects (GroupTransfer, RoleTransfer, AclUserHasGroupTransfer, AclUserHasGroupCollectionTransfer). Handles both single objects and collections. |

### Propel Schema

**Location**: `src/Spryker/Zed/Acl/Persistence/Propel/Schema/`

#### spy_acl.schema.xml

**Tables Defined**: `spy_acl_role`, `spy_acl_rule`, `spy_acl_group`, `spy_acl_user_has_group`, `spy_acl_groups_has_roles`

**Event Behaviors**: None. Only `timestampable` and `archivable` behaviors are used.

**Table: spy_acl_role**

| Column | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| id_acl_role | INTEGER | Yes | Auto-increment | Primary key |
| name | VARCHAR(255) | Yes | - | Role name, unique constraint |

**Behaviors**: `timestampable` (adds created_at, updated_at), `archivable` (enables soft delete)

**Table: spy_acl_rule**

| Column | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| id_acl_rule | INTEGER | Yes | Auto-increment | Primary key |
| fk_acl_role | INTEGER | Yes | - | Foreign key to spy_acl_role |
| bundle | VARCHAR(60) | Yes | - | Spryker bundle name (module) |
| controller | VARCHAR(60) | Yes | - | Controller name |
| action | VARCHAR(45) | Yes | - | Action method name |
| type | ENUM('allow', 'deny') | Yes | - | Permission type |

**Foreign Keys**:
- `fk_acl_role` → `spy_acl_role.id_acl_role` (CASCADE on delete)

**Behaviors**: `timestampable`, `archivable`

**Table: spy_acl_group**

| Column | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| id_acl_group | INTEGER | Yes | Auto-increment | Primary key |
| name | VARCHAR(255) | Yes | - | Group name, unique constraint |

**Behaviors**: `timestampable`, `archivable`

**Table: spy_acl_user_has_group** (Cross-reference table)

| Column | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| fk_user | INTEGER | Yes | - | Primary key, foreign key to spy_user |
| fk_acl_group | INTEGER | Yes | - | Primary key, foreign key to spy_acl_group |

**Composite Primary Key**: (`fk_user`, `fk_acl_group`)

**Foreign Keys**:
- `fk_user` → `spy_user.id_user` (CASCADE on delete)
- `fk_acl_group` → `spy_acl_group.id_acl_group` (CASCADE on delete)

**Table: spy_acl_groups_has_roles** (Cross-reference table)

| Column | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| fk_acl_role | INTEGER | Yes | - | Primary key, foreign key to spy_acl_role |
| fk_acl_group | INTEGER | Yes | - | Primary key, foreign key to spy_acl_group |

**Composite Primary Key**: (`fk_acl_role`, `fk_acl_group`)

**Foreign Keys**:
- `fk_acl_role` → `spy_acl_role.id_acl_role` (CASCADE on delete)
- `fk_acl_group` → `spy_acl_group.id_acl_group` (CASCADE on delete)

**Schema Architecture Notes**:
- Establishes hierarchical ACL structure: Users → Groups → Roles → Rules
- All foreign key relationships use CASCADE delete for referential integrity
- Junction tables manage many-to-many relationships (user-to-group, group-to-role)
- Timestampable behavior tracks creation and modification timestamps
- Archivable behavior enables soft deletes (records moved to archive tables)
- No event behaviors defined - ACL changes do not publish events

## Presentation Layer

**Location**: `src/Spryker/Zed/Acl/Presentation/`

| Template Path | Controller::Action | Purpose | Extends | Key Variables |
|---------------|-------------------|---------|---------|---------------|
| Group/index.twig | GroupController::indexAction | ACL groups list view with data table | @Gui/Layout/layout.twig | groupsTable (GroupTable) |
| Group/add.twig | GroupController::addAction | Group creation form | @Gui/Layout/layout.twig | form (GroupForm), availableRoles (array) |
| Group/edit.twig | GroupController::editAction | Group edit form with role assignment | @Gui/Layout/layout.twig | form (GroupForm), usersTable (GroupUsersTable), idGroup (int), currentGroupRoles (array) |
| Role/index.twig | RoleController::indexAction | ACL roles list view with data table | @Gui/Layout/layout.twig | rolesTable (RoleTable) |
| Role/create.twig | RoleController::createAction | Role creation form with rules | @Gui/Layout/layout.twig | form (RoleForm) |
| Role/update.twig | RoleController::updateAction | Role edit form with rules management | @Gui/Layout/layout.twig | form (RoleForm), idRole (int), rulesTable (RulesetTable) |
| Index/denied.twig | IndexController::deniedAction | Access denied error page | @Gui/Layout/layout.twig | None (static error page) |

**Template Inheritance**: All templates extend `@Gui/Layout/layout.twig`, which provides the standard Zed administration interface layout with navigation, header, and styling.

**JavaScript Components**: Templates include embedded JavaScript for:
- AJAX form submissions (rule creation, user removal)
- Dynamic form field population (controller/action dropdowns based on bundle selection)
- Data table pagination and filtering
- Modal dialogs for confirmations

## Extension Points

### Plugin Interfaces from AclExtension Module

Projects can implement these interfaces to extend ACL functionality:

| Interface | Purpose | Method Signature |
|-----------|---------|------------------|
| AclInstallerPluginInterface | Custom installation logic | install(): void |
| AclRolesExpanderPluginInterface | Modify roles before/after retrieval | expand(RolesTransfer): RolesTransfer |
| AclRolePostSavePluginInterface | Post-processing after role save | execute(RoleTransfer): RoleTransfer |
| AclAccessCheckerStrategyPluginInterface | Custom access control logic | isAccessible(UserTransfer, string $bundle, string $controller, string $action): bool |

### Plugin Interfaces from AclMerchantPortalExtension Module

| Interface | Purpose | Method Signature |
|-----------|---------|------------------|
| AclEntityConfigurationExpanderPluginInterface | Extend Merchant Portal ACL configuration | expand(AclEntityMetadataConfigTransfer): AclEntityMetadataConfigTransfer |

## Configuration Options

### Environment Variables Referenced

| Config Key | Type | Description | Default |
|------------|------|-------------|---------|
| AclConstants::ACL_DEFAULT_RULES | array | Default ACL rules applied to all installations | Must be configured in project config |
| AclConstants::ACL_DEFAULT_CREDENTIALS | array | Default credentials configuration | Must be configured in project config |
| AclConstants::ACL_USER_RULE_WHITELIST | array | User-specific rules that bypass ACL checks | Empty array |

### Constants from Shared Layer

| Constant | Value | Usage |
|----------|-------|-------|
| AclConstants::VALIDATOR_WILDCARD | '*' | Wildcard value for bundle/controller/action matching |
| AclConstants::ALLOW | 'allow' | Rule type for allowing access |
| AclConstants::DENY | 'deny' | Rule type for denying access |
| AclConstants::ROOT_ROLE | 'root_role' | Name of root administrator role |
| AclConstants::ROOT_GROUP | 'root_group' | Name of root administrator group |

## Access Control Flow

### Request Processing

1. **Event Listener Registration**: `AccessControlEventDispatcherPlugin` registers kernel controller event listener
2. **Before Controller Execution**: Event listener intercepts request before controller action
3. **User Validation**: Check if user is logged in via `hasCurrentUser()`
4. **Ignorable Check**: Verify if route is whitelisted via `isIgnorable(bundle, controller, action)`
5. **Access Check**: Validate user permissions via `checkAccess(user, bundle, controller, action)`
6. **Rule Resolution**:
   - Retrieve user groups
   - Retrieve group roles
   - Retrieve role rules
   - Match rules against requested bundle/controller/action
   - Apply rule hierarchy (specific rules override wildcard rules)
7. **Access Decision**:
   - If "allow" rule matches: grant access
   - If "deny" rule matches: deny access
   - If no rule matches: deny access by default
8. **Redirect on Deny**: Redirect to `/acl/index/denied` if access denied

### Navigation Filtering

1. **Navigation Build**: Zed navigation collects all menu items
2. **Filter Plugin Execution**: `AclNavigationItemCollectionFilterPlugin` processes collection
3. **Access Validation**: For each navigation item, check user access to target route
4. **Item Removal**: Remove inaccessible items from navigation tree
5. **Render Filtered Menu**: Display only accessible navigation items to user

## Related Modules

This module integrates with the following modules:

- **User** (`src/Spryker/User/`) - User management and authentication, provides user data for ACL checks
- **Router** (`src/Spryker/Router/`) - Routing system integration, provides route information for access control
- **Navigation** (`src/Spryker/Navigation/`) - Menu access control, filters navigation items based on user permissions
