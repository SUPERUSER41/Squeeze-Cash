<?php


class WC_Squeeze_Cash_Gateway extends WC_Payment_Gateway {


	/**
	 * WC_Squeeze_Cash_Gateway constructor.
	 */
	public function __construct() {
		//Define payment gateway plugin ID
		$this->id = 'squeeze_cash';
		//Define payment gateway icon
		$this->icon = plugins_url('../assets/logo.svg', __FILE__);
		//Define custom fields on checkout
		$this->has_fields = false;
		//Define title of plugin (this will display in woocommerce settings)
		$this->method_title = __('Squeeze Pay', 'squeeze-cash');
		//Define description of plugin (this will display in woocommerce settings)
		$this->method_description = __('Accept payments with squeeze cash.', 'squeeze-cash');

		//Tell woocommerce what we are supporting. Gateways can support subscriptions, refunds, saved payment methods
		$this->supports = array(
			'products',
		);

		//Define function to initialize form fields
		$this->init_form_fields();

		//Define function to initialize settings
		$this->init_settings();
		$this->title = 'Squeeze Cash';
		$this->description = 'Pay with Squeeze Cash';
		$this->enabled = $this->get_option('enabled');
		//Squeeze merchant settings
		$this->squeeze_merchant_id = $this->get_option('squeeze_merchant_id');
		$this->squeeze_access_token = $this->get_option('squeeze_access_token');

		if ( is_admin() ) {
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options'));
		}
		add_action('wp_enqueue_scripts', array($this, 'add_squeeze_checkout_css'));
		add_action('wp_enqueue_scripts', array($this, 'add_squeeze_checkout_js'));
	}


	public function init_form_fields() {
		$this->form_fields = array(

			'enabled' => array(
				'title' => 'Enable/Disable',
				'type' => 'checkbox',
				'label' => 'Squeeze Cash Payment Gateway',
				'default' => 'no',
				'description' => ''
			),

			'squeeze_merchant_id' => array(
				'title'       => 'Squeeze ID',
				'type'        => 'text',
				'description' => 'This is the ID provided by Squeeze Cash in your merchant account.',
				'default'     => 'SQ00006',
				'required' => true,
				'desc_tip'    => true,
			),

			'squeeze_access_token' => array(
				'title'       => 'Access Token',
				'type'        => 'password',
				'required' => true,
				'description' => 'This is the access token provided by Squeeze Cash when you signed up for merchant account.',
				'default'     => 'gvo2tr216cqfm',
				'desc_tip'    => true,
			),
		);
	}



	public function payment_fields() {
		global $woocommerce;
		do_action( 'woocommerce_credit_card_form_start', $this->id );
		echo'
			<div class="squeeze-container">
			  	<div class="squeeze-merchant">
			        <div class="squeeze-merchant__image">
			          <img  src=" '.$_COOKIE['merchant_picture']. ' " alt="merchant image" />
			        </div>
			        <div class="squeeze-merchant__name">
			          <p>'.$_COOKIE['merchant_name'].'</p>
			          <p>'.$this->squeeze_merchant_id.'</p>
			        </div>
			     </div>
			     <div class="squeeze-logo-cost">
			        <img src=" '.plugins_url('../assets/logo.svg', __FILE__). ' " alt="squeeze cash logo" />
			        <p class="squeeze-logo-cost__cost">
			         <span><img src=" '.plugins_url('../assets/shopping_cart.svg', __FILE__). ' " alt="cart icon" /></span> 
			          '.$_COOKIE['merchant_currency'].$woocommerce->cart->get_cart_total().'
			     	</p>
			     </div>
      			<h2>Pay with Squeeze Cash</h2>
		      	<div class="squeeze-checkout-form"  >
			        <div class="squeeze-checkout-form__field-group">
			          <label for="squeeze-user-id">Squeeze User ID</label>
			          <input
			            maxlength="7"
			            name="squeeze-user-id"
			            id="squeeze-user-id"
			            placeholder="SQ"
			            type="text"
			          />
			        </div>
			        <div class="squeeze-checkout-form__field-group">
			          <label for="squeeze-user-pin">Squeeze User PIN</label>
			          <input
			            name="squeeze-user-pin"
			            maxlength="4"
			            id="squeeze-user-pin"
			            placeholder="4 Digit PIN"
			            type="password"
			          />
			        </div>
		
			        <div class="squeeze-checkout-form__disclaimer">
			          <img src=" '.plugins_url('../assets/padlock.svg', __FILE__). ' " alt="padlock" />
			          <p>Your Squeeze account information will not be stored</p>
			        </div>

			        <p class="squeeze-checkout-form__terms-privacy">
			          By paying via a merchant’s website, you agree to Squeeze Cash’s Terms
			          of Use and Privacy Policy.
			        </p>
		      	</div>
    		</div>';
		do_action( 'woocommerce_credit_card_form_end', $this->id );
	}
	public function add_squeeze_checkout_css(){
		wp_register_style('squeeze-cash-css', plugin_dir_url(__FILE__) .'../styles/styles.css');
		wp_enqueue_style('squeeze-cash-css');
	}
	public function add_squeeze_checkout_js(){
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
			return;
		}
		//if our payment gateway is disabled, we do not have to enqueue JS too
		if ( 'no' === $this->enabled ) {
			return;
		}

		// no reason to enqueue JavaScript if API keys are not set
		if ( empty( $this->squeeze_merchant_id ) || empty( $this->squeeze_access_token ) ) {
			return;
		}
		wp_register_script (
			'squeeze-cash-js',
			plugins_url('../scripts/squeeze-cash.js', __FILE__),
			['jquery'],
			'1.0.0',
			true
		);

		wp_localize_script('squeeze-cash-js', 'squeezeCashAjax', [
			'merchantId' => $this->squeeze_merchant_id,
			'accessToken' => $this->squeeze_access_token,
		]);

		wp_enqueue_script('squeeze-cash-js');
	}

	public function process_payment( $order_id ) {
		global $woocommerce;

		$squeeze_user_id = '';
		$squeeze_user_pin = '';

		if('squeeze_cash' === $_POST['payment_method'] && !isset($_POST['squeeze-user-id']) || empty($_POST['squeeze-user-id'])  ){
			wc_add_notice('Error: Please enter your Squeeze Cash ID', 'error');
		}else{
			$squeeze_user_id = $_POST['squeeze-user-id'];
		}

		if('squeeze_cash' === $_POST['payment_method'] && !isset($_POST['squeeze-user-pin']) || empty($_POST['squeeze-user-pin'])  ){
			wc_add_notice('Error: Please enter your Squeeze Cash Pin', 'error');
		}else{
			$squeeze_user_pin = $_POST['squeeze-user-pin'];
		}


		$order = wc_get_order( $order_id );

		$url = 'https://us-central1-squeeze-a69e9.cloudfunctions.net/makePayment';

		$arg_data = array(
			'userSqueeze' => $squeeze_user_id,
			'merchantSqueeze' => $this->squeeze_merchant_id,
			'accessToken' => $this->squeeze_access_token,
			'userPin' => $squeeze_user_pin,
			'amount' => '1',
		    'reason' => 'Squeeze Payment'
		);

		$data = json_encode($arg_data);

		$args = array(
			'headers' => array('Content-Type' => 'text/plain','Access-Control-Allow-Origin' =>'*'),
			'body' => $data);

		$response = wp_remote_post($url, $args);

		if(!is_wp_error( $response )){
			$response_body = wp_remote_retrieve_body($response);

			$response_body = json_decode($response_body);

			if($response_body->type == 'error'){
				wc_add_notice($response_body->message, 'error');
			}else{
				$order->payment_complete();

				$order->add_order_note( 'Your order has been received. Thank You!', true );

				// Remove cart
				$woocommerce->cart->empty_cart();

				// Return thankyou redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			}

		}

	}



	public function process_admin_options() {
		parent::process_admin_options();

		$url = 'https://us-central1-squeeze-a69e9.cloudfunctions.net/authenticateMerchant';

		$arg_data = array('squeezeId' => $this->squeeze_merchant_id,'accessToken' => $this->squeeze_access_token);

		$data = json_encode($arg_data);

		$args = array(
			'headers' => array('Content-Type' => 'text/plain','Access-Control-Allow-Origin' =>'*'),
			'body' => $data);

		$response = wp_remote_post($url, $args);

		$response_body = wp_remote_retrieve_body($response);

		$response_body = json_decode($response_body);

		if($response_body->errorCode == 0){
			setcookie('merchant_name', $response_body->response->merchantName, time() + (86400 * 30), "/");
			setcookie('merchant_picture', $response_body->response->picture, time() + (86400 * 30), "/");
			setcookie('merchant_currency', $response_body->response->currency, time() + (86400 * 30), "/");
		}else{
			WC_Admin_Settings::add_error('Error: Failed to authenticate your merchant account, please try again.  ');
		}

	}

}

