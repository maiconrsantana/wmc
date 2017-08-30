<header id="menu2" class="st_menu">
    <div id='top_header' class="header-top <?php echo apply_filters('st_header_top_class','') ?>">
        <div class="text-center">
            <a class="logo" href="<?php echo home_url('/')?>">
                <?php
                $logo_url = st()->get_option('logo');
                if(!empty($logo_url)){
                    ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="logo" title="<?php bloginfo('name')?>">
                    <?php
                }
                ?>
            </a>
        </div>
        <div class="menu-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav">
                            <?php if(has_nav_menu('primary')){
                                wp_nav_menu(
                                    array(
                                        'theme_location'=>'primary',
                                        "container"=>"",
                                        'items_wrap'      => '<ul id="slimmenu" data-title="<a href=\''.home_url('/').'\'><img alt=\''.TravelHelper::get_alt_image().'\' width=auto height=40px class=st_logo_mobile src='.$logo_url.' /></a>" class="%2$s slimmenu">%3$s</ul>',
                                    )
                                );
                            } ?>

                            <div class="user-nav-wrapper">
                                <?php get_template_part('users/user','nav');?>
                            </div>
                            <div class="collapse-button collapse-user"><i class="fa fa-user"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>