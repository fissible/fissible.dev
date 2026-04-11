---
title: Account Self-Service
description: User account management features including password changes, two-factor authentication, avatar uploads, notification preferences, and account deletion.
---

Station provides self-service account management from the profile settings page in the admin panel. Users can update their password, enable two-factor authentication, upload an avatar, configure notification preferences, and delete their account.

## Password changes

Password changes use the standard Filament profile form. Users enter their current password and choose a new one.

## Two-factor authentication

Station supports TOTP (Time-based One-Time Password) app-based two-factor authentication via Filament's built-in MFA UI.

| Detail | Value |
|--------|-------|
| **Method** | TOTP (Google Authenticator, Authy, etc.) |
| **Storage** | TOTP secret stored on the user model |
| **Feature flag** | `MFA_ENABLED` env var (default: `false`) |

When `MFA_ENABLED=true`, users see a 2FA setup section on their profile page where they can scan a QR code and confirm with a verification code.

## Avatar

Users upload an avatar image from the profile page. Avatars are stored via **Spatie Media Library** in the `avatar` collection on the `public` disk.

## Notification preferences

Users can opt out of specific notification types. Preferences are stored as a JSON array on the user model.

| Notification type | Description |
|-------------------|-------------|
| `submitted_for_review` | An entry was submitted for review |
| `entry_published` | An entry was published |

All notification types default to **enabled**. Users toggle individual types off from their profile page.

## Account deletion (anonymization)

Account deletion uses an anonymization approach that preserves authored content for audit purposes.

### Process

1. User confirms deletion by entering their password
2. The **AnonymizeUser** service executes the following steps:
   - Replaces email with `anonymized+{uuid}@deleted.invalid`
   - Randomizes the password
   - Revokes all tenant memberships and roles
   - Invalidates all active sessions
   - Sets the `anonymized_at` timestamp

### What is preserved

- **Name** — retained for attribution on authored content
- **Authored content** — entries and revisions remain in place

### Safety guard

Account deletion is blocked if the user is the **only super admin** in any tenant. This prevents a tenant from losing all administrative access. The user must assign another super admin before deleting their account.
