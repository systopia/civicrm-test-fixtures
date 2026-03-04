<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Scenarios;

use Civi\Api4\Contribution;
use Civi\Api4\ContributionRecur;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionRecurScenario;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Scenarios\ContributionRecurScenario
 * @group headless
 */
final class ContributionRecurScenarioTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCustomWithInvalidCustomerMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      contactMethod: 'invalidContactMethod'
    );
  }

  public function testCustomWithInvalidMembershipMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      membershipMethod: 'invalidMembershipMethod'
    );
  }

  public function testCustomWithInvalidRecurringMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      recurringMethod: 'invalidRecurringMethod'
    );
  }

  public function testCustomWithInvalidContributionMethod_ThrowsException(): void {
    self::expectException(\InvalidArgumentException::class);
    ContributionRecurScenario::custom(
      contributionMethod: 'invalidContributionMethod'
    );
  }

  public function testPendingRecurWithoutContribution_CreatesAndReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithoutContribution();

    self::assertGreaterThan(0, $bag->contactId);
    self::assertGreaterThan(0, $bag->membershipId);
    self::assertGreaterThan(0, $bag->recurringContributionId);

    $recur = ContributionRecur::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addWhere(
        'id',
        '=',
        $bag->recurringContributionId
      )->execute()->first();

    self::assertNotNull($recur);
    self::assertSame($bag->contactId, $recur['contact_id']);
    self::assertEquals('Pending', $recur['contribution_status_id:name']);
  }

  public function testPendingRecurWithoutContribution_WithOverrides_AppliesOverrides(): void {
    $bag = ContributionRecurScenario::pendingRecurWithoutContribution(
      contactOverrides: ['first_name' => 'Alice'],
      membershipOverrides: ['join_date' => '2022-01-01'],
      recurringOverrides: ['amount' => 99.99, 'currency' => 'USD']
    );

    $recur = ContributionRecur::get(FALSE)->addWhere('id', '=', $bag->recurringContributionId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame(99.99, $recur['amount']);
    self::assertSame('USD', $recur['currency']);
  }

  public function testPendingRecurWithPendingContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithPendingContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addWhere(
        'id',
        '=',
        $bag->recurringContributionId
      )->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, $recur['amount']);
    self::assertSame('EUR', $recur['currency']);
    self::assertEquals('Pending', $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()
      ->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertEquals('Pending', $contribution['contribution_status_id:name']);
  }

  public function testPendingRecurWithCompletedContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithCompletedContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addWhere(
        'id',
        '=',
        $bag->recurringContributionId
      )->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, $recur['amount']);
    self::assertSame('EUR', $recur['currency']);
    //After completing the contribution the state changes to "In Progress"
    self::assertSame('In Progress', $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()
      ->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertSame('Completed', $contribution['contribution_status_id:name']);
  }

  public function testPendingRecurWithCancelledContribution_ReturnsBag(): void {
    $bag = ContributionRecurScenario::pendingRecurWithCancelledContribution();
    self::assertNotNull($bag->recurringContributionId);
    self::assertNotNull($bag->contributionId);

    $recur = ContributionRecur::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addWhere(
        'id',
        '=',
        $bag->recurringContributionId
      )->execute()->first();

    self::assertNotNull($recur, 'Recurring contribution was not created.');
    self::assertSame(10.0, $recur['amount']);
    self::assertSame('EUR', $recur['currency']);
    self::assertSame('Pending', $recur['contribution_status_id:name']);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $bag->contributionId)
      ->execute()
      ->first();

    self::assertNotNull($contribution, 'Pending contribution was not created.');
    self::assertSame('Cancelled', $contribution['contribution_status_id:name']);
  }

}
