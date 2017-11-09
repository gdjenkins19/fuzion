# fuzion
The last personal fuzion implementation.

# Pre-Requirement

Install Composer (https://www.abeautifulsite.net/installing-composer-on-os-x)
    - curl -sS https://getcomposer.org/installer | php
    - sudo mv composer.phar /usr/local/bin/composer

# Installation

1. composer install (--no-dev)
2. Configure lib/database/database.json
3. composer run-script build-all
... To Clean Up
4. composer run-script build-clean

# REQUIREMENTS

1. Remove buildDB from Database and into Builder
2. Implement the Login/Authenticate/Authorize Framework