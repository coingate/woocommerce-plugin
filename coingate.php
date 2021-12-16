<?php

/*
Plugin Name: WooCommerce Payment Gateway - CoinGate
Plugin URI: https://coingate.com
Description: Accept Bitcoin via CoinGate in your WooCommerce store
Version: 1.2.4
Author: CoinGate
Author URI: https://coingate.com
License: MIT License
License URI: https://github.com/coingate/woocommerce-plugin/blob/master/LICENSE
Github Plugin URI: https://github.com/coingate/woocommerce-plugin
*/

add_action('plugins_loaded', 'coingate_init');

define('COINGATE_WOOCOMMERCE_VERSION', '1.2.4');

function coingate_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    };

    define('PLUGIN_DIR', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)) . '/');

    require_once(__DIR__ . '/lib/coingate/init.php');

    class WC_Gateway_Coingate extends WC_Payment_Gateway
    {
        public function __construct()
        {
            global $woocommerce;

            $this->id = 'coingate';
            $this->has_fields = false;
            $this->method_title = 'CoinGate';
            $this->icon = apply_filters('woocommerce_coingate_icon', PLUGIN_DIR . 'assets/bitcoin.png');

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->api_secret = $this->get_option('api_secret');
            $this->api_auth_token = (empty($this->get_option('api_auth_token')) ? $this->get_option('api_secret') : $this->get_option('api_auth_token'));
            $this->receive_currency = $this->get_option('receive_currency');
            $this->order_statuses = $this->get_option('order_statuses');
            $this->test = ('yes' === $this->get_option('test', 'no'));

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_order_statuses'));
            add_action('woocommerce_thankyou_coingate', array($this, 'thankyou'));
            add_action('woocommerce_api_wc_gateway_coingate', array($this, 'payment_callback'));
        }

        public function admin_options()
        {
            ?>
            <h3><?php _e('CoinGate', 'woothemes'); ?></h3>
            <p><?php _e('Accept Bitcoin through the CoinGate.com and receive payments in euros and US dollars.<br>
        <a href="https://developer.coingate.com/docs/issues" target="_blank">Not working? Common issues</a> &middot; <a href="mailto:support@coingate.com">support@coingate.com</a>', 'woothemes'); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
            <?php

        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable CoinGate', 'woocommerce'),
                    'label' => __('Enable Cryptocurrency payments via CoinGate', 'woocommerce'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no',
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('The payment method description which a user sees at the checkout of your store.', 'woocommerce'),
                    'default' => __('Pay with BTC, LTC, ETH, XMR, XRP, BCH and other cryptocurrencies. Powered by CoinGate.'),
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('The payment method title which a customer sees at the checkout of your store.', 'woocommerce'),
                    'default' => __('Cryptocurrencies via CoinGate (more than 50 supported)', 'woocommerce'),
                ),
                'api_auth_token' => array(
                    'title' => __('API Auth Token', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('CoinGate API Auth Token', 'woocommerce'),
                    'default' => (empty($this->get_option('api_secret')) ? '' : $this->get_option('api_secret')),
                ),
                'receive_currency' => array(
                    'title' => __('Payout Currency', 'woocommerce'),
                    'type' => 'select',
                    'options' => array(
                        'BTC' => __('Bitcoin (฿)', 'woocommerce'),
                        'USDT' => __('USDT', 'woocommerce'),
                        'EUR' => __('Euros (€)', 'woocommerce'),
                        'USD' => __('U.S. Dollars ($)', 'woocommerce'),
                        'DO_NOT_CONVERT' => __('Do not convert', 'woocommerce')
                    ),
                    'description' => __('Choose the currency in which your payouts will be made (BTC, EUR or USD). For real-time EUR or USD settlements, you must verify as a merchant on CoinGate. Do not forget to add your Bitcoin address or bank details for payouts on <a href="https://coingate.com" target="_blank">your CoinGate account</a>.', 'woocomerce'),
                    'default' => 'BTC',
                ),
                'order_statuses' => array(
                    'type' => 'order_statuses'
                ),
                'test' => array(
                    'title' => __('Test (Sandbox)', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable Test Mode (Sandbox)', 'woocommerce'),
                    'default' => 'no',
                    'description' => __('To test on <a href="https://sandbox.coingate.com" target="_blank">CoinGate Sandbox</a>, turn Test Mode "On".
                    Please note, for Test Mode you must create a separate account on <a href="https://sandbox.coingate.com" target="_blank">sandbox.coingate.com</a> and generate API credentials there.
                    API credentials generated on <a href="https://coingate.com" target="_blank">coingate.com</a> are "Live" credentials and will not work for "Test" mode.', 'woocommerce'),
                ),
            );
        }

        public function thankyou()
        {
            if ($description = $this->get_description()) {
                echo wpautop(wptexturize($description));
            }
        }

        public function process_payment($order_id)
        {
            global $woocommerce, $page, $paged;
            $order = new WC_Order($order_id);

            $this->init_coingate();

            $description = array();
            foreach ($order->get_items('line_item') as $item) {
                $description[] = $item['qty'] . ' × ' . $item['name'];
            }

            $token = get_post_meta($order->get_id(), 'coingate_order_token', true);

            if (empty($token)) {
                $token = substr(md5(rand()), 0, 32);

                update_post_meta($order_id, 'coingate_order_token', $token);
            }

            $wcOrder = wc_get_order($order_id);

            $order = \CoinGate\Merchant\Order::create(array(
                'order_id'          => $order->get_id(),
                'price_amount'      => number_format($order->get_total(), 8, '.', ''),
                'price_currency'    => get_woocommerce_currency(),
                'receive_currency'  => $this->receive_currency,
                'cancel_url'        => $order->get_cancel_order_url(),
                'callback_url'      => trailingslashit(get_bloginfo('wpurl')) . '?wc-api=wc_gateway_coingate',
                'success_url'       => add_query_arg('order-received', $order->get_id(), add_query_arg('key', $order->get_order_key(), $this->get_return_url($wcOrder))),
                'title'             => get_bloginfo('name', 'raw') . ' Order #' . $order->get_id(),
                'description'       => implode(', ', $description),
                'token'             => $token
            ));

            if ($order && $order->payment_url) {
                return array(
                    'result' => 'success',
                    'redirect' => $order->payment_url,
                );
            } else {
                return array(
                    'result' => 'fail',
                );
            }
        }

        public function payment_callback()
        {
            $request = $_REQUEST;

            global $woocommerce;

            $order = new WC_Order($request['order_id']);


            try {
                if (!$order || !$order->get_id()) {
                    throw new Exception('Order #' . $request['order_id'] . ' does not exists');
                }

                $token = get_post_meta($order->get_id(), 'coingate_order_token', true);

                if (empty($token) || strcmp(empty($_GET['token']) ? $request['token'] : $_GET['token'], $token) !== 0) {
                    throw new Exception('Callback token does not match');
                }

                $this->init_coingate();
                $cgOrder = \CoinGate\Merchant\Order::find($request['id']);

                if (!$cgOrder) {
                    throw new Exception('CoinGate Order #' . $order->get_id() . ' does not exists');
                }

                $orderStatuses = $this->get_option('order_statuses');
                $wcOrderStatus = $orderStatuses[$cgOrder->status];
                $wcExpiredStatus = $orderStatuses['expired'];
                $wcCanceledStatus = $orderStatuses['canceled'];
                $wcPaidStatus = $orderStatuses['paid'];

                switch ($cgOrder->status) {
                    case 'paid':
                        $statusWas = "wc-" . $order->status;

                        $order->update_status($wcOrderStatus);
                        $order->add_order_note(__('Payment is confirmed on the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.', 'coingate'));
                        $order->payment_complete();

                        if ($order->status == 'processing' && ($statusWas == $wcExpiredStatus || $statusWas == $wcCanceledStatus)) {
                            WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order->get_id());
                        }
                        if (($order->status == 'processing' || $order->status == 'completed') && ($statusWas == $wcExpiredStatus || $statusWas == $wcCanceledStatus)) {
                            WC()->mailer()->emails['WC_Email_New_Order']->trigger($order->get_id());
                        }
                        break;
                    case 'invalid':
                        $order->update_status($wcOrderStatus);
                        $order->add_order_note(__('Payment rejected by the network or did not confirm within 7 days.', 'coingate'));
                        break;
                    case 'expired':
                        if($order->get_payment_method() === "coingate") {
                            $order->update_status($wcOrderStatus);
                            $order->add_order_note(__('Buyer did not pay within the required time and the invoice expired.',
                                'coingate'));
                        }
                        break;
                    case 'canceled':
                        $order->update_status($wcOrderStatus);
                        $order->add_order_note(__('Buyer canceled the invoice.', 'coingate'));
                        break;
                    case 'refunded':
                        $order->update_status($wcOrderStatus);
                        $order->add_order_note(__('Payment was refunded to the buyer.', 'coingate'));
                        break;
                }
            } catch (Exception $e) {
                die(get_class($e) . ': ' . $e->getMessage());
            }
        }

        public function generate_order_statuses_html()
        {
            ob_start();

            $cgStatuses = $this->cgOrderStatuses();
            $wcStatuses = wc_get_order_statuses();
            $defaultStatuses = array('paid' => 'wc-processing', 'invalid' => 'wc-failed', 'expired' => 'wc-cancelled', 'canceled' => 'wc-cancelled', 'refunded' => 'wc-refunded');

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">Order Statuses:</th>
                <td class="forminp" id="coingate_order_statuses">
                    <table cellspacing="0">
                        <?php
                        foreach ($cgStatuses as $cgStatusName => $cgStatusTitle) {
                            ?>
                            <tr>
                                <th><?php echo $cgStatusTitle; ?></th>
                                <td>
                                    <select name="woocommerce_coingate_order_statuses[<?php echo $cgStatusName; ?>]">
                                        <?php
                                        $orderStatuses = get_option('woocommerce_coingate_settings');
                                        $orderStatuses = $orderStatuses['order_statuses'];

                                        foreach ($wcStatuses as $wcStatusName => $wcStatusTitle) {
                                            $currentStatus = $orderStatuses[$cgStatusName];

                                            if (empty($currentStatus) === true)
                                                $currentStatus = $defaultStatuses[$cgStatusName];

                                            if ($currentStatus == $wcStatusName)
                                                echo "<option value=\"$wcStatusName\" selected>$wcStatusTitle</option>";
                                            else
                                                echo "<option value=\"$wcStatusName\">$wcStatusTitle</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <?php

            return ob_get_clean();
        }

        public function validate_order_statuses_field()
        {
            $orderStatuses = $this->get_option('order_statuses');

            if (isset($_POST[$this->plugin_id . $this->id . '_order_statuses']))
                $orderStatuses = $_POST[$this->plugin_id . $this->id . '_order_statuses'];

            return $orderStatuses;
        }

        public function save_order_statuses()
        {
            $cgOrderStatuses = $this->cgOrderStatuses();
            $wcStatuses = wc_get_order_statuses();

            if (isset($_POST['woocommerce_coingate_order_statuses']) === true) {
                $cgSettings = get_option('woocommerce_coingate_settings');
                $orderStatuses = $cgSettings['order_statuses'];

                foreach ($cgOrderStatuses as $cgStatusName => $cgStatusTitle) {
                    if (isset($_POST['woocommerce_coingate_order_statuses'][$cgStatusName]) === false)
                        continue;

                    $wcStatusName = $_POST['woocommerce_coingate_order_statuses'][$cgStatusName];

                    if (array_key_exists($wcStatusName, $wcStatuses) === true)
                        $orderStatuses[$cgStatusName] = $wcStatusName;
                }

                $cgSettings['order_statuses'] = $orderStatuses;
                update_option('woocommerce_coingate_settings', $cgSettings);
            }
        }

        private function cgOrderStatuses()
        {
            return array('paid' => 'Paid', 'invalid' => 'Invalid', 'expired' => 'Expired', 'canceled' => 'Canceled', 'refunded' => 'Refunded');
        }

        private function init_coingate()
        {
            \CoinGate\CoinGate::config(
                array(
                    'auth_token'    => (empty($this->api_auth_token) ? $this->api_secret : $this->api_auth_token),
                    'environment'   => ($this->test ? 'sandbox' : 'live'),
                    'user_agent'    => ('CoinGate - WooCommerce v' . WOOCOMMERCE_VERSION . ' Plugin v' . COINGATE_WOOCOMMERCE_VERSION),
                )
            );
        }
    }

    function add_coingate_gateway($methods)
    {
        $methods[] = 'WC_Gateway_Coingate';

        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_coingate_gateway');
}
