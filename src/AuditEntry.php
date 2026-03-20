<?php

declare(strict_types=1);

namespace NAudit;

/**
 * Value object for a single audit log entry.
 */
final class AuditEntry
{
	private string $entityClass = '';
	private string $entityId = '';
	private string $entityLabel = '';
	private string $action = '';
	/** @var array<string, array{old: mixed, new: mixed}> */
	private array $changes = [];
	private ?int $userId = null;
	private ?string $userName = null;
	private ?string $ipAddress = null;
	private \DateTimeImmutable $createdAt;


	public function __construct()
	{
		$this->createdAt = new \DateTimeImmutable();
	}


	public function getEntityClass(): string { return $this->entityClass; }
	public function setEntityClass(string $v): void { $this->entityClass = $v; }

	public function getEntityId(): string { return $this->entityId; }
	public function setEntityId(string $v): void { $this->entityId = $v; }

	public function getEntityLabel(): string { return $this->entityLabel; }
	public function setEntityLabel(string $v): void { $this->entityLabel = $v; }

	public function getAction(): string { return $this->action; }
	public function setAction(string $v): void { $this->action = $v; }

	/** @return array<string, array{old: mixed, new: mixed}> */
	public function getChanges(): array { return $this->changes; }
	/** @param array<string, array{old: mixed, new: mixed}> $v */
	public function setChanges(array $v): void { $this->changes = $v; }

	public function getUserId(): ?int { return $this->userId; }
	public function setUserId(?int $v): void { $this->userId = $v; }

	public function getUserName(): ?string { return $this->userName; }
	public function setUserName(?string $v): void { $this->userName = $v; }

	public function getIpAddress(): ?string { return $this->ipAddress; }
	public function setIpAddress(?string $v): void { $this->ipAddress = $v; }

	public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
	public function setCreatedAt(\DateTimeImmutable $v): void { $this->createdAt = $v; }
}
