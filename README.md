# HttpClient PHP Class
Http Lib in PHP with support of Guzzle-HTTP

The `HttpClient` class is a PHP class that simplifies HTTP requests using the GuzzleHttp library. This class provides a straightforward interface for making HTTP GET, POST, PUT, and DELETE requests, while also allowing customization of request parameters, headers, and cookie management.

## Installation

To use this class, you need to have the GuzzleHttp library installed. You can install it using Composer:

```bash
composer require osti/http-client-php
```

## Usage
Here's an example of how to use the `HttpClient` class to make an HTTP GET request:
```php
use HttpClient;

// Create an instance of HttpClient
$http = new HttpClient();

// Make an HTTP GET request
$response = $http->makeRequest('GET', 'https://www.example.com');

// Handle the response
if ($response !== false) {
    // The request was successful
    echo $response; // Response content
} else {
    // An error occurred during the request
    echo "Error during the request.";
}
```

# Main Methods
#### Constructor
The constructor of the `HttpClient` class accepts two optional parameters: `$cookies` and `$verify`. Setting $cookies to `true` will enable the use of cookies, and a temporary file will be created to store them. Setting `$verify` to `true` will enable SSL verification. 
Here's an example usage:
```php
$http = new HttpClient($cookies = true, $verify = true);

```

#### addHeaders(array $headers)
Adds `HTTP headers` to the request. 
Here's an example:
```php
$http->addHeaders([
    'User-Agent' => 'My User Agent',
    'Authorization' => 'Bearer Token123'
]);
```

#### setBody($params, $needEncode = true)
Sets the `HTTP request` body. You can specify the parameters to send in the request. If `$needEncode` is set to `true`, the parameters will be encoded as JSON. 
Here's an example:
```php
$http->setBody([
    'query' => ['key' => 'value'],
    'json' => ['data' => 'content']
]);
```

#### Other Methods
The `HttpClient` class also offers other useful methods, such as `hideHeaders()`, `setTimeout()`, `setOption()`, `deleteOption()` and more.


