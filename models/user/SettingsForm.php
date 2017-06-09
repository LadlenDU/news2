<?php

namespace app\models\user;

use dektrium\user\Module;
//use dektrium\user\models\Token;
use Yii;
use dektrium\user\models\SettingsForm as BaseSettingsForm;

class SettingsForm extends BaseSettingsForm
{
    /**
     * @inheritdoc
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->scenario = 'settings';
            $this->user->username = $this->username;
            $this->user->password = $this->new_password;
            if ($this->email == $this->user->email && $this->user->unconfirmed_email != null) {
                $this->user->unconfirmed_email = null;
            } elseif ($this->email != $this->user->email) {
                switch ($this->module->emailChangeStrategy) {
                    case Module::STRATEGY_INSECURE:
                        $this->insecureEmailChange();
                        break;
                    case Module::STRATEGY_DEFAULT:
                        $this->defaultEmailChange();
                        break;
                    case Module::STRATEGY_SECURE:
                        $this->secureEmailChange();
                        break;
                    default:
                        throw new \OutOfBoundsException('Invalid email changing strategy');
                }
            }

            if ($this->new_password) {
                Yii::$app->mailer->compose()
                    ->setTo($this->user->email)
                    ->setFrom(Yii::$app->params['fromDistribEmail'])
                    ->setSubject('Изменение пароля')
                    ->setTextBody('Вы установили новый пароль: ' . $this->new_password)
                    ->send();
            }

            return $this->user->save();
        }

        return false;
    }
}
