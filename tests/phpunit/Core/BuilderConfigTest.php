<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\BuilderConfig;

/**
 * @covers \Systopia\TestFixtures\Core\BuilderConfig
 */
final class BuilderConfigTest extends TestCase {

  /**
   *
   */
  public function testConstruct_WithDefaults_SetsExpectedValues(): void {
    $config = new BuilderConfig();

    self::assertSame(1, $config->defaultFinancialTypeId);
    self::assertSame(1, $config->statusCompletedId);
    self::assertSame(2, $config->statusPendingId);
  }

  /**
   *
   */
  public function testConstruct_WithCustomValues_SetsExpectedValues(): void {
    $config = new BuilderConfig(
      defaultFinancialTypeId: 99, statusCompletedId: 10, statusPendingId: 20,
    );

    self::assertSame(99, $config->defaultFinancialTypeId);
    self::assertSame(10, $config->statusCompletedId);
    self::assertSame(20, $config->statusPendingId);
  }

}
