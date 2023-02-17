<?php

/*
Plugin Name: NEF Custom Shipping Rates
Version: 1.0
Description: Sets shipping rates based on percent of order total
Author: Rory McDaniel

*/


function nef_shipping_method_init() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		require_once(__DIR__ . '/nef-custom-shipping-rates-class.php');
	}

}
add_action( 'woocommerce_shipping_init', 'nef_shipping_method_init' );

function nef_add_shipping_method($methods)
{
	$methods['nef_percentage_shipping'] = 'WC_NEF_Percentage_Shipping';
	return $methods;
}
add_filter('woocommerce_shipping_methods', 'nef_add_shipping_method');
