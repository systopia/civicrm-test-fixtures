<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\ContributionRecur;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Builder for CiviCRM Recurring Contributions (contribution_recur).
 *
 * This builder encapsulates the creation of recurring contributions with
 * sensible defaults and convenience methods.
 *
 * All public builder methods return the numeric ID of the created
 * contribution_recur entity.
 */
final class ContributionRecurBuilder extends AbstractBaseBuilder {

  /**
   * Return the APIv4 entity class handled by this builder.
   *
   * @return class-string
   */
  protected static function defineApiEntityClass(): string {
    return ContributionRecur::class;
  }

  /**
   * Define default values for recurring contribution creation.
   *
   * A contact_id is required and must be injected via {@see createForContact()}.
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
      'amount' => 10.00,
      'currency' => 'EUR',
      'financial_type_id:name' => 'Donation',
      'contribution_status_id:name' => 'Pending',

      // Frequency.
      'frequency_unit' => 'month',
      'frequency_interval' => 1,

      // Recurring dates.
      'start_date' => date('Y-m-d H:i:s'),
    ];

    return array_replace_recursive($base, $overrides);
  }

  /**
   * Create a recurring contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id (must be positive).
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created recurring contribution.
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
   * Create a pending recurring contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id.
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created recurring contribution.
   */
  public static function createPendingForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'contribution_status_id:name' => 'Pending',
    ], $overrides));
  }

  /**
   * Create a completed recurring contribution for the given contact.
   *
   * @param int $contactId
   *   Contact ID to assign to contact_id.
   * @param array<string, mixed> $overrides
   *   Optional payload overrides.
   *
   * @return int
   *   The ID of the created recurring contribution.
   */
  public static function createCompletedForContact(int $contactId, array $overrides = []): int {
    return self::createForContact($contactId, array_replace_recursive([
      'contribution_status_id:name' => 'Completed',
    ], $overrides));
  }

}
