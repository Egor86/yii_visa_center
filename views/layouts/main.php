<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Visa-Center',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $item = [['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'Application form', 'url' => ['/site/apply']]];

    if (Yii::$app->user->isGuest) {
        $item[] = ['label' => 'Registration', 'url' => ['/site/register']];
        $item[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $item[] = (
            '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->email . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        );
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $item
//        [
//            ['label' => 'Home', 'url' => ['/site/index']],
//            ['label' => 'Application form', 'url' => ['/site/apply']],
//            ['label' => 'About', 'url' => ['/site/about']],
//            ['label' => 'Contact', 'url' => ['/site/contact']],
//            Yii::$app->user->isGuest ? (
//                ['label' => 'Registration', 'url' => ['/site/register']]
//            ) : '',
//            Yii::$app->user->isGuest ? (
//            ['label' => 'Registration', 'url' => ['/site/register']] .
//                ['label' => 'Login', 'url' => ['/site/login']
//            ]) : (
//                '<li>'
//                . Html::beginForm(['/site/logout'], 'post')
//                . Html::submitButton(
//                    'Logout (' . Yii::$app->user->identity->email . ')',
//                    ['class' => 'btn btn-link logout']
//                )
//                . Html::endForm()
//                . '</li>'
//            )
//        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

        <?php if (Yii::$app->getSession()->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?= Yii::$app->getSession()->getFlash('success')?>
            </div>
        <?php endif;?>

        <?php if (Yii::$app->getSession()->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Yii::$app->getSession()->getFlash('error'); ?>
            </div>
        <?php endif;?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Visa-Center <?= date('Y') ?></p>

<!--        <p class="pull-right">--><?//= Yii::powered() ?><!--</p>-->
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
