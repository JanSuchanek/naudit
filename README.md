# NAudit

Doctrine entity audit log for Nette Framework — automatically tracks changes to entities.

## Features

- 📝 **Change Tracking** — Records create/update/delete with field diffs
- 🔌 **AuditableInterface** — Mark entities for tracking
- 📊 **JSON Storage** — Changes stored as JSON in `audit_log` table
- ⚙️ **DI Extension** — Auto-registers Doctrine event subscriber

## Installation

```bash
composer require jansuchanek/naudit
```

## Configuration

```neon
extensions:
    audit: NAudit\DI\NAuditExtension
```

## Usage

Implement `AuditableInterface` on your entities:

```php
use NAudit\AuditableInterface;

class Product implements AuditableInterface
{
    use AuditableTrait;

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
```

## Migration

```sql
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_class VARCHAR(255) NOT NULL,
    entity_id INT NOT NULL,
    action VARCHAR(16) NOT NULL,
    changes JSON,
    user_id INT,
    created_at DATETIME NOT NULL,
    INDEX idx_entity (entity_class, entity_id)
);
```

## Requirements

- PHP >= 8.2
- Doctrine ORM ^3.0
- Nette DI ^3.2

## License

MIT
