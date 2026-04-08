---
title: Users & Invitations
description: User accounts, tenant memberships, invitation flow, account self-service, and platform admin behavior.
---

Station separates **user accounts** (global) from **tenant memberships** (per-tenant). A single user can belong to multiple tenants, each with a different role. All user additions require consent through an invitation flow — there is no direct "add user" action.

## User accounts

User accounts are global and not tied to any specific tenant. A user's relationship to a tenant is defined by their **tenant membership**, which tracks:

- **Status** — `active` or `suspended`
- **How they were added** — `install`, `invitation`, or `platform`
- **Suspension timestamp** — set when a user is suspended, cleared on reactivation

A suspended user cannot access the admin panel for that tenant but retains their account and role. Reactivation restores full access. Suspension in one tenant does not affect access to other tenants.

## Invitation flow

All user additions go through an email-based invitation flow:

### Sending an invitation

1. An admin clicks "Invite User" in the team management UI
2. Enters the invitee's email address and selects a role
3. The system generates a secure token (SHA-256 hashed in the database, raw token in the email only)
4. The invitation expires after 14 days

Invitations are rate-limited to 20 per admin per hour per tenant. Duplicate checks prevent inviting someone who is already a member or has a pending invitation. After an invitation is declined, expired, or revoked, the same email can be re-invited.

### Accepting — new user

If no account exists for the invited email:

1. The invitee sees a registration form (name, password — email is pre-filled and read-only)
2. On submit: account is created with a verified email, membership is created, and the intended role is assigned
3. Redirected to the login page

### Accepting — existing user

If an account already exists:

1. The invitee sees the tenant name and offered role with accept/decline options
2. **Accept:** membership and role are created, redirected to the admin dashboard
3. **Decline:** invitation is marked as declined

### Acceptance-time validation

The intended role stored on the invitation is validated again at acceptance time. If the role has been removed or is no longer assignable under current tenant policy, the invitation is invalidated. However, the original inviter's permissions are **not** re-checked — the invitation was authorized when sent. This prevents punishing invitees for internal org changes.

### Invalid tokens

Expired, revoked, or non-existent tokens show a generic "This invitation is no longer valid" message with no specifics (for security).

### Resending

Resending an invitation generates a new token, resets the expiry to 14 days, and invalidates the old link.

## Team management UI

User management lives under the "Team" navigation group in the admin panel, accessible to admins and super admins only. All actions use inline modals — no separate edit or detail pages.

### Tabs

| Tab | Shows | Actions |
|-----|-------|---------|
| Active | Current members with role, join date, and how they were added | Edit role, suspend, remove, send password reset |
| Invited | Pending invitations with intended role, inviter, and expiry | Resend, revoke |
| Suspended | Inactive members with suspension date | Reactivate, remove |

All tabs include a role filter dropdown. Each action respects the role hierarchy — an actor can only manage users with a strictly lower role.

## Account self-service

Users manage their own accounts from the profile settings page:

### Password and authentication

- Password changes via the standard Filament profile form
- **TOTP two-factor authentication** — users can enable app-based 2FA through Filament's built-in MFA UI

### Avatar

User avatars are stored via Spatie Media Library in the `avatar` collection on the `public` disk.

### Notification preferences

Users can opt out of specific notification types (e.g., `submitted_for_review`, `entry_published`). Preferences are stored as a JSON array on the user model. All notifications default to enabled.

### Account deletion (anonymization)

Users can delete their own account from the profile page. This requires password confirmation and triggers anonymization:

- Email is replaced with `anonymized+{uuid}@deleted.invalid` and the password is randomized
- All tenant memberships and role assignments are revoked
- All active sessions are invalidated
- An `anonymized_at` timestamp is set
- The user's name and authored content are retained for audit purposes

A safety guard prevents deletion if the user is the only super admin in any tenant.

## Platform admin behavior

Users with `is_platform_admin = true` have special cross-tenant privileges:

- **Panel access** — can access any tenant's admin panel without a membership
- **Implicit super admin** — treated as super admin for authorization checks in any tenant
- **No membership pollution** — platform admins do not get Spatie role records or membership entries in tenants they visit
- **Platform panel** — access to the platform-wide management panel at `/platform` for tenant management, system backups, and system health

## Audit trail

All user management actions emit Laravel events for audit logging:

- `TeamMemberInvited`, `InvitationAccepted`, `InvitationDeclined`, `InvitationRevoked`, `InvitationResent`
- `TeamMemberRoleChanged`, `TeamMemberSuspended`, `TeamMemberReactivated`, `TeamMemberRemoved`
- `PasswordResetSent`
