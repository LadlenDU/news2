<?php

/**
 * @var dektrium\user\models\User   $user
 * @var dektrium\user\models\Token  $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'Email created for you on {0}', Yii::$app->name) ?>.
<?= Yii::t('user', 'In order to complete your registration (verify and enter your password), please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.
