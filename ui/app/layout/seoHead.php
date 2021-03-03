<script>
        dataLayer = [{
            'environment': '<?php echo getenv('ENV') == Constants::ENV_PROD ? 'production' : 'staging' ?>',
            'environmentVersion':  "<?= $platformVersion ?>",
            'page': {
                'pageName': 'home',
                'category': {
                    'primaryCategory': 'marketplace',
                },
                'attributes': {
                    'country': 'AE',
                    'language': 'ae-EN',
                    'currency': 'AED'
                }
            },
            'user': {
                <?php if($isAuth): ?>
                'id': '<?php echo $objUser->id ?>',
                'status': 'logged',
                'type': '<?php echo $objUser->menuId == Constants::MENU_DISTRIBUTOR ? 'distributor' : 'pharmacy' ?>',
                'level': '<?php echo $objUser->menuId == Constants::MENU_DISTRIBUTOR ? 'freemium' : 'premium' ?>'
                <?php else: ?>
                'id': '',
                'status': 'anonymous',
                'type': '',
                'level': ''
                <?php endif; ?>
            }
        }];
    </script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W5PTNF7');</script>
<!-- End Google Tag Manager -->