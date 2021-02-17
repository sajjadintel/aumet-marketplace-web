<?php
ob_start("compress_htmlcode");
function compress_htmlcode($codedata)
{
    $searchdata = array(
        '/\>[^\S ]+/s', // remove whitespaces after tags
        '/[^\S ]+\</s', // remove whitespaces before tags
        '/(\s)+/s' // remove multiple whitespace sequences
    );
    $replacedata = array('>', '<', '\\1');
    $codedata = preg_replace($searchdata, $replacedata, $codedata);
    return $codedata;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $LANGUAGE ?>" dir="<?php echo $_SESSION['userLangDirection'] ?>" direction="<?php echo $_SESSION['userLangDirection'] ?>" style="direction: <?php echo $_SESSION['userLangDirection'] ?>">
<!--begin::Head-->

<head>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NM5G929');</script>
    <!-- End Google Tag Manager -->

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-app.js"></script>

    <script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-performance.js"></script>

    <script>
        const firebaseConfig = {
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
        console.log(defaultProject.name);
        firebase.analytics();

        var perf = firebase.performance();

        firebase.auth().onAuthStateChanged(function(user) {
            if (!user) {
                _loadPage('/web/auth/signout', false, null);
            } else {
                firebase
                    .auth()
                    .currentUser.getIdToken(true)
                    .then(function(idToken) {
                        _idToken = idToken;
                    })
                    .catch(function(error) {
                        // Handle error
                    });
            }
        });
    </script>

    <meta charset="utf-8" />
    <title><?php echo $vTitle; ?></title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="canonical" href="https://marketplace.aumet.tech" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap">
    <!--end::Fonts-->

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!--begin::Page Custom Styles(used by this page)-->

    <link href="/theme/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

    <!--end::Page Custom Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="/theme/assets/plugins/global/plugins.bundle<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/plugins/custom/prismjs/prismjs.bundle.min.css" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/style.bundle<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="/theme/assets/css/themes/layout/header/base/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.min.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/header/menu/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.min.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/brand/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.min.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/css/themes/layout/aside/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.min.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <!--end::Layout Themes-->

    <link href="/assets/css/app.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/assets/css/app<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/theme/assets/plugins/custom/datatables/datatables.bundle.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />

    <link href="/assets/css/colors.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
    <link href="/assets/css/magnific-popup.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/smart_tab.min.css" rel="stylesheet" type="text/css" />

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
    <link rel="manifest" href="/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicons/ms-icon-144x144.png">

    <link rel="icon" href="/favicons/favicon.ico">

    <meta name="theme-color" content="#ffffff">

    <script>
        var HOST_URL = "";
    </script>
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
    <script src="/theme/assets/plugins/global/plugins.bundle.min.js"></script>
    <script src="/theme/assets/plugins/custom/prismjs/prismjs.bundle.min.js"></script>
    <script src="/theme/assets/js/scripts.bundle.min.js"></script>
    <script src="/assets/js/jquery.foggy.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/3.1.0/jquery.serializejson.min.js" integrity="sha512-4y8bsEzrXJqRyl2dqjdKk/DetH59JcFTtYNMsy5DUpvVV8CXiSrQ1gSCL3+dFgj1Xco0ONPizsYd6wX2eAXL2g==" crossorigin="anonymous"></script>
    <script src="/assets/js/jquery.autocomplete.js"></script>
    <script src="/assets/js/jquery.magnific-popup.js"></script>
    <script src="/assets/js/jquery.slider.js"></script>
    <script src="/assets/js/jquery.expandable.js"></script>

    <!--end::Global Theme Bundle-->

    <!--begin::Slick-->
    <link rel="stylesheet" type="text/css" href="/assets/lib/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="/assets/lib/slick/slick-theme.css" />
    <!--end::Slick-->

</head>
<!--end::Head-->
<!--begin::Body ** subheader-enabled subheader-fixed -->

<body id="kt_body" class="header-fixed header-mobile-fixed  aside-enabled aside-fixed aside-minimize-hoverable aside-minimize page-loading ">


<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NM5G929"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <!--begin::Main-->
    <!--begin::Header Mobile-->
    <?php include_once 'headerMobile.php'; ?>
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">
            <?php if (!$vIsPlatformLocked) : ?>
                <!--begin::Aside-->
                <div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
                    <!--begin::Brand-->
                    <?php include_once 'asideBrand.php'; ?>
                    <!--end::Brand-->
                    <!--begin::Aside Menu-->
                    <?php include_once file_exists(__DIR__ . "/../asideMenu/$objUser->menuCode.php") ? __DIR__ . "/../asideMenu/$objUser->menuCode.php" : __DIR__ . "/../asideMenu/default.php";
                    ?>
                    <!--end::Aside Menu-->
                </div>
                <!--end::Aside-->
            <?php endif ?>
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                <div id="kt_header" class="header header-fixed">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <!--begin::Header Menu Wrapper-->
                        <?php include_once 'headerMenu.php'; ?>
                        <!--end::Header Menu Wrapper-->


                        <?php if (Helper::isPharmacy($_SESSION['objUser']->roleId)) { ?>
                            <div class="search-wrapper search-wrapper-desktop">
                                <i id="searchBarInputDesktopIcon" class="fa fa-search  search-icon"></i>
                                <input class="form-control" id="searchBarInputDesktop" type="text" name="searchBarInputDesktop" autocomplete="off" />
                            </div>
                        <?php } ?>

                        <!--begin::Topbar-->
                        <?php include_once 'headerTopbar.php'; ?>
                        <!--end::Topbar-->
                    </div>

                    <!--end::Container-->
                </div>
                <!--end::Header-->
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="pageContent">
                </div>
                <!--end::Content-->
                <?php include_once 'modal.php'; ?>
                <? include_once 'ui/app/sale/orders/missing-product-list-modal.php'; ?>
                <?php //include_once 'webGuidedTour-distributor.php';
                ?>
                <!--begin::Footer-->
                <?php include_once 'footer.php'; ?>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->
    <!-- begin::User Panel-->
    <?php include_once 'userPanel.php'; ?>
    <!-- end::User Panel-->
    <!--begin::Quick Cart-->

    <!--end::Quick Cart-->
    <!--begin::Quick Panel-->
    <?php //include_once 'quickPanel.php';
    ?>
    <!--end::Quick Panel-->
    <!--begin::Chat Panel-->
    <?php include_once 'chatPanel.php'; ?>
    <?php include_once 'supportPanel.php'; ?>
    <!--end::Chat Panel-->
    <!--begin::Sticky Toolbar-->
    <?php //include_once 'stickyToolbar.php';
    ?>
    <!--end::Sticky Toolbar-->

    <div class="supportButton" data-toggle="modal" data-target="#support_modal">
        <i class="la la-headset la-2x text-white"></i>
    </div>

    <script>
        var docLang = "<?php echo $LANGUAGE; ?>";
        var _ajaxUrl = "<?php echo $ajaxUrl; ?>";
        var _id = "<?php echo $objUser->id ?>";
    </script>

    <script type="text/javascript" src="/theme/assets/plugins/custom/jstree/jstree.bundle.js<?php echo $platformVersion ?>"></script>

    <script type="text/javascript" src="/assets/js/math.min.js"></script>
    <script type="text/javascript" src="/assets/js/locals.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/tour-distributor.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/locals.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/cart.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/cart-checkout.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/app.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/app-modals.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/feedback-modals.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/autocomplete.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/products-search.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-products.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-customers.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/profile.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/jquery.smartTab.min.js"></script>


    <script type="text/javascript" src="/assets/js/treeview.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/demoApp.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/theme/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript" src="/theme/assets/plugins/custom/datatables/ellipsis.js"></script>
    <script type="text/javascript" src="/theme/assets/js/pages/crud/forms/widgets/form-repeater.js"></script>
    <script type="text/javascript" src="/theme/assets/js/pages/crud/forms/widgets/jquery-rate-picker.js"></script>

    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="/assets/lib/slick/slick.min.js"></script>

    <script>
        jQuery(document).ready(function() {
            WebApp.init();
        });

        var _autocompleteDesktop = $('#searchBarInputDesktop').autocomplete2({
            serviceUrl: '/web/searchbar',
            onSelect: function(suggestion) {
                console.log('selected', suggestion);
                WebApp.loadPage('/web/pharmacy/product/search?query=' + suggestion.value);
            },
            onSearchComplete: function(a, b, c) {
                $('#searchBarInputDesktop').addClass('search-wrapper-open');
                $('.autocomplete-suggestions').addClass('autocomplete-suggestions-open');
            },
            onHide: function(a, b) {
                $('#searchBarInputDesktop').removeClass('search-wrapper-open');
                $('.autocomplete-suggestions').removeClass('autocomplete-suggestions-open');
            },
        });

        $('#searchBarInputMobile').autocomplete2({
            serviceUrl: '/web/searchbar',
            onSelect: function(suggestion) {
                console.log('selected', suggestion);
                WebApp.loadPage('/web/pharmacy/product/search?query=' + suggestion.value);
            },
            onSearchComplete: function(a, b, c) {
                $('#searchBarInputMobile').addClass('search-wrapper-open');
                $('.autocomplete-suggestions').addClass('autocomplete-suggestions-open');
            },
            onHide: function(a, b) {
                $('#searchBarInputMobile').removeClass('search-wrapper-open');
                $('.autocomplete-suggestions').removeClass('autocomplete-suggestions-open');
            },
        });

        $('#searchBarInputDesktop').keyup(function(e) {
            if (e.keyCode == 13) {
                WebApp.loadPage('/web/pharmacy/product/search?query=' + $('#searchBarInputDesktop').val());
            }
        });

        $('#searchBarInputMobile').keyup(function(e) {
            if (e.keyCode == 13) {
                WebApp.loadPage('/web/pharmacy/product/search?query=' + $('#searchBarInputMobile').val());
            }
        });

        $("#searchBarInputDesktopIcon").click(function() {
            WebApp.loadPage('/web/pharmacy/product/search?query=' + $('#searchBarInputDesktop').val());
        });

        $("#searchBarInputMobileIcon").click(function() {
            WebApp.loadPage('/web/pharmacy/product/search?query=' + $('#searchBarInputMobile').val());
        });
    </script>

    <?php if ($notAuthorized == 1) { ?>
        <script>
            Swal.fire({
                text: 'The URL you are trying to access is invalid.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Ok, got it!',
                customClass: {
                    confirmButton: 'btn font-weight-bold btn-light-primary',
                },
            }).then(function() {
                KTUtil.scrollTop();
                window.location.href = '/web/auth/signout';
            });
        </script>
    <?php } ?>


<script>
    dataLayer = [{
        'environment': '<?php echo getenv('ENV') == Constants::ENV_PROD ? 'production' : 'staging' ?>',
        'environmentVersion': '1.0',
        'page': {
            'pageName': 'home',
            'category': {
                'primaryCategory': 'blog',
            },
            'attributes': {
                'country': 'AE',
                'language': 'ae-EN',
                'currency': 'AED'
            }
        },
        'user': {
            'id': '<?php echo $objUser->id ?>',
            'status': 'logged',
            'type': '<?php echo $objUser->menuId == Constants::MENU_DISTRIBUTOR ? 'distributor' : 'pharmacy' ?>',
            'level': '<?php echo $objUser->menuId == Constants::MENU_DISTRIBUTOR ? 'freemium' : 'premium' ?>'
        }
    }];
</script>

</body>
<!--end::Body-->

</html>
<?php ob_end_flush(); ?>