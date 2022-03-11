# wcomm_admin_cron_pick
A script for woocommerce users, to be run by cron and php-cli(?), to get a mail with a random product to review.
The database connection variables should be altered as needed.
The following example shows what the cron-job would do

Example:
/usr/local/bin/php /home/jon_deg_user/public_html/path/to/script.php

"Use php to run the script in the location /home..."

Background.
Having set up a few webshops and adding products I found that I could need a push from time to time to maintain the product info like price, category and stock.
Using the pseudo-random-number-generator available in php/linux I could get new product from the database with each call. The setup with cron-job that send out email from the script-output was allready set up in the host. So this is needed: a cron-job schedule that runs my script, and email function that emails the output.

Notes:
2022-03-11 making the repo. The script is in swedish. It was tested with misshosting.
