<?php
global $submit_title;
global $submit_description;
global $property_price; 
global $property_label; 
global $prop_action_category;
global $prop_action_category_selected;
global $prop_category_selected;
global $property_city;
global $property_area;
global $guestnumber;
global $property_country;
global $property_description;
global $property_admin_area;
global $instant_booking;
?>


<?php if ( is_user_logged_in() ) {    ?>
<form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data" class="add-estate">
        <?php
        if (function_exists('icl_translate') ){
            print do_action( 'wpml_add_language_form_field' );
        }
        ?>
<?php }else{ ?>
<div id="new_post"  class="add-estate">
<?php } ?>
 
<div class="col-md-12">
    <div class="user_dashboard_panel">
    <h4 class="user_dashboard_panel_title"><?php  esc_html_e('Description','wpestate');?></h4>
    <div class="row">   
        <div class="alert alert-danger" id="phase1_err"></div>

        <div class="col-md-12">
            <div class="col-md-3 dashboard_chapter_label">           
                <p>
                   <label for="title"><?php esc_html_e('*Title','wpestate'); ?> </label>
                 </p>
            </div>

            <div class="col-md-6">           
                <p>
                   <label for="title"><?php esc_html_e('*Title (mandatory)','wpestate'); ?> </label>
                   <input type="text" id="title" class="form-control" value="<?php print $submit_title; ?>" size="20" name="wpestate_title" />
                </p>
            </div>
        </div>


        <div class="col-md-12">
            <div class="col-md-3 dashboard_chapter_label">
                <p>
                    <label for="prop_category"><?php esc_html_e('*Category and Listed In/Room Type','wpestate');?></label>
                </p>
            </div>

            <div class="col-md-3">
                <p>
                    <label for="prop_category"><?php esc_html_e('*Category (mandatory)','wpestate');?></label>
                    <?php 
                        $args=array(
                                'class'       => 'select-submit2',
                                'hide_empty'  => false,
                                'selected'    => $prop_category_selected,
                                'name'        => 'prop_category',
                                'id'          => 'prop_category_submit',
                                'orderby'     => 'NAME',
                                'order'       => 'ASC',
                                'show_option_none'   => esc_html__( 'None','wpestate'),
                                'taxonomy'    => 'property_category',
                                'hierarchical'=> true
                            );
                        wp_dropdown_categories( $args ); ?>

                </p>
            </div>



            <div class="col-md-3">
                <p>
                    <label for="prop_action_category"> <?php esc_html_e('*Listed In/Room Type (mandatory)','wpestate'); $prop_action_category;?></label>
                    <?php 
                    $args=array(
                            'class'       => 'select-submit2',
                            'hide_empty'  => false,
                            'selected'    => $prop_action_category_selected,
                            'name'        => 'prop_action_category',
                            'id'          => 'prop_action_category_submit',
                            'orderby'     => 'NAME',
                            'order'       => 'ASC',
                            'show_option_none'   => esc_html__( 'None','wpestate'),
                            'taxonomy'    => 'property_action_category',
                            'hierarchical'=> true
                        );

                       wp_dropdown_categories( $args );  ?>
                </p>       
            </div>

        </div>    




        <div class="col-md-12"> 

            <div class="col-md-3 dashboard_chapter_label">
                <p>
                    <label for="guest_no"><?php esc_html_e('*Guest No','wpestate');?></label>
                </p>
            </div>
            <div class="col-md-3">
                <p>
                    <label for="guest_no"><?php esc_html_e('*Guest No (mandatory)','wpestate');?></label>
                    <select id="guest_no" name="guest_no">
                        <?php 
                        for($i=0; $i<15; $i++) {
                            print '<option value="'.$i.'" ';
                                if ( $guestnumber==$i){
                                    print ' selected="selected" ';
                                }
                            print '>'.$i.'</option>';
                        } ?>
                    </select>    
                </p>
            </div>
        </div>  

        <div class="col-md-12">
            <div class="col-md-3 dashboard_chapter_label">    
               <p>
                   <label for="property_city_front"><?php esc_html_e('*City and Neighborhood','wpestate');?></label>
                </p>
           </div>

            <div class="col-md-3">    
                <p>
                    <?php
                    $show_adv_search_general            =   get_option('wp_estate_wpestate_autocomplete','');
                    $wpestate_internal_search           =   '';
                    if($show_adv_search_general=='no'){
                        $wpestate_internal_search='_autointernal';
                    }
                    ?>
                    <label for="property_city_front"><?php esc_html_e('*City (mandatory)','wpestate');?></label>
                    <input type="text"   id="property_city_front<?php echo $wpestate_internal_search;?>" name="property_city_front" placeholder="<?php esc_html_e('Type the city name','wpestate');?>" value="<?php echo $property_city;?>" class="advanced_select  form-control">
                    <?php  if($show_adv_search_general!='no'){ ?>
                    <input type="hidden" id="property_country" name="property_country" value="<?php echo $property_country;?>">
                    <?php } ?>
                    <input type="hidden" id="property_city" name="property_city"  value="<?php echo $property_city;?>" >
                    <input type="hidden" id="property_admin_area" name="property_admin_area" value="<?php echo $property_admin_area;?>">
                </p>
            </div>

            <div class="col-md-3"> 
                <label for="property_city_front"><?php esc_html_e('Neighborhood','wpestate');?></label>
                <input type="text"   id="property_area_front" name="property_area_front" placeholder="<?php esc_html_e('Type the neighborhood name','wpestate');?>" value="<?php echo $property_area;?>" class="advanced_select  form-control">        
            </div>
        </div>    



        <?php  if($show_adv_search_general=='no'){
        ?>  
        <div class="col-md-12"> 
            <div class="col-md-3 dashboard_chapter_label"> 
                <label for="property_country"><?php esc_html_e('Country','wpestate');?></label>
            </div>
            <div class="col-md-3 property_country"> 
                <label for="property_country"><?php esc_html_e('Country','wpestate');?></label>
                <?php print wpestate_country_list( '' ); ?>
            </div>
        </div>
        <?php    
        }?>

        <div class="col-md-12">
            <div class="col-md-3 dashboard_chapter_label"> 
                <label for="property_description"><?php esc_html_e('Property Description','wpestate');?></label>
            </div>

            <div class="col-md-6"> 
                <label for="property_description"><?php esc_html_e('Property Description','wpestate');?></label>
                <textarea  rows="4" id="property_description" name="property_description"  class="advanced_select  form-control" placeholder="<?php esc_html_e('Describe your property','wpestate');?>"><?php echo $property_description; ?></textarea>
            </div>
        </div>

        <div class="col-md-12"> 
            <input style="float:left;" type="checkbox" class="form-control" value="1"  id="instant_booking" name="instant_booking" <?php print $instant_booking; ?> >
            <label style="display: inline;" for="instant_booking"><?php esc_html_e('Allow instant booking? If checked, you will not have the option to reject a booking request.','wpestate');?></label>
        </div>

            <?php
            if ( !is_user_logged_in() ) { 
                print '<input type="hidden" name="pointblank" value="1">';  
            }else{
                print '<input type="hidden" name="pointblank" value="0">';   
            }
        ?>
    </div>
    <div class="col-md-12" style="display: inline-block;">   
        <?php if ( is_user_logged_in() ) {    ?>
            <input type="submit"  class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button"  disabled  id="form_submit_1" value="<?php esc_html_e('Continue', 'wpestate') ?>" />
        <?php }else{ ?>
            <input type="submit"  class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button externalsubmit"  disabled  id="form_submit_1" value="<?php esc_html_e('Continue', 'wpestate') ?>" />

        <?php } ?>
    </div>


    </div>
</div>

    <input type="hidden" id="security-login-submit" name="security-login-submit" value="<?php echo estate_create_onetime_nonce( 'submit_front_ajax_nonce' );?>">
 
        
<?php 

print ' <input type="hidden" name="estatenonce" value="'.sh_create_onetime_nonce( 'thisestate' ).'"/>';

wp_nonce_field('submit_new_estate','new_estate'); 

function sh_create_onetime_nonce($action = -1) {
    $time = time();
    $nonce = wp_create_nonce($time.$action);
    return $nonce . '-' . $time;
}

?>
    
<?php if ( is_user_logged_in() ) {    ?>
</form>  
<?php }else{ 
    echo '<span class="next_submit_page_first_step">'.esc_html__('You must Login / Register in the modal form that shows after you press the Continue button or else your data will be lost. ','wpestate').'</span>';?>
</div>    
<?php } ?>