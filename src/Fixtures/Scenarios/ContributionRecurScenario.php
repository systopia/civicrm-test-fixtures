<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Scenarios;

use Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

/**
 * Scenario helpers for recurring contribution test data.
 *
 * Scenarios orchestrate multiple builders and return a fixture bag that contains
 * the relevant entity IDs in a stable schema.
 */
final class ContributionRecurScenario {

  /**
   * Create a default contact, a membership, and a pending recurring contribution.
   *
   * This is a convenience scenario for common tests that require a basic setup of:
   * - Contact (created with {@see ContactBuilder})
   * - Membership for that contact (created with {@see MembershipBuilder})
   * - Pending recurring contribution for that contact (created with {@see ContributionRecurBuilder})
   *
   * Each override array is merged into the respective builder defaults.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Optional overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, recurringContributionId.
   */
  public static function contactWithMembershipAndPendingRecurringContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $recurringOverrides = [],
  ): ContributionRecurBag {
    $contactId = ContactBuilder::createDefault($contactOverrides);
    $membershipId = MembershipBuilder::createForContact($contactId, $membershipOverrides);
    $recurId = ContributionRecurBuilder::createPendingForContact($contactId, $recurringOverrides);

    return ContributionRecurBag::fromIds(
      contactId: $contactId,
      membershipId: $membershipId,
      recurringContributionId: $recurId,
    );
  }

}
