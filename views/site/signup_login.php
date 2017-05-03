<?php

use yii\helpers\Url;

?>
<div class="site-index">

    <div class="jumbotron">

        <p><a class="btn btn-lg btn-success" href="<?= Url::to(['site/register'])?>">I am not registered</a></p>
    </div>
    <div class="jumbotron">

        <p><a class="btn btn-lg btn-success" href="<?= Url::to(['site/login'])?>">I am registered</a></p>
    </div>
</div>