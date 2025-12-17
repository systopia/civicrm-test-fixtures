<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Scenarios;

use Civi\Api4\Contact;
use Civi\Api4\Contribution;
use Civi\Api4\Membership;
use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Scenarios\ContributionScenario
 */
final class ContributionScenarioTest extends TestCase {

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
    if ($this->tx !== NULL) {
      $this->tx->rollback();
      $this->tx = NULL;
    }
    parent::tearDown();
  }

  /**
   *
   */
  public function testContactWithMembershipAndOpenContribution_CreatesAndReturnsBag(): void {
    $bag = ContributionScenario::contactWithMembershipAndOpenContribution();

    $data = $bag->toArray();

    $contactId = $data['contactId'] ?? NULL;
    $membershipId = $data['membershipId'] ?? NULL;
    $contributionId = $data['contributionId'] ?? NULL;

    self::assertIsInt($contactId);
    self::assertIsInt($membershipId);
    self::assertIsInt($contributionId);

    self::assertGreaterThan(0, $contactId);
    self::assertGreaterThan(0, $membershipId);
    self::assertGreaterThan(0, $contributionId);

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertSame($contactId, (int) $membership['contact_id']);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);
    self::assertSame($contactId, (int) $contribution['contact_id']);
  }

  /**
   *
   */
  public function testContactWithMembershipAndOpenContribution_AppliesOverrides(): void {
    $bag = ContributionScenario::contactWithMembershipAndOpenContribution(
      contactOverrides: [
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
      ],
      membershipOverrides: [
        'join_date' => '2022-01-01',
        'start_date' => '2022-01-02',
      ],
      contributionOverrides: [
        'total_amount' => 99.95,
        'currency' => 'USD',
      ],
    );

    $data = $bag->toArray();

    $contactId = $data['contactId'] ?? NULL;
    $membershipId = $data['membershipId'] ?? NULL;
    $contributionId = $data['contributionId'] ?? NULL;

    self::assertIsInt($contactId);
    self::assertIsInt($membershipId);
    self::assertIsInt($contributionId);

    $contact = Contact::get(FALSE)->addWhere('id', '=', $contactId)->execute()->first();

    self::assertNotNull($contact);
    self::assertSame('Ada', (string) ($contact['first_name'] ?? ''));
    self::assertSame('Lovelace', (string) ($contact['last_name'] ?? ''));

    $membership = Membership::get(FALSE)->addWhere('id', '=', $membershipId)->execute()->first();

    self::assertNotNull($membership);
    self::assertSame('2022-01-01', (string) $membership['join_date']);
    self::assertSame('2022-01-02', (string) $membership['start_date']);

    $contribution = Contribution::get(FALSE)->addWhere('id', '=', $contributionId)->execute()->first();

    self::assertNotNull($contribution);
    self::assertSame(99.95, (float) $contribution['total_amount']);
    self::assertSame('USD', (string) $contribution['currency']);
  }

}
