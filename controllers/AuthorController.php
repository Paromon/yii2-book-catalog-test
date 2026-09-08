<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Author;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class AuthorController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'provider' => new ActiveDataProvider([
                'query' => Author::find(),
                'pagination' => ['pageSize' => 20],
                'sort' => [
                    'defaultOrder' => ['full_name' => SORT_ASC],
                    'attributes' => [
                        'full_name',
                    ],
                ],
            ]),
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', [
            'author' => $this->findAuthor($id),
        ]);
    }

    public function actionCreate(): string|Response
    {
        $author = new Author();
        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            return $this->redirect(['view', 'id' => $author->id]);
        }

        return $this->render('form', ['author' => $author]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $author = $this->findAuthor($id);
        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            return $this->redirect(['view', 'id' => $author->id]);
        }

        return $this->render('form', ['author' => $author]);
    }

    public function actionDelete(int $id): Response
    {
        $author = $this->findAuthor($id);
        if ($author->getBooks()->exists()) {
            Yii::$app->session->setFlash('error', 'Нельзя удалить автора, у которого есть книги.');

            return $this->redirect(['view', 'id' => $author->id]);
        }

        try {
            $author->delete();
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error', 'Нельзя удалить автора, у которого есть книги.');

            return $this->redirect(['view', 'id' => $author->id]);
        }

        return $this->redirect(['index']);
    }

    private function findAuthor(int $id): Author
    {
        $author = Author::find()->with('books')->where(['id' => $id])->one();
        if ($author === null) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        return $author;
    }
}
