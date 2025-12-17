<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\MembershipType;
use InvalidArgumentException;
use RuntimeException;

/**
 * Builder for CiviCRM MembershipType entities.
 *
 * This builder is designed to work in empty test databases by creating a
 * minimal, valid membership type with sensible defaults.
 *
 * The created entity is persisted via APIv4 and the numeric ID is returned.
 */
final class MembershipTypeBuilder {

  /**
   * Create a minimal valid membership type and return its ID.
   *
   * The builder applies defaults and merges any overrides into them.
   *
   * Validation rules:
   * - If provided, `period_type` must be one of: "rolling", "fixed".
   * - If provided, `duration_interval` must be a positive integer.
   *
   * @param array<string, mixed> $overrides
   *   Optional overrides merged into the default payload.
   *
   * @return int
   *   The ID of the created membership type.
   *
   * @throws \InvalidArgumentException
   *   When invalid override values are provided.
   * @throws \CRM_Core_Exception
   *   When the API call fails at the CiviCRM level.
   * @throws \Civi\API\Exception\UnauthorizedException
   *   When the API call is not authorized.
   * @throws RuntimeException
   *   When the API does not return a valid ID.
   */
  public static function create(array $overrides = []): int {
    if (isset($overrides['period_type']) && !in_array($overrides['period_type'], ['rolling', 'fixed'], TRUE)) {
      throw new InvalidArgumentException('Invalid period_type');
    }

    if (isset($overrides['duration_interval']) &&
      (!is_int($overrides['duration_interval']) || $overrides['duration_interval'] <= 0)) {
      throw new InvalidArgumentException('duration_interval must be a positive integer');
    }

    $base = [
      'name' => 'Test Membership ' . uniqid(),
      'member_of_contact_id' => ContactBuilder::create(),
      'period_type' => 'rolling',
      'duration_unit' => 'year',
      'duration_interval' => 1,
      'minimum_fee' => 0,
      'financial_type_id' => 1,
    ];

    $data = array_replace_recursive($base, $overrides);

    /** @var array<string, mixed>|null $result */
    $result = MembershipType::create(FALSE)->setValues($data)->execute()->first();

    if ($result === NULL || !isset($result['id'])) {
      throw new RuntimeException('Failed to create entity via MembershipType::create().');
    }

    /** @var int $id */
    $id = $result['id'];

    return $id;
  }

}
