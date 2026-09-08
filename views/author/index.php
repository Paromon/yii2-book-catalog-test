<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $provider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php if (!Yii::$app->user->isGuest): ?>
    <p><?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-primary']) ?></p>
<?php endif; ?>
<?= GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
        'full_name',
        [
            'label' => 'Подписка',
            'format' => 'raw',
            'value' => static fn ($model) => Html::a('Подписаться', ['/subscription/create', 'authorId' => $model->id]),
        ],
        [
            'class' => ActionColumn::class,
            'template' => Yii::$app->user->isGuest ? '{view}' : '{view} {update} {delete}',
        ],
    ],
]) ?>
