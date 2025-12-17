<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Interfaces;

/**
 * Minimal contract for an APIv4 "create action".
 *
 * This interface abstracts the fluent APIv4 create workflow so builders can
 * operate against a small, testable surface instead of concrete APIv4 classes.
 *
 * Typical usage pattern:
 * - setValues()
 * - execute()
 * - consume result via ApiResultInterface
 */
interface ApiActionInterface {

  /**
   * Set values on the API action.
   *
   * Implementations are expected to forward these values to the underlying
   * APIv4 action and return $this to allow fluent chaining.
   *
   * @param array<string, mixed> $values
   *   Key-value pairs passed to the API create call.
   *
   * @return self
   */
  public function setValues(array $values): self;

  /**
   * Execute the API action.
   *
   * @return ApiResultInterface
   *   Adapter around the APIv4 result object.
   */
  public function execute(): ApiResultInterface;

}
