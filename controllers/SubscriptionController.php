<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Author;
use app\models\AuthorSubscription;
use Yii;
use yii\db\IntegrityException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class SubscriptionController extends Controller
{
    public function actionCreate(int $authorId): string|Response
    {
        $author = Author::findOne($authorId);
        if ($author === null) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        $subscription = new AuthorSubscription();
        if ($subscription->load(Yii::$app->request->post())) {
            $subscription->author_id = $authorId;
            try {
                if ($subscription->save()) {
                    Yii::$app->session->setFlash('success', 'Подписка оформлена.');

                    return $this->redirect(['/author/view', 'id' => $author->id]);
                }
            } catch (IntegrityException $e) {
                $subscription->addError('phone', 'Этот номер уже подписан на данного автора.');
            }
        }

        return $this->render('create', [
            'subscription' => $subscription,
            'author' => $author,
        ]);
    }
}
