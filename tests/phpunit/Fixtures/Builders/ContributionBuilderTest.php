<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\Contribution;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder
 * @group headless
 */
final class ContributionBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCreateForContact_CreatesContributionAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $contributionId);

    $contribution = Contribution::get(FALSE)->addSelect('*')->addSelect('contribution_status_id:name')->addSelect(
        'financial_type_id:name'
      )->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);

    self::assertSame($contactId, $contribution['contact_id']);
    self::assertSame('EUR', $contribution['currency']);
    self::assertSame('Donation', $contribution['financial_type_id:name']);
    self::assertSame('Pending', $contribution['contribution_status_id:name']);
    self::assertNotEmpty($contribution['receive_date']);
    self::assertGreaterThan(0.0, $contribution['total_amount']);
  }

  public function testCreateForContact_WithOverrides_AppliesOverrides(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createForContact($contactId, [
      'total_amount' => 42.50,
      'currency' => 'USD',
      'receive_date' => '2022-01-01 12:34:56',
    ]);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);
    self::assertSame(42.50, $contribution['total_amount']);
    self::assertSame('USD', $contribution['currency']);
    self::assertSame('2022-01-01 12:34:56', $contribution['receive_date']);
  }

  public function testCreatePendingForContact_SetsPendingStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createPendingForContact($contactId);

    $contribution =
      Contribution::get(FALSE)->addSelect('contribution_status_id:name')->addWhere('id', '=', $contributionId)->execute(
        )->first();

    self::assertNotNull($contribution);
    self::assertSame('Pending', $contribution['contribution_status_id:name']);
  }

  public function testCreateCompletedForContact_SetsCompletedStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createCompletedForContact($contactId);

    $contribution =
      Contribution::get(FALSE)->addSelect('contribution_status_id:name')->addWhere('id', '=', $contributionId)->execute(
        )->first();

    self::assertNotNull($contribution);
    self::assertSame('Completed', $contribution['contribution_status_id:name']);
  }

  public function testCreateCancelledForContact_SetsCancelledStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createCancelledForContact($contactId);

    $contribution =
      Contribution::get(FALSE)->addSelect('contribution_status_id:name')->addWhere('id', '=', $contributionId)->execute(
        )->first();

    self::assertNotNull($contribution);
    self::assertSame('Cancelled', $contribution['contribution_status_id:name']);
  }

  public function testCreateForContact_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    ContributionBuilder::createForContact(0);
  }

}
