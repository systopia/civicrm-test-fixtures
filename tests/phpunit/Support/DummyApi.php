<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Support;

class DummyApi {

  public static function create(bool $checkPermissions = FALSE): object {
    return new DummyApiAction();
  }

}
