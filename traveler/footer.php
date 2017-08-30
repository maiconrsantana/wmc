<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Footer
 *
 * Created by ShineTheme
 *
 */
?>
</div>
<!-- end row -->
</div>
<!--    End #Wrap-->
<?php

    $footer_template = TravelHelper::st_get_template_footer(get_the_ID());
    if($footer_template){
        $vc_content = STTemplate::get_vc_pagecontent($footer_template);
        if ($vc_content){
            echo '<footer id="main-footer" class="container-fluid">';
            echo STTemplate::get_vc_pagecontent($footer_template);
            echo ' </footer>';
        }
    }else
    {
?>
<!--        Default Footer -->
    <footer id="main-footer" class="container-fluid">
        <div class="container text-center">
            <p><?php _e('Copy &copy; 2014 Shinetheme. All Rights Reserved',ST_TEXTDOMAIN)?></p>
        </div>

    </footer>
<?php }?>

<!-- Gotop -->
<?php
    switch (st()->get_option('scroll_style' ,'')) {
        case "tour_box":
            ?>
            <div id="gotop" class="go_top_tour_box" title="<?php _e('Go to top',ST_TEXTDOMAIN)?>">
                <i class="fa fa-angle-double-up"></i><p><?php echo __("TOP", ST_TEXTDOMAIN )  ; ?></p>
            </div>
            <?php
            break;
        default :
            ?>
            <div id="gotop" title="<?php _e('Go to top',ST_TEXTDOMAIN)?>">
                <i class="fa fa-chevron-up"></i>
            </div>
            <?php
            break;
    }
?>

<!-- End Gotop -->
<?php do_action('st_before_footer');?>
<?php wp_footer(); ?>
<?php do_action('st_after_footer');?>

<script type="text/javascript">
var esconderdivs = document.querySelectorAll(".btn-group-select-num");
for(var i = 0; i < esconderdivs.length; i++){ 
esconderdivs[i].classList.add("hidden")
}

var adicionardivs = document.querySelectorAll("#field-rental-room-num");
for(var i = 0; i < adicionardivs.length; i++){ 
adicionardivs[i].classList.remove("hidden")
}

var adicionardivs = document.querySelectorAll("#field-rental-adult");
for(var i = 0; i < adicionardivs.length; i++){ 
adicionardivs[i].classList.remove("hidden")
}
</script>



</body>
</html>