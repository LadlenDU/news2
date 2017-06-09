<?php

namespace app\models\user;

//use dektrium\user\Finder;
//use dektrium\user\Mailer;
//use yii\base\Model;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{
    const SCENARIO_SET_PASSWORD = 'set_password';

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_SET_PASSWORD => ['password'],
        ];
    }

    /**
     * Set user's password.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function setPassword(Token $token)
    {
        if (!$this->validate() || $token->user === null) {
            return false;
        }

        if ($token->user->resetPassword($this->password)) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your password has been set successfully.'));
            $token->delete();
        } else {
            \Yii::$app->session->setFlash(
                'danger',
                \Yii::t('user', 'An error occurred and your password has not been set. Please try again later.')
            );
        }

        return true;
    }
}
