# Magendoo ShippingRestrictions

    magendoo/module-shippingrestrictions

- [Magendoo ShippingRestrictions](#magendoo-shippingrestrictions)
  - [Main Functionalities](#main-functionalities)
  - [Installation](#installation)
    - [Type 1: Zip file](#type-1-zip-file)
    - [Type 2: Composer](#type-2-composer)
  - [Configuration](#configuration)
  - [Specifications](#specifications)


## Main Functionalities
Shipping Restrictions module

## Installation
- in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Magendoo`
 - Enable the module by running `php bin/magento module:enable Magendoo_ShippingRestrictions`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require magendoo/module-shippingrestrictions`
 - enable the module by running `php bin/magento module:enable Magendoo_ShippingRestrictions`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

- TBD


## Specifications

 - Model
	- ShippingRestriction
