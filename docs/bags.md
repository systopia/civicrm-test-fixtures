[Back to Overview](index.md)

# Fixture Bags

Fixture Bags are immutable value objects returned by scenarios.

They package one or more IDs in a **consistent, schema-driven format**.

A bag’s responsibilities:

- define a canonical key schema (`schema()`)
- export IDs according to that schema (`toArray()`)
- remain immutable (read-only properties)

## Using a bag

Bags are consumed via `toArray()`:

```php
$data = $bag->toArray();

$contactId      = $data['contactId'];
$membershipId   = $data['membershipId'];
$contributionId = $data['contributionId'];
```

## Schema

Each bag defines its canonical set of keys:

```php
public static function schema(): array {
  return ['contactId', 'membershipId', 'contributionId'];
}
```

This schema is the stable contract for consumers and tests.

---

## Example: Contribution Bag

A bag typically stores IDs as read-only properties and exports them:

```php
use Systopia\TestFixtures\Fixtures\Bags\ContributionBag;

$bag = ContributionBag::fromIds(
  contactId: 123,
  membershipId: 456,
  contributionId: 789,
);

$data = $bag->toArray();
```

## Immutability

Bags are intended to be immutable to reduce test side effects.

In practice this means:

- IDs are set once via the constructor / named constructor
- IDs are exposed as public readonly properties (when relevant)
- consumers do not mutate bag state

---

## Nullability

Some IDs may be optional depending on the scenario.

Bags represent that explicitly as ```?int``` and export ```int|null``` values.

Example: membershipId may be null.

```php
$bag = ContributionBag::fromIds(
  contactId: 123,
  membershipId: null,
  contributionId: null,
);
```

---

## Why bags exist (instead of returning arrays)

Bags provide:

- stable keys via a canonical schema
- a single return type for scenarios
- a clear place for invariants (e.g. “IDs must be positive when present”)

They also keep tests readable:

- scenarios communicate intent
- bags communicate results

---

## Notes on base implementation

Bags extend AbstractBaseFixtureBag and implement export().

The base class provides toArray() which returns the canonical export.

Concrete bags should keep logic minimal:

- validate invariants in the constructor
- export the IDs in canonical schema order

---

[Back to Overview](index.md)  
