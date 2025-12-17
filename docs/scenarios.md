[Back to Overview](index.md)

# Scenarios

Scenarios create **multiple related entities** and return a **Fixture Bag** containing the relevant IDs.

Use scenarios when:

- a test needs a realistic setup (e.g. contact + membership + contribution)
- the same setup is needed across multiple tests
- you want one call that communicates intent

---

## What a Scenario returns

A scenario returns a bag (immutable value object). Consumers should use `toArray()`:

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

$bag = ContributionScenario::contactWithMembershipAndPendingContribution();

$data = $bag->toArray();

$contactId      = $data['contactId'];
$membershipId   = $data['membershipId'];
$contributionId = $data['contributionId'];
```

## Scenarios accept overrides

Scenarios forward override arrays to the underlying builders.

Example:

```php
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

This keeps tests readable while still allowing custom data per test.

---

## Example: recurring contributions scenario

```php
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionRecurScenario;

$bag = ContributionRecurScenario::contactWithMembershipAndPendingRecurringContribution();

$data = $bag->toArray();

$contactId = $data['contactId'];
$membershipId = $data['membershipId'];
$recurId = $data['recurringContributionId'];
```

## Transaction handling (important)

Scenarios create **real CiviCRM entities**.

Tests are expected to wrap scenario execution in a DB transaction and roll it back.

Typical PHPUnit pattern:

```php
private ?\CRM_Core_Transaction $tx = null;

protected function setUp(): void {
  $this->tx = new \CRM_Core_Transaction();
}

protected function tearDown(): void {
  if ($this->tx !== null) {
    $this->tx->rollback();
    $this->tx = null;
  }
}
```

## When NOT to use a scenario

Do not create a scenario for every small variation.

Use a builder directly when you only need a single entity ID.

A practical rule:

- Builder: one entity
- Scenario: multiple entities that belong together conceptually
- Bag: returned by scenarios to package IDs consistently

---

[Back to Overview](index.md)  
