silverstripe-oauth-client
====================


OAuth2 Client for SilverStripe
------------------------------

This module adds a OAuth2 client support to SilverStripe.
It uses THEPHPLEAGUE OAuth client (https://github.com/thephpleague/oauth2-client) to talk OAuth2 to Identity Providers.

This module does not let your SilverStripe be an OAuth Server. It currently does very basic user provisioning.

At the time of writing this, SilverStripe already had OAuth2 client support using this module (https://github.com/BetterBrief/silverstripe-opauth/). However, this has not been maintained or updated for over 8 months now. The original library Opauth is also suffering from lack of support and maintainability (https://github.com/opauth/opauth/issues/118)

Due to lack of maintainability, I decided to build this on top of THEPHPLEAGUE OAuth client library, which also supports proxy.
THEPHPLEAGUE OAuth client library also has third-party provider support.

Using SilverStripe content administration capabilities, and the help of this module. I have allowed for a GenericProvider to be added on the fly using CMS for any identity provider.

Configuration
-------------

You can install it using composer or just download it and extract it in to a folder called silverstripe-oauth-client.
Run composer install. This will get all dependencies such as thephpleague/oauth-client etc.
Run dev/build?flush=all as usual.
You should now see OAuth settings, visible on left hand side tab called OAuth. This is where you will need to add an OAuth Provider(s).

How to use
----------

After configuration, you should just be able to visit /oauth/login using web-browser to initiate the OAuth2 process.


Screenshots
-----------

![Adding OAuth Provider](https://raw.githubusercontent.com/shoaibali/silverstripe-oauth-client/master/images/screenshots/add-provider.png)

![Editing OAuth Provider](https://raw.githubusercontent.com/shoaibali/silverstripe-oauth-client/master/images/screenshots/edit-provider.png)
