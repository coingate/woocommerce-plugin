<?php

/*
  Plugin Name: WooCommerce Payment Gateway - CoinGate
  Plugin URI: https://coingate.com
  Description: Accept Bitcoin via CoinGate in your WooCommerce store
  Version: 1.0.4-dev
  Author: CoinGate
  Author URI: https://coingate.com
  License: MIT License
  License URI: https://github.com/coingate/woocommerce-plugin/blob/master/LICENSE.md
  Github Plugin URI: https://github.com/coingate/woocommerce-plugin
 */

add_action('plugins_loaded', 'coingate_init');

define('COINGATE_WOOCOMMERCE_VERSION', '1.0.4-dev');

function coingate_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    };

    define('PLUGIN_DIR', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)).'/');

    require_once dirname(__FILE__).'/lib/coingate/init.php';

    class WC_Gateway_Coingate extends WC_Payment_Gateway
    {
        public function __construct()
        {
            global $woocommerce;

            $this->id = 'coingate';
            $this->has_fields = false;
            $this->method_title = __('CoinGate', 'woocommerce');
            $this->icon = apply_filters('woocommerce_paypal_icon', PLUGIN_DIR.'assets/bitcoin.png');

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->app_id = $this->settings['app_id'];
            $this->api_key = $this->settings['api_key'];
            $this->api_secret = $this->settings['api_secret'];
            $this->receive_currency = $this->settings['receive_currency'];
            $this->test = $this->settings['test'];

            add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_thankyou_coingate', array($this, 'thankyou'));
            add_action('coingate_callback', array($this, 'payment_callback'));
            add_action('woocommerce_api_wc_gateway_coingate', array($this, 'check_callback_request'));
        }

        public function admin_options()
        {
            ?>
                <h3><?php _e('CoinGate', 'woothemes'); ?></h3>
                <p><?php _e('Accept Bitcoin through the CoinGate.com and receive payments in euros and US dollars.', 'woothemes'); ?></p>
                <p style="display: block; background: #d9edf7; border: 1px solid #bce8f1; color: #31708f; padding: 12px;"><strong>Having trouble?</strong> We can help you! Download the log file from <code><?php echo wc_get_log_file_path('coingate'); ?></code> and send it to <a href="mailto:support@coingate.com">support@coingate.com</a></p>
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
                    'label' => __('Enable Bitcoin payment via CoinGate', 'woocommerce'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no',
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'woocommerce'),
                    'default' => __('Pay with Bitcoin via Coingate'),
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Payment method title that the customer will see on your website.', 'woocommerce'),
                    'default' => __('Bitcoin', 'woocommerce'),
                ),
                'app_id' => array(
                    'title' => __('App ID', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('CoinGate App ID', 'woocommerce'),
                    'default' => '',
                ),
                'api_key' => array(
                    'title' => __('API Key', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('CoinGate API Key', 'woocommerce'),
                    'default' => __('', 'woocommerce'),
                ),
                'api_secret' => array(
                    'title' => __('API Secret', 'woocommerce'),
                    'type' => 'text',
                    'default' => '',
                    'description' => __('CoinGate API Secret', 'woocommerce'),
                ),
                'receive_currency' => array(
                    'title' => __('Receive Currency', 'woocommerce'),
                    'type' => 'select',
                    'options' => array(
                        'EUR' => __('Euros (€)', 'woocommerce'),
                        'USD' => __('US Dollars ($)', 'woocommerce'),
                        'BTC' => __('Bitcoin (฿)', 'woocommerce'),
                    ),
                    'description' => __('The currency in which you wish to receive your payments.', 'woocomerce'),
                    'default' => 'EUR',
                ),
                'test' => array(
                    'title' => __('Test', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable test mode', 'woocommerce'),
                    'default' => 'no',
                    'description' => __('Enable this to accept test payments (sandbox.coingate.com). <a href="http://support.coingate.com/knowledge_base/topics/how-can-i-test-your-service-without-signing-up" target="_blank">Read more about testing</a>', 'woocommerce'),
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
                $description[] = $item['qty'].' × '.$item['name'];
            }

            $token = get_post_meta($order->id, 'coingate_order_token', true);

            if ($token == '') {
                $token = substr(md5(rand()), 0, 32);

                update_post_meta($order_id, 'coingate_order_token', $token);
            }

            $order = \CoinGate\Merchant\Order::create(array(
              'order_id' => $order->id,
              'price' => number_format($order->get_total(), 2, '.', ''),
              'currency' => get_woocommerce_currency(),
              'receive_currency' => $this->receive_currency,
              'cancel_url' => $order->get_cancel_order_url(),
              'callback_url' => trailingslashit(get_bloginfo('wpurl')).'?wc-api=wc_gateway_coingate&token='.$token,
              'success_url' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('thanks')))),
              'title' => get_bloginfo('name', 'raw').' Order #'.$order->id,
              'description' => implode($description, ', '),
            ));

            if ($order && $order->payment_url) {
                return array(
                    'result' => 'success',
                    'redirect' => $order->payment_url,
                );
            } else {
                $this->log('Request');

                return array(
                    'result' => 'fail',
                );
            }
        }

        public function check_callback_request()
        {
            @ob_clean();
            do_action('coingate_callback', $_REQUEST);
        }

        public function payment_callback($request)
        {
            global $woocommerce;

            $order = new WC_Order($request['order_id']);

            try {
                if (!$order || !$order->id) {
                    throw new Exception('Order #'.$request['order_id'].' does not exists');
                }

                $token = get_post_meta($order->id, 'coingate_order_token', true);

                if (empty($token) || $_GET['token'] != $token) {
                    throw new Exception('Token: '.$_GET['token'].' do not match');
                }

                $this->init_coingate();
                $cgOrder = \CoinGate\Merchant\Order::find($request['id']);

                if (!$cgOrder) {
                    $this->log('Callback');

                    throw new Exception('CoinGate Order #'.$order->id.' does not exists');
                }

                if (!is_array($cgOrder)) {
                    throw new Exception('Something wrong with callback');
                }

                if ($cgOrder->status == 'paid') {
                    $order->add_order_note(__('Payment was completed successfully', 'woocomerce'));
                    $order->payment_complete();
                } elseif ($cgOrder->status == 'refunded') {
                    $order->update_status('refunded', __('Payment refunded to customer', 'woocomerce'));
                } elseif (in_array($coingate_response['status'], array('invalid', 'expired', 'canceled'))) {
                    $order->update_status('failed', __('Payment failed', 'woocomerce'));
                }
            } catch (Exception $e) {
                echo get_class($e).': '.$e->getMessage();
            }
        }

        private function log($name, $customData = '')
        {
            $logger = new WC_Logger();
            $logger->add('coingate', $name
                .' - App ID: '.$this->app_id
                .'; Mode: '.($this->test == '1' ? 'sandbox' : 'live')
                .'; PHP Version: '.phpversion()
                .'; cURL Version: '.json_encode(curl_version())
                .'; WordPress Version: '.get_bloginfo('version')
                .'; WooCommerce Version: '.WOOCOMMERCE_VERSION
                .'; Plugin Version: '.COINGATE_WOOCOMMERCE_VERSION
                .$customData
                ."\n");
        }

        private function init_coingate()
        {
          \CoinGate\CoinGate::config(
            array(
              'app_id' => $this->app_id,
              'api_key' => $this->api_key,
              'api_secret' => $this->api_secret,
              'mode' => $this->test == '1' ? 'sandbox' : 'live',
              'user_agent' => 'CoinGate - WooCommerce v' . WOOCOMMERCE_VERSION . ' Plugin v'.COINGATE_WOOCOMMERCE_VERSION,
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
