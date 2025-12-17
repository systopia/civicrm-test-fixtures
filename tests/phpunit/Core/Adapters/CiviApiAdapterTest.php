<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core\Adapters;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\Adapters\CiviApiAdapter;
use Systopia\TestFixtures\Core\Interfaces\ApiResultInterface;

/**
 * @covers \Systopia\TestFixtures\Core\Adapters\CiviApiAdapter
 */
final class CiviApiAdapterTest extends TestCase {

  /**
   *
   */
  public function testExecute_ReturnsResultAdapter_AndFirstReturnsRow(): void {
    /** @var array<string, mixed> $expectedRow */
    $expectedRow = ['id' => 123];

    // Fake API result (has first())
    $apiResult = new class($expectedRow) {

      /** @var array<string, mixed> */
      private array $row;

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

    // Fake API action (has setValues + execute)
    $apiAction = new class($apiResult) {

      /** @var array<string, mixed> */
      public array $receivedValues = [];

      private object $result;

      public function __construct(object $result) {
        $this->result = $result;
      }

      /**
       * @param array<string, mixed> $values
       */
      public function setValues(array $values): self {
        $this->receivedValues = $values;

        return $this;
      }

      /**
       * @return object
       */
      public function execute(): object {
        return $this->result;
      }

    };

    $adapter = new CiviApiAdapter($apiAction);

    $result = $adapter->setValues(['foo' => 'bar'])->execute();

    self::assertInstanceOf(ApiResultInterface::class, $result);
    self::assertSame(['foo' => 'bar'], $apiAction->receivedValues);
    self::assertSame($expectedRow, $result->first());
  }

}
