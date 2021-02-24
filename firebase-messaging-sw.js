// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.2.9/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.9/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyBvHrrg5DEsM_evTfPiabFBx-Af430kYwQ",
    authDomain: "aumet-marketplace-dev.firebaseapp.com",
    projectId: "aumet-marketplace-dev",
    storageBucket: "aumet-marketplace-dev.appspot.com",
    messagingSenderId: "1014458021475",
    appId: "1:1014458021475:web:53d2672ca6296c3503ea56"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();