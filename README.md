# phpqa

[![Build Status](https://travis-ci.org/rodrigorm/phpqa-patch.svg)](https://travis-ci.org/rodrigorm/phpqa-patch)

**phpqa** is a command-line tool for PHPMD.

## Installation

### Composer

    composer require 'rodrigorm/phpqa-patch=*'

For a system-wide installation via Composer, you can run:

    composer global require 'rodrigorm/phpqa-patch=*'

Make sure you have `~/.composer/vendor/bin/` in your path.

## Usage

### Patch PHPMD

    $ git diff HEAD^1 > /tmp/patch.txt

    $ phpmd /path/to/project/Example.php xml phpmd.xml --reportfile /tmp/pmd.xml

    $ phpqa patch-pmd --patch /tmp/patch.txt         \
                      --path-prefix /path/to/project \
                      /tmp/pmd.xml
    phpqa dev-master by Rodrigo Moyle.

    1 violations found:

    Example.php:11     Lorem ipsum dolor sit amet.

## Patch PHPCPD

    $ git diff HEAD^1 > /tmp/patch.txt

    $ phpcpd --log-pmd /tmp/pmd-cpd.xml /path/to/project/

    $ phpqa patch-cpd --patch /tmp/patch.txt         \
                      --path-prefix /path/to/project \
                      /tmp/pmd-cpd.xml
