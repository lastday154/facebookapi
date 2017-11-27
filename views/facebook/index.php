<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
    <div class="error" style="color: #a94442;"><?php echo isset($error) ? $error : ''; ?></div>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'url')->label('Please enter facebook page url or page id') ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>