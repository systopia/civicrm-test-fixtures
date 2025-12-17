<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\ContributionRecur;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder
 */
final class ContributionRecurBuilderTest extends TestCase {

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
  public function testCreateForContact_CreatesRecurringContributionAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $recurId);

    $recur = ContributionRecur::get(FALSE)->addWhere('id', '=', $recurId)->execute()->first();

    self::assertNotNull($recur);
    self::assertSame($contactId, (int) $recur['contact_id']);
    self::assertSame('EUR', (string) $recur['currency']);
    self::assertGreaterThan(0, (int) $recur['financial_type_id']);
    self::assertGreaterThan(0, (int) $recur['contribution_status_id']);
    self::assertSame('month', (string) $recur['frequency_unit']);
    self::assertSame(1, (int) $recur['frequency_interval']);
    self::assertNotEmpty($recur['start_date']);
    self::assertGreaterThan(0.0, (float) $recur['amount']);
  }

  /**
   *
   */
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
    self::assertSame(42.50, (float) $recur['amount']);
    self::assertSame('USD', (string) $recur['currency']);
    self::assertSame('year', (string) $recur['frequency_unit']);
    self::assertSame(2, (int) $recur['frequency_interval']);
    self::assertSame('2022-01-01 12:34:56', (string) $recur['start_date']);
  }

  /**
   *
   */
  public function testCreatePendingForContact_SetsPendingStatus(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createPendingForContact($contactId);

    $recur = ContributionRecur::get(FALSE)->addWhere('id', '=', $recurId)->execute()->first();

    self::assertNotNull($recur);
    self::assertGreaterThan(0, (int) $recur['contribution_status_id']);
  }

  /**
   *
   */
  public function testCreateCompletedForContact_SetsCompletedStatus(): void {
    $contactId = ContactBuilder::create();
    $recurId = ContributionRecurBuilder::createCompletedForContact($contactId);

    $recur = ContributionRecur::get(FALSE)->addWhere('id', '=', $recurId)->execute()->first();

    self::assertNotNull($recur);
    self::assertGreaterThan(0, (int) $recur['contribution_status_id']);
  }

  /**
   *
   */
  public function testCreateForContact_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    ContributionRecurBuilder::createForContact(0);
  }

}
