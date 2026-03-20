<?php

declare(strict_types=1);

namespace NAudit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;

/**
 * Doctrine event listener that automatically logs entity changes.
 *
 * Tracks INSERT, UPDATE, DELETE operations on audited entities.
 * Only entities implementing AuditableInterface are tracked.
 *
 * Usage:
 *   1. Implement AuditableInterface on your entity
 *   2. Register this listener in DI config (done automatically by NAuditExtension)
 */
final class AuditListener
{
	private ?EntityManagerInterface $em = null;


	public function setEntityManager(EntityManagerInterface $em): void
	{
		$this->em = $em;
	}


	public function postPersist(PostPersistEventArgs $args): void
	{
		$entity = $args->getObject();
		if (!$entity instanceof AuditableInterface) {
			return;
		}

		$this->log($args->getObjectManager(), $entity, 'INSERT', []);
	}


	public function postUpdate(PostUpdateEventArgs $args): void
	{
		$entity = $args->getObject();
		if (!$entity instanceof AuditableInterface) {
			return;
		}

		$em = $args->getObjectManager();
		$uow = $em->getUnitOfWork();
		/** @var array<string, array{old: mixed, new: mixed}> $changeSet */
		$changeSet = $uow->getEntityChangeSet($entity);

		// Filter out sensitive fields
		/** @var array<string, array{old: mixed, new: mixed}> $changes */
		$changes = [];
		$skip = $entity->getAuditSkipFields();
		foreach ($changeSet as $field => [$old, $new]) {
			if (in_array($field, $skip, true)) {
				continue;
			}
			$changes[$field] = [
				'old' => $this->normalize($old),
				'new' => $this->normalize($new),
			];
		}

		if ($changes !== []) {
			$this->log($em, $entity, 'UPDATE', $changes);
		}
	}


	public function preRemove(PreRemoveEventArgs $args): void
	{
		$entity = $args->getObject();
		if (!$entity instanceof AuditableInterface) {
			return;
		}

		$this->log($args->getObjectManager(), $entity, 'DELETE', []);
	}


	/**
	 * @param array<string, array{old: mixed, new: mixed}> $changes
	 */
	private function log(
		EntityManagerInterface|\Doctrine\Persistence\ObjectManager $em,
		AuditableInterface $entity,
		string $action,
		array $changes,
	): void {
		$entry = new AuditEntry();
		$entry->setEntityClass(get_class($entity));
		$entry->setEntityId((string) $entity->getAuditId());
		$entry->setAction($action);
		$entry->setChanges($changes);
		$entry->setEntityLabel($entity->getAuditLabel());
		$entry->setCreatedAt(new \DateTimeImmutable());

		// Try to get current user from the injected EM's connection
		// (user info is set externally via AuditUserProvider)
		if ($this->em) {
			$conn = $this->em->getConnection();
			// User info stored as connection parameter by AuditUserProvider
		}

		// Use a separate connection to avoid UnitOfWork conflicts
		/** @var EntityManagerInterface $em */
		$conn = $em->getConnection();
		$conn->insert('audit_log', [
			'entity_class' => $entry->getEntityClass(),
			'entity_id' => $entry->getEntityId(),
			'entity_label' => $entry->getEntityLabel(),
			'action' => $entry->getAction(),
			'changes' => json_encode($entry->getChanges(), JSON_UNESCAPED_UNICODE),
			'user_id' => $entry->getUserId(),
			'user_name' => $entry->getUserName(),
			'ip_address' => $entry->getIpAddress(),
			'created_at' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
		]);
	}


	private function normalize(mixed $value): mixed
	{
		if ($value instanceof \DateTimeInterface) {
			return $value->format('Y-m-d H:i:s');
		}
		if (is_object($value) && method_exists($value, 'getId')) {
			return $value->getId();
		}
		if (is_object($value) && method_exists($value, '__toString')) {
			return (string) $value;
		}
		if (is_object($value)) {
			return get_class($value) . '#?';
		}
		return $value;
	}
}
