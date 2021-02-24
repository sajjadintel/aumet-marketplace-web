function activateFirebase() {
    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: "AIzaSyBvHrrg5DEsM_evTfPiabFBx-Af430kYwQ",
        authDomain: "aumet-marketplace-dev.firebaseapp.com",
        projectId: "aumet-marketplace-dev",
        storageBucket: "aumet-marketplace-dev.appspot.com",
        messagingSenderId: "1014458021475",
        appId: "1:1014458021475:web:53d2672ca6296c3503ea56"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(enabled => {})
        .catch((error) => {
            alert('Notifications Refused');
        });

    messaging.getToken()
        .then(token => {
            $.ajax({
                url: '/web/me/update-token',
                method: 'POST',
                data: {
                    fcm_token: token
                }
            });
        });
}