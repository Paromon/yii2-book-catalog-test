<?php

declare(strict_types=1);

namespace app\commands;

use app\models\Author;
use app\models\AuthorSubscription;
use app\models\Book;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

final class SeedController extends Controller
{
    public function actionDemo(): int
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->ensureUser();
            $authors = $this->ensureAuthors();
            $this->ensureBooks($authors);
            $this->ensureSubscription($authors['tolstoy']);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Демо-данные готовы. Логин: admin / пароль: admin\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    private function ensureUser(): void
    {
        if (User::findByUsername('admin') !== null) {
            return;
        }

        $user = new User();
        $user->username = 'admin';
        $user->setPassword('admin');
        $user->generateAuthKey();
        if (!$user->save()) {
            throw new \RuntimeException('Не удалось создать пользователя admin.');
        }
    }

    /**
     * @return array{tolstoy: Author, dostoevsky: Author, chekhov: Author}
     */
    private function ensureAuthors(): array
    {
        $names = [
            'tolstoy' => 'Лев Николаевич Толстой',
            'dostoevsky' => 'Фёдор Михайлович Достоевский',
            'chekhov' => 'Антон Павлович Чехов',
        ];

        $authors = [];
        foreach ($names as $key => $fullName) {
            $author = Author::findOne(['full_name' => $fullName]);
            if ($author === null) {
                $author = new Author();
                $author->full_name = $fullName;
                if (!$author->save()) {
                    throw new \RuntimeException('Не удалось создать автора: ' . $fullName);
                }
            }
            $authors[$key] = $author;
        }

        return $authors;
    }

    /**
     * @param array{tolstoy: Author, dostoevsky: Author, chekhov: Author} $authors
     */
    private function ensureBooks(array $authors): void
    {
        $books = [
            [
                'title' => 'Война и мир',
                'year' => 1869,
                'isbn' => '978-5-17-000001',
                'description' => 'Роман-эпопея.',
                'authors' => [$authors['tolstoy']],
            ],
            [
                'title' => 'Анна Каренина',
                'year' => 1877,
                'isbn' => '978-5-17-000002',
                'description' => 'Роман.',
                'authors' => [$authors['tolstoy']],
            ],
            [
                'title' => 'Преступление и наказание',
                'year' => 1866,
                'isbn' => '978-5-17-000003',
                'description' => 'Роман.',
                'authors' => [$authors['dostoevsky']],
            ],
            [
                'title' => 'Сборник классики',
                'year' => 1869,
                'isbn' => '978-5-17-000004',
                'description' => 'Учебный пример книги с несколькими авторами.',
                'authors' => [$authors['tolstoy'], $authors['dostoevsky'], $authors['chekhov']],
            ],
            [
                'title' => 'Вишнёвый сад',
                'year' => 1904,
                'isbn' => '978-5-17-000005',
                'description' => 'Пьеса.',
                'authors' => [$authors['chekhov']],
            ],
        ];

        foreach ($books as $data) {
            $book = Book::findOne(['isbn' => $data['isbn']]);
            if ($book === null) {
                $book = new Book();
                $book->title = $data['title'];
                $book->year = $data['year'];
                $book->isbn = $data['isbn'];
                $book->description = $data['description'];
                $book->authorIds = array_map(static fn (Author $author) => (int) $author->id, $data['authors']);
                if (!$book->save()) {
                    throw new \RuntimeException('Не удалось создать книгу: ' . $data['title']);
                }
            }

            $book->unlinkAll('authors', true);
            foreach ($data['authors'] as $author) {
                $book->link('authors', $author);
            }
        }
    }

    private function ensureSubscription(Author $author): void
    {
        $phone = '+79001234567';
        $exists = AuthorSubscription::find()
            ->where(['author_id' => $author->id, 'phone' => $phone])
            ->exists();
        if ($exists) {
            return;
        }

        $subscription = new AuthorSubscription();
        $subscription->author_id = $author->id;
        $subscription->phone = $phone;
        if (!$subscription->save()) {
            throw new \RuntimeException('Не удалось создать подписку.');
        }
    }
}
