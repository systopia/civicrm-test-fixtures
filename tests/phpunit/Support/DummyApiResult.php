<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Support;

final class DummyApiResult {

  /**
   * @return array<string, int>
   */
  public function first(): array {
    return ['id' => 123];
  }

}
