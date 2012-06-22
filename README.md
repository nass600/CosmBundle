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

Add CosmBundle to your vendor/bundles/ directory.

Add the following lines in your ``deps`` file:

    [Nass600CosmBundle]
      git =https://github.com/nass600/CosmBundle.git
      target=/bundles/Nass600/CosmBundle
      version=master

Run the vendors script:

    ./bin/vendors install

Add the Ideup namespace to your `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // your other namespaces
        'Nass600' => __DIR__.'/../vendor/bundles',
    );


Add PachubeBundle to your `app/AppKernel.php`:

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Nass600\CosmBundle\Nass600CosmBundle(),
        );
    }