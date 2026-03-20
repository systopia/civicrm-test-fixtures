<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core;

/**
 * Central in-memory store for fixture entities during test execution.
 *
 * This store is used to keep track of entities created by fixture builders
 * or scenarios, allowing later retrieval (e.g. for assertions or cross-references).
 *
 * Important:
 * - The store is static and therefore shared across the entire test runtime.
 * - Each entity is stored by its class name.
 * - Currently, only a single entity per class is stored (last write wins).
 *   Subsequent calls to {@see addEntity()} for the same class will overwrite
 *   the previous entry.
 *
 * Typical usage:
 * - Builders/scenarios register created entities
 * - Tests or other fixtures retrieve them via {@see getEntities()}
 * - {@see reset()} should be called between tests to avoid state leakage
 */
final class FixtureEntityStore {

  /**
   * Internal storage for fixture entities.
   *
   * Key:   Fully-qualified class name of the entity
   * Value: Entity data (usually API result or normalized fixture array)
   *
   * @var array<class-string, array<string,mixed>>
   */
  private static array $entities = [];

  /**
   * Store an entity instance for a given entity class.
   *
   * @param class-string $entityClass
   *   Fully-qualified class name of the entity (e.g. Contact::class).
   * @param array<string,mixed> $entity
   *   Entity data as associative array.
   *
   * TODO: Make it possible to store and call entities by its entityName as well
   * TODO: Implement a way to reset the store automatically
   *
   * @return void
   */
  public static function addEntity(string $entityClass, array $entity): void {
    self::$entities[$entityClass] ??= [];
    self::$entities[$entityClass] = $entity;
  }

  /**
   * Retrieve all stored entities.
   *
   * @return array<class-string, array<string,mixed>>
   *   Map of entity class names to their stored entity data.
   */
  public static function getEntities(): array {
    return self::$entities;
  }

  /**
   * Clears all stored entities. Should be called between tests to ensure
   * isolation and prevent cross-test contamination.
   *
   * @return void
   */
  public static function reset(): void {
    self::$entities = [];
  }

}
