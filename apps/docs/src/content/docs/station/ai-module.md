---
title: AI Module
description: Configure tenant-owned AI agents, monitor usage, control AI features, and run registered prompts from automations.
sidebar:
  order: 56
---

The AI Module adds bring-your-own-key AI support to Station. Tenants configure one or more provider agents, Station records every run for audit and usage reporting, and other modules can safely call AI through a small shared interface.

Station does not include AI provider credits or a shared platform key. The tenant owns the provider account, API key, model choice, and provider billing relationship.

## What v1 includes

| Area | Capability |
|------|------------|
| Agents | Configure Anthropic, OpenAI, Google Gemini, AWS Bedrock, or Ollama agents |
| Health | Agent status tracks active, rate limited, invalid, and disabled states |
| Usage | Tenant dashboard with runs, tokens, estimated cost, recent runs, and CSV export |
| Controls | Per-feature kill switches for registered AI features |
| Automations | `AI: run prompt` action for Station automations |
| Audit | Append-only AI run records with status, tokens, latency, model, provider, and error code |

The module is free, but every provider call uses the tenant's own API key. Provider pricing, rate limits, and data-processing terms come from the selected provider.

## Install and enable

Install the package and run the module lifecycle:

```bash
composer require fissible/ai
php artisan station:module:install ai
```

Platform admins can also install the module from **Platform > Modules** when the package is available to the Station installation.

After installation, tenant admins with `ai.configure` see an **AI** group in the admin navigation:

- **AI Agents**
- **AI Usage**
- **AI Feature Settings**

## Configure an agent

Go to **AI > AI Agents** and create an agent.

| Field | Description |
|-------|-------------|
| Name | Admin-facing name, such as `Anthropic - Sonnet` |
| Provider | Anthropic, OpenAI, Google Gemini, AWS Bedrock, or Ollama |
| Default model | Provider model identifier to use for runs |
| Default | Whether this agent is the tenant default |
| API key | Provider API key. Required on create; leave blank on edit to keep the existing secret |
| Base URL | Optional for providers that support custom endpoints, such as Ollama or compatible OpenAI endpoints |

An agent must be active before consumer features run. The configure pages remain reachable even when no agent is active, so admins can recover from revoked keys or rate limits.

## Agent status

| Status | Meaning |
|--------|---------|
| Active | The agent can be selected for AI runs |
| Rate limited | The provider rejected a run due to rate limit or quota |
| Invalid | Credentials, model, or provider configuration failed |
| Disabled | The agent is intentionally unavailable |

Runtime failures update the agent status immediately when possible. A health check command is also available for scheduled or manual checks:

```bash
php artisan ai:health-check
```

## Usage and audit

Open **AI > AI Usage** to review:

- Runs this month and over the last 30 days
- Token totals and estimated cost
- Exceptions by feature
- Top features and agents by token usage
- Recent run history

Use **Export CSV** for offline review. Cost values are estimates based on the module's price map; always reconcile against the provider dashboard for billing.

Run records are designed for audit and troubleshooting. They store metadata such as provider, model, prompt key, feature key, token counts, latency, status, and error code. Prompt and response bodies are not retained as a general-purpose transcript store.

## Feature settings

Open **AI > AI Feature Settings** to see registered AI feature keys and their prompts.

Disabling a feature prevents that feature from calling AI for the tenant. This is useful when:

- A provider account is under cost pressure
- A specific workflow needs review
- A tenant wants to keep some AI surfaces off while allowing others

Feature settings are tenant-scoped.

## AI in automations

The AI Module registers an automation action named **AI: run prompt**.

Use it when an automation needs to turn existing trigger data or previous action output into generated text or structured output.

Configuration:

| Setting | Description |
|---------|-------------|
| Prompt | Registered prompt key to run |
| Context | Key-value map of prompt context values |

Context values can be literals or variable templates such as `{trigger.email}` or values from earlier actions. The action output includes:

| Output key | Description |
|------------|-------------|
| `text` | Generated text |
| `structured` | Structured result when the prompt returns one |
| `tokens_in` / `tokens_out` | Provider token counts |
| `model` | Model used |
| `agent_id` | Agent selected for the run |
| `run_id` | AI audit run ID |

Downstream automation steps can read the AI action's structured payload.

## Permissions

| Permission | Default roles |
|------------|---------------|
| `ai.configure` | Admin, Super Admin |
| `ai.use` | Author, Editor, Admin, Super Admin |
| `ai.audit.view` | Admin, Super Admin |

The `ai.use` permission allows AI consumer features to run, but the tenant still needs at least one active agent and the specific feature must remain enabled.

## Operational notes

- Keep provider API keys scoped and rotated according to the provider's guidance.
- Use provider-side budgets or limits where available.
- Treat Station cost estimates as operational hints, not invoices.
- If AI actions fail after a key change, check **AI Agents** first for invalid or rate-limited status.
- If no active agents remain, consumer AI features stop running until an admin fixes or creates an agent.
