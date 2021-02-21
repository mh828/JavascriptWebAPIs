<?php

use Minishlink\WebPush\Subscription as SubscriptionAlias;

include 'vendor/autoload.php';


if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    file_put_contents('endpoint.json', file_get_contents('php://input'));
}



if(file_exists('endpoint.json')){
    header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    $subscription = SubscriptionAlias::create([
        'endpoint' => $content->endpoint,
        'publicKey' => $content->keys->p256dh,
        'authToken' => $content->keys->auth
    ]);
    $webpush = new \Minishlink\WebPush\WebPush();
    $webpush->sendOneNotification($subscription,"Are You Ok");
}