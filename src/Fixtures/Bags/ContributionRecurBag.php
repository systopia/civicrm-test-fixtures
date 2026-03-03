<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Bags;

use InvalidArgumentException;
use Systopia\TestFixtures\Core\AbstractBaseFixtureBag;

/**
 * Fixture bag representing a contact with an optional membership
 * and an optional recurring contribution.
 *
 * This bag is typically returned by recurring-contribution scenarios
 * and provides a stable, immutable container for the IDs created
 * during the scenario execution.
 *
 * All IDs are validated eagerly to ensure invalid test data is caught
 * immediately and does not leak into later assertions.
 */
final class ContributionRecurBag extends AbstractBaseFixtureBag {

  /**
   * @param int $contactId
   *   ID of the created contact (must be a positive integer).
   * @param int|null $membershipId
   *   Optional ID of the created membership (positive integer when provided).
   * @param int|null $recurringContributionId
   *   Optional ID of the created recurring contribution
   *   (positive integer when provided).
   *
   * @throws \InvalidArgumentException
   *   When any provided ID is not a positive integer.
   */
  public function __construct(
    public readonly int $contactId,
    public readonly ?int $membershipId = NULL,
    public readonly ?int $contributionId = NULL,
    public readonly ?int $recurringContributionId = NULL,
  ) {
    if ($contactId <= 0) {
      throw new InvalidArgumentException('contactId must be a positive integer.');
    }

    if ($membershipId !== NULL && $membershipId <= 0) {
      throw new InvalidArgumentException('membershipId must be a positive integer when provided.');
    }

    if ($contributionId !== NULL && $contributionId <= 0) {
      throw new InvalidArgumentException('contributionId must be a positive integer when provided.');
    }

    if ($recurringContributionId !== NULL && $recurringContributionId <= 0) {
      throw new InvalidArgumentException('recurringContributionId must be a positive integer when provided.');
    }
  }

  /**
   * Return the canonical schema for this bag.
   *
   * The schema defines the exact set and order of keys that must be
   * returned by {@see export()} and {@see toArray()}.
   *
   * @return non-empty-list<string>
   */
  public static function schema(): array {
    return ['contactId', 'membershipId', 'recurringContributionId', 'contributionId'];
  }

  /**
   * Export the bag contents in canonical schema form.
   *
   * @return array<string, int|null>
   */
  protected function export(): array {
    return [
      'contactId' => $this->contactId,
      'membershipId' => $this->membershipId,
      'recurringContributionId' => $this->recurringContributionId,
      'contributionId' => $this->contributionId,
    ];
  }

  /**
   * Convenience factory for creating a bag from known IDs.
   *
   * This is typically used by scenarios after creating entities
   * via builders.
   *
   * @param int $contactId
   *   ID of the created contact.
   * @param int|null $membershipId
   *   Optional membership ID.
   * @param int|null $contributionId
   *   Optional contribution ID.
   * @param int|null $recurringContributionId
   *   Optional recurring contribution ID.
   *
   * @return self
   */
  public static function fromIds(
    int $contactId,
    ?int $membershipId = NULL,
    ?int $contributionId = NULL,
    ?int $recurringContributionId = NULL,
  ): self {
    return new self($contactId, $membershipId, $contributionId, $recurringContributionId);
  }

}
