--TEST--
SiTech\HTTP\Response for a 200 OK page
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/HTTP/Response.php');

$response = 'HTTP/1.1 200 OK
Date: Wed, 08 Jun 2011 21:45:17 GMT
Server: Apache/2.2.3 (CentOS)
X-Powered-By: PHP/5.3.5 ZendServer/5.0
Content-Language: en
Connection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=UTF-8

<html>
	<head>
		<title>Whee!</title>
	</head>
	<body>
		Hello World!
	</body>
</html>
';
$response = \SiTech\HTTP\Response::fromString($response);
var_dump($response);
$response->output();
?>
--EXPECT--
object(SiTech\HTTP\Response)#1 (5) {
  ["_body":protected]=>
  string(87) "<html>
	<head>
		<title>Whee!</title>
	</head>
	<body>
		Hello World!
	</body>
</html>
"
  ["_code":protected]=>
  string(3) "200"
  ["_headers":protected]=>
  array(7) {
    ["date"]=>
    string(30) " Wed, 08 Jun 2011 21:45:17 GMT"
    ["server"]=>
    string(22) " Apache/2.2.3 (CentOS)"
    ["x-powered-by"]=>
    string(25) " PHP/5.3.5 ZendServer/5.0"
    ["content-language"]=>
    string(3) " en"
    ["connection"]=>
    string(6) " close"
    ["transfer-encoding"]=>
    string(8) " chunked"
    ["content-type"]=>
    string(25) " text/html; charset=UTF-8"
  }
  ["_message":protected]=>
  string(2) "OK"
  ["_version":protected]=>
  string(3) "1.1"
}
<html>
	<head>
		<title>Whee!</title>
	</head>
	<body>
		Hello World!
	</body>
</html>