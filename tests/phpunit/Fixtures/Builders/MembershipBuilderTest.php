<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\Membership;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder
 * @group headless
 */
final class MembershipBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCreateForContact_CreatesMembershipAndReturnsId(): void {
    $contactId = ContactBuilder::create();
    $membershipId = MembershipBuilder::createForContact($contactId);

    self::assertGreaterThan(0, $membershipId);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertSame($contactId, $membership['contact_id']);
    self::assertGreaterThan(0, $membership['membership_type_id']);
    self::assertNotEmpty($membership['join_date']);
    self::assertNotEmpty($membership['start_date']);
  }

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

  public function testCreateActiveForContact_SetsExpectedDateRange(): void {
    $contactId = ContactBuilder::create();
    $membershipId = MembershipBuilder::createActiveForContact($contactId);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertNotEmpty($membership['end_date']);
    self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $membership['start_date']);
    self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $membership['end_date']);
  }

  public function testCreateForContact_WithInvalidContactId_ThrowsException_(): void {
    $this->expectException(\InvalidArgumentException::class);
    MembershipBuilder::createForContact(0);
  }

}
