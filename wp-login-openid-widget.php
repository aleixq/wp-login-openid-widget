<?php
/*
Plugin Name: WP login openid widget
Plugin URI: http://www.communia.org
Description: Simple plugin to get widget with login form , openid enabled.
Author: kinta
Version: 1.0
Author URI: http://www.communia.org/
*/
/* a custom action hook */
add_action('wp_login_openid_widget_openid_hook', 'wp_login_openid_widget_openid_callback');
/*
 * 2. Declare the callback function. 
 *    Note that there is no return value.
 */
function wp_login_openid_widget_openid_callback()
{
;
}

/*
*  Crea formulari d'afegir openid
*
*/
function wp_login_openid_widget_openid_form(){
?>
<form method="post" action="wp-admin/users.php?page=your_openids" id="wp_login_openid_widget-add-openid">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="openid_identifier"><?php _e('Add OpenID', 'openid') ?></label></th>
                <td><input id="openid_identifier" name="openid_identifier" /></td>
            </tr>
        </table>
        <?php wp_nonce_field('openid-add_openid'); ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Add OpenID', 'openid') ?>" />
            <input type="hidden" name="action" value="add" >
        </p>
        </form>

<?
}

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'wp_login_openid_widget_load_widgets' );

/**
 * Register our widget.
 * 'wp_login_openid_widget' is the widget class used below.
 *
 * @since 0.1
 */
function wp_login_openid_widget_load_widgets() {
	register_widget( 'wp_login_openid_widget' );
}
/**
 * Pologin Widget Class
 */
class wp_login_openid_widget extends WP_Widget {
 
 
    /** constructor -- name this the same as the class above */
    function wp_login_openid_widget() {
        parent::WP_Widget(false, $name = 'Login openid Widget');	
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $message 	= $instance['message'];
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
							<?php echo $message; ?>
							

        <?
	if ( ! is_user_logged_in() ) { // Display WordPress login form:
	    $args = array(
	//        'redirect' => admin_url(), 
	//        'redirect' => '',
		'form_id' => 'wp_login_openid_widgetform',
		'label_username' => __( 'User' ),
		'label_password' => __( 'Password' ),
		'label_remember' => __( 'Remember me' ),
		'label_log_in' => __( 'Login' ),
		'remember' => false
	    );
	    wp_login_form( $args );
	?>
	    <a href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password">Lost Password</a>
	<?
	} else { // If logged in:

	    $user = wp_get_current_user();
	    if (count(get_user_openids($user->ID))==0){
	    do_action('wp_login_openid_widget_openid_hook');
	    wp_login_openid_widget_openid_form();
	}
	    $user = wp_get_current_user();
	    echo "Has entrat com a ".$user->display_name.'<br/>';
	    wp_loginout( home_url() ); // Display "Log Out" link.
	    /*
	    echo " | ";
	    wp_register('', ''); // Display "Site Admin" link.
	    */
	} 
	?>
              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
 
        $title 		= esc_attr($instance['title']);
        $message	= esc_attr($instance['message']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('Simple Message'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo $message; ?>" />
        </p>
        <?php 
    }
 
 
} // end class wp_login_openid_widget
add_action('widgets_init', create_function('', 'return register_widget("wp_login_openid_widget");'));
?>
