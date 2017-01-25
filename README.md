ACGS
===============================
Sending contacts from ActiveCampaign to Google Sheet via webhook, using API of both systems.

INSTALLATION
-------------------

Step 1: Turn on the Google Sheets API

1. Use this wizard to create or select a project in the Google Developers Console and automatically turn on the API. Click Continue, then Go to credentials.
2. On the Add credentials to your project page, click the Cancel button.
3. At the top of the page, select the OAuth consent screen tab. Select an Email address, enter a Product name if not already set, and click the Save button.
4. Select the Credentials tab, click the Create credentials button and select OAuth client ID.
5. Select the application type Other, enter the name "Google Sheets API Quickstart", and click the Create button.
6. Click OK to dismiss the resulting dialog.
7. Click the file_download (Download JSON) button to the right of the client ID.
8. Move this file to your working directory and rename it client_secret.json.

Step 2: Install the Google Client Library

Run the following command to install the library using composer:
php composer.phar require google/apiclient:^2.0