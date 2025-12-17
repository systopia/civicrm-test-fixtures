<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Adapters;

use Systopia\TestFixtures\Core\Interfaces\ApiResultInterface;

/**
 * Adapter around a CiviCRM APIv4 result object.
 *
 * This adapter normalizes access to APIv4 results so callers depend only on
 * ApiResultInterface instead of concrete APIv4 result implementations.
 */
final class CiviApiResultAdapter implements ApiResultInterface {

  /**
   * Underlying APIv4 result object.
   *
   * Kept intentionally generic: in production this is a real APIv4 result,
   * in tests it may be a fake providing a compatible first() method.
   *
   * @var object
   */
  private object $result;

  /**
   * @param mixed $result
   *   APIv4 result object providing a first() method.
   */
  public function __construct(object $result) {
    $this->result = $result;
  }

  /**
   * Return the first result row, if any.
   *
   * @return array<string, mixed>|null
   *   The first row returned by the API call, or NULL if no rows were returned.
   */
  public function first(): ?array {
    /** @var array<string, mixed>|null $row */
    $row = $this->result->first();

    return $row;
  }

}
