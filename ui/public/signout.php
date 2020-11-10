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
    <html>
    <!--begin::Head-->

    <head>
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

            firebase.auth().onAuthStateChanged(function (user) {
                if (!user) {
                    _loadPage('/web/auth/signout', false, null);
                } else {
                    firebase
                        .auth()
                        .currentUser.getIdToken(true)
                        .then(function (idToken) {
                            _idToken = idToken;
                        })
                        .catch(function (error) {
                            // Handle error
                        });
                }
            });
        </script>

        <meta charset="utf-8" />
        <title><?php echo $vTitle; ?></title>


        <link rel="icon" href="/favicons/favicon.ico">

        <meta name="theme-color" content="#ffffff">
    </head>
    <!--end::Head-->
    <!--begin::Body ** subheader-enabled subheader-fixed -->

    <body>



    <script>
        var docLang = "<?php echo $LANGUAGE; ?>";
        var _ajaxUrl = "<?php echo $ajaxUrl; ?>";
        var _id = "<?php echo $objUser->id ?>";
    </script>

    <script type="text/javascript" src="/theme/assets/plugins/custom/jstree/jstree.bundle.js<?php echo $platformVersion ?>"></script>

    <script type="text/javascript" src="/assets/js/math.min.js"></script>
    <script type="text/javascript" src="/assets/js/tour-distributor.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/locals.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/cart.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/app.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/autocomplete.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/products-search.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-orders.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-feedback.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-products.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-customers.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/distributor-dashboard.js<?php echo $platformVersion ?>"></script>


    <script type="text/javascript" src="/assets/js/treeview.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/assets/js/demoApp.js<?php echo $platformVersion ?>"></script>
    <script type="text/javascript" src="/theme/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript" src="/theme/assets/js/pages/crud/forms/widgets/form-repeater.js"></script>

    <script>
        jQuery(document).ready(function() {
            WebApp.init();
        });
    </script>
    </body>
    <!--end::Body-->

    </html>
<?php ob_end_flush(); ?>