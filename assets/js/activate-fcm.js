function activateFirebase(credentials) {
    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: credentials.apiKey,
        authDomain: credentials.authDomain,
        projectId: credentials.projectId,
        storageBucket: credentials.storageBucket,
        messagingSenderId: credentials.messagingSenderId,
        appId: credentials.appId,
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(enabled => {})
        .catch(error => {});

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