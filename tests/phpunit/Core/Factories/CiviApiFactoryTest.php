<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core\Factories;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\Factories\CiviApiFactory;
use Systopia\TestFixtures\Core\Interfaces\ApiActionInterface;
use Systopia\TestFixtures\Tests\Support\DummyApi;

/**
 * @covers \Systopia\TestFixtures\Core\Factories\CiviApiFactory
 */
final class CiviApiFactoryTest extends TestCase {

  /**
   *
   */
  public function testCreate_WithValidApiClass_ReturnsApiActionAdapter(): void {
    $factory = new CiviApiFactory();

    $action = $factory->create(DummyApi::class, FALSE);

    self::assertInstanceOf(ApiActionInterface::class, $action);
  }

  /**
   *
   */
  public function testCreate_WithInvalidApiClass_ThrowsRuntimeException(): void {
    $factory = new CiviApiFactory();

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('API entity class not found');

    //@phpstan-ignore-next-line class.notFound
    $factory->create(NonExistingTestClass::class);
  }

}
