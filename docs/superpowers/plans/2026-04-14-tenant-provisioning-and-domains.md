# Station Tenant Provisioning and Domain Plan

## Goal

Define how Station should handle:

- internal demo tenants
- paid self-serve tenant provisioning
- tenant lifecycle actions in `/platform`
- platform-owned subdomains
- customer custom domains

This plan is intended as a product + implementation guide for the next Station architecture phase after the Laravel web app scaffold.

## Provisioning entry points

Station should have one tenant provisioning system with multiple operational entry points, not separate provisioning logic per use case.

There are three ongoing provisioning paths plus one one-time bootstrap case.

### Bootstrap: initial install

- triggered by `/setup` or `station:install`
- creates the first/default tenant
- special case: one-time app bootstrap, not an ongoing sales/ops flow

### 1. Shared public demo

- a stable tenant such as `station-demo.fissible.dev`
- linked from `fissible.dev`
- intended for self-directed product exploration
- resettable/reseedable from `/platform`

### 2. Internal or prospect demo tenant

- created manually from `/platform`
- does not require public checkout
- can be prepared for a specific prospect
- should be convertible into a live paid tenant later

### 3. Paid self-serve customer tenant

- created automatically after successful purchase
- uses the same tenant provisioner as the other flows
- differs only in trigger and commercial state

Core rule:

- all three ongoing entry points plus the bootstrap case should converge on the same underlying provisioning service and lifecycle model

## Working principles

### 1. Provision fast on a domain we control

A newly created tenant should become usable immediately on a platform-owned subdomain.

Do **not** block initial provisioning on customer DNS changes, custom domain verification, or certificate issuance.

### 2. Treat custom domains as a second step

Custom domains should be attached after the tenant exists and is already reachable on its default subdomain.

### 3. Do not hard-delete by default

Tenant removal should be lifecycle-based:

- `active`
- `suspended`
- `archived`
- `purged`

`purged` is irreversible and platform-admin-only.

### 4. Platform owns cross-tenant operations

All tenant creation, suspension, archival, purging, and domain attachment flows should live in the `/platform` surface, not inside a tenant-scoped admin area.

## Domain strategy

### DNS and hosting assumptions

The managed-subdomain model should rely on wildcard DNS, not per-tenant DNS changes.

### Recommended DNS setup

- `fissible.dev` -> marketing/platform app ingress
- `platform.fissible.dev` -> same app ingress
- `*.fissible.dev` -> same app ingress

This means:

- `station-demo.fissible.dev` works without a special DNS step
- newly provisioned tenants like `acme.fissible.dev` work immediately
- tenant creation does not require a Name.com API call per customer

### Operational rule

For platform-managed subdomains, provisioning a tenant should only require:

1. creating the tenant record
2. reserving the slug
3. seeding the tenant

DNS should already be in place.

### When the DNS provider API is actually needed

The Name.com API is useful for platform-level DNS management, but it should not be on the critical path for routine tenant creation.

Appropriate uses:

- creating or updating wildcard/platform records for `fissible.dev`
- managing special platform-owned hosts
- maintenance or failover DNS changes
- verification records for domains we directly control

Not the default use case:

- creating one DNS record per new tenant subdomain

### Root and platform hosts

The Laravel app should serve both the public marketing site and the platform surface.

Recommended host layout:

- `fissible.dev` = public marketing site and purchase flow
- `platform.fissible.dev` = platform-wide tenant management surface
- `{slug}.fissible.dev` = managed tenant hostnames

Reason:

- one Laravel app can own marketing, checkout, tenant provisioning, and platform operations
- `platform.fissible.dev` keeps cross-tenant operations off tenant hosts
- `fissible.dev` remains the primary brand and acquisition surface

### Managed tenant subdomains

Every tenant gets a default platform-managed hostname on creation.

Recommended pattern:

- `{slug}.fissible.dev`

Alternative if we want stronger separation between the marketing root and tenant fleet:

- `{slug}.station.fissible.dev`

Preferred default for now:

- `{slug}.fissible.dev`

Reason:
- simpler to explain
- fewer DNS moving parts
- fits the current brand/domain posture

Reserved slugs must be blocked at validation time.

Minimum reserved list:

- `www`
- `platform`
- `station-demo`
- `api`
- `admin`
- `mail`
- `staging`
- `app`
- `support`

This should be implemented in the first tenant-management milestone, not deferred.

### Customer custom domains

Each tenant may later attach one or more custom domains, with one marked primary.

Initial minimal scope:

- support one custom domain per tenant
- support optional `www` variant later

Future scope:

- multiple custom domains
- redirect policy management
- domain-level environment routing

### Managed subdomain behavior after custom domain activation

When a custom domain becomes primary:

- the managed subdomain should remain valid
- the managed subdomain should redirect to the primary custom domain for frontend traffic
- platform/admin recovery access should still be possible through a platform-controlled hostname pattern if needed

Reason:

- avoids orphaning the tenant if the custom domain later breaks
- gives support a predictable fallback path
- keeps canonical frontend traffic on the customer-owned domain

## Tenant model changes

The current tenant docs already define:

- `name`
- `slug`
- `domain`
- `uuid`

That is enough for the first version, but the likely production model should expand to separate managed and custom domains.

Recommended direction:

### Short-term

Keep the current `tenants` table shape and interpret fields as:

- `slug` = platform-managed subdomain key
- `domain` = primary custom domain override

### Medium-term

Move domain state into a separate table:

- `tenant_domains`

Suggested fields:

- `id`
- `tenant_id`
- `host`
- `kind` (`managed`, `custom`)
- `is_primary`
- `status` (`pending`, `verified`, `active`, `failed`)
- `created_by`
- `verified_by`
- `verification_token` or challenge metadata
- `last_verified_at`
- `tls_status`

Reason:
- the current single `domain` column is enough for initial routing
- it is not enough for a real custom-domain lifecycle

## Tenant lifecycle

### 1. Create

Triggered by:

- `/setup` for first install
- `/platform` manual creation
- successful checkout in self-serve flow

System actions:

1. create tenant record
2. reserve unique slug
3. assign platform-managed hostname
4. seed baseline content/settings
5. create or attach owner user
6. create tenant membership
7. assign owner/admin role
8. enqueue any async setup work

Result:

The tenant is immediately reachable on its managed subdomain.

### 2. Suspend

Purpose:

- stop tenant access without destroying data

Effects:

- frontend returns suspended/disabled experience
- tenant admin login is blocked or restricted
- background jobs for that tenant are paused where practical
- billing or abuse issues can be enforced quickly

### 3. Archive

Purpose:

- hide tenant from active fleet while retaining full recoverability

Effects:

- tenant no longer treated as active
- frontend disabled
- admin access restricted to platform admins
- can later be restored

### 4. Purge

Purpose:

- irreversible destructive removal

Rules:

- platform-admin-only
- explicit confirmation flow
- preferably delayed via queued job and audit log
- should revoke domains before deletion
- should clean tenant-scoped media, memberships, and content

This should never be the default “delete” button behavior.

## `/platform` tenant actions

The `/platform` panel should eventually expose a tenant management area with these actions:

### Immediate actions

- Create tenant
- View tenant details
- Suspend tenant
- Reactivate tenant
- Archive tenant
- Restore archived tenant
- Purge tenant

### Domain actions

- View managed hostname
- Add custom domain
- Show DNS instructions
- Verify domain
- Set primary domain
- Remove custom domain

### Commercial/ops actions

- View plan/subscription state
- Mark as demo tenant
- Extend trial
- Transfer ownership
- Trigger reseed/bootstrap jobs
- Reset shared demo tenant
- Convert prospect demo to paid/live tenant

## Demo tenant flow

Demo tenants are internal or sales-owned tenants created from `/platform`.

### Desired behavior

Platform admin clicks `Create Demo Tenant` and provides:

- tenant name
- slug
- owner email optional
- optional starter template

System provisions:

- tenant
- managed subdomain
- seeded demo content
- optional owner invitation

Phase A expectation for seeded demo content:

- homepage
- privacy page
- primary menu
- secondary menu
- demo user with resettable credentials
- if available in the current seed path, a contact page and contact form

This should produce a tenant that looks real enough for self-directed exploration and sales demos, not an empty shell.

Recommended flags:

- `is_demo`
- `demo_expires_at`

Recommended platform actions:

- duplicate demo template into new demo tenant
- reset demo tenant content
- archive demo tenant
- purge demo tenant

### Shared public demo

The public demo should be treated as a special long-lived demo tenant.

Recommended hostname:

- `station-demo.fissible.dev`

Expected behavior:

- prominently linked from `fissible.dev`
- safe fake data only
- a fixed demo user with resettable credentials
- no sensitive integrations
- no irreversible side effects from demo usage

Required platform action:

- `Reset Demo` should reseed the tenant back to a known-good demo state without recreating the whole system manually

Recommended login pattern:

- a shared demo account with credentials shown on the marketing/demo entry point
- reset action should also restore that demo account to a known password/state

### Prospect demo tenants

Prospect demos should be separate tenants created for specific sales conversations.

Recommended pattern:

- `{prospect-slug}.fissible.dev`
- or `demo-{prospect-slug}.fissible.dev`

Expected behavior:

- created manually from `/platform`
- seeded from a reusable starter/demo template
- optionally invite the prospect directly
- kept isolated from the public shared demo

### Demo expiry policy

Demo tenants should not disappear abruptly.

Recommended flow:

1. warning email before expiry
2. suspend on `demo_expires_at`
3. retain for a grace window derived from configuration or policy
4. archive after grace window
5. purge only by explicit platform action

## Paid self-serve provisioning flow

This flow should start on the public Station frontend, but the resulting tenant is managed by the platform layer.

### Proposed flow

1. visitor selects plan / purchase option
2. visitor completes checkout
3. payment success creates a provisioning record
4. backend provisions tenant automatically
5. owner account is created or invited
6. tenant becomes available on managed subdomain
7. owner is redirected to onboarding
8. custom domain setup is offered after first login or from onboarding checklist

### Why this split matters

Provisioning on a managed subdomain makes the commercial flow reliable.

Waiting for customer DNS before tenant creation would create:

- failed onboarding
- support overhead
- ambiguous payment success states

### Converting a prospect demo into a paid tenant

If a prospect demo converts, the preferred flow is to promote that existing tenant rather than reprovision from scratch.

Expected conversion actions:

1. attach subscription/billing record
2. remove demo status/expiry behavior
3. update commercial state and plan
4. optionally transfer ownership or invite the customer owner
5. keep existing content, users, and configuration
6. optionally attach custom domain later

Reason:

- preserves setup work already done for the prospect
- avoids migration friction during sales conversion
- makes high-touch sales and self-serve provisioning share the same operating model

## Suggested provisioning architecture

### Core concepts

- `TenantProvisioner` service
- `TenantSeeder` or starter-template service
- `ProvisionTenant` queued job
- `TenantLifecycleService`
- `TenantDomainService`
- `ResetTenantDemo` job or equivalent reseed action
- `ConvertTenantToPaid` service or action

### Flow outline

1. payment webhook or successful checkout handler creates `tenant_provisioning_requests`
2. request is marked `pending`
3. queued job provisions tenant
4. request is marked `succeeded` or `failed`
5. owner receives login/invitation email

### Why use a provisioning record

This gives:

- retryability
- idempotency
- auditability
- support visibility for failed setups

## Domain handling details

### Managed subdomains

Platform DNS should already be configured for wildcard routing.

Example:

- `*.fissible.dev` -> app ingress

Tenant resolution then maps subdomain to tenant `slug`.

### Custom domains

Custom domains should be a guided flow:

1. tenant admin enters desired domain
2. Station shows DNS records to add
3. Station verifies DNS points correctly
4. TLS is issued/configured
5. domain is activated
6. domain may become primary

Until activation completes:

- tenant continues working on managed subdomain

### Resolution order

The existing documented order is correct and should remain:

1. custom domain match
2. subdomain slug match
3. single-tenant fallback only in single mode

For real multi-tenant production:

- `STATION_TENANCY_MODE=multi`

## Design decisions

These are the current implementation decisions reflected in this plan:

### 1. One custom domain or many?

Recommendation:

- one primary custom domain in v1

### 2. Who can attach domains?

Recommendation:

- tenant admins can request/add domains
- platform can verify/override/remove any domain

### 3. When should custom domains become active?

Recommendation:

- only after DNS verification and TLS readiness

### 4. What should purge actually delete?

Recommendation:

- all tenant-scoped content
- memberships
- media
- invitations
- settings
- domain mappings

Retain:

- platform audit log entries with redacted references if necessary

### 5. Should demo tenants auto-expire?

Recommendation:

- yes, via warning + suspension + grace period + archival

## Recommended implementation phases

### Phase A: Manual lifecycle in `/platform`

Ship first:

- create tenant
- reserved-slug validation
- minimal demo seeder for shared and prospect demos
- suspend/reactivate
- archive/restore
- purge with confirmation
- managed hostname display
- reset shared demo tenant

No billing required yet.

Architecture expectations:

- keep `/platform` in the same Laravel app as a route group with dedicated middleware
- scaffold `TenantLifecycleService` and `TenantDomainService` in this phase, even if some methods are initially stubs
- keep the current tenant `domain` column for now with an explicit note that it is temporary until Phase B
- add minimum schema support for `status`, `is_demo`, and `demo_expires_at`

### Phase B: Domain onboarding

Ship next:

- add custom domain
- verification flow
- activation
- set primary domain

### Phase C: Paid self-serve provisioning

Ship after manual lifecycle is stable:

- checkout integration
- provisioning requests table
- async tenant provisioning job
- owner onboarding flow

### Phase D: Polish and ops

- richer demo templates
- tenant cloning/reset
- automated demo expiry
- better audit trails
- subscription lifecycle automation

## Claude review checklist

When reviewing this against the Laravel web app scaffold, verify that:

- `/platform` is implemented as a route group in the same Laravel app
- tenant lifecycle and domain services are scaffolded early, even if behavior is partial
- the current tenant `domain` column is treated as temporary until domain onboarding expands
- Phase A includes a minimal demo seeder and shared demo reset flow
- the shared public demo, prospect demos, and paid self-serve path all use the same tenant provisioner
- reserved slug validation includes platform and demo hostnames

## Summary

The intended Station model should be:

1. keep a stable public demo tenant such as `station-demo.fissible.dev`
2. allow `/platform` to create prospect-specific demo tenants on demand
3. provision paid customer tenants automatically from the public frontend
4. route all of those paths through one tenant provisioner
5. make managed subdomains work immediately via wildcard DNS
6. convert prospect demos into paid/live tenants without reprovisioning
7. attach custom domains only after the tenant already exists
8. treat destructive deletion as a rare purge action, not the default offboarding path
