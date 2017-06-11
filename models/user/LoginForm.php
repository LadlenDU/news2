<?php

namespace app\models\user;

use dektrium\user\helpers\Password;
use Yii;
use dektrium\user\models\LoginForm as BaseLoginForm;

class LoginForm extends BaseLoginForm
{
    /** @inheritdoc */
    public function rules()
    {
        $rules = parent::rules();

        if (isset($rules['passwordValidate'])) {
            $rules['passwordValidate'] = [
                'password',
                function ($attribute) {
                    if (!$this->user->password_hash) {
                        $this->addError($attribute, Yii::t('user', 'You have not set password yet'));
                    } elseif ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    }
                }
            ];
        }

        return $rules;
    }

    /**
     * Validates if the hash of the given password is identical to the saved hash in the database.
     * It will always succeed if the module is in DEBUG mode.
     *
     * @return void
     */
    /*public function validatePassword($attribute, $params)
    {
        if ($this->user === null || !Password::validate($this->password, $this->user->password_hash))
            $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
    }*/
}
