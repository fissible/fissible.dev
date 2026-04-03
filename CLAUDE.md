
# Tome Context Store
This project uses Tome (`.tome.db`) for structured context.
- Before responding, extract topic keywords from the user's message and call `tome_lookup`.
- If another project is mentioned by name, also call `tome_cross_lookup`.
- When you learn a durable truth about this project (architectural decisions, conventions,
  gotchas, dependency relationships), call `tome_store` to save it.
- Prefer `kind='decision'` for choices with rationale, `kind='gotcha'` for non-obvious
  pitfalls, `kind='convention'` for patterns to follow, and `kind='fact'` for everything
  else (including dependency relationships).
- For gotchas and dependency facts, save automatically. For decisions, conventions,
  and architectural facts, ask the user first.
- When a fact from Tome directly helps answer the user's question, call
  `tome_rate(fact_id, useful=True)`. If a fact was misleading or wrong, call
  `tome_rate(fact_id, useful=False, reason="...")`. Verified incorrect facts are
  deleted automatically.
- To inspect structured tabular data, use `tome_query_dataset(name)`.
- During idle periods (no user prompt for 2+ minutes), call `tome_dream()` to run
  one maintenance cycle. Review the returned batch and rate/delete facts as needed.
  Stop dreaming when the user sends a new prompt.

## Code navigation
Before reading any source file, use cymbal to find what you need:
- `cymbal search <name>` — find a symbol by name (avoids reading whole files)
- `cymbal show <symbol>` — read a function or class body directly
- `cymbal outline <file>` — see all definitions in a file before opening it
- `cymbal impact <symbol>` — find callers before changing a function
- `cymbal trace <symbol>` — see what a function calls

Only fall back to reading files directly when cymbal cannot answer the question.
