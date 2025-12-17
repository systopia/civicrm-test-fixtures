<?php

declare(strict_types = 1);

namespace Systopia\TestFixtures\Core;

/**
 * Configuration value object for builder defaults.
 *
 * This class centralizes commonly used numeric IDs that are required by
 * multiple builders (e.g. financial types or contribution statuses).
 *
 * The intent is to:
 * - avoid scattering magic numbers across builders
 * - provide a single place for adjusting installation-specific defaults
 *
 * All properties are read-only to keep the configuration immutable and
 * predictable during test execution.
 */
final class BuilderConfig {

  /**
   * @param int $defaultFinancialTypeId
   *   Default financial type ID used by contribution-related builders.
   * @param int $statusCompletedId
   *   Contribution status ID representing a "completed" state.
   * @param int $statusPendingId
   *   Contribution status ID representing a "pending/open" state.
   */
  public function __construct(
    public readonly int $defaultFinancialTypeId = 1,
    public readonly int $statusCompletedId = 1,
    public readonly int $statusPendingId = 2,
  ) {
  }

}
