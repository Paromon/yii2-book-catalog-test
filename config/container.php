<?php

declare(strict_types=1);

use app\services\BookNotificationService;
use app\services\BookService;
use app\services\SmsPilotSender;
use app\services\SmsSenderInterface;
use yii\di\Container;
use yii\httpclient\Client;

return static function (Container $container): void {
    $container->setSingleton(Client::class, static fn () => new Client([
        'requestConfig' => [
            'options' => [
                'timeout' => 10,
                'connectTimeout' => 5,
            ],
        ],
    ]));

    $container->setSingleton(SmsSenderInterface::class, static function (Container $container) {
        return new SmsPilotSender(
            $container->get(Client::class),
            (string) env('SMSPILOT_API_KEY', ''),
            (string) env('SMSPILOT_API_URL', 'https://smspilot.ru/api.php'),
        );
    });

    $container->set(BookNotificationService::class);
    $container->set(BookService::class);
};
