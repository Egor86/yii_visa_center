<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form ActiveForm */

$this->title = Yii::t('app', 'Application form');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (isset($dateModel)) :
    $this->params['breadcrumbs'][] = Yii::t('app', 'Order time');
?>
    <div class="order">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($dateModel, 'order_time')->input('datetime-local') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div><!-- order -->
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'first_name',
            'last_name',
            'email:email',
            'passport',
            'citizenship',
//            'password_hash',
//            'access_token:ntext',
//            'status',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>

<?php else:?>

    <div class="site-application_form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'first_name') ?>
        <?= $form->field($model, 'last_name') ?>
        <?= $form->field($model, 'passport') ?>
        <?= $form->field($model, 'citizenship') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div><!-- site-application_form -->

<?php endif;?>