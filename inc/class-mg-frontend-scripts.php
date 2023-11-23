<?php

class MG_Frontend_Scripts {
    public function __construct() {
        add_action( 'wp_head', array( $this, 'load_eloqua_script' ) );
        add_action( 'wp_head', array( $this, 'load_tag_manager' ) );
    }

    public function load_tag_manager() {
        ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-67GMRNGE99"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-67GMRNGE99');
            gtag('config', 'AW-10978967825');
        </script>
        <?php
    }

    public function load_eloqua_script() {
        // wp_die( 'footer' );
        ?>
        <!-- Eloqua First Party Cookies Tracking Pixel Tienda Christus Start -->
        <script type="text/javascript">
            var _elqQ = _elqQ || [];
            _elqQ.push(['elqSetSiteId', '769142976']);
            _elqQ.push(['elqUseFirstPartyCookie', 'tiendachristus.com']);
            _elqQ.push(['elqTrackPageView']);
            (function() {
            function async_load() {
            var s = document.createElement('script'); s.type = 'text/javascript';
            s.async = true;
            s.src = '//img.en25.com/i/elqCfg.min.js';
            var x = document.getElementsByTagName('script')[0];
            x.parentNode.insertBefore(s, x);
            }
            if(window.addEventListener) window.addEventListener('DOMContentLoaded', async_load, false);
            else if (window.attachEvent) window.attachEvent('onload', async_load);
            })();
        </script>
        <!-- Eloqua First Party Cookies Tracking Pixel Tienda Christus End -->
        <?php
    }
}

new MG_Frontend_Scripts;
