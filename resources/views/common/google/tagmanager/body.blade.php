<?php if(!empty(config('google.tag_manager.id')) && (env('APP_ENV') == 'production' || env('APP_ENV') == 'staging' || env('APP_ENV') == 'sandbox')): ?>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?=config('google.tag_manager.id')?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif ?>