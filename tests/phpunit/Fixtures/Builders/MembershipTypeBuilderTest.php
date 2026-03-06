<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\MembershipType;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\MembershipTypeBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\MembershipTypeBuilder
 * @group headless
 */
final class MembershipTypeBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCreate_CreatesMembershipTypeAndReturnsId(): void {
    $typeId = MembershipTypeBuilder::create();

    self::assertGreaterThan(0, $typeId);

    $type = MembershipType::get(FALSE)->addWhere('id', '=', $typeId)->execute()->first();

    self::assertNotNull($type);
    self::assertSame('rolling', $type['period_type']);
    self::assertSame('year', $type['duration_unit']);
    self::assertSame(1, $type['duration_interval']);
    self::assertGreaterThan(0, $type['financial_type_id']);
  }

  public function testCreate_WithOverrides_AppliesOverrides(): void {
    $typeId = MembershipTypeBuilder::create([
      'name' => 'Custom Test Membership',
      'duration_interval' => 2,
    ]);

    $type = MembershipType::get(FALSE)->addWhere('id', '=', $typeId)->execute()->first();

    self::assertNotNull($type);
    self::assertArrayHasKey('name', $type);
    self::assertSame('Custom Test Membership', $type['name']);
    self::assertSame(2, $type['duration_interval']);
  }

  public function testCreate_WithInvalidPeriodType_ThrowsException(): void {
    $this->expectException(\Throwable::class);

    MembershipTypeBuilder::create([
      'period_type' => 'invalid',
    ]);
  }

  public function testCreate_WithInvalidDurationInterval_ThrowsException(): void {
    $this->expectException(\Throwable::class);

    MembershipTypeBuilder::create([
      'duration_interval' => 0,
    ]);
  }

}
