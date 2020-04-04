# EzPlatformDrawIOFieldTypeBundle

## Installation

1. Enable the bundle
 
```php
// config/bundles.php
return [
    // ...
    EzSystems\EzPlatformDrawIOFieldTypeBundle\EzPlatformDrawIOFieldTypeBundle::class => ['all' => true],
    // ...
];
```

2) Get the bundle using composer

```
composer require ezsystems/ezplatform-drawio-fieldtype
```

3. Create new Content Type (eg: Diagram) with attribute Draw IO Field Type
