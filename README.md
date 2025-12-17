# Systopia Test Fixtures

A small PHP library to create **repeatable CiviCRM APIv4 test data** with minimal boilerplate.

It provides:

- **Builders** for single entities (returning numeric IDs)
- **Scenarios** that orchestrate multiple builders and return a **Fixture Bag**
- **Fixture Bags** as immutable return objects with schema validation
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

## Quick Start

### Create a scenario (Contact + Membership + open Contribution)

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

$bag = ContributionScenario::contactWithMembershipAndOpenContribution();

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

$bag = ContributionScenario::contactWithMembershipAndOpenContribution(
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

### Fixture Bags

Fixture bags validate that:

- the export contains all keys defined in the schema
- no extra keys are present

Consume IDs via toArray():

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

$bag = ContributionScenario::contactWithMembershipAndOpenContribution();

$data = $bag->toArray();

// Canonical keys defined by the bag schema:
$contactId      = $data['contactId'];        // int
$membershipId   = $data['membershipId'];     // int|null
$contributionId = $data['contributionId'];   // int|null
```

## Full Example

```php
<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Scenarios;

use Civi\Api4\Contact;
use Civi\Api4\Contribution;
use Civi\Api4\Membership;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

final class ScenarioTest extends TestCase {

  private ?\CRM_Core_Transaction $tx = NULL;

  protected function setUp(): void {
    parent::setUp();
    $this->tx = new \CRM_Core_Transaction();
  }

  protected function tearDown(): void {
    if ($this->tx !== NULL) {
      $this->tx->rollback();
      $this->tx = NULL;
    }
    parent::tearDown();
  }

  public function testContactWithMembershipAndOpenContribution_CreatesAndReturnsBag(): void {
    $bag = ContributionScenario::contactWithMembershipAndOpenContribution();

    $data = $bag->toArray();

    $contactId = $data['contactId'] ?? NULL;
    $membershipId = $data['membershipId'] ?? NULL;
    $contributionId = $data['contributionId'] ?? NULL;

    self::assertIsInt($contactId);
    self::assertIsInt($membershipId);
    self::assertIsInt($contributionId);
    self::assertGreaterThan(0, $contactId);
    self::assertGreaterThan(0, $membershipId);
    self::assertGreaterThan(0, $contributionId);

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertSame($contactId, (int) $membership['contact_id']);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);
    self::assertSame($contactId, (int) $contribution['contact_id']);
  }
}
```

## Extending the library

- Add a **Builder** when you need a reusable way to create a single entity.
- Add a **Scenario** when multiple entities belong together conceptually.
- Add a **Bag** whenever a scenario returns more than one ID.
