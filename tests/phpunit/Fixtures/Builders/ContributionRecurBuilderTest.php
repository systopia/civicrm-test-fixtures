<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\ContributionRecur;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder
 * @group headless
 */
final class ContributionRecurBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCreateForContact_CreatesRecurringContributionAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $recurId);

    $recur = ContributionRecur::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addSelect(
        'financial_type_id:name'
      )->addWhere('id', '=', $recurId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame($contactId, $recur['contact_id']);
    self::assertSame('EUR', $recur['currency']);
    self::assertSame('Donation', $recur['financial_type_id:name']);
    self::assertSame('Pending', $recur['contribution_status_id:name']);
    self::assertSame('month', $recur['frequency_unit']);
    self::assertSame(1, $recur['frequency_interval']);
    self::assertNotEmpty($recur['start_date']);
    self::assertGreaterThan(0.0, $recur['amount']);
  }

  public function testCreateForContact_WithOverrides_AppliesOverrides(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createForContact($contactId, [
      'amount' => 42.50,
      'currency' => 'USD',
      'frequency_unit' => 'year',
      'frequency_interval' => 2,
      'start_date' => '2022-01-01 12:34:56',
    ]);

    $recur = ContributionRecur::get(FALSE)->addWhere('id', '=', $recurId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame(42.50, $recur['amount']);
    self::assertSame('USD', $recur['currency']);
    self::assertSame('year', $recur['frequency_unit']);
    self::assertSame(2, $recur['frequency_interval']);
    self::assertSame('2022-01-01 12:34:56', $recur['start_date']);
  }

  public function testCreatePendingForContact_SetsPendingStatus(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createPendingForContact($contactId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $recurId)
      ->execute()
      ->first();

    self::assertNotNull($recur);
    self::assertSame('Pending', $recur['contribution_status_id:name']);
  }

  public function testCreateCompletedForContact_SetsCompletedStatus(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createCompletedForContact($contactId);

    $recur = ContributionRecur::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $recurId)
      ->execute()
      ->first();

    self::assertNotNull($recur);
    self::assertSame('Completed', $recur['contribution_status_id:name']);
  }

  public function testCreateForContact_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    ContributionRecurBuilder::createForContact(0);
  }

}
