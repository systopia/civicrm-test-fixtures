<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Support;

final class DummyApiAction {

  public function setValues(): self {
    return $this;
  }

  public function execute(): DummyApiResult {
    return new DummyApiResult();
  }

}
