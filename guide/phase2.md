Phase 2 focuses on Core Business Modules of the SaaS Multi-Tenant Digital Loan System, built on top of the tenant-aware modular monolith foundation from Phase 1.

No code. Developer-ready. Structured. Best-practice aligned.

Phase 2 Implementation Prompt
Core Business Modules – Multi-Tenant Loan SaaS
Objective

Implement the complete loan lifecycle system inside the existing modular monolith architecture.
All modules must remain tenant-aware, role-protected, event-driven internally, and structured for future microservice extraction.

Scope of Phase 2

Implement the following modules fully:

Borrower Management

Loan Management

Loan Product Configuration

Loan Workflow Engine

Transaction Management

Repayment & Payment Processing (Dynamic Gateway Ready)

Notification System

Reporting (Basic Operational Reports)

1️⃣ Loan Product Configuration Module
Purpose

Allow each tenant to define their own loan products.

Requirements

Each tenant must be able to configure:

Loan name

Interest calculation type (flat / reducing balance)

Interest rate

Loan term (days, weeks, months)

Processing fee (fixed or percentage)

Late fee rules

Grace period

Repayment frequency

Minimum & maximum loan amount

Penalty rules

Implementation Rules

Must be fully tenant-isolated

Configuration stored per tenant

Validation logic in Service layer

Product cannot be deleted if loans exist

Only tenant_admin can create/edit

2️⃣ Borrower Management Module
Purpose

Manage borrowers per tenant.

Requirements

Each borrower must include:

Full name

Phone number

Email

ID number

Status (active, blacklisted)

Optional scoring metadata

Rules

Borrower must belong to one tenant only

Unique phone/email per tenant

Blacklisted borrower cannot apply for new loan

All actions logged

3️⃣ Loan Management Module
Purpose

Handle full loan lifecycle.

Loan Lifecycle States

Draft

Pending Approval

Approved

Rejected

Disbursed

Active

Overdue

Closed

Defaulted

Core Functionalities

Create loan linked to borrower + product

Calculate schedule automatically

Approve/reject loan

Disburse loan

Auto-generate repayment schedule

Mark overdue automatically

Close loan when fully paid

Rules

Only staff or tenant_admin can approve

Approval must trigger event

Disbursement must trigger transaction record

All amounts must be calculated via service layer

No business logic in controllers

4️⃣ Loan Workflow Engine
Purpose

Allow flexible workflow configuration per tenant.

Requirements

Define approval stages

Configure approval hierarchy

Optional multi-level approval

Define automatic vs manual approval

Rules

Workflow config stored per tenant

Loan moves through workflow states

Each transition emits event

Logs must be stored

5️⃣ Transaction Management Module
Purpose

Central financial ledger per tenant.

Transaction Types

Disbursement

Repayment

Fee charge

Penalty

Adjustment

Requirements

All monetary changes recorded

Linked to loan and borrower

Immutable records

Soft delete disabled

Timestamped with audit trail

6️⃣ Repayment & Dynamic Payment Gateway Module
Purpose

Support dynamic integration of payment providers.

Gateways may include:

Pesapal

AzamPesa

Selcom

Digicash

Others in future

Architecture Requirement

Use strategy pattern

Each gateway implemented as a service class

Tenant can configure:

Secret keys

Public keys

Endpoints

Callback URLs

Rules

Gateway credentials stored encrypted

Gateway selection per tenant

Payment callback must validate signature

Repayment automatically reconciles loan

Failed payments logged

7️⃣ Notification Module
Purpose

Send notifications based on events.

Channels

SMS

Email

Push (future ready)

Trigger Events

Loan approved

Loan rejected

Loan disbursed

Payment received

Loan overdue

Rules

Must use event-driven approach

Queue notifications

Tenant can configure provider keys

Retry failed notifications

8️⃣ Reporting Module (Operational)
Reports Required

Total loans per tenant

Active loans

Overdue loans

Disbursement totals

Repayment totals

Revenue from interest and penalties

Rules

Reports must be tenant-isolated

Aggregations done via optimized queries

No cross-tenant aggregation unless owner role

9️⃣ Events & Internal Communication

All modules must communicate via domain events.

Example Event Flow:

LoanApproved →
Trigger Notification →
Trigger Disbursement →
Trigger Transaction Record →
Trigger Reporting Update

Rules:

No module directly calling another module’s controller

Use service contracts or events

All events logged

🔟 Security Requirements

All endpoints authenticated

Role-based middleware enforced

Tenant context required for every request

Encrypt payment credentials

Validate all monetary calculations

Prevent cross-tenant access strictly

11️⃣ Testing Requirements

Must include:

Loan lifecycle test

Multi-tenant isolation test

Payment gateway mock test

Notification queue test

Transaction integrity test

Reporting accuracy test

Deliverables of Phase 2

Fully functional loan lifecycle

Tenant-configurable loan products

Dynamic payment gateway architecture

Event-driven internal communication

Central transaction ledger

Notification system

Operational reporting

Fully tenant-isolated data

End State After Phase 2

You will have:

✔ A production-ready multi-tenant loan engine
✔ Configurable products per tenant
✔ Dynamic payment integrations
✔ Event-driven modular architecture
✔ Clean separation of concerns
✔ Foundation ready for scaling or microservices extraction