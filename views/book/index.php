<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $provider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php if (!Yii::$app->user->isGuest): ?>
    <p><?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-primary']) ?></p>
<?php endif; ?>
<?= GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
        [
            'attribute' => 'title',
        ],
        [
            'attribute' => 'year',
        ],
        [
            'attribute' => 'isbn',
        ],
        [
            'label' => 'Авторы',
            'value' => static fn ($model) => implode(', ', array_map(static fn ($author) => $author->full_name, $model->authors)),
        ],
        [
            'class' => ActionColumn::class,
            'template' => Yii::$app->user->isGuest ? '{view}' : '{view} {update} {delete}',
        ],
    ],
]) ?>
