<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Interfaces;

/**
 * Contract for fixture bags returned by scenarios.
 *
 * Fixture bags are immutable value objects that package one or more entity IDs
 * created during a scenario. They provide a stable, schema-driven way for tests
 * to consume scenario results.
 */
interface FixtureBagInterface {

  /**
   * Return the canonical schema for this bag.
   *
   * The schema defines the exact set and order of keys that will be present in
   * the exported array returned by toArray().
   *
   * @return non-empty-list<string>
   *   List of canonical keys for this bag.
   */
  public static function schema(): array;

  /**
   * Export the bag contents according to the canonical schema.
   *
   * Implementations must return an associative array whose keys exactly match
   * the schema defined by schema(). Values are expected to be numeric IDs or
   * NULL, depending on the scenario.
   *
   * @return array<string, int|null>
   *   Exported bag values keyed by schema.
   */
  public function toArray(): array;

}
