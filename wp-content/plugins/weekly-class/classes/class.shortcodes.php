<?php

new CurlyWeeklyClassShortcodes();

/**
* Weekly Classe
*/
class CurlyWeeklyClassShortcodes {

	public $_classes;

	public function __construct(){

		add_shortcode( 'wcs_timetable', array( $this, 'timetable' ) );
		add_shortcode( 'wcs-schedule', array( $this, 'wcs_schedule') );

		add_action( 'wp_footer', array( $this, 'modal_templates' ) );

		add_filter( 'wcs_filter_front_tdata', array( $this, 'filter_tdata' ), 10, 2 );
		add_filter( 'wcs_view_css', array( $this, 'default_css' ), 10, 3 );

		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'late_assets' ), 99 );

		require_once( WCS_PATH . '/assets/defaults/front/view.plain.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.compact.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.large-list.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.weekly-schedule.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.weekly-tabs.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.agenda.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.carousel.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.grid.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.timeline.php');
	  require_once( WCS_PATH . '/assets/defaults/front/view.monthly-schedule.php');
		require_once( WCS_PATH . '/assets/defaults/front/view.monthly-calendar.php');
		require_once( WCS_PATH . '/assets/defaults/front/view.countdown.php');
		require_once( WCS_PATH . '/assets/defaults/front/view.cover.php');

		//$views = apply_filters( 'wcs_views', array() );

	}

	public static function set_dependency( $dep ){

		$wcs_page_dependencies = wp_cache_get( 'dependencies', 'EventsSchedule' );

		if( ! $wcs_page_dependencies ){

			wp_cache_set( 'dependencies', array( $dep ), 'EventsSchedule' );

		} else if( ! in_array( $dep, $wcs_page_dependencies ) ){

			$wcs_page_dependencies[] = $dep;

			wp_cache_set( 'dependencies', $wcs_page_dependencies, 'EventsSchedule' );

		}

	}

	function late_assets(){

	}

	/** Front Assets */
	function assets(){

		$key = esc_attr( get_option( 'wcs_api_key', 'AIzaSyArPwtdP09w4OeKGuRDjZlGkUshNh180z8' ) );
		$key = ! empty( $key ) ? $key : 'AIzaSyArPwtdP09w4OeKGuRDjZlGkUshNh180z8';
		$maps_url = add_query_arg( array( 'key' => $key ), '//maps.google.com/maps/api/js');

		wp_register_script(
			'wcs-google-map',
			$maps_url,
			array( 'jquery' ),
			null
		);
		wp_register_script(
			'wcs-gmaps',
			plugins_url() . '/weekly-class/assets/libs/gmaps/gmap3.min.js',
			array( 'jquery' ),
			'7.1'
		);

		wp_register_script(
			'wcs-images-loaded',
			plugins_url() . '/weekly-class/assets/libs/metafizzy/imagesloaded.pkgd.min.js',
			array( 'jquery' ),
			null,
			true
		);

		wp_register_script(
			'wcs-match-height',
			plugins_url() . '/weekly-class/assets/libs/matchheight/jquery.matchHeight-min.js',
			array( 'jquery'  ),
			null,
			true
		);

		wp_register_script(
			'wcs-vue',
			WP_DEBUG ? plugins_url() . '/weekly-class/assets/libs/vue/vue.js' : plugins_url() . '/weekly-class/assets/libs/vue/vue.min.js',
			array( 'jquery' ),
			null,
			true
		);


		wp_register_script(
			'wcs-images-loaded-vue',
			plugins_url() . '/weekly-class/assets/libs/vue/vueimagesloaded.js',
			array( 'jquery', 'wcs-vue', 'wcs-images-loaded' ),
			null,
			true
		);

		wp_register_script(
			'wcs-owl',
			plugins_url() . '/weekly-class/assets/libs/owlcarousel/js/owl.carousel.min.js',
			array( 'jquery', 'wcs-images-loaded-vue' ),
			null,
			true
		);

		wp_register_script(
			'wcs-isotope-native',
			plugins_url() . '/weekly-class/assets/libs/metafizzy/isotope.pkgd.min.js',
			array( 'jquery' ),
			null,
			true
		);

		wp_register_script(
			'wcs-lodash',
			plugins_url() . '/weekly-class/assets/libs/lodash/lodash.js',
			array( 'jquery' ),
			null,
			true
		);

		wp_register_script(
			'wcs-isotope',
			plugins_url() . '/weekly-class/assets/libs/vue/vue_isotope.js',
			array( 'jquery', 'wcs-vue', 'wcs-isotope-native', 'wcs-lodash', 'wcs-images-loaded-vue' ),
			null,
			true
		);

		wp_register_script(
			'wcs-moment',
			plugins_url() . '/weekly-class/assets/libs/moment/moment.js',
			array( 'jquery' ),
			null,
			true
		);



		wp_register_script(
			'wcs-vue-resource',
			plugins_url() . '/weekly-class/assets/libs/vue/vue-resource.min.js',
			array( 'jquery', 'wcs-vue' ),
			null,
			true
		);

		wp_register_script(
			'wcs-main',
			plugins_url() . '/weekly-class/assets/front/js/min/scripts-min.js',
			array( 'jquery', 'wcs-vue', 'wcs-images-loaded', 'wcs-gmaps', 'wcs-vue-resource', 'wcs-moment' ),
			WP_DEBUG || is_user_logged_in() ? rand() : null,
			true
		);

		wp_register_script(
			'wcs-fullcalendar',
			plugins_url() . '/weekly-class/assets/libs/fullcalendar/fullcalendar.min.js',
			array( 'jquery', 'wcs-moment' ),
			'2.0',
			true
		);

		global $wp_locale;

		wp_localize_script( 'wcs-main', 'wcs_locale', array(
			'firstDay' => intval( get_option( 'start_of_week') ),
			'monthNames' => array_values( $wp_locale->month ),
			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
			'dayNames' => array_values( $wp_locale->weekday ),
			'dayNamesShort' => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin' => array_values( $wp_locale->weekday_initial ),
			'gmtOffset' => get_option( 'gmt_offset' ) * HOUR_IN_SECONDS
		));
		wp_localize_script( 'wcs-main', 'wcs_moment_locale', array(
			'firstDay' => intval( get_option( 'start_of_week') ),
			'months' => array_values( $wp_locale->month ),
			'monthsShort' => array_values( $wp_locale->month_abbrev ),
			'weekdays' => array_values( $wp_locale->weekday ),
			'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
			'weekdaysMin' => array_values( $wp_locale->weekday_initial )
		) );

		wp_localize_script( 'wcs-main', 'wcs_settings', array(
			'hasSingle' => filter_var( esc_attr( get_option( 'wcs_single', true ) ), FILTER_VALIDATE_BOOLEAN )
		));
		wp_localize_script( 'wcs-main', 'wcs_google_key', $key );
		wp_localize_script( 'wcs-main', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'wcs-main', 'wcs_maps_url', $maps_url );

		wp_register_style(
			'wcs-display-monthly',
			plugins_url() . '/weekly-class/assets/libs/fullcalendar/fullcalendar.min.css',
			null,
			false,
			'all'
		);


		wp_register_style(
			'wcs-timetable',
			plugins_url() . '/weekly-class/assets/front/css/timetable.css',
			null,
			WP_DEBUG || is_user_logged_in() ? rand() : null,
			'all'
		);

		wp_enqueue_style( 'wcs-timetable' );

	}

	function wcs_schedule( $atts = null, $content = null ){
		$atts = shortcode_atts( array( 'id' => null ), $atts, 'wcs_schedule' );
		return do_shortcode( "[wcs_timetable id='{$atts['id']}']" );
	}

	function modal_templates(){

		$wcs_page_has_shortcode = wp_cache_get( 'has_shortcode', 'EventsSchedule' );
		$wcs_page_dependencies 	= wp_cache_get( 'dependencies', 'EventsSchedule' );
//error_log(print_r($wcs_page_dependencies, true));
		if( ! filter_var( $wcs_page_has_shortcode, FILTER_VALIDATE_BOOLEAN ) ) return;

		$wcs_page_dependencies = ! $wcs_page_dependencies ? array() : $wcs_page_dependencies;

		foreach( $wcs_page_dependencies as $key => $dep ){
			if( ! wp_script_is( $dep ) ) wp_enqueue_script( $dep );
		}

		if( ! wp_script_is( 'wcs-main' ) )
			wp_enqueue_script( 'wcs-main' );

		$templates = apply_filters( 'wcs_register_templates_filters', array() );

		foreach( $templates as $key => $data ){
			foreach( $data as $key_data => $url ){
				include( $url );
			}
		}

		?>

		<div id="wcs-vue-modal"></div>

		<?php


	}


	public static function hex2rgb( $colour, $opacity = 1, $array = false ) {

		if ( ! is_array( $colour ) && strpos( $colour, '#') !== false ) {
		    $colour = substr( $colour, 1 );
		}
		if ( strlen( $colour ) == 6 ) {
		        list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} elseif ( strlen( $colour ) == 3 ) {
		        list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
		        return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		if( $array === true )
			return array( 'r' => $r, 'g' => $g, 'b' => $b );

		return "rgba( $r, $g, $b, $opacity)";

	}

	public static function contrast( $color, $opacity1 = 1, $opacity2 = 1 ) {
		return (abs(self::brightness('#ffffff') - self::brightness(self::darken($color))) > abs(self::brightness('#000000') - self::brightness(self::darken($color)))) ? self::hex2rgb('#ffffff', $opacity1) : self::hex2rgb('#000000', $opacity2);
	}

	public static function brightness( $hexStr ) {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
		$rgbArray = array();
		if (strlen($hexStr) == 6) {
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) {
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false;
		}
		return (($rgbArray['red']*299) + ($rgbArray['green']*587) + ($rgbArray['blue']*114))/1000;
	}
	public static function darken( $color, $dif=20 ){
	    $color = str_replace('#', '', $color);
	    if (strlen($color) != 6){ return '000000'; }
	    $rgb = '';
	    for ($x=0;$x<3;$x++){
	        $c = hexdec(substr($color,(2*$x),2)) - $dif;
	        $c = ($c < 0) ? 0 : dechex($c);
	        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
	    }
	    return '#'.$rgb;
	}


	static function set_filters( $filter = null, $key = null, $filters = array() ){

		if( ! is_null( $filter ) ){

			if( strpos( $filter, ',' ) !== false ){
				if( strpos( $filter, ' ' ) !== false){
					$filter = str_replace( ' ', '', $filter );
				}
				$filter = explode( ',', $filter );
				foreach( $filter as $value ){
					$filters[] = array( 'name' => $key, 'value' => $value );
				}
			} else{
				$filters[] = array( 'name' => $key, 'value' => $filter );
			}

		}

		return $filters;
	}


	function filter_tdata( $data, $id ){
		$data = apply_filters( 'wcs_filter_migration_tdata', $data );

		$data['id'] = isset( $data['id'] ) ? $data['id'] : $id;
		$data['color_special_contrast'] =  self::contrast( isset( $data['color_special'] ) && ! empty( $data['color_special'] ) ? $data['color_special'] : '#BF392B', 1, 1 );
		$data['terms_colors'] = array();
		if( ! isset( $data['content'] ) || ! is_array( $data['content'] ) ){
			$content = array();
			$taxes = WeeklyClass::get_object_taxonomies( 'class', 'objects' );
			foreach( $taxes as $tax =>$tax_value ){
				$content[$tax] = array();
				$terms = get_terms( $tax, array( 'hide_empty' => true ) );
				foreach($terms as $term){
					$content[$tax][] = $term->term_id;
				}
			}
		} else{
			$content = $data['content'];
		}

		foreach( $content as $tax => $terms ){
			foreach( $terms as $term ){
				$term_data  = get_option( "taxonomy_$term" );
				if( $term_data !== false && ! empty( $term_data ) && isset( $term_data['color'] ) && ! empty( $term_data['color'] ) ){
					$data['terms_colors'][$term] = $term_data['color'];
				}
			}
		}

		foreach ( $data as $key => $value ) {
			if( strpos( $key, 'show_' ) === 0 ){
				$data[$key] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			}
			if( ! isset( $data['show_filter_time_of_day'] ) && $key === 'show_filter_time' ){
				$data['show_filter_time_of_day'] = $value;
			}
			if( ! isset( $data['show_filter_day_of_week'] ) && $key === 'show_filter_day' ){
				$data['show_filter_day_of_week'] = $value;
			}
			if( strpos( $key, 'dateformat' ) !== false  ){
				$data[$key] = stripslashes( $value );
			}
		}
		return $data;
	}


	function default_css( $css, $data, $schedule_id ){
		$color_text = isset( $data['color_text'] ) && ! empty( $data['color_text'] ) && $data['color_text'] !== 'undefined' ? $data['color_text'] : 'inherit';
		$color_special = isset( $data['color_special'] ) && ! empty( $data['color_special'] ) && $data['color_special'] !== 'undefined' ? $data['color_special'] : '#BF392B';
		$css .= "
			.wcs-timetable--$schedule_id.wcs-timetable__container{
				color: {$color_text};
			}
			.wcs-timetable--$schedule_id .wcs-timetable{
				border-color: {$color_text};
			}
			.wcs-timetable--$schedule_id .wcs-filters__title,
			.wcs-timetable--$schedule_id .wcs-filters__filter-wrapper:hover,
			.wcs-timetable--$schedule_id .wcs-filter:checked + span{
				color: {$color_special};
			}
			.wcs-timetable--$schedule_id .wcs-btn--action,
			.wcs-timetable--$schedule_id .wcs-btn--action:hover{
				background-color: {$color_special};
				color: " . self::contrast( $color_special, 1, 0.75 ) . ";
			}

			.wcs-modal[data-wcs-modal-id='$schedule_id'] .wcs-btn--action,
			.wcs-modal[data-wcs-modal-id='$schedule_id'] .wcs-btn--action:hover{
				background-color: {$color_special};
				color: " . self::contrast( $color_special, 1, 0.75 ) . ";
			}
		";


		if( isset( $data['filters_style'] ) && filter_var( $data['filters_style'], FILTER_VALIDATE_BOOLEAN ) ){

			$css .= "
				.wcs-timetable--$schedule_id .wcs-filters--switches input:checked + .wcs-switcher__switch{
					color: $color_special;
				}
				.wcs-timetable--$schedule_id .wcs-switcher__switch{
					color: " . ( $color_text === 'inherit' ? 'inherit' : self::hex2rgb( $color_text, 0.25 ) ) . ";
					border-color: " . ( $color_text === 'inherit' ? 'inherit' : self::hex2rgb( $color_text, 0.25 ) ) . ";
				}
			";

		}

		return $css;

	}

	public static function get_view( $views, $view ){
		if( is_array( $views ) ){
			foreach( $views as $key => $item ){
				if( intval( $item['value'] ) === intval( $view ) ){
					return $item;
				}
			}
		}
	}


	static public function timetable( $atts, $content = null ){

		$atts = shortcode_atts( array( 'id' => null ), $atts, 'wcs_timetable' );
		extract( $atts );

		$prepend_filters = true;

		$views = apply_filters( 'wcs_views', array() );

		$tdata_transient = is_user_logged_in() ? false : get_transient( 'wcs_tdata_' . $id );

		if( ! $tdata_transient ){

			$data = get_option( "__wcs_schedule_$id" );

			if( $data === false )
				return;

			$data = apply_filters( 'wcs_filter_front_tdata', maybe_unserialize( $data ), $id );

			$view_object 	= self::get_view( $views, isset( $data['view'] ) ? $data['view'] : 0 );
			$has_filters 	= isset( $view_object['filters'] ) ? filter_var( $view_object['filters'], FILTER_VALIDATE_BOOLEAN ) : true;

			$template 	= isset( $view_object['slug'] ) ? esc_attr( $view_object['slug'] ) : false;
			$template_filters = $has_filters ? isset( $view_object['filters_template'] ) ? esc_attr( $view_object['filters_template'] ) : 'default' : false;

			$mixins = isset( $view_object['mixins'] ) ? esc_attr( $view_object['mixins'] ) : false;

			$tdata_transient = array();
			$tdata_transient['is_single'] = isset( $view_object['single'] ) && filter_var( $view_object['single'], FILTER_VALIDATE_BOOLEAN ) ? true : false;
			$tdata_transient['filters'] = json_encode( $has_filters ? WeeklyClass::get_filters_json( $data ) : array() );
			$tdata_transient['options'] = array_merge( $data, array( 'el_id' => $id, 'mixins' => $mixins, 'is_single' => $tdata_transient['is_single'] ) );
			$tdata_transient['css'] 		= apply_filters( 'wcs_view_css', '', $data, $id );
			$tdata_transient['template'] = $template;
			$tdata_transient['template_filters'] = $template_filters;

			if( ! is_user_logged_in() ){
				set_transient( 'wcs_tdata_' . $id, $tdata_transient, WEEK_IN_SECONDS );
			}

		}

		$data = $tdata_transient['options'];

		if( $tdata_transient['template'] !== false ){
			$template = wcs_get_template_part( 'display/' . $tdata_transient['template'] );
		}

		if( $tdata_transient['template_filters'] !== false ){
			$template_filters = wcs_get_template_part( 'filters/filters', $tdata_transient['template_filters'] );
		}

		if( ! $tdata_transient['is_single'] ){

			$days = isset( $data['days'] ) && $data['days'] !== 0 && $data['days'] !== '0' ? intval( $data['days'] ) : WCS_ALL_DAYS;

			$start_tstamp = apply_filters( 'wcs_start', strtotime( date_i18n( 'Y/m/d', current_time( 'timestamp' ) ) ), $data );
			$stop_tstamp 	= apply_filters( 'wcs_stop', $start_tstamp + ( $days - 1 ) * DAY_IN_SECONDS, $data );

			$start 	= date_i18n( 'Y-m-d', $start_tstamp );
			$stop 	= date_i18n( 'Y-m-d', $stop_tstamp );

			$feed = WeeklyClass::get_events_json( true,
				array(
					'start' => $start,
					'end' => $stop,
					'limit' => isset( $data['limit'] ) ? intval( $data['limit'] ) : 0,
					'content' => isset( $data['content'] ) && is_array( $data['content'] ) ? $data['content'] : array()
				)
			);

			$data['ts_start'] = $start;
			$data['ts_stop'] 	= $stop;

		}

		else {
			if( ! empty( $data['single'] ) ){
				$feed = WeeklyClass::get_event_json( $data['single'] );
			}
		}

		ob_start();

		$view_object 	= self::get_view( $views, isset( $data['view'] ) ? $data['view'] : 0 );

		$prepend_filters = isset( $view_object['prepend_filters'] ) && ! filter_var( $view_object['prepend_filters'], FILTER_VALIDATE_BOOLEAN ) ? false : $prepend_filters;

		?>
		<div class="wcs-timetable__wrapper">
			<?php echo '<style type="text/css" scoped>' . apply_filters( 'wcs_minify_css', htmlspecialchars_decode( $tdata_transient['css'] ) ) . '</style>'; ?>
			<script type="text/x-template" id="wcs_templates_timetable--wcs-app-<?php echo $id ?>">
				<div class="wcs-timetable__container <?php echo "wcs-timetable--$id"; ?>" :class="app_classes" data-id="<?php echo $id ?>"  id="wcs-app-<?php echo $id ?>" v-cloak>
				<?php
					if( $prepend_filters ) { if( isset( $template_filters ) && ! empty( $template_filters ) && $template_filters !== false ) include( $template_filters ); }
					if( isset( $template ) && ! empty( $template ) && $template !== false ) include( $template );
				?>
				</div>
			</script>
			<script type="text/javascript">
				/* <![CDATA[ */
				window['<?php echo 'wcs_tdata_' . $id ?>'] = <?php echo json_encode( isset( $feed ) ? $feed : array() ) ?>;
				window['<?php echo 'wcs_tdata_filters_' . $id ?>'] = <?php echo $tdata_transient['filters'] ?>;
				window['<?php echo 'wcs_tdata_options_' . $id ?>'] = <?php echo json_encode( $data ) ?>;
				/* ]]> */
			</script>
			<div class="wcs-vue" id="wcs-app-<?php echo $id ?>"></div>

		</div>
		<?php

			wp_cache_set( 'has_shortcode', true, 'EventsSchedule' );

			return ob_get_clean();

	    }

}


?>
