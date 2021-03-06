<?php

/**
 * This file has all the required helpers functions and definitions.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'trending_mag_pro_generate_field_name' ) ) {

	/**
	 * Generates the key for the fields name attribute.
	 *
	 * @param string $name Name for the current field.
	 * @param bool   $echo Whether to echo or return the result.
	 */
	function trending_mag_pro_generate_field_name( $name, $post_type = false, $echo = true ) {
		$post_type = ! $post_type ? get_post_type() : $post_type;
		$post_type = str_replace( array( '-', ' ' ), '_', $post_type );

		$name = str_replace( ']', '', $name );
		$name = explode( '[', $name );
		$name = implode( '][', $name );

		$field_name = "trending_mag_pro[{$post_type}][{$name}]";

		if ( ! $post_type ) {
			$field_name = "trending_mag_pro[{$name}]";
		}

		if ( ! $echo ) {
			return $field_name;
		}

		echo esc_attr( $field_name );
	}
}



if ( ! function_exists( 'trending_mag_pro_get_post_data' ) ) {

	/**
	 * Returns the saved post meta value.
	 *
	 * @param int $post_id Post ID.
	 */
	function trending_mag_pro_get_post_data( $post_id = '' ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		return get_post_meta( $post_id, 'trending_mag_pro', true );
	}
}


if ( ! function_exists( 'trending_mag_pro_get_submitted_data' ) ) {

	/**
	 * Returns sanitized the data of POST method.
	 */
	function trending_mag_pro_get_submitted_data() {

		if ( ! isset( $_POST['_trending_mag_pro_nonce'] ) || ! isset( $_POST['trending_mag_pro'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['_trending_mag_pro_nonce'] ), '_trending_mag_pro_nonce_action' ) ) {
			return;
		}

		$submitted_data = sanitize_meta( 'trending_mag_pro', $_POST, 'post' );
		return $submitted_data['trending_mag_pro'];
	}
}



if ( ! function_exists( 'trending_mag_pro_get_poll_content' ) ) {

	/**
	 * Returns the poll html.
	 */
	function trending_mag_pro_get_poll_content( $poll_id ) {
		if ( ! $poll_id ) {
			return;
		}

		$post_data    = trending_mag_pro_get_post_data( $poll_id );
		$polls_data   = ! empty( $post_data['trending_mag_polls'] ) ? $post_data['trending_mag_polls'] : array();
		$poll_options = ! empty( $polls_data['poll_options'] ) ? $polls_data['poll_options'] : array();

		$selection_type     = ! empty( $poll_options['selection_type'] ) ? $poll_options['selection_type'] : 'single';
		$poll_title         = get_the_title( $poll_id );
		$option_input_field = ! empty( $poll_options['option_input_field'] ) ? $poll_options['option_input_field'] : '';

		$type = 'single' === $selection_type ? 'radio' : 'checkbox';
		$name = 'single' === $selection_type ? 'poll_options[selected_poll]' : 'poll_options[selected_poll][]';

		ob_start();
		if ( is_array( $option_input_field ) && ! empty( $option_input_field ) ) {
			?>
			<form method="POST">

				<?php if ( $poll_title ) { ?>
					<p><strong><?php echo esc_html( $poll_title ); ?></strong></p>
				<?php } ?>

				<div class="rm-polls-ans">
					<ul class="poling-list">
						<?php
						foreach ( $option_input_field as $option_input_value ) {
							?>
							<li>
								<label class="rm-label">
									<input value="<?php echo esc_attr( $option_input_value ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php trending_mag_pro_generate_field_name( $name, 'trending-mag-polls' ); ?>"> <?php echo esc_html( $option_input_value ); ?>
								</label>
							</li>
							<?php
						}
						?>
					</ul>
					<p>
					<input type="submit" name="vote" value="<?php esc_attr_e( 'Vote', 'trending-mag-pro' ); ?>" class="rm-button-primary small vote"></p>
				</div>
				<input type="hidden" name="<?php trending_mag_pro_generate_field_name( 'poll_options[poll_id]', 'trending-mag-polls' ); ?>" value="<?php echo esc_attr( $poll_id ); ?>">
				<?php wp_nonce_field( '_trending_mag_pro_nonce_action', '_trending_mag_pro_nonce' ); ?>
			</form>
			<script>
			/**
			 * Preventing from resubmission of form on page reload.
			 */
			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			}
			</script>
			<?php
		}

		return ob_get_clean();
	}
}


if ( ! function_exists( 'trending_mag_pro_get_supported_sharer' ) ) {

	/**
	 * Returns the supported social sharer array.
	 */
	function trending_mag_pro_get_supported_sharer() {
		return array(
			'facebook',
			'twitter',
			'whatsapp',
			'linkedin',
			'pinterest',
		);

	}
}


if ( ! function_exists( 'trending_mag_pro_list_sharer_button' ) ) {

	/**
	 * Retrives the social sharer buttons list tags.
	 */
	function trending_mag_pro_list_sharer_button( $args ) {

		if ( ! $args ) {
			return;
		}

		$url   = get_the_permalink();
		$title = get_the_title();
		$image = get_the_post_thumbnail_url();

		$sharer_links = array(
			'facebook'  => "//www.facebook.com/sharer/sharer.php?u={$url}&t={$title}",
			'twitter'   => "//twitter.com/share?url={$url}&text={$title}",
			'whatsapp'  => "//api.whatsapp.com/send?text={$url}",
			'linkedin'  => "//www.linkedin.com/sharing/share-offsite/?url={$url}",
			'pinterest' => "//pinterest.com/pin/create/link/?url={$url}&media={$image}&description={$title}",
		);

		ob_start();
		if ( is_array( $sharer_links ) && ! empty( $sharer_links ) ) {
			foreach ( $sharer_links as $social => $links ) {
				if ( ! in_array( $social, $args, true ) ) {
					continue;
				}
				$fa_class   = "fa fa-{$social}";
				$title_attr = __( 'Share on', 'trending-mag-pro' ) . ' ' . ucfirst( $social );
				?>
				<li class="<?php echo esc_attr( $social ); ?>" title="<?php echo esc_attr( $title_attr ); ?>" >
					<a href="<?php echo esc_url( $links ); ?>" class="sharer-link" target="_blank" rel="noopener noreferrer">
						<i class="<?php echo esc_attr( $fa_class ); ?>"></i> <?php esc_html_e( 'Share', 'trending-mag-pro' ); ?>
					</a>
				</li>
				<?php
			}
		}
		$content = ob_get_clean();

		echo $content; // phpcs:ignore

	}
}


if ( ! function_exists( 'trending_mag_pro_get_premium_fonts' ) ) {
	
	/**
	 * Returns the array of all google font lists for premium purpose.
	 */
	function trending_mag_pro_get_premium_fonts() {
		$fonts = array(
			'ABeeZee:400,400i'                                                       => esc_html( 'ABeeZee' ),
			'Abel'                                                                   => esc_html( 'Abel' ),
			'Abhaya+Libre:400,500,600,700,800'                                       => esc_html( 'Abhaya Libre' ),
			'Abril+Fatface'                                                          => esc_html( 'Abril Fatface' ),
			'Aclonica'                                                               => esc_html( 'Aclonica' ),
			'Acme'                                                                   => esc_html( 'Acme' ),
			'Actor'                                                                  => esc_html( 'Actor' ),
			'Adamina'                                                                => esc_html( 'Adamina' ),
			'Advent+Pro:400,500,600,700'                                             => esc_html( 'Advent Pro' ),
			'Aguafina+Script'                                                        => esc_html( 'Aguafina Script' ),
			'Akronim'                                                                => esc_html( 'Akronim' ),
			'Aladin'                                                                 => esc_html( 'Aladin' ),
			'Aldrich'                                                                => esc_html( 'Aldrich' ),
			'Alef:400,700'                                                           => esc_html( 'Alef' ),
			'Alegreya:400,400i,500,500i,700,700i,800,800i'                           => esc_html( 'Alegreya' ),
			'Alegreya+Sans:400,400i,500,500i,700,700i,800,800i'                      => esc_html( 'Alegreya Sans' ),
			'Alegreya+Sans+SC:400,400i,500,500i,700,700i,800,800i'                   => esc_html( 'Alegreya Sans SC' ),
			'Alegreya+SC:400,400i,500,500i,700,700i,800,800i'                        => esc_html( 'Alegreya SC' ),
			'Alex+Brush'                                                             => esc_html( 'Alex Brush' ),
			'Alfa+Slab+One'                                                          => esc_html( 'Alfa Slab One' ),
			'Alice'                                                                  => esc_html( 'Alice' ),
			'Alike'                                                                  => esc_html( 'Alike' ),
			'Alike+Angular'                                                          => esc_html( 'Alike Angular' ),
			'Allan:400,700'                                                          => esc_html( 'Allan' ),
			'Allerta'                                                                => esc_html( 'Allerta' ),
			'Allerta+Stencil'                                                        => esc_html( 'Allerta Stencil' ),
			'Allura'                                                                 => esc_html( 'Allura' ),
			'Almendra:400,400i,700,700i'                                             => esc_html( 'Almendra' ),
			'Almendra+Display'                                                       => esc_html( 'Almendra Display' ),
			'Almendra+SC'                                                            => esc_html( 'Almendra SC' ),
			'Amarante'                                                               => esc_html( 'Amarante' ),
			'Amaranth:400,400i,700,700i'                                             => esc_html( 'Amaranth' ),
			'Amatic+SC:400,700'                                                      => esc_html( 'Amatic SC' ),
			'Amethysta'                                                              => esc_html( 'Amethysta' ),
			'Amiko:400,600,700'                                                      => esc_html( 'Amiko' ),
			'Amiri:400,400i,700,700i'                                                => esc_html( 'Amiri' ),
			'Amita:400,700'                                                          => esc_html( 'Amita' ),
			'Anaheim'                                                                => esc_html( 'Anaheim' ),
			'Andada'                                                                 => esc_html( 'Andada' ),
			'Andika'                                                                 => esc_html( 'Andika' ),
			'Angkor'                                                                 => esc_html( 'Angkor' ),
			'Annie+Use+Your+Telescope'                                               => esc_html( 'Annie Use Your Telescope' ),
			'Anonymous+Pro:400,400i,700,700i'                                        => esc_html( 'Anonymous Pro' ),
			'Antic'                                                                  => esc_html( 'Antic' ),
			'Antic+Didone'                                                           => esc_html( 'Antic Didone' ),
			'Antic+Slab'                                                             => esc_html( 'Antic Slab' ),
			'Anton'                                                                  => esc_html( 'Anton' ),
			'Arapey:400,400i'                                                        => esc_html( 'Arapey' ),
			'Arbutus'                                                                => esc_html( 'Arbutus' ),
			'Arbutus+Slab'                                                           => esc_html( 'Arbutus Slab' ),
			'Architects+Daughter'                                                    => esc_html( 'Architects Daughter' ),
			'Archivo:400,400i,500,500i,600,600i,700,700i'                            => esc_html( 'Archivo' ),
			'Archivo+Black'                                                          => esc_html( 'Archivo Black' ),
			'Archivo+Narrow:400,400i,500,500i,600,600i,700,700i'                     => esc_html( 'Archivo Narrow' ),
			'Aref+Ruqaa:400,700'                                                     => esc_html( 'Aref Ruqaa' ),
			'Arima+Madurai:400,500,700,800'                                          => esc_html( 'Arima Madurai' ),
			'Arimo:400,400i,700,700i'                                                => esc_html( 'Arimo' ),
			'Arizonia'                                                               => esc_html( 'Arizonia' ),
			'Armata'                                                                 => esc_html( 'Armata' ),
			'Arsenal:400,400i,700,700i'                                              => esc_html( 'Arsenal' ),
			'Artifika'                                                               => esc_html( 'Artifika' ),
			'Arvo:400,400i,700,700i'                                                 => esc_html( 'Arvo' ),
			'Arya:400,700'                                                           => esc_html( 'Arya' ),
			'Asap:400,400i,500,500i,600,600i,700,700i'                               => esc_html( 'Asap' ),
			'Asap+Condensed:400,400i,500,500i,600,600i,700,700i'                     => esc_html( 'Asap Condensed' ),
			'Asar'                                                                   => esc_html( 'Asar' ),
			'Asset'                                                                  => esc_html( 'Asset' ),
			'Assistant:400,600,700,800'                                              => esc_html( 'Assistant' ),
			'Astloch:400,700'                                                        => esc_html( 'Astloch' ),
			'Asul:400,700'                                                           => esc_html( 'Asul' ),
			'Athiti:400,500,600,700'                                                 => esc_html( 'Athiti' ),
			'Atma:400,500,600,700'                                                   => esc_html( 'Atma' ),
			'Atomic+Age'                                                             => esc_html( 'Atomic Age' ),
			'Aubrey'                                                                 => esc_html( 'Aubrey' ),
			'Audiowide'                                                              => esc_html( 'Audiowide' ),
			'Autour+One'                                                             => esc_html( 'Autour One' ),
			'Average'                                                                => esc_html( 'Average' ),
			'Average+Sans'                                                           => esc_html( 'Average Sans' ),
			'Averia+Gruesa+Libre'                                                    => esc_html( 'Averia Gruesa Libre' ),
			'Averia+Libre:400,400i,700,700i'                                         => esc_html( 'Averia Libre' ),
			'Averia+Sans+Libre:400,400i,700,700i'                                    => esc_html( 'Averia Sans Libre' ),
			'Averia+Serif+Libre:400,400i,700,700i'                                   => esc_html( 'Averia Serif Libre' ),
			'Bad+Script'                                                             => esc_html( 'Bad Script' ),
			'Bahiana'                                                                => esc_html( 'Bahiana' ),
			'Baloo'                                                                  => esc_html( 'Baloo' ),
			'Balthazar'                                                              => esc_html( 'Balthazar' ),
			'Bangers'                                                                => esc_html( 'Bangers' ),
			'Barlow:400,400i,500,500i,600,600i,700,700i,800,800i'                    => esc_html( 'Barlow' ),
			'Barlow+Condensed:400,400i,500,500i,600,600i,700,700i,800,800i'          => esc_html( 'Barlow Condensed' ),
			'Barlow+Semi+Condensed:400,400i,500,500i,600,600i,700,700i,800,800i'     => esc_html( 'Barlow Semi Condensed' ),
			'Barrio'                                                                 => esc_html( 'Barrio' ),
			'Basic'                                                                  => esc_html( 'Basic' ),
			'Battambang:400,700'                                                     => esc_html( 'Battambang' ),
			'Baumans'                                                                => esc_html( 'Baumans' ),
			'Bayon'                                                                  => esc_html( 'Bayon' ),
			'Belgrano'                                                               => esc_html( 'Belgrano' ),
			'Bellefair'                                                              => esc_html( 'Bellefair' ),
			'Belleza'                                                                => esc_html( 'Belleza' ),
			'BenchNine:400,700'                                                      => esc_html( 'BenchNine' ),
			'Bentham'                                                                => esc_html( 'Bentham' ),
			'Berkshire+Swash'                                                        => esc_html( 'Berkshire Swash' ),
			'Bevan'                                                                  => esc_html( 'Bevan' ),
			'Bigelow+Rules'                                                          => esc_html( 'Bigelow Rules' ),
			'Bigshot+One'                                                            => esc_html( 'Bigshot One' ),
			'Bilbo'                                                                  => esc_html( 'Bilbo' ),
			'Bilbo+Swash+Caps'                                                       => esc_html( 'Bilbo Swash Caps' ),
			'BioRhyme:400,700,800'                                                   => esc_html( 'BioRhyme' ),
			'BioRhyme+Expanded:400,700,800'                                          => esc_html( 'BioRhyme Expanded' ),
			'Biryani:400,600,700,800'                                                => esc_html( 'Biryani' ),
			'Bitter:400,400i,700'                                                    => esc_html( 'Bitter' ),
			'Black+Ops+One'                                                          => esc_html( 'Black Ops One' ),
			'Bokor'                                                                  => esc_html( 'Bokor' ),
			'Bonbon'                                                                 => esc_html( 'Bonbon' ),
			'Boogaloo'                                                               => esc_html( 'Boogaloo' ),
			'Bowlby+One'                                                             => esc_html( 'Bowlby One' ),
			'Bowlby+One+SC'                                                          => esc_html( 'Bowlby One SC' ),
			'Brawler'                                                                => esc_html( 'Brawler' ),
			'Bree+Serif'                                                             => esc_html( 'Bree Serif' ),
			'Bubblegum+Sans'                                                         => esc_html( 'Bubblegum Sans' ),
			'Bubbler+One'                                                            => esc_html( 'Bubbler One' ),
			'Buda:300'                                                               => esc_html( 'Buda' ),
			'Buenard:400,700'                                                        => esc_html( 'Buenard' ),
			'Bungee'                                                                 => esc_html( 'Bungee' ),
			'Bungee+Hairline'                                                        => esc_html( 'Bungee Hairline' ),
			'Bungee+Inline'                                                          => esc_html( 'Bungee Inline' ),
			'Bungee+Outline'                                                         => esc_html( 'Bungee Outline' ),
			'Bungee+Shade'                                                           => esc_html( 'Bungee Shade' ),
			'Butcherman'                                                             => esc_html( 'Butcherman' ),
			'Butterfly+Kids'                                                         => esc_html( 'Butterfly Kids' ),
			'Cabin:400,400i,500,500i,600,600i,700,700i'                              => esc_html( 'Cabin' ),
			'Cabin+Condensed:400,500,600,700'                                        => esc_html( 'Cabin Condensed' ),
			'Cabin+Sketch:400,700'                                                   => esc_html( 'Cabin Sketch' ),
			'Caesar+Dressing'                                                        => esc_html( 'Caesar Dressing' ),
			'Cagliostro'                                                             => esc_html( 'Cagliostro' ),
			'Cairo:400,600,700'                                                      => esc_html( 'Cairo' ),
			'Calligraffitti'                                                         => esc_html( 'Calligraffitti' ),
			'Cambay:400,400i,700,700i'                                               => esc_html( 'Cambay' ),
			'Cambo'                                                                  => esc_html( 'Cambo' ),
			'Cantarell:400,400i,700,700i'                                            => esc_html( 'Cantarell' ),
			'Cantata+One'                                                            => esc_html( 'Cantata One' ),
			'Capriola'                                                               => esc_html( 'Capriola' ),
			'Cardo:400,400i,700'                                                     => esc_html( 'Cardo' ),
			'Carme'                                                                  => esc_html( 'Carme' ),
			'Carrois+Gothic'                                                         => esc_html( 'Carrois Gothic' ),
			'Carrois+Gothic+SC'                                                      => esc_html( 'Carrois Gothic SC' ),
			'Carter+One'                                                             => esc_html( 'Carter One' ),
			'Catamaran:400,500,600,700,800'                                          => esc_html( 'Catamaran' ),
			'Caudex:400,400i,700,700i'                                               => esc_html( 'Caudex' ),
			'Caveat:400,700'                                                         => esc_html( 'Caveat' ),
			'Caveat+Brush'                                                           => esc_html( 'Caveat Brush' ),
			'Cedarville+Cursive'                                                     => esc_html( 'Cedarville Cursive' ),
			'Ceviche+One'                                                            => esc_html( 'Ceviche One' ),
			'Changa:400,500,600,700,800'                                             => esc_html( 'Changa' ),
			'Changa+One:400,400i'                                                    => esc_html( 'Changa One' ),
			'Chango'                                                                 => esc_html( 'Chango' ),
			'Chathura:400,700,800'                                                   => esc_html( 'Chathura' ),
			'Chau+Philomene+One:400,400i'                                            => esc_html( 'Chau Philomene One' ),
			'Chela+One'                                                              => esc_html( 'Chela One' ),
			'Chelsea+Market'                                                         => esc_html( 'Chelsea Market' ),
			'Chenla'                                                                 => esc_html( 'Chenla' ),
			'Cherry+Cream+Soda'                                                      => esc_html( 'Cherry Cream Soda' ),
			'Cherry+Swash:400,700'                                                   => esc_html( 'Cherry Swash' ),
			'Chewy'                                                                  => esc_html( 'Chewy' ),
			'Chicle'                                                                 => esc_html( 'Chicle' ),
			'Chivo:400,400i,700,700i'                                                => esc_html( 'Chivo' ),
			'Chonburi'                                                               => esc_html( 'Chonburi' ),
			'Cinzel:400,700'                                                         => esc_html( 'Cinzel' ),
			'Cinzel+Decorative:400,700'                                              => esc_html( 'Cinzel Decorative' ),
			'Clicker+Script'                                                         => esc_html( 'Clicker Script' ),
			'Coda:400,800'                                                           => esc_html( 'Coda' ),
			'Coda+Caption:800'                                                       => esc_html( 'Coda Caption' ),
			'Codystar'                                                               => esc_html( 'Codystar' ),
			'Coiny'                                                                  => esc_html( 'Coiny' ),
			'Combo'                                                                  => esc_html( 'Combo' ),
			'Comfortaa:400,700'                                                      => esc_html( 'Comfortaa' ),
			'Coming+Soon'                                                            => esc_html( 'Coming Soon' ),
			'Concert+One'                                                            => esc_html( 'Concert One' ),
			'Condiment'                                                              => esc_html( 'Condiment' ),
			'Content:400,700'                                                        => esc_html( 'Content' ),
			'Contrail+One'                                                           => esc_html( 'Contrail One' ),
			'Convergence'                                                            => esc_html( 'Convergence' ),
			'Cookie'                                                                 => esc_html( 'Cookie' ),
			'Copse'                                                                  => esc_html( 'Copse' ),
			'Corben:400,700'                                                         => esc_html( 'Corben' ),
			'Cormorant:400,400i,500,500i,600,600i,700,700i'                          => esc_html( 'Cormorant' ),
			'Cormorant+Garamond:400,400i,500,500i,600,600i,700,700i&display=swap'    => esc_html( 'Cormorant Garamond' ),
			'Cormorant+Infant:400,400i,500,500i,600,600i,700,700i'                   => esc_html( 'Cormorant Infant' ),
			'Cormorant+SC:400,500,600,700'                                           => esc_html( 'Cormorant SC' ),
			'Cormorant+Unicase:400,500,600,700'                                      => esc_html( 'Cormorant Unicase' ),
			'Cormorant+Upright:400,500,600,700'                                      => esc_html( 'Cormorant Upright' ),
			'Courgette'                                                              => esc_html( 'Courgette' ),
			'Cousine:400,400i,700,700i'                                              => esc_html( 'Cousine' ),
			'Coustard'                                                               => esc_html( 'Coustard' ),
			'Covered+By+Your+Grace'                                                  => esc_html( 'Covered By Your Grace' ),
			'Crafty+Girls'                                                           => esc_html( 'Crafty Girls' ),
			'Creepster'                                                              => esc_html( 'Creepster' ),
			'Crete+Round:400,400i'                                                   => esc_html( 'Crete Round' ),
			'Crimson+Text:400,400i,600,600i,700,700i'                                => esc_html( 'Crimson Text' ),
			'Croissant+One'                                                          => esc_html( 'Croissant One' ),
			'Crushed'                                                                => esc_html( 'Crushed' ),
			'Cuprum:400,400i,700,700i'                                               => esc_html( 'Cuprum' ),
			'Cutive'                                                                 => esc_html( 'Cutive' ),
			'Cutive+Mono'                                                            => esc_html( 'Cutive Mono' ),
			'Damion'                                                                 => esc_html( 'Damion' ),
			'Dancing+Script:400,700'                                                 => esc_html( 'Dancing Script' ),
			'Dangrek'                                                                => esc_html( 'Dangrek' ),
			'David+Libre:400,500,700'                                                => esc_html( 'David Libre' ),
			'Dawning+of+a+New+Day'                                                   => esc_html( 'Dawning of a New Day' ),
			'Days+One'                                                               => esc_html( 'Days One' ),
			'Dekko'                                                                  => esc_html( 'Dekko' ),
			'Delius'                                                                 => esc_html( 'Delius' ),
			'Delius+Swash+Caps'                                                      => esc_html( 'Delius Swash Caps' ),
			'Delius+Unicase:400,700'                                                 => esc_html( 'Delius Unicase' ),
			'Della+Respira'                                                          => esc_html( 'Della Respira' ),
			'Denk+One'                                                               => esc_html( 'Denk One' ),
			'Devonshire'                                                             => esc_html( 'Devonshire' ),
			'Dhurjati'                                                               => esc_html( 'Dhurjati' ),
			'Didact+Gothic'                                                          => esc_html( 'Didact Gothic' ),
			'Diplomata'                                                              => esc_html( 'Diplomata' ),
			'Diplomata+SC'                                                           => esc_html( 'Diplomata SC' ),
			'Domine:400,700'                                                         => esc_html( 'Domine' ),
			'Donegal+One'                                                            => esc_html( 'Donegal One' ),
			'Doppio+One'                                                             => esc_html( 'Doppio One' ),
			'Dorsa'                                                                  => esc_html( 'Dorsa' ),
			'Dosis:400,500,600,700,800'                                              => esc_html( 'Dosis' ),
			'Dr+Sugiyama'                                                            => esc_html( 'Dr Sugiyama' ),
			'Duru+Sans'                                                              => esc_html( 'Duru Sans' ),
			'Dynalight'                                                              => esc_html( 'Dynalight' ),
			'Eagle+Lake'                                                             => esc_html( 'Eagle Lake' ),
			'Eater'                                                                  => esc_html( 'Eater' ),
			'EB+Garamond:400,400i,500,500i,600,600i,700,700i,800,800i'               => esc_html( 'EB Garamond' ),
			'Economica:400,400i,700,700i'                                            => esc_html( 'Economica' ),
			'Eczar:400,500,600,700,800'                                              => esc_html( 'Eczar' ),
			'El+Messiri:400,500,600,700'                                             => esc_html( 'El Messiri' ),
			'Electrolize'                                                            => esc_html( 'Electrolize' ),
			'Elsie'                                                                  => esc_html( 'Elsie' ),
			'Elsie+Swash+Caps'                                                       => esc_html( 'Elsie Swash Caps' ),
			'Emblema+One'                                                            => esc_html( 'Emblema One' ),
			'Emilys+Candy'                                                           => esc_html( 'Emilys Candy' ),
			'Encode+Sans:400,500,600,700,800'                                        => esc_html( 'Encode Sans' ),
			'Encode+Sans+Condensed:400,500,600,700,800'                              => esc_html( 'Encode Sans Condensed' ),
			'Encode+Sans+Expanded:400,500,600,700,800'                               => esc_html( 'Encode Sans Expanded' ),
			'Encode+Sans+Semi+Condensed:400,500,600,700,800'                         => esc_html( 'Encode Sans Semi Condensed' ),
			'Encode+Sans+Semi+Expanded:400,500,600,700,800'                          => esc_html( 'Encode Sans Semi Expanded' ),
			'Engagement'                                                             => esc_html( 'Engagement' ),
			'Englebert'                                                              => esc_html( 'Englebert' ),
			'Enriqueta:400,700'                                                      => esc_html( 'Enriqueta' ),
			'Erica+One'                                                              => esc_html( 'Erica One' ),
			'Esteban'                                                                => esc_html( 'Esteban' ),
			'Euphoria+Script'                                                        => esc_html( 'Euphoria Script' ),
			'Ewert'                                                                  => esc_html( 'Ewert' ),
			'Exo:400,400i,500,500i,600,600i,700,700i,800,800i'                       => esc_html( 'Exo' ),
			'Exo+2:400,400i,500,500i,600,600i,700,700i,800,800i'                     => esc_html( 'Exo 2' ),
			'Expletus+Sans:400,400i,500,500i,600,600i,700,700i'                      => esc_html( 'Expletus Sans' ),
			'Fanwood+Text:400,400i'                                                  => esc_html( 'Fanwood Text' ),
			'Farsan'                                                                 => esc_html( 'Farsan' ),
			'Fascinate'                                                              => esc_html( 'Fascinate' ),
			'Fascinate+Inline'                                                       => esc_html( 'Fascinate Inline' ),
			'Faster+One'                                                             => esc_html( 'Faster One' ),
			'Fasthand'                                                               => esc_html( 'Fasthand' ),
			'Fauna+One'                                                              => esc_html( 'Fauna One' ),
			'Faustina:400,400i,500,500i,600,600i,700,700i'                           => esc_html( 'Faustina' ),
			'Federant'                                                               => esc_html( 'Federant' ),
			'Federo'                                                                 => esc_html( 'Federo' ),
			'Felipa'                                                                 => esc_html( 'Felipa' ),
			'Fenix'                                                                  => esc_html( 'Fenix' ),
			'Finger+Paint'                                                           => esc_html( 'Finger Paint' ),
			'Fira+Mono:400,500,700'                                                  => esc_html( 'Fira Mono' ),
			'Fira+Sans:400,400i,500,500i,600,600i,700,700i,800,800i'                 => esc_html( 'Fira Sans' ),
			'Fira+Sans+Condensed:400,400i,500,500i,600,600i,700,700i,800,800i'       => esc_html( 'Fira Sans Condensed' ),
			'Fira+Sans+Extra+Condensed:400,400i,500,500i,600,600i,700,700i,800,800i' => esc_html( 'Fira Sans Extra Condensed' ),
			'Fjalla+One'                                                             => esc_html( 'Fjalla One' ),
			'Fjord+One'                                                              => esc_html( 'Fjord One' ),
			'Flamenco'                                                               => esc_html( 'Flamenco' ),
			'Flavors'                                                                => esc_html( 'Flavors' ),
			'Fondamento:400,400i'                                                    => esc_html( 'Fondamento' ),
			'Fontdiner+Swanky'                                                       => esc_html( 'Fontdiner Swanky' ),
			'Forum'                                                                  => esc_html( 'Forum' ),
			'Francois+One'                                                           => esc_html( 'Francois One' ),
			'Frank+Ruhl+Libre:400,500,700'                                           => esc_html( 'Frank Ruhl Libre' ),
			'Freckle+Face'                                                           => esc_html( 'Freckle Face' ),
			'Fredericka+the+Great'                                                   => esc_html( 'Fredericka the Great' ),
			'Fredoka+One'                                                            => esc_html( 'Fredoka One' ),
			'Freehand'                                                               => esc_html( 'Freehand' ),
			'Fresca'                                                                 => esc_html( 'Fresca' ),
			'Frijole'                                                                => esc_html( 'Frijole' ),
			'Fruktur'                                                                => esc_html( 'Fruktur' ),
			'Fugaz+One'                                                              => esc_html( 'Fugaz One' ),
			'Gabriela'                                                               => esc_html( 'Gabriela' ),
			'Gafata'                                                                 => esc_html( 'Gafata' ),
			'Galada'                                                                 => esc_html( 'Galada' ),
			'Galdeano'                                                               => esc_html( 'Galdeano' ),
			'Galindo'                                                                => esc_html( 'Galindo' ),
			'Gentium+Basic:400,400i,700,700i'                                        => esc_html( 'Gentium Basic' ),
			'Gentium+Book+Basic:400,400i,700,700i'                                   => esc_html( 'Gentium Book Basic' ),
			'Geo:400,400i'                                                           => esc_html( 'Geo' ),
			'Geostar'                                                                => esc_html( 'Geostar' ),
			'Geostar+Fill'                                                           => esc_html( 'Geostar Fill' ),
			'Germania+One'                                                           => esc_html( 'Germania One' ),
			'GFS+Didot'                                                              => esc_html( 'GFS Didot' ),
			'GFS+Neohellenic:400,400i,700,700i'                                      => esc_html( 'GFS Neohellenic' ),
			'Gidugu'                                                                 => esc_html( 'Gidugu' ),
			'Gilda+Display'                                                          => esc_html( 'Gilda Display' ),
			'Give+You+Glory'                                                         => esc_html( 'Give You Glory' ),
			'Glass+Antiqua'                                                          => esc_html( 'Glass Antiqua' ),
			'Glegoo:400,700'                                                         => esc_html( 'Glegoo' ),
			'Gloria+Hallelujah'                                                      => esc_html( 'Gloria Hallelujah' ),
			'Goblin+One'                                                             => esc_html( 'Goblin One' ),
			'Gochi+Hand'                                                             => esc_html( 'Gochi Hand' ),
			'Gorditas:400,700'                                                       => esc_html( 'Gorditas' ),
			'Goudy+Bookletter+1911'                                                  => esc_html( 'Goudy Bookletter 1911' ),
			'Graduate'                                                               => esc_html( 'Graduate' ),
			'Grand+Hotel'                                                            => esc_html( 'Grand Hotel' ),
			'Gravitas+One'                                                           => esc_html( 'Gravitas One' ),
			'Great+Vibes'                                                            => esc_html( 'Great Vibes' ),
			'Griffy'                                                                 => esc_html( 'Griffy' ),
			'Gruppo'                                                                 => esc_html( 'Gruppo' ),
			'Gudea:400,400i,700'                                                     => esc_html( 'Gudea' ),
			'Gurajada'                                                               => esc_html( 'Gurajada' ),
			'Habibi'                                                                 => esc_html( 'Habibi' ),
			'Halant:400,500,600,700'                                                 => esc_html( 'Halant' ),
			'Hammersmith+One'                                                        => esc_html( 'Hammersmith One' ),
			'Hanalei'                                                                => esc_html( 'Hanalei' ),
			'Hanalei+Fill'                                                           => esc_html( 'Hanalei Fill' ),
			'Handlee'                                                                => esc_html( 'Handlee' ),
			'Hanuman:400,700'                                                        => esc_html( 'Hanuman' ),
			'Happy+Monkey'                                                           => esc_html( 'Happy Monkey' ),
			'Harmattan'                                                              => esc_html( 'Harmattan' ),
			'Headland+One'                                                           => esc_html( 'Headland One' ),
			'Heebo:400,500,700,800'                                                  => esc_html( 'Heebo' ),
			'Henny+Penny'                                                            => esc_html( 'Henny Penny' ),
			'Herr+Von+Muellerhoff'                                                   => esc_html( 'Herr Von Muellerhoff' ),
			'Hind:400,500,600,700'                                                   => esc_html( 'Hind' ),
			'Hind+Guntur:400,500,600,700'                                            => esc_html( 'Hind Guntur' ),
			'Hind+Madurai:400,500,600,700'                                           => esc_html( 'Hind Madurai' ),
			'Hind+Siliguri:400,500,600,700'                                          => esc_html( 'Hind Siliguri' ),
			'Hind+Vadodara:400,500,600,700'                                          => esc_html( 'Hind Vadodara' ),
			'Holtwood+One+SC'                                                        => esc_html( 'Holtwood One SC' ),
			'Homemade+Apple'                                                         => esc_html( 'Homemade Apple' ),
			'Homenaje'                                                               => esc_html( 'Homenaje' ),
			'Iceberg'                                                                => esc_html( 'Iceberg' ),
			'Iceland'                                                                => esc_html( 'Iceland' ),
			'IM+Fell+Double+Pica:400,400i'                                           => esc_html( 'IM Fell Double Pica' ),
			'IM+Fell+Double+Pica+SC'                                                 => esc_html( 'IM Fell Double Pica SC' ),
			'IM+Fell+DW+Pica:400,400i'                                               => esc_html( 'IM Fell DW Pica' ),
			'IM+Fell+DW+Pica+SC'                                                     => esc_html( 'IM Fell DW Pica SC' ),
			'IM+Fell+English:400,400i'                                               => esc_html( 'IM Fell English' ),
			'IM+Fell+English+SC'                                                     => esc_html( 'IM Fell English SC' ),
			'IM+Fell+French+Canon:400,400i'                                          => esc_html( 'IM Fell French Canon' ),
			'IM+Fell+French+Canon+SC'                                                => esc_html( 'IM Fell French Canon SC' ),
			'IM+Fell+Great+Primer:400,400i'                                          => esc_html( 'IM Fell Great Primer' ),
			'IM+Fell+Great+Primer+SC'                                                => esc_html( 'IM Fell Great Primer SC' ),
			'Imprima'                                                                => esc_html( 'Imprima' ),
			'Inconsolata:400,700'                                                    => esc_html( 'Inconsolata' ),
			'Inder'                                                                  => esc_html( 'Inder' ),
			'Indie+Flower'                                                           => esc_html( 'Indie Flower' ),
			'Inika:400,700'                                                          => esc_html( 'Inika' ),
			'Inknut+Antiqua:400,500,600,700,800'                                     => esc_html( 'Inknut Antiqua' ),
			'Irish+Grover'                                                           => esc_html( 'Irish Grover' ),
			'Istok+Web:400,400i,700,700i'                                            => esc_html( 'Istok Web' ),
			'Italiana'                                                               => esc_html( 'Italiana' ),
			'Italianno'                                                              => esc_html( 'Italianno' ),
			'Itim'                                                                   => esc_html( 'Itim' ),
			'Jacques+Francois'                                                       => esc_html( 'Jacques Francois' ),
			'Jacques+Francois+Shadow'                                                => esc_html( 'Jacques Francois Shadow' ),
			'Jaldi:400,700'                                                          => esc_html( 'Jaldi' ),
			'Jim+Nightshade'                                                         => esc_html( 'Jim Nightshade' ),
			'Jockey+One'                                                             => esc_html( 'Jockey One' ),
			'Jolly+Lodger'                                                           => esc_html( 'Jolly Lodger' ),
			'Jomhuria'                                                               => esc_html( 'Jomhuria' ),
			'Josefin+Sans:400,400i,600,600i,700,700i'                                => esc_html( 'Josefin Sans' ),
			'Josefin+Slab:400,400i,600,600i,700,700i'                                => esc_html( 'Josefin Slab' ),
			'Joti+One'                                                               => esc_html( 'Joti One' ),
			'Judson:400,400i,700'                                                    => esc_html( 'Judson' ),
			'Julee'                                                                  => esc_html( 'Julee' ),
			'Julius+Sans+One'                                                        => esc_html( 'Julius Sans One' ),
			'Junge'                                                                  => esc_html( 'Junge' ),
			'Jura:400,500,600,700'                                                   => esc_html( 'Jura' ),
			'Just+Another+Hand'                                                      => esc_html( 'Just Another Hand' ),
			'Just+Me+Again+Down+Here'                                                => esc_html( 'Just Me Again Down Here' ),
			'Kadwa:400,700'                                                          => esc_html( 'Kadwa' ),
			'Kalam:400,700'                                                          => esc_html( 'Kalam' ),
			'Kameron:400,700'                                                        => esc_html( 'Kameron' ),
			'Kanit:400,400i,500,500i,600,600i,700,700i,800,800i'                     => esc_html( 'Kanit' ),
			'Kantumruy:400,700'                                                      => esc_html( 'Kantumruy' ),
			'Karla:400,400i,700,700i'                                                => esc_html( 'Karla' ),
			'Karma:400,500,600,700'                                                  => esc_html( 'Karma' ),
			'Katibeh'                                                                => esc_html( 'Katibeh' ),
			'Kaushan+Script'                                                         => esc_html( 'Kaushan Script' ),
			'Kavivanar'                                                              => esc_html( 'Kavivanar' ),
			'Kavoon'                                                                 => esc_html( 'Kavoon' ),
			'Kdam+Thmor'                                                             => esc_html( 'Kdam Thmor' ),
			'Keania+One'                                                             => esc_html( 'Keania One' ),
			'Kelly+Slab'                                                             => esc_html( 'Kelly Slab' ),
			'Kenia'                                                                  => esc_html( 'Kenia' ),
			'Khand:400,500,600,700'                                                  => esc_html( 'Khand' ),
			'Khmer'                                                                  => esc_html( 'Khmer' ),
			'Khula:400,600,700,800'                                                  => esc_html( 'Khula' ),
			'Kite+One'                                                               => esc_html( 'Kite One' ),
			'Knewave'                                                                => esc_html( 'Knewave' ),
			'Kotta+One'                                                              => esc_html( 'Kotta One' ),
			'Koulen'                                                                 => esc_html( 'Koulen' ),
			'Kranky'                                                                 => esc_html( 'Kranky' ),
			'Kreon:400,700'                                                          => esc_html( 'Kreon' ),
			'Kristi'                                                                 => esc_html( 'Kristi' ),
			'Krona+One'                                                              => esc_html( 'Krona One' ),
			'Kumar+One'                                                              => esc_html( 'Kumar One' ),
			'Kumar+One+Outline'                                                      => esc_html( 'Kumar One Outline' ),
			'Kurale'                                                                 => esc_html( 'Kurale' ),
			'La+Belle+Aurore'                                                        => esc_html( 'La Belle Aurore' ),
			'Laila:400,500,600,700'                                                  => esc_html( 'Laila' ),
			'Lakki+Reddy'                                                            => esc_html( 'Lakki Reddy' ),
			'Lalezar'                                                                => esc_html( 'Lalezar' ),
			'Lancelot'                                                               => esc_html( 'Lancelot' ),
			'Lateef'                                                                 => esc_html( 'Lateef' ),
			'Lato:400,400i,700,700i'                                                 => esc_html( 'Lato' ),
			'League+Script'                                                          => esc_html( 'League Script' ),
			'Leckerli+One'                                                           => esc_html( 'Leckerli One' ),
			'Ledger'                                                                 => esc_html( 'Ledger' ),
			'Lekton:400,400i,700'                                                    => esc_html( 'Lekton' ),
			'Lemon'                                                                  => esc_html( 'Lemon' ),
			'Lemonada:400,600,700'                                                   => esc_html( 'Lemonada' ),
			'Libre+Barcode+128'                                                      => esc_html( 'Libre Barcode 128' ),
			'Libre+Barcode+128+Text'                                                 => esc_html( 'Libre Barcode 128 Text' ),
			'Libre+Barcode+39'                                                       => esc_html( 'Libre Barcode 39' ),
			'Libre+Barcode+39+Extended'                                              => esc_html( 'Libre Barcode 39 Extended' ),
			'Libre+Barcode+39+Extended+Text'                                         => esc_html( 'Libre Barcode 39 Extended Text' ),
			'Libre+Barcode+39+Text'                                                  => esc_html( 'Libre Barcode 39 Text' ),
			'Libre+Baskerville:400,400i,700'                                         => esc_html( 'Libre Baskerville' ),
			'Libre+Franklin:400,400i,500,500i,600,600i,700,700i,800,800i'            => esc_html( 'Libre Franklin' ),
			'Life+Savers:400,700'                                                    => esc_html( 'Life Savers' ),
			'Lilita+One'                                                             => esc_html( 'Lilita One' ),
			'Lily+Script+One'                                                        => esc_html( 'Lily Script One' ),
			'Limelight'                                                              => esc_html( 'Limelight' ),
			'Linden+Hill:400,400i'                                                   => esc_html( 'Linden Hill' ),
			'Lobster'                                                                => esc_html( 'Lobster' ),
			'Lobster+Two:400,400i,700,700i'                                          => esc_html( 'Lobster Two' ),
			'Londrina+Outline'                                                       => esc_html( 'Londrina Outline' ),
			'Londrina+Shadow'                                                        => esc_html( 'Londrina Shadow' ),
			'Londrina+Sketch'                                                        => esc_html( 'Londrina Sketch' ),
			'Londrina+Solid'                                                         => esc_html( 'Londrina Solid' ),
			'Lora:400,400i,700,700i'                                                 => esc_html( 'Lora' ),
			'Love+Ya+Like+A+Sister'                                                  => esc_html( 'Love Ya Like A Sister' ),
			'Loved+by+the+King'                                                      => esc_html( 'Loved by the King' ),
			'Lovers+Quarrel'                                                         => esc_html( 'Lovers Quarrel' ),
			'Luckiest+Guy'                                                           => esc_html( 'Luckiest Guy' ),
			'Lusitana:400,700'                                                       => esc_html( 'Lusitana' ),
			'Lustria'                                                                => esc_html( 'Lustria' ),
			'Macondo'                                                                => esc_html( 'Macondo' ),
			'Macondo+Swash+Caps'                                                     => esc_html( 'Macondo Swash Caps' ),
			'Mada:400,500,600,700'                                                   => esc_html( 'Mada' ),
			'Magra:400,700'                                                          => esc_html( 'Magra' ),
			'Maiden+Orange'                                                          => esc_html( 'Maiden Orange' ),
			'Maitree:400,500,600,700'                                                => esc_html( 'Maitree' ),
			'Mako'                                                                   => esc_html( 'Mako' ),
			'Mallanna'                                                               => esc_html( 'Mallanna' ),
			'Mandali'                                                                => esc_html( 'Mandali' ),
			'Manuale:400,400i,500,500i,600,600i,700,700i'                            => esc_html( 'Manuale' ),
			'Marcellus'                                                              => esc_html( 'Marcellus' ),
			'Marcellus+SC'                                                           => esc_html( 'Marcellus SC' ),
			'Marck+Script'                                                           => esc_html( 'Marck Script' ),
			'Margarine'                                                              => esc_html( 'Margarine' ),
			'Marko+One'                                                              => esc_html( 'Marko One' ),
			'Marmelad'                                                               => esc_html( 'Marmelad' ),
			'Martel:400,600,700,800'                                                 => esc_html( 'Martel' ),
			'Martel+Sans:400,600,700,800'                                            => esc_html( 'Martel Sans',  '' ),
			'Marvel:400,400i,700,700i'                                               => esc_html( 'Marvel',  '' ),
			'Mate:400,400i'                                                          => esc_html( 'Mate' ),
			'Mate+SC'                                                                => esc_html( 'Mate SC' ),
			'Maven+Pro:400,500,700'                                                  => esc_html( 'Maven Pro' ),
			'McLaren'                                                                => esc_html( 'McLaren' ),
			'Meddon'                                                                 => esc_html( 'Meddon' ),
			'MedievalSharp'                                                          => esc_html( 'MedievalSharp' ),
			'Medula+One'                                                             => esc_html( 'Medula One' ),
			'Meera+Inimai'                                                           => esc_html( 'Meera Inimai' ),
			'Megrim'                                                                 => esc_html( 'Megrim' ),
			'Meie+Script'                                                            => esc_html( 'Meie Script' ),
			'Merienda:400,700'                                                       => esc_html( 'Merienda' ),
			'Merienda+One'                                                           => esc_html( 'Merienda One' ),
			'Merriweather:400,400i,700,700i'                                         => esc_html( 'Merriweather' ),
			'Merriweather+Sans:400,400i,700,700i,800,800i'                           => esc_html( 'Merriweather Sans' ),
			'Metal'                                                                  => esc_html( 'Metal' ),
			'Metal+Mania'                                                            => esc_html( 'Metal Mania' ),
			'Metamorphous'                                                           => esc_html( 'Metamorphous' ),
			'Metrophobic'                                                            => esc_html( 'Metrophobic' ),
			'Michroma'                                                               => esc_html( 'Michroma' ),
			'Milonga'                                                                => esc_html( 'Milonga' ),
			'Miltonian'                                                              => esc_html( 'Miltonian' ),
			'Miltonian+Tattoo'                                                       => esc_html( 'Miltonian Tattoo' ),
			'Miniver'                                                                => esc_html( 'Miniver' ),
			'Miriam+Libre:400,700'                                                   => esc_html( 'Miriam Libre' ),
			'Mirza:400,500,600,700'                                                  => esc_html( 'Mirza' ),
			'Miss+Fajardose'                                                         => esc_html( 'Miss Fajardose' ),
			'Mitr:400,500,600,700'                                                   => esc_html( 'Mitr' ),
			'Modak'                                                                  => esc_html( 'Modak' ),
			'Modern+Antiqua'                                                         => esc_html( 'Modern Antiqua' ),
			'Mogra'                                                                  => esc_html( 'Mogra' ),
			'Molengo'                                                                => esc_html( 'Molengo' ),
			'Molle:400i'                                                             => esc_html( 'Molle' ),
			'Monda:400,700'                                                          => esc_html( 'Monda' ),
			'Monofett'                                                               => esc_html( 'Monofett' ),
			'Monoton'                                                                => esc_html( 'Monoton' ),
			'Monsieur+La+Doulaise'                                                   => esc_html( 'Monsieur La Doulaise' ),
			'Montaga'                                                                => esc_html( 'Montaga' ),
			'Montez'                                                                 => esc_html( 'Montez' ),
			'Montserrat:400,400i,500,500i,600,600i,700,700i,800,800i'                => esc_html( 'Montserrat' ),
			'Montserrat+Alternates:400,400i,500,500i,600,600i,700,700i,800,800i'     => esc_html( 'Montserrat Alternates' ),
			'Montserrat+Subrayada:400,400i,500,500i,600,600i,700,700i,800,800i'      => esc_html( 'Montserrat Subrayada' ),
			'Moul'                                                                   => esc_html( 'Moul' ),
			'Moulpali'                                                               => esc_html( 'Moulpali' ),
			'Mountains+of+Christmas:400,700'                                         => esc_html( 'Mountains of Christmas' ),
			'Mouse+Memoirs'                                                          => esc_html( 'Mouse Memoirs' ),
			'Mr+Bedfort'                                                             => esc_html( 'Mr Bedfort' ),
			'Mr+Dafoe'                                                               => esc_html( 'Mr Dafoe' ),
			'Mr+De+Haviland'                                                         => esc_html( 'Mr De Haviland' ),
			'Mrs+Saint+Delafield'                                                    => esc_html( 'Mrs Saint Delafield' ),
			'Mrs+Sheppards'                                                          => esc_html( 'Mrs Sheppards' ),
			'Mukta:400,500,600,700,800'                                              => esc_html( 'Mukta' ),
			'Mukta+Mahee:400,500,600,700,800'                                        => esc_html( 'Mukta Mahee' ),
			'Mukta+Malar:400,500,600,700,800'                                        => esc_html( 'Mukta Malar' ),
			'Mukta+Vaani:400,500,600,700,800'                                        => esc_html( 'Mukta Vaani' ),
			'Muli:400,400i,600,600i,700,700i,800,800i'                               => esc_html( 'Muli' ),
			'Mystery+Quest'                                                          => esc_html( 'Mystery Quest' ),
			'Nanum+Brush+Script'                                                     => esc_html( 'Nanum Brush Script' ),
			'Nanum+Gothic:400,700,800'                                               => esc_html( 'Nanum Gothic' ),
			'Nanum+Gothic+Coding:400,700'                                            => esc_html( 'Nanum Gothic Coding' ),
			'Nanum+Myeongjo:400,700,800'                                             => esc_html( 'Nanum Myeongjo' ),
			'Nanum+Pen+Script'                                                       => esc_html( 'Nanum Pen Script' ),
			'Neucha'                                                                 => esc_html( 'Neucha' ),
			'Neuton:400,400i,700,800'                                                => esc_html( 'Neuton' ),
			'New+Rocker'                                                             => esc_html( 'New Rocker' ),
			'News+Cycle:400,700'                                                     => esc_html( 'News Cycle' ),
			'Niconne'                                                                => esc_html( 'Niconne' ),
			'Nixie+One'                                                              => esc_html( 'Nixie One' ),
			'Nobile:400,400i,500,500i,700,700i'                                      => esc_html( 'Nobile' ),
			'Nokora:400,700'                                                         => esc_html( 'Nokora' ),
			'Norican'                                                                => esc_html( 'Norican' ),
			'Nosifer'                                                                => esc_html( 'Nosifer' ),
			'Nothing+You+Could+Do'                                                   => esc_html( 'Nothing You Could Do' ),
			'Noticia+Text:400,400i,700,700i'                                         => esc_html( 'Noticia Text' ),
			'Noto+Sans:400,400i,700,700i'                                            => esc_html( 'Noto Sans' ),
			'Noto+Serif:400,400i,700,700i'                                           => esc_html( 'Noto Serif' ),
			'Nova+Cut'                                                               => esc_html( 'Nova Cut' ),
			'Nova+Flat'                                                              => esc_html( 'Nova Flat' ),
			'Nova+Mono'                                                              => esc_html( 'Nova Mono' ),
			'Nova+Oval'                                                              => esc_html( 'Nova Oval' ),
			'Nova+Round'                                                             => esc_html( 'Nova Round' ),
			'Nova+Script'                                                            => esc_html( 'Nova Script' ),
			'Nova+Slim'                                                              => esc_html( 'Nova Slim' ),
			'Nova+Square'                                                            => esc_html( 'Nova Square' ),
			'NTR'                                                                    => esc_html( 'NTR' ),
			'Numans'                                                                 => esc_html( 'Numans' ),
			'Nunito:400,400i,600,600i,700,700i,800,800i'                             => esc_html( 'Nunito' ),
			'Nunito+Sans:400,400i,600,600i,700,700i'                                 => esc_html( 'Nunito Sans' ),
			'Odor+Mean+Chey'                                                         => esc_html( 'Odor Mean Chey' ),
			'Offside'                                                                => esc_html( 'Offside' ),
			'Old+Standard+TT:400,400i,700'                                           => esc_html( 'Old Standard TT' ),
			'Oldenburg'                                                              => esc_html( 'Oldenburg' ),
			'Oleo+Script:400,700'                                                    => esc_html( 'Oleo Script' ),
			'Oleo+Script+Swash+Caps:400,700'                                         => esc_html( 'Oleo Script Swash Caps' ),
			'Open+Sans:400,400i,600,600i,700,700i,800,800i'                          => esc_html( 'Open Sans' ),
			'Open+Sans+Condensed:300,300i,700'                                       => esc_html( 'Open Sans Condensed' ),
			'Oranienbaum'                                                            => esc_html( 'Oranienbaum' ),
			'Orbitron:400,500,700'                                                   => esc_html( 'Orbitron' ),
			'Oregano:400,400i'                                                       => esc_html( 'Oregano' ),
			'Orienta'                                                                => esc_html( 'Orienta' ),
			'Original+Surfer'                                                        => esc_html( 'Original Surfer' ),
			'Oswald:400,500,600,700'                                                 => esc_html( 'Oswald' ),
			'Over+the+Rainbow'                                                       => esc_html( 'Over the Rainbow' ),
			'Overlock:400,400i,700,700i'                                             => esc_html( 'Overlock' ),
			'Overlock+SC'                                                            => esc_html( 'Overlock SC' ),
			'Overpass:400,400i,600,600i,700,700i,800,800i'                           => esc_html( 'Overpass' ),
			'Overpass+Mono:400,600,700'                                              => esc_html( 'Overpass Mono' ),
			'Ovo'                                                                    => esc_html( 'Ovo' ),
			'Oxygen:400,700'                                                         => esc_html( 'Oxygen' ),
			'Oxygen+Mono'                                                            => esc_html( 'Oxygen Mono' ),
			'Pacifico'                                                               => esc_html( 'Pacifico' ),
			'Padauk:400,700'                                                         => esc_html( 'Padauk' ),
			'Palanquin:400,500,600,700'                                              => esc_html( 'Palanquin' ),
			'Palanquin+Dark:400,500,600,700'                                         => esc_html( 'Palanquin Dark' ),
			'Pangolin'                                                               => esc_html( 'Pangolin' ),
			'Paprika'                                                                => esc_html( 'Paprika' ),
			'Parisienne'                                                             => esc_html( 'Parisienne' ),
			'Passero+One'                                                            => esc_html( 'Passero One' ),
			'Passion+One:400,700'                                                    => esc_html( 'Passion One' ),
			'Pathway+Gothic+One'                                                     => esc_html( 'Pathway Gothic One' ),
			'Patrick+Hand'                                                           => esc_html( 'Patrick Hand' ),
			'Patrick+Hand+SC'                                                        => esc_html( 'Patrick Hand SC' ),
			'Pattaya'                                                                => esc_html( 'Pattaya' ),
			'Patua+One'                                                              => esc_html( 'Patua One' ),
			'Pavanam'                                                                => esc_html( 'Pavanam' ),
			'Paytone+One'                                                            => esc_html( 'Paytone One' ),
			'Peddana'                                                                => esc_html( 'Peddana' ),
			'Peralta'                                                                => esc_html( 'Peralta' ),
			'Permanent+Marker'                                                       => esc_html( 'Permanent Marker' ),
			'Petit+Formal+Script'                                                    => esc_html( 'Petit Formal Script' ),
			'Petrona'                                                                => esc_html( 'Petrona' ),
			'Philosopher:400,400i,700,700i'                                          => esc_html( 'Philosopher' ),
			'Piedra'                                                                 => esc_html( 'Piedra' ),
			'Pinyon+Script'                                                          => esc_html( 'Pinyon Script' ),
			'Pirata+One'                                                             => esc_html( 'Pirata One' ),
			'Plaster'                                                                => esc_html( 'Plaster' ),
			'Play:400,700'                                                           => esc_html( 'Play' ),
			'Playball'                                                               => esc_html( 'Playball' ),
			'Playfair+Display:400,400i,700,700i'                                     => esc_html( 'Playfair Display' ),
			'Playfair+Display+SC:400,400i,700,700i'                                  => esc_html( 'Playfair Display SC' ),
			'Podkova:400,500,600,700,800'                                            => esc_html( 'Podkova' ),
			'Poiret+One'                                                             => esc_html( 'Poiret One' ),
			'Poller+One'                                                             => esc_html( 'Poller One' ),
			'Poly:400,400i'                                                          => esc_html( 'Poly' ),
			'Pompiere'                                                               => esc_html( 'Pompiere' ),
			'Pontano+Sans'                                                           => esc_html( 'Pontano Sans' ),
			'Poppins:400,400i,500,500i,600,600i,700,700i,800,800i'                   => esc_html( 'Poppins' ),
			'Port+Lligat+Sans'                                                       => esc_html( 'Port Lligat Sans' ),
			'Port+Lligat+Slab'                                                       => esc_html( 'Port Lligat Slab' ),
			'Pragati+Narrow:400,700'                                                 => esc_html( 'Pragati Narrow' ),
			'Prata'                                                                  => esc_html( 'Prata' ),
			'Preahvihear'                                                            => esc_html( 'Preahvihear' ),
			'Press+Start+2P'                                                         => esc_html( 'Press Start 2P' ),
			'Pridi:400,500,600,700'                                                  => esc_html( 'Pridi' ),
			'Princess+Sofia'                                                         => esc_html( 'Princess Sofia' ),
			'Prociono'                                                               => esc_html( 'Prociono' ),
			'Prompt:400,400i,500,500i,600,600i,700,700i,800,800i'                    => esc_html( 'Prompt' ),
			'Prosto+One'                                                             => esc_html( 'Prosto One' ),
			'Proza+Libre:400,400i,500,500i,600,600i,700,700i,800,800i'               => esc_html( 'Proza Libre' ),
			'PT+Mono'                                                                => esc_html( 'PT Mono' ),
			'PT+Sans:400,400i,700,700i'                                              => esc_html( 'PT Sans' ),
			'PT+Sans+Caption:400,700'                                                => esc_html( 'PT Sans Caption' ),
			'PT+Sans+Narrow:400,700'                                                 => esc_html( 'PT Sans Narrow' ),
			'PT+Serif:400,400i,700,700i'                                             => esc_html( 'PT Serif' ),
			'PT+Serif+Caption:400,400i'                                              => esc_html( 'PT Serif Caption' ),
			'Puritan:400,400i,700,700i'                                              => esc_html( 'Puritan' ),
			'Purple+Purse'                                                           => esc_html( 'Purple Purse' ),
			'Quando'                                                                 => esc_html( 'Quando' ),
			'Quantico:400,400i,700,700i'                                             => esc_html( 'Quantico' ),
			'Quattrocento:400,700'                                                   => esc_html( 'Quattrocento' ),
			'Quattrocento+Sans:400,400i,700,700i'                                    => esc_html( 'Quattrocento Sans' ),
			'Questrial'                                                              => esc_html( 'Questrial' ),
			'Quicksand:400,500,700'                                                  => esc_html( 'Quicksand' ),
			'Quintessential'                                                         => esc_html( 'Quintessential' ),
			'Qwigley'                                                                => esc_html( 'Qwigley' ),
			'Racing+Sans+One'                                                        => esc_html( 'Racing Sans One' ),
			'Radley:400,400i'                                                        => esc_html( 'Radley' ),
			'Rajdhani:400,500,600,700'                                               => esc_html( 'Rajdhani' ),
			'Rakkas'                                                                 => esc_html( 'Rakkas' ),
			'Raleway:400,400i,500,500i,600,600i,700,700i,800,800i'                   => esc_html( 'Raleway' ),
			'Raleway+Dots'                                                           => esc_html( 'Raleway Dots' ),
			'Ramabhadra'                                                             => esc_html( 'Ramabhadra' ),
			'Ramaraja'                                                               => esc_html( 'Ramaraja' ),
			'Rambla:400,400i,700,700i'                                               => esc_html( 'Rambla' ),
			'Rammetto+One'                                                           => esc_html( 'Rammetto One' ),
			'Ranchers'                                                               => esc_html( 'Ranchers' ),
			'Rancho'                                                                 => esc_html( 'Rancho' ),
			'Ranga:400,700'                                                          => esc_html( 'Ranga' ),
			'Rasa:400,500,600,700'                                                   => esc_html( 'Rasa' ),
			'Rationale'                                                              => esc_html( 'Rationale' ),
			'Ravi+Prakash'                                                           => esc_html( 'Ravi Prakash' ),
			'Redressed'                                                              => esc_html( 'Redressed' ),
			'Reem+Kufi'                                                              => esc_html( 'Reem Kufi' ),
			'Reenie+Beanie'                                                          => esc_html( 'Reenie Beanie' ),
			'Revalia'                                                                => esc_html( 'Revalia' ),
			'Rhodium+Libre'                                                          => esc_html( 'Rhodium Libre' ),
			'Ribeye'                                                                 => esc_html( 'Ribeye' ),
			'Ribeye+Marrow'                                                          => esc_html( 'Ribeye Marrow' ),
			'Righteous'                                                              => esc_html( 'Righteous' ),
			'Risque'                                                                 => esc_html( 'Risque' ),
			'Roboto:300,400,500,700,900&display=swap'                                => esc_html( 'Roboto' ),
			'Roboto+Condensed:400,400i,700,700i'                                     => esc_html( 'Roboto Condensed' ),
			'Roboto+Mono:400,400i,500,500i,700,700i'                                 => esc_html( 'Roboto Mono' ),
			'Roboto+Slab:400,700'                                                    => esc_html( 'Roboto Slab' ),
			'Rochester'                                                              => esc_html( 'Rochester' ),
			'Rock+Salt'                                                              => esc_html( 'Rock Salt' ),
			'Rokkitt:400,500,600,700,800'                                            => esc_html( 'Rokkitt' ),
			'Romanesco'                                                              => esc_html( 'Romanesco' ),
			'Ropa+Sans:400,400i'                                                     => esc_html( 'Ropa Sans' ),
			'Rosario:400,400i,700,700i'                                              => esc_html( 'Rosario' ),
			'Rosarivo:400,400i'                                                      => esc_html( 'Rosarivo' ),
			'Rouge+Script'                                                           => esc_html( 'Rouge Script' ),
			'Rozha+One'                                                              => esc_html( 'Rozha One' ),
			'Rubik:400,400i,500,500i,700,700i'                                       => esc_html( 'Rubik' ),
			'Rubik+Mono+One'                                                         => esc_html( 'Rubik Mono One' ),
			'Ruda:400,700'                                                           => esc_html( 'Ruda' ),
			'Rufina:400,700'                                                         => esc_html( 'Rufina' ),
			'Ruge+Boogie'                                                            => esc_html( 'Ruge Boogie' ),
			'Ruluko'                                                                 => esc_html( 'Ruluko' ),
			'Rum+Raisin'                                                             => esc_html( 'Rum Raisin' ),
			'Ruslan+Display'                                                         => esc_html( 'Ruslan Display' ),
			'Russo+One'                                                              => esc_html( 'Russo One' ),
			'Ruthie'                                                                 => esc_html( 'Ruthie' ),
			'Rye'                                                                    => esc_html( 'Rye' ),
			'Sacramento'                                                             => esc_html( 'Sacramento' ),
			'Sahitya:400,700'                                                        => esc_html( 'Sahitya' ),
			'Sail'                                                                   => esc_html( 'Sail' ),
			'Saira:400,500,600,700,800'                                              => esc_html( 'Saira' ),
			'Saira+Extra+Condensed:400,500,600,700,800'                              => esc_html( 'Saira Extra Condensed' ),
			'Saira+Semi+Condensed:400,500,600,700,800'                               => esc_html( 'Saira Semi Condensed' ),
			'Salsa'                                                                  => esc_html( 'Salsa' ),
			'Sanchez:400,400i'                                                       => esc_html( 'Sanchez' ),
			'Sancreek'                                                               => esc_html( 'Sancreek' ),
			'Sansita:400,400i,700,700i,800,800i'                                     => esc_html( 'Sansita' ),
			'Sarala:400,700'                                                         => esc_html( 'Sarala' ),
			'Sarina'                                                                 => esc_html( 'Sarina' ),
			'Sarpanch:400,500,600,700,800'                                           => esc_html( 'Sarpanch' ),
			'Satisfy'                                                                => esc_html( 'Satisfy' ),
			'Scada:400,400i,700,700i'                                                => esc_html( 'Scada' ),
			'Scheherazade:400,700'                                                   => esc_html( 'Scheherazade' ),
			'Schoolbell'                                                             => esc_html( 'Schoolbell' ),
			'Scope+One'                                                              => esc_html( 'Scope One' ),
			'Seaweed+Script'                                                         => esc_html( 'Seaweed Script' ),
			'Secular+One'                                                            => esc_html( 'Secular One' ),
			'Sedgwick+Ave'                                                           => esc_html( 'Sedgwick Ave' ),
			'Sedgwick+Ave+Display'                                                   => esc_html( 'Sedgwick Ave Display' ),
			'Sevillana'                                                              => esc_html( 'Sevillana' ),
			'Seymour+One'                                                            => esc_html( 'Seymour One' ),
			'Shadows+Into+Light'                                                     => esc_html( 'Shadows Into Light' ),
			'Shadows+Into+Light+Two'                                                 => esc_html( 'Shadows Into Light Two' ),
			'Shanti'                                                                 => esc_html( 'Shanti' ),
			'Share:400,400i,700,700i'                                                => esc_html( 'Share' ),
			'Share+Tech'                                                             => esc_html( 'Share Tech' ),
			'Share+Tech+Mono'                                                        => esc_html( 'Share Tech Mono' ),
			'Shojumaru'                                                              => esc_html( 'Shojumaru' ),
			'Short+Stack'                                                            => esc_html( 'Short Stack' ),
			'Shrikhand'                                                              => esc_html( 'Shrikhand' ),
			'Siemreap'                                                               => esc_html( 'Siemreap' ),
			'Sigmar+One'                                                             => esc_html( 'Sigmar One' ),
			'Signika:400,600,700'                                                    => esc_html( 'Signika' ),
			'Signika+Negative:400,600,700'                                           => esc_html( 'Signika Negative' ),
			'Simonetta:400,400i'                                                     => esc_html( 'Simonetta' ),
			'Sintony:400,700'                                                        => esc_html( 'Sintony' ),
			'Sirin+Stencil'                                                          => esc_html( 'Sirin Stencil' ),
			'Six+Caps'                                                               => esc_html( 'Six Caps' ),
			'Skranji:400,700'                                                        => esc_html( 'Skranji' ),
			'Slabo+13px'                                                             => esc_html( 'Slabo 13px' ),
			'Slabo+27px'                                                             => esc_html( 'Slabo 27px' ),
			'Slackey'                                                                => esc_html( 'Slackey' ),
			'Smokum'                                                                 => esc_html( 'Smokum' ),
			'Smythe'                                                                 => esc_html( 'Smythe' ),
			'Sniglet:400,800'                                                        => esc_html( 'Sniglet' ),
			'Snippet'                                                                => esc_html( 'Snippet' ),
			'Snowburst+One'                                                          => esc_html( 'Snowburst One' ),
			'Sofadi+One'                                                             => esc_html( 'Sofadi One' ),
			'Sofia'                                                                  => esc_html( 'Sofia' ),
			'Sonsie+One'                                                             => esc_html( 'Sonsie One' ),
			'Sorts+Mill+Goudy:400,400i'                                              => esc_html( 'Sorts Mill Goudy' ),
			'Source+Code+Pro:400,500,600,700'                                        => esc_html( 'Source Code Pro' ),
			'Source+Sans+Pro:400,400i,600,600i,700,700i'                             => esc_html( 'Source Sans Pro' ),
			'Source+Serif+Pro:400,600,700'                                           => esc_html( 'Source Serif Pro' ),
			'Space+Mono:400,400i,700,700i'                                           => esc_html( 'Space Mono' ),
			'Special+Elite'                                                          => esc_html( 'Special Elite' ),
			'Spectral:400,400i,500,500i,600,600i,700,700i,800,800i'                  => esc_html( 'Spectral' ),
			'Spectral+SC:400,400i,500,500i,600,600i,700,700i,800,800i'               => esc_html( 'Spectral SC' ),
			'Spicy+Rice'                                                             => esc_html( 'Spicy Rice' ),
			'Spinnaker'                                                              => esc_html( 'Spinnaker' ),
			'Spirax'                                                                 => esc_html( 'Spirax' ),
			'Squada+One'                                                             => esc_html( 'Squada One' ),
			'Sree+Krushnadevaraya'                                                   => esc_html( 'Sree Krushnadevaraya' ),
			'Sriracha'                                                               => esc_html( 'Sriracha' ),
			'Stalemate'                                                              => esc_html( 'Stalemate' ),
			'Stalinist+One'                                                          => esc_html( 'Stalinist One' ),
			'Stardos+Stencil:400,700'                                                => esc_html( 'Stardos Stencil' ),
			'Stint+Ultra+Condensed'                                                  => esc_html( 'Stint Ultra Condensed' ),
			'Stint+Ultra+Expanded'                                                   => esc_html( 'Stint Ultra Expanded' ),
			'Stoke'                                                                  => esc_html( 'Stoke' ),
			'Strait'                                                                 => esc_html( 'Strait' ),
			'Sue+Ellen+Francisco'                                                    => esc_html( 'Sue Ellen Francisco' ),
			'Suez+One'                                                               => esc_html( 'Suez One' ),
			'Sumana:400,700'                                                         => esc_html( 'Sumana' ),
			'Sunshiney'                                                              => esc_html( 'Sunshiney' ),
			'Supermercado+One'                                                       => esc_html( 'Supermercado One' ),
			'Sura:400,700'                                                           => esc_html( 'Sura' ),
			'Suranna'                                                                => esc_html( 'Suranna' ),
			'Suravaram'                                                              => esc_html( 'Suravaram' ),
			'Suwannaphum'                                                            => esc_html( 'Suwannaphum' ),
			'Swanky+and+Moo+Moo'                                                     => esc_html( 'Swanky and Moo Moo' ),
			'Syncopate:400,700'                                                      => esc_html( 'Syncopate' ),
			'Tangerine:400,700'                                                      => esc_html( 'Tangerine' ),
			'Taprom'                                                                 => esc_html( 'Taprom' ),
			'Tauri'                                                                  => esc_html( 'Tauri' ),
			'Taviraj:400,400i,500,500i,600,600i,700,700i,800,800i'                   => esc_html( 'Taviraj' ),
			'Teko:400,500,600,700'                                                   => esc_html( 'Teko' ),
			'Telex'                                                                  => esc_html( 'Telex' ),
			'Tenali+Ramakrishna'                                                     => esc_html( 'Tenali Ramakrishna' ),
			'Tenor+Sans'                                                             => esc_html( 'Tenor Sans' ),
			'Text+Me+One'                                                            => esc_html( 'Text Me One' ),
			'The+Girl+Next+Door'                                                     => esc_html( 'The Girl Next Door' ),
			'Tienne:400,700'                                                         => esc_html( 'Tienne' ),
			'Tillana:400,500,600,700,800'                                            => esc_html( 'Tillana' ),
			'Timmana'                                                                => esc_html( 'Timmana' ),
			'Tinos:400,400i,700,700i'                                                => esc_html( 'Tinos' ),
			'Titan+One'                                                              => esc_html( 'Titan One' ),
			'Titillium+Web:400,400i,600,600i,700,700i'                               => esc_html( 'Titillium Web' ),
			'Trade+Winds'                                                            => esc_html( 'Trade Winds' ),
			'Trirong:400,400i,500,500i,600,600i,700,700i,800,800i'                   => esc_html( 'Trirong' ),
			'Trocchi'                                                                => esc_html( 'Trocchi' ),
			'Trochut:400,400i,700'                                                   => esc_html( 'Trochut' ),
			'Trykker'                                                                => esc_html( 'Trykker' ),
			'Tulpen+One'                                                             => esc_html( 'Tulpen One' ),
			'Ubuntu:400,400i,500,500i,700,700i'                                      => esc_html( 'Ubuntu' ),
			'Ubuntu+Condensed'                                                       => esc_html( 'Ubuntu Condensed' ),
			'Ubuntu+Mono:400,400i,700,700i'                                          => esc_html( 'Ubuntu Mono' ),
			'Ultra'                                                                  => esc_html( 'Ultra' ),
			'Uncial+Antiqua'                                                         => esc_html( 'Uncial Antiqua' ),
			'Underdog'                                                               => esc_html( 'Underdog' ),
			'Unica+One'                                                              => esc_html( 'Unica One' ),
			'UnifrakturCook:700'                                                     => esc_html( 'UnifrakturCook' ),
			'UnifrakturMaguntia'                                                     => esc_html( 'UnifrakturMaguntia' ),
			'Unkempt:400,700'                                                        => esc_html( 'Unkempt' ),
			'Unlock'                                                                 => esc_html( 'Unlock' ),
			'Unna:400,400i,700,700i'                                                 => esc_html( 'Unna' ),
			'Vampiro+One'                                                            => esc_html( 'Vampiro One' ),
			'Varela'                                                                 => esc_html( 'Varela' ),
			'Varela+Round'                                                           => esc_html( 'Varela Round' ),
			'Vast+Shadow'                                                            => esc_html( 'Vast Shadow' ),
			'Vesper+Libre:400,500,700'                                               => esc_html( 'Vesper Libre' ),
			'Vibur'                                                                  => esc_html( 'Vibur' ),
			'Vidaloka'                                                               => esc_html( 'Vidaloka' ),
			'Viga'                                                                   => esc_html( 'Viga' ),
			'Voces'                                                                  => esc_html( 'Voces' ),
			'Volkhov:400,400i,700,700i'                                              => esc_html( 'Volkhov' ),
			'Vollkorn:400,400i,600,600i,700,700i'                                    => esc_html( 'Vollkorn' ),
			'Vollkorn+SC:400,600,700'                                                => esc_html( 'Vollkorn SC' ),
			'Voltaire'                                                               => esc_html( 'Voltaire' ),
			'VT323'                                                                  => esc_html( 'VT323' ),
			'Waiting+for+the+Sunrise'                                                => esc_html( 'Waiting for the Sunrise' ),
			'Wallpoet'                                                               => esc_html( 'Wallpoet' ),
			'Walter+Turncoat'                                                        => esc_html( 'Walter Turncoat' ),
			'Warnes'                                                                 => esc_html( 'Warnes' ),
			'Wellfleet'                                                              => esc_html( 'Wellfleet' ),
			'Wendy+One'                                                              => esc_html( 'Wendy One' ),
			'Wire+One'                                                               => esc_html( 'Wire One' ),
			'Work+Sans:400,500,600,700,800'                                          => esc_html( 'Work Sans' ),
			'Yanone+Kaffeesatz:400,700'                                              => esc_html( 'Yanone Kaffeesatz' ),
			'Yantramanav:400,500,700'                                                => esc_html( 'Yantramanav' ),
			'Yatra+One'                                                              => esc_html( 'Yatra One' ),
			'Yellowtail'                                                             => esc_html( 'Yellowtail' ),
			'Yeseva+One'                                                             => esc_html( 'Yeseva One' ),
			'Yesteryear'                                                             => esc_html( 'Yesteryear' ),
			'Yrsa:400,500,600,700'                                                   => esc_html( 'Yrsa' ),
			'Zeyada'                                                                 => esc_html( 'Zeyada' ),
			'Zilla+Slab:400,400i,500,500i,600,600i,700,700i'                         => esc_html( 'Zilla Slab' ),
			'Zilla+Slab+Highlight:400,700'                                           => esc_html( 'Zilla Slab Highlight' )
		);
		return $fonts;
	}
}

