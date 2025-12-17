<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Interfaces;

/**
 * Factory interface for creating APIv4 "create actions".
 *
 * This abstraction allows builders to obtain API actions without depending
 * directly on concrete APIv4 classes. It also enables injecting fake or mock
 * factories in tests.
 */
interface ApiFactoryInterface {

  /**
   * Create an APIv4 create action for the given entity class.
   *
   * Implementations are expected to instantiate (or fake) an APIv4 create
   * action and return it wrapped as an ApiActionInterface.
   *
   * @param class-string $apiEntityClass
   *   Fully qualified APIv4 entity class name
   *   (e.g. \Civi\Api4\Contact::class).
   * @param bool $checkPermissions
   *   Whether the API call should enforce permission checks.
   *
   * @return ApiActionInterface
   */
  public function create(string $apiEntityClass, bool $checkPermissions = FALSE): ApiActionInterface;

}
