<?php

namespace app\controllers;

use app\models\Customer;
use app\models\CustomerOrder;
use Yii;
use yii\web\BadRequestHttpException;

class CustomerController extends \yii\web\Controller
{
    public function actionConfirm()
    {
        $params = \Yii::$app->getRequest()->getQueryParams();

        if (!empty($params)) {
            $model = Customer::findIdentityByAccessToken($params['security_key'] ?? []);

            if ($model && $model->validateAuthKey($params['data'] ?? [])) {

                if ($model->status == Customer::STATUS_VERIFY) {
                    Yii::$app->user->login($model, 3600*24*30);
                    return $this->goHome();
                }

                $model->setScenario(Customer::SCENARIO_VERIFIED);
                $model->status = Customer::STATUS_VERIFY;

                $model->update(false);
                Yii::$app->user->login($model, 3600*24*30);
//                return $this->goHome();
                return $this->redirect('site/apply');
            }
        }
        throw new BadRequestHttpException('The requested page does not exist.');
    }

    public function actionOrder()
    {
        $user = Yii::$app->getUser();

        if (!$user) {
            throw new BadRequestHttpException('The requested page does not exist.');
        }

        $model = new CustomerOrder(['customer_id' => $user->id]);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {

        }
    }
}
