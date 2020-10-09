<!DOCTYPE html>
<html lang="<?php echo $_SESSION['userLang'] ?>" dir="<?php echo $_SESSION['userLangDirection'] ?>" direction="<?php echo $_SESSION['userLangDirection'] ?>" style="direction: <?php echo $_SESSION['userLangDirection'] ?>">
<!--begin::Head-->

<head>
	<base href="/theme/">
	<meta charset="utf-8" />
	<title><?php echo $vTitle; ?></title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<link rel="canonical" href="https://marketplace.aumet.tech" />
	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap">
	<!--end::Fonts-->
	<!--begin::Page Custom Styles(used by this page)-->
	<link href="assets/css/pages/login/login-3<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
	<!--end::Page Custom Styles-->
	<!--begin::Global Theme Styles(used by all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles-->
	<!--begin::Layout Themes(used by all pages)-->
	<link href="assets/css/themes/layout/header/base/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/themes/layout/header/menu/light<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/themes/layout/brand/dark<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/themes/layout/aside/dark<?php echo $_SESSION['userLangDirection'] == "ltr" ? "" : ".rtl" ?>.css" rel="stylesheet" type="text/css" />
	<!--end::Layout Themes-->

	<link href="/assets/css/auth.css<?php echo $platformVersion ?>" rel="stylesheet" type="text/css" />

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
	<meta name="theme-color" content="#ffffff">
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

	<!-- The core Firebase JS SDK is always required and must be listed first -->
	<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js"></script>

	<!-- TODO: Add SDKs for Firebase products that you want to use
	 https://firebase.google.com/docs/web/setup#available-libraries -->
	<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-auth.js"></script>
	<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-analytics.js"></script>

	<script>
		// Your web app's Firebase configuration
		var firebaseConfig = {
			apiKey: "AIzaSyApi3WBeQ3HmB_we8CSOF8k1qgU1SEpxao",
			authDomain: "aumet-marketplace.firebaseapp.com",
			databaseURL: "https://aumet-marketplace.firebaseio.com",
			projectId: "aumet-marketplace",
			storageBucket: "aumet-marketplace.appspot.com",
			messagingSenderId: "418237979621",
			appId: "1:418237979621:web:5fe1d4393d8676f5a3ad0c",
			measurementId: "G-QEWB1B33ZE"
		};

		// Initialize Firebase
		firebase.initializeApp(firebaseConfig);
		firebase.analytics();
	</script>

	<?php include_once "$vAuthFile.php" ?>

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
			"font-family": "Poppins"
		};
	</script>
	<!--end::Global Config-->
	<!--begin::Global Theme Bundle(used by all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Theme Bundle-->
	<!--begin::Page Scripts(used by this page)-->
	<script>
		var docLang = "<?php echo $_SESSION['userLang'] ?>";
	</script>
	<script src="/assets/js/locals.js<?php echo $platformVersion ?>"></script>
	<script src="/assets/js/auth.js<?php echo $platformVersion ?>"></script>
	<!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>