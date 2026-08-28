// Import Firebase scripts (v9 compat version)
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// Initialize Firebase
firebase.initializeApp({
  apiKey: "AIzaSyBkvOQCCUMwbIptWsJyir-6DstWtmRdOLI",
  authDomain: "go-rider-ea9a5.firebaseapp.com",
  projectId: "go-rider-ea9a5",
  storageBucket: "go-rider-ea9a5.appspot.com",
  messagingSenderId: "996129731043",
  appId: "1:996129731043:web:c800fb9c9b26e931e570d6"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  const title = payload.data?.title || 'Notification';
  const options = {
    body: payload.data?.body || '',
    data: { url: payload.data?.url || '/jobs' }
  };

  self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  const url = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (let client of windowClients) {
        if (client.url.includes(url) && 'focus' in client) {
          return client.focus();
        }
      }
      return clients.openWindow(url);
    })
  );
});

self.addEventListener('message', (event) => {
  if (event.data?.action === 'show-notification') {
    self.registration.showNotification(event.data.title, event.data.options);
  }
});
