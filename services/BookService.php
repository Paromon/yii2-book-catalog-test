<?php

declare(strict_types=1);

namespace app\services;

use app\models\Author;
use app\models\Book;
use app\models\BookForm;
use RuntimeException;
use Throwable;
use Yii;
use yii\db\IntegrityException;
use yii\web\UploadedFile;

final class BookService
{
    public function __construct(private readonly BookNotificationService $notificationService)
    {
    }

    public function create(BookForm $form): Book
    {
        $book = new Book();
        $this->save($book, $form);
        $this->notificationService->notifyAboutNewBook($book);

        return $book;
    }

    public function update(Book $book, BookForm $form): Book
    {
        $this->save($book, $form);

        return $book;
    }

    public function delete(Book $book): void
    {
        $cover = $book->cover;
        if (!$book->delete()) {
            throw new RuntimeException('Unable to delete book.');
        }
        $this->deleteCoverFile($cover);
    }

    private function save(Book $book, BookForm $form): void
    {
        $book->setAttributes([
            'title' => $form->title,
            'year' => $form->year,
            'description' => $form->description,
            'isbn' => $form->isbn,
        ]);
        $book->authorIds = array_values(array_unique(array_map('intval', $form->authorIds)));

        if (!$book->validate()) {
            $this->copyErrors($book, $form);
            throw new RuntimeException('Book validation failed.');
        }

        $oldCover = $book->cover;
        $newCover = null;
        if ($form->cover instanceof UploadedFile) {
            $newCover = $this->storeCover($form);
            $book->cover = $newCover;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$book->save(false)) {
                throw new RuntimeException('Unable to save book.');
            }

            $this->syncAuthors($book, $form);
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            if ($newCover !== null) {
                $this->deleteCoverFile($newCover);
                $book->cover = $oldCover;
            }
            if ($e instanceof IntegrityException && str_contains($e->getMessage(), 'ux_book_isbn')) {
                $form->addError('isbn', 'Книга с таким ISBN уже существует.');
            }
            throw $e;
        }

        if ($newCover !== null && $oldCover !== null && $oldCover !== $newCover) {
            $this->deleteCoverFile($oldCover);
        }
    }

    private function syncAuthors(Book $book, BookForm $form): void
    {
        $book->unlinkAll('authors', true);
        $authors = Author::find()->where(['id' => $book->authorIds])->all();
        if ($book->authorIds === [] || count($authors) !== count($book->authorIds)) {
            $form->addError('authorIds', 'Выберите существующих авторов.');
            throw new RuntimeException('Invalid authors.');
        }

        foreach ($authors as $author) {
            $book->link('authors', $author);
        }
    }

    private function copyErrors(Book $book, BookForm $form): void
    {
        foreach ($book->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                $form->addError($attribute, $error);
            }
        }
    }

    private function storeCover(BookForm $form): string
    {
        if (!$form->cover instanceof UploadedFile) {
            throw new RuntimeException('Cover file is missing.');
        }

        $directory = Yii::getAlias('@webroot/uploads');
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $extension = strtolower((string) $form->cover->extension);
        $filename = Yii::$app->security->generateRandomString(16) . '.' . $extension;
        $absolutePath = $directory . DIRECTORY_SEPARATOR . $filename;
        if (!$form->cover->saveAs($absolutePath)) {
            throw new RuntimeException('Unable to save cover image.');
        }

        return '/uploads/' . $filename;
    }

    private function deleteCoverFile(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $directory = Yii::getAlias('@webroot/uploads');
        $absolutePath = $directory . DIRECTORY_SEPARATOR . basename($path);
        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}
