<?php

declare(strict_types=1);

namespace app\services;

use app\models\AuthorSubscription;
use app\models\Book;
use Throwable;
use Yii;

final class BookNotificationService
{
    public function __construct(private readonly SmsSenderInterface $smsSender)
    {
    }

    public function notifyAboutNewBook(Book $book): void
    {
        $phones = AuthorSubscription::find()
            ->alias('s')
            ->select('s.phone')
            ->distinct()
            ->innerJoin('{{%book_author}} ba', 'ba.author_id = s.author_id')
            ->where(['ba.book_id' => $book->id])
            ->column();

        foreach ($phones as $phone) {
            try {
                $this->smsSender->send(
                    (string) $phone,
                    sprintf('Новая книга: %s (%s)', $book->title, $book->year)
                );
            } catch (Throwable $e) {
                Yii::error([
                    'msg' => 'Failed to send SMS notification for a new book.',
                    'bookId' => $book->id,
                    'error' => $e->getMessage(),
                ], __METHOD__);
            }
        }
    }
}
