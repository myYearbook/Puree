Puree is a PHP-based service for combining/packing CSS and combining JS based on a specific path and query format

Configure Puree with configuration options in index.php

* ASSETS_URL - The URL where your assets live (if using relative URLs in CSS)
* INSTALL_PATH - The relative path to document root for your script installation
* PATH_PREFIX_CSS - The path relative to index.php where we can find CSS assets to include
* PATH_PREFIX_JS - The path relative to index.php where we can find JS assets to include
* Optional: function logError( ... ) - Define functionality for logging in case of error