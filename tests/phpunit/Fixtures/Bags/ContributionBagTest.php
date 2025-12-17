<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Tests\Fixtures\Bags;

use PHPUnit\Framework\TestCase;
use Systopia\TestFixtures\Fixtures\Bags\ContributionBag;

/**
 * @covers \Systopia\TestFixtures\Fixtures\Bags\ContributionBag
 */
final class ContributionBagTest extends TestCase {

  /**
   *
   */
  public function testConstructor_WithValidValues_AssignsProperties(): void {
    $bag = new ContributionBag(
      contactId: 123, membershipId: 456, contributionId: 789
    );

    self::assertSame(123, $bag->contactId);
    self::assertSame(456, $bag->membershipId);
    self::assertSame(789, $bag->contributionId);
  }

  /**
   *
   */
  public function testConstructor_WithInvalidContactId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContributionBag(
      contactId: 0, membershipId: 1, contributionId: NULL
    );
  }

  /**
   *
   */
  public function testConstructor_WithInvalidMembershipId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);

    new ContributionBag(
      contactId: 1, membershipId: 0, contributionId: NULL
    );
  }

  /**
   *
   */
  public function testConstructor_WithInvalidContributionId_ThrowsException(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContributionBag(
      contactId: 1, membershipId: NULL, contributionId: 0
    );
  }

  /**
   *
   */
  public function testFromIds_WithValidValues_ReturnsBag(): void {
    $bag = ContributionBag::fromIds(
      contactId: 10,
      membershipId: 20,
      contributionId: 30
    );

    self::assertSame(10, $bag->contactId);
    self::assertSame(20, $bag->membershipId);
    self::assertSame(30, $bag->contributionId);
  }

  /**
   *
   */
  public function testToArray_WithValidBag_ReturnsSchemaConformArray(): void {
    $bag = new ContributionBag(
      contactId: 1, membershipId: 2, contributionId: 3
    );

    self::assertSame([
      'contactId' => 1,
      'membershipId' => 2,
      'contributionId' => 3,
    ], $bag->toArray());
  }

}
