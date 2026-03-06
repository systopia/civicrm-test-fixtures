<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core;

use RuntimeException;
use Systopia\TestFixtures\Core\Factories\CiviApiFactory;
use Systopia\TestFixtures\Core\Interfaces\ApiFactoryInterface;

/**
 * Generic APIv4 entity builder base.
 *
 * Concrete builders provide:
 * - apiEntityClass(): class-string of the APIv4 entity (e.g. \Civi\Api4\Contact::class)
 * - defaults(): baseline values merged with overrides.
 *
 * Why the ApiCreateFactory exists:
 * - Production: we want the real APIv4 create action.
 * - Tests: we want to replace the API call with a mock/fake to unit-test BaseBuilder behavior
 *   without requiring a CiviCRM database.
 *
 * This keeps concrete builders simple (they only define defaults) and keeps BaseBuilder testable.
 */
abstract class AbstractBaseBuilder {

  /**
   * Factory used to obtain an APIv4 "create action".
   *
   * - In production, this defaults to CiviApi4Factory (lazy).
   * - In tests, you can inject a mock factory once via setApiFactory().
   *
   * Note: This is static on purpose. All builders are static and share the same API access strategy.
   */
  private static ?ApiFactoryInterface $apiFactory = NULL;

  /**
   * Inject a custom ApiCreateFactory (primarily for tests).
   *
   * Example (test):
   *   BaseBuilder::setApiFactory($mockFactory);
   */
  public static function setApiFactory(ApiFactoryInterface $factory): void {
    self::$apiFactory = $factory;
  }

  /**
   * Get the currently configured API factory.
   * Lazily instantiates the default factory if none was injected.
   */
  protected static function api(): ApiFactoryInterface {
    return self::$apiFactory ??= new CiviApiFactory();
  }

  /**
   * Return the APIv4 entity class name, e.g. \Civi\Api4\Contact::class.
   *
   * @return class-string
   */
  abstract protected static function defineApiEntityClass(): string;

  /**
   * Return default values for the entity. The passed overrides should already be merged in here.
   *
   * @param array<string, mixed> $overrides
   *
   * @return array<string, mixed>
   */
  abstract protected static function defineDefaults(array $overrides = []): array;

  /**
   * Create the entity via APIv4 and return its numeric ID.
   *
   * @param array<string, mixed> $overrides
   */
  final public static function create(array $overrides = []): int {
    $values = static::defineDefaults($overrides);

    $api = static::defineApiEntityClass();
    if (!class_exists($api)) {
      throw new RuntimeException(sprintf('API entity class not found: %s', $api));
    }

    /** @var array<string, mixed>|null $row */
    $row = static::api()->create($api)->setValues($values)->execute()->first();

    if ($row === NULL || !isset($row['id'])) {
      throw new RuntimeException(sprintf('Failed to create entity via %s::create()', $api));
    }

    /** @var int $id */
    $id = $row['id'];

    return $id;
  }

  /**
   * Create the entity using the builder defaults (optionally overridden) and return its numeric ID.
   * This is a convenience wrapper so concrete builders don't need to implement createDefault().
   *
   * @param array<string, mixed> $overrides
   */
  final public static function createDefault(array $overrides = []): int {
    return self::create($overrides);
  }

  /**
   * Generate a short unique token suitable for test data (names, external identifiers, etc.).
   *
   * @param string $prefix
   *
   * @return string
   */
  final protected static function uniqueToken(string $prefix = ''): string {
    $uniqueId = uniqid('', TRUE);
    $token = substr(str_replace('.', '', $uniqueId), -12);

    return $prefix . $token;
  }

}
