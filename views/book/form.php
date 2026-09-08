<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\BookForm $formModel */
/** @var array<int, string> $authors */
/** @var app\models\Book|null $book */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $book ? 'Редактировать книгу' : 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->errorSummary($formModel) ?>
    <?= $form->field($formModel, 'title') ?>
    <?= $form->field($formModel, 'year')->input('number') ?>
    <?= $form->field($formModel, 'isbn') ?>
    <?= $form->field($formModel, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($formModel, 'authorIds')->checkboxList($authors) ?>
    <?php if ($book && $book->cover): ?>
        <p>Текущая обложка:</p>
        <p><?= Html::img($book->cover, ['class' => 'book-cover', 'alt' => $book->title]) ?></p>
    <?php endif; ?>
    <?= $form->field($formModel, 'cover')->fileInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $book ? ['view', 'id' => $book->id] : ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
<?php ActiveForm::end(); ?>
