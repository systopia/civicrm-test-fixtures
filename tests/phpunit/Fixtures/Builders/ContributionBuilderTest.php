<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\Contribution;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder
 */
final class ContributionBuilderTest extends TestCase {

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
  public function testCreateForContact_CreatesContributionAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $contributionId);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);

    self::assertSame($contactId, (int) $contribution['contact_id']);
    self::assertSame('EUR', (string) $contribution['currency']);
    self::assertGreaterThan(0, (int) $contribution['financial_type_id']);
    self::assertGreaterThan(0, (int) $contribution['contribution_status_id']);
    self::assertNotEmpty($contribution['receive_date']);
    self::assertGreaterThan(0.0, (float) $contribution['total_amount']);
  }

  /**
   *
   */
  public function testCreateForContact_WithOverrides_AppliesOverrides(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createForContact($contactId, [
      'total_amount' => 42.50,
      'currency' => 'USD',
      'receive_date' => '2022-01-01 12:34:56',
    ]);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);
    self::assertSame(42.50, (float) $contribution['total_amount']);
    self::assertSame('USD', (string) $contribution['currency']);
    self::assertSame('2022-01-01 12:34:56', (string) $contribution['receive_date']);
  }

  /**
   *
   */
  public function testCreatePendingForContact_SetsPendingStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createPendingForContact($contactId);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $contributionId)
      ->execute()->first();

    self::assertNotNull($contribution);
    self::assertEquals('Pending', $contribution['contribution_status_id:name']);
  }

  /**
   *
   */
  public function testCreateCompletedForContact_SetsCompletedStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createCompletedForContact($contactId);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $contributionId)
      ->execute()->first();

    self::assertNotNull($contribution);
    self::assertEquals('Completed', $contribution['contribution_status_id:name']);
  }

  /**
   *
   */
  public function testCreateCancelledForContact_SetsCancelledStatus(): void {
    $contactId = ContactBuilder::create();
    $contributionId = ContributionBuilder::createCancelledForContact($contactId);

    $contribution = Contribution::get(FALSE)
      ->addSelect('contribution_status_id:name')
      ->addWhere('id', '=', $contributionId)
      ->execute()->first();

    self::assertNotNull($contribution);
    self::assertEquals('Cancelled', $contribution['contribution_status_id:name']);
  }

  /**
   *
   */
  public function testCreateForContact_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    ContributionBuilder::createForContact(0);
  }

}
