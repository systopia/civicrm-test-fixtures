# Systopia Test Fixtures

A small PHP library to create **repeatable CiviCRM APIv4 test data** with minimal boilerplate.

It provides:

- [Builders](builders.md) for single entities (returning numeric IDs)
- [Scenarios](scenarios.md)  that orchestrate multiple builders and return a **Fixture Bag**
- [Fixture Bags](bags.md)  as immutable return objects with schema validation
- [Fixture Entity Store](entity-store.md) static in-memory registry of created entities
- A thin **APIv4 adapter/factory layer** so builders stay simple and can be tested with fakes

---

## Non-Goals

This library is intentionally **not**:

- an ORM or persistence abstraction
- a general-purpose APIv4 wrapper
- intended for production code
- a replacement for CiviCRM business logic

It exists solely to make **tests comfortable, readable, repeatable and intention-revealing**.

---

## Installation

### Requirements

- PHP >= 8.1
- CiviCRM with APIv4 enabled
- Intended for test environments only

Install via Composer (typically as a dev dependency):

```bash
composer require --dev systopia/test-fixtures
```

---

## Design Philosophy

### Builders return IDs

Builders encapsulate default payloads + overrides and return the created entity ID.

### Scenarios return Bags

Scenarios orchestrate multiple builders (e.g. contact + membership + contribution) and return a bag containing the
relevant IDs.

### Bags export a canonical array

Bags are immutable objects. Consumers should read IDs via `toArray()`.

### IMPORTANT

Scenarios create real CiviCRM entities.

**Tests are expected to wrap execution in a database transaction and roll it back!** (See the example)

---

## Access Builder Data

The FixtureEntityStore is a lightweight, static in-memory registry used during test execution to keep track of created entities.

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

ContributionScenario::contactWithMembershipAndPendingContribution();

$entities = FixtureEntityStore::getEntities();
$contact = $entities['Civi\Api4\Contact'];
```

---

## Quick Start

### Create a scenario (Contact + Membership + pending Contribution)

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

$bag = ContributionScenario::contactWithMembershipAndPendingContribution();

$data = $bag->toArray();

$contactId      = $data['contactId'];
$membershipId   = $data['membershipId'];
$contributionId = $data['contributionId'];
```

### Overrides

Most builders/scenarios accept override arrays (array<string, mixed>) which are merged into defaults.

Override contact, membership and contribution fields:

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

$bag = ContributionScenario::contactWithMembershipAndPendingContribution(
  contactOverrides: [
    'first_name' => 'Ada',
    'last_name'  => 'Lovelace',
  ],
  membershipOverrides: [
    'join_date'  => '2022-01-01',
    'start_date' => '2022-01-02',
  ],
  contributionOverrides: [
    'total_amount' => 99.95,
    'currency'     => 'USD',
  ],
);
```

### Use Builders directly

If you only need a single entity ID, call a builder:

```php
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;

$contactId = ContactBuilder::createDefault();
// or assign default overrides:
$contactId = ContactBuilder::createDefault([
  'first_name' => 'Test',
  'last_name'  => 'User',
]);
```

Create an open (pending) Contribution and an active membership for a contact:

```php
use Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

$contributionId = ContributionBuilder::createPendingForContact($contactId);
$membershipId = MembershipBuilder::createActiveForContact($contactId);
```
