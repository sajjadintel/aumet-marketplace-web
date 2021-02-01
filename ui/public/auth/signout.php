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
        <meta charset="utf-8" />
        <title><?php echo $vTitle; ?></title>


        <link rel="icon" href="/favicons/favicon.ico">

        <meta name="theme-color" content="#ffffff">
    </head>
    <body>
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
        var defaultProject = firebase.initializeApp(firebaseConfig);
        console.log(defaultProject);
        firebase.analytics();

        firebase
            .auth()
            .signOut()
            .then(function () {
                alert("ok");
                window.location.href = "/";
            })
            .catch(function (error) {
                alert("ok 2");
                window.location.href = "/";
            });

        window.location.href = "/";
    </script>
    </body>
    </html>
<?php ob_end_flush(); ?>