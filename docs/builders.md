[Back to Overview](index.md)

# Builders

Builders create **single CiviCRM APIv4 entities** and return the created entity **ID** (`int`).

They are designed to:

- keep tests concise and intention-revealing
- provide sensible defaults
- allow targeted overrides
- centralize validation for common pitfalls

---

## What a Builder returns

A builder returns a numeric ID:

```php
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;

$contactId = ContactBuilder::createDefault();
```

This ID is suitable for use with:

- other builders (e.g. creating a membership for a contact)
- scenarios (which orchestrate multiple builders)
- direct APIv4 reads in assertions

---

## Defaults + Overrides

Builders provide defaults and merge in overrides using array_replace_recursive().

Example: create a contact with a custom name:

```php
$contactId = ContactBuilder::createDefault([
  'first_name' => 'Ada',
  'last_name'  => 'Lovelace',
]);
```

How overrides are merged

The builder constructs a $base array and merges overrides into it:

- Defaults provide a valid baseline payload
- Overrides allow per-test customization without copy/pasting full payloads

---

## Builder "variants"

Some builders provide convenience variants that encode a common intention.

Example: create a deceased contact:

```php
$contactId = ContactBuilder::createDeceased([
  'deceased_date' => '2021-02-03',
]);
```

### Builders with required foreign keys

Some entities require a foreign key (e.g. contact_id).
These builders typically offer a convenience method that takes the required ID explicitly.

Example: create a contribution for a contact:

```php
$contributionId = ContributionBuilder::createForContact($contactId);
```

Example: create a pending contribution:

```php
$contributionId = ContributionBuilder::createPendingForContact($contactId, [
  'total_amount' => 99.95,
  'currency' => 'USD',
]);
```

## Validation

Builders may validate common constraints early (e.g. “contactId must be positive”).
This avoids creating broken fixtures and produces clearer test failures.

---

## Notes

Builders rely on the shared AbstractBaseBuilder which performs the API create call.

Builders should stay focused on defaults, variants and validation. Multi-entity orchestration belongs in scenarios.

---

[Back to Overview](index.md)  
