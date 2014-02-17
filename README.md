[![Build Status](https://travis-ci.org/CansaSCityShuffle/Puice.png?branch=master)](https://travis-ci.org/CansaSCityShuffle/Puice)

Puice
=====

Dependency and Configruation Management Framework inspired by Guice and these two Google Talks:
 - http://www.youtube.com/watch?v=RlfLCWKxHJ0
 - http://www.youtube.com/watch?v=-FRm3VPhseI

Install
-------

To install Puice put this into your composer.json
`"CansaSCityShuffle/Puice": "*"`

For example: 
```json
{
    "require": {
        "CansaSCityShuffle/Puice": "*"
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

Free for all
