phpcli
======

PHP CLI Tools including a logger class

## Installation
phpcli is installed via [Composer](https://getcomposer.org/).
You just need to [add dependency](https://getcomposer.org/doc/04-schema.md#package-links>) on phpcli into your package.

Example:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ReturnPath/phpcli.git"
        }
    ],
    "require": {
        "nategood/httpful": "*",
        "rp/phpcli": "~v1.0"
    }
}
```

Example Use:
```php
// Setup the cli logger
$logger = new \rp\phpcli\Logger();
$logger->setTimeZone(new \DateTimeZone('America/Denver'));

// Set default env's from the environment
load_var('server_env');
load_var('db_env');
load_var('db_env_name');
```
