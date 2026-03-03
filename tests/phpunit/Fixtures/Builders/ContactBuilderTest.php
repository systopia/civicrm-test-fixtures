<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Builders;

use Civi\Api4\Contact;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Builders\ContactBuilder
 */
final class ContactBuilderTest extends TestCase {

  private ?\CRM_Core_Transaction $tx = NULL;

  /**
   *
   */
  protected function setUp(): void {
    parent::setUp();
    $this->tx = new \CRM_Core_Transaction();
  }

  /**
   *
   */
  protected function tearDown(): void {
    if ($this->tx instanceof \CRM_Core_Transaction) {
      $this->tx->rollback();
      $this->tx = NULL;
    }
    parent::tearDown();
  }

  /**
   *
   */
  public function testCreate_CreatesContactAndReturnsId(): void {
    $contactId = ContactBuilder::create();

    self::assertGreaterThan(0, $contactId);

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);
    self::assertSame('Individual', $contact['contact_type']);
    self::assertSame('Test', $contact['first_name']);
    self::assertStringStartsWith('User ', $contact['last_name']);
  }

  /**
   *
   */
  public function testCreate_WithOverrides_AppliesOverrides(): void {
    $contactId = ContactBuilder::create([
      'first_name' => 'Daniel',
      'last_name' => 'Hahn',
    ]);

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);
    self::assertSame('Daniel', $contact['first_name']);
    self::assertSame('Hahn', $contact['last_name']);
  }

  /**
   *
   */
  public function testCreateDeceased_CreatesDeceasedContact(): void {
    $contactId = ContactBuilder::createDeceased();

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);
    self::assertSame(TRUE, $contact['is_deceased']);
    self::assertSame('2020-01-01', $contact['deceased_date']);
  }

}
