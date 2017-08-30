<?php


////////////////////////////////////////////////////////////////////////////////
/// Ajax  add booking  function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_add_allinone_custom', 'wpestate_ajax_add_allinone_custom' );  
if( !function_exists('wpestate_ajax_add_allinone_custom') ):
    function wpestate_ajax_add_allinone_custom(){
  
      //  check_ajax_referer( 'booking_ajax_nonce','security');
        $current_user = wp_get_current_user();
        $allowded_html      =   array();
        $userID             =   $current_user->ID;
        $from               =   $current_user->user_login;
        
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }
        
        $property_id        =   intval( $_POST['listing_id'] );
        $the_post= get_post( $property_id); 
 
        if( $current_user->ID != $the_post->post_author ) {
            exit('you don\'t have the right to see this');
        }

        
        $new_custom_price   =   '';
        if( isset($_POST['new_price']) ){
            $new_custom_price            = floatval ( $_POST['new_price'] ) ;
        }
     
        $fromdate           =   wp_kses ( $_POST['book_from'], $allowded_html );
        $to_date            =   wp_kses ( $_POST['book_to'], $allowded_html );
      
        ////////////////// 
        $period_min_days_booking                =   intval( $_POST['period_min_days_booking'] );
        $period_extra_price_per_guest           =   intval( $_POST['period_extra_price_per_guest'] );
        $period_price_per_weekeend              =   intval( $_POST['period_price_per_weekeend'] );
        $period_checkin_change_over             =   intval( $_POST['period_checkin_change_over'] );
        $period_checkin_checkout_change_over    =   intval( $_POST['period_checkin_checkout_change_over'] );
        
        
        if($new_custom_price==0 && $period_min_days_booking==1 && $period_extra_price_per_guest==0 && $period_price_per_weekeend==0 
            && $period_checkin_change_over ==0 && $period_checkin_checkout_change_over==0 ){
            print'blank';
            return;
        }
        
        
             
        $mega_details_temp_array=array();
        $mega_details_temp_array['period_min_days_booking']             =   $period_min_days_booking;
        $mega_details_temp_array['period_extra_price_per_guest']        =   $period_extra_price_per_guest;
        $mega_details_temp_array['period_price_per_weekeend']           =   $period_price_per_weekeend;
        $mega_details_temp_array['period_checkin_change_over']          =   $period_checkin_change_over;
        $mega_details_temp_array['period_checkin_checkout_change_over'] =   $period_checkin_checkout_change_over;
           
                
                
        // build the price array 
        //print 'mem1 '.memory_get_usage ();
      
        $price_array=  wpml_custom_price_adjust($property_id);
        if(empty($price_array)){
            $price_array=array();
        }
        
        
        $mega_details_array = wpml_mega_details_adjust($property_id);
        
        if( !is_array($mega_details_array)){
            $mega_details_array=array();
        }
        
     
        ///////////////////////////////////////////////////
        
        $from_date      =   new DateTime($fromdate);
        $from_date_unix =   $from_date->getTimestamp();
        $to_date        =   new DateTime($to_date);
        $to_date_unix   =   $to_date->getTimestamp();
        
        if($new_custom_price!=0 && $new_custom_price!=''){
            $price_array[$from_date_unix]           =   $new_custom_price;
        }
        
        $mega_details_array[$from_date_unix]    =   $mega_details_temp_array;
        
   
        
            $from_date->modify('tomorrow');
            $from_date_unix =   $from_date->getTimestamp();
                
            while ($from_date_unix <= $to_date_unix){
                if($new_custom_price!=0 && $new_custom_price!=''){
                    $price_array[$from_date_unix]           =   $new_custom_price;
                }
               
                $mega_details_array[$from_date_unix]    =   $mega_details_temp_array;
                //print 'memx '.memory_get_usage ().' </br>/';
                $from_date->modify('tomorrow');
                $from_date_unix =   $from_date->getTimestamp();
            }
        
        // clean price options from old data
        $now=time() - 30*24*60*60;
        foreach ($price_array as $key=>$value){
            if( $key < $now ){
                unset( $price_array[$key] );
                unset( $mega_details_array[$key] );
            } 
        }
        
        
        // end clean
        
        update_post_meta($property_id, 'custom_price',$price_array );
        wpml_custom_price_adjust_save($property_id,$price_array);
          
        update_post_meta($property_id, 'mega_details',$mega_details_array );
        wpml_mega_details_adjust_save($property_id,$mega_details_array);
         
        echo wpestate_show_price_custom($new_custom_price);
       
        die();
  } 
endif;






add_action( 'wp_ajax_wpestate_ajax_delete_custom_period', 'wpestate_ajax_delete_custom_period' );  
if( !function_exists('wpestate_ajax_delete_custom_period') ):
    function wpestate_ajax_delete_custom_period(){ 

        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;


        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        
        $allowed_html=array();
        if( !isset($_POST['edit_id'])  || $_POST['edit_id']=='') {
            exit('1');
        }else{
            $edit_id = intval($_POST['edit_id']);
        }
      
        $to_date    =   wp_kses ( $_POST['to_date'],$allowed_html);
        $from_date  =   wp_kses ( $_POST['from_date'],$allowed_html);
        
         
        $the_post= get_post( $edit_id); 
        if( $userID!= $the_post->post_author ) {
            exit('you don\'t have the right to delete this');;
        }
         // build the price array 
    
      
        $price_array        =  wpml_custom_price_adjust($edit_id);
        $mega_details_array =  wpml_mega_details_adjust($edit_id);
         
        if( !is_array($mega_details_array)){
            $mega_details_array=array();
        }

        ///////////////////////////////////////////////////
        
        $from_date      =   new DateTime("@".$from_date);
        $from_date_unix =   $from_date->getTimestamp();
        $to_date        =   new DateTime("@".$to_date);
        $to_date_unix   =   $to_date->getTimestamp();
        
        if($price_array[$from_date_unix]){
            unset($price_array[$from_date_unix]);
        }
        
        if($mega_details_array[$from_date_unix]){
            unset($mega_details_array[$from_date_unix]);
        }
         

        
        $from_date->modify('tomorrow');
        $from_date_unix =   $from_date->getTimestamp();

        while ($from_date_unix <= $to_date_unix){
            
            if($price_array[$from_date_unix]){
                unset($price_array[$from_date_unix]);
            }
            
            if($mega_details_array[$from_date_unix]){
                unset($mega_details_array[$from_date_unix]);
            }
        
        
            $from_date->modify('tomorrow');
            $from_date_unix =   $from_date->getTimestamp();
        }
        //print_r($price_array);
        update_post_meta($edit_id, 'custom_price',$price_array );
        wpml_custom_price_adjust_save($edit_id,$price_array);
        
        update_post_meta($edit_id, 'mega_details',$mega_details_array );
        wpml_mega_details_adjust_save($edit_id,$mega_details_array);
        
        
        
        print 'deleted';
        die();
    }
endif;    




add_action( 'wp_ajax_nopriv_wpestate_ajax_front_end_submit', 'wpestate_ajax_front_end_submit' );  
add_action( 'wp_ajax_wpestate_ajax_front_end_submit', 'wpestate_ajax_front_end_submit' );  
if( !function_exists('wpestate_ajax_front_end_submit') ):
    function wpestate_ajax_front_end_submit(){ 
        $allowed_html                   =   array();
        if( !isset($_POST['title'])  || $_POST['title']=='') {
            exit('1');
        }
    
        if( !isset($_POST['prop_category'])  || $_POST['prop_category']=='') {
            exit('2');
        }
    
        if( !isset($_POST['prop_action_category'])  || $_POST['prop_action_category']=='') {
            exit('3');
        }
        
        if( !isset($_POST['property_city'])  || $_POST['property_city']=='') {
            exit('4');
        }

        if( !isset($_POST['guest_no'])  || $_POST['guest_no']=='') {
            exit('5');
        }
    
        if ( !isset($_POST['new_estate']) || !wp_verify_nonce($_POST['new_estate'],'submit_new_estate') ){
            exit('6'); 
        }
   
    
    $paid_submission_status    = esc_html ( get_option('wp_estate_paid_submission','') );
    if ( $paid_submission_status!='membership' || ( $paid_submission_status== 'membership' || wpestate_get_current_user_listings($userID) > 0)  ){ // if user can submit        
        /*if ( !isset($_POST['new_estate']) || !wp_verify_nonce($_POST['new_estate'],'submit_new_estate') ){
           exit('Sorry, your not submiting from site'); 
        }*/
        
        if( !estate_verify_onetime_nonce_login($_POST['security'], 'submit_front_ajax_nonce') ){
            exit('Sorry, your not submiting from site or you have too many attempts'); 
        }
        
   
        if( !isset($_POST['prop_category']) ) {
            $prop_category  = 0;           
        }else{
            $prop_category  =   intval($_POST['prop_category']);
        }
  
        if( !isset($_POST['prop_action_category']) ) {
            $prop_action_category   =   0;           
        }else{
            $prop_action_category  =   wp_kses($_POST['prop_action_category'],$allowed_html);
        }
        
        if( !isset($_POST['property_city']) ) {
            $property_city  =   '';           
        }else{
            $property_city  =   wp_kses($_POST['property_city'],$allowed_html);
        }
        
        if( !isset($_POST['property_area_front']) ) {
            $property_area  =   '';           
        }else{
            $property_area  =   wp_kses($_POST['property_area_front'],$allowed_html);
        }
        
        
        if( !isset($_POST['property_country']) ) {
            $property_country   =   '';           
        }else{
            $property_country  =   wp_kses($_POST['property_country'],$allowed_html);
        }
        
        if( !isset($_POST['property_description']) ) {
            $property_description   =   '';           
        }else{
            $property_description  =   wp_kses($_POST['property_description'],$allowed_html);
        }
      
        $show_err                       =   '';
        $post_id                        =   '';
        $submit_title                   =   wp_kses( $_POST['title'],$allowed_html ); 
        $guest_no                       =   intval( $_POST['guest_no']);
        $has_errors                     =   false;
        $errors                         =   array();
        
        
        if($submit_title==''){
            $has_errors=true;
            $errors[]=esc_html__( 'Please submit a title for your listing','wpestate');
        }
        
        if($prop_category=='' || $prop_category=='-1'){
            $has_errors=true;
            $errors[]=esc_html__( 'Please submit a category for your property','wpestate');
        }
        
        
        if($prop_action_category=='' || $prop_action_category=='-1'){
            $has_errors=true;
            $errors[]=esc_html__( 'Please chose a room type for your listing','wpestate');
        }
       
        
        if($has_errors){
            foreach($errors as $key=>$value){
                $show_err.=$value.'</br>';
            }            
        }else{
            $paid_submission_status = esc_html ( get_option('wp_estate_paid_submission','') );
            $new_status             = 'pending';
            
            $admin_submission_status= esc_html ( get_option('wp_estate_admin_submission','') );
            if($admin_submission_status=='no' && $paid_submission_status!='per listing'){
               $new_status='publish';  
            }
            
            
          
            $new_user_id=0;
           
          
            $post = array(
                'post_title'	=> $submit_title,
                'post_status'	=> $new_status, 
                'post_type'     => 'estate_property' ,
                'post_author'   => $new_user_id ,
                'post_content'  => $property_description
            );
            $post_id =  wp_insert_post($post );  
            
          
       
        }
        
        if($post_id) {
            $prop_category                  =   get_term( $prop_category, 'property_category');
            if(isset($prop_category->term_id)){
                $prop_category_selected         =   $prop_category->term_id;
            }

            $prop_action_category           =   get_term( $prop_action_category, 'property_action_category');  
            if(isset($prop_action_category->term_id)){
                 $prop_action_category_selected  =   $prop_action_category->term_id;
            }
            
            $api_prop_category_name =   '';
            if( isset($prop_category->name) ){
                $api_prop_category_name=$prop_category->name;
                wp_set_object_terms($post_id,$prop_category->name,'property_category'); 
            }  
            
            $api_prop_action_category_name  = '';
            if ( isset ($prop_action_category->name) ){
                $api_prop_action_category_name  =   $prop_action_category->name;
                wp_set_object_terms($post_id,$prop_action_category->name,'property_action_category'); 
            }  
            if( isset($property_city) && $property_city!='none' ){
                wp_set_object_terms($post_id,$property_city,'property_city'); 
            }  
            
           
            if( isset($property_area) && $property_area!='none' ){
                $property_area= wpestate_double_tax_cover($property_area,$property_city,$post_id);
               // wp_set_object_terms($post_id,$property_area,'property_area'); 
            }  
  
          
            if( isset($property_area) && $property_area!='none' && $property_area!=''){
                $property_area_obj=   get_term_by('name', $property_area, 'property_area'); 
           
                    $t_id = $property_area_obj->term_id ;
                    $term_meta = get_option( "taxonomy_$t_id");
                    $allowed_html   =   array();
                    $term_meta['cityparent'] =  wp_kses( $property_city,$allowed_html);
//                    $term_meta['pagetax'] = '';
//                    $term_meta['category_featured_image '] = '';
//                    $term_meta['category_tagline'] = '';
//                    $term_meta['category_attach_id'] = '';

                    //save the option array
                     update_option( "taxonomy_$t_id", $term_meta );
               
            }
            
            
      
            update_post_meta($post_id, 'prop_featured', 0);
            update_post_meta($post_id, 'guest_no', $guest_no);
            update_post_meta($post_id,'instant_booking',intval($_POST['instant_booking']));
            update_post_meta($post_id, 'property_country', $property_country);            
            update_post_meta($post_id, 'pay_status', 'not paid');
            update_post_meta($post_id, 'page_custom_zoom', 16);
            $sidebar =  get_option( 'wp_estate_blog_sidebar', true); 
            update_post_meta($post_id, 'sidebar_option', $sidebar);
            $sidebar_name   = get_option( 'wp_estate_blog_sidebar_name', true); 
            update_post_meta($post_id, 'sidebar_select', $sidebar_name);
            
            
            
            $property_admin_area    =   '';
            rcapi_create_new_listing($new_user_id,$post_id,$submit_title,$property_description,$new_status,$api_prop_category_name,$api_prop_action_category_name,$property_city,$property_area,$guest_no,$property_admin_area,$property_country,intval($_POST['instant_booking']) );
  
            
            
            
            // get user dashboard link
            $edit_link                       =   wpestate_get_dasboard_edit_listing();
            $edit_link_desc                  =   esc_url_raw ( add_query_arg( 'listing_edit', $post_id, $edit_link) ) ;
            $edit_link_desc                  =   esc_url_raw ( add_query_arg( 'action', 'description', $edit_link_desc) ) ;
            $edit_link_desc                  =   esc_url_raw ( add_query_arg( 'isnew', 1, $edit_link_desc) ) ;
            
            $arguments=array(
                'new_listing_url'   => get_permalink($post_id),
                'new_listing_title' => $submit_title
            );
            wpestate_select_email_type(get_option('admin_email'),'new_listing_submission',$arguments);          
            wp_reset_query();
            print $post_id;
            die();
        }else{
            print 'out';
        }
    }
}
endif;    
    




////////////////////////////////////////////////////////////////////////////////
/// Ajax  add booking  function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_add_custom_price', 'wpestate_ajax_add_custom_price' );  
if( !function_exists('wpestate_ajax_add_custom_price') ):
    function wpestate_ajax_add_custom_price(){
  
      //  check_ajax_referer( 'booking_ajax_nonce','security');
        $current_user = wp_get_current_user();
        $allowded_html      =   array();
        $userID             =   $current_user->ID;
        
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        
        $from               =   $current_user->user_login;
        $new_custom_price   =   '';
   
        
        if( isset($_POST['new_price']) ){
            $new_custom_price            = floatval ( $_POST['new_price'] ) ;
        }
     
     
        $property_id        =   intval( $_POST['listing_id'] );
        
        $the_post= get_post( $property_id); 
 
        if( $current_user->ID != $the_post->post_author ) {
            exit('you don\'t have the right to see this');
        }

        
        
        
        $fromdate           =   wp_kses ( $_POST['book_from'], $allowded_html );
        $to_date            =   wp_kses ( $_POST['book_to'], $allowded_html );
      
        ////////////////// 
        $period_min_days_booking                =   intval( $_POST['period_min_days_booking'] );
        $period_extra_price_per_guest           =   intval( $_POST['period_extra_price_per_guest'] );
        $period_price_per_weekeend              =   intval( $_POST['period_price_per_weekeend'] );
        $period_checkin_change_over             =   intval( $_POST['period_checkin_change_over'] );
        $period_checkin_checkout_change_over    =   intval( $_POST['period_checkin_checkout_change_over'] );
        $period_price_per_month                 =   intval( $_POST['period_price_per_month'] );
        $period_price_per_week                  =   intval( $_POST['period_price_per_week'] );
        
             
        $mega_details_temp_array=array();
        $mega_details_temp_array['period_min_days_booking']             =   $period_min_days_booking;
        $mega_details_temp_array['period_extra_price_per_guest']        =   $period_extra_price_per_guest;
        $mega_details_temp_array['period_price_per_weekeend']           =   $period_price_per_weekeend;
        $mega_details_temp_array['period_checkin_change_over']          =   $period_checkin_change_over;
        $mega_details_temp_array['period_checkin_checkout_change_over'] =   $period_checkin_checkout_change_over;
        $mega_details_temp_array['period_price_per_month']              =   $period_price_per_month;
        $mega_details_temp_array['period_price_per_week']               =   $period_price_per_week;
           
                
                
        // build the price array 
        //print 'mem1 '.memory_get_usage ();
       
        $price_array = wpml_custom_price_adjust($property_id);
        if(empty($price_array)){
            $price_array=array();
        }
        
        
        $mega_details_array = wpml_mega_details_adjust($property_id);
        if( !is_array($mega_details_array)){
            $mega_details_array=array();
        }
        
     
        ///////////////////////////////////////////////////
        
        $from_date      =   new DateTime($fromdate);
        $from_date_unix =   $from_date->getTimestamp();
        $to_date        =   new DateTime($to_date);
        $to_date_unix   =   $to_date->getTimestamp();
        
        if($new_custom_price!=0 && $new_custom_price!=''){
            $price_array[$from_date_unix]           =   $new_custom_price;
        }
        
        $mega_details_array[$from_date_unix]    =   $mega_details_temp_array;
        
   
        
            $from_date->modify('tomorrow');
            $from_date_unix =   $from_date->getTimestamp();
                
            while ($from_date_unix <= $to_date_unix){
                if($new_custom_price!=0 && $new_custom_price!=''){
                    $price_array[$from_date_unix]           =   $new_custom_price;
                }
               
                $mega_details_array[$from_date_unix]    =   $mega_details_temp_array;
                //print 'memx '.memory_get_usage ().' </br>/';
                $from_date->modify('tomorrow');
                $from_date_unix =   $from_date->getTimestamp();
            }
        
        // clean price options from old data
        $now=time() - 30*24*60*60;
        foreach ($price_array as $key=>$value){
            if( $key < $now ){
                unset( $price_array[$key] );
                unset( $mega_details_array[$key] );
            } 
        }
        
        
        // end clean
        
        update_post_meta($property_id, 'custom_price',$price_array );
        wpml_custom_price_adjust_save($property_id,$price_array);
        
        update_post_meta($property_id, 'mega_details',$mega_details_array );
        wpml_mega_details_adjust_save($property_id,$mega_details_array);
        
        
        
        $api_update_details['custom_price']                    =   $price_array;
        $api_update_details['mega_details']                    =   $mega_details_array;

        rcapi_update_listing($property_id,$api_update_details);
         
        echo wpestate_show_price_custom($new_custom_price);
       
        die();
  } 
endif;
////////////////////////////////////////////////////////////////////////////////
/// Ajax  add booking  function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_add_booking', 'wpestate_ajax_add_booking' );  
if( !function_exists('wpestate_ajax_add_booking') ):
    function wpestate_ajax_add_booking(){
      
      //  check_ajax_referer( 'booking_ajax_nonce','security');
        $current_user       =   wp_get_current_user();
        $allowded_html      =   array();
        $userID             =   $current_user->ID;
        
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        
        $from               =   $current_user->user_login;
        $comment            =   '';
        $status             =   'pending';
        
        if( isset($_POST['comment']) ){
            $comment            =    wp_kses ( $_POST['comment'],$allowded_html ) ;
        }
        
        $booking_guest_no    =   0;
        if(isset($_POST['booking_guest_no'])){
            $booking_guest_no    =   intval($_POST['booking_guest_no']);
        }
        
        if ( isset ($_POST['confirmed']) ) {
            if (intval($_POST['confirmed'])==1 ){
                $status    =   'confirmed';
            }
        }
        
     
        
        $property_id        =   intval( $_POST['listing_edit'] );        
        $instant_booking    =   floatval   ( get_post_meta($property_id, 'instant_booking', true) );
        $owner_id           =   wpsestate_get_author($property_id);
        $fromdate           =   wp_kses ( $_POST['fromdate'], $allowded_html );
        $to_date            =   wp_kses ( $_POST['todate'], $allowded_html );
        $event_name         =   esc_html__( 'Booking Request','wpestate');
        $extra_options      =   wp_kses ( $_POST['extra_options'], $allowded_html );
        $post = array(
            'post_title'	=> $event_name,
            'post_content'	=> $comment,
            'post_status'	=> 'publish', 
            'post_type'         => 'wpestate_booking' ,
            'post_author'       => $userID
        );
        $post_id = $bookid=$booking_id= wp_insert_post($post );  
        
        $post = array(
            'ID'                => $post_id,
            'post_title'	=> $event_name.' '.$post_id
        );
        wp_update_post( $post );
       
       
       
        
        update_post_meta($post_id, 'booking_status', $status);
        update_post_meta($post_id, 'booking_id', $property_id);
        update_post_meta($post_id, 'owner_id', $owner_id);
        update_post_meta($post_id, 'booking_from_date', $fromdate);
        update_post_meta($post_id, 'booking_to_date', $to_date);
        update_post_meta($post_id, 'booking_invoice_no', 0);
        update_post_meta($post_id, 'booking_pay_ammount', 0);
        update_post_meta($post_id, 'booking_guests', $booking_guest_no);
        update_post_meta($post_id, 'extra_options', $extra_options);
        
        $security_deposit= get_post_meta(  $property_id,'security_deposit',true);
        update_post_meta($post_id, 'security_deposit', $security_deposit);
   
        $full_pay_invoice_id =0;
        update_post_meta($post_id, 'full_pay_invoice_id', $full_pay_invoice_id);
        
        $to_be_paid =0;
        update_post_meta($post_id, 'to_be_paid', $to_be_paid);
        
        
       
        // build the reservation array 
        $reservation_array = wpestate_get_booking_dates($property_id);      
        update_post_meta($property_id, 'booking_dates', $reservation_array); 
        
        
        if ( $owner_id == $userID ) {
            $subject    =   esc_html__( 'You reserved a period','wpestate');
            $description=   esc_html__( 'You have reserverd a period on your own listing','wpestate');

            $from               =   $current_user->user_login;
            $to                 =   $owner_id;

            $receiver          =   get_userdata($owner_id);
            $receiver_email    =   $receiver->user_email;


            wpestate_add_to_inbox($userID,$from,$to, $subject,$description);
            wpestate_send_booking_email('mynewbook',$receiver_email,$property_id);

            
        }else{
            
            $subject    =   esc_html__( 'New Booking Request from ','wpestate');
            $description=   esc_html__( 'You have received a new booking request','wpestate');
            
            $from               =   $current_user->ID;
            $to                 =   $owner_id;

            $receiver          =   get_userdata($owner_id);
            $receiver_email    =   $receiver->user_email;

            //print " email to ".$receiver_email.' pr id '.$property_id.'/'.$from.'/'.$to;
            //print $userID." / ".$userID."/".$to;
            
            if($instant_booking==1){
                //instant
                wpestate_generate_instant_booking($bookid);
                wpestate_add_to_inbox($userID,$userID,$to, $subject,$description,"external_book_req");
                wpestate_send_booking_email('newbook',$receiver_email,$property_id);
            }else{
                //normal]
                wpestate_add_to_inbox($userID,$userID,$to, $subject,$description,"external_book_req");
                wpestate_send_booking_email('newbook',$receiver_email,$property_id);
            }
        
            
   

        }    
       
        
     

        if($extra_options!=''){ 
            $extra_options_array    =   explode(',',$extra_options);
        }
        
        $booking_array      =   wpestate_booking_price($booking_guest_no,$invoice_id, $property_id, $fromdate, $to_date,$booking_id,$extra_options_array);
        update_post_meta($booking_id, 'custom_price_array',$booking_array['custom_price_array']);
       
        
        $property_author = wpsestate_get_author($property_id);
        
        if( $userID != $property_author){
            $rcapi_listing_id   =   get_post_meta($property_id,'rcapi_listing_id',true);
            $add_booking_details =array(

                "booking_status"            =>  $status,
                "original_property_id"      =>  $property_id,
                "rcapi_listing_id"          =>  $rcapi_listing_id,
                "book_author"               =>  $userID,
                "owner_id"                  =>  $owner_id,
                "booking_from_date"         =>  $fromdate,
                "booking_to_date"           =>  $to_date,
                "booking_invoice_no"        =>  0,
                "booking_pay_ammount"       =>  $booking_array['deposit'],
                "booking_guests"            =>  $booking_guest_no,
                "extra_options"             =>  $extra_options,
                "security_deposit"          =>  $booking_array['security_deposit'],
                "full_pay_invoice_id"       =>  0,
                "to_be_paid"                =>  $booking_array['deposit'],
                "youearned"                 =>  $booking_array['youearned'],
                "service_fee"               =>  $booking_array['service_fee'],
                "booking_taxes"             =>  $booking_array['taxes'],
                "total_price"               =>  $booking_array['total_price'],
                "custom_price_array"        =>  $booking_array['custom_price_array'],
                "submission_curency_status" =>  esc_html( get_option('wp_estate_submission_curency','') ),

            );
            // update on API if is the case
            rcapi_save_booking($booking_id,$add_booking_details);
        }
                 
        die();
  } 
endif;

////////////////////////////////////////////////////////////////////////////////
/// Ajax  add booking  function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_add_booking_instant', 'wpestate_ajax_add_booking_instant' );  
if( !function_exists('wpestate_ajax_add_booking_instant') ):
    function wpestate_ajax_add_booking_instant(){
     
      //  check_ajax_referer( 'booking_ajax_nonce','security');
        $current_user       =   wp_get_current_user();
        $allowded_html      =   array();
        $userID             =   $current_user->ID;
        
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        
        if($userID === 0 ){
            exit('out pls');
        }

        
        $from               =   $current_user->user_login;
        $comment            =   '';
        $status             =   'pending';
        
        if( isset($_POST['comment']) ){
            $comment            =    wp_kses ( $_POST['comment'],$allowded_html ) ;
        }
        
        $booking_guest_no    =   0;
        if(isset($_POST['booking_guest_no'])){
            $booking_guest_no    =   intval($_POST['booking_guest_no']);
        }
        
        if ( isset ($_POST['confirmed']) ) {
            if (intval($_POST['confirmed'])==1 ){
                $status    =   'confirmed';
            }
        }
        
     
        
        $property_id        =   intval( $_POST['listing_edit'] );        
        $instant_booking    =   floatval   ( get_post_meta($property_id, 'instant_booking', true) );
        
        if($instant_booking!=1){
            die();
        }
        
        // PREPARE get property details 
        $invoice_id             =   0;
        $owner_id               =   wpsestate_get_author($property_id);
        $rcapi_listing_id       =   get_post_meta($property_id,'rcapi_listing_id',true);
        $early_bird_percent     =   floatval(get_post_meta($property_id, 'early_bird_percent', true));
        $early_bird_days        =   floatval(get_post_meta($property_id, 'early_bird_days', true));
        $taxes_value            =   floatval(get_post_meta($property_id, 'property_taxes', true));
        $fromdate               =   wp_kses ( $_POST['fromdate'], $allowded_html );
        $to_date                =   wp_kses ( $_POST['todate'], $allowded_html );
        $event_name             =   esc_html__( 'Booking Request','wpestate');
        $security_deposit       =   get_post_meta(  $property_id,'security_deposit',true);
        $full_pay_invoice_id    =   0;
        $to_be_paid             =   0;
        $extra_pay_options      =   get_post_meta($property_id,  'extra_pay_options', true);
        $extra_options          =   wp_kses ( $_POST['extra_options'], $allowded_html );
        $extra_options          =   rtrim($extra_options, ",");
        $price_per_weekeend     =   floatval(get_post_meta($property_id, 'price_per_weekeend', true));
        if($extra_options!=''){ 
            $extra_options_array    =   explode(',',$extra_options);
        }

        
        
        
        
        // STEP1 -make the book
       
        
        $post = array(
            'post_title'	=> $event_name,
            'post_content'	=> $comment,
            'post_status'	=> 'publish', 
            'post_type'         => 'wpestate_booking' ,
            'post_author'       => $userID
        );
        $booking_id= wp_insert_post($post );  
        
        $post = array(
            'ID'                => $booking_id,
            'post_title'	=> $event_name.' '.$booking_id
        );
        wp_update_post( $post );
       
 
        update_post_meta($booking_id, 'booking_status', $status);
        update_post_meta($booking_id, 'booking_id', $property_id);
        update_post_meta($booking_id, 'owner_id', $owner_id);
        update_post_meta($booking_id, 'booking_from_date', $fromdate);
        update_post_meta($booking_id, 'booking_to_date', $to_date);
        update_post_meta($booking_id, 'booking_invoice_no', 0);
        update_post_meta($booking_id, 'booking_pay_ammount', 0);
        update_post_meta($booking_id, 'booking_guests', $booking_guest_no);
        update_post_meta($booking_id, 'extra_options', $extra_options);
        update_post_meta($booking_id, 'security_deposit', $security_deposit);
        update_post_meta($booking_id, 'full_pay_invoice_id', $full_pay_invoice_id);
        update_post_meta($booking_id, 'to_be_paid', $to_be_paid);
        update_post_meta($booking_id, 'early_bird_percent', $early_bird_percent);
        update_post_meta($booking_id, 'early_bird_days', $early_bird_days);
        update_post_meta($booking_id, 'booking_taxes', $taxes_value);
      
        

        // Re build the reservation array 
        $reservation_array  = get_post_meta($property_id, 'booking_dates',true);
        if($reservation_array==''){
            $reservation_array = wpestate_get_booking_dates($property_id);
        }
        update_post_meta($property_id, 'booking_dates', $reservation_array); 
        
        //get booking array
        $booking_array      =   wpestate_booking_price($booking_guest_no,$invoice_id, $property_id, $fromdate, $to_date,$booking_id,$extra_options_array);
        $price              =   $booking_array['total_price'];  
        
        
        // updating the booking detisl 
        update_post_meta($booking_id, 'to_be_paid', $booking_array['deposit']);
        update_post_meta($booking_id, 'booking_taxes', $booking_array['taxes']);
        update_post_meta($booking_id, 'service_fee', $booking_array['service_fee']);
        update_post_meta($booking_id, 'taxes', $booking_array['taxes'] );
        update_post_meta($booking_id, 'service_fee', $booking_array['service_fee']);
        update_post_meta($booking_id, 'youearned', $booking_array['youearned']);
        update_post_meta($booking_id, 'custom_price_array',$booking_array['custom_price_array']);
        update_post_meta($booking_id, 'balance',$booking_array['balance']);
        update_post_meta($booking_id, 'total_price',$booking_array['total_price']);
        
        
        $property_author = wpsestate_get_author($property_id);
        
        if( $userID != $property_author){
            $rcapi_listing_id   =   get_post_meta($property_id,'rcapi_listing_id',true);
            $add_booking_details =array(

                "booking_status"            =>  $status,
                "original_property_id"      =>  $property_id,
                "rcapi_listing_id"          =>  $rcapi_listing_id,
                "book_author"               =>  $userID,
                "owner_id"                  =>  $owner_id,
                "booking_from_date"         =>  $fromdate,
                "booking_to_date"           =>  $to_date,
                "booking_invoice_no"        =>  0,
                "booking_pay_ammount"       =>  $booking_array['deposit'],
                "booking_guests"            =>  $booking_guest_no,
                "extra_options"             =>  $extra_options,
                "security_deposit"          =>  $booking_array['security_deposit'],
                "full_pay_invoice_id"       =>  0,
                "to_be_paid"                =>  $booking_array['deposit'],
                "youearned"                 =>  $booking_array['youearned'],
                "service_fee"               =>  $booking_array['service_fee'],
                "booking_taxes"             =>  $booking_array['taxes'],
                "total_price"               =>  $booking_array['total_price'],
                "custom_price_array"        =>  $booking_array['custom_price_array'],
                "submission_curency_status" =>  esc_html( get_option('wp_estate_submission_curency','') ),
                "balance"                   =>  $booking_array['balance']
            );
            // update on API if is the case

            if($booking_array['balance'] > 0){
                update_post_meta($booking_id, 'booking_status_full','waiting' );
                $add_booking_details['booking_status_full'] =   'waiting';
            }


            rcapi_save_booking($booking_id,$add_booking_details);
        }
        
        //STEP 2 generate the invoice
         
        //check if period already reserverd
        wpestate_check_for_booked_time($fromdate,$to_date,$reservation_array);
        //end check

        
        // fill up the details array to display
        $details[]      =   array(esc_html__('Subtotal','wpestate'),$booking_array['inter_price']);  
        if( is_array($extra_options_array) &&  !empty ( $extra_options_array )  ){
            $extra_pay_options     =      ( get_post_meta($property_id,  'extra_pay_options', true) );
              $options_array_explanations=array(
                                0   =>  esc_html__('Single Fee','wpestate'),
                                1   =>  esc_html__('Per Night','wpestate'),
                                2   =>  esc_html__('Per Guest','wpestate'),
                                3   =>  esc_html__('Per Night per Guest','wpestate')
                            );
            foreach ($extra_options_array as $key=>$value){
                if( isset($extra_pay_options[$value][0]) ){
                    $value_computed     =   wpestate_calculate_extra_options_value($booking_array['count_days'],$booking_guest_no,$extra_pay_options[$value][2],$extra_pay_options[$value][1]);
                   
                    $extra_option_value_show_single     =   wpestate_show_price_booking_for_invoice($extra_pay_options[$value][1],$currency,$where_currency,0,1);

                    $temp_array         =   array($extra_pay_options[$value][0],$value_computed,$extra_option_value_show_single.' '.$options_array_explanations [ $extra_pay_options[$value][2] ] );
                    $details[]          =   $temp_array;
                }
            }
        }
     
       
        //security details
        if( intval($booking_array['security_deposit']) != 0){
            $sec_array= array(__('Security Deposit','wpestate'), $booking_array['security_deposit'] );
            $details[]=$sec_array;
        }
          //earky bird
        if( intval($booking_array['early_bird_discount']) != 0){
            $sec_array= array(__('Early Bird Discount','wpestate'), $booking_array['early_bird_discount'] );
            $details[]=$sec_array;
        }
        
        
        $billing_for    =   esc_html__( 'Reservation fee','wpestate');
        $type           =   esc_html__( 'One Time','wpestate');
        $pack_id        =   $booking_id; // booking id
       
        $time = time(); 
        $date = date('Y-m-d H:i:s',$time); 
        $user_id        =   wpse119881_get_author($booking_id);

        $is_featured    =   '';
        $is_upgrade     =   '';
        $paypal_tax_id  =   '';


        $invoice_id =  wpestate_booking_insert_invoice($billing_for,$type,$pack_id,$date,$user_id,$is_featured,$is_upgrade,$paypal_tax_id,$details,$price,$owner_id);       

        // update booking status
        if( $userID != $property_author){
            update_post_meta($booking_id, 'booking_status', 'waiting');
            update_post_meta($booking_id, 'booking_invoice_no', $invoice_id);
            $booking_details =array(
                    'booking_status'            => 'waiting',
                    'booking_invoice_no'        => $invoice_id
            );
            $rcapi_booking_id = get_post_meta($booking_id,'rcapi_booking_id',true);
            rcapi_edit_booking($booking_id,$rcapi_booking_id,$booking_details);
        }
        
        //update invoice data
        update_post_meta($invoice_id, 'early_bird_percent', $early_bird_percent);
        update_post_meta($invoice_id, 'early_bird_days', $early_bird_days);
        update_post_meta($invoice_id, 'booking_taxes', $taxes_value);
        update_post_meta($invoice_id, 'booking_taxes', $booking_array['taxes']);  
        update_post_meta($invoice_id, 'service_fee', $booking_array['service_fee']);
        update_post_meta($invoice_id, 'youearned', $booking_array['youearned']);
        update_post_meta($invoice_id, 'depozit_to_be_paid', $booking_array['deposit']);
        update_post_meta($invoice_id, 'item_price', $booking_array['total_price']); 
        update_post_meta($invoice_id, 'custom_price_array',$booking_array['custom_price_array']);
        update_post_meta($invoice_id, 'balance',$booking_array['balance']);
          
      
        // send notifications
        $receiver          =   get_userdata($user_id);
        $receiver_email    =   $receiver->user_email;
        $receiver_login    =   $receiver->user_login;
        $from              =   $owner_id;
        $to                =   $user_id;
        $subject           =   esc_html__( 'New Invoice','wpestate');
        $description       =   esc_html__( 'A new invoice was generated for your booking request','wpestate');
        wpestate_add_to_inbox($userID,$userID,$to,$subject,$description,1);
        wpestate_send_booking_email('newinvoice',$receiver_email);
     
        
        
        
        
        
        
        
        
        
        
        
        //STEP3 - show me the money
        
        $currency           =   esc_html( get_post_meta($invoice_id, 'invoice_currency',true) );               
        $where_currency     =   esc_html( get_option('wp_estate_where_currency_symbol', '') );
        $default_price      =   get_post_meta($invoice_id, 'default_price', true);
        
        $booking_from_date  =   esc_html(get_post_meta($booking_id, 'booking_from_date', true));
        $property_id        =   esc_html(get_post_meta($booking_id, 'booking_id', true));
        
        $booking_to_date    =   esc_html(get_post_meta($booking_id, 'booking_to_date', true)); 
        $booking_guests     =   floatval(get_post_meta($booking_id, 'booking_guests', true));
       // $booking_array      =   wpestate_booking_price($booking_guests,$invoice_id,$property_id, $booking_from_date, $booking_to_date);
     

        if($booking_array['numberDays']>7 && $booking_array['numberDays']< 30){
            $default_price=$booking_array['week_price'];
        }else if($booking_array['numberDays']>30){
            $default_price=$booking_array['month_price'];
        }
       
        $wp_estate_book_down            =   get_post_meta($invoice_id, 'invoice_percent', true);
        $wp_estate_book_down_fixed_fee  =   get_post_meta($invoice_id, 'invoice_percent_fixed_fee', true);
        $invoice_price                  =   floatval( get_post_meta($invoice_id, 'item_price', true)) ;
        
        
        $include_expeses    = esc_html ( get_option('wp_estate_include_expenses','') );
      
        if($include_expeses=='yes'){
            $total_price_comp   =   $invoice_price;  
        
        }else{
            $total_price_comp   =   $invoice_price - $booking_array['city_fee'] - $booking_array['cleaning_fee'];  
        
        }
       
        
        $depozit = wpestate_calculate_deposit($wp_estate_book_down,$wp_estate_book_down_fixed_fee,$total_price_comp);
          
       // $depozit            =   round($wp_estate_book_down*$total_price_comp/100,2);
        $balance            =   $invoice_price-$depozit;
        

        $price_show                 =   wpestate_show_price_booking_for_invoice($default_price,$currency,$where_currency,0,1);
        $price_per_weekeend_show    =   wpestate_show_price_booking_for_invoice($price_per_weekeend,$currency,$where_currency,0,1);
        $total_price_show           =   wpestate_show_price_booking_for_invoice($invoice_price,$currency,$where_currency,0,1);
        $depozit_show               =   wpestate_show_price_booking_for_invoice($depozit,$currency,$where_currency,0,1);
        $balance_show               =   wpestate_show_price_booking_for_invoice($balance,$currency,$where_currency,0,1);
        $city_fee_show              =   wpestate_show_price_booking_for_invoice($booking_array['city_fee'],$currency,$where_currency,0,1);
        $cleaning_fee_show          =   wpestate_show_price_booking_for_invoice($booking_array['cleaning_fee'],$currency,$where_currency,0,1);
        $inter_price_show           =   wpestate_show_price_booking_for_invoice($booking_array['inter_price'],$currency,$where_currency,0,1);      
        $total_guest                =   wpestate_show_price_booking_for_invoice($booking_array['total_extra_price_per_guest'],$currency,$where_currency,1,1); 
        $guest_price                =   wpestate_show_price_booking_for_invoice($booking_array['extra_price_per_guest'],$currency,$where_currency,1,1); 
        $extra_price_per_guest      =   wpestate_show_price_booking($booking_array['extra_price_per_guest'],$currency,$where_currency,1);
        
        
        $depozit_stripe     =   $depozit*100;
        $details            =   get_post_meta($invoice_id, 'renting_details', true);

        
      

        // strip details generation
        require_once get_template_directory().'/libs/stripe/lib/Stripe.php';
        $stripe_secret_key              =   esc_html( get_option('wp_estate_stripe_secret_key','') );
        $stripe_publishable_key         =   esc_html( get_option('wp_estate_stripe_publishable_key','') );

        $stripe = array(
          "secret_key"      => $stripe_secret_key,
          "publishable_key" => $stripe_publishable_key
        );

        Stripe::setApiKey($stripe['secret_key']);


        $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'stripecharge.php'
            ));

        if( $pages ){
            $processor_link = esc_url ( get_permalink( $pages[0]->ID) );
        }else{
            $processor_link=esc_html( home_url() );
        }
        
        
         print '              
            <div class="create_invoice_form">
                   <h3>'.esc_html__( 'Invoice INV','wpestate').$invoice_id.'</h3>

                   <div class="invoice_table">
                        <div class="invoice_data">
                                <span class="date_interval"><span class="invoice_data_legend">'.esc_html__( 'Period','wpestate').' : </span>'.$booking_from_date.' '.esc_html__( 'to','wpestate').' '.$booking_to_date.'</span>
                                <span class="date_duration"><span class="invoice_data_legend">'.esc_html__( 'No of nights','wpestate').': </span>'.$booking_array['count_days'].'</span>
                                <span class="date_duration"><span class="invoice_data_legend">'.esc_html__( 'No of guests','wpestate').': </span>'.$booking_guests.'</span>';
                                if($booking_array['price_per_guest_from_one']==1){
                                    print'    
                                    <span class="date_duration"><span class="invoice_data_legend">'.esc_html__( 'Price per Guest','wpestate').': </span>'; 
                                    print $extra_price_per_guest;
                                    print'</span>';
                                }else{
                                    print'    
                                    <span class="date_duration"><span class="invoice_data_legend">'.esc_html__( 'Price per night','wpestate').': </span>';
                                    print $price_show;
                                    if($booking_array['has_custom']){
                                        print ', '.esc_html__('has custom price','wpestate');
                                    }
                                    if($booking_array['cover_weekend']){
                                        print ', '.esc_html__('has weekend price of','wpestate').' '.$price_per_weekeend_show;
                                    }
                                    print'</span>';
                                }
                                if($booking_array['has_custom']){
                                    print '<span class="invoice_data_legend">'.__('Price details:','wpestate').'</span>';
                                    foreach($booking_array['custom_price_array'] as $date=>$price){
                                        $day_price = wpestate_show_price_booking_for_invoice($price,$currency,$where_currency,1,1); 
                                        print '<span class="price_custom_explained">'.__('on','wpestate').' '.date("Y-m-d",$date).' '.__('price is','wpestate').' '.$day_price.'</span>';
                                    }
                                }
                        
                            
                            print '    
                        </div>

                        <div class="invoice_details">
                            <div class="invoice_row header_legend">
                               <span class="inv_legend">'.esc_html__( 'Cost','wpestate').'</span>
                               <span class="inv_data">  '.esc_html__( 'Price','wpestate').'</span>
                               <span class="inv_exp">   '.esc_html__( 'Detail','wpestate').'</span>
                            </div>';
                     
                        print'
                        <div class="invoice_row invoice_content">
                            <span class="inv_legend">   '.esc_html__( 'Subtotal','wpestate').'</span>
                            <span class="inv_data">   '.$inter_price_show.'</span>';
                        
                            if($booking_array['price_per_guest_from_one']==1){
                                print  $extra_price_per_guest.' x '.$booking_array['count_days'].' '.esc_html__( 'nights','wpestate').' x '.$booking_array['curent_guest_no'].' '.esc_html__( 'guests','wpestate');
                            } else{ 
                                if($booking_array['cover_weekend']){
                                    $new_price_to_show= esc_html__('has weekend price of','wpestate').' '.$price_per_weekeend_show;
                                }else{
                                    if($booking_array['has_custom']){
                                        $new_price_to_show=esc_html__("custom price","wpestate");
                                    }else{
                                        $new_price_to_show=$price_show.' '.esc_html__( 'per night','wpestate');
                                    }
                                }
                                                
                                                
                               
                                
                                if($booking_array['numberDays']==1){
                                    print ' <span class="inv_exp">   ('.$booking_array['numberDays'].' '.esc_html__( 'night','wpestate').' | '.$new_price_to_show.') </span>';
                                }else{
                                    print ' <span class="inv_exp">   ('.$booking_array['numberDays'].' '.esc_html__( 'nights','wpestate').' | '.$new_price_to_show.') </span>';
                                }
                            }
                            
                            if($booking_array['custom_period_quest']==1){
                               esc_html_e(" period with custom price per guest","wpestate");
                            }
                            
                            print'</div>';

                           // print_r($booking_array);
                            
                            if($booking_array['has_guest_overload']!=0 && $booking_array['total_extra_price_per_guest']!=0){
                                print'
                                <div class="invoice_row invoice_content">
                                    <span class="inv_legend">   '.esc_html__( 'Extra Guests','wpestate').'</span>
                                    <span class="inv_data" id="extra-guests" data-extra-guests="'.$booking_array['total_extra_price_per_guest'].'">  '.$total_guest.'</span>
                                    <span class="inv_exp">   ('.$booking_array['numberDays'].' '.esc_html__( 'nights','wpestate').' | '.$booking_array['extra_guests'].' '.esc_html__('extra guests','wpestate').' ) </span>
                                </div>';
                                $details[]        =array(esc_html__('Extra Guests'),$booking_array['total_extra_price_per_guest']);  
                            }
                        
                            
                            
                            if($booking_array['cleaning_fee']!=0 && $booking_array['cleaning_fee']!=''){
                               print'
                               <div class="invoice_row invoice_content">
                                   <span class="inv_legend">   '.esc_html__( 'Cleaning fee','wpestate').'</span>
                                   <span class="inv_data" id="cleaning-fee" data-cleaning-fee="'.$booking_array['cleaning_fee'].'">  '.$cleaning_fee_show.'</span>
                               </div>';
                            }
                            $details[]        =array(esc_html__('Cleaning fee','wpestate'),$booking_array['cleaning_fee']); 
  
  
                            if($booking_array['city_fee']!=0 && $booking_array['city_fee']!=''){
                               print'
                               <div class="invoice_row invoice_content">
                                   <span class="inv_legend">   '.esc_html__( 'City fee','wpestate').'</span>
                                   <span class="inv_data" id="city-fee" data-city-fee="'.$booking_array['city_fee'].'">  '.$city_fee_show.'</span>
                               </div>';
                            }
                            $details[]        =array(esc_html__('City fee','wpestate'),$booking_array['city_fee']); 
  
                             update_post_meta($invoice_id, 'renting_details', $details);
                            
                            
                            
                             
                             
                             
                        
                            $options_array_explanations=array(
                                0   =>  esc_html__('Single Fee','wpestate'),
                                1   =>  esc_html__('Per Night','wpestate'),
                                2   =>  esc_html__('Per Guest','wpestate'),
                                3   =>  esc_html__('Per Night per Guest','wpestate')
                            );
//extra_options$extra_options_array
                            foreach ($extra_options_array as $key=>$value){
                                if( isset($extra_pay_options[$value][0]) ){
                                    $extra_option_value                 =   wpestate_calculate_extra_options_value($booking_array['count_days'],$booking_guests,$extra_pay_options[$value][2],$extra_pay_options[$value][1]);
                                    $extra_option_value_show            =   wpestate_show_price_booking_for_invoice($extra_option_value,$currency,$where_currency,1,1);
                                    $extra_option_value_show_single     =   wpestate_show_price_booking_for_invoice($extra_pay_options[$value][1],$currency,$where_currency,0,1);

                                    print'
                                    <div class="invoice_row invoice_content">
                                        <span class="inv_legend">   '.$extra_pay_options[$value][0].'</span>
                                        <span class="inv_data">  '.$extra_option_value_show.'</span>
                                        <span class="inv_data">'.$extra_option_value_show_single.' '.$options_array_explanations[$extra_pay_options[$value][2]].'</span>
                                    </div>';
                                }
                            }
                           
                            
                             if($booking_array['security_deposit']!=0){
                                 $security_depozit_show =   wpestate_show_price_booking_for_invoice($booking_array['security_deposit'],$currency,$where_currency,1,1);
                                print'
                                <div class="invoice_row invoice_content">
                                    <span class="inv_legend">   '.__('Security Deposit','wpestate').'</span>
                                    <span class="inv_data">  '.$security_depozit_show.'</span>
                                    <span class="inv_data">'.__('*refundable','wpestate').'</span>
                                </div>';
                            }

                                
                         
                            if( $booking_array['early_bird_discount'] >0){
                                $early_bird_discount_show =   wpestate_show_price_booking_for_invoice($booking_array['early_bird_discount'],$currency,$where_currency,1,1);
                                print'
                                <div class="invoice_row invoice_content">
                                    <span class="inv_legend">   '.__('Early Bird Discount','wpestate').'</span>
                                    <span class="inv_data">  '.$early_bird_discount_show.'</span>
                                    <span class="inv_data"></span>
                                </div>';
                            }
                            
                            
                            
                            
                        print ' 
                            <div class="invoice_row invoice_total total_inv_span total_invoice_for_payment">
                               <span class="inv_legend"><strong>'.esc_html__( 'Total','wpestate').'</strong></span>
                               <span class="inv_data" id="total_amm" data-total="'.$invoice_price.'">'.$total_price_show.'</span></br>
                               
                               <span class="inv_legend">'.esc_html__( 'Reservation Fee Required','wpestate').':</span> <span class="inv_depozit depozit_show" data-value="'.$depozit.'"> '.$depozit_show.'</span></br>
                               <span class="inv_legend">'.esc_html__( 'Balance Owing','wpestate').':</span> <span class="inv_depozit balance_show"  data-value="'.$balance.'">'.$balance_show.'</span>
                           </div>
                       </div>';
                        
                        
           

                    if( floatval($depozit)==0){
                        print '<span id="confirm_zero_instant_booking"   data-propid="'.$property_id.'" data-bookid="'.$booking_id.'" data-invoiceid="'.$invoice_id.'">'.esc_html__( 'Confirm Booking - Nothing To Pay','wpestate').'</span>';
                     
                    }else{
                        $is_paypal_live= esc_html ( get_option('wp_estate_enable_paypal','') );
                        $is_stripe_live= esc_html ( get_option('wp_estate_enable_stripe','') );
                        $submission_curency_status  =   esc_html( get_option('wp_estate_submission_curency','') );

                        print '<span class="pay_notice_booking">'.esc_html__( 'Pay Deposit & Confirm Reservation','wpestate').'</span>';
                        if ( $is_stripe_live=='yes'){
                            print ' 
                            <form action="'.$processor_link.'" method="post" class="booking_form_stripe">
                                <script src="https://checkout.stripe.com/checkout.js" 
                                class="stripe-button"
                                data-key="'. $stripe['publishable_key'].'"
                                data-amount="'.$depozit_stripe.'" 
                                data-zip-code="true"
                                data-email="'.$user_email.'"
                                data-currency="'.$submission_curency_status.'"
                                data-label="'.esc_html__( 'Pay with Credit Card','wpestate').'"
                                data-description="Reservation Payment">
                                </script>
                                <input type="hidden" name="booking_id" value="'.$booking_id.'">
                                <input type="hidden" name="invoice_id" value="'.$invoice_id.'">
                                <input type="hidden" name="userID" value="'.$userID.'">
                                <input type="hidden" name="depozit" value="'.$depozit_stripe.'">
                            </form>';
                        }
                        if ( $is_paypal_live=='yes'){
                            print '<span id="paypal_booking" data-deposit="'.$depozit.'"  data-propid="'.$property_id.'" data-bookid="'.$booking_id.'" data-invoiceid="'.$invoice_id.'">'.esc_html__( 'Pay with Paypal','wpestate').'</span>';
                        }
                        $enable_direct_pay      =   esc_html ( get_option('wp_estate_enable_direct_pay','') );

                        if ( $enable_direct_pay=='yes'){
                          //  print '<span id="direct_pay_booking" data-propid="'.$property_id.'" data-bookid="'.$booking_id.'" data-invoiceid="'.$invoice_id.'">'.esc_html__( 'Wire Transfer','wpestate').'</span>';
                        }
                    }
                  print'
                  </div>


            </div>';
        
        $rcapi_booking_id = get_post_meta($booking_id,'rcapi_booking_id',true);
        
        
          
        $invoice_details=array(
            "invoice_status"                =>  "issued",
            "purchase_date"                 =>  $date,
            "buyer_id"                      =>  $userID,
            "item_price"                    =>  $booking_array['total_price'], 
            "rcapi_booking_id"              =>  $rcapi_booking_id,
            "orignal_invoice_id"            =>  $invoice_id,
            "billing_for"                   =>  $billing_for,
            "type"                          =>  $type,
            "pack_id"                       =>  $pack_id,
            "date"                          =>  $date,
            "user_id"                       =>  $user_id,
            "is_featured"                   =>  $is_featured,
            "is_upgrade"                    =>  $is_upgrade,
            "paypal_tax_id"                 =>  $paypal_tax_id,
            "details"                       =>  $details,
            "price"                         =>  $price,
            "to_be_paid"                    =>  $booking_array['deposit'],
            "submission_curency_status"     =>  $submission_curency_status,
            "bookid"                        =>  $bookid,
            "author_id"                     =>  $author_id,
            "youearned"                     =>  $booking_array['youearned'],
            "service_fee"                   =>  $booking_array['service_fee'],
            "booking_taxes"                 =>  $booking_array['taxes'],
            "security_deposit"              =>  $booking_array['security_deposit'],
            "renting_details"               =>  $details,
            "custom_price_array"            =>  $booking_array['custom_price_array'],
            "balance"                       =>  $booking_array['balance']
        );
        
        if($booking_array['balance'] > 0){
            update_post_meta($invoice_id, 'invoice_status_full','waiting');
            $invoice_details['invoice_status_full'] =   'waiting';
        }

        if($booking_array['balance'] == 0){
            update_post_meta($invoice_id, 'is_full_instant',1);
            update_post_meta($booking_id, 'is_full_instant',1);
        }
        
        if( $userID != $property_author){
            rcapi_invoice_booking($invoice_id,  $invoice_details );
        }
        
        die();
} 
endif;




  


///////////////////////////////////////////////////////////////////////////
//edit property location
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_ammenities', 'wpestate_ajax_update_listing_ammenities' );  
if( !function_exists('wpestate_ajax_update_listing_ammenities') ):
    function wpestate_ajax_update_listing_ammenities(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }



        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    $allowed_html           =   array();
                    $i=0;

                    $custom_values_amm = explode('~',wp_kses($_POST['custom_fields_amm'], $allowed_html));
                    $feature_list_array             =   array();
                    $feature_list                   =   esc_html( get_option('wp_estate_feature_list') );
                    $feature_list_array             =   explode( ',',$feature_list);

                    foreach($feature_list_array as $key => $value){

                        $post_var_name      =   str_replace(' ','_', trim($value) );
                        $post_var_name      =   wpestate_limit45(sanitize_title( $post_var_name ));
                        $post_var_name      =   sanitize_key($post_var_name);

                        $feature_value  =   wp_kses( $custom_values_amm[$i+1] ,$allowed_html);
                        update_post_meta($edit_id, $post_var_name, $feature_value);
                        $moving_array[] =   $post_var_name;
                        $i++;
                    }
                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();

                }
            }
        }
    }
endif;

///////////////////////////////////////////////////////////////////////////
//edit property location
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_location', 'wpestate_ajax_update_listing_location' );  
if( !function_exists('wpestate_ajax_update_listing_location') ):
    function wpestate_ajax_update_listing_location(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;

        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    $allowed_html           =   array();

                    $property_latitude      = floatval($_POST['property_latitude']);
                    $property_longitude     = floatval($_POST['property_longitude']);
                    $google_camera_angle    = floatval($_POST['google_camera_angle']);
                    $property_address       = wp_kses($_POST['property_address'],$allowed_html);
                    $property_zip           = wp_kses($_POST['property_zip'],$allowed_html);
                    $property_county        = wp_kses($_POST['property_county'],$allowed_html);
                    $property_state         = wp_kses($_POST['property_state'],$allowed_html);

                    update_post_meta($edit_id, 'property_latitude', $property_latitude);
                    update_post_meta($edit_id, 'property_longitude', $property_longitude);
                    update_post_meta($edit_id, 'google_camera_angle', $google_camera_angle);
                    update_post_meta($edit_id, 'property_address', $property_address);
                    update_post_meta($edit_id, 'property_zip', $property_zip);
                    update_post_meta($edit_id, 'property_state', $property_state);
                    update_post_meta($edit_id, 'property_county', $property_county);
                    
                       //  RCAPI
                 
                    $api_update_details=array();
                    $api_update_details['property_latitude']        =     $property_latitude;
                    $api_update_details['property_longitude']       =     $property_longitude;
                    $api_update_details['google_camera_angle']      =     $google_camera_angle;
                    $api_update_details['property_address']         =     $property_address;
                    $api_update_details['property_zip']             =     $property_zip;
                    $api_update_details['property_state']           =     $property_state; 
                    $api_update_details['property_county']          =     $property_county;
                      
                    
                    
                    rcapi_update_listing($edit_id,$api_update_details);
                    
                    // end RCAPI

                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();

                }
            }   
        }
    }    
endif;


///////////////////////////////////////////////////////////////////////////
//edit property location
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_ical_feed', 'wpestate_ajax_update_ical_feed' );  
if( !function_exists('wpestate_ajax_update_ical_feed') ):
    function wpestate_ajax_update_ical_feed(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;

        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }



        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    
                    $tmp_feed_array =   array();
                    $all_feeds      =   array();
                    //array_feeds,array_labels
                    foreach( $_POST['array_feeds'] as $key=>$value ){
                        if($value!=''){
                            $tmp_feed_array['feed'] =   esc_url_raw($value);
                            $tmp_feed_array['name'] =   esc_html($_POST['array_labels'][$key]);
                            $all_feeds[]            =   $tmp_feed_array;
                        }
                    }
                    
                    if( !empty($all_feeds)   ){
                        update_post_meta($edit_id, 'property_icalendar_import_multi', $all_feeds);
                        wpestate_import_calendar_feed_listing_global($edit_id);
                    }
                    
                    
                    
//                    $property_icalendar_import      =esc_url_raw($_POST['property_icalendar_import']);
//                    update_post_meta($edit_id, 'property_icalendar_import', $property_icalendar_import);
//                    
//                    if ($property_icalendar_import!=''){
//                        wpestate_import_calendar_feed_listing($edit_id);
//                    }
//                    
                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();

                }
            }   
        }
    }    
endif;


///////////////////////////////////////////////////////////////////////////
//edit property location
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_delete_imported_dates', 'wpestate_ajax_delete_imported_dates' );  
if( !function_exists('wpestate_ajax_delete_imported_dates') ):
    function wpestate_ajax_delete_imported_dates(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;
        
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $edit_id    =   intval( $_POST['edit_id']);
        $key        =   intval($_POST['key']);
        
        $property_icalendar_import_multi = get_post_meta($edit_id, 'property_icalendar_import_multi',true);
        
        $name_to_delete=$property_icalendar_import_multi[$key]['name'];
        
        unset($property_icalendar_import_multi[$key]);
        update_post_meta($edit_id, 'property_icalendar_import_multi',$property_icalendar_import_multi);
        
        $the_post   =   get_post( $edit_id); 
        if( $userID!= $the_post->post_author ) {
            exit('you don\'t have the right to delete this');;
        }else{
            $reservation_array  = get_post_meta($edit_id, 'booking_dates',true  ); 
            
            foreach($reservation_array as $key=>$value){
//                if( !is_numeric($value)){
//                    unset($reservation_array[$key]);
//                }
                if($name_to_delete==$value){
                     unset($reservation_array[$key]);
                }
                
                
            }
            update_post_meta($edit_id, 'booking_dates',$reservation_array);
            print'done';
        }
        die();
        
    }
endif;




////////////////////////////////////////////////////////////////////////////
//edit property images
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_details', 'wpestate_ajax_update_listing_details' );  
if( !function_exists('wpestate_ajax_update_listing_details') ):
    function wpestate_ajax_update_listing_details(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;
        $api_update_details                       =     array();
        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    $allowed_html           =   array();
                    $property_size          =   floatval($_POST['property_size']);
                    $property_rooms         =   floatval($_POST['property_rooms']);
                    $property_bedrooms      =   floatval($_POST['property_bedrooms']);
                    $property_bathrooms     =   floatval($_POST['property_bathrooms']);

                    update_post_meta($edit_id, 'property_size', $property_size);
                    update_post_meta($edit_id, 'property_rooms', $property_rooms);
                    update_post_meta($edit_id, 'property_bedrooms', $property_bedrooms);
                    update_post_meta($edit_id, 'property_bathrooms', $property_bathrooms);

                    $custom_values = explode('~',wp_kses($_POST['custom_fields_val'], $allowed_html));

                    // save custom fields
                    $i=0;
                    $custom_fields = get_option( 'wp_estate_custom_fields', true);  
                    if( !empty($custom_fields)){  
                        while($i< count($custom_fields) ){
                            $name =   $custom_fields[$i][0];
                            $type =   $custom_fields[$i][1];
                            $slug =   str_replace(' ','_',$name);
                            $slug =   wpestate_limit45(sanitize_title( $name ));
                            $slug =   sanitize_key($slug);

                            if($type=='numeric'){
                                $value_custom    =   intval(wp_kses( $custom_values[$i+1],$allowed_html ) );
                                update_post_meta($edit_id, $slug, $value_custom);
                            }else{
                                $value_custom    =   esc_html(wp_kses( $custom_values[$i+1],$allowed_html ) );
                                update_post_meta($edit_id, $slug, $value_custom);
                            }                       
                            
                            $api_update_details[$slug] =     $value_custom;
                            $i++;
                        }
                    }

                    
                    //  RCAPI
                 
                    $api_update_details['property_size']      =     $property_size;
                    $api_update_details['property_rooms']     =     $property_rooms;
                    $api_update_details['property_bedrooms']  =     $property_bedrooms;
                    $api_update_details['property_bathrooms'] =     $property_bathrooms;
                    
                    
                    
                    rcapi_update_listing($edit_id,$api_update_details);
                    
                    // end RCAPI
                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();
                }
            }
        }    
    }
endif;

////////////////////////////////////////////////////////////////////////////
//edit property images
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_images', 'wpestate_ajax_update_listing_images' );  
if( !function_exists('wpestate_ajax_update_listing_images') ):
    function wpestate_ajax_update_listing_images(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;


        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    $allowed_html   =   array();

                    $video_type     =   wp_kses($_POST['video_type'],$allowed_html);
                    $video_id       =   wp_kses($_POST['video_id'],$allowed_html);
                    $attachthumb    =   intval($_POST['attachthumb']);
                    $attachid       =   wp_kses($_POST['attachid'],$allowed_html);

                    $attach_array   =   explode(',',$attachid);
                    $last_id        =   '';

                    // check for deleted images
                    $arguments = array(
                                'numberposts'   => -1,
                                'post_type'     => 'attachment',
                                'post_parent'   => $edit_id,
                                'post_status'   => null,
                                'orderby'       => 'menu_order',
                                'order'         => 'ASC'
                    );
                    $post_attachments = get_posts($arguments);

                    $new_thumb=0;
                    $curent_thumb=get_post_thumbnail_id($edit_id);
                    foreach ($post_attachments as $attachment){
                        if ( !in_array ($attachment->ID,$attach_array) ){
                            wp_delete_post($attachment->ID);
                            if( $curent_thumb == $attachment->ID ){
                                $new_thumb=1;
                            }
                        }
                    }

                    // check for deleted images

                    $order=0;
                    foreach($attach_array as $att_id){
                        if( !is_numeric($att_id) ){

                        }else{
                            if($last_id==''){
                                $last_id=  $att_id;  
                            }
                            $order++;
                            wp_update_post( array(
                                        'ID' => $att_id,
                                        'post_parent' => $edit_id,
                                        'menu_order'=>$order
                                    ));


                        }
                    }

                    if( $attachthumb !=''  ){
                        set_post_thumbnail( $edit_id, $attachthumb ); 
                    } 

                    if($new_thumb==1 || !has_post_thumbnail($edit_id) || $attachthumb==''){
                        set_post_thumbnail( $edit_id, $last_id );
                    }

                    update_post_meta($edit_id, 'embed_video_type', $video_type);
                    update_post_meta($edit_id, 'embed_video_id', $video_id);

                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();
                }
            }
        }   
    }
endif;







////////////////////////////////////////////////////////////////////////////
//edit property price
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_price', 'wpestate_ajax_update_listing_price' );  
if( !function_exists('wpestate_ajax_update_listing_price') ):
    function wpestate_ajax_update_listing_price(){ 
        $current_user = wp_get_current_user();
        $userID                         =   $current_user->ID;

        if ( !is_user_logged_in() ) {   
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }
        $api_update_details=array();
        


        if( isset( $_POST['listing_edit'] ) ) {
            if( !is_numeric($_POST['listing_edit'] ) ){
                exit('you don\'t have the right to edit this');
            }else{
                $edit_id    =   intval($_POST['listing_edit'] );
                $the_post   =   get_post( $edit_id); 

                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to edit this","wpestate");
                    die();
                }else{
                    $allowed_html                   =  array();
                    $cleaning_fee                   =   floatval( $_POST['cleaning_fee']);
                    $city_fee                       =   floatval( $_POST['city_fee']);
                    $price                          =   floatval( $_POST['price']);
                    $price_week                     =   floatval( $_POST['price_week']);
                    $price_month                    =   floatval( $_POST['price_month']);
                    $cleaning_fee_per_day           =   floatval( $_POST['cleaning_fee_per_day']);
                    $city_fee_per_day               =   floatval( $_POST['city_fee_per_day']);
                    $min_days_booking               =   floatval( $_POST['min_days_booking']);
                    $price_per_guest_from_one       =   floatval( $_POST['price_per_guest_from_one']);
                    $price_per_weekeend             =   floatval( $_POST['price_per_weekeend']);
                    $checkin_change_over            =   floatval( $_POST['checkin_change_over']);
                    $checkin_checkout_change_over   =   floatval( $_POST['checkin_checkout_change_over']);
                    $extra_price_per_guest          =   floatval( $_POST['extra_price_per_guest']);
                    $overload_guest                 =   floatval( $_POST['overload_guest']);
                    
                   
                    $city_fee_percent               =   floatval( $_POST['city_fee_percent']);
                    $security_deposit               =   floatval( $_POST['security_deposit']);
                    $property_price_after_label     =   esc_html( $_POST['property_price_after_label']);
                    $property_price_before_label    =   esc_html( $_POST['property_price_before_label']);
                    $extra_pay_options              =   $_POST['extra_pay_options'];
                    $early_bird_percent             =   floatval( $_POST['early_bird_percent']);
                    $early_bird_days                =   floatval( $_POST['early_bird_days']);
                    $property_taxes                 =   floatval( $_POST['property_taxes']);
            
                    $extra_pay_values=array();
                    foreach($extra_pay_options as $key=>$pay_option){
                        $option= explode('|', $pay_option);
                        $extra_pay_values[]=$option;
                        
                    }
                    

                    update_post_meta($edit_id, 'property_price', $price);
                    update_post_meta($edit_id, 'cleaning_fee', $cleaning_fee);
                    update_post_meta($edit_id, 'city_fee', $city_fee);
                    update_post_meta($edit_id, 'property_price_per_week', $price_week);
                    update_post_meta($edit_id, 'property_price_per_month', $price_month);
                    update_post_meta($edit_id, 'cleaning_fee_per_day', $cleaning_fee_per_day);
                    update_post_meta($edit_id, 'city_fee_per_day', $city_fee_per_day);
                    update_post_meta($edit_id, 'price_per_guest_from_one', $price_per_guest_from_one);
                    update_post_meta($edit_id, 'price_per_weekeend', $price_per_weekeend);
                    update_post_meta($edit_id, 'checkin_change_over', $checkin_change_over);
                    update_post_meta($edit_id, 'checkin_checkout_change_over', $checkin_checkout_change_over);
                    update_post_meta($edit_id, 'min_days_booking', $min_days_booking);
                    update_post_meta($edit_id, 'extra_price_per_guest', $extra_price_per_guest);
                    update_post_meta($edit_id, 'overload_guest', $overload_guest);
                    
                    
                    update_post_meta($edit_id, 'city_fee_percent', $city_fee_percent);
                    update_post_meta($edit_id, 'security_deposit', $security_deposit);
                    update_post_meta($edit_id, 'property_price_after_label', $property_price_after_label);
                    update_post_meta($edit_id, 'property_price_before_label', $property_price_before_label);
                    update_post_meta($edit_id, 'extra_pay_options', $extra_pay_values);
                    update_post_meta($edit_id, 'early_bird_days', $early_bird_days);
                    update_post_meta($edit_id, 'early_bird_percent', $early_bird_percent);
                    update_post_meta($edit_id, 'property_taxes', $property_taxes);
                    
                    
                   


                    //  RCAPI
                    $api_update_details['property_price']                           =   $price;
                    $api_update_details['cleaning_fee']                             =   $cleaning_fee;
                    $api_update_details['city_fee']                                 =   city_fee;
                    $api_update_details['property_price_per_week']                  =   $price_week;
                    $api_update_details['property_price_per_month']                 =   $price_month;
                    $api_update_details['cleaning_fee_per_day']                     =   $cleaning_fee_per_day;
                    $api_update_details['city_fee_per_day']                         =   $city_fee_per_day;
                    $api_update_details['price_per_guest_from_one']                 =   $price_per_guest_from_one;
                    $api_update_details['price_per_weekeend']                       =   $price_per_weekeend;
                    $api_update_details['checkin_change_over']                      =   $checkin_change_over;
                    $api_update_details['checkin_checkout_change_over']             =   $checkin_checkout_change_over;
                    $api_update_details['min_days_booking']                         =   $min_days_booking;
                    $api_update_details['extra_price_per_guest']                    =   $extra_price_per_guest;
                    $api_update_details['overload_guest']                           =   $overload_guest;
                    $api_update_details['security_deposit']                         =   $security_deposit;
                    $api_update_details['property_price_after_label']               =   $property_price_after_label;
                    $api_update_details['property_price_before_label']              =   $property_price_before_label;
                    $api_update_details['extra_pay_options']                        =   $extra_pay_values;
                    $api_update_details['early_bird_days']                          =   $early_bird_days;
                    $api_update_details['early_bird_percent']                       =   $early_bird_percent;
                    $api_update_details['property_taxes']                           =   $property_taxes;
                    $api_update_details['city_fee_percent']                         =   $city_fee_percent;
                    
                    $custom_price_array  =   wpml_custom_price_adjust($edit_id);
                    $mega_details        =   wpml_mega_details_adjust($edit_id);
            
                    $api_update_details['custom_price']                    =   $custom_price_array;
                    $api_update_details['mega_details']                    =   $mega_details;
                    
                  

        
                    
                    rcapi_update_listing($edit_id,$api_update_details);
                    
                    // end RCAPI
                    
                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                    die();

                }   
            }
        }
    }
endif;


////////////////////////////////////////////////////////////////////////////
//edit property description
////////////////////////////////////////////////////////////////////////////   

add_action( 'wp_ajax_wpestate_ajax_update_listing_description', 'wpestate_ajax_update_listing_description' );  
if( !function_exists('wpestate_ajax_update_listing_description') ):
function wpestate_ajax_update_listing_description(){ 
    
    $current_user       =   wp_get_current_user();
    $userID             =   $current_user->ID;
    $api_update_details =   array();
   
    if ( !is_user_logged_in() ) {   
        exit('ko');
    }
    if($userID === 0 ){
        exit('out pls');
    }


    if( isset( $_POST['listing_edit'] ) ) {
        if( !is_numeric($_POST['listing_edit'] ) ){
            exit('you don\'t have the right to edit this');
        }else{
            $edit_id    =   intval($_POST['listing_edit'] );
            $the_post   =   get_post( $edit_id); 
            
            if( $current_user->ID != $the_post->post_author ) {
                esc_html_e("you don't have the right to edit this","wpestate");
                die();
            }else{
            ////////////////////////////////////////////////////////////////////    
            // start the edit    
            ////////////////////////////////////////////////////////////////////    
                $allowed_html                   =   array();
                $has_errors                     =   false;
                $show_err                       =   '';
                $submit_title                   =   wp_kses( $_POST['title'] ,$allowed_html); 
                $submit_desc                    =   wp_kses( $_POST['prop_desc'] ,$allowed_html); 
                $guest_no                       =   intval( $_POST['guests']);
            
                //category
                if( !isset($_POST['category']) ) {
                    $prop_category=0;           
                }else{
                    $prop_category  =   intval($_POST['category']);
                }

                if($prop_category==-1){
                    wp_delete_object_term_relationships($edit_id,'property_category'); 
                }
                
                //action category
                if( !isset($_POST['action_category']) ) {
                    $prop_action_category=0;           
                }else{
                    $prop_action_category  =   wp_kses($_POST['action_category'],$allowed_html);
                }

                if($prop_action_category==-1){
                    wp_delete_object_term_relationships($edit_id,'property_action_category'); 
                }
                
                $prop_category                  =   get_term( $prop_category, 'property_category');
                if(isset($prop_category->term_id)){
                    $prop_category_selected         =   $prop_category->term_id;
                }

                $prop_action_category           =   get_term( $prop_action_category, 'property_action_category');  
                if(isset($prop_action_category->term_id)){
                    $prop_action_category_selected  =   $prop_action_category->term_id;
                }
                
                // city
                if( !isset($_POST['city']) ) {
                    $property_city=0;           
                }else{
                    $property_city  =   wp_kses($_POST['city'],$allowed_html);
                }
                
                if( !isset($_POST['country']) ) {
                    $property_country=0;           
                }else{
                    $property_country  =   wp_kses($_POST['country'],$allowed_html);
                }
                
                 if( !isset($_POST['area']) ) {
                    $property_area=0;           
                }else{
                    $property_area  =   wp_kses($_POST['area'],$allowed_html);
                }
               
                if( !isset($_POST['property_admin_area']) ) {
                    $property_admin_area='';           
                }else{
                    $property_admin_area  =   wp_kses($_POST['property_admin_area'],$allowed_html);
                }
                
                
                
                
                //////////////////////////////////////// the updated 
                
                if($submit_title==''){
                    $has_errors=true;
                    $errors[]=esc_html__( 'Please submit a title for your listing','wpestate');
                }

                if($prop_category=='' || $prop_category=='-1'){
                    $has_errors=true;
                    $errors[]=esc_html__( 'Please submit a category for your property','wpestate');
                }

                if($prop_action_category=='' || $prop_action_category=='-1'){
                    $has_errors=true;
                    $errors[]=esc_html__( 'Please chose a room type for your listing','wpestate');
                }
                
                if($property_city==''){
                    $has_errors=true;
                    $errors[]=esc_html__( 'Please submit a city for your listing','wpestate');
                }

                
                
                if($has_errors){
                    foreach($errors as $key=>$value){
                       $show_err.=$value.'</br>';
                    }
                    echo json_encode(array('edited'=>false, 'response'=>$show_err));
                }else{
                    $post = array(
                        'ID'            => $edit_id,
                        'post_title'    => $submit_title,
                        'post_type'     => 'estate_property',
                        'post_content'  =>  $submit_desc
                    );

                 
                            
                    $post_id =  wp_update_post($post );  
                    $prop_category                  =   get_term( $prop_category, 'property_category');
                    $prop_action_category           =   get_term( $prop_action_category, 'property_action_category');     

                    
                    if( isset($property_city) && $property_city!='none' && $property_city!='' ){
                        wp_set_object_terms($post_id,$property_city,'property_city'); 
                    } 
                    
                    if( isset($property_area) && $property_area!='none' ){
                        $property_area= wpestate_double_tax_cover($property_area,$property_city,$post_id);
                       // wp_set_object_terms($post_id,$property_area,'property_area'); 
                    }  
  
                    
                    if ( isset ($prop_action_category->name) ){
                        wp_set_object_terms($post_id,$prop_action_category->name,'property_action_category'); 
                    } 
                
                    if( isset($prop_category->name) ){
                        wp_set_object_terms($post_id,$prop_category->name,'property_category'); 
                    } 
                    
                    
                    if( isset($property_area) && $property_area!='none' && $property_area!=''){
                        $property_area_obj=   get_term_by('name', $property_area, 'property_area'); 
             
                        $t_id = $property_area_obj->term_id ;
                        $term_meta = get_option( "taxonomy_$t_id");
                        $allowed_html   =   array();
                        $term_meta['cityparent'] =  wp_kses( $property_city,$allowed_html);
//                        $term_meta['pagetax'] = '';
//                        $term_meta['category_featured_image '] = '';
//                        $term_meta['category_tagline'] = '';
//                        $term_meta['category_attach_id'] = '';

                        //save the option array
                         update_option( "taxonomy_$t_id", $term_meta );
               
                    }
                    
                    update_post_meta($post_id, 'guest_no', $guest_no);
                    update_post_meta($post_id, 'instant_booking',intval($_POST['instant_booking']));
                      
                    update_post_meta($post_id, 'property_country', strtolower($property_country));
                    $property_admin_area                     =   str_replace(" ", "-", $property_admin_area);
                    $property_admin_area                     =   str_replace("\'", "", $property_admin_area);
                    update_post_meta($post_id, 'property_admin_area',strtolower( $property_admin_area) ); 
                    
                    
                    
                    //rentals club api update
                       
                    $api_update_details['post_title']           =   $submit_title;
                    $api_update_details['submit_desc']          =   $submit_desc;
                    $api_update_details['property_city']        =   $property_city;
                    $api_update_details['property_area']        =   $property_area;      
                    $api_update_details['guest_no']             =   $guest_no;
                    $api_update_details['instant_booking']      =   $instant_booking;
                    $api_update_details['property_country']     =   $property_country;
                    $api_update_details['property_admin_area']  =   $property_admin_area;
                   
                    if ( isset ($prop_action_category->name) ){
                        $api_update_details['prop_category_action_name']  =   $prop_action_category->name;     
                    } 
                
                    if( isset($prop_category->name) ){
                        $api_update_details['prop_category_name']  =   $prop_category->name;     
                    } 
                    
                    rcapi_update_listing($post_id,$api_update_details);
                    
                    //END rentals club api update
                    
                    echo json_encode(array('edited'=>true, 'response'=>esc_html__( 'Changes are saved!','wpestate')));
                }
                
              
                die();
            }  
        }
    }
}
endif;