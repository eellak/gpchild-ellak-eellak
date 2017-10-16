<?php

/*
 Template Name: Associates Template
 */
//global $wpdb;

get_header(); ?>

	<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?> itemprop="mainContentOfPage" role="main">
			<?php

$args=array(
		'post_type' => 'synergates_post_type',
		'meta_key' => 'associate_order',
		'orderby' => 'meta_value_num',
		'order' => 'ASC');

$synergates_query=new WP_Query($args);
?>

<div class="container associates-ext-container">
	
	<div class="row page-title">
		<div class="col-xs-12">
			<?php echo get_the_title(get_the_ID()); ?>
		</div>
	</div>

		
<?php
if($synergates_query->have_posts()):
	while($synergates_query->have_posts()):
	$synergates_query->the_post();
?>
	<div class="row associate-row">
		<div class='col-md-3 col-xs-12 associate-photo'>
			<div class="photo-container">
				<div class="photo-frame thumbnail">
					<div class="centered" style="background-image: url('<?php the_field('associate_photo')?>')">
						<!--<img src="<?php // the_field('associate_photo') ?>">-->
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-9 col-xs-12 associate-content">
			<div class="associate-name">
			<?php the_title() ?>
			</div>
			
			<div class="associate-job-title">
			<?php the_field("associate_job_title") ?>
			</div>
			
			<div class="associate-bio">
			<?php the_content() ?>
			</div>
		</div>
	</div>
<?php
	endwhile;
endif;
?>
		
</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action('generate_sidebars');
get_footer();