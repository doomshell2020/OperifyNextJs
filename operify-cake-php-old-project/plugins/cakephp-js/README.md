JsHelper
========

JsHelper for CakePHP 3.x

The JsHelper from CakePHP 1.x and 2.x was eventually dropped with the release of CakePHP 3.0.
This project is a converted version of the latest stable CakePHP 2.x JsHelper, which will work on CakePHP 3.0 in pretty much the same way.

**NOTE:** The project is currently just a quick conversion of the latest stable JsHelper. It has not been extensively tested yet. Please report any issues you may have with it, so they can be addressed.

Installation
------------

The easiest way to install the plugin is to use Composer. Simply run:

```
php composer.phar require oldskool/cakephp-js:dev-master
```

You can also clone the repository in the plugins folder:

```
$ cd plugins
$ git clone git@github.com:oldskool/cakephp-js.git CakeJs
```

Once the plugin is in place, load it in your `src/Config/bootstrap.php` by adding this line:

```php
Plugin::load('CakeJs');
```

Now to use the Helper, simply load it in your Controller:

```php
public $helpers = ['CakeJs.Js'];
```
