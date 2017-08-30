<?php
wp_enqueue_style('weather-icons.css');
$temp = TravelHelper::get_location_temp();
?>
<div class="loc-info hidden-xs hidden-sm">
    <h3 class="loc-info-title"> <?php the_title() ?></h3>
    <?php if ($temp): ?>
        <p class="loc-info-weather">
       <span class="loc-info-weather-num">
       <?php echo balanceTags($temp['temp']) ?>
       </span>
            <?php echo balanceTags($temp['icon']) ?>
        </p>
    <?php endif; ?>
    <ul class="loc-info-list">
        <?php
        $services = TravelHelper::get_services();
        foreach ($services as $service => $service_icon):
            if ($service !== 'hotel_room'):
                $class_service = null;
                switch ($service) {
                    case 'st_hotel':
                        $class_service = STHotel::inst();
                        break;
                    case 'st_rental':
                        $class_service = STRental::inst();
                        break;
                    case 'st_tours':
                        $class_service = STTour::get_instance();
                        break;
                    case 'st_activity':
                        $class_service = STActivity::inst();
                        break;
                    case 'st_cars':
                        $class_service = STCars::get_instance();
                        break;
                    default:
                        $class_service = STHotel::inst();
                        break;
                }
                if ($class_service->is_available()):
                    $location = new STLocation();
                    $infomation = $location->get_info_by_post_type(get_the_ID(), $service);
                    $min_price = (float)$infomation['min_max_price']['price_min'];
                    if (!$min_price < 0) $min_price = 0;
                    $min_price = TravelHelper::format_money($min_price);
                    if (empty($min_price) or !$min_price) {
                        $min_price = __("Free", ST_TEXTDOMAIN);
                    }
                    if (is_array($infomation) && count($infomation)) {

                        $offer = $infomation['offers'];
                        if (!empty($offer)) {
                            $page_search = st_get_page_search_result($service);
                            if (!empty($page_search) and get_post_type($page_search) == 'page') {
                                $link = add_query_arg(array('location_id' => get_the_ID(), 'pick-up' => get_the_title(), 'location_name' => get_the_title()), get_the_permalink($page_search));
                            } else {
                                $link = add_query_arg(array(
                                    's' => '',
                                    'post_type' => $service,
                                    'location_id' => get_the_ID(),
                                    'pick-up' => get_the_title()
                                ), home_url('/'));
                            }
                            if( $offer >= 2 ){
                                $offer_string = sprintf(__('%d %s from %s/night', ST_TEXTDOMAIN), $offer , $location->get_post_type_name( $service ), $min_price);
                            }else{
                                $offer_string = sprintf(__('%d %s from %s/night', ST_TEXTDOMAIN), $offer , $location->get_post_type_name( $service, true ), $min_price);
                            }
                            echo '<li><a href="' . $link . '"><i class="fa ' . esc_attr($service_icon) . '"></i> ' . $offer_string . '</a></li>';
                        }
                    }
                    ?>
                <?php endif; endif; endforeach; ?>
    </ul>
    <?php
    $page_search = st_get_page_search_result($st_type);
    if (!empty($page_search)) {
        $link = add_query_arg(array('location_id' => get_the_ID(), 'pick-up' => get_the_title(), 'location_name' => get_the_title()), get_the_permalink($page_search));
    } else {
        $link = add_query_arg(array(
            's' => '',
            'post_type' => $st_type,
            'location_id' => get_the_ID(),
            'pick-up' => get_the_title()
        ), home_url('/'));
    }
    ?>
    <a class="btn btn-white btn-ghost mt10" href="<?php echo esc_url($link) ?>">
        <i class="fa fa-angle-right"></i>
        <?php echo STLanguage::st_get_language('explore') ?>
    </a>
</div>