<?php
/**
 * @package wp-runequesters
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

?>

<div class="wrap">

	<div class="icon32 icon32-main"><br></div>

	<h2><?php _e( 'WP RuneQuesters', WPRQ_TEXTDOMAIN ); ?></h2>

	<div id="welcome-panel" class="welcome-panel">

		<div class="welcome-panel-content">

			<h3><?php echo sprintf( __( 'Welcome to %s', WPRQ_TEXTDOMAIN ), __( 'WP RuneQuesters', WPRQ_TEXTDOMAIN ) ); ?></h3>

			<p class="about-description"><?php _e( 'Right now you are in the beginning of your adventures.', WPRQ_TEXTDOMAIN ); ?></p>

			<ul class="actions-list">

				<!--li><a href="" class="action-icon action-add-character"><?php _e( 'Create your character', WPRQ_TEXTDOMAIN ); ?></a></li-->

				<ul class="todo">

					<li>

						<a href="<?php echo admin_url('admin.php?page=' . WPRQ_NAME . '/mycharacters'); ?>">
							
							<div class="todo-icon wprq-characters"></div>

							<div class="todo-content">

								<h4 class="todo-name"><?php _e( 'My characters', WPRQ_TEXTDOMAIN ); ?></h4>

								<?php _e( 'Giving life to an entity', WPRQ_TEXTDOMAIN ); ?>

							</div>

						</a>

					</li>

					<li>

						<a href="<?php echo admin_url('admin.php?page=' . WPRQ_NAME . '/maps'); ?>">

							<div class="todo-icon wprole-world"></div>

							<div class="todo-content">

								<h4 class="todo-name"><?php _e( 'The World', WPRQ_TEXTDOMAIN ); ?></h4>

								<?php _e( 'The interactive maps', WPRQ_TEXTDOMAIN ); ?>

							</div>

						</a>

					</li>

				</ul>
			
			</ul>

		</div>

	</div>

	<br>

</div><!-- END: .wrap -->