# Book Catalog

Каталог книг на Yii2 Basic. Гости просматривают книги, авторов и отчёт TOP-10, а также подписываются на автора по телефону. Авторизованные пользователи ведут CRUD книг и авторов. При создании книги подписчики авторов получают SMS через SMSPilot.

## Requirements

* Docker
* Docker Compose

## Installation

```bash
cp .env.example .env
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php yii migrate --interactive=0
docker compose exec app php yii seed/demo
```

В `.env` задайте `COOKIE_VALIDATION_KEY` (любая случайная строка) и при необходимости свой `SMSPILOT_API_KEY`. Для локальной разработки достаточно test key из `.env.example`.

URL: [http://localhost:8080](http://localhost:8080)

## Demo user

После `php yii seed/demo`:

* логин: `admin`
* пароль: `admin`

Создать пользователя вручную:

```bash
docker compose exec app php yii user/create admin admin
```

## Что проверить

* Каталог книг и карточка книги — без логина
* Авторы и подписка по телефону — без регистрации
* Отчёт TOP-10: [http://localhost:8080/report](http://localhost:8080/report)
* CRUD книг/авторов — после входа
* Новая книга с несколькими авторами отправляет SMS через SMSPilot emulator; ошибка SMS не откатывает сохранение книги

## Stack

* PHP 8.2, Yii2 Basic, MySQL 8
* SMSPilot test key задаётся в `.env` (`SMSPILOT_API_KEY`)
