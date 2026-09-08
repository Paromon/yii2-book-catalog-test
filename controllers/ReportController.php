<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ReportForm;
use app\services\AuthorReportService;
use Yii;
use yii\web\Controller;

final class ReportController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly AuthorReportService $reportService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionTopAuthors(): string
    {
        $form = new ReportForm();
        $form->year = Yii::$app->request->get('year', date('Y'));
        $rows = [];
        if ($form->validate()) {
            $rows = $this->reportService->topByYear((int) $form->year);
        }

        return $this->render('top-authors', [
            'formModel' => $form,
            'year' => $form->year,
            'rows' => $rows,
        ]);
    }
}
