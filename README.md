# Symfony2 JSON Bundle with CURL transport

## Installation

### In order to install this library via composer run the following command in the console:

```sh
composer require petrlatsko/jsonbundle
```

### Register bundle into app/AppKernel.php

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new PetrLatsko\JsonBundle\PetrLatskoJsonBundle(),
    );
    // ...

    return $bundles;
}
```

### Usage examples

```php
// Get client
$jsonReq = $this->get('json.curl_client');
// Do request
$jsonReq->getJson('http://site.com/json-sourse')
```
