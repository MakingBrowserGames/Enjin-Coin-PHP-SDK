# Enjin-Coin-PHP-SDK
PHP SDK for Enjin Coin - https://enjincoin.io

# Requirements

* PHP 7.1+ 
* MySQL 5.6+
* Composer 

##### Required modules on Ubuntu:
```
apt-get install php7.1-curl php7.1-json php7.1-mysql php7.1-dom php7.1-mbstring php7.1-bcmath
```

##### Required modules on Windows:
* Download PHP from http://php.net/downloads.php
* Download MySQL from https://dev.mysql.com/downloads/
* Download Composer from https://getcomposer.org/download/

Ensure that the following lines are uncommented in your php.ini file:
```
extension=php_curl.dll
extension=php_mysqli.dll
extension=php_sockets.dll
extension=php_mbstring.dll
```

# Install xdebug - required for code coverage
Follow the instructions on this page to install xdebug
https://xdebug.org/wizard.php


# Installation
```
composer install
```

# Run all unit tests
```
composer run test
```

# Run tests for 1 class (e.g. IdentitiesTest)
```
composer test -- --filter IdentitiesTest
```

# Run tests for 1 class method (e.g. IdentitiesTest and the method)
```
composer test -- --filter IdentitiesTest::testGet_AfterIdentityIdIsSet
```

# View Test Results
```
Open reports/index.html
```

# Run Checkstyle Analysis
```
cd vendor/phpcheckstyle/phpcheckstyle
php run.php --src ../../../src 
```

#View Checkstyle Reports
```
Open vendor/phpcheckstyle/phpcheckstyle/style-report/index.html
```
