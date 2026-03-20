<?php

declare(strict_types=1);

namespace NAudit;

use Nette\Security\User;

/**
 * Provides current user info to the audit listener.
 */
final class AuditUserProvider
{
	/**
	 * Get user ID from Nette identity.
	 */
	public static function extractUserId(?User $user): ?int
	{
		if (!$user || !$user->isLoggedIn()) {
			return null;
		}
		$identity = $user->getIdentity();
		$id = $identity?->getId();
		return is_numeric($id) ? (int) $id : null;
	}


	/**
	 * Get user display name from Nette identity.
	 */
	public static function extractUserName(?User $user): ?string
	{
		if (!$user || !$user->isLoggedIn()) {
			return null;
		}
		$identity = $user->getIdentity();
		if ($identity === null) {
			return null;
		}
		$data = $identity->getData();
		$name = $data['name'] ?? $data['email'] ?? null;
		return is_string($name) ? $name : 'ID:' . (is_numeric($identity->getId()) ? (string) $identity->getId() : '?');
	}
}
