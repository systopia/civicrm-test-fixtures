<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Support;

use Systopia\TestFixtures\Core\AbstractBaseBuilder;

/**
 * Minimal concrete builder to exercise BaseBuilder behavior in unit tests.
 */
final class DummyBuilderAbstract extends AbstractBaseBuilder {

  protected static function defineApiEntityClass(): string {
    return DummyApi::class;
  }

  /**
   * @param array<string, mixed> $overrides
   *
   * @return array<string, mixed>
   */
  protected static function defineDefaults(array $overrides = []): array {
    $base = [
      'a' => 1,
      'nested' => [
        'x' => 'default',
      ],
    ];

    return array_replace_recursive($base, $overrides);
  }

  /**
   * Expose BaseBuilder::uniqueToken() for testing.
   */
  public static function uniqueTokenPublic(string $prefix = ''): string {
    return parent::uniqueToken($prefix);
  }

}
