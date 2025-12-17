<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\FinancialType;
use RuntimeException;

/**
 * Builder for CiviCRM Financial Types.
 *
 * This builder is intentionally **not** based on AbstractBaseBuilder because
 * FinancialType creation is typically needed very early (often before other
 * fixtures exist) and should work in completely empty test databases.
 *
 * It creates a minimal, valid financial type and returns its numeric ID.
 */
final class FinancialTypeBuilder {

  /**
   * Create a minimal valid financial type and return its ID.
   *
   * The builder applies sensible defaults and allows selective overrides.
   *
   * Validation rules:
   * - If provided, `name` must be a non-empty string.
   *
   * @param array<string, mixed> $overrides
   *   Optional overrides merged into the default payload.
   *
   * @return int
   *   The ID of the created financial type.
   *
   * @throws \InvalidArgumentException
   *   When an invalid override is provided (e.g. empty name).
   * @throws \CRM_Core_Exception
   *   When the API call fails at the CiviCRM level.
   * @throws \Civi\API\Exception\UnauthorizedException
   *   When the API call is not authorized.
   * @throws \RuntimeException
   *   When the API does not return a valid ID.
   */
  public static function create(array $overrides = []): int {
    if (isset($overrides['name']) && is_string($overrides['name']) && trim($overrides['name']) === '') {
      throw new \InvalidArgumentException('FinancialType name must not be empty.');
    }

    $base = [
      'name' => 'Test Financial Type ' . uniqid('', TRUE),
      'is_active' => TRUE,
      'is_reserved' => FALSE,
    ];

    $data = array_replace_recursive($base, $overrides);

    $result = FinancialType::create(FALSE)->setValues($data)->execute()->first();

    if ($result === NULL || !isset($result['id'])) {
      throw new RuntimeException('Failed to create entity via %s::create()');
    }

    /** @var int $id */
    $id = $result['id'];

    return $id;
  }

}
