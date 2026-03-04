<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\FinancialType;
use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\FinancialTypeBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\FinancialTypeBuilder
 * @group headless
 */
final class FinancialTypeBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()->apply();
  }

  public function testCreate_CreatesFinancialTypeAndReturnsId(): void {
    $typeId = FinancialTypeBuilder::create();

    self::assertGreaterThan(0, $typeId);

    $type = FinancialType::get(FALSE)->addWhere('id', '=', $typeId)->execute()->first();

    self::assertNotNull($type);
    self::assertTrue((bool) $type['is_active']);
    self::assertFalse((bool) $type['is_reserved']);
    self::assertNotEmpty($type['name']);
  }

  public function testCreate_WithOverrides_AppliesOverrides(): void {
    $typeId = FinancialTypeBuilder::create([
      'name' => 'Custom Financial Type',
      'is_active' => FALSE,
    ]);

    $type = FinancialType::get(FALSE)->addWhere('id', '=', $typeId)->execute()->first();

    self::assertNotNull($type);
    self::assertArrayHasKey('name', $type);
    self::assertSame('Custom Financial Type', $type['name']);
    self::assertFalse((bool) $type['is_active']);
  }

  public function testCreate_WithEmptyName_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);

    FinancialTypeBuilder::create([
      'name' => '',
    ]);
  }

}
