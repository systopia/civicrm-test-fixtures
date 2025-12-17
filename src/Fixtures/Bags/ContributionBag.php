<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Fixtures\Bags;

use Systopia\TestFixtures\Core\AbstractBaseFixtureBag;

/**
 * Fixture bag representing a contact with an optional membership
 * and an optional contribution.
 *
 * This bag is typically returned by contribution-related scenarios
 * and provides a stable, intention-revealing container for the IDs
 * created during the scenario.
 *
 * All values are validated eagerly to ensure that invalid test data
 * is detected immediately rather than leaking into later assertions.
 */
final class ContributionBag extends AbstractBaseFixtureBag {

  /**
   * @param int $contactId
   *   ID of the created contact (must be a positive integer).
   * @param int|null $membershipId
   *   Optional ID of the created membership (positive integer when provided).
   * @param int|null $contributionId
   *   Optional ID of the created contribution (positive integer when provided).
   *
   * @throws \InvalidArgumentException
   *   When any provided ID is not a positive integer.
   */
  public function __construct(
    public readonly int $contactId,
    public readonly ?int $membershipId = NULL,
    public readonly ?int $contributionId = NULL,
  ) {
    if ($contactId <= 0) {
      throw new \InvalidArgumentException('contactId must be a positive integer.');
    }

    if ($membershipId !== NULL && $membershipId <= 0) {
      throw new \InvalidArgumentException('membershipId must be a positive integer when provided.');
    }

    if ($contributionId !== NULL && $contributionId <= 0) {
      throw new \InvalidArgumentException('contributionId must be a positive integer when provided.');
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
    return ['contactId', 'membershipId', 'contributionId'];
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
   *
   * @return self
   */
  public static function fromIds(
    int $contactId,
    ?int $membershipId = NULL,
    ?int $contributionId = NULL,
  ): self {
    return new self($contactId, $membershipId, $contributionId);
  }

}
