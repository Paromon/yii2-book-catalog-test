<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\ReportForm $formModel */
/** @var int|string|null $year */
/** @var array<int, array{id: int|string, full_name: string, book_count: int|string}> $rows */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ТОП-10 авторов';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<p>Авторы, выпустившие больше всего книг за выбранный год.</p>

<form method="get" action="<?= Html::encode(Url::to(['top-authors'])) ?>" class="mb-4">
    <div class="mb-3">
        <label class="form-label" for="year">Год</label>
        <input class="form-control<?= $formModel->hasErrors('year') ? ' is-invalid' : '' ?>"
               type="number"
               id="year"
               name="year"
               value="<?= Html::encode((string) $year) ?>">
        <?php if ($formModel->hasErrors('year')): ?>
            <div class="invalid-feedback"><?= Html::encode($formModel->getFirstError('year')) ?></div>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Показать</button>
</form>

<?php if ($formModel->hasErrors()): ?>
    <div class="alert alert-danger">Укажите корректный год.</div>
<?php elseif ($rows === []): ?>
    <p>За <?= Html::encode((string) $year) ?> год книг не найдено.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Автор</th>
            <th>Книг</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $i => $row): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= Html::a(Html::encode($row['full_name']), ['/author/view', 'id' => $row['id']]) ?></td>
                <td><?= Html::encode((string) $row['book_count']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
