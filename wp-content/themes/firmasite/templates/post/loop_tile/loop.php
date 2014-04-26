<?php
/**
 * @package firmasite
 */
 global $firmasite_loop_tile, $firmasite_settings, $firmasite_loop_tile_count;
 
$firmasite_loop_tile["i"]++;
if (1 == $firmasite_loop_tile["i"]){
	$firmasite_loop_tile_count++;	
	?>
	<ul id="firmasite_loop_tile<?php echo $firmasite_loop_tile_count; ?>" class="loop_tile list-unstyled row">
	<?php 
} ?>
<li id="post-<?php the_ID(); ?>" <?php post_class("col-xs-12 col-sm-" . round(12 / ($firmasite_settings["loop_tile_row"] -1)) . " col-md-" . round(12 / $firmasite_settings["loop_tile_row"]) . " loop_tile_item loop_tile_" .$firmasite_loop_tile_count. "_item"); ?>>
<div class="">
    <?php if (has_post_thumbnail() && !(isset($firmasite_settings["loop-thumbnail"]) && !empty($firmasite_settings["loop-thumbnail"]))  ) {	
        the_post_thumbnail('thumbnail',array(
            'alt'	=> trim(strip_tags( $post->post_title )),
            'title'	=> trim(strip_tags( $post->post_title )),
            ) ); 
    } ?>					
     <div class="caption well well-sm">
        <h4 class="entry-title"><strong><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', "firmasite" ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></strong></h4>

        <p>
            <?php 	
            if ( !preg_match('/<!--more(.*?)?-->/', $post->post_content) ){
                the_excerpt();
            } else {
                the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', "firmasite" ) );
            }
             ?>		
		</p>

		<?php if (empty($post->post_title)){ ?>
		<a class="pull-right" href="<?php the_permalink(); ?>" rel="bookmark">
			<small><i class="icon-bookmark"></i><?php  _e( 'Permalink', "firmasite" ); ?></small>
		</a>
		<?php } ?>
        <small>
            <?php if(is_object_in_term($post->ID,'category')){ ?>
                <?php the_terms($post->ID,'category', '<span class="icon-folder-open"></span> &nbsp;  ', ', ',''  ); ?>
            <?php } ?> 
        </small>
     </div>
</div>
</li>
<?php
$firmasite_loop_tile["item_left"]--;
if (0 == $firmasite_loop_tile["item_left"]) {
	?>
	<li class="loop-grid-sizer loop_tile_<?php echo $firmasite_loop_tile_count; ?>_grid col-xs-12 col-sm-<?php echo round(12 / ($firmasite_settings["loop_tile_row"] -1)); ?> col-md-<?php echo round(12 / $firmasite_settings["loop_tile_row"]); ?>"></li>
	</ul>
	<?php
	wp_enqueue_script( 'jquery-masonry' );
	echo firmasite_masonry_implement();
	$firmasite_loop_tile = null;
}