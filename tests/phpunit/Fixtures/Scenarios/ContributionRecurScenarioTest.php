<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Scenarios;

use Civi\Api4\Contribution;
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
    if ($this->tx instanceof \CRM_Core_Transaction) {
      $this->tx->rollback();
      $this->tx = NULL;
    }
    parent::tearDown();
  }

  /**
   *
   */
  public function testCustomWithInvalidCustomerMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      contactMethod: 'invalidContactMethod'
    );
  }

  /**
   *
   */
  public function testCustomWithInvalidMembershipMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      membershipMethod: 'invalidMembershipMethod'
    );
  }

  /**
   *
   */
  public function testCustomWithInvalidRecurringMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      recurringMethod: 'invalidRecurringMethod'
    );
  }

  /**
   *
   */
  public function testCustomWithInvalidContributionMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      contributionMethod: 'invalidContributionMethod'
    );
  }

  /**
   *
   */
  public function testPendingRecurWithoutContribution_CreatesAndReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithoutContribution();

    self::assertGreaterThan(0, $bag->contactId);
    self::assertGreaterThan(0, (int) $bag->membershipId);
    self::assertGreaterThan(0, (int) $bag->recurringContributionId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('*')
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', (int) $bag->recurringContributionId)
      ->execute()->first();

    self::assertNotNull($recur);
    self::assertSame($bag->contactId, (int) $recur['contact_id']);
    self::assertEquals('Pending', $recur['contribution_status_id:name']);
  }

  /**
   *
   */
  public function testPendingRecurWithoutContribution_WithOverrides_AppliesOverrides(): void {
    $bag = ContributionRecurScenario::pendingRecurWithoutContribution(
      contactOverrides: ['first_name' => 'Alice'],
      membershipOverrides: ['join_date' => '2022-01-01'], recurringOverrides: ['amount' => 99.99, 'currency' => 'USD']
    );

    $recur = ContributionRecur::get(FALSE)
      ->addWhere('id', '=', (int) $bag->recurringContributionId)
      ->execute()->first();

    self::assertNotNull($recur);
    self::assertSame(99.99, (float) $recur['amount']);
    self::assertSame('USD', (string) $recur['currency']);
  }

  /**
   * Ensure a pending recurring contribution with a pending contribution
   * returns a valid bag and creates the expected recurring entity.
   */
  public function testPendingRecurWithPendingContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithPendingContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('*')
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->recurringContributionId)
      ->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, (float) $recur['amount']);
    self::assertSame('EUR', (string) $recur['currency']);
    self::assertEquals('Pending', (string) $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertEquals('Pending', (string) $contribution['contribution_status_id:name']);
  }

  /**
   * Ensure a pending recurring contribution with a pending contribution
   * returns a valid bag and creates the expected recurring entity.
   */
  public function testPendingRecurWithCompletedContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithCompletedContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('*')
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->recurringContributionId)
      ->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, (float) $recur['amount']);
    self::assertSame('EUR', (string) $recur['currency']);
    //After completing the contribution the state changes to "In Progress"
    self::assertEquals('In Progress', $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertEquals('Completed', $contribution['contribution_status_id:name']);
  }

  /**
   * Ensure a pending recurring contribution with a pending contribution
   * returns a valid bag and creates the expected recurring entity.
   */
  public function testPendingRecurWithCancelledContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithCancelledContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('*')
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->recurringContributionId)
      ->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, (float) $recur['amount']);
    self::assertSame('EUR', (string) $recur['currency']);
    self::assertEquals('Pending', $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertEquals('Cancelled', $contribution['contribution_status_id:name']);
  }

}
