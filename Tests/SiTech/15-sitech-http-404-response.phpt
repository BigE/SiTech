--TEST--
SiTech\HTTP\Response parsing and outputting a 404 error.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/HTTP/Response.php');

$response = 'HTTP/1.1 404 Not Found
Connection:Keep-Alive
Content-Length:234
Content-Type:text/html; charset=iso-8859-1
Date:Thu, 09 Jun 2011 14:09:08 GMT
Keep-Alive:timeout=15, max=97
Server:Apache/2.2.17 (Ubuntu)
Vary:Accept-Encoding

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /hoohah! was not found on this server.</p>
<hr>
<address>Apache/2.2.17 (Ubuntu) Server at localhost Port 80</address>
</body></html>';
$response = \SiTech\HTTP\Response::fromString($response);
var_dump($response);
$response->output();
?>
--EXPECT--
object(SiTech\HTTP\Response)#1 (5) {
  ["_body":protected]=>
  string(279) "<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /hoohah! was not found on this server.</p>
<hr>
<address>Apache/2.2.17 (Ubuntu) Server at localhost Port 80</address>
</body></html>"
  ["_code":protected]=>
  string(3) "404"
  ["_headers":protected]=>
  array(7) {
    ["connection"]=>
    string(10) "Keep-Alive"
    ["content-length"]=>
    string(3) "234"
    ["content-type"]=>
    string(29) "text/html; charset=iso-8859-1"
    ["date"]=>
    string(29) "Thu, 09 Jun 2011 14:09:08 GMT"
    ["keep-alive"]=>
    string(18) "timeout=15, max=97"
    ["server"]=>
    string(22) "Apache/2.2.17 (Ubuntu)"
    ["vary"]=>
    string(15) "Accept-Encoding"
  }
  ["_message":protected]=>
  string(9) "Not Found"
  ["_version":protected]=>
  string(3) "1.1"
}
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /hoohah! was not found on this server.</p>
<hr>
<address>Apache/2.2.17 (Ubuntu) Server at localhost Port 80</address>
</body></html>