<!DOCTYPE html>
<html lang="<?php echo $LANGUAGE ?>" dir="<?php echo $cssDirection ?>" direction="<?php echo $cssDirection ?>" style="direction: <?php echo $cssDirection ?>">
<!--begin::Head-->

<head>
    <script>
        dataLayer = [{
            'environment': '<?php echo getenv('ENV') == Constants::ENV_PROD ? 'production' : 'staging' ?>',
            'environmentVersion': '1.0',
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
                'id': '',
                'status': 'anonymous',
                'type': '',
                'level': ''
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

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
         https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-analytics.js"></script>

    <script>
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyBy1rh8zZNp1lnUBLyQ15a-cgNvZzsNFBU",
            authDomain: "aumet-com.firebaseapp.com",
            databaseURL: "https://aumet-com.firebaseio.com",
            projectId: "aumet-com",
            storageBucket: "aumet-com.appspot.com",
            messagingSenderId: "380649916442",
            appId: "1:380649916442:web:8ff3bfa9cd74f7c69969a3",
            measurementId: "G-YJ2BRPK2JD"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();
        firebase.auth().signOut();
    </script>
    <meta charset="utf-8" />
    <title><?php if (isset($pageSeoTitle)){echo $pageSeoTitle;}  ?></title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="canonical" href="" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap">
    <!--end::Fonts-->
    <!--begin::Page Custom Styles(used by this page)-->
    <link href="/theme/assets/css/pages/login/login-3<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <!--end::Page Custom Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="/theme/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/style.bundle<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="/theme/assets/css/themes/layout/header/base/light<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/header/menu/light<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/brand/dark<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/aside/dark<?php echo $cssDirection == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
    <!--end::Layout Themes-->

    <link href="/assets/css/auth.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/assets/css/app.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />

    <link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
    <link rel="manifest" href="/favicons/manifest.json" crossorigin="use-credentials">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="/favicons/favicon.ico">
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NM5G929"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
        var KTAppSettings = {
            "breakpoints": {
                "sm": 576,
                "md": 768,
                "lg": 992,
                "xl": 1200,
                "xxl": 1400
            },
            "colors": {
                "theme": {
                    "base": {
                        "white": "#ffffff",
                        "primary": "#3699FF",
                        "secondary": "#E5EAEE",
                        "success": "#1BC5BD",
                        "info": "#8950FC",
                        "warning": "#FFA800",
                        "danger": "#F64E60",
                        "light": "#E4E6EF",
                        "dark": "#181C32"
                    },
                    "light": {
                        "white": "#ffffff",
                        "primary": "#E1F0FF",
                        "secondary": "#EBEDF3",
                        "success": "#C9F7F5",
                        "info": "#EEE5FF",
                        "warning": "#FFF4DE",
                        "danger": "#FFE2E5",
                        "light": "#F3F6F9",
                        "dark": "#D6D6E0"
                    },
                    "inverse": {
                        "white": "#ffffff",
                        "primary": "#ffffff",
                        "secondary": "#3F4254",
                        "success": "#ffffff",
                        "info": "#ffffff",
                        "warning": "#ffffff",
                        "danger": "#ffffff",
                        "light": "#464E5F",
                        "dark": "#ffffff"
                    }
                },
                "gray": {
                    "gray-100": "#F3F6F9",
                    "gray-200": "#EBEDF3",
                    "gray-300": "#E4E6EF",
                    "gray-400": "#D1D3E0",
                    "gray-500": "#B5B5C3",
                    "gray-600": "#7E8299",
                    "gray-700": "#5E6278",
                    "gray-800": "#3F4254",
                    "gray-900": "#181C32"
                }
            },
            "font-family": "Cairo"
        };
    </script>
    <!--end::Global Config-->
    <!--begin::Global Theme Bundle(used by all pages)-->
    <script src="/theme/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/theme/assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
    <script src="/theme/assets/js/scripts.bundle.js"></script>
    <!--end::Global Theme Bundle-->
    <!--begin::Page Scripts(used by this page)-->
    <script>
        var docLang = "<?php echo $LANGUAGE ?>";
    </script>
    <script src="/assets/js/locals.js<?php echo $platformVersion ?>"></script>
    <?php if($vAuthFile == 'signup-invite'): ?>
        <script src="/assets/js/auth-invite.js<?php echo $platformVersion ?>"></script>
    <?php else: ?>
        <script src="/assets/js/auth.js<?php echo $platformVersion ?>"></script>
    <?php endif; ?>
    <script src="/assets/js/app.js<?php echo $platformVersion ?>"></script>
    <script src="/assets/js/jquery.foggy.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/3.1.0/jquery.serializejson.min.js" integrity="sha512-4y8bsEzrXJqRyl2dqjdKk/DetH59JcFTtYNMsy5DUpvVV8CXiSrQ1gSCL3+dFgj1Xco0ONPizsYd6wX2eAXL2g==" crossorigin="anonymous"></script>

    <!--end::Page Scripts-->

    <?php include getcwd() . '/ui/app/layout/supportPanel.php'; ?>
    <div class="supportButton" data-toggle="modal" data-target="#support_modal">
        <i class="la la-headset la-2x text-white"></i>
    </div>



    <?php include_once "$vAuthFile.php" ?>
</body>
<!--end::Body-->

</html>