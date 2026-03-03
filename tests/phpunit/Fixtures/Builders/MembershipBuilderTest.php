<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\Membership;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder
 */
final class MembershipBuilderTest extends TestCase {

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
  public function testCreateForContact_CreatesMembershipAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $membershipId = MembershipBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $membershipId);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertSame($contactId, (int) $membership['contact_id']);
    self::assertGreaterThan(0, (int) $membership['membership_type_id']);
    self::assertNotEmpty($membership['join_date']);
    self::assertNotEmpty($membership['start_date']);
  }

  /**
   *
   */
  public function testCreateForContact_WithOverrides_AppliesOverrides(): void {
    $contactId = ContactBuilder::create();
    $membershipId = MembershipBuilder::createForContact($contactId, [
      'join_date' => '2022-01-01',
      'start_date' => '2022-01-02',
    ]);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertArrayHasKey('join_date', $membership);
    self::assertArrayHasKey('start_date', $membership);
    self::assertSame('2022-01-01', $membership['join_date']);
    self::assertSame('2022-01-02', $membership['start_date']);
  }

  /**
   *
   */
  public function testCreateActiveForContact_SetsExpectedDateRange(): void {
    $contactId = ContactBuilder::create();
    $membershipId = MembershipBuilder::createActiveForContact($contactId);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertNotEmpty($membership['end_date']);
    self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) $membership['start_date']);
    self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) $membership['end_date']);
  }

  /**
   *
   */
  public function testCreateForContact_WithInvalidContactId_ThrowsException_(): void {
    $this->expectException(\InvalidArgumentException::class);
    MembershipBuilder::createForContact(0);
  }

}
