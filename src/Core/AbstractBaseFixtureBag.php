<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core;

use RuntimeException;
use Systopia\TestFixtures\Core\Interfaces\FixtureBagInterface;

/**
 * Base class for scenario return objects ("fixture bags").
 *
 * Fixture bags are immutable value objects that expose scenario results
 * in a canonical, schema-driven array format.
 *
 * This base class enforces:
 * - exact schema compliance (no missing or extra keys)
 * - a single, stable export method for consumers
 *
 * Concrete bags are expected to:
 * - define their schema via FixtureBagInterface::schema()
 * - implement export() to return IDs matching that schema
 */
abstract class AbstractBaseFixtureBag implements FixtureBagInterface {

  /**
   * Export the bag contents according to the canonical schema.
   *
   * This method validates that:
   * - all keys defined in the schema are present
   * - no additional keys are returned
   *
   * Implementations should not override this method.
   *
   * @return array<string, int|null>
   *   Associative array of IDs keyed by the canonical schema.
   *
   * @throws RuntimeException
   *   Thrown if the exported data is missing required keys or contains
   *   keys not defined in the schema.
   */
  final public function toArray(): array {
    $data = $this->export();

    $schema = static::schema();

    $missing = array_diff($schema, array_keys($data));
    if ($missing !== []) {
      throw new RuntimeException(
        sprintf(
          'Bag %s export missing keys: %s',
          static::class,
          implode(', ', $missing)
        )
      );
    }

    $extra = array_diff(array_keys($data), $schema);
    if ($extra !== []) {
      throw new RuntimeException(
        sprintf(
          'Bag %s export has extra keys not in schema: %s',
          static::class,
          implode(', ', $extra)
        )
      );
    }

    return $data;
  }

  /**
   * Return the raw exported values for this bag.
   *
   * Concrete implementations must return an associative array whose keys
   * exactly match the schema defined by schema().
   *
   * Values must be numeric IDs or NULL, depending on the scenario.
   *
   * @return array<string, int|null>
   *   Raw exported bag values keyed by schema.
   */
  abstract protected function export(): array;

}
