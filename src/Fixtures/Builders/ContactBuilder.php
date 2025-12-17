<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\Contact;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Builder for CiviCRM Contact entities.
 *
 * This builder provides sensible defaults for creating individual contacts
 * in tests and exposes convenience methods for common variations
 * (e.g. deceased contacts).
 *
 * All builder methods return the numeric contact ID created via APIv4.
 */
final class ContactBuilder extends AbstractBaseBuilder {

  /**
   * Return the APIv4 entity class handled by this builder.
   *
   * @return class-string
   */
  protected static function defineApiEntityClass(): string {
    return Contact::class;
  }

  /**
   * Define default values for contact creation.
   *
   * Defaults to an Individual contact with a unique last name to
   * avoid collisions in test databases.
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
      'contact_type' => 'Individual',
      'first_name' => 'Test',
      'last_name' => 'User ' . self::uniqueToken('u_'),
    ];

    return array_replace_recursive($base, $overrides);
  }

  /**
   * Create a deceased contact.
   *
   * This is a convenience wrapper around {@see create()} that
   * applies the minimal required fields to mark a contact as deceased.
   *
   * Additional overrides may be provided and are merged on top.
   *
   * @param array<string, mixed> $overrides
   *   Optional overrides for the contact payload.
   *
   * @return int
   *   The ID of the created contact.
   */
  public static function createDeceased(array $overrides = []): int {
    return self::create(array_replace_recursive([
      'is_deceased' => TRUE,
      'deceased_date' => '2020-01-01',
    ], $overrides));
  }

}
