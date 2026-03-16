<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core;

final class FixtureEntityStore {

  /**
   * @var array<class-string, array<string,mixed>>
   */
  private static array $entities = [];

  /**
   * Adds an entry to the Store.
   *
   * @param class-string $entityClass
   * @param array<string,mixed> $entity
   */
  public static function addEntity(string $entityClass, array $entity): void {
    self::$entities[$entityClass] ??= [];
    self::$entities[$entityClass] = $entity;
  }

  /**
   * Retrieves all entities from the store.
   *
   * @return array<class-string, array<string,mixed>>
   */
  public static function getEntities(): array {
    return self::$entities;
  }

  /**
   * Resets the EntityStorage
   *
   * @return void
   */
  public static function reset(): void {
    self::$entities = [];
  }

}
