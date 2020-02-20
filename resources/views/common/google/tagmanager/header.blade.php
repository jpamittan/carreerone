<?php if(!empty(config('google.tag_manager.id')) && (env('APP_ENV') == 'production' || env('APP_ENV') == 'staging' || env('APP_ENV') == 'sandbox')): ?>
<!-- Google Tag Manager -->
<script>(function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(), event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?=config('google.tag_manager.id')?>');</script>
<!-- End Google Tag Manager -->
<?php endif; ?>