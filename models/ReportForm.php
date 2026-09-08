<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

final class ReportForm extends Model
{
    public int|string|null $year = null;

    public function rules(): array
    {
        return [
            [['year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'year' => 'Год',
        ];
    }
}
