## Smile Retailer Offer 

This module is a plugin for [RetailerSuite](https://github.com/Smile-SA/elasticsuite-for-retailer).

This module add the ability to manage ACL in admin panel per Retailer Shop.

### Requirements

The module requires :

- [Retailer](https://github.com/Smile-SA/magento2-module-retailer) > 1.2.*
- [Offer](https://github.com/Smile-SA/magento2-module-offer) > 1.3.*

### How to use

1. Install the module via Composer :

``` composer require smile/module-retailer-admin ```

2. Enable it

``` bin/magento module:enable Smile_RetailerAdmin ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

### How to define retailer by user

Go to magento admin panel

Menu : System > Permissions > All Users

Select a user and affect a retailer.
