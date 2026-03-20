[Back to Overview](index.md)

# Fixture Entity Store

The FixtureEntityStore is a lightweight, static in-memory registry used during test execution to keep track of created entities.

## Purpose

It allows builders and scenarios to register created entities centrally, so they can be accessed later in the test lifecycle (e.g. for assertions or cross-entity validation).

This is especially useful when:
- multiple builders interact indirectly
- entities need to be inspected beyond their returned IDs
- debugging complex scenarios

## Behavior

- Entities are stored by their class name
- Only one entity per class is stored (last write wins)
- The store is static and shared across the entire test runtime

## Usage

The Store automatically stores entities, when using [builders](builders.md) or [scenarios](scenarios.md). (which also invoke builders)

Retrieve all stored entities:

```php
$entities = FixtureEntityStore::getEntities();
$contact = $entities['\Civi\Api4\Contact'];
```

Reset between tests:

```php
FixtureEntityStore::reset();
```

### Important

Because the store is static:
- it must be reset between tests to avoid state leakage
- it should only be used in test context

### When to use it

Use the FixtureEntityStore only when:
- returning IDs via builders/scenarios is not sufficient
- you need full entity payloads for assertions or debugging

Avoid using it as a primary data access pattern. Prefer explicit return values (IDs via Bags) whenever possible.

[Back to Overview](index.md)
