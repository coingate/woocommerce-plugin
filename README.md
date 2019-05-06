# WooCommerce CoinGate Plugin

Accept Bitcoin and 50+ Cryptocurrencies on your WooCommerce store.

Read the plugin installation instructions below to get started with CoinGate Cryptocurrency payment gateway on your shop. Accept Bitcoin, Litecoin, Ethereum and other coins hassle-free - and receive settlements in Bitcoin or in Euros to your bank.
Full setup guide with screenshots is also available on our blog: <https://blog.coingate.com/2017/05/install-woocommerce-bitcoin-plugin/>

## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please take note, that for "Test" mode you **must** generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

Also note, that *Receive Currency* parameter in your module configuration window defines the currency of your settlements from CoinGate. Set it to BTC, EUR, USD or *Do not convert*, depending on how you wish to receive payouts. To receive settlements in **Euros** or **U.S. Dollars** to your bank, you have to verify as a merchant on CoinGate (login to your CoinGate account and click *Verification*). If you set your receive currency to **Bitcoin**, verification is not needed.

### via [WordPress Plugin Manager](https://codex.wordpress.org/Plugins_Add_New_Screen)

1. Go to *Admin » Plugins » Add New* in admin panel.

2. Enter **coingate woocommerce** in search box.

3. Click **Install Now**.

4. Activate the plugin through the **Plugins** menu in WordPress.

5. Enter [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) data to WooCommerce-Coingate Plugin Settings: *Admin » WooCommerce » Settings* click on **Payments** tab find **CoinGate** in Payment Methods table and click **Set up**.

6. Don't forget check **Enable Cryptocurrency payments via CoinGate** checkbox in WooCommerce-Coingate Plugin settings.

### via WooCommerce FTP Uploader

1. Download [woocommerce-coingate-1.2.3.zip](https://github.com/coingate/woocommerce-plugin/releases/download/v1.2.3/woocommerce-coingate-1.2.3.zip).

2. Go to *Admin » Plugins » Add New* in admin panel.

3. Upload *woocommerce-coingate-1.2.3.zip* in *Upload Plugin*

4. Activate the plugin through the **Plugins** menu in WordPress.

5. Enter [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) data to WooCommerce-Coingate Plugin Settings: *Admin » WooCommerce » Settings* click on **Payments** tab find **CoinGate** in Payment Methods table and click **Set up**.

6. Don't forget check **Enable Cryptocurrency payments via CoinGate** checkbox in WooCommerce-Coingate Plugin settings.

### via FTP

1. Download [woocommerce-coingate-1.2.3.zip](https://github.com/coingate/woocommerce-plugin/releases/download/v1.2.3/woocommerce-coingate-1.2.3.zip).

2. Unzip and upload **woocommerce-coingate/** directory to **/wp-content/plugins/** through FTP.

3. Activate the plugin through the **Plugins** menu in WordPress.

4. Enter [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) data to WooCommerce-Coingate Plugin Settings: *Admin » WooCommerce » Settings* click on **Payments** tab find **CoinGate** in Payment Methods table and click **Set up**.

5. Don't forget check **Enable Cryptocurrency payments via CoinGate** checkbox in WooCommerce-Coingate Plugin settings.
