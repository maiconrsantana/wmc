<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 4/18/2017
     * Time: 4:29 PM
     */
    if ( !class_exists( 'ST_Ical_Sysc' ) ) {
        class ST_Ical_Sysc extends TravelerObject
        {
            public function __construct()
            {
                add_action( 'wp_ajax_st_import_ical', [ $this, 'st_import_ical' ] );
            }

            public function st_import_ical()
            {
                $url = esc_url( STInput::post( 'url', '' ) );
                $post_id = (int)STInput::post( 'post_id', '' );

                if ( !empty( $url ) ) {
                    $ical = new ICal( $url );
                    if ( !empty( $ical ) ) {
                        $events = $ical->events();
                        if ( !empty( $events ) && is_array( $events ) ) {
                            foreach ( $events as $key => $event ) {
                                $sumary = explode('|', $event['SUMMARY']);
                                $price = (float)$sumary[0];
                                if ( $price < 0 ) {
                                    $price = 0;
                                }
                                $available = 'available';
                                if(isset($sumary[1]) && !empty($sumary[1]) && strtolower($sumary[1]) == 'unavailable'){
                                    $available = 'not_available';
                                }
                                $start = DateTime::createFromFormat( 'Ymd', $event[ 'DTSTART' ] );
                                $start = strtotime( $start->format( 'Y-m-d' ) );
                                $end = DateTime::createFromFormat( 'Ymd', $event[ 'DTEND' ] );
                                $end = strtotime( $end->format( 'Y-m-d' ) );
                                $this->import_event( $post_id, $price, $start, $end, $available );
                            }
                        }
                    }
                }

                update_post_meta( $post_id, 'sys_created', current_time( 'timestamp', 1 ) );
                echo json_encode( [
                    'status'  => 1,
                    'message' => '<p class="text-success">' . __( 'Successful', ST_TEXTDOMAIN ) . '</p>'
                ] );
                die;

            }

            private function import_event( $post_id, $price, $start, $end, $available )
            {
                global $wpdb;
                $sql = "SELECT
                    id
                FROM
                    {$wpdb->prefix}st_availability
                WHERE
                    post_id = {$post_id}
                AND (
                    (
                        {$start} BETWEEN check_in
                        AND check_out
                    )
                    OR (
                        {$end} BETWEEN check_in
                        AND check_out
                    )
                )";
                if ( !$wpdb->get_var( $sql ) ) {
                    $string = '';
                    for($i = $start; $i<= $end; $i = strtotime('+1 day', $i)){
                        $string .= "('{$post_id}', 'hotel_room','{$i}', '{$i}', '{$price}', '{$available}'),";
                    }
                    if(!empty($string)){
                        $string = substr($string, 0, -1);$sql = "INSERT INTO {$wpdb->prefix}st_availability (post_id, post_type,check_in,check_out,price, status) VALUE {$string}";
                        $wpdb->query( $sql );
                    }
                }
            }
        }

        new ST_Ical_Sysc();
    }