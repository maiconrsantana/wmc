<?php
/**
 * Template Name: Landing Page
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <?php if(defined('ST_TRAVELER_VERSION')){?>  <meta name="traveler" content="<?php echo esc_attr(ST_TRAVELER_VERSION) ?>"/>  <?php };?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php if(!function_exists('_wp_render_title_tag')):?>
        <title><?php wp_title('|',true,'right') ?></title>
    <?php endif;?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="landing-wrapper">
        <header class="ld-site-header">
            <div class="ld-logo">
                <a href="<?php echo esc_url(home_url('/'))?>">
                    <?php
                    $logo = st()->get_option('logo','');
                    if(!empty($logo)){
                    ?>
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>">
                    <?php }else{
                        bloginfo('name');
                    } ?>
                </a>
            </div>
            <nav class="ld-nav">
                <ul class="ld-menu">
                    <li class="menu-item">
                        <a href="#demos">DEMOS</a>
                    </li>
                    <li class="menu-item">
                        <a href="#features">FEATURES</a>
                    </li>
                    <li class="menu-item">
                        <a href="#cases">SHOWCASES</a>
                    </li>
                </ul>
            </nav>
            <div class="ld-buy-now">
                <a href="https://themeforest.net/item/traveler-traveltourbooking-wordpress-theme/10822683" target="_blank">Buy Now</a>
            </div>
        </header>
        <div class="ld-banner">
            <div class="container">
                <div class="ld-banner-content">
                    <h2 class="banner-title">#1 BOOKING WORDPRESS THEME</h2>
                    <p class="desc">Traveler based on deep research on the most popular travel booking websites such as booking.com, tripadvisor, yahoo travel, expedia, priceline, hotels.com, travelocity, kayak, orbitz, etc. This guys can’t be wrong. You should definitely give it a shot <img draggable="false" class="emoji" alt="🙂" src="https://s.w.org/images/core/emoji/2.2.1/svg/1f642.svg"></p>
                    <a class="ld-explore" href="#demos">Discover All Demos</a>
                </div>
            </div>
        </div>
        <div class="ld-main-content">
            <section class="ld-demos-home" id="demos">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-1 text-center lb-demos-title-desc">
                            <h2 class="demos-title">Pick From <span class="ld-highlight">Our Demos</span></h2>
                            <p class="desc">Profuse demos mean just as many possibilities. Offering over 14 options, this versatile and fabulous theme is designed to fit comfortably to your need.</p>
                        </div>
                    </div>
                </div>
                <div class="ld-demos">
                    <?php
                    $arr = array(
                        array(
                            'title' => 'List Hotels - Full Map',
                            'img' => get_template_directory_uri().'/demo/img/home/hotel-full-map.jpg',
                            'link' => '#',
                            'new' => 1
                        ),
                        array(
                            'title' => 'List Cars - Full Map',
                            'img' => get_template_directory_uri().'/demo/img/home/cars-full-map.jpg',
                            'link' => '#',
                            'new' => 1
                        ),
                        array(
                            'title' => 'List Rentals - Full Map',
                            'img' => get_template_directory_uri().'/demo/img/home/rental-full-map.jpg',
                            'link' => '#',
                            'new' => 1
                        ),
                        array(
                            'title' => 'List Tours - Full Map',
                            'img' => get_template_directory_uri().'/demo/img/home/tour-full-map.jpg',
                            'link' => '#',
                            'new' => 1
                        ),
                        array(
                            'title' => 'List Activities - Full Map',
                            'img' => get_template_directory_uri().'/demo/img/home/activity-full-map.jpg',
                            'link' => '#',
                            'new' => 1
                        ),
                        array(
                            'title' => 'Default Layout',
                            'img' => get_template_directory_uri().'/demo/img/home/default-home.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Video Background',
                            'img' => get_template_directory_uri().'/demo/img/home/video-background.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Word Rotator',
                            'img' => get_template_directory_uri().'/demo/img/home/word-rotator.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Hero Slider Width Weather Widget',
                            'img' => get_template_directory_uri().'/demo/img/home/weather-slider.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Blured Slider + Weather Widget',
                            'img' => get_template_directory_uri().'/demo/img/home/search-on-slider.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Grid Images',
                            'img' => get_template_directory_uri().'/demo/img/home/grid-images.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Testimonials Rotator',
                            'img' => get_template_directory_uri().'/demo/img/home/testimonial-rotator.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Hero Slider',
                            'img' => get_template_directory_uri().'/demo/img/home/hero-slider.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Location List',
                            'img' => get_template_directory_uri().'/demo/img/home/location-list.jpg',
                            'link' => '#'
                        ),
                        array(
                            'title' => 'Coming soon',
                            'img' => get_template_directory_uri().'/demo/img/home/hotel-full-map.jpg',
                            'link' => '#'
                        ),
                    )
                    ?>
                    <div class="ld-container">
                        <div class="ld-row">
                            <?php
                            foreach($arr as $key => $val){
                            ?>
                            <div class="item">
                                <div class="thumb">
                                    <?php
                                    if(!empty($val['new'])){
                                        echo '<div class="ld_featured">New</div>';
                                    }
                                    ?>
                                    <a class="link-home" href="<?php echo esc_url($val['link'])?>" target="_blank">
                                        <img src="<?php echo esc_url($val['img'])?>" alt="hotel full map">
                                    </a>
                                </div>
                                <h3 class="title"><a href="<?php echo esc_url($val['link'])?>" target="_blank"><?php echo esc_attr($val['title'])?></a></h3>
                            </div>
                                <?php
                                } ?>
                        </div>
                    </div>
                </div>
            </section>
            <section class="ld-featured">
                <div class="container ft-container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="ld-featured-title">Main <span class="ld-highlight">Features</span></h2>
                        </div>
                        <div class="col-md-12">
                            <div class="features-list">
                                <?php
                                $features = array(
                                    array(
                                        'icon' => 'fa-paypal',
                                        'title' => 'Paypal Integrated',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-language',
                                        'title' => 'WPML Supporter',
                                        'desc' => 'Your online business can get even more convenient and productive with plugin designed especially for online shopping experience.',
                                    ),
                                    array(
                                        'icon' => 'fa-arrows',
                                        'title' => 'Fully Responsive',
                                        'desc' => 'Traveler displaying itself at its best on every screensizes or platforms.',
                                    ),
                                    array(
                                        'icon' => 'fa-pagelines',
                                        'title' => 'Visual Composer',
                                        'desc' => 'Visual Composer with extra advanced functionalities and organised clean skin.',
                                    ),
                                    array(
                                        'icon' => 'fa-hand-pointer-o',
                                        'title' => 'One Click Install',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-search',
                                        'title' => 'Smart Search',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-desktop',
                                        'title' => 'Home Page Demos',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-flag',
                                        'title' => 'Font Awesome Icons',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-user',
                                        'title' => 'Dashboard User Page',
                                        'desc' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s',
                                    ),
                                    array(
                                        'icon' => 'fa-envelope',
                                        'title' => 'Mailchimps',
                                        'desc' => 'Online email marketing solution to manage subscribers, send emails, and track results. Offers integrations with other programs.',
                                    ),
                                    array(
                                        'icon' => 'fa-envelope-o',
                                        'title' => 'Contact Form 7',
                                        'desc' => 'Just another contact form plugin. Simple but flexible.',
                                    ),
                                    array(
                                        'icon' => 'fa-cogs',
                                        'title' => 'Traveler Core System',
                                        'desc' => 'With so much of our experience we make Traveler Core System to our each theme for help client more easy to use our product and optimize for SEO.',
                                    ),
                                );
                                foreach($features as $key => $val){
                                    ?>
                                    <div class="feature-item">
                                        <span class="icon"><i class="fa <?php echo $val['icon']?>"></i></span>
                                        <div class="title-desc">
                                            <h4 class="title"><?php echo esc_attr($val['title'])?></h4>
                                            <p class="desc"><?php echo esc_attr($val['desc'])?></p>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
            <section class="ld-show-case">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center title-showcase">
                            <h2 class="show-case-title">Showcase</h2>
                            <p class="desc">Examples of highlight quality standards websites created with the Traveler Theme.</p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="ld-install-theme">

            </section>
            <footer class="ld-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <ul class="ld-social">
                                <li><a href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                                <li><a href="#" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>