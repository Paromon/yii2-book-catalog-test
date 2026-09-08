<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Author $author */

use yii\helpers\Html;

$this->title = $author->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($author->full_name) ?></h1>

<p>
    <?= Html::a('Подписаться на новые книги', ['/subscription/create', 'authorId' => $author->id], ['class' => 'btn btn-outline-primary']) ?>
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= Html::a('Редактировать', ['update', 'id' => $author->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $author->id], [
            'class' => 'btn btn-danger',
            'data' => ['method' => 'post', 'confirm' => 'Удалить автора?'],
        ]) ?>
    <?php endif; ?>
</p>

<h2>Книги автора</h2>
<?php if ($author->books === []): ?>
    <p>Книг пока нет.</p>
<?php else: ?>
    <ul>
        <?php foreach ($author->books as $book): ?>
            <li><?= Html::a(Html::encode($book->title . ' (' . $book->year . ')'), ['/book/view', 'id' => $book->id]) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
