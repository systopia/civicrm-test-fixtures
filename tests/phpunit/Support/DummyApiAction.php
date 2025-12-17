<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Support;

/**
 *
 */
final class DummyApiAction {

  //@phpstan-ignore-next-line property.alwaysReadWrittenProperties
  private array $values = [];

  /**
   * @param array<string, mixed> $values
   */
  public function setValues(array $values): self {
    $this->values = $values;

    return $this;
  }

  /**
   *
   */
  public function execute(): DummyApiResult {
    return new DummyApiResult();
  }

}
