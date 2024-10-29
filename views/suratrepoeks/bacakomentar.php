<?php if (
    $model->komentar != NULL &&
    (Yii::$app->user->identity->username === $model['owner']
        || Yii::$app->user->identity->username === $model['approver']
        || Yii::$app->user->identity->issekretaris)
) { ?>
    <h3>
        <span class="badge bg-primary">Koreksi Surat:</span>
    </h3>
    <p>
        <?php echo $model->komentar; ?>
    </p>
<?php } ?>