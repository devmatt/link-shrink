Link Shrink
===========

**Link Shrink** is a library for abstracting interaction with url shortening services like [Bit.ly](https://bitly.com/) and [Goo.gl](https://goo.gl/).

### Adapters ###
**Adapters** are used to make the requests to the APIs. [Ivory Http Adapter](https://github.com/egeloen/ivory-http-adapter) is used to provide a consistent interface. Any of the libraries listed there may be used depending on your needs and existing infrastructure.

### Providers ###

**Providers** are where the magic happens. Each provider contains the logic for interacting with the various APIs.
Currently Bit.ly and Goo.gl are supported and a GenericResolver is provided to retrieving the final location of the link (i.e., reversing the url shortening process)


Installation
------------

The recommended way to install Geocoder is through [Composer](http://getcomposer.org).
Create a `composer.json` file into your project:

``` json
{
    "require": {
        "devmatt/link-shrink": "dev-master"
    }
}
```


How-to get started
------------------

``` php
<?php
require_once('./vendor/autoload.php');

$adapter = new \Ivory\HttpAdapter\Guzzle4HttpAdapter();
$provider = new Bitly($adapter, 'YOUR-BITLY-API-KEY');
$shrink = new LinkShrink($provider);

$shrink->shorten('https://github.com/devmatt/link-shrink'); // http://bit.ly/1vuXvCt
$shrink->expand('http://bit.ly/1vuXvCt'); // https://github.com/devmatt/link-shrink
```

If you want to load and use multiple providers

``` php
<?php
require_once('./vendor/autoload.php');

$adapter = new \Ivory\HttpAdapter\Guzzle4HttpAdapter();
$providers = array(
    new \LinkShrink\Provider\Bitly($adapter, 'YOUR-BITLY-API-KEY'),
    new \LinkShrink\Provider\Google($adapter, 'YOUR-GOOGLE-API-KEY'), // API key not required but highly recommended
    new \LinkShrink\Provider\GenericResolver($adapter),
);
$shrink = new \LinkShrink\LinkShrink();
$shrink->registerProviders($providers);

$shrink->switchProvider('bitly');
$shrink->shorten('https://github.com/devmatt/link-shrink'); // http://bit.ly/1vuXvCt
$shrink->expand('http://bit.ly/1vuXvCt'); // https://github.com/devmatt/link-shrink

$shrink->switchProvider('google');
$shrink->shorten('https://github.com/devmatt/link-shrink'); // http://goo.gl/L3DzDn
$shrink->expand('http://goo.gl/L3DzDn'); // https://github.com/devmatt/link-shrink
```

Credits
-------

Project structure inspired by the amazing [Geocoder](https://github.com/geocoder-php/Geocoder) library.


License
-------
Link Shrink is released under MIT licence. More info can be found in the LICENCE file.

