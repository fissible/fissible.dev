---
title: Reference
description: fault fingerprinting, status workflow, and test skeleton generation.
---

## Status workflow

```
open → investigating → resolved
         ↑                ↓
         └── re-opened ←──┘
```

## Test skeleton generation

From any exception in the triage UI, click **Generate Test** to produce a Pest/PHPUnit
skeleton that reproduces the failing condition:

```php
it('handles NotFoundException from UserController@show', function () {
    // Arrange: reproduce the conditions that triggered the exception
    // ...

    // Act
    $response = $this->get('/users/999');

    // Assert
    $response->assertStatus(404);
});
```

← [Installation](installation)
