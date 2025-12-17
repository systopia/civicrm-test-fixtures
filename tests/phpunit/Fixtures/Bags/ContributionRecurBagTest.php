<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Bags;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
 */
final class ContributionRecurBagTest extends TestCase {

  /**
   *
   */
  public function testConstructor_WithValidValues_AssignsProperties(): void {
    $bag = new ContributionRecurBag(
      contactId: 1, membershipId: 2, recurringContributionId: 3
    );

    self::assertSame(1, $bag->contactId);
    self::assertSame(2, $bag->membershipId);
    self::assertSame(3, $bag->recurringContributionId);
  }

  /**
   *
   */
  public function testConstructor_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContributionRecurBag(contactId: 0);
  }

  /**
   *
   */
  public function testConstructor_WithInvalidMembershipId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContributionRecurBag(contactId: 1, membershipId: 0);
  }

  /**
   *
   */
  public function testConstructor_WithInvalidRecurringContributionId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContributionRecurBag(contactId: 1, recurringContributionId: 0);
  }

  /**
   *
   */
  public function testFromIds_WithValidValues_ReturnsBag(): void {
    $bag = ContributionRecurBag::fromIds(
      contactId: 10,
      membershipId: 20,
      recurringContributionId: 30
    );

    self::assertSame(10, $bag->contactId);
    self::assertSame(20, $bag->membershipId);
    self::assertSame(30, $bag->recurringContributionId);
  }

  /**
   *
   */
  public function testToArray_WithValidBag_ReturnsSchemaConformArray(): void {
    $bag = new ContributionRecurBag(
      contactId: 1, membershipId: 2, recurringContributionId: 3
    );

    self::assertSame([
      'contactId' => 1,
      'membershipId' => 2,
      'recurringContributionId' => 3,
    ], $bag->toArray());
  }

}
