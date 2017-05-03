<?php

namespace app\controllers;

use app\models\Customer;
use app\models\CustomerOrder;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'apply'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['apply'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->getUser()->isGuest) {
                                $customer = Customer::findIdentity(Yii::$app->getUser()->id);
                                if ($customer && $customer->status === Customer::STATUS_VERIFY) {
                                    return true;
                                }
                            }
                            return false;
                        },
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionRegister()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new Customer(['scenario' => Customer::SCENARIO_REGISTER]);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success',
                Yii::t('app', 'You need to confirm registration via confirmation link, sending to your {email}', ['email' => $model->email]));
            $this->goHome();
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionApply()
    {
        $user = Yii::$app->getUser();

        if (!$user) {
            throw new BadRequestHttpException('The requested page does not exist.');
        }

        $model = Customer::findOne($user->getId());
        $model->setScenario(Customer::SCENARIO_APPLICATION_FORM);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Form is saved'));
            $dateModel = new CustomerOrder();
        }

        if (Yii::$app->getRequest()->post('CustomerOrder')) {
            $dateModel = new CustomerOrder();
            if ($dateModel->load(Yii::$app->request->post()) && $dateModel->save()) {
                Yii::$app->getSession()->setFlash('success',
                    Yii::t('app', 'You are welcome at {dateTime}!!', ['dateTime' => $dateModel->order_time])
                );
                return $this->goHome();
            }
        }

        return $this->render('application_form', [
            'model' => $model,
            'dateModel' => $dateModel ?? null
        ]);
    }
}
