<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core\Adapters;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\Adapters\CiviApiResultAdapter;

/**
 * @covers \Systopia\TestFixtures\Core\Adapters\CiviApiResultAdapter
 */
final class CiviApiResultAdapterTest extends TestCase {

  public function testFirst_WithValidRow_ReturnsRow(): void {
    /** @var array<string, mixed> $expectedRow */
    $expectedRow = ['id' => 123, 'foo' => 'bar'];

    // Fake API result with first()
    $apiResult = new class($expectedRow) {

      /** @var array<string, mixed> */
      private readonly array $row;

      /**
       * @param array<string, mixed> $row
       */
      public function __construct(array $row) {
        $this->row = $row;
      }

      /**
       * @return array<string, mixed>
       */
      public function first(): array {
        return $this->row;
      }

    };

    $adapter = new CiviApiResultAdapter($apiResult);

    self::assertSame($expectedRow, $adapter->first());
  }

  public function testFirst_WithNullRow_ReturnsNull(): void {
    // Fake API result returning null.
    $apiResult = new class {

      public function first(): null {
        return NULL;
      }

    };

    $adapter = new CiviApiResultAdapter($apiResult);

    self::assertNull($adapter->first());
  }

}
