<?php

declare(strict_types=1);

namespace NAudit\DI;

use NAudit\AuditListener;
use NAudit\AuditUserProvider;
use Nette\DI\CompilerExtension;

/**
 * Nette DI Extension for NAudit.
 *
 * Registers the AuditListener as a Doctrine event subscriber
 * and the AuditUserProvider service.
 *
 * Config:
 *   audit:
 *       # no config needed — just register the extension
 */
final class NAuditExtension extends CompilerExtension
{
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('listener'))
			->setFactory(AuditListener::class);

		$builder->addDefinition($this->prefix('userProvider'))
			->setFactory(AuditUserProvider::class);
	}


	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		// Register as Doctrine event listener
		$listener = $builder->getDefinition($this->prefix('listener'));
		$listener->addTag('nettrine.subscriber');
	}
}
