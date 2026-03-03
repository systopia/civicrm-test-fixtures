<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\Contribution;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Builder for CiviCRM Contribution entities.
 *
 * This builder provides sensible defaults and convenience methods for
 * creating contributions for a given contact.
 *
 * All builder methods return the numeric contribution ID created via APIv4.
 */
final class ContributionBuilder extends AbstractBaseBuilder {

  /**
   * Return the APIv4 entity class handled by this builder.
   *
   * @return class-string
   */
  protected static function defineApiEntityClass(): string {
    return Contribution::class;
  }

  /**
   * Define default values for contribution creation.
   *
   * Note: A contribution requires a contact ID. This builder expects callers
   * to provide it via {@see createForContact()}, which injects contact_id.
   *
   * Passed overrides are merged recursively into these defaults.
   *
   * @param array<string, mixed> $overrides
   *   Values to override the default payload.
   *
   * @return array<string, mixed>
   *   Final payload passed to the APIv4 create action.
   */
  protected static function defineDefaults(array $overrides = []): array {
    $base = [
      'total_amount' => 10.00,
      'receive_date' => date('Y-m-d H:i:s'),
      'currency' => 'EUR',
      'financial_type_id:name' => 'Donation',
      'contribution_status_id:name' => 'Pending',
      'next_sched_contribution_date' => (new \DateTime('+1 month'))->format('Y-m-d H:i:s'),
    ];

    return array_replace_recursive($base, $overrides);
  }

  /**
   * Create a contribution for the given contact.
   *
   * This method ensures contact_id is set and delegates creation to the base
   * builder logic.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id (must be positive).
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created contribution.
   *
   * @throws \InvalidArgumentException
   *   When $contactId is not a positive integer.
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
   * Create a pending contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id.
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created contribution.
   */
  public static function createPendingForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'contribution_status_id:name' => 'Pending',
    ], $overrides));
  }

  /**
   * Create a completed contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id.
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created contribution.
   */
  public static function createCompletedForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'contribution_status_id:name' => 'Completed',
      'receive_date' => (new \DateTimeImmutable('-14 days'))->format('Y-m-d H:i:s'),
    ], $overrides));
  }

  /**
   * Create a cancelled contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id.
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created contribution.
   */
  public static function createCancelledForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'contribution_status_id:name' => 'Cancelled',
      'receive_date' => (new \DateTimeImmutable('-14 days'))->format('Y-m-d H:i:s'),
    ], $overrides));
  }

}
