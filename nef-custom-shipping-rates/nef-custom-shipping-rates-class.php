<?php
    if (!class_exists('WC_NEF_Percentage_Shipping')) {
        class WC_NEF_Percentage_Shipping extends WC_Shipping_Method
        {
            /**
             * Constructor for your shipping class
             *
             * @access public
             * @return void
             */
            public function __construct()
            {
                $this->id = 'nef_percentage_shipping';
                $this->method_title = __('NEF Percentage Shipping');
				$this->title              = "Percentage Shipping";
                $this->method_description = __('Shipping rates based on cart total.'); //
                // $this->enabled = $this->settings['enabled']; // This can be added as an setting but for this example its forced enabled
				$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
                $this->init();

            }

            /**
             * Init your settings
             *
             * @access public
             * @return void
             */
            public function init()
            {
                // Load the settings API
                $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                // Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            public function get_wc_cart_total(){
                $cart_subtotal = WC()->cart->cart_contents_total + WC()->cart->get_taxes_total(false,false);
                return $cart_subtotal;
            }

            /**
             * calculate_shipping function.
             *
             * @access public
             * @param mixed $package
             * @return void
             */
            public function calculate_shipping($package = array())
            {
                $standard_rate = floatval($this->settings['standard_rate']) / 100;
                $min = floatval($this->settings['minimum']);
                if($standard_rate == 0){
                    $standard_rate = 0.10;// Default to 10%;
                }
                $discount_rate = floatval($this->settings['discount_rate']) / 100;
                if($discount_rate == 0) {
                    $discount_rate = 0.07; // Default to 7%;
                }
                // This is where you'll add your rates
                $total = $this->get_wc_cart_total();
                if ($total < floatval($this->settings['order_amount'])) {
                    $cost = $total * $standard_rate;
                } else {
                    $cost = $total * $discount_rate;
                }
                if ($cost < $min) {
                    $cost = $min;
                }
                $rate = array(
                    'id' => $this->id,
                    'label' => $this->settings['label'],
                    'cost' => $cost,
                    'calc_tax' => 'per_order'
                );


                $this->add_rate($rate);
            }

            /**
             * Initialise Gateway Settings Form Fields
             */
            public function init_form_fields() {
                $this->form_fields = array(
                    'enabled' => array( 'title' => __('Enable/Disable', 'woocommerce'),'type' => 'checkbox','label' => __('Enable this shipping method', 'woocommerce'),'default' => 'yes'),
                    'label' => array(
                        'title' => __( 'Label', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'This controls the label which the user sees during checkout next to the shipping amount.', 'woocommerce' ),
                        'default' => __( 'Shipping', 'woocommerce' )
                    ),
                    'minimum' => array(
                        'title' => __( 'Minimum Shipping Charge', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Any calculated rate below this amount will be rounded up to this amount.', 'woocommerce' ),
                        'default' => __( '3.00', 'woocommerce' )
                    ),
                    'order_amount' => array(
                        'title' => __( 'Cart Amount for Discount', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'A cart that contains this amount or more will receive the discounted rate.', 'woocommerce' ),
                        'default' => __( '2000.00', 'woocommerce' )
                    ),

                    'standard_rate' => array(
                        'title' => __( 'Standard Shipping Rate (Percent)', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Please enter as a whole number, without the percent sign. Decimals are OK.', 'woocommerce' ),
                        'default' => __( '10.00', 'woocommerce' )
                    ),
                    'discount_rate' => array(
                        'title' => __( 'Discounted Shipping Rate (Percent)', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Please enter as a whole number, without the percent sign. Decimals are OK.', 'woocommerce' ),
                        'default' => __( '7.00', 'woocommerce' )
                    ),




                );
            } // End init_form_fields()
        }
    }
