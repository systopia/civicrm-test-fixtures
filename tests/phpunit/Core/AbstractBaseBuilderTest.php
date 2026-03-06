<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;
use Systopia\TestFixtures\Core\Interfaces\ApiActionInterface;
use Systopia\TestFixtures\Core\Interfaces\ApiFactoryInterface;
use Systopia\TestFixtures\Core\Interfaces\ApiResultInterface;
use Systopia\TestFixtures\Tests\Support\DummyApi;
use Systopia\TestFixtures\Tests\Support\DummyBuilderAbstract;

/**
 * @covers \Systopia\TestFixtures\Core\AbstractBaseBuilder
 */
final class AbstractBaseBuilderTest extends TestCase {

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreate_WithValidRow_ReturnsId(): void {
    $result = $this->createMock(ApiResultInterface::class);
    $result->method('first')->willReturn(['id' => 123]);

    $action = $this->createMock(ApiActionInterface::class);
    $action->method('setValues')->willReturnSelf();
    $action->method('execute')->willReturn($result);

    $factory = $this->createMock(ApiFactoryInterface::class);
    $factory->expects($this->once())->method('create')->with(DummyApi::class, FALSE)->willReturn($action);

    AbstractBaseBuilder::setApiFactory($factory);

    $id = DummyBuilderAbstract::create();

    self::assertSame(123, $id);
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreate_WithOverrides_PassesMergedValuesToApi(): void {
    $expectedValues = [
      'a' => 1,
      'nested' => [
        'x' => 'override',
      ],
    ];

    $result = $this->createMock(ApiResultInterface::class);
    $result->method('first')->willReturn(['id' => 123]);

    $action = $this->createMock(ApiActionInterface::class);
    $action->expects($this->once())->method('setValues')->with($this->equalTo($expectedValues))->willReturnSelf();

    $action->method('execute')->willReturn($result);

    $factory = $this->createMock(ApiFactoryInterface::class);
    $factory->method('create')->willReturn($action);

    AbstractBaseBuilder::setApiFactory($factory);

    DummyBuilderAbstract::create([
      'nested' => ['x' => 'override'],
    ]);
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreate_WithMissingApiClass_ThrowsRuntimeException(): void {
    $builder = new class extends AbstractBaseBuilder {

      protected static function defineApiEntityClass(): string {
        return 'NonExistentApiClass';
      }

      protected static function defineDefaults(array $overrides = []): array {
        return [];
      }

    };

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('API entity class not found');

    $builder::create();
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreate_WithNullRow_ThrowsRuntimeException(): void {
    $result = $this->createMock(ApiResultInterface::class);
    $result->method('first')->willReturn(NULL);

    $action = $this->createMock(ApiActionInterface::class);
    $action->method('setValues')->willReturnSelf();
    $action->method('execute')->willReturn($result);

    $factory = $this->createMock(ApiFactoryInterface::class);
    $factory->method('create')->willReturn($action);

    AbstractBaseBuilder::setApiFactory($factory);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Failed to create entity');

    DummyBuilderAbstract::create();
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreate_WithRowMissingId_ThrowsRuntimeException(): void {
    $result = $this->createMock(ApiResultInterface::class);
    $result->method('first')->willReturn(['foo' => 'bar']);

    $action = $this->createMock(ApiActionInterface::class);
    $action->method('setValues')->willReturnSelf();
    $action->method('execute')->willReturn($result);

    $factory = $this->createMock(ApiFactoryInterface::class);
    $factory->method('create')->willReturn($action);

    AbstractBaseBuilder::setApiFactory($factory);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Failed to create entity');

    DummyBuilderAbstract::create();
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testCreateDefault_WithValidRow_ReturnsId(): void {
    $result = $this->createMock(ApiResultInterface::class);
    $result->method('first')->willReturn(['id' => 123]);

    $action = $this->createMock(ApiActionInterface::class);
    $action->method('setValues')->willReturnSelf();
    $action->method('execute')->willReturn($result);

    $factory = $this->createMock(ApiFactoryInterface::class);
    $factory->method('create')->willReturn($action);

    AbstractBaseBuilder::setApiFactory($factory);

    $id = DummyBuilderAbstract::createDefault();

    self::assertSame(123, $id);
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testUniqueToken_WithPrefix_ReturnsPrefixedToken(): void {
    $token = DummyBuilderAbstract::uniqueTokenPublic('x_');

    self::assertStringStartsWith('x_', $token);
    self::assertGreaterThan(strlen('x_'), strlen($token));
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testUniqueToken_WithTwoCalls_ReturnsDifferentTokens(): void {
    $a = DummyBuilderAbstract::uniqueTokenPublic();
    $b = DummyBuilderAbstract::uniqueTokenPublic();

    self::assertNotSame($a, $b);
  }

}
