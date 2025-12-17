<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Interfaces;

/**
 * Minimal contract for an APIv4 result object.
 *
 * This interface abstracts access to APIv4 result objects so callers do not
 * depend on concrete APIv4 result implementations.
 */
interface ApiResultInterface {

  /**
   * Return the first result row, if available.
   *
   * Implementations are expected to mirror the behavior of APIv4 results:
   * - return an associative array for the first row
   * - return NULL if the result set is empty
   *
   * @return array<string, mixed>|null
   *   The first result row, or NULL if no rows were returned.
   */
  public function first(): ?array;

}
