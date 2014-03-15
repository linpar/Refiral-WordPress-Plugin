<?php
/**
* Plugin Name: Refiral
* Plugin URI: http://www.refiral.com
* Description: Launch your referral campaign virally. Boost your sales upto 3X with our new hybrid marketing channel. Run your personalized, easy to integrate fully automated referral program.
* Version: 1.0
* Author: Refiral
* Author URI: http://www.refiral.com
* License: GPLv2
**/
class Refiral
{
    private $plugin_id;
    private $order_id;  // store the current order id
    private $options;

    public function __construct($id) {
    	$this->plugin_id = $id;
    	$this->options = array('refiral_key' => '', 'refiral_enable' => 'on' );

        register_activation_hook(__FILE__, array(&$this, 'update_refiral_options'));

        // Check if WooCommerce is active
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('woocommerce_checkout_order_processed', array(&$this, 'refiral_invoice'));
            add_action('wp_head', array(&$this, 'refiral_campaign_script'));
        }

        add_action('admin_menu', array(&$this, 'refiral_admin_options'));
        add_action('admin_init', array(&$this, 'init'));
    }
    public function init()
    {
        register_setting($this->plugin_id.'_options', $this->plugin_id);
    }

    public function update_refiral_options()
    {
        update_option($this->plugin_id, $this->options);
    }

    private function get_refiral_options()
    {
        $this->options = get_option($this->plugin_id);
    }

    public function refiral_invoice($order_id) {
        session_start();
        $_SESSION['refiral_invoice'] = $order_id;
    }

    public function refiral_campaign_script() {
        session_start();
        $this->get_refiral_options();
        if($this->options['refiral_enable'] == 'on')
        {
            echo '<script>';
            echo 'var apiKey="'.$this->options['refiral_key'].'";</script>';
            echo '<script src="http://refiral.com/api/woocommerce.js"></script>';

            if(isset($_SESSION['refiral_invoice']) && $_SESSION['refiral_invoice'] != -1)
            {
                $this->order_id = $_SESSION['refiral_invoice'];
                $_SESSION['refiral_invoice'] = -1;
                if(class_exists('WC_Order'))
                {
                    $order = new WC_Order($this->order_id);
                    $order_total = $order->get_total( );
                    $order_subtotal = $order->get_subtotal_to_display();
                    $order_subtotal = preg_replace("/[^0-9.]/", "", $order_subtotal);
                    $order_subtotal = round(preg_replace('{^\.}', '', $order_subtotal, 1));
                    $order_coupons = $order->get_used_coupons( );
                    $order_coupon = $order_coupons[0];
                    $order_items = ($order->get_items());
                    foreach ($order_items as $order_item) {
                        $cartInfo =  $order_item['name']. ' ' .$order_item['product_id']. ', ';
                    }
                    $order_email = $order->billing_email;
                    $order_name = $order->billing_first_name.' '.$order->billing_last_name;
                    echo "<script>";
                    echo "invoiceRefiral('$order_total', '$order_subtotal', '$order_coupon', '$cartInfo', '$order_name', '$order_email');";
                    echo "</script>";
                }
            }
        }
    }

	public function refiral_admin_options() {
		add_options_page('Refiral Options', 'Refiral', 'manage_options', $this->plugin_id.'-options', array(&$this, 'refiral_options_page'));
	 
	}

    public function refiral_options_page()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You can manage options from the Settings->Refiral Options menu.') );
        }
        
        include('refiral_options.php');
    }

}
$Refiral = new Refiral('refiral');
?>