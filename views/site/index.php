<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Visa-Center';
?>
<div class="site-index">

    <div class="jumbotron">
            <p><a class="btn btn-lg btn-success" href="<?= Yii::$app->user->isGuest ? Url::to(['site/register']) : Url::to(['site/apply'])?>">Подати заявку</a></p>
    </div>

    <div class="body-content">

    </div>
</div>
