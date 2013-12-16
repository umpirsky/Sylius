Sylius [![Build status...](https://secure.travis-ci.org/Sylius/Sylius.png?branch=master)](http://travis-ci.org/Sylius/Sylius) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Sylius/Sylius/badges/quality-score.png?s=f6d89b8aad6e15cab61134e7c0544ee1313f7f31)](https://scrutinizer-ci.com/g/Sylius/Sylius/)
======

Sylius is an open source e-commerce solution for **PHP**, based on the [**Symfony2**](http://symfony.com) framework.

Ultimate goal of the project is to create a webshop engine, which is user-friendly, *loved* by developers and has a helpful community.

Sylius is constructed from fully decoupled components (bundles in Symfony2 glossary), which means that every feature (products catalog, shipping engine, promotions system...) can be used in any other application. 

We're using full-stack BDD methodology, with [phpspec](http://phpspec.net) and [Behat](http://behat.org).

Documentation
-------------

Documentation is available at [docs.sylius.org](http://docs.sylius.org).

Quick Installation
------------------

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project sylius/sylius -s dev
$ cd sylius
$ php app/console sylius:install
```

To be able to use included fixtures, that make testing and development phases much easier, you may need
to run Composer tool with `--dev` option:

```bash
$ php composer.phar install --dev
```

[Behat](http://behat.org) scenarios
-----------------------------------

You need to copy Behat default configuration file and enter your specific ``base_url``
option there.

```bash
$ cp behat.yml.dist behat.yml
$ vi behat.yml
```

Then download [Selenium Server](http://seleniumhq.org/download/), and run it.

```bash
$ java -jar selenium-server-standalone-2.39.0.jar
```

You can run Behat using the following command.

```bash
$ bin/behat
```

Sylius Authentication
---------------------

Add Guzzle package : 

```bash
composer.phar require guzzle/guzzle *
```

When you press the "store" link on Hypebeast, just axecute an action like that :

```php
$url  = "http://store.hypebeast.dev/app_dev.php/";
$auth = $url . "auth/mobile";
$key  = "YouHaveToChangeThisKey";

$browser = new \Guzzle\Http\Client($auth);

$request = $browser->post(
        sprintf('?api_key=%s', base64_encode($key)),
        null,
        [
            'id'        => 100,
            'username'  => 'TheUsername',
            'email'     => 'your@address.email',
            'firstname' => 'John',
            'lastname'  => 'DOE',
        ]
        );

$response = $request->send();

return new RedirectResponse($url, 302, [ 'Set-Cookie' => $response->getSetCookie() ]);
```

An authenticated token is created for the given user and the user is created/updated on Sylius.
Then, you are redirected on Sylius and you are authenticated

Update user default country
---------------------------

Just post a form to any URL with a field named **"\_hypebeast\_default\_country"** containing the ID of the new country.
```html
<form action="." method="POST">
        <select name="_hypebeast_default_country">
        	<option value="1">Afghanistan</option>
        	<option value="2">Afrique du Sud</option>
        	<option value="3">Albanie</option>
        </select>
</form>
```

Troubleshooting
---------------

If something goes wrong, errors & exceptions are logged at the application level.

```bash
$ tail -f app/logs/prod.log
$ tail -f app/logs/dev.log
```

If you are using the supplied Vagrant development environment, please see the related [Troubleshooting guide](vagrant/README.md#Troubleshooting) for more information.

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Sylius on Twitter
-----------------

If you want to keep up with the updates, [follow the official Sylius account on Twitter](http://twitter.com/Sylius).

Bug tracking
------------

Sylius uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
If you have found bug, please create an issue.

MIT License
-----------

License can be found [here](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

Sylius was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/Sylius/contributors).
