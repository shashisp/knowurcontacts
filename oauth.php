<html>
<head>

<title>GetyourContacts</title>
    <link rel="stylesheet" href="http://bootswatch.com/flatly/bootstrap.css" media="screen">
    <link rel="stylesheet" href="http://bootswatch.com/bower_components/font-awesome/css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="http://bootswatch.com/assets/css/bootswatch.min.css">
</head>
<body>
	
<?php
$client_id='133186028197-lpsptdcfl3jhv9dpgft7jadi5aure6du.apps.googleusercontent.com';
$client_secret='-owDbyzAqae50_sJG8t0hypU';
$redirect_uri='http://getcontacts.ap01.aws.af.cm/oauth.php';
$max_results = 100;
 
$auth_code = $_GET["code"];  
 
function curl_file_get_contents($url)
{
 $curl = curl_init();
 $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
 
 curl_setopt($curl,CURLOPT_URL,$url);	//The URL to fetch. This can also be set when initializing a session with curl_init().
 curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);	//TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
 curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);	//The number of seconds to wait while trying to connect.	
 
 curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);	//The contents of the "User-Agent: " header to be used in a HTTP request.
 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);	//To follow any "Location: " header that the server sends as part of the HTTP header.
 curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);	//To automatically set the Referer: field in requests where it follows a Location: redirect.
 curl_setopt($curl, CURLOPT_TIMEOUT, 10);	//The maximum number of seconds to allow cURL functions to execute.
 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);	//To stop cURL from verifying the peer's certificate.
 
 $contents = curl_exec($curl);
 curl_close($curl);
 return $contents;
}
 
$fields=array(
    'code'=>  urlencode($auth_code),
    'client_id'=>  urlencode($client_id),
    'client_secret'=>  urlencode($client_secret),
    'redirect_uri'=>  urlencode($redirect_uri),
    'grant_type'=>  urlencode('authorization_code')
);
$post = '';
foreach($fields as $key=>$value) { $post .= $key.'='.$value.'&'; }
$post = rtrim($post,'&');
 
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL,'https://accounts.google.com/o/oauth2/token');
curl_setopt($curl,CURLOPT_POST,5);
curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);
$result = curl_exec($curl);
curl_close($curl);
 
$response =  json_decode($result);
$accesstoken = $response->access_token;
 
$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&oauth_token='.$accesstoken;
$xmlresponse =  curl_file_get_contents($url);

$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&alt=json&v=3.0&oauth_token='.$accesstoken;
$xmlresponse =  curl_file_get_contents($url);
 
$temp = json_decode($xmlresponse,true);


foreach($temp['feed']['entry'] as $cnt) {
    echo $cnt['title']['$t'] . " |- Email: " . $cnt['gd$email']['0']['address'];
    if(isset($cnt['gd$phoneNumber'])) echo " Phone " . $cnt['gd$phoneNumber'][0]['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$street'])) echo " -Street " . $cnt['gd$structuredPostalAddress'][0]['gd$street']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$neighborhood'])) echo " --- " . $cnt['gd$structuredPostalAddress'][0]['gd$neighborhood']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$pobox'])) echo " --- " . $cnt['gd$structuredPostalAddress'][0]['gd$pobox']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$postcode'])) echo " -Postcode " . $cnt['gd$structuredPostalAddress'][0]['gd$postcode']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$city'])) echo " -City " . $cnt['gd$structuredPostalAddress'][0]['gd$city']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$region'])) echo " -Region " . $cnt['gd$structuredPostalAddress'][0]['gd$region']['$t'];
    if(isset($cnt['gd$structuredPostalAddress'][0]['gd$country'])) echo " -Country " . $cnt['gd$structuredPostalAddress'][0]['gd$country']['$t'];
    echo "</br>";
}



?>
	</div>
</body></html>