<?php


require '../vendor/autoload.php'; // Make sure Guzzle is installed and included

use GuzzleHttp\Client;

$client = new Client();
function getClientIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$ip = '8.8.8.8';
// Function to get city from IP using ipwho.is API
function getCityFromIP($ip, $client)
{
    $response = $client->request('GET', "https://ipwho.is/{$ip}");
    $result = json_decode($response->getBody(), true);
    return $result['city'];
}
function getVisitorName()
{
    return isset($_GET['visitor_name']) ? htmlspecialchars($_GET['visitor_name']) : 'Guest';
}

function getLocation($ip)
{
    $json = file_get_contents("http://ipinfo.io/{$ip}/json");
    $details = json_decode($json);
    return $details->city ?? 'Unknown';
}

// Function to get weather information for a city using OpenWeatherMap API
function getWeather($city, $client)
{
    $apiKey = 'b726f4d53e9b8b842c8f85302165c06e';
    $units = 'metric';
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&units={$units}&appid={$apiKey}";

    $response = $client->request('GET', $url);
    $result = json_decode($response->getBody(), true);

    return $result['main']['temp'];
}
// Retrieve client information
$clientIP = getClientIP();
$city = getCityFromIP($ip, $client);
$temperature = getWeather($city, $client);
$visitorName = getVisitorName();

header('Content-Type: application/json');
echo json_encode([
    'client_ip' => $clientIP,
    'location' => $city,
    'greeting' => "Hello, $visitorName! The temperature is $temperature degrees Celsius in $city"
]);
