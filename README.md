[![Build Status](https://travis-ci.org/alwinmark/Puice.png?branch=master)](https://travis-ci.org/alwinmark/Puice)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alwinmark/Puice/badges/quality-score.png?s=657ed43434b91c44e677ae7aadbca01e6cd42643)](https://scrutinizer-ci.com/g/alwinmark/Puice/)
[![HHVM Status](http://hhvm.h4cc.de/badge/alwinmark/puice.png)](http://hhvm.h4cc.de/package/alwinmark/puice)
[![Latest Stable Version](https://poser.pugx.org/alwinmark/puice/v/stable.png)](https://packagist.org/packages/alwinmark/puice)
[![License](https://poser.pugx.org/alwinmark/puice/license.png)](https://packagist.org/packages/alwinmark/puice)

Puice
=====

Dependency and Configruation Management Framework inspired by Guice and these two Google Talks:
 - http://www.youtube.com/watch?v=RlfLCWKxHJ0
 - http://www.youtube.com/watch?v=-FRm3VPhseI

Benchmark
---------

https://github.com/alwinmark/benchmarking-dependency-injection-containers (currently only Benchmark 1 is implemented)

Install
-------

Install php5-curl

* on debian/ubuntu: `sudo apt-get install php5-curl`

To install Puice put this into your composer.json
`"alwinmark/Puice": "*"`

For example: 
```json
{
    "require": {
        "alwinmark/puice": "1.0.0"
    },
    "require-dev": {
        "behat/behat": "2.4.*@stable",
        "behat/mink-extension": "*",
        "behat/mink-selenium2-driver": "*",
        "behat/mink-goutte-driver": "*",
        "phpspec/phpspec": "2.0.*@dev"
    },
    "minimum-stability": "dev",
    "config": {
        "bin-dir": "vendor/bin/"
    },
    "autoload": {"psr-0": {"": "src"}}
}
```

How to use it
-------------

If you want to know how to use it, take a look at [the feature file](features/puice.feature).

Maybe you have allready some Kind of Configuration/Injection Framework and you only want to use the [Generic Factory](src/Puice/Factory.php) Puice is providing. If thats the case, you have to write some Gluecode, that implements the [Puice\Config](src/Puice/Config.php) Interface and pass it to the Factory.

Licence
--------

MIT
