<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Author $author */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $author->isNewRecord ? 'Добавить автора' : 'Редактировать автора';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($author, 'full_name') ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $author->isNewRecord ? ['index'] : ['view', 'id' => $author->id], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
<?php ActiveForm::end(); ?>
