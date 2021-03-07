
# marketplace-web
A Marketplace connecting healthcare providers to local distributors to place orders online  (enable pharmacies to order online needed Items from local distributors to pharmacies)

# Cron Installation
Add this line to your crontab, replacing `path/to/app` with the path to your repository root

`crontab -e`

Add this line

`* * * * * cd /path/to/app; php index.php /cron`

# Notifications
Copy FCM variable values to `firebase-messaging-sw.js`