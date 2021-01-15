                    <div class="col-md-8 col-md-offset-2">

                        <div id="tumblr_content" style="">
                            <a href="<?php echo $this->config->base_url(); ?>">&laquo; try another</a>
                            <h1><?php echo $response->blog->title ?></h1>
                            <h2><?php echo $response->blog->description ?></h2>
                            <p>This <strong>tumblr</strong> was last updated <span class="last-updated"><?php echo date('F jS Y @ g:ia',$response->blog->updated) ?></span> and has <span class="num-posts"><?php echo $response->blog->posts ?></span> posts.<p>

                            <div class="posts_container">
<?php echo $this->pagination->create_links(); ?>
                                <div class="posts row">
<?php 
$i = 0;
foreach ($response->posts as $post): 
    if ($i && $i%3 == 0):
?>
    </div><div class="row">
<?php
    endif;
?>
<div class="col-md-4">
<h2><a href="<?php echo $post->post_url?>"><?php echo ( isset($post->caption) ) ? $post->caption : "<em>untitled</em>" ?></a></h2>
<p class="date"><?php echo date('F jS Y @ g:ia',$post->timestamp) ?></p>
</div>
<?php 
$i++;    
endforeach; 
?>
                                </div>
<?php echo $this->pagination->create_links(); ?>
                            </div> 
                        </div>


