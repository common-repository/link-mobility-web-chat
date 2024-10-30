<?php
/**
 * Plugin Name: Link Mobility Web Chat
 * Plugin URI: https://Chatexperience.linkmobility.com
 * Description: Link Mobility Web Chat Widget configuration settings
 * Version: 1.0
 * Author: LINK Mobility Group AS
 * Author URI: http://www.linkmobility.com
 */

class Chatexperience {
	private $Chatexperience_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'Chatexperience_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'Chatexperience_page_init' ) );
	}

	public function Chatexperience_add_plugin_page() {
		add_options_page(
			'Chatexperience',
			'Chatexperience',
			'manage_options',
			'Chatexperience',
			array( $this, 'Chatexperience_create_admin_page' )
		);
	}

	public function Chatexperience_create_admin_page() {
		$this->Chatexperience_options = get_option( 'Chatexperience_option_name' ); ?>

		<div class="wrap">
			<h2>Chatexperience</h2>
			<p>Configuration settings for your Chatexperience Chatbot. Copy your chatbot data from your publising 
			   dialog as <a href='https://docs.chatexperience.linkmobility.com/basic-concepts/publishing/channels/web/wordpress' target='_blank'>detailed here</a> and save.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'Chatexperience_option_group' );
					do_settings_sections( 'Chatexperience-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function Chatexperience_page_init() {
		register_setting(
			'Chatexperience_option_group',
			'Chatexperience_option_name',
			array( $this, 'Chatexperience_sanitize' )
		);

		add_settings_section(
			'Chatexperience_setting_section',
			'Settings',
			array( $this, 'Chatexperience_section_info' ),
			'Chatexperience-admin'
		);

		add_settings_field(
			'Chatexperience_node_0',
			'Chatexperience Node',
			array( $this, 'Chatexperience_node_0_callback' ),
			'Chatexperience-admin',
			'Chatexperience_setting_section'
		);

		add_settings_field(
			'chatbot_id_1',
			'Chatbot Id',
			array( $this, 'chatbot_id_1_callback' ),
			'Chatexperience-admin',
			'Chatexperience_setting_section'
		);
	}

	public function Chatexperience_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['Chatexperience_node_0'] ) ) {
			$sanitary_values['Chatexperience_node_0'] = sanitize_text_field( $input['Chatexperience_node_0'] );
		}

		if ( isset( $input['chatbot_id_1'] ) ) {
			$sanitary_values['chatbot_id_1'] = sanitize_text_field( $input['chatbot_id_1'] );
		}

		return $sanitary_values;
	}

	public function Chatexperience_section_info() {

	}

	public function Chatexperience_node_0_callback() {
		printf(
			'<input placeholder="Copy the Chatexperience node id from your chatbot publishing page" class="regular-text" type="text" name="Chatexperience_option_name[Chatexperience_node_0]" id="Chatexperience_node_0" value="%s">',
			isset( $this->Chatexperience_options['Chatexperience_node_0'] ) ? esc_attr( $this->Chatexperience_options['Chatexperience_node_0']) : ''
		);
	}

	public function chatbot_id_1_callback() {
		printf(
			'<input placeholder="Copy the Chatexperience Chatbot Id from your chatbot publishing page" class="regular-text" type="text" name="Chatexperience_option_name[chatbot_id_1]" id="chatbot_id_1" value="%s">',
			isset( $this->Chatexperience_options['chatbot_id_1'] ) ? esc_attr( $this->Chatexperience_options['chatbot_id_1']) : ''
		);
	}

}
if ( is_admin() )
	$Chatexperience = new Chatexperience();


function Chatexperience_render() {

	$Chatexperience_options = get_option( 'Chatexperience_option_name' );
	$Chatexperience_node = $Chatexperience_options['Chatexperience_node_0'];
	$Chatexperience_id = $Chatexperience_options['chatbot_id_1'];

	//no data. no render.
	if( empty( $Chatexperience_node) || empty( $Chatexperience_id )){
	    return;
	}
	
	wp_enqueue_script( "Chatexperience", "https://chatexperience.linkmobility.com/webchatsrc/Chatexperience.js", null, '1.0', true );
	wp_add_inline_script( "Chatexperience", ' Chatexperience.Start("' . $Chatexperience_id . '");' );
}

function Chatexperience_add_data_attribute($tag, $handle) {
	if ( 'Chatexperience' !== $handle )
		return $tag;
	
	//Adds required attributes to the Chatexperience script
	$Chatexperience_options = get_option( 'Chatexperience_option_name' );
	$Chatexperience_node = $Chatexperience_options['Chatexperience_node_0'];

	return str_replace( ' src', ' data-id="chatbotsrc" data-node="' . $Chatexperience_node . '" src', $tag );
	
}

add_action( 'wp_footer', 'Chatexperience_render' );
add_filter('script_loader_tag', 'Chatexperience_add_data_attribute', 10, 2);