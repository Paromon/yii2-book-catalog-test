<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Author $author */
/** @var app\models\AuthorSubscription $subscription */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Подписка на автора';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['/author/index']];
$this->params['breadcrumbs'][] = ['label' => $author->full_name, 'url' => ['/author/view', 'id' => $author->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<p>Автор: <?= Html::encode($author->full_name) ?></p>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($subscription) ?>
    <?= $form->field($subscription, 'phone')->textInput(['placeholder' => '+79991234567']) ?>
    <div class="form-group">
        <?= Html::submitButton('Подписаться', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
