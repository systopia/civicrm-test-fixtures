<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Core\FixtureEntityStore;
use Systopia\TestFixtures\Fixtures\Builders\SepaCreditorBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\SepaCreditorBuilder
 * @group headless
 */
final class SepaCreditorBuilderTest extends TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless(): CiviEnvBuilder {
    FixtureEntityStore::reset();

    return Test::headless()
      ->install('civi_campaign')
      ->install('org.project60.sepa')
      ->install('org.project60.banking')
      ->apply();
  }

  public function testCreate_CreatesCreditorAndReturnsId(): void {
    $creditorId = SepaCreditorBuilder::create();
    self::assertGreaterThan(0, $creditorId);

    $storedEntities = FixtureEntityStore::getEntities();
    $creditor = $storedEntities['Civi\Api4\SepaCreditor'];

    /** @var string $creditorIdentifier */
    $creditorIdentifier = $creditor['identifier'];
    self::assertStringStartsWith('DE98ZZZ', $creditorIdentifier);

    /** @var string $bic */
    $bic = $creditor['bic'];
    self::assertSame('TESTDEFFXXX', $bic);

    /** @var string $creditorName */
    $creditorName = $creditor['name'];
    self::assertStringStartsWith('Test Creditor ', $creditorName);
  }

  public function testCreate_WithOverrides_AppliesOverrides(): void {
    $creditorId = SepaCreditorBuilder::create([
      'name' => 'Max Mustermann',
      'label' => 'Maximilian Mustermann',
    ]);
    self::assertGreaterThan(0, $creditorId);

    $storedEntities = FixtureEntityStore::getEntities();
    $creditor = $storedEntities['Civi\Api4\SepaCreditor'];

    self::assertEquals('Max Mustermann', $creditor['name']);
    self::assertEquals('Maximilian Mustermann', $creditor['label']);
  }

}
