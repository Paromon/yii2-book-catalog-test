<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Book $book */

use yii\helpers\Html;

$this->title = $book->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($book->title) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $book->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $book->id], [
            'class' => 'btn btn-danger',
            'data' => ['method' => 'post', 'confirm' => 'Удалить книгу?'],
        ]) ?>
    </p>
<?php endif; ?>

<p><strong>Год:</strong> <?= Html::encode((string) $book->year) ?></p>
<p><strong>ISBN:</strong> <?= Html::encode($book->isbn) ?></p>
<p>
    <strong>Авторы:</strong>
    <?php foreach ($book->authors as $i => $author): ?>
        <?= $i > 0 ? ', ' : '' ?>
        <?= Html::a(Html::encode($author->full_name), ['/author/view', 'id' => $author->id]) ?>
    <?php endforeach; ?>
</p>
<?php if ($book->cover): ?>
    <p><?= Html::img($book->cover, ['class' => 'book-cover', 'alt' => $book->title]) ?></p>
<?php endif; ?>
<p><?= nl2br(Html::encode((string) $book->description)) ?></p>
