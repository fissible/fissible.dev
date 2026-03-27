---
title: Installation
description: Install seed via Homebrew or macbin. Requires bash 3.2+ with no external dependencies.
---

## Requirements

- bash 3.2 or later
- No external dependencies — seed uses only bash and standard POSIX utilities

## Homebrew (recommended)

```bash
brew install fissible/tap/seed
```

Verify the install:

```bash
seed --version
```

## macbin

seed is also available via fissible/macbin:

```bash
macbin install seed
```

## From source

Clone the repository and add it to your `PATH`:

```bash
git clone https://github.com/fissible/seed.git
export PATH="$PATH:/path/to/seed/bin"
```

## MCP server setup

To use seed's generators inside Claude Desktop or any MCP-compatible client, add the server to `~/.config/claude/claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "seed": {
      "command": "seed",
      "args": ["mcp"]
    }
  }
}
```

Restart Claude Desktop after editing the config. All 37 generators will be available as MCP tools.
