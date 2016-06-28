<?php
/*
 * Plugin Name: Custom Post Type Extended Widget
 * Version: 1.0
 * Plugin URI: http://wordpress.org
 * Description: This will list and link Custom Post Type by Category inside a widget.
 * Author: Carl Alberto
 * Author URI: http://carlalberto.ml/
 * Requires at least: 4.0
 * Tested up to: 4.5
 *
 * Text Domain: custom-post-type-extended-widget
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Carl Alberto
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'widgets_init', 'cpte_register_widget' );

function cpte_register_widget() {

	register_widget( 'cpte_widget' );

}

class cpte_widget extends WP_Widget {

	public function __construct() {

		parent::__construct(
			'cpte_widget',
			__( 'List Post by Category', 'custom-post-type-extended-widget' ),
			array(
				'classname'   => 'cpte_widget widget_recent_entries',
				'description' => __( 'This will list post by ', 'custom-post-type-extended-widget' )
			)
		);

	}

	function form( $instance ) {
		$defaults  = array( 'title' => '', 'category' => '', 'number' => 5, 'show_date' => '' );
		$instance  = wp_parse_args( ( array ) $instance, $defaults );
		$title     = $instance['title'];
		$category  = $instance['category'];
		$number    = $instance['number'];
		?>

		<p>
			<label for="cpte_widget_title"><?php _e( 'Title' ); ?>:</label>
			<input type="text" class="widefat" id="cpte_widget_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="cpte_widget_category"><?php _e( 'Category' ); ?>:</label>
			<?php
				wp_dropdown_categories(
					array(
						'orderby'    => 'title',
						'hide_empty' => false,
						'name'       => $this->get_field_name(
							'category' ),
								'id'         => 'cpte_widget_category',
								'class'      => 'widefat',
								'selected'   => $category,
					)
				);
			?>
		</p>

		<p>
			<label for="cpte_widget_number"><?php _e( 'Number of posts to show' ); ?>: </label>
			<input type="text" id="cpte_widget_number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<?php

	}

	function widget( $args, $instance ) {

		extract( $args );
		echo $before_widget;
		$title     = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$category  = $instance['category'];
		$number    = $instance['number'];
		if ( !empty( $title ) ) echo $before_title . $title . $after_title;
		$cat_recent_posts = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => $number,
				'cat'            => $category
			)
		);

		if ( $cat_recent_posts->have_posts() ) {
			echo '<ul>';
				while ( $cat_recent_posts->have_posts() ) {
					$cat_recent_posts->the_post();
					echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
				}
			echo '</ul>';
		} else {
			_e( 'No posts on that category.', 'custom-post-type-extended-widget' );
		}
		wp_reset_postdata();

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {

		$instance              = $old_instance;
		$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
		$instance['category']  = wp_strip_all_tags( $new_instance['category'] );
		$instance['number']    = is_numeric( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;

		return $instance;
	}

}

?>