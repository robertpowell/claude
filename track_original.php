<?php

// get the IP addres of the visitor
$remoteaddr=$_SERVER['REMOTE_ADDR'];

// do not perfrom look up for 

// Useragent code start
$query = http_build_query([
  'access_key' => 'access_key_here',
  'ua' => $_SERVER['HTTP_USER_AGENT'],
]);

$ch = curl_init('http://api.userstack.com/detect?' . $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$json = curl_exec($ch);
curl_close($ch);

$api_result = json_decode($json, true);

if ($api_result['device']['type'] === 'tablet') {
  // echo "It's a tablet";
} 
// echo '<pre>';
//print_r ($api_result);
// echo '</pre>';
// useragent code end


//assign values to variables
$os = $api_result['os']['family_code'];
$device = $api_result['device']['name'];
$browser = $api_result['browser']['name'];
$ismobile = $api_result['device']['is_mobile_device'];
$iscrawler = $api_result['crawler']['is_crawler'];

// set null values to something if needed
if ($ismobile<>1) {
    $ismobile=0;
    }
if ($iscrawler<>1) {
    $iscrawler=0;
    }

if (isset($_SERVER['HTTP_REFERER'])){
    $referrer=$_SERVER['HTTP_REFERER'];
}else{
    if(!isset($referrer)){
    $referrer="not_set";
    }
}
// remove fleetfoot clinic from the referrer string str_replace(find,replace,string,count) 

$referrer = str_replace("https://fleetfootclinic.co.uk/","",$referrer);

//$pagevisited = str_replace("/",".",$_SERVER[PHP_SELF],);

$pagevisited = stripslashes($_SERVER['PHP_SELF']);

// event needs to be in each page visited.
// ipaddress lookup code start
$ip = curl_init('http://ip-api.com/json/'.$remoteaddr);
curl_setopt($ip, CURLOPT_RETURNTRANSFER, true);
$ipjson = curl_exec($ip);
curl_close($ip);
$ipapi_result = json_decode($ipjson, true);

//echo '<pre>';
//print_r ($ipapi_result);
//echo '</pre>';

// ipaddress lookup code finish

if (isset($event)){
if ($event ==""){
$event="Not known";
}}else{
    $event="Not known";
}
// // echo $pagevisited;

$servername = "localhost";
$username = "fleetfootstats";
$password = "Qwerty*123==-";
$dbname = "visits";
$tablename = "events";

// change the table name if the request is from a bot
if ($iscrawler==1) {
  $tablename= "bots";
}

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// add a record
$sql = "INSERT INTO $tablename ( ipaddress, country, city, zip, pagevisited, referringpage, requestmethod, event, os, device, browser, ismobile, iscrawler) VALUES ('$remoteaddr', '$ipapi_result[countryCode]', '$ipapi_result[city]', '$ipapi_result[zip]' ,'$pagevisited', '$referrer', '$_SERVER[REQUEST_METHOD]', '$event', '$os', '$device', '$browser', '$ismobile', '$iscrawler' )";
if ($conn->query($sql) === TRUE) {
    //echo "New record created successfully";
} else {
    //echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();

?> 
