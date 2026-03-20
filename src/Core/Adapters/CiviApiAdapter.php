<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core\Adapters;

use Systopia\TestFixtures\Core\Interfaces\ApiActionInterface;
use Systopia\TestFixtures\Core\Interfaces\ApiResultInterface;

/**
 * Adapter around a CiviCRM APIv4 "create action".
 *
 * This adapter normalizes the APIv4 fluent interface so the rest of the library
 * depends only on small, testable interfaces (ApiActionInterface/ApiResultInterface).
 *
 * Important: In APIv4, execute() returns a Result object. We must keep that result
 * and call first() on it (not on the action), otherwise callers may see null.
 */
final class CiviApiAdapter implements ApiActionInterface {

  /**
   * The underlying APIv4 action object (e.g. Civi\Api4\Action\*\Create).
   *
   * We keep this as "object" on purpose: production passes a real APIv4 action,
   * unit tests may pass a fake implementing the same methods.
   *
   * @var object
   */
  private object $action;

  /**
   * @param object $action
   *    APIv4 action object providing setValues() and execute().
   */
  public function __construct(object $action) {
    $this->action = $action;
  }

  /**
   * Set values on the underlying API action.
   *
   * @param array<string, mixed> $values
   *
   * @return self
   */
  public function setValues(array $values): self {
    // @phpstan-ignore method.notFound
    $this->action->setValues($values);
    return $this;
  }

  /**
   * Execute the underlying API action and wrap the result in an adapter.
   *
   * @return \Systopia\TestFixtures\Core\Interfaces\ApiResultInterface
   */
  public function execute(): ApiResultInterface {
    // @phpstan-ignore method.notFound
    $result = $this->action->execute();
    return new CiviApiResultAdapter($result);
  }

}
