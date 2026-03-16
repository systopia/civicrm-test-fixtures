<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Builders;

use Civi\Api4\SepaCreditor;
use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Builder for SEPA creditor entities.
 *
 * This builder provides sensible defaults for creating SEPA creditors
 * in tests.
 *
 * All builder methods return the numeric creditor ID created via APIv4.
 */
final class SepaCreditorBuilder extends AbstractBaseBuilder {

  /**
   * Return the APIv4 entity class handled by this builder.
   *
   * @return class-string
   */
  protected static function defineApiEntityClass(): string {
    return SepaCreditor::class;
  }

  /**
   * Define default values for SEPA creditor creation.
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
    $token = self::uniqueToken('sc_');
    $shortToken = strtoupper(substr($token, -4));

    $base = [
      'identifier' => 'DE98ZZZ' . strtoupper(substr(md5($token), 0, 11)),
      'label' => 'Test Creditor ' . $token,
      'name' => 'Test Creditor ' . $token,
      'iban' => 'DE12500105170648489890',
      'bic' => 'TESTDEFFXXX',
      'mandate_prefix' => $shortToken,
      'currency' => 'EUR',
      'category' => 'TEST',
      'mandate_active' => 1,
      'creditor_type' => 'SEPA',
      'pi_ooff' => '9',
      'pi_rcur' => '7-8',
      'uses_bic' => 1,
    ];

    return array_replace_recursive($base, $overrides);
  }

}
