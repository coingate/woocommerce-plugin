=== WooCommerce Crypto Payment Processor ===
Contributors: CoinGate
Donate link: https://coingate.com
Tags: coingate, bitcoin, accept bitcoin, litecoin, ethereum, ripple, monero, bitcoin cash, bcash, altcoins, altcoin, accept altcoin, bitcoin processor, bitcoin gateway, payment gateway, payment module, bitcoin module, bitcoin woocommerce, btc
Requires at least: 5.3
Requires PHP: 7.3
Tested up to: 6.6
Stable tag: 2.2.0
License: MIT
License URI: https://github.com/coingate/woocommerce-plugin/blob/master/LICENSE

== Description ==

Accept cryptocurrency payments in your store with the CoinGate module - a fully automated payment processing and invoicing solution for online stores.

Receive payouts in Bitcoin and other cryptocurrencies, Euros, GBP and USD without exchange rate volatility risk!

***COINGATE CRYPTOCURRENCY PAYMENT GATEWAY FOR WOOCOMMERCE***

Start accepting Bitcoin and other cryptocurrencies in no time! Our official WooCommerce module connects your store to our ***fully automated cryptocurrency processing platform and invoice system***, giving you access to the rapidly emerging blockchain market.

With CoinGate, online businesses can ***accept Bitcoin, Litecoin, Ethereum, Doge*** as well as 70+ other digital currencies (Altcoins) hassle-free. Payments received from customers can be instantly converted to Fiat, and you can receive ***payouts in Euros and U.S. Dollars*** directly to your bank account. Alternatively, store owners can choose to receive ***payouts in Bitcoin*** and other cryptocurrencies such as ***Ethereum, Ripple*** and more!

***START ACCEPTING BITCOIN IN MINUTES!***

The extension allows stores that use WordPress WooCommerce shopping cart system to accept Bitcoin payments as well as Altcoins via the CoinGate API. Integration and configuration of our WooCommerce module is super simple, while the solution you get is fully automated – you will receive automatic payment confirmations and order status updates.
To find out more about the benefits of Cryptocurrencies for your business, as well as the solution offered by CoinGate, [check our website](https://coingate.com/accept).

= Features =

* A simple, one-time setup with little-to-no maintenance needed;
* Instant payment confirmations and order status updates;
*  Pricing of merchandise in any local fiat currency;
* Issuing full and partial refunds manually and automatically;
* Permission-based account management tools;
* Bitcoin Lightning Network support;
* Payouts in stablecoins, BTC, other cryptos, or fiat currencies (EUR/GBP/USD);
* Mitigation of cryptocurrency market volatility;
* [Sandbox environment](https://sandbox.coingate.com/) for testing with Tesnet Bitcoin.
* No setup or recurring fees.
* No chargebacks – guaranteed!

= Functionality =

* Extend invoice expiration time up to 24 hours (if pay and receive currency is the same);
* Automatically accept slightly underpaid orders;
* Change the invoice settings: disable/enable coins, switch positioning, settlements, & more;
* Manage payout options for each accepted cryptocurrency.

= How it works - example =

1. An item in the store costs 100 euro.
2. A customer wants to buy the item and selects to pay with Bitcoin.
3. An invoice is generated and, according to the current exchange rate, the price is 50,000 euro per bitcoin, so the customer has to pay 0.002 bitcoins.
4. Once the invoice is paid, the merchant gets a notification and is credited 99 euro (100 euro minus our 1% flat fee), or 0.00198 BTC.

To be able to use the plugin you have to create an account on [https://coingate.com/accept](https://coingate.com/accept)

== Installation ==

First and foremost, you will need to sign up for an account on CoinGate. For production, use our main website: [https://coingate.com/](https://coingate.com). But before you go live, you may want to do some testing, to make sure everything works nicely. For this purpose, we have a sandbox environment: [https://sandbox.coingate.com](https://sandbox.coingate.com/) – just remember to create an account here even if you already have one on our main website.

The installation of our WooCommerce payment module is quite easy. In order to ensure that CoinGate payment gateway is working properly on your website, we will go over these two quick steps:

* Setup API credentials on CoinGate.
* Install the CoinGate payment module for WooCommerce.

For the testing mode to work, you must generate separate API credentials on [https://sandbox.coingate.com](https://sandbox.coingate.com), since API credentials generated on [https://coingate.com](https://coingate.com) will not work in the sandbox environment. To create a set of API credentials, log in to your CoinGate account, and either complete the auto-setup wizard, or find the API tab on the menu – click “Apps”, and then click “+New App”.

*Please note, this guide was created using WordPress 4.7.4, but is also useful for installing older or upcoming versions.*

1. Login to your WordPress admin panel and go to Plugins > Add New.
2. In the Search Plugins field, type in “coingate”. When the CoinGate for WooCommerce plugin is displayed, click “Install Now” (if asked, enter your FTP credentials).
3. After the plugin is installed, click “Activate”.
4. Go to WooCommerce > Settings > Payments > Method : “Cryptocurrencies via CoinGate” and then click the checkbox “Enabled”. After Enabled is clicked then press on CoinGate on the same page. If needed, change the Description and the Title according to your preferences.
5. Enter your API credentials on the WooCommerce configuration page.
6. Set the *Receive Currency* parameter to the currency in which you wish to receive your payouts from CoinGate.
At this point, you can also configure how CoinGate order statuses will correspond to WooCommerce order statuses. Leave the default options if you are not sure.
7. If you are using Sandbox API credentials, then turn Test Mode on.
8. Finally, click “Save changes”, and you are good to go!

== Screenshots ==

1. CoinGate payment form - Cryptocurrency selection window
2. CoinGate payment form - Invoice
3. CoinGate payment form - Confirmation

== Changelog ==

= v2.1.0 - 2022-10-10 =
* Added: Ability to send customer email to CoinGate's checkout form.
* Fixed: Send app info from the API auth token validation action.

= v2.0.2 - 2022-07-04 =
* Added: WordPress coding standards.
* Added: Github workflow.
* Changed: Updated composer libraries.
* Fixed: Not showing payment settings page [Issue #11]

= v2.0.1 - 2022-06-09 =
* Plugin now requires Wordpress 5.3 version or greater.
* Plugin now requires PHP 7.3.0 or greater.
* Updated CoinGate PHP library up to 4.1.0.
* Changed: Plugin refactored from the ground.
* Added: API auth token validation.
* Added: New status - "Do nothing" in the payment gateway settings page.
* Added: New CoinGate payment status - "Confirming".
