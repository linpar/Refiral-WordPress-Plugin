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

// Version check
global $wp_version;
if(!version_compare($wp_version, '3.0', '>='))
{
    die("Refiral requires WordPress 3.0 or above. <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update WordPress to latest Version!</a>");
}
// END - Version check


// Check if class exists already.\
if(!class_exists('Refiral')) { 

    class Refiral
    {
        private $plugin_id; // Plugin's id
        private $order_id;  // store the current order id
        private $options;

        // Defining Constructor
        public function __construct($id) {
            $this->plugin_id = $id;
            $this->options = array('refiral_key' => '', 'refiral_enable' => 'on' );

            register_activation_hook(__FILE__, array(&$this, 'update_refiral_options'));

            // Initiallizing plugin admin options
            add_action('admin_init', array(&$this, 'init'));
            // Add admin menu item
            add_action('admin_menu', array(&$this, 'refiral_admin_options'));

            // Check if WooCommerce is active
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                // Execute refiral_inovice if order processed
                add_action('woocommerce_checkout_order_processed', array(&$this, 'refiral_invoice'));
                // Add plugin's code at wp_head
                add_action('wp_head', array(&$this, 'refiral_campaign_script'));
            }
        }

        // Initiallize
        public function init()
        {
            register_setting($this->plugin_id.'_options', $this->plugin_id);
        }

        // Update new options in Database
        public function update_refiral_options()
        {
            update_option($this->plugin_id, $this->options);
        }

        // Get options from Database
        private function get_refiral_options()
        {
            $this->options = get_option($this->plugin_id);
        }

        // Add order_id in session variable
        public function refiral_invoice($order_id) {
            // Start Session
            session_start();
            $_SESSION['refiral_invoice'] = $order_id;
        }

        // Add script at wp_head
        public function refiral_campaign_script() {
            // Start Session
            session_start();
            $this->get_refiral_options();
            if($this->options['refiral_enable'] == 'on' && $this->options['refiral_key'] != '')
            {
            	$flag = false;
                // Script Javascript Code
                echo '<script>';
                echo 'var apiKey="'.$this->options['refiral_key'].'";';

                // If order processed
                if(isset($_SESSION['refiral_invoice']) && $_SESSION['refiral_invoice'] != -1)
                {
                    // Get order id from session variable
                    $this->order_id = $_SESSION['refiral_invoice'];
                    // Reset session variable
                    $_SESSION['refiral_invoice'] = -1;
                    // Check if WC_Order class exists
                    if(class_exists('WC_Order'))
                    {
                        // Send data to Refiral API
                        $order = new WC_Order($this->order_id);
                        $order_total = $order->get_total( );
                        $order_subtotal = $order->get_subtotal_to_display();
                        $order_subtotal = preg_replace("/[^0-9.]/", "", $order_subtotal);
                        $order_subtotal = preg_replace('{^\.}', '', $order_subtotal, 1);
                        $order_coupons = $order->get_used_coupons( );
                        $order_coupon = $order_coupons[0];
                        $order_items = ($order->get_items());
                        foreach ($order_items as $order_item) {
                            $cartInfoArray[] =  array("id" => $order_item['product_id'], "name" => $order_item['name'], "quantity" => $order_item['qty']);
                        }
                        $cartInfo = json_encode($cartInfoArray);
                        $order_email = $order->billing_email;
                        $order_name = $order->billing_first_name.' '.$order->billing_last_name;
                        $flag = true;
                    }
                }
                if($flag)
                	echo 'var showButton = false;';
                else
                	echo 'var showButton = true;';
                echo '</script>';
                echo '<script src="http://refiral.com/api/all.js"></script>';
                if($flag)
                {
	                echo "<script>";
	                echo "invoiceRefiral('$order_subtotal', '$order_total', '$order_coupon', '$cartInfo', '$order_name', '$order_email');";
	                echo "</script>";
                }
            }
        }

        // Add plugin in Admin Menu
        public function refiral_admin_options() {
            add_options_page('Refiral Options', 'Refiral', 'manage_options', $this->plugin_id.'-options', array(&$this, 'refiral_options_page'));
         
        }

        // Render Admin Options Page
        public function refiral_options_page()
        {
            if (!current_user_can('manage_options'))
            {
                wp_die( __('You can manage options from the Settings->Refiral Options menu.') );
            }

            // Include option's page
            include_once('refiral_options.php');
        }

    }

    // Let's Start
    $Refiral = new Refiral('refiral');
}
?>