<?php
/*
Plugin Name: NEXT BuddyPress Pack
Plugin URI: {URI where you plan to host your plugin file}
Description: Plugin to add some widgets used in NEXT social networks
Version: 0.3.0
Author: Alberto Souza 
Author URI: albertosouza.net
*/
class BpNextProfileWidget extends WP_Widget {
  function BpNextProfileWidget() {
    parent::WP_Widget( false, $name = 'NEXT Logged ind user Profile' );
  }

  function widget( $args, $instance ) {
    extract( $args );

    echo $before_widget;

    // add more menu items
    bp_next_pack_add_menu_items();

    if ( is_user_logged_in() ) {
      $title = apply_filters('widget_title', $instance['title']);
      if( $title ) echo $before_title . $title . $after_title;
      $this->logedUserBlock( $args, $instance );
    }else {
      echo $before_title;
      _e('Login block','bp-next-pack');
      echo $after_title;
      $this->disconectedUser( $args, $instance );
    }
    echo $after_widget;
  }

  //////////////////////////////////////////////////////
  //Update the widget settings
  /**
   * Update the login widget options
   *
   * @param array $new_instance The new instance options
   * @param array $old_instance The old instance options
   */
  function update( $new_instance, $old_instance ) {
    $instance             = $old_instance;
    $instance['title']    = strip_tags( $new_instance['title'] );
    $instance['register'] = esc_url( $new_instance['register'] );
    $instance['lostpass'] = esc_url( $new_instance['lostpass'] );

    return $instance;
  }
  
  ////////////////////////////////////////////////////
  //Display the widget settings on the widgets admin panel
  function form( $instance ) {

    // Form values
    $title    = !empty( $instance['title'] )    ? esc_attr( $instance['title'] )    : '';
    $register = !empty( $instance['register'] ) ? esc_attr( $instance['register'] ) : '';
    $lostpass = !empty( $instance['lostpass'] ) ? esc_attr( $instance['lostpass'] ) : '';

    ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbpress' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e( 'Register URI:', 'bbpress' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>" type="text" value="<?php echo $register; ?>" /></label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e( 'Lost Password URI:', 'bbpress' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'lostpass' ); ?>" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" type="text" value="<?php echo $lostpass; ?>" /></label>
    </p>

    <?php
  }

  function disconectedUser( $args, $instance ) {
    ?><div class="login-box"><?php
    // get login form
    self::getLoginForm();
    // facebook login buttom
    self::GetFacebookLogin();
    // login nav buttom 
    self::GetLoginNav();
    ?>
      
    </div>
    <?php

  }
  
  function logedUserBlock( $args, $instance ){
    $userdata = wp_get_current_user();
    $user_image_args = array(
      'type'   => 'full' 
    );

    ?>
    <a class="next-user-avatar" href="<?php bp_loggedin_user_link() ?>">
      <?php bp_loggedin_user_avatar( ); ?> <?php print $userdata->display_name ?>
    </a> 
    <span class="activity"> 
      <a href="<?php echo bp_loggedin_user_domain() ?>profile/edit">Edit My Profile</a>    
    </span>
    <div id="user-menu">       

          <?php bp_next_pack_adminbar_account_menu(); ?>

          <?php /* if ( has_nav_menu( 'profile-menu' ) ) : ?>
              <?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'profile-menu', 'items_wrap' => '%3$s' ) ); ?>
          <?php endif; */?>

          <?php do_action( 'bp_member_options_nav' ) ?>


      <ul class="user-loggedin-group-menu">
        <?php if( ! class_exists('BP_Groups_Group') ) {
          _e( 'You must enable Groups component to use this widget.', 'bp-group-hierarchy' );
          return; 
        } ?>
      </ul>
      
      <ul class="user-loggedin-friends-menu">
        
      </ul>
      
    </div><!-- #item-nav -->
    <?php
  }
  
  function getLoginForm(){
    ?>
    <form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">
      <fieldset>
        <legend><?php _e( 'Log In', 'bbpress' ); ?></legend>
        <div class="bbp-username">
          <label for="user_login"><?php _e( 'Username', 'bbpress' ); ?>: </label>
          <input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" tabindex="<?php bbp_tab_index(); ?>" />
        </div>
        <div class="bbp-password">
          <label for="user_pass"><?php _e( 'Password', 'bbpress' ); ?>: </label>
          <input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" tabindex="<?php bbp_tab_index(); ?>" />
        </div>
        <div class="bbp-remember-me">
          <input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ), true, true ); ?> id="rememberme" tabindex="<?php bbp_tab_index(); ?>" />
          <label for="rememberme"><?php _e( 'Remember Me', 'bbpress' ); ?></label>
        </div>
        <div class="bbp-submit-wrapper">
          <?php do_action( 'login_form' ); ?>
          <button type="submit" name="user-submit" id="user-submit" tabindex="<?php bbp_tab_index(); ?>" class="button submit user-submit"><?php _e( 'Log In', 'bbpress' ); ?></button>
          <?php bbp_user_login_fields(); ?>
        </div>
        <?php if ( !empty( $register ) || !empty( $lostpass ) ) : ?>
          <div class="bbp-login-links">
            <?php if ( !empty( $register ) ) : ?>
              <a href="<?php echo esc_url( $register ); ?>" title="<?php _e( 'Register', 'bbpress' ); ?>" class="bbp-register-link"><?php _e( 'Register', 'bbpress' ); ?></a>
            <?php endif; ?>
            <?php if ( !empty( $lostpass ) ) : ?>
              <a href="<?php echo esc_url( $lostpass ); ?>" title="<?php _e( 'Lost Password', 'bbpress' ); ?>" class="bbp-lostpass-link"><?php _e( 'Lost Password', 'bbpress' ); ?></a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </fieldset>
    </form>
    <?php
  }
  
  function getFacebookLogin(){
    global $opt_jfb_hide_button;
      
    if( !get_option($opt_jfb_hide_button) ){
      ?><div class="facebook-login"?>
      <span class="login_or"><?php _e('or'); ?></span> <?php 
      jfb_output_facebook_btn();
      ?></div><?php
    }
    
  }
    
  // get login nav for login block
  function getLoginNav(){
    if ( !$interim_login ) { ?>
      <p id="nav">
      <?php if ( isset($_GET['checkemail']) && in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) : ?>
      <?php elseif ( get_option('users_can_register') ) : ?>
      <a href="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login' ) ); ?>"><?php _e( 'Register' ); ?></a> |
      <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>
      <?php else : ?>
      <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>
      <?php endif; ?>
      </p>
    <?php } 
  }
}


class BpNextUserGroupsWidget extends WP_Widget {
  function BpNextUserGroupsWidget() {
    parent::WP_Widget( false, $name = 'NEXT Logged in user groups' );
  }

  function widget( $args, $instance ) {
    
    if ( is_user_logged_in() ) {
      extract( $args );
      echo $before_widget;
      $title = apply_filters('widget_title', $instance['title']);
      if( $title ) echo $before_title . $title . $after_title;
      $this->logedUserBlock( $args, $instance ); 
      echo $after_widget;
    }
    
  }


  //////////////////////////////////////////////////////
  //Update the widget settings
  function update( $new_instance, $old_instance )
  {
      $instance = $old_instance;
      $instance['title'] = $new_instance['title'];
      return $instance;
  }
  
  ////////////////////////////////////////////////////
  //Display the widget settings on the widgets admin panel
  function form( $instance )
  {
      ?>
      <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title:'; ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
      </p>
      <?php
  }
  
  /**
   * Show logged in use groups block
   * 
   */
  function logedUserBlock( $args, $instance ){
    global $bp;
      
    $user_id = get_current_user_id( );
    $quantidade = 4;
    $query_args = array(
      'type' => 'active',
      'page' => 1,
      'per_page' => $quantidade,
      'max' => NULL,
      'show_hidden' => NULL,
      'user_id' => $user_id,
      'slug' => NULL,
      'search_terms' => NULL,
      'include' => NULL,
      'exclude' => NULL,
      'populate_extras' => 1
    );

    ?>
    <?php if ( bp_has_groups( $query_args ) ) : ?>
    
      <?php do_action( 'bp_before_directory_groups_list' ); ?>
    
      <ul id="groups-list" class="item-list" role="main">
    
      <?php while ( bp_groups() ) : bp_the_group(); ?>

      <li>
        <div class="item-avatar">
          <a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
        </div>
  
        <div class="item">
          <div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
          <div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span></div>
          <?php do_action( 'bp_directory_groups_item' ); ?>
  
        </div>

        <div class="clear"></div>
      </li>
  
    <?php endwhile; ?>
    
    </ul>
    <div class="link-more">
    <a href="<?php print bp_loggedin_user_link() . 'groups/' ; ?>" title="<?php _e("Click here to show the complete group list") ?>"><?php _e("More groups") ?></a>
    </div>
    
    <?php else: ?>
    
      <div id="message" class="info">
        <p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
      </div>
    
    <?php endif;
  }
}



class UserLoggedImgWidget extends WP_Widget {
  function UserLoggedImgWidget() {
    parent::WP_Widget( false, $name = 'Widgets com usuários logados' );
  }

  function widget( $args, $instance ) {
    
	  extract( $args );
	  echo $before_widget;
	  $title = apply_filters('widget_title', $instance['title']);
	  if( $title ) echo $before_title . $title . $after_title;
	  
		$uids = bp_next_pack_get_random_users();
		
		echo '<ul class="header-block">';
			foreach($uids as $indice=>$uid){
				
				echo '<li class="img-0'.$indice.'">';
					echo bp_core_fetch_avatar( array( 'item_id' => $uid, 'type' => 'full' ) ) ;
				echo '</li>';
			}
		echo '</ul>';
	  
	  
	  echo $after_widget;
  }


  //////////////////////////////////////////////////////
  //Update the widget settings
  function update( $new_instance, $old_instance )
  {
      $instance = $old_instance;
      $instance['title'] = $new_instance['title'];
      return $instance;
  }
  
  ////////////////////////////////////////////////////
  //Display the widget settings on the widgets admin panel
  function form( $instance )
  {
      ?>
      <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title:'; ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
      </p>
      <?php
  }
  
  function LoggedUserList () {}
  
}

add_action( 'widgets_init', 'BpNextProfileWidgetInit' );
function BpNextProfileWidgetInit() {
  register_widget( 'BpNextProfileWidget' );
  register_widget( 'BpNextUserGroupsWidget' );
  register_widget( 'UserLoggedImgWidget' );
}

// **** "logged in  Account" Menu ******
function bp_next_pack_adminbar_account_menu() {
  global $bp;

  if ( !$bp->bp_nav || !is_user_logged_in() )
    return false;
  ?>
  <script>
    jQuery(document).ready(function() { 
        jQuery('ul.superfish-menu').superfish({ 
            delay:       300,                            // one second delay on mouseout 
            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
            speed:       'fast',                          // faster animation speed 
            autoArrows:  true,                           // disable generation of arrow mark-up 
            dropShadows: true                            // disable drop shadows 
        }); 
    }); 
  </script>
  <div class="bp-next-pack-user-menu" >
    <ul class="superfish-menu sf-vertical sf-js-enabled sf-shadow">
      
  <?php

  // notification menu  
  bp_adminbar_notifications_menu();
  
  // Loop through each navigation item
  $counter = 0;
  foreach( (array)$bp->bp_nav as $nav_item ) {
    $alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

    if ( -1 == $nav_item['position'] )
      continue;

    echo '<li' . $alt . '>';
    echo '<a id="bp-admin-' . $nav_item['css_id'] . '" href="' . $nav_item['link'] . '">' . $nav_item['name'] . '</a>';

    if ( isset( $bp->bp_options_nav[$nav_item['slug']] ) && is_array( $bp->bp_options_nav[$nav_item['slug']] ) ) {
      echo '<ul>';
      $sub_counter = 0;

      foreach( (array)$bp->bp_options_nav[$nav_item['slug']] as $subnav_item ) {
        $link = $subnav_item['link'];
        $name = $subnav_item['name'];

        if ( isset( $bp->displayed_user->domain ) )
          $link = str_replace( $bp->displayed_user->domain, $bp->loggedin_user->domain, $subnav_item['link'] );

        if ( isset( $bp->displayed_user->userdata->user_login ) )
          $name = str_replace( $bp->displayed_user->userdata->user_login, $bp->loggedin_user->userdata->user_login, $subnav_item['name'] );

        $alt = ( 0 == $sub_counter % 2 ) ? ' class="alt"' : '';
        echo '<li' . $alt . '><a id="bp-admin-' . $subnav_item['css_id'] . '" href="' . $link . '">' . $name . '</a></li>';
        $sub_counter++;
      }
      echo '</ul>';
    }
    echo '</li>';
    $counter++;
  }
  $alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';
  echo '<li' . $alt . '><a id="bp-admin-logout" class="logout" href="' . wp_logout_url( home_url() ) . '">' . __( 'Log Out', 'buddypress' ) . '</a></li>';
  echo '</ul>';
  echo '</div>';  
}

/**
 * Function to add links to buddypress bar
 * @TODO Need add translaction suport
 * return void
 */
function bp_next_pack_add_menu_items(){
  global $bp;
    
  // add goups menu item
  bp_core_new_subnav_item( array(
    'name' => 'Criar grupo',
    'slug' => 'create',
    'parent_slug' => $bp->groups->slug,
    'parent_url' => $bp->loggedin_user->domain . $bp->groups->slug . '/',
    'screen_function' => true,
    'position' => 0
  ) );

  // add events menu item
  bp_core_new_subnav_item( array(
    'name' => 'Criar evento',
    'slug' => 'create',
    'parent_slug' => $bp->events->slug,
    'parent_url' => $bp->loggedin_user->domain . $bp->events->slug . '/',
    'screen_function' => true,
    'position' => 0
  ) );   

  // add sites menu item
  bp_core_new_subnav_item( array(
    'name' => 'Criar blog',
    'slug' => 'create',
    'parent_slug' => $bp->blogs->slug,
    'parent_url' => $bp->loggedin_user->domain . $bp->blogs->root_slug . '/',
    'screen_function' => true,
    'position' => 0
  ) );
  
  // sort the sub menu items
  bp_core_sort_subnav_items();
  
}

// get users with specified roles
function bp_next_pack_get_random_users( ) {
    global $wpdb;
    
    $sql = '
        SELECT distinct  ID, display_name
        FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
        ON          ' . $wpdb->users . '.ID             =       ' . $wpdb->usermeta . '.user_id
    ';

    $sql .= ' ORDER BY RAND() ';
	$sql .= ' LIMIT 4 ';
    $userIDs = $wpdb->get_col( $sql );
    return $userIDs;
}

?>