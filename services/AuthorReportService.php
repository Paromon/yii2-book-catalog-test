<?php

declare(strict_types=1);

namespace app\services;

use app\models\Author;

final class AuthorReportService
{
    /**
     * @return array<int, array{id: int|string, full_name: string, book_count: int|string}>
     */
    public function topByYear(int $year): array
    {
        return Author::find()
            ->alias('a')
            ->select([
                'a.id',
                'a.full_name',
                'book_count' => 'COUNT(DISTINCT b.id)',
            ])
            ->innerJoin('{{%book_author}} ba', 'ba.author_id = a.id')
            ->innerJoin('{{%book}} b', 'b.id = ba.book_id')
            ->where(['b.year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['book_count' => SORT_DESC, 'a.full_name' => SORT_ASC])
            ->limit(10)
            ->asArray()
            ->all();
    }
}
