<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Core;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\FixtureEntityStore;

final class FixtureEntityStoreTest extends TestCase {

  protected function setUp(): void {
    FixtureEntityStore::reset();
  }

  public function testAddEntity_WithValidStore_ReturnsStore(): void {
    FixtureEntityStore::addEntity(
      'Civi\Api4\Contact',
      ['id' => 123, 'display_name' => 'Test']
    );

    $entities = FixtureEntityStore::getEntities();

    self::assertArrayHasKey('Civi\Api4\Contact', $entities);
    self::assertSame(123, $entities['Civi\Api4\Contact']['id']);
    self::assertSame('Test', $entities['Civi\Api4\Contact']['display_name']);
  }

  public function testAddEntity_OverridesExistingEntity(): void {
    FixtureEntityStore::addEntity(
      'Civi\Api4\Contact',
      ['id' => 123]
    );

    FixtureEntityStore::addEntity(
      'Civi\Api4\Contact',
      ['id' => 456]
    );

    $entities = FixtureEntityStore::getEntities();
    self::assertSame(456, $entities['Civi\Api4\Contact']['id']);
  }

  public function testReset_ClearsStore(): void {
    FixtureEntityStore::addEntity(
      'Civi\Api4\Contact',
      ['id' => 123]
    );

    FixtureEntityStore::reset();
    $entities = FixtureEntityStore::getEntities();
    self::assertSame([], $entities);
  }

  public function testGetEntities_WithEmptyStore_ReturnsEmptyArray(): void {
    $entities = FixtureEntityStore::getEntities();
    self::assertSame([], $entities);
  }

}
