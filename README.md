CosmBundle
==================

The CosmBundle offers access to the cosm.com feed API either by using a PHP service or through the CLI
for Symfony2. Is on charge of handle all the operations related to cosm feeds via the bundle CRUD interface.

Features include:

- CRUD API for managing feeds
- CLI commands for executing operations


What is Cosm?
----------------

Pachube is an web service provider allowing developers to connect their own data (energy and environment data
from objects, devices & buildings) to the Web and to build their own applications on it.

For more information about the service, please visit: https://cosm.com


Installation
------------

#### On Symfony 2.0.x:

Add CosmBundle to your vendor/bundles/ directory.

Add the following lines in your ``deps`` file:

```
    [Nass600CosmBundle]
      git =https://github.com/nass600/CosmBundle.git
      target=/bundles/Nass600/CosmBundle
```

Run the vendors script:

``` bash
    ./bin/vendors update
```

Add the Nass600 namespace to your `app/autoload.php`:

``` php
<?php
    // app/autoload.php
    $loader->registerNamespaces(array(
        // your other namespaces
        'Nass600' => __DIR__.'/../vendor/bundles',
    );
```

Add CosmBundle to your `app/AppKernel.php`:

``` php
<?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Nass600\CosmBundle\Nass600CosmBundle(),
        );
    }
```

#### On Symfony 2.1.x:

Add this line to your project's composer.json:

``` json
    "require": {
        // your other packages
        "nass600/cosm-bundle": "dev-master"
    },
```

Add CosmBundle to your `app/AppKernel.php`:

``` php
<?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Nass600\CosmBundle\Nass600CosmBundle(),
        );
    }
```