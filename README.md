# Accept Bitcoin and 50+ Cryptocurrencies with CoinGate
Contributors: CoinGate
Donate link: https://coingate.com
Tags: coingate, bitcoin, accept bitcoin, litecoin, ethereum, ripple, monero, bitcoin cash, bcash, altcoins, altcoin, accept altcoin, bitcoin processor, bitcoin gateway, payment gateway, payment module, bitcoin module, bitcoin woocommerce, btc
Requires at least: 4.0
Tested up to: 5.2.1
Stable tag: trunk
License: MIT
License URI: https://github.com/coingate/woocommerce-plugin/blob/master/LICENSE

## Description

***Please note that we do not serve U.S. registered businesses due to regulatory reasons yet.***

Accept cryptocurrency payments in your WooCommerce store with [CoinGate](https://coingate.com/) - our fully automated payment processing and invoice system makes it easy, convenient, and risk-free for you and your customers.

With a simple installation of the CoinGate WooCommerce extension in your store's checkout, customers can pay for your goods and services with cryptocurrencies like Bitcoin, Litecoin, Ethereum, Bitcoin Cash, and XRP, among 50+ other altcoins.

With CoinGate’s WooCommerce plugin, merchants can also receive real-time settlements of cryptocurrencies in *Euros - payouts are made directly to your bank account.* This way, businesses are not exposed to price volatility risk and can enjoy fixed payouts no matter the cryptocurrency’s price.
Alternatively, store owners can choose to receive *payouts in cryptocurrency as well.*

### Features

* The gateway is fully automatic – set it and forget it.
* Receive automatic payment confirmations and order status updates.
* Set your prices in any local fiat currency, and the payment amount in cryptocurrency will be calculated using real-time exchange rates.
* [Customize the invoice](https://blog.coingate.com/2019/03/how-to-customize-merchants-invoice-guide/) – disable/enable cryptocurrencies, change their position on the invoice, and more.
* Select the [settlement currencies and payout options](https://blog.coingate.com/2019/08/payouts-fiat-settlements/) for each crypto-asset;
* Use a [sandbox environment](https://sandbox.coingate.com) for testing with Testnet Bitcoin.
* No setup or recurring fees.
* No chargebacks – guaranteed!

### Functionality

* [Extend invoice expiration time](https://blog.coingate.com/2017/09/bitcoin-merchant-extend-invoice-expiration-time/) up to 24 hours (if payouts are in BTC).
* Accept slight underpayments automatically.
* Refunds can be issued directly from the invoice and without the involvement of the seller.

### How it works - example

1. An item in the store costs 100 euro.
2. A customer wants to buy the item and selects to pay with Bitcoin.
3. An invoice is generated and, according to the current exchange rate, the price is 10,000 euro per bitcoin, so the customer has to pay 0.01 bitcoins.
4. Once the invoice is paid, the merchant gets a notification and is credited 99 euro (100 euro minus our 1% flat fee), or 0.0099 BTC.

*If you’re having trouble installing the plugin, check our blog for a more in-depth description at https://blog.coingate.com/2017/05/install-woocommerce-bitcoin-plugin/*

To use a plugin, a business is required to [pass the verification](https://blog.coingate.com/2019/05/verify-merchant-account-faq/) or [apply for a trial account](https://blog.coingate.com/2020/06/business-trial-account/) first.

Any questions? Write to our support team at [support@coingate.com](mailto:support@coingate.com)

## Installation

First and foremost, you will need to sign up for an account on CoinGate. For production, use our main website: [https://coingate.com/](https://coingate.com). But before you go live, you may want to do some testing, to make sure everything works nicely. For this purpose, we have a sandbox environment: [https://sandbox.coingate.com](https://sandbox.coingate.com/) – just remember to create an account here even if you already have one on our main website.

The installation of our WooCommerce payment module is quite easy. In order to ensure that CoinGate payment gateway is working properly on your website, we will go over these two quick steps:

* Setup API credentials on CoinGate.
* Install the CoinGate payment module for WooCommerce.

For the testing mode to work, you must generate separate API credentials on [https://sandbox.coingate.com](https://sandbox.coingate.com), since API credentials generated on [https://coingate.com](https://coingate.com) will not work in the sandbox environment. To create a set of API credentials, log in to your CoinGate account, and either complete the auto-setup wizard, or find the API tab on the menu – click “Apps”, and then click “+New App”.

*Please note, this guide was created using WordPress 4.7.4, but is also useful for installing older or upcoming versions.*

1. Login to your WordPress admin panel and go to Plugins > Add New.
2. In the Search Plugins field, type in “coingate”. When the CoinGate for WooCommerce plugin is displayed, click “Install Now” (if asked, enter your FTP credentials).
3. After the plugin is installed, click “Activate”.	
4. Go to WooCommerce > Settings > Checkout > CoinGate and then click the check box next to “Enable Bitcoin payment via CoinGate”. If needed, change the Description and the Title according to your preferences.
5. Enter your API credentials on the WooCommerce configuration page.
6. Set the *Receive Currency* parameter to the currency in which you wish to receive your payouts from CoinGate. 
At this point, you can also configure how CoinGate order statuses will correspond to WooCommerce order statuses. Leave the default options if you are not sure.
7. If you are using Sandbox API credentials, then turn Test Mode on.
8. Finally, click “Save changes”, and you are good to go!
