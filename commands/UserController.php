<?php

declare(strict_types=1);

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

final class UserController extends Controller
{
    public function actionCreate(string $username, string $password): int
    {
        if (User::findByUsername($username) !== null) {
            $this->stderr("Пользователь {$username} уже существует.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user = new User();
        $user->username = $username;
        $user->setPassword($password);
        $user->generateAuthKey();
        if (!$user->save()) {
            $this->stderr("Не удалось создать пользователя.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Пользователь {$username} создан.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }
}
