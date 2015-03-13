<?php

class AP_HoneyPot {

	var $dummy_api_key;
	var $bl_types;

	var $visitor_ip;
	var $age_thres;
	var $threat;
	var $honeypot;
	var $do_log;
	var $do_stats;
	var $log_blocked_only;
	var $not_logged_ips;
	var $not_logged_ips_str;
	var $logtable;
	var $logtable_exists;
	var $stats_data;
	var $active;
	var $api_key;
	var $denied_types;
	var $stats_pattern;
	var $stats_link;
	var $gmt_offset;

	function AP_HoneyPot() {
		$this->set_vars();
		$this->add_actions();
		$this->register_widgets();
		$this->register_admin_notices();
	}

	function set_vars() {
		global $table_prefix;

		/* consts */
		$this->dummy_api_key = 'abcdefghijkl';
		$this->bl_types = array( 1, 2, 4 );

		/* vars */
		$this->visitor_ip = $_SERVER['REMOTE_ADDR'];

		// Get thresholds
		$this->age_thres = get_option('httpbl_age_thres');
		if ( empty( $this->age_thres ) ) {
			$this->age_thres = '14';
			update_option( 'httpbl_age_thres' , $this->age_thres );
		}
		$this->threat['thres'] = get_option('httpbl_threat_thres');
		if ( empty( $this->threat['thres'] ) ) {
			$this->threat['thres'] = '30';
			update_option( 'httpbl_threat_thres' , $this->threat['thres'] );
		}
		$this->threat['thres_s'] = get_option('httpbl_threat_thres_s');
		$this->threat['thres_h'] = get_option('httpbl_threat_thres_h');
		$this->threat['thres_c'] = get_option('httpbl_threat_thres_c');

		foreach ( $this->bl_types as $value ) {
			$this->denied_types[$value] = get_option( 'httpbl_deny_' . $value );
		}

		$this->white_listed_ips_str = get_option( 'httpbl_white_listed_ips' );
		$this->white_listed_ips = explode( " ", $this->white_listed_ips_str );

		$this->honeypot = get_option( 'httpbl_hp' );

		$this->logtable = $table_prefix . 'httpbl_log';
		$this->do_log = get_option( 'httpbl_log' );
		$this->do_stats = get_option( 'httpbl_stats' );
		$this->stats_pattern = get_option('httpbl_stats_pattern');
		$this->stats_link = get_option('httpbl_stats_link');
		$this->gmt_offset = get_option( 'gmt_offset' );
		$this->logtable_exists = $this->check_log_table();

		$this->api_key = get_option( "httpbl_key" );
		if ( empty( $this->api_key ) ) {
			$this->api_key = $this->dummy_api_key;
			update_option( 'httpbl_key' , $this->api_key );
			$this->active = false;
		} elseif ( $this->api_key == $this->dummy_api_key ) {
			$this->active = false;
		} else {
			$this->active = true;
		}

		$this->not_logged_ips_str = get_option( 'httpbl_not_logged_ips' );
		$this->log_blocked_only = get_option( 'httpbl_log_blocked_only' );

		if ( $this->do_log ) {
			$this->not_logged_ips = explode( " ", $this->not_logged_ips_str );
		} else {
			$this->not_logged_ips = false;
		}
	}

	function add_actions() {
		add_action( 'init', array( &$this, 'check_post_args' ), 1);
		add_action( 'init', array( &$this, 'check_visitor' ), 1);
		add_action( 'wp_footer', array( &$this, 'show_honeypot' ) );
		add_action( 'init', array( &$this, 'get_stats' ), 10 );
		add_action( 'admin_menu', array( &$this, 'config_page' ) );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
	}

	function register_widgets() {
		add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );
	}

	function register_admin_notices() {
		add_action( 'admin_notices', array( &$this, 'plugin_not_active' ) );
	}

	function plugin_not_active(){
		if ( ! $this->active ) {
			echo '<div id="ap-honeypot-nag" class="updated fade">
			   To start using <strong>AP HoneyPot</strong> you must specify a working http:BL Access Key! <a href="' .
			   APHP_PLUGIN_SETTINGS_URL .
			   '">Go to configuration</a>.
			</div>';
		}
	}

	function add_dashboard_widgets() {
		wp_add_dashboard_widget('ap_honeypot_dashboard_log', 'AP HoneyPot Log',
			array( &$this, 'dashboard_log' ), array( &$this, 'dashboard_log_configure' ) );
		wp_add_dashboard_widget('ap_honeypot_dashboard_check_ip', 'AP HoneyPot Check IP',
			array( &$this, 'dashboard_check_ip' ) );
	}

	function dashboard_log_configure() {
        if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
                $widget_options = array();

        if ( !isset($widget_options['dashboard_ap_honeypot']) )
                $widget_options['dashboard_ap_honeypot'] = array();

        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-ap-honeypot-log-entries']) ) {
                $number = absint( $_POST['widget-ap-honeypot-log-entries']['items'] );
				if ($number < 1)
					$number = 1;
				elseif ($number > 50)
					$number = 50;
                $widget_options['dashboard_ap_honeypot_log_entries']['items'] = $number;
                update_option( 'dashboard_widget_options', $widget_options );
        }

        $number = isset( $widget_options['dashboard_ap_honeypot_log_entries']['items'] ) ? (int) $widget_options['dashboard_ap_honeypot_log_entries']['items'] : 10;
		if ($number < 1)
			$number = 1;
		elseif ($number > 50)
			$number = 50;

        echo '<p><label for="log-entries-number">' . __('Number of log entries to show:', 'aphoneypot') . '</label>';
        echo '<input id="log-entries-number" name="widget-ap-honeypot-log-entries[items]" type="text" value="' . $number . '" size="3" /></p>';
	}

	function dashboard_log() {
		?>
		<table cellpadding="5px" cellspacing="3px">
			<tr>
				<th>ID</th>
				<th>IP</th>
				<th>Date</th>
				<th>User agent</th>
				<th>Last seen<sup>1</sup></th>
				<th>Threat</th>
				<th>Type<sup>2</sup></th>
				<th>Blocked</th>
			</tr>
			<?php
				$widget_options = get_option( 'dashboard_widget_options' );
				$number = isset( $widget_options['dashboard_ap_honeypot_log_entries']['items'] ) ? (int) $widget_options['dashboard_ap_honeypot_log_entries']['items'] : 10;
				if ($number < 1)
					$number = 1;
				elseif ($number > 50)
					$number = 50;
			?>
			<?php $this->print_log_table_contents($number); ?>
		</table>
		<p><small><sup>1</sup> Counting from the day of visit.</small></p>
		<p><small><sup>2</sup> S - suspicious, H - harvester, C - comment spammer.</small></p>
		<p><small><a href="<?php echo esc_url( APHP_PLUGIN_SETTINGS_URL ); ?>">Go to plugin settings</a></small></p>
		<?php
	}

	function dashboard_check_ip() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['ap_honeypot_test_ip']) ) {
			$ip = long2ip(ip2long(trim($_POST['ap_honeypot_test_ip'])));
			$result = $this->parse_httpbl_answer($ip, $this->check_httpbl($ip));
			$ip = "<a href='http://www.projecthoneypot.org/ip_" . $ip .
						"' target='_blank'>" . $ip . "</a>";
			if ($result) {
		?>
			<table cellspacing="10px" cellpadding="5px" width="100%">
				<tr>
					<th style="text-align:left;">IP</th>
					<th style="text-align:left;">Last Seen</th>
					<th style="text-align:left;">Threat</th>
					<th style="text-align:left;">Type</th>
					<th style="text-align:left;">White-listed</th>
					<th style="text-align:left;">To be blocked</th>
				</tr>
				<tr>
					<td><?php echo $ip; ?></td>
					<td><?php echo $result['age']; ?> days</td>
					<td><?php echo $result['threat']; ?></td>
					<td><?php echo implode('/', $result['type']); ?></td>
					<td><?php echo ($result['WL'] ? '<strong>YES</strong>' : 'No') ; ?></td>
					<td><?php echo ($result['block'] ? '<strong>YES</strong>' : 'No') ; ?></td>
				</tr>
			</table>
		<?php
			} else {
		?>
				<p>No data...</p>
		<?php
			}
		}
		?>
		<table width = "100%">
			<tr>
				<td align="center">
		<form id="ap-honeypot-check-ip" method="post" action="" name="post">
			<label for="ap-honeypot-check-ip-test-ip">IP Address:</label><input type="text" size="20" value="" autocomplete="off" tabindex="1" id="ap-honeypot-check-ip-test-ip" name="ap_honeypot_test_ip">
			<p class="submit">
				<input type="submit" class="button" value="Check">
				<br class="clear"/>
			</p>
		</form>
				</td>
			</tr>
		</table>
		<?php
	}

	function plugin_action_links( $links, $file ) {
		if ( $file != APHP_PLUGIN_BASENAME )
			return $links;

		$settings_link = '<a href="' . esc_url( APHP_PLUGIN_SETTINGS_URL ) . '">'
			. esc_html( __( 'Settings', 'aphoneypot' ) ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	// Add a line to the log table
	function add_log( $ip, $user_agent, $response, $blocked ) {
		global $wpdb;
		$time = gmdate( 'Y-m-d H:i:s',
			time() + $this->gmt_offset * 60 * 60 );
		$blocked = ( $blocked ? 1 : 0 );

		$wpdb->query( $wpdb->prepare( "
			INSERT INTO $this->logtable
			( ip, time, user_agent, httpbl_response, blocked )
			VALUES ( %s, %s, %s, %s, %s )",
			$ip, $time, $user_agent, $response, $blocked ) );
	}

	// Get latest $lines entries from the log table
	function get_log( $lines = 50 ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare ( "
			SELECT * FROM $this->logtable
			ORDER BY id DESC LIMIT %d", $lines ) );
	}

	// Get numbers of blocked and passed visitors from the log table
	// and place them in $this->stats_data[]
	function get_stats() {
		global $wpdb;
		if ( $this->do_log && $this->do_stats && $this->logtable_exists ) {
			$results = $wpdb->get_results("
				SELECT blocked, count(*) FROM $this->logtable
				GROUP BY blocked", ARRAY_N );
		}

		if ( ! empty( $results ) ) {
			foreach ( (array) $results as $row ) {
				if ( $row[0] == 1 )
					$this->stats_data['blocked'] = $row[1];
				else
					$this->stats_data['passed'] = $row[1];
			}
		}
		if ( ! isset($this->stats_data['blocked']) )
			$this->stats_data['blocked'] = 0;

		if ( ! isset($this->stats_data['passed']) )
			$this->stats_data['passed'] = 0;
	}

	// Display stats. Output may be configured at the plugin's config page.
	function print_stats() {
		if ($this->do_log && $this->do_stats) {
			$search = array(
				'$block',
				'$pass',
				'$total'
			);
			$replace = array(
				$this->stats_data['blocked'],
				$this->stats_data['passed'],
				$this->stats_data['blocked'] + $this->stats_data['passed']
			);
			$link_prefix = array(
				"",
				"<a href='http://www.projecthoneypot.org/?rf=101236'>",
				"<a href='http://wordpress.org/extend/plugins/httpbl/'>"
			);
			$link_suffix = array(
				"",
				"</a>",
				"</a>"
			);
			echo $link_prefix[$this->stats_link] . str_replace($search, $replace, $this->stats_pattern) . $link_suffix[$this->stats_link];
		}
	}

	// Check whether the table exists
	function check_log_table() {
		global $wpdb;

		/* to rewrite! */
		$result = $wpdb->get_results( "SHOW TABLES LIKE '$this->logtable'" );
		foreach ($result as $stdobject) {
			foreach ($stdobject as $table) {
				if ("$this->logtable" == $table) {
					return true;
				}
			}
		}
		return false;
	}

	// Truncate the log table
	function truncate_log_table() {
		global $wpdb;
		return $wpdb->get_results( "TRUNCATE $this->logtable" );
	}

	// Drop the log table
	function drop_log_table() {
		global $wpdb;
		update_option( 'httpbl_log', false );
		$this->do_log = false;
		$this->logtable_exists = false;
		return $wpdb->get_results( "DROP TABLE $this->logtable" );
	}

	// Create a new log table
	function create_log_table() {
		global $wpdb;
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `$this->logtable` (
			`id` INT( 6 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`ip` VARCHAR( 16 ) NOT NULL DEFAULT 'unknown',
			`time` DATETIME NOT NULL,
			`user_agent` VARCHAR( 255 ) NOT NULL DEFAULT 'unknown',
			`httpbl_response` VARCHAR( 16 ) NOT NULL,
			`blocked` BOOL NOT NULL )" );
	}

	function check_httpbl( $ip = null ) {
		if ( empty($ip) )
			$ip = $this->visitor_ip;

		// The http:BL query
		$httpbl_host = $this->api_key . "." . implode ( ".", array_reverse( explode( ".", $ip ) ) ) . ".dnsbl.httpbl.org";

		$httpbl = explode( ".", gethostbyname( $httpbl_host ) );

		if ( empty( $httpbl ) || ( $httpbl[0] != 127 ) ) {
			return false;
			if ( WP_DEBUG ) {
				trigger_error( sprintf( __('Connection to %1$s failed!',  'aphoneypot'), $httpbl_host ) );
				$e = new Exception();
				trigger_error( print_r( $e->getTraceAsString(), true ) );
			}
		}
		return $httpbl;
	}

	function parse_httpbl_answer( $test_ip, $httpbl ) {
		if (empty ($httpbl))
			return false;

		$result = array(
			'age' => false,
			'threat' => false,
			'type' => array(),
			'WL' => false,
			'block' => false
		);

		$age = false;
		$threat = false;

		$result['age'] = $httpbl[1];

		if ( $result['age'] < $this->age_thres )
			$age = true;

		$result['threat'] = $httpbl[2];

		if ( $httpbl[3] & 1 ) {
			$result['type'][] = 'S';

			if ( $this->threat['thres_s'] ) {
				if ( $httpbl[2] > $this->threat['thres_s'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		if ( $httpbl[3] & 2 ) {
			$result['type'][] = 'H';

			if ( $this->threat['thres_h'] ) {
				if ( $httpbl[2] > $this->threat['thres_h'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		if ( $httpbl[3] & 4 ) {
			$result['type'][] = 'C';

			if ( $this->threat['thres_c'] ) {
				if ( $httpbl[2] > $this->threat['thres_c'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		if ( ! empty( $this->white_listed_ips ) ) {
			foreach ( $this->white_listed_ips as $ip ) {
				if ( $ip == $test_ip ) {
					$result['WL'] = true;
					break;
				}
			}
		}

		foreach ( $this->denied_types as $key => $value ) {
			if ( ($httpbl[3] - $httpbl[3] % $key) > 0 && $value )
				$deny = true;
		}

		if ($deny && $age && $threat && !$result['WL'])
			$result['block'] = true;

		return $result;
	}

	// The visitor verification function
	function check_visitor() {
		if ( ! $this->active )
			return;
		if ( ( $httpbl = $this->check_httpbl() ) === false )
			return;

		// Assume that visitor's OK
		$age = false;
		$threat = false;
		$deny = false;
		$blocked = false;

		if ( $httpbl[1] < $this->age_thres )
			$age = true;

		// Check suspicious threat
		if ( $httpbl[3] & 1 ) {
			if ( $this->threat['thres_s'] ) {
				if ( $httpbl[2] > $this->threat['thres_s'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		// Check harvester threat
		if ( $httpbl[3] & 2 ) {
			if ( $this->threat['thres_h'] ) {
				if ( $httpbl[2] > $this->threat['thres_h'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		// Check comment spammer threat
		if ( $httpbl[3] & 4 ) {
			if ( $this->threat['thres_c'] ) {
				if ( $httpbl[2] > $this->threat['thres_c'] )
					$threat = true;
			} else {
				if ( $httpbl[2] > $this->threat['thres'] )
					$threat = true;
			}
		}

		foreach ( $this->denied_types as $key => $value ) {
			if ( ($httpbl[3] - $httpbl[3] % $key) > 0 && $value )
				$deny = true;
		}

		if ( ! empty( $this->white_listed_ips ) ) {
			foreach ( $this->white_listed_ips as $ip ) {
				if ( $ip == $this->visitor_ip ) {
					$white_listed = true;
					break;
				}
			}
		} else {
			$white_listed = false;
		}

		// If he's not OK
		if ( $deny && $age && $threat && ! $white_listed )
			$blocked = true;

		// Are we logging?
		if ( $this->do_log == true ) {

			// At first we assume that the visitor
			// should be logged
			$log = true;

			// Checking if he's not one of those, who
			// are not logged
			if ( ! empty( $this->not_logged_ips ) ) {
				foreach ( $this->not_logged_ips as $ip ) {
					if ( $ip == $this->visitor_ip ) {
						$log = false;
						break;
					}
				}
			}

			// Don't log search engine bots
			if ( $httpbl[3] == 0 )
				$log = false;

			// If we log only blocked ones
			if ( $this->log_blocked_only && !$blocked )
				$log = false;

			// If he can be logged, we log him
			if ( $log ) {
				$this->add_log(
					$this->visitor_ip,
					$_SERVER['HTTP_USER_AGENT'],
					implode( $httpbl, "." ),
					$blocked
				);
			}
		}

		if ( $blocked ) {
			// If we've got a Honey Pot link
			if ( $this->honeypot ) {
				header( "HTTP/1.1 301 Moved Permanently ");
				header( "Location: $this->honeypot" );
			}
			die();
		}
	}

	function show_honeypot() {
		if ( $this->honeypot )
			echo '<div style="display: none;"><a href="' . $this->honeypot . '">Bear</a></div>';
	}

	function config_page() {
		add_submenu_page( APHP_PLUGIN_MENU_PARENT, 'AP HoneyPot',
			'AP HoneyPot', 'activate_plugins', APHP_PLUGIN_FULL_PATH, array( &$this, 'configuration' ) );
	}

	function check_post_args() {
		// If the save button was clicked...
		if ( ! empty( $_POST['ap_hp_save'] ) ) {
			$this->save_configuration();
			$this->set_vars();
		}

		// Should we purge the log table?
		if ( ! empty( $_POST["httpbl_truncate"] ) )
			$this->truncate_log_table();

		// Should we delete the log table?
		if ( ! empty( $_POST["httpbl_drop"] ) )
			$this->drop_log_table();

		// Should we create a new log table?
		if ( ! empty( $_POST["httpbl_create"] ) )
			$this->create_log_table();
	}

	function save_configuration() {
		// ...the options are updated.
		if ( ! empty($_POST['key']) )
			update_option( 'httpbl_key', $_POST['key'] );
		else
			update_option( 'httpbl_key' , 'abcdefghijkl' );

		if ( ! empty($_POST['age_thres']) )
			update_option( 'httpbl_age_thres', $_POST['age_thres'] );
		else
			update_option( 'httpbl_age_thres', 14 );

		if ( ! empty($_POST['threat_thres']) )
			update_option( 'httpbl_threat_thres', $_POST['threat_thres'] );
		else
			update_option( 'httpbl_threat_thres', 30 );

		if ( isset($_POST['threat_thres_s']) )
			update_option( 'httpbl_threat_thres_s', $_POST['threat_thres_s'] );

		if ( isset($_POST['threat_thres_h']) )
			update_option( 'httpbl_threat_thres_h', $_POST['threat_thres_h'] );

		if ( isset($_POST['threat_thres_c']) )
			update_option( 'httpbl_threat_thres_c', $_POST['threat_thres_c'] );

		foreach ( $this->bl_types as $value ) {
			if ( ! empty($_POST["deny_{$value}"]) )
				$denied_value = true;
			else
				$denied_value = false;
			update_option( "httpbl_deny_{$value}", $denied_value );
		}

		if ( isset($_POST['white_listed_ips']) )
			update_option('httpbl_white_listed_ips', $_POST['white_listed_ips'] );

		if ( isset($_POST['hp']) )
			update_option( 'httpbl_hp', $_POST['hp'] );

		if ( ! empty($_POST['enable_log']) )
			update_option( 'httpbl_log', true );
		else
			update_option( 'httpbl_log', false );

		if ( ! empty($_POST['log_blocked_only']) )
			update_option( 'httpbl_log_blocked_only', true );
		else
			update_option( 'httpbl_log_blocked_only', false );

		if ( isset($_POST['not_logged_ips']) )
			update_option('httpbl_not_logged_ips', $_POST['not_logged_ips'] );

		if ( ! empty( $_POST['enable_stats'] ) )
			update_option( 'httpbl_stats', true );
		else
			update_option( 'httpbl_stats', false );

		if ( isset( $_POST['stats_pattern'] ) )
			update_option( 'httpbl_stats_pattern', $_POST['stats_pattern'] );

		if ( isset( $_POST['stats_link'] ) )
			update_option( 'httpbl_stats_link', $_POST['stats_link'] );

		header( "HTTP/1.1 301 Moved Permanently ");
		header( "Location: " . APHP_PLUGIN_SETTINGS_URL . "&saved=1" );
		die();
	}

	function configuration() {
		// If we log, but there's no table.
		if ( $this->do_log && ! $this->logtable_exists )
			$this->create_log_table();

		foreach ( $this->bl_types as $value ) {
			$deny_checkbox[$value] = ($this->denied_types[$value] ? "checked='checked'" : "");
		}

		$log_checkbox = checked( $this->do_log, true, false );
		$log_blocked_only_checkbox = checked( $this->log_blocked_only, true, false );
		$stats_checkbox = checked( $this->do_stats, true, false );
		$stats_link_radio = array("", "", "");
		for ($i = 0; $i < 3; $i++) {
			if ($this->stats_link == $i) {
				$stats_link_radio[$i] = "checked='checked'";
				break;
			}
		}
		// The page contents. ?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br/></div>
			<h2>AP HoneyPot Wordpress Plugin</h2>

			<p><a href="#conf">Configuration</a>
			<?php // No need to link to the log section, if we're not logging ?>
			<?php if ($this->do_log): ?>
					| <a href="#log">Log</a></p>
			<?php endif; ?>
			<p>The AP HoneyPot WordPress Plugin allows you to verify IP addresses of clients connecting to your blog against the <a href="http://www.projecthoneypot.org/?rf=101236">Project Honey Pot</a> database.</p>

			<a name="conf"></a>
			<h3>Main options</h3>
			<form action='' method='post' id='httpbl_conf'>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="key">http:BL Access Key</label></th>
							<td>
								<input type="text" class="regular-text code" value="<?php echo $this->api_key ?>" id="key" name="key" />
								<br/>
								<small>An Access Key is required to perform a http:BL query. You can get your key at <a href="http://www.projecthoneypot.org/httpbl_configure.php">http:BL Access Management page</a>. You need to register a free account at the Project Honey Pot website to get one.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="age_thres">Age threshold</label></th>
							<td>
								<input type="text" class="small-text" value="<?php echo $this->age_thres ?>" id="age_thres" name="age_thres" />
								<br/>
								<small>http:BL service provides you information about the date of the last activity of a checked IP. Due to the fact that the information in the Project Honey Pot database may be obsolete, you may set an age threshold, counted in days. If the verified IP hasn't been active for a period of time longer than the threshold it will be regarded as harmless.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="age_thres">General threat score threshold</label></th>
							<td>
								<input type="text" class="small-text" value="<?php echo $this->threat['thres'] ?>" id="threat_thres" name="threat_thres" />
								<br/>
								<small>Each suspicious IP address is given a threat score. This scored is asigned by Project Honey Pot basing on various factors, such as the IP's activity or the damage done during the visits. The score is a number between 0 and 255, where 0 is no threat at all and 255 is extremely harmful. In the field above you may set the threat score threshold. IP address with a score greater than the given number will be regarded as harmful.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Particular thread scrore thresholds</th>
							<td>
								<label for="threat_thres_s">Suspicious</label>
								<input type="text" class="small-text" value="<?php echo $this->threat['thres_s'] ?>" id="threat_thres_s" name="threat_thres_s" />
								<label for="threat_thres_h">Harvester</label>
								<input type="text" class="small-text" value="<?php echo $this->threat['thres_h'] ?>" id="threat_thres_h" name="threat_thres_h" />
								<label for="threat_thres_c">Comment spammer</label>
								<input type="text" class="small-text" value="<?php echo $this->threat['thres_c'] ?>" id="threat_thres_c" name="threat_thres_c" />
								<br/>
								<small>These values override the general threat score threshold. Leave blank to use the general threat score threshold.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Types of visitors to be treated as malicious</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Types of visitors to be treated as malicious</span></legend>
									<label for="deny_1">
										<input type="checkbox" <?php echo $deny_checkbox[1] ?> value="1" id="deny_1" name="deny_1">
									Suspicious</label>
									<br/>
									<label for="deny_2">
										<input type="checkbox" <?php echo $deny_checkbox[2] ?> value="1" id="deny_2" name="deny_2">
									Harvesters</label>
									<br/>
									<label for="deny_4">
										<input type="checkbox" <?php echo $deny_checkbox[4] ?> value="1" id="deny_4" name="deny_4">
									Comment spammers</label>
									<br/>
									<small>The field above allows you to specify which types of visitors should be regarded as harmful. It is recommended to tick all of them.</small>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="white_listed_ips">White-listed IP addresses</label></th>
							<td>
								<input type="text" class="regular-text code" value="<?php echo $this->white_listed_ips_str ?>" id="white_listed_ips" name="white_listed_ips" />
								<br/>
								<small>Enter a space-separated list of IP addresses which will not be blocked even if they are detected as malicious.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="hp">Honey Pot</label</th>
							<td>
								<input type="text" class="regular-text code" value="<?php echo $this->honeypot ?>" id="hp" name="hp" />
								<br/>
								<small>If you've got a Honey Pot or a Quick Link you may redirect all unwelcome visitors to it. If you leave the following field empty all harmful visitors will be given a blank page instead of your blog.</small>
								<br/>
								<small>More details are available at the <a href="http://www.projecthoneypot.org/httpbl_api.php">http:BL API Specification page</a>.</small>
							</td>
						</tr>
					</tbody>
				</table>

				<h3>Logging options</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Logging state</th>
							<td>
								<fieldset>
									<label for="enable_log">
										<input type="checkbox" <?php echo $log_checkbox ?> value="1" id="enable_log" name="enable_log">
									Enable logging</label>
									<br/>
									<small>If you enable logging all visitors which are recorded in the Project Honey Pot's database will be logged in the database and listed in the table below. Remember to create a proper table in the database before you enable this option!</small>

								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Blocked users</th>
							<td>
								<fieldset>
									<label for="log_blocked_only">
										<input type="checkbox" <?php echo $log_blocked_only_checkbox ?> value="1" id="log_blocked_only" name="log_blocked_only">
									Log only blocked visitors</label>
									<br/>
									<small>Enabling this option will result in logging only blocked visitors. The rest shall be forgotten.</small>

								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="not_logged_ips">Not logged IP addresses</label></th>
							<td>
								<input type="text" class="regular-text code" value="<?php echo $this->not_logged_ips_str ?>" id="not_logged_ips" name="not_logged_ips" />
								<br/>
								<small>Enter a space-separated list of IP addresses which will not be recorded in the log.</small>
							</td>
						</tr>
					</tbody>
				</table>

				<h3>Statistics options</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Stats state</th>
							<td>
								<fieldset>
									<label for="enable_stats">
										<input type="checkbox" <?php echo $stats_checkbox ?> value="1" id="enable_stats" name="enable_stats">
									Enable stats</label>
									<br/>
									<small>If stats are enabled the plugin will get information about its performance from the database, allowing it to be displayed using <code>$ap_honeypot->print_stats()</code> function.</small>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="stats_pattern">Output pattern</label></th>
							<td>
								<input type="text" class="regular-text code" value="<?php echo $this->stats_pattern ?>" id="stats_pattern" name="stats_pattern" />
								<br/>
								<small>This input field allows you to specify the output format of the statistics. You can use following variables: <code>$block</code> will be replaced with the number of blocked visitors, <code>$pass</code> with the number of logged but not blocked visitors, and <code>$total</code> with the total number of entries in the log table. HTML is welcome. PHP won't be compiled.</small>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Output link</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Output link</span></legend>
									<label><input type="radio" <?php echo $stats_link_radio[0]; ?> value="0" name="stats_link"> Disabled</label><br/>
									<label><input type="radio" <?php echo $stats_link_radio[1]; ?> value="1" name="stats_link"> <a href="http://www.projecthoneypot.org/?rf=101236">Project Honey Pot</a></label><br/>
									<label><input type="radio" <?php echo $stats_link_radio[2]; ?> value="2" name="stats_link"> <a href="http://wordpress.org/extend/plugins/ap-honeypot/">AP HoneyPot WordPress Plugin</a></label><br/>
									<small>Should we enclose the output specified in the field above with a hyperlink?</small>
								</fieldset>
							</td>
						</tr>
					</tbody>
				<table>

			<div style="float:right"><a href="http://www.projecthoneypot.org/?rf=101236"><img src="<?php echo plugins_url('', APHP_PLUGIN_FULL_PATH);?>/project_honey_pot_button.png" height="31px" width="88px" border="0" alt="Stop Spam Harvesters, Join Project Honey Pot"></a></div>
				<input id="ap_hp_save" class="button-primary" type="submit" name="ap_hp_save" value='Save Settings' />
			</form>
			<?php $this->print_log(); ?>
		</div>
		<?php
	} // configuration()

	function print_log() { ?>
		<?php if ( $this->do_log ): ?>
			<hr/>
			<a name="log"></a>
			<h3>Log</h3>
			<?php if ( $this->logtable_exists ): ?>
				<form action='' method='post' name='httpbl_log'><p>
					<?php
						// If log_table exists display a log purging form and output log
						// in a nice XHTML table.
					?>
					<script language="JavaScript"><!--
					var response;
					// Delete or purge confirmation.
					function httpblConfirm(action) {
						response = confirm("Do you really want to "+action+
							" the log table ?");
						return response;
					}
					//--></script>
					<input type='submit' class="button-primary" name='httpbl_truncate' value='Purge the log table' onClick='return httpblConfirm("purge")'/>
					<input type='submit' class="button-primary" name='httpbl_drop' value='Delete the log table' style="margin:0 0 0 30px" onClick='return httpblConfirm("delete")'/>
				</p></form>
				<p>A list of 50 most recent visitors listed in the Project Honey Pot's database.</p>
				<table cellpadding="5px" cellspacing="3px">
					<tr>
						<th>ID</th>
						<th>IP</th>
						<th>Date</th>
						<th>User agent</th>
						<th>Last seen<sup>1</sup></th>
						<th>Threat</th>
						<th>Type<sup>2</sup></th>
						<th>Blocked</th>
					</tr>
					<?php $this->print_log_table_contents(); ?>
				</table>
				<p><small><sup>1</sup> Counting from the day of visit.</small></p>
				<p><small><sup>2</sup> S - suspicious, H - harvester, C - comment spammer.</small></p>
			<?php else: /* table doesn't exist */ ?>
				<form action='' method='post' name='httpbl_log'><p>
					It seems that you haven't got a log table yet. Maybe you'd like to <input type='submit' name='httpbl_create' value='create it' /> ?
				</p></form>
			<?php endif; /* if table exists */ ?>
		<?php endif; /* do log */ ?>
		<?php
	} // print_log();

	function print_log_table_contents( $lines = 50 ) {
		// Table with logs.
		// Get data from the database.
		$results = $this->get_log( $lines );
		$i = 0;
		$threat_type = array( "", "S", "H", "S/H", "C", "S/C", "H/C", "S/H/C");
		foreach ($results as $row) {
			// Odd and even rows look differently.
			$style = ($i++ % 2 ? " class='alternate'" : "" );
			echo "\n\t<tr$style>";
			foreach ($row as $key => $val) {
				if ($key == "ip")
					// IP address lookup in the Project Honey Pot database.
					$val = "<a href='http://www.projecthoneypot.org/ip_" . $val .
						"' target='_blank'>" . $val . "</a>";
				if ($key == "user_agent")
					// In case the user agent string contains
					// unwelcome characters.
					$val = htmlentities($val, ENT_QUOTES);
				if ($key == "blocked")
					$val = ($val ? "<strong>YES</strong>" : "No");
				if ($key == "httpbl_response") {
					// Make the http:BL response human-readible.
					$octets = explode( ".", $val);
					$plural = ( $octets[1] == 1 ? "" : "s");
					$lastseen = $octets[1]." day$plural";
					$td = "\n\t\t<td><small>$lastseen</small></td>".
						"\n\t\t<td><small>".$octets[2].
						"</small></td>\n\t\t<td><small>".
						$threat_type[$octets[3]].
						"</small></td>";
				} else {
					// If it's not an http:BL response it's
					// displayed in one column.
					$td = "\n\t\t<td><small>$val</small></td>";
				}
				echo $td;
			}
			echo "\n\t</tr>";
		}
	}
}
