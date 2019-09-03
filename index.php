<?php
/*
Plugin Name: Not Paid WP
Plugin URL: https://github.com/SurfEdge/not-paid-wp
Description: Client did not pay? Add opacity to the body tag and decrease it every day until their site completely fades away.
Version: 1.0
Author: SurfEdge
Author URI: https://github.com/SurfEdge/
Contributors: chamathpali, kleampa
*/

/*
	This plugin is based on the https://github.com/kleampa/not-paid. (@riklomas) and Ciprian (@kleampa) 
*/

function run_not_paid() {
	$options = get_option('not_paid_wp_settings', array() );
	$due = null;
	$deadline = null;

	if(isset($options['not_paid_wp_due_date'])){
		$due = $options['not_paid_wp_due_date'] ;
	}
	if(isset($options['not_paid_wp_deadline'])){
		$deadline = $options['not_paid_wp_deadline'] ;
	}

	if($due && $deadline){ 
	?>
	<script type="text/javascript">
	
		(function(){
			/* change these variables as you wish */
			var due_date = new Date('<?php echo esc_attr( $due ); ?>');
			var days_deadline = <?php echo esc_attr( $deadline ); ?>;
			/* stop changing here */
			
			var current_date = new Date();
			var utc1 = Date.UTC(due_date.getFullYear(), due_date.getMonth(), due_date.getDate());
			var utc2 = Date.UTC(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
			var days = Math.floor((utc2 - utc1) / (1000 * 60 * 60 * 24));
			
			if(days > 0) {
				var days_late = days_deadline-days;
				var opacity = (days_late*100/days_deadline)/100;
					opacity = (opacity < 0) ? 0 : opacity;
					opacity = (opacity > 1) ? 1 : opacity;
				if(opacity >= 0 && opacity < 1) {
					document.getElementsByTagName("BODY")[0].style.opacity = opacity;
				}
			}
		})();
	</script>
	<?php
		}
	}
	add_action( 'wp_footer', 'run_not_paid' );

add_action( 'admin_menu', 'not_paid_wp_add_admin_menu' );
add_action( 'admin_init', 'not_paid_wp_settings_init' );


function not_paid_wp_add_admin_menu(  ) { 
	add_options_page( 'Not-Paid-WP', 'Not-Paid-WP', 'manage_options', 'not-paid-wp', 'not_paid_wp_options_page' );
}


function not_paid_wp_settings_init(  ) { 

	register_setting( 'pluginPage', 'not_paid_wp_settings' );

	add_settings_section(
		'not_paid_wp_pluginPage_section', 
		__( 'Client did not pay?', 'Not Paid WP' ), 
		'not_paid_wp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'not_paid_wp_due_date', 
		__( 'Due Date (02/25/2019)', 'Not Paid WP' ), 
		'not_paid_wp_due_date_render', 
		'pluginPage', 
		'not_paid_wp_pluginPage_section' 
	);

	add_settings_field( 
		'not_paid_wp_deadline', 
		__( 'Days Deadline - # of days', 'Not Paid WP' ), 
		'not_paid_wp_deadline_render', 
		'pluginPage', 
		'not_paid_wp_pluginPage_section' 
	);
}


function not_paid_wp_due_date_render(  ) { 
	$options = get_option( 'not_paid_wp_settings' );
	?>
	<input type='date' name='not_paid_wp_settings[not_paid_wp_due_date]' value='<?php echo $options['not_paid_wp_due_date']; ?>'>
	<?php

}


function not_paid_wp_deadline_render(  ) { 

	$options = get_option( 'not_paid_wp_settings' );
	?>
	<input type='text' name='not_paid_wp_settings[not_paid_wp_deadline]' value='<?php echo $options['not_paid_wp_deadline']; ?>'>
	<?php

}


function not_paid_wp_settings_section_callback(  ) { 

	echo __( 'Add opacity to the body tag and decrease it every day until their site completely fades away.<br>Set a due date and customize the number of days you offer them until the website is fully vanished.<br>Contribute at <a href="https://github.com/SurfEdge/not-paid-wp">https://github.com/SurfEdge/not-paid-wp</a><br><h4>This will only work if you set the below values!</h4>', 'Not Paid WP' );

}

function not_paid_wp_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Not-Paid-WP</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
