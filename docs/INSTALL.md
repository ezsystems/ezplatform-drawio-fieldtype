# EzPlatformDrawIOFieldTypeBundle

## Installation

1. Enable the bundle
 
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new EzSystems\EzPlatformDrawIOFieldTypeBundle\EzPlatformDrawIOFieldTypeBundle(),
        // ...
    );
}
``` 

2) Get the bundle using composer

```
composer require ezsystems/ezplatform-drawio-fieldtype
```

3. Create new Content Type (eg: Diagram) with attribute Draw IO Field Type
