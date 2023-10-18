<?php 

/*
 * Plugin Name:       Wookaspi
 * Plugin URI:        https://wookaspi.kz/download
 * Description:       Kaspi Payment Gateway for Woocommerce
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Zhaniya Meiramova
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://wookaspi.kz/download
 * Text Domain:       wookaspi
 * Domain Path:       /languages
 */

 if(! in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins',get_option('active_plugins')))){
return;
 }

 add_action( 'plugins_loaded', 'wookaspi_init', 11);

  function wookaspi_init(){
    if(class_exists('WC_Payment_Gateway')){
            class WC_Kaspi_Gateway extends WC_Payment_Gateway{
                public function __construct(){
                    $this ->id = 'kaspi_payment';
                    $this ->icon = apply_filters( 'woocommerce_kaspi_icon', plugins_url('/assets/icon.png',__FILE__));
                    $this ->has_fields = false;
                    $this ->method_title = __('Kaspi.kz', 'wookaspi');
                    $this ->method_description = __('Kaspi Payment Gateway for Woocommerce', 'wookaspi');;
                    $this ->init_form_fields();
                    $this ->init_settings();
                    $this -> title = $this->get_option('title');
                    $this -> description = $this->get_option('description');
                    $this -> instructions = $this->get_option('instructions');
                    add_action('woocommerce_update_options_payment_gateways' . $this->id, array($this, 'process_admin_options'));
                    add_action('woocommerce_thank_you_' .$this->id, array($this, 'thank_you_page'));
                }



                public function init_form_fields(){
                    $this -> form_fields = apply_filters( 'wookaspi_fields', array(
                        'enabled' => array(
                            'title' =>__('Enabled/Didabled', 'wookaspi'),
                            'type' => 'checkbox',
                            'label' => __('Enable or Disable WooKaspi','wookaspi'),
                            'default' => 'no'
                        ),
                        'api_token' => array(
                            'title' =>__('API Authorization Token', 'wookaspi'),
                            'type' => 'text',
                            'description' => __('Enter your API Token','wookaspi'),
                            'default' => '',
                            'desc_tip' =>__('You can now generate an API token yourself in the “Settings” section','wookaspi')
                        ),
                        'login' => array(
                            'title' => '<button>Login to Kaspi Pay</button>',
                            'type' =>'checkbox',
                            'label' => __('Save','wookaspi'),
                            
                        ),
                        'title' => array(
                            'title' => __('Pay with Kaspi.kz','wookaspi'),
                            'type' =>'text',
                            'default' => __('Pay with Kaspi.kz','wookaspi'),
                            'description' =>__('Add a new title','wookaspi')
                            
                        ),
                        'description' => array(
                            'title' => __('Kaspi Pay Description','wookaspi'),
                            'type' =>'textarea',
                            'default' => __('Please use kaspi.kz payment gateway if you want to use it','wookaspi'),
                            'description' =>__('Add a new Description','wookaspi')
                            
                        ),
                        'instructions' => array(
                            'title' => __('Kaspi Pay Instructions','wookaspi'),
                            'type' =>'textarea',
                            'default' => __('Please pay with you kaspi.kz mobile app','wookaspi'),
                            'description' =>__('Add Instructions','wookaspi')
                            
                        ),
                    ) );
                }

                public function process_payments($order_id ){
                    $order_id = wc_get_order($order_id);

                    $order-> update_status('on-hold', __('Avaiting new payment','wookaspi'));
                    $this ->clear_payment_api();
                    $order->reduce_order_stock();
                    WC()->cart->empty_cart();
                    return array(
                        'result'=>'success',
                        'redirect'=> $this->get_return_url($order)
                    );
                }

                public function clear_payment_api(){

                }
                public function thank_you_page(){
                    if($this->instructions){
                        echo wpautop( $this->instructions);
                    }
                }
            }
    };
 }

 add_filter( 'woocommerce_payment_gateways', 'kaspi_payment_gateway');
  
function kaspi_payment_gateway($gateways){
    $gateways[] = 'WC_Kaspi_Gateway';
    return $gateways;

}