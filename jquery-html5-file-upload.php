<?php
/*
Plugin Name: JQuery Html5 File Upload
Plugin URI: http://wordpress.org/extend/plugins/jquery-html5-file-upload/
Description: This plugin adds a file upload functionality to the front-end screen. It allows multiple file upload asynchronously along with upload status bar.
Version: 3.0
Author: sinashshajahan
Author URI: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/**The URL of the plugin directory*/
define( 'JQHFUPLUGINDIRURL', plugin_dir_url( __FILE__ ) );

/* Runs when plugin is activated */
register_activation_hook( __FILE__, 'jquery_html5_file_upload_install' );

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'jquery_html5_file_upload_remove' );

function jquery_html5_file_upload_install() {
	add_option( "jqhfu_accepted_file_types", 'gif|jpeg|jpg|png', '', 'yes' );
	add_option( "jqhfu_inline_file_types", 'gif|jpeg|jpg|png', '', 'yes' );
	add_option( "jqhfu_maximum_file_size", '5', '', 'yes' );
	add_option( "jqhfu_thumbnail_width", '80', '', 'yes' );
	add_option( "jqhfu_thumbnail_height", '80', '', 'yes' );

	$upload_array = wp_upload_dir();
	$upload_dir   = $upload_array['basedir'] . '/files/';
	/* Create the directory where you upoad the file */
	if ( ! is_dir( $upload_dir ) ) {
		$is_success = mkdir( $upload_dir, '0755', true );
		if ( ! $is_success ) {
			die( 'Unable to create a directory within the upload folder' );
		}
	}
}

function jquery_html5_file_upload_remove() {
	/* Deletes the database field */
	delete_option( 'jqhfu_accepted_file_types' );
	delete_option( 'jqhfu_inline_file_types' );
	delete_option( 'jqhfu_maximum_file_size' );
	delete_option( 'jqhfu_thumbnail_width' );
	delete_option( 'jqhfu_thumbnail_height' );
}

if ( isset( $_POST['savesetting'] ) && $_POST['savesetting'] == "Save Setting" ) {
	update_option( "jqhfu_accepted_file_types", $_POST['accepted_file_types'] );
	update_option( "jqhfu_inline_file_types", $_POST['inline_file_types'] );
	update_option( "jqhfu_maximum_file_size", $_POST['maximum_file_size'] );
	update_option( "jqhfu_thumbnail_width", $_POST['thumbnail_width'] );
	update_option( "jqhfu_thumbnail_height", $_POST['thumbnail_height'] );
}

// Add settings link on plugin page
function jquery_html5_file_upload_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=jquery-html5-file-upload-setting.php">Settings</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'jquery_html5_file_upload_settings_link' );

if ( is_admin() ) {

	/* Call the html code */
	add_action( 'admin_menu', 'jquery_html5_file_upload_admin_menu' );


	function jquery_html5_file_upload_admin_menu() {
		add_options_page( 'JQuery HTML5 File Upload Setting', 'JQuery HTML5 File Upload Setting', 'administrator',
		                  'jquery-html5-file-upload-setting', 'jquery_html5_file_upload_html_page' );
	}
}

function jquery_html5_file_upload_html_page() {
	$args = array(
		'orderby'  => 'display_name',
		'order'    => 'ASC',
		'selected' => $_POST['user'],
	);
	?>
	<h2>JQuery HTML5 File Upload Setting</h2>

	<form method="post">
		<?php wp_nonce_field( 'update-options' ); ?>

		<table>
			<tr>
				<td>Accepted File Types</td>
				<td>
					<input type="text" name="accepted_file_types"
					       value="<?php print( get_option( 'jqhfu_accepted_file_types' ) ); ?>"/>&nbsp;filetype
					seperated by | (e.g. gif|jpeg|jpg|png)
				</td>
			</tr>
			<tr>
				<td>Inline File Types</td>
				<td>
					<input type="text" name="inline_file_types"
					       value="<?php print( get_option( 'jqhfu_inline_file_types' ) ); ?>"/>&nbsp;filetype seperated
					by | (e.g. gif|jpeg|jpg|png)
				</td>
			</tr>
			<tr>
				<td>Maximum File Size</td>
				<td>
					<input type="text" name="maximum_file_size"
					       value="<?php print( get_option( 'jqhfu_maximum_file_size' ) ); ?>"/>&nbsp;MB
				</td>
			</tr>
			<tr>
				<td>Thumbnail Width</td>
				<td>
					<input type="text" name="thumbnail_width"
					       value="<?php print( get_option( 'jqhfu_thumbnail_width' ) ); ?>"/>&nbsp;px
				</td>
			</tr
			<tr>
				<td>Thumbnail Height</td>
				<td>
					<input type="text" name="thumbnail_height"
					       value="<?php print( get_option( 'jqhfu_thumbnail_height' ) ); ?>"/>&nbsp;px
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="savesetting" value="Save Setting"/>
				</td>
			</tr>
		</table>
		<br/>
		<hr/>
		<h2>View Uploaded Files</h2>
		<table>
			<tr>
				<td>Select User</td>
				<td>
					<?php wp_dropdown_users( $args ); ?>
				</td>
				<td>
					<input type="submit" name="viewfiles" value="View Files"/> &nbsp; <input type="submit"
					                                                                         name="viewguestfiles"
					                                                                         value="View Guest Files"/>
				</td>
			</tr>
			<tr>
		</table>
		<table>
			<tr>
				<td>
					<?php
					if ( isset( $_POST['viewfiles'] ) && $_POST['viewfiles'] == 'View Files' ) {
						if ( $_POST['user'] ) {
							$upload_array = wp_upload_dir();
							$imgpath      = $upload_array['basedir'] . '/files/' . $_POST ['user'] . '/';
							$filearray    = glob( $imgpath . '*' );
							if ( $filearray && is_array( $filearray ) ) {
								foreach (
									$filearray
									as
									$filename
								)
								{
									if ( basename( $filename ) != 'thumbnail' ) {
										print( '<a href="' . $upload_array['baseurl'] . '/files/' . $_POST ['user'] . '/' . basename( $filename ) . '" target="_blank"/>' . basename( $filename ) . '</a>' );
										print( '<br/>' );
									}
								}
							}
						}
					} else if ( isset( $_POST['viewguestfiles'] ) && $_POST['viewguestfiles'] == 'View Guest Files' ) {
						$upload_array = wp_upload_dir();
						$imgpath      = $upload_array['basedir'] . '/files/guest/';
						$filearray    = glob( $imgpath . '*' );
						if ( $filearray && is_array( $filearray ) ) {
							foreach (
								$filearray
								as
								$filename
							)
							{
								if ( basename( $filename ) != 'thumbnail' ) {
									print( '<a href="' . $upload_array['baseurl'] . '/files/guest/' . basename( $filename ) . '" target="_blank"/>' . basename( $filename ) . '</a>' );
									print( '<br/>' );
								}
							}
						}
					}
					?>
				</td>
			</tr>
		</table>
	</form>
	<?php
}


function jqhfu_enqueue_scripts() {
	$stylepath  = JQHFUPLUGINDIRURL . 'css/';
	$scriptpath = JQHFUPLUGINDIRURL . 'js/';

	wp_enqueue_style( 'blueimp-gallery-style', $stylepath . 'blueimp-gallery.min.css' );
	wp_enqueue_style( 'jquery.fileupload-style', $stylepath . 'jquery.fileupload.css' );

	if ( ! wp_script_is( 'jquery' ) ) {
		wp_enqueue_script( 'jquery', $scriptpath . 'jquery.min.js', array(), '', false );
	}
	wp_enqueue_script( 'jquery-ui-script', $scriptpath . 'jquery-ui.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jtmpl-script', $scriptpath . 'tmpl.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'load-image-all-script', $scriptpath . 'load-image.all.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'canvas-to-blob-script', $scriptpath . 'canvas-to-blob.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-blueimp-gallery-script', $scriptpath . 'jquery.blueimp-gallery.min.js',
	                   array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-iframe-transport-script', $scriptpath . 'jquery.iframe-transport.js', array( 'jquery' ),
	                   '', true );
	wp_enqueue_script( 'jquery-fileupload-script', $scriptpath . 'jquery.fileupload.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-fileupload-process-script', $scriptpath . 'jquery.fileupload-process.js',
	                   array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-fileupload-image-script', $scriptpath . 'jquery.fileupload-image.js', array( 'jquery' ),
	                   '', true );
	wp_enqueue_script( 'jquery-fileupload-audio-script', $scriptpath . 'jquery.fileupload-audio.js', array( 'jquery' ),
	                   '', true );
	wp_enqueue_script( 'jquery-fileupload-video-script', $scriptpath . 'jquery.fileupload-video.js', array( 'jquery' ),
	                   '', true );
	wp_enqueue_script( 'jquery-fileupload-validate-script', $scriptpath . 'jquery.fileupload-validate.js',
	                   array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-fileupload-ui-script', $scriptpath . 'jquery.fileupload-ui.js', array( 'jquery' ), '',
	                   true );
	wp_enqueue_script( 'jquery-fileupload-jquery-ui-script', $scriptpath . 'jquery.fileupload-jquery-ui.js',
	                   array( 'jquery' ), '', true );
}

require 'UploadHandler.php';

class RecordUploadHandler extends UploadHandler {

	public $qr_url = "";

	public $rec_url = "";

	protected function get_upload_path(
		$file_name = null,
		$version = 'mgx'
	) {
		$upload_array = wp_upload_dir();

		return $upload_array['basedir'] . '/aocrecords/' . $version . date( '/Y/m/' ) . $file_name;
	}

	protected function get_download_url(
		$file_name,
		$version = 'mgx',
		$direct = false
	) {
		$upload_array = wp_upload_dir();

		return $upload_array['baseurl'] . '/aocrecords/' . $version . date( '/Y/m/' ) . $file_name;
	}

	protected function set_file_name(
		$name,
		$uploaded_file,
		$mgx_analyzer,
		$md5
	) {
		// Set $name
		$player_code = 0;
		foreach (
			$mgx_analyzer->players
			as
			$p
		)
		{
			$player_code = $p->owner ? $p->index : $player_code;
		}
		$name_string = 'Ymd_' . $mgx_analyzer->gameInfo->getPlayersString() . '_' . $player_code .  'P_' . substr($md5,0,5);
		$new_name    = date( $name_string, filemtime( $uploaded_file ) ) . '.';

		return [
			$new_name . strtolower( pathinfo( $name, PATHINFO_EXTENSION ) ),
			$new_name . 'png',
			$player_code . 'P_md5'
		];
	}

	protected function handle_file_upload(
		$uploaded_file,
		$name,
		$size,
		$type,
		$error,
		$index = null,
		$content_range = null
	) {
		if ( $type == 'application/octet-stream' ) {

			// Generate identity for record file (md5) and identity for host game (each game may consists 2 - 8 record files)
			$file_obj = new stdClass();
			// Analyze record file
			// Load Analyzer
			spl_autoload_register( function ( $class ) {
				if ( substr( $class, 0, 11 ) === 'RecAnalyst\\' ) {
					$f = dirname( __FILE__ ) . '/' . 'mgxparser/' . str_replace( 'RecAnalyst\\', '', $class ) . '.php';
					if ( file_exists( $f ) ) {
						include( $f );
					}
				}
			} );

			$r = new \RecAnalyst\RecAnalyst();
			$r->load( $name, fopen( $uploaded_file, 'r' ) );
			if ( ! $r->analyze() ) {
				return $file_obj->error = $this->get_error_message( 'accept_file_types' );
			}

			$file_md5 = md5_file( $uploaded_file );

			$args = array(
				'post_type'  => array( 'aoc-record' ),
				'meta_query' => array(
					array(
						'key'   => '1P_md5',
						'value' => $file_md5,
					),
				),
			);
			$identical_rec = get_posts($args);
			if (isset($identical_rec[0])) {
				return $file_obj->error = 'Already exists: <a href="' . get_permalink($identical_rec[0]->ID) . '">See existed Record</a>';
			}

			$salt = '';
			foreach (
				$r->players
				as
				$p
			)
			{
				$salt = $salt . $p->name .
				        '#' . $p->index .
				        '#' . $p->civId .
				        '#' . $p->team .
				        '#' . $p->colorId .
				        '#' . $p->feudalTime .
				        '#' . $p->castleTime .
				        '#' . $p->imperialTime .
				        '#' . $p->resignTime . '@@@';
			}
			$host_md5 = md5( $salt );

			$args            = array(
				'post_type'              => array( 'aoc-record' ),
				'meta_query'             => array(
					array(
						'key'     => 'host_md5',
						'value'   => $host_md5,
						'compare' => '=',
					),
				),
				'cache_results'          => true,
				'update_post_meta_cache' => true,
			);
			$host_game_query = get_posts( $args );
			$host_game       = isset( $host_game_query[0] ) ? $host_game_query[0] : false;

			// Don't forget operations after deleting a record
			$record_id = - 1;
			$names = $this->set_file_name( $name, $uploaded_file, $r, $host_md5 );
			$rec_name = $names[0];
			if ( false == $host_game ) {
				// Set filenames
				$map_path = $this->get_upload_path( null, 'map' );
				if ( ! is_dir( $map_path ) ) {
					mkdir( $map_path, $this->options['mkdir_mode'], true );
				}
				imagepng( $r->generateMap(), $map_path . $names[1] );

				$research_path = $this->get_upload_path( null, 'research' );
				if ( ! is_dir( $research_path ) ) {
					mkdir( $research_path, $this->options['mkdir_mode'], true );
				}
				imagepng( $r->generateResearches(), $research_path . $names[1] );

				$qr_path = $this->get_upload_path( null, 'qrcode' );
				if ( ! is_dir( $qr_path ) ) {
					mkdir( $qr_path, $this->options['mkdir_mode'], true );
				}
				require 'phpqrcode/qrlib.php';
				//processing form input
				//remember to sanitize user input in real-life solution !!!
				//available value: L, M, Q, H
				$errorCorrectionLevel = 'Q';
				$matrixPointSize      = 5;
				QRcode::png( get_permalink( $record_id ), $qr_path . $names[1], $errorCorrectionLevel, $matrixPointSize, 2 );

				$r->getOutput()['images'] = array(
					'qrcode' => $this->get_download_url($names[1], 'qrcode'),
					'map' => $this->get_download_url($names[1], 'map'),
					'research' => $this->get_download_url($names[1], 'research')
				);

				// Create a new host game (a de facto custom type post in wordpress)
				// and update $record_id
				$host_game_attr = array(
					'post_title' => $names[0],
					'post_status'  => 'publish',
					'guid'         => $host_md5,
					'post_type'    => 'aoc-record',
					'post_content' => json_encode( $r->getOutput(), JSON_UNESCAPED_UNICODE ),
					'meta_input'   => array(
						$names[2] => $file_md5
					),
				);
				$created_id     = wp_insert_post( $host_game_attr );
				$record_id      = $created_id == 0 ? - 1 : $created_id;
			} else {
				$record_id = $host_game->ID;
				$host_game_attr = array(
					'ID' => $record_id,
					'meta_input'   => array(
						$names[2] => $host_md5,
					),
				);
				wp_insert_post( $host_game_attr );
			}
		}

		$name = isset( $rec_name ) ? $rec_name : $name;

		return parent::handle_file_upload( $uploaded_file, $name, $size, $type, $error, $index, $content_range );
	}
}

function jqhfu_load_ajax_function() {
	global $current_user;
	// get_currentuserinfo();
	$current_user_id = $current_user->ID;
	if ( ! isset( $current_user_id ) || $current_user_id == '' ) {
		$current_user_id = 'guest';
	}
	$upload_handler = new RecordUploadHandler( null, $current_user_id, true, null );

	//echo json_encode([$upload_handler->qr_url, $upload_handler->rec_url]);

	die();
}

function jqhfu_add_inline_script() {
	?>
	<script type="text/javascript">

		jQuery(function () {
			'use strict';

			// Initialize the jQuery File Upload widget:
			jQuery('#fileupload').fileupload({
				url: '<?php print( admin_url( 'admin-ajax.php' ) );?>'
			}); // .fileupload方法，第一个参数是option

			// Enable iframe cross-domain access via redirect option:
			jQuery('#fileupload').fileupload(
				'option',
				'redirect',
				window.location.href.replace(
					/\/[^\/]*$/,
					<?php
					$absoluteurl = str_replace( home_url(), '', JQHFUPLUGINDIRURL );
					print( "'" . $absoluteurl . "cors/result.html?%s'" );
					?>
				)
			);

			if (jQuery('#fileupload')) {
				// Load existing files:
				jQuery('#fileupload').addClass('fileupload-processing');
				// Load existing files:
				jQuery.ajax({
					// Uncomment the following to send cross-domain cookies:
					//xhrFields: {withCredentials: true},
					url: jQuery('#fileupload').fileupload('option', 'url'),
					data: {action: "load_ajax_function"},
					acceptFileTypes: /(\.|\/)(<?php print( get_option( 'jqhfu_accepted_file_types' ) ); ?>)$/i,
					dataType: 'json',
					context: jQuery('#fileupload')[0]
				}).always(function () {
					jQuery(this).removeClass('fileupload-processing');
				}).done(function (result) {
					jQuery(this).fileupload('option', 'done')
						.call(this, jQuery.Event('done'), {result: result});
				});
			}

		});
	</script>
	<?php
}

/* Block of code that need to be printed to the form*/
function jquery_html5_file_upload_hook() {
	?>
	<!-- The file upload form used as target for the file upload widget -->
	<form id="fileupload" action="<?php print( admin_url() . 'admin-ajax.php' ); ?>" method="POST"
	      enctype="multipart/form-data" style="max-width:500px;">
		<!-- Redirect browsers with JavaScript disabled to the origin page -->
		<input type="hidden" name="action" value="load_ajax_function"/>
		<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
		<div class="fileupload-buttonbar">
			<div class="fileupload-buttons">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<label class="jqhfu-file-container">
					Add files...
					<input type="file" name="files[]" multiple class="jqhfu-inputfile">
				</label>
				<button type="submit" class="start jqhfu-button">Start upload</button>
				<button type="reset" class="cancel jqhfu-button">Cancel upload</button>
				<button type="button" class="delete jqhfu-button">Delete</button>
				<input type="checkbox" class="toggle">
				<!-- The global file processing state -->
				<span class="fileupload-process"></span>
			</div>
			<!-- The global progress state -->
			<div class="fileupload-progress jqhfu-fade" style="display:none;max-width:500px;margin-top:2px;">
				<!-- The global progress bar -->
				<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
				<!-- The extended global progress state -->
				<div class="progress-extended">&nbsp;</div>
			</div>
		</div>
		<!-- The table listing the files available for upload/download -->
		<div class="jqhfu-upload-download-table">
			<table role="presentation">
				<tbody class="files"></tbody>
			</table>
		</div>
	</form>

	<!-- The blueimp Gallery widget -->
	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
		<div class="slides"></div>
		<h3 class="title"></h3>
		<a class="prev">‹</a>
		<a class="next">›</a>
		<a class="close">×</a>
		<a class="play-pause"></a>
		<ol class="indicator"></ol>
	</div>
	<!-- The template to display files available for upload -->
	<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload jqhfu-fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name" style="max-width:190px;overflow-x:hidden;">{%=file.name%}</p>
            <strong class="error"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress"></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="start jqhfu-button" disabled>Start</button>
            {% } %}
            {% if (!i) { %}
                <button class="cancel jqhfu-button">Cancel</button>
            {% } %}
        </td>
    </tr>
{% } %}




	</script>
	<!-- The template to display files available for download -->
	<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download jqhfu-fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name" style="max-width:190px;overflow-x:hidden;">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="error">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="delete jqhfu-button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}&action=load_ajax_function"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Delete</button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>
    </tr>
{% } %}




	</script>
	<?php
}

function jquery_file_upload_shortcode() {
	jquery_html5_file_upload_hook();
}


if ( ! function_exists( 'create_record_type' ) ) {

// Register Custom Post Type
	function create_record_type() {

		$labels = array(
			'name'                  => _x( 'AoC Records', 'Post Type General Name', 'mgx-uploader' ),
			'singular_name'         => _x( 'AoC Record', 'Post Type Singular Name', 'mgx-uploader' ),
			'menu_name'             => __( 'Post Types', 'mgx-uploader' ),
			'name_admin_bar'        => __( 'Post Type', 'mgx-uploader' ),
			'archives'              => __( 'Item Archives', 'mgx-uploader' ),
			'parent_item_colon'     => __( 'Parent Item:', 'mgx-uploader' ),
			'all_items'             => __( 'All Items', 'mgx-uploader' ),
			'add_new_item'          => __( 'Add New Item', 'mgx-uploader' ),
			'add_new'               => __( 'Add New', 'mgx-uploader' ),
			'new_item'              => __( 'New Item', 'mgx-uploader' ),
			'edit_item'             => __( 'Edit Item', 'mgx-uploader' ),
			'update_item'           => __( 'Update Item', 'mgx-uploader' ),
			'view_item'             => __( 'View Item', 'mgx-uploader' ),
			'search_items'          => __( 'Search Item', 'mgx-uploader' ),
			'not_found'             => __( 'Not found', 'mgx-uploader' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mgx-uploader' ),
			'featured_image'        => __( 'Featured Image', 'mgx-uploader' ),
			'set_featured_image'    => __( 'Set featured image', 'mgx-uploader' ),
			'remove_featured_image' => __( 'Remove featured image', 'mgx-uploader' ),
			'use_featured_image'    => __( 'Use as featured image', 'mgx-uploader' ),
			'insert_into_item'      => __( 'Insert into item', 'mgx-uploader' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'mgx-uploader' ),
			'items_list'            => __( 'Items list', 'mgx-uploader' ),
			'items_list_navigation' => __( 'Items list navigation', 'mgx-uploader' ),
			'filter_items_list'     => __( 'Filter items list', 'mgx-uploader' ),
		);
		$args   = array(
			'label'               => __( 'AoC Record', 'mgx-uploader' ),
			'description'         => __( 'Information of an AoC Record', 'mgx-uploader' ),
			'labels'              => $labels,
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'author',
				'thumbnail',
				'comments',
				'custom-fields',
			),
			'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => 'aoc-record',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'aoc-record', $args );

	}

	add_action( 'init', 'create_record_type', 0 );

}


/* Add the resources */
add_action( 'wp_enqueue_scripts', 'jqhfu_enqueue_scripts' );

/* Load the inline script */
add_action( 'wp_footer', 'jqhfu_add_inline_script' );

/* Hook on ajax call */
add_action( 'wp_ajax_load_ajax_function',
            'jqhfu_load_ajax_function' ); // https://developer.wordpress.org/reference/hooks/wp_ajax__requestaction/
add_action( 'wp_ajax_nopriv_load_ajax_function',
            'jqhfu_load_ajax_function' ); // https://developer.wordpress.org/reference/hooks/wp_ajax_nopriv__requestaction/

add_shortcode( 'jquery_file_upload', 'jquery_file_upload_shortcode' );