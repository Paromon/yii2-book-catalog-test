<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use app\models\BookForm;
use app\services\BookService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class BookController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly BookService $bookService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                ]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $provider = new ActiveDataProvider([
            'query' => Book::find()->with('authors'),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'title',
                    'year',
                    'isbn',
                ],
            ],
        ]);

        return $this->render('index', ['provider' => $provider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['book' => $this->findBook($id)]);
    }

    public function actionCreate(): string|Response
    {
        $form = new BookForm();
        if ($form->loadPosted(Yii::$app->request->post())) {
            if ($form->validate()) {
                try {
                    $book = $this->bookService->create($form);

                    return $this->redirect(['view', 'id' => $book->id]);
                } catch (Throwable $e) {
                    if (!$form->hasErrors()) {
                        Yii::error($e->getMessage(), __METHOD__);
                        $form->addError('', 'Не удалось сохранить книгу.');
                    }
                }
            }
        }

        return $this->render('form', [
            'formModel' => $form,
            'authors' => $this->authorList(),
            'book' => null,
        ]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $book = $this->findBook($id);
        $form = BookForm::fromBook($book);
        if ($form->loadPosted(Yii::$app->request->post())) {
            if ($form->validate()) {
                try {
                    $this->bookService->update($book, $form);

                    return $this->redirect(['view', 'id' => $book->id]);
                } catch (Throwable $e) {
                    if (!$form->hasErrors()) {
                        Yii::error($e->getMessage(), __METHOD__);
                        $form->addError('', 'Не удалось сохранить книгу.');
                    }
                }
            }
        }

        return $this->render('form', [
            'formModel' => $form,
            'authors' => $this->authorList(),
            'book' => $book,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->bookService->delete($this->findBook($id));

        return $this->redirect(['index']);
    }

    /**
     * @return array<int, string>
     */
    private function authorList(): array
    {
        return Author::find()->select('full_name')->indexBy('id')->orderBy(['full_name' => SORT_ASC])->column();
    }

    private function findBook(int $id): Book
    {
        $book = Book::find()->with('authors')->where(['id' => $id])->one();
        if ($book === null) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        return $book;
    }
}
