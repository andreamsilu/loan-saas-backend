Phase 1 Implementation Prompt – SaaS Multi-Tenant Backend (Developer-Ready)
Objective:

Implement the foundation of a modular monolithic SaaS multi-tenant loan system in Laravel, ensuring tenant isolation, RBAC, authentication, and dynamic folder creation as the system grows.

Tasks

Project Initialization

Set up Laravel project with proper environment configuration.

Ensure database connection to MySQL is ready.

Install Laravel Sanctum for API authentication.

Set up Git repository with main and development branches.

Dynamic Modular Folder Structure

Create app/Modules and app/Shared folders if they don’t exist.

For each module (Tenant, User, Borrower, Loan, Transaction, Payment, Notification, Billing, Reporting):

Create subfolders: Controllers, Models, Services, Events, Listeners, Routes.

For Shared utilities, create folders: Traits, Helpers, Enums, Contracts.

Always check if folder exists before creating dynamically during implementation.

Multi-Tenant Setup

Implement tenant-aware architecture using a Tenant model.

All business tables (Borrowers, Loans, Transactions) must include tenant_id.

Implement middleware to resolve tenant from subdomain, API header, or custom domain.

Store tenant context globally for use in models, services, and events.

Models with Tenant Isolation

Implement global tenant scope to ensure queries are filtered by tenant_id.

Ensure any query automatically respects tenant isolation without extra developer effort.

Authentication and Role-Based Access Control

Use Laravel Sanctum for API authentication.

Define roles: owner, tenant_admin, staff, borrower.

Implement middleware to enforce role permissions on routes.

Protect all tenant-specific routes with authentication + RBAC middleware.

Routing

Each module should have its own routes file.

Dynamically load all module routes in central routes/api.php.

Ensure routes are tenant-aware and protected by middleware.

Seeding and Testing

Seed multiple tenants for testing purposes.

Seed sample users per tenant with roles assigned.

Verify tenant isolation through CRUD operations.

Write tests to ensure no tenant can access another tenant’s data.

Best Practices

Modules must be self-contained and communicate only via events or service contracts.

Controllers handle HTTP; services handle business logic.

Encrypt all tenant secrets and API keys.

Follow PSR-12 coding standards.

All dynamic folders and modules should be created on-the-fly during implementation.

Keep everything documented for maintainability and future microservices extraction.

Deliverables

Dynamic modular folder structure for all modules and shared utilities.

Tenant model, middleware, and global scope applied.

Authentication with roles and RBAC fully implemented.

Seeded tenants and sample users tested.

Fully tenant-aware system ready for Phase 2: Core Business Modules.