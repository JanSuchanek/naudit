<?php

declare(strict_types=1);

namespace NAudit;

/**
 * Interface for entities that should be audited.
 *
 * Implement this on any Doctrine entity to enable automatic change tracking.
 */
interface AuditableInterface
{
	/**
	 * Get the entity ID for audit log.
	 */
	public function getAuditId(): int|string;

	/**
	 * Get a human-readable label for the audit log entry.
	 * E.g. product name, page title, customer email.
	 */
	public function getAuditLabel(): string;

	/**
	 * Fields to skip in change tracking (e.g. 'password', 'token').
	 * @return list<string>
	 */
	public function getAuditSkipFields(): array;
}
