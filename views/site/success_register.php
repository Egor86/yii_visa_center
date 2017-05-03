<?php

if (Yii::$app->session->hasFlash('confirm')):

?>

<div class="alert alert-success">
    You need to confirm registration via confirmation link, sended to your email
</div>
<?php endif;?>