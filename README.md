spidertracks-php
================

This class allows for quick interaction with the AFF API from SpiderTracks, specified by https://support.spidertracks.com/hc/en.../AFF_and_API_User_Guide.pdf.

Requirements
------------

1. PHP 5.3+ (tested with 5.4.24)
2. curl
3. SimpleXML
4. An API-enabled SpiderTracks account

Usage
-----

First, require SpiderTracksClient.php. Then instantiate the client class as follows:

````
$stClient = new SpiderTracksClient('username', 'password');
````

where the username and password are the credentials used to log into http://go.spidertracks.com.

To get track data since a given date, pass in a DateTime to getSince:

````
$positions = $stClient->getSince(new DateTime('May 28 2014 12:00:00'));
````

This will return an array of stdClass objects, each of which will have the following properties:

1. imei - SpiderTracks device IMEI
2. date - position capture date, as a DateTime
3. latitude - in decimal degrees
4. longitude - in decimal degrees
5. altitude - in meters
6. speed - in meters/second
7. heading - in degrees

Known Issues
------------

Currently there is no special handling for record counts at or above 1000 (the API will only show 999 positions at a time). The easiest workaround is to, for instances where the response array is that length, take the date field from the last entry and use it as the parameter for another call (or many) to getSince().

Also, in testing I'm getting what appears to be heartbeat/sample data back from the API. This may be an account setup issue rather than a client issue.

Other Notes
-----------

The SpiderTracks client uses SpiderTracksClient::format() method to convert received XML to the final array-of-objects form. This method is public, so a cached response can be fed into the parser (vs. making another web request).

This library was built for use in http://limitless-horizons.org.
