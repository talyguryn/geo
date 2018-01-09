<?php

// использовать встроенный автозагрузчик, либо через composer
require 'vendor/autoload.php';

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //check ip from share internet
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //to check ip is pass from proxy
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

$token = include 'config.php';

$api = new \Yandex\Locator\Api($token);
// Определение местоположения по IP
$api->setIp(getRealIpAddr());

try {
    $api->load();
} catch (\Yandex\Locator\Exception\CurlError $ex) {
    // ошибка curl
    echo $ex->getMessage();
} catch (\Yandex\Locator\Exception\ServerError $ex) {
    // ошибка Яндекса
    echo $ex->getMessage();
} catch (\Yandex\Locator\Exception $ex) {
    // какая-то другая ошибка (запроса, например)
    echo $ex->getMessage();
}

$response = $api->getResponse();
// как определено положение
$response->getType();
// широта
$lat = $response->getLatitude();
// долгота
$lon = $response->getLongitude();

// сериализация/десереализация объекта
#var_dump(unserialize(serialize($response)));

?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>

<body>
<script type="text/javascript" src="https://api-maps.yandex.ru/2.1/?lang=en_RU"></script>
<style>
        html, body, #map {
            width: 100%; height: 100%; padding: 0; margin: 0;
        }
</style>
<div id="map"></div>

<script>
ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map('map', {
            center: [<?php echo $lat; ?>, <?php echo $lon; ?>],
            zoom: 9
        }, {
            searchControlProvider: 'yandex#search'
        }),
        myPlacemark = new ymaps.Placemark(myMap.getCenter());

    myMap.geoObjects.add(myPlacemark);

}
</script>
</body>
</html>
