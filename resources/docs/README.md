# ACL Module

## Overview

The ACL (Access Control List) module provides comprehensive access control functionality for the Spryker Zed Administration Interface. It enables fine-grained permission management through a hierarchical system of roles, groups, privileges, and resources.

The module implements role-based access control (RBAC) patterns, allowing administrators to define who can access which resources and perform which operations within the backend administration interface. It integrates deeply with the Spryker navigation and routing system to enforce access restrictions at multiple levels.

## Application Layers

- [Zed Layer](./ZED.md) - Backend business logic and administration
- [Shared Layer](./SHARED.md) - Shared code and transfers

**Not present in this module**: Yves Layer, Client Layer, Glue Layer, Service Layer

## Architecture Summary

### Extension Points

- **AclExtension** - Plugin interfaces for extending ACL functionality
- **AclMerchantPortalExtension** - Merchant Portal specific ACL extensions
- **EventDispatcherExtension** - Event-based integration points
- **ZedNavigationExtension** - Navigation menu access control

### Key Features

- Role-based access control for Zed administrators
- Group management for organizing users
- Rule-based privilege definitions
- Resource-level access restrictions
- Bundle/Controller/Action level permissions
- Integration with Spryker navigation system
- Automatic route access validation
- Database-backed ACL configuration
- Extensible through plugin architecture
