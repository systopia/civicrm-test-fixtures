<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Scenarios;

use Systopia\TestFixtures\Fixtures\Bags\ContributionBag;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

/**
 * Scenario helpers for contribution-related test data.
 *
 * Scenarios coordinate multiple builders and return a fixture bag that exposes
 * the created entity IDs in a stable, validated schema.
 */
final class ContributionScenario {

  /**
   * Create a default contact, a membership, and a pending contribution.
   *
   * This is a common test setup for contribution-related features and ensures
   * that all related entities are created consistently.
   *
   * Each override array is merged into the respective builder defaults.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $contributionOverrides
   *   Optional overrides for {@see ContributionBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionBag
   *   A fixture bag containing: contactId, membershipId, contributionId.
   */
  public static function contactWithMembershipAndOpenContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $contributionOverrides = [],
  ): ContributionBag {
    $contactId = ContactBuilder::createDefault($contactOverrides);
    $membershipId = MembershipBuilder::createForContact($contactId, $membershipOverrides);
    $contributionId = ContributionBuilder::createPendingForContact($contactId, $contributionOverrides);

    return ContributionBag::fromIds(
      contactId: $contactId,
      membershipId: $membershipId,
      contributionId: $contributionId,
    );
  }

}
