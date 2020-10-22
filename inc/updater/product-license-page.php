<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin License Class
 *
 * @package Admin License Class
 */
class Trending_Mag_Pro_License_Page {

	public $page_slug = '';

	public $download_slug = '';

	public $download_id = '';

	public $download_name = '';

	public $status_slug = '';

	public $store_url = '';

	public $license_key = '';

	public $current_version = '';

	public $author_name = '';

	public $plugin_path = '';

	public function __construct( $config = array() ) {

		$this->set_variables( $config );

		$this->download_updater();

		add_action( 'admin_init', array( $this, 'register_settings' ), 10 );

		add_action( 'admin_init', array( $this, 'activate_license' ), 20 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 998 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		if ( $this->verify_license_status() !== false && $this->verify_license_status() == 'valid' ) {

			add_action( 'admin_init', array( $this, 'trending_mag_pro_define_activated' ), 30 );
		}

	}

	public function set_variables( $config = array() ) {

		$this->download_slug = $config['text_domain'];

		$this->download_id = $config['download_id'];

		$this->download_name = $config['name'];

		$this->store_url = $config['host_url'];

		$this->page_slug = $this->download_slug . '-license-page';

		$this->status_slug = $this->download_slug . '-license-status';

		$this->current_version = $config['version'];

		$this->author_name = $config['author'];

		$this->plugin_path = '/' . $this->download_slug . '/' . $this->download_slug . '.php';

		$this->license_key = trim( get_option( $this->page_slug ) );

	}

	public function register_settings() {

		$settings_args = array(
			'sanitize_callback' => array( $this, 'settings_callback' ),
		);
		register_setting( $this->page_slug . '-group', $this->page_slug, $settings_args );
	}

	public function settings_callback( $field_value ) {

		$old_value = get_option( $this->page_slug );
		if ( $old_value && $old_value != $field_value ) {
			delete_option( $this->status_slug ); // new license has been entered, so must reactivate
		}

		return sanitize_text_field( $field_value );

	}

	public function admin_notices() {

		if ( isset( $_GET['license_activation'] ) && ! empty( $_GET['message'] ) ) {
			switch ( $_GET['license_activation'] ) {
				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
					<?php
					break;
				case 'true':
				default:
					?>
				<div class="notice">
					<p><?php esc_html_e( 'Successfully activated license key', 'trending-mag-pro' ); ?></p>
				</div>
					<?php
					// Developers can put a custom success message here for when activation is successful if they way.
					break;
			}
		}
	}

	public function activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['edd_license_activate'] ) || isset( $_POST['edd_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'edd_product_license_nonce', 'edd_product_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}
			// retrieve the license from the database
			$license = trim( get_option( $this->page_slug ) );
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_id'    => $this->download_id, // The ID of the item in EDD
				'url'        => home_url(),
			);

			if ( isset( $_POST['edd_license_deactivate'] ) ) {
				$api_params['edd_action'] = 'deactivate_license';
			}

			// Call the custom API.
			$response = wp_remote_post(
				$this->store_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$message = ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : esc_html__( 'An error occurred, please try again.', 'trending-mag-pro' );
			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( false === $license_data->success ) {
					switch ( $license_data->error ) {
						case 'expired':
							$message = sprintf(
								esc_html__( 'Your license key expired on %s.', 'trending-mag-pro' ),
								date_i18n(
									get_option( 'date_format' ),
									strtotime(
										$license_data->expires,
										current_time( 'timestamp' )
									)
								)
							);
							break;
						case 'revoked':
							$message = esc_html__( 'Your license key has been disabled.', 'trending-mag-pro' );
							break;
						case 'missing':
							$message = esc_html__( 'Invalid license key. Please enter proper license key and activate it again.', 'trending-mag-pro' );
							break;
						case 'invalid':
						case 'site_inactive':
							$message = esc_html__( 'Your license is not active for this URL.', 'trending-mag-pro' );
							break;
						case 'item_name_mismatch':
							$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'trending-mag-pro' ), $this->download_name );
							break;
						case 'no_activations_left':
							$message = esc_html__( 'Your license key has reached its activation limit.', 'trending-mag-pro' );
							break;
						default:
							$message = esc_html__( 'An error occurred, please try again.', 'trending-mag-pro' );
							break;
					}
				}
			}
			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=' . $this->page_slug );
				$redirect = add_query_arg(
					array(
						'license_activation' => 'false',
						'message'            => urlencode( $message ),
					),
					$base_url
				);
				wp_redirect( $redirect );
				exit();
			}
			// $license_data->license will be either "valid" or "invalid"
			update_option( $this->status_slug, $license_data->license );
			wp_redirect( admin_url( 'admin.php?page=' . $this->page_slug ) );
			exit();
		}
	}

	public function verify_license_status() {

		$license_status = get_option( $this->status_slug );

		return $license_status;
	}

	public function admin_menu() {

		add_menu_page(
			esc_html__( $this->download_name . ' License', 'trending-mag-pro' ),
			esc_html__( $this->download_name . ' License', 'trending-mag-pro' ),
			'manage_options',
			$this->page_slug,
			array(
				$this,
				'admin_menu_callback',
			),
			'dashicons-admin-network',
			3
		);

	}

	public function admin_menu_callback() {

		$product_license = get_option( $this->page_slug );
		$license_status  = get_option( $this->status_slug );
		?>
		<div class="wrap">
			<h2><?php esc_html_e( $this->download_name . ' License Options', 'trending-mag-pro' ); ?></h2>
			<p><?php esc_html_e( 'Please enter license key below and update license key first After updating your license key click on Activate button to activate your license key.', 'trending-mag-pro' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( $this->page_slug . '-group' ); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php esc_html_e( 'License Key', 'trending-mag-pro' ); ?>
							</th>
							<td>
								<input id="<?php echo esc_attr( $this->page_slug ); ?>" name="<?php echo esc_attr( $this->page_slug ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $product_license ); ?>" />
								<label class="description" for="<?php echo esc_attr( $this->page_slug ); ?>"><?php esc_html_e( 'Enter your license key', 'trending-mag-pro' ); ?></label>
							</td>
						</tr>
						<?php if ( false !== $product_license ) { ?>
							<tr valign="top">
								<th scope="row" valign="top">
									<?php esc_html_e( 'Activate License', 'trending-mag-pro' ); ?>
								</th>
								<td>
									<?php if ( $license_status !== false && $license_status == 'valid' ) { ?>
										<span class="button button-secondary" style="box-shadow:none; color:#fff; background:#2ba5dd; border:none; text-transform: uppercase;"><?php esc_html_e( 'active', 'trending-mag-pro' ); ?></span>
										<?php wp_nonce_field( 'edd_product_license_nonce', 'edd_product_license_nonce' ); ?>
										<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php esc_html_e( 'Deactivate License', 'trending-mag-pro' ); ?>"/>
										<a href="<?php echo esc_url( network_admin_url( 'themes.php?page=wishful-companion-panel-install-demos' ) ); ?>">
											<span class="button button-secondary" style="box-shadow:none; color:#fff; background:#007cba; border:none; text-transform: uppercase;"><?php esc_html_e( 'Import Demo', 'trending-mag-pro' ); ?></span>
										</a>
										<?php
									} else {
										wp_nonce_field( 'edd_product_license_nonce', 'edd_product_license_nonce' );
										?>
										<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php esc_html_e( 'Activate License', 'trending-mag-pro' ); ?>"/>
										<?php
									}
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php submit_button( esc_html__( 'Update License', 'trending-mag-pro' ) ); ?>
			</form>
		</div>
		<?php

	}

	public function download_updater() {

		if ( ! class_exists( 'Trending_Mag_Pro_EDD_SL_Plugin_Updater' ) ) {

			require_once dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';

		}

		$edd_updater = new Trending_Mag_Pro_EDD_SL_Plugin_Updater(
			$this->store_url,
			WP_PLUGIN_DIR . $this->plugin_path,
			array(
				'version' => $this->current_version, // current version number.
				'license' => $this->license_key, // license key (used get_option above to retrieve from DB).
				'item_id' => $this->download_id, // id of this product in EDD.
				'author'  => $this->author_name, // author of this plugin.
				'url'     => home_url(),
			)
		);
	}

	public function trending_mag_pro_define_activated() {

		define( 'ACTIVATED_LICENSE_PRO', TRENDING_MAG_PRO_CURRENT_VERSION );
	}

}
