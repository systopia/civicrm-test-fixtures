<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\Membership;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Builder for CiviCRM Membership entities.
 *
 * This builder provides sensible defaults for memberships and exposes
 * convenience methods for common test cases (e.g. active memberships).
 *
 * All methods create real CiviCRM entities via APIv4 and return the numeric ID.
 */
final class MembershipBuilder extends AbstractBaseBuilder {

  /**
   * Return the APIv4 entity class used by this builder.
   *
   * @return class-string
   */
  protected static function defineApiEntityClass(): string {
    return Membership::class;
  }

  /**
   * Define the default membership payload.
   *
   * Note:
   * - A membership type is created automatically if none is provided.
   * - The contact_id must be supplied by {@see createForContact()}.
   *
   * @param array<string, mixed> $overrides
   *   Values to override or extend the default payload.
   *
   * @return array<string, mixed>
   *   Fully merged APIv4 payload for membership creation.
   *
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  protected static function defineDefaults(array $overrides = []): array {
    $membershipTypeId = MembershipTypeBuilder::create();

    $base = [
      'membership_type_id' => $membershipTypeId,
      'join_date' => date('Y-m-d'),
      'start_date' => date('Y-m-d'),
    ];

    return array_replace_recursive($base, $overrides);
  }

  /**
   * Create a membership for the given contact.
   *
   * @param int $contactId
   *   The contact ID the membership belongs to.
   * @param array<string, mixed> $overrides
   *   Optional API field overrides.
   *
   * @return int
   *   The created membership ID.
   *
   * @throws \InvalidArgumentException
   *   If the contact ID is not a positive integer.
   */
  public static function createForContact(int $contactId, array $overrides = []): int {
    if ($contactId <= 0) {
      throw new \InvalidArgumentException('contactId must be a positive integer.');
    }

    return self::create(array_replace_recursive([
      'contact_id' => $contactId,
    ], $overrides));
  }

  /**
   * Create a membership that is likely considered "active" by date-based rules.
   *
   * This sets a start date in the past and an end date in the future,
   * without making assumptions about membership status logic.
   *
   * @param int $contactId
   *   The contact ID the membership belongs to.
   * @param array<string, mixed> $overrides
   *   Optional API field overrides.
   *
   * @return int
   *   The created membership ID.
   *
   * @throws \InvalidArgumentException
   *   If the contact ID is not a positive integer.
   */
  public static function createActiveForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'start_date' => date('Y-m-d', strtotime('-7 days')),
      'end_date' => date('Y-m-d', strtotime('+1 year')),
    ], $overrides));
  }

}
