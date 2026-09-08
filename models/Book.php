<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property string|null $cover
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 */
final class Book extends ActiveRecord
{
    /** @var int[] */
    public array $authorIds = [];

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['title', 'year', 'isbn', 'authorIds'], 'required'],
            [['title'], 'trim'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
            [['isbn'], 'trim'],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'match', 'pattern' => '/^[0-9\-Xx]{10,20}$/', 'message' => 'ISBN должен содержать 10–20 символов (цифры, дефис, X).'],
            [['isbn'], 'unique'],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['cover'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover' => 'Обложка',
            'authorIds' => 'Авторы',
        ];
    }

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }
}
