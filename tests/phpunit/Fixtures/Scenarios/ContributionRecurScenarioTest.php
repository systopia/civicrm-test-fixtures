<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Scenarios;

use Civi\Api4\ContributionRecur;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionRecurScenario;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Scenarios\ContributionRecurScenario
 */
final class ContributionRecurScenarioTest extends TestCase {

  private ?\CRM_Core_Transaction $tx = NULL;

  /**
   *
   */
  protected function setUp(): void {
    parent::setUp();
    $this->tx = new \CRM_Core_Transaction();
  }

  /**
   *
   */
  protected function tearDown(): void {
    if ($this->tx !== NULL) {
      $this->tx->rollback();
      $this->tx = NULL;
    }
    parent::tearDown();
  }

  /**
   *
   */
  public function testContactWithMembershipAndPendingRecurringContribution_CreatesAndReturnsBag(): void {
    $bag = ContributionRecurScenario::contactWithMembershipAndPendingRecurringContribution();

    self::assertGreaterThan(0, $bag->contactId);
    self::assertGreaterThan(0, (int) $bag->membershipId);
    self::assertGreaterThan(0, (int) $bag->recurringContributionId);

    $recur =
      ContributionRecur::get(FALSE)->addWhere('id', '=', (int) $bag->recurringContributionId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame($bag->contactId, (int) $recur['contact_id']);
    self::assertGreaterThan(0, (int) $recur['contribution_status_id']);
  }

  /**
   *
   */
  public function testContactWithMembershipAndPendingRecurringContribution_AppliesOverrides(): void {
    $bag = ContributionRecurScenario::contactWithMembershipAndPendingRecurringContribution(
      contactOverrides: ['first_name' => 'Alice'],
      membershipOverrides: ['join_date' => '2022-01-01'], recurringOverrides: ['amount' => 99.99, 'currency' => 'USD']
    );

    $recur =
      ContributionRecur::get(FALSE)->addWhere('id', '=', (int) $bag->recurringContributionId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame(99.99, (float) $recur['amount']);
    self::assertSame('USD', (string) $recur['currency']);
  }

}
