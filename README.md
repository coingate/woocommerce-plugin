# Accept BTC, LTC, ETH, TRX, stablecoins and other cryptocurrencies

## Description

***Accept Crypto Payments with CoinGate’s WooCommerce Plugin***


Easily accept cryptocurrency payments on your WooCommerce store using CoinGate. Our WooCommerce plugin provides fully automated payment processing and invoicing, making crypto payments simple, secure, and seamless for both you and your customers.

With just a quick setup, your customers can pay using 15+ cryptocurrencies like Bitcoin, USDC, Ethereum, and Litecoin across multiple networks—including Layer 2 solutions like Polygon, Arbitrum, Base, Optimism, Solana, and more.

You can receive settlements directly to your bank account in EUR, USD, or GBP—or choose to keep crypto.

### Key Features

* **Fully automated gateway** – no manual processing needed.
* **Real-time exchange rates** – convert crypto to fiat instantly at checkout.
* **Multi-chain support** – accept crypto on Ethereum, Polygon, Arbitrum, Solana, Base, Optimism, and [more](https://coingate.com/supported-currencies).
* **Customizable invoices** – choose supported coins, accept underpaid or overpaid orders, and adjust invoice settings.
* **Automatic order updates** – payment confirmations trigger status changes.
* **Test mode available** – experiment in a [sandbox environment](https://sandbox.coingate.com/).
* **Crypto refunds** – issue full and partial [refunds](https://coingate.com/blog/post/merchant-refund).
* **Exportable reports** – access accounting and payout data in just a few clicks.
* **Role-based account management** – control [permissions](https://coingate.com/blog/post/business-user-permissions) for team members.
* **Built-in AML/KYC tools** – stay protected and compliant.
* **Flexible fees** – starting at 1%, with lower [rates](https://coingate.com/pricing) available for high-volume merchants.
* **No chargebacks** – all crypto payments are final.
* **Gain seamless access to Binance Pay** - unlocks the ability for millions of Binance users to purchase from your store using any wallet assets tied to their Binance account.

### How It Works (Example)

1. A customer selects crypto as the payment method for a €100 order.
2. Based on real-time rates, they’re shown the amount to pay in their chosen cryptocurrency.
3. After payment confirmation, you receive ~€99 (minus fees) in your CoinGate account.
4. You can withdraw funds to your bank in EUR, USD, or GBP—or hold them in crypto.

## Installation

First and foremost, you will need to sign up for an account on CoinGate. For production, use our main website: [https://coingate.com/](https://coingate.com). But before you go live, you may want to do some testing, to make sure everything works nicely. For this purpose, we have a sandbox environment: [https://sandbox.coingate.com](https://sandbox.coingate.com/) – just remember to create an account here even if you already have one on our main website.

The installation of our WooCommerce payment module is quite easy. In order to ensure that CoinGate payment gateway is working properly on your website, we will go over these two quick steps:

* Create [API credentials](https://support.coingate.com/hc/en-us/articles/4402498918546) and configure ["Settlement Currency"](https://support.coingate.com/hc/en-us/articles/21208494395676-How-to-Configure-Settlement-Currency) on CoinGate.
* Install the CoinGate payment module for WooCommerce.

For the testing mode to work, you must generate separate API credentials on [https://sandbox.coingate.com](https://sandbox.coingate.com), since API credentials generated on [https://coingate.com](https://coingate.com) will not work in the sandbox environment. To create a set of API credentials, log in to your CoinGate account, and either complete the auto-setup wizard, or find the API tab on the menu – click “Apps”, and then click “+New App”.

1. Login to your WordPress admin panel and go to Plugins > Add New.
2. In the Search Plugins field, type in “coingate”. When the CoinGate for WooCommerce plugin is displayed, click “Install Now” (if asked, enter your FTP credentials).
3. After the plugin is installed, click “Activate”.	
4. Go to WooCommerce > Settings > Payments > Method : “Cryptocurrencies via CoinGate” and then click the check box “Enabled”. After Enabled is clicked then press on CoinGate on the same page. If needed, change the Description and the Title according to your preferences.
5. Enter your API credentials on the WooCommerce configuration page.
At this point, you can also configure how CoinGate order statuses will correspond to WooCommerce order statuses. Leave the default options if you are not sure.
6. If you are using Sandbox API credentials, then turn Test Mode on.
7. Finally, click “Save changes”, and you are good to go!

## Disclaimer

CoinGate is an independent payment platform and is not affiliated, associated, authorised, endorsed by, or in any way officially connected with WooCommerce. This plugin is a third-party developer integration provided solely for enabling CoinGate services on WooCommerce stores.


