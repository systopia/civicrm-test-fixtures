<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Scenarios;

use InvalidArgumentException;
use Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag;
use Systopia\TestFixtures\Fixtures\Builders\ContactBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionBuilder;
use Systopia\TestFixtures\Fixtures\Builders\ContributionRecurBuilder;
use Systopia\TestFixtures\Fixtures\Builders\MembershipBuilder;

/**
 * Scenario helpers for recurring contribution test data.
 *
 * Scenarios orchestrate multiple builders and return a fixture bag that contains
 * the relevant entity IDs in a stable, validated schema.
 *
 * This scenario supports two variants:
 * - recurring contribution without an initial contribution
 * - recurring contribution with an initial (pending) contribution
 *
 * The public API exposes these variants explicitly, while a shared private
 * factory method avoids duplication of setup logic.
 */
final class ContributionRecurScenario {

  /**
   * Universal entry point:
   * - Defaults are encoded as default method names.
   * - Variants are selected by passing a different method name.
   * - Overrides work consistently for every entity.
   *
   * @param array<string, mixed> $contactOverrides
   *   Overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   * @param array<string, mixed>|null $contributionOverrides
   *   Overrides for {@see ContributionBuilder::createPendingForContact()},
   *   or NULL if no contribution should be created.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, contributionId (optional),
   *   recurringContributionId.
   */
  private static function create(
    array $contactOverrides,
    array $membershipOverrides,
    array $recurringOverrides,
    ?array $contributionOverrides = NULL,
    string $contactMethod = 'createDefault',
    string $membershipMethod = 'createForContact',
    string $recurringMethod = 'createPendingForContact',
    ?string $contributionMethod = NULL,
  ): ContributionRecurBag {
    // Contact
    if (!method_exists(ContactBuilder::class, $contactMethod)) {
      throw new InvalidArgumentException(
        sprintf(
          'Unknown builder method %s::%s()',
          ContactBuilder::class,
          $contactMethod,
        )
      );
    }
    /** @var int $contactId */
    $contactId = ContactBuilder::{$contactMethod}($contactOverrides);

    // Membership
    if (!method_exists(MembershipBuilder::class, $membershipMethod)) {
      throw new InvalidArgumentException(
        sprintf(
          'Unknown builder method %s::%s()',
          MembershipBuilder::class,
          $membershipMethod,
        )
      );
    }
    /** @var int $membershipId */
    $membershipId = MembershipBuilder::{$membershipMethod}($contactId, $membershipOverrides);

    // Recurring contribution
    if (!method_exists(ContributionRecurBuilder::class, $recurringMethod)) {
      throw new InvalidArgumentException(
        sprintf(
          'Unknown builder method %s::%s()',
          ContributionRecurBuilder::class,
          $recurringMethod,
        )
      );
    }
    /** @var int $recurId */
    $recurId = ContributionRecurBuilder::{$recurringMethod}($contactId, $recurringOverrides);

    // Optional contribution
    $contributionId = NULL;
    if ($contributionMethod !== NULL) {
      if (!method_exists(ContributionBuilder::class, $contributionMethod)) {
        throw new InvalidArgumentException(
          sprintf(
            'Unknown builder method %s::%s()',
            ContributionBuilder::class,
            $contributionMethod,
          )
        );
      }
      $contributionOverrides['contribution_recur_id'] = $recurId;
      /** @var int $contributionId */
      $contributionId = ContributionBuilder::{$contributionMethod}($contactId, $contributionOverrides);
    }

    return ContributionRecurBag::fromIds(
      contactId: $contactId,
      membershipId: $membershipId,
      contributionId: $contributionId,
      recurringContributionId: $recurId,
    );
  }

  /**
   * Create a custom definition.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $contributionOverrides
   *   Optional overrides for {@see ContributionBuilder::createPendingForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Optional overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, contributionId,
   *   recurringContributionId.
   */
  public static function custom(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $contributionOverrides = [],
    array $recurringOverrides = [],
    string $contactMethod = 'createDefault',
    string $membershipMethod = 'createForContact',
    string $recurringMethod = 'createPendingForContact',
    ?string $contributionMethod = NULL,
  ): ContributionRecurBag {
    return self::create(
      contactOverrides: $contactOverrides,
      membershipOverrides: $membershipOverrides,
      recurringOverrides: $recurringOverrides,
      contributionOverrides: $contributionOverrides,
      contactMethod: $contactMethod,
      membershipMethod: $membershipMethod,
      recurringMethod: $recurringMethod,
      contributionMethod: $contributionMethod
    );
  }

  /**
   * Create a contact with a membership and a pending recurring contribution.
   *
   * This scenario represents the minimal valid setup for tests that operate
   * purely on recurring contributions without requiring an initial contribution.
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
  public static function pendingRecurWithoutContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $recurringOverrides = [],
  ): ContributionRecurBag {
    return self::create(
      contactOverrides: $contactOverrides,
      membershipOverrides: $membershipOverrides,
      recurringOverrides: $recurringOverrides
    );
  }

  /**
   * Create a contact with a membership, a pending contribution,
   * and a pending recurring contribution.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $contributionOverrides
   *   Optional overrides for {@see ContributionBuilder::createPendingForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Optional overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, contributionId,
   *   recurringContributionId.
   */
  public static function pendingRecurWithPendingContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $contributionOverrides = [],
    array $recurringOverrides = [],
  ): ContributionRecurBag {
    return self::create(
      contactOverrides: $contactOverrides,
      membershipOverrides: $membershipOverrides,
      recurringOverrides: $recurringOverrides,
      contributionOverrides: $contributionOverrides,
      contributionMethod: 'createPendingForContact',
    );
  }

  /**
   * Create a contact with a membership, a completed contribution,
   * and a pending recurring contribution.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $contributionOverrides
   *   Optional overrides for {@see ContributionBuilder::createPendingForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Optional overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, contributionId,
   *   recurringContributionId.
   */
  public static function pendingRecurWithCompletedContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $contributionOverrides = [],
    array $recurringOverrides = [],
  ): ContributionRecurBag {
    return self::create(
      contactOverrides: $contactOverrides,
      membershipOverrides: $membershipOverrides,
      recurringOverrides: $recurringOverrides,
      contributionOverrides: $contributionOverrides,
      contributionMethod: 'createCompletedForContact',
    );
  }

  /**
   * Create a contact with a membership, a cancelled contribution,
   * and a pending recurring contribution.
   *
   * @param array<string, mixed> $contactOverrides
   *   Optional overrides for {@see ContactBuilder::createDefault()}.
   * @param array<string, mixed> $membershipOverrides
   *   Optional overrides for {@see MembershipBuilder::createForContact()}.
   * @param array<string, mixed> $contributionOverrides
   *   Optional overrides for {@see ContributionBuilder::createPendingForContact()}.
   * @param array<string, mixed> $recurringOverrides
   *   Optional overrides for {@see ContributionRecurBuilder::createPendingForContact()}.
   *
   * @return \Systopia\TestFixtures\Fixtures\Bags\ContributionRecurBag
   *   A bag containing: contactId, membershipId, contributionId,
   *   recurringContributionId.
   */
  public static function pendingRecurWithCancelledContribution(
    array $contactOverrides = [],
    array $membershipOverrides = [],
    array $contributionOverrides = [],
    array $recurringOverrides = [],
  ): ContributionRecurBag {
    return self::create(
      contactOverrides: $contactOverrides,
      membershipOverrides: $membershipOverrides,
      recurringOverrides: $recurringOverrides,
      contributionOverrides: $contributionOverrides,
      contributionMethod: 'createCancelledForContact',
    );
  }

}
