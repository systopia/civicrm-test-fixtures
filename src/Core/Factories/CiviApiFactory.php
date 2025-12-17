<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Factories;

use RuntimeException;
use Systopia\TestFixtures\Core\Adapters\CiviApiAdapter;
use Systopia\TestFixtures\Core\Interfaces\ApiActionInterface;
use Systopia\TestFixtures\Core\Interfaces\ApiFactoryInterface;

/**
 * Factory for creating APIv4 "create actions".
 *
 * This factory encapsulates the instantiation of APIv4 create actions so that
 * builders can remain decoupled from concrete APIv4 classes.
 *
 * In production, this factory returns adapters around real APIv4 actions.
 * In tests, a different implementation can be injected to return fakes.
 */
final class CiviApiFactory implements ApiFactoryInterface {

  /**
   * Create an APIv4 create action for the given entity class.
   *
   * The given class must exist and provide a static create() method compatible
   * with the CiviCRM APIv4 fluent interface.
   *
   * @param class-string $apiEntityClass
   *   Fully qualified APIv4 entity class name
   *   (e.g. \Civi\Api4\Contact::class).
   * @param bool $checkPermissions
   *   Whether the API call should perform permission checks.
   *
   * @return \Systopia\TestFixtures\Core\Interfaces\ApiActionInterface
   *
   * @throws RuntimeException
   *   Thrown if the given API entity class does not exist.
   */
  public function create(string $apiEntityClass, bool $checkPermissions = FALSE): ApiActionInterface {
    if (!class_exists($apiEntityClass)) {
      throw new RuntimeException(sprintf('API entity class not found: %s', $apiEntityClass));
    }

    $action = $apiEntityClass::create($checkPermissions);

    return new CiviApiAdapter($action);
  }

}
