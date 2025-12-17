<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Systopia\TestFixtures\Core\AbstractBaseFixtureBag;

/**
 * @covers \Systopia\TestFixtures\Core\AbstractBaseFixtureBag
 */
final class AbstractBaseFixtureBagTest extends TestCase {

  /**
   *
   */
  public function testToArray_ReturnsData_WhenSchemaAndExportMatch(): void {
    $bag = new class() extends AbstractBaseFixtureBag {

      /**
       *
       */
      public static function schema(): array {
        return ['contactId', 'membershipId', 'contributionId'];
      }

      /**
       *
       */
      protected function export(): array {
        return [
          'contactId' => 123,
          'membershipId' => 456,
          'contributionId' => 789,
        ];
      }

    };

    self::assertSame([
      'contactId' => 123,
      'membershipId' => 456,
      'contributionId' => 789,
    ], $bag->toArray());
  }

  /**
   *
   */
  public function testToArray_AllowsNullValues(): void {
    $bag = new class() extends AbstractBaseFixtureBag {

      /**
       *
       */
      public static function schema(): array {
        return ['contactId', 'membershipId', 'contributionId'];
      }

      /**
       *
       */
      protected function export(): array {
        return [
          'contactId' => 123,
          'membershipId' => NULL,
          'contributionId' => NULL,
        ];
      }

    };

    self::assertSame([
      'contactId' => 123,
      'membershipId' => NULL,
      'contributionId' => NULL,
    ], $bag->toArray());
  }

  /**
   *
   */
  public function testToArray_WithMissingSchemaKeys_ThrowsException(): void {
    $bag = new class() extends AbstractBaseFixtureBag {

      /**
       *
       */
      public static function schema(): array {
        return ['a', 'b'];
      }

      /**
       *
       */
      protected function export(): array {
        return ['a' => 1];
      }

    };

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('export missing keys');
    $bag->toArray();
  }

  /**
   *
   */
  public function testToArray_WithExtraSchemaKeys_ThrowsException(): void {
    $bag = new class() extends AbstractBaseFixtureBag {

      /**
       *
       */
      public static function schema(): array {
        return ['a'];
      }

      /**
       *
       */
      protected function export(): array {
        // Extra 'b'.
        return ['a' => 1, 'b' => 2];
      }

    };

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('export has extra keys');
    $bag->toArray();
  }

}
