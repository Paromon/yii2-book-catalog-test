<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

final class BookForm extends Model
{
    public ?int $id = null;
    public string $title = '';
    public int|string|null $year = null;
    public ?string $description = null;
    public string $isbn = '';
    /** @var int[] */
    public array $authorIds = [];
    public ?UploadedFile $cover = null;

    public function rules(): array
    {
        return [
            [['title', 'year', 'isbn', 'authorIds'], 'required'],
            [['cover'], 'required', 'when' => static fn (self $model) => $model->id === null, 'message' => 'Загрузите фото главной страницы.'],
            [['title', 'isbn'], 'trim'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'match', 'pattern' => '/^[0-9\-Xx]{10,20}$/', 'message' => 'ISBN должен содержать 10–20 символов (цифры, дефис, X).'],
            [
                ['isbn'],
                'unique',
                'targetClass' => Book::class,
                'targetAttribute' => 'isbn',
                'filter' => function ($query) {
                    if ($this->id !== null) {
                        $query->andWhere(['<>', 'id', $this->id]);
                    }
                },
                'message' => 'Книга с таким ISBN уже существует.',
            ],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [
                ['cover'],
                'file',
                'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/webp'],
                'maxSize' => 5 * 1024 * 1024,
                'checkExtensionByMimeType' => true,
                'skipOnEmpty' => true,
                'wrongExtension' => 'Допустимы файлы png, jpg, jpeg, webp.',
                'wrongMimeType' => 'Недопустимый тип файла обложки.',
                'tooBig' => 'Размер файла не должен превышать 5 МБ.',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'authorIds' => 'Авторы',
            'cover' => 'Обложка',
        ];
    }

    public static function fromBook(Book $book): self
    {
        $form = new self();
        $form->id = (int) $book->id;
        $form->title = $book->title;
        $form->year = $book->year;
        $form->description = $book->description;
        $form->isbn = $book->isbn;
        if ($book->isRelationPopulated('authors')) {
            $form->authorIds = array_map(static fn (Author $author) => (int) $author->id, $book->authors);
        } else {
            $form->authorIds = array_map('intval', $book->getAuthors()->select('id')->column());
        }

        return $form;
    }
}
