<?php

namespace app\models\user;

use dektrium\user\Module;
use dektrium\user\models\Token;
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
                /** @var Token $token */
                /*$token = Yii::createObject([
                    'class'   => Token::className(),
                    'user_id' => $this->user->id,
                    'type'    => Token::TYPE_CONFIRM_OLD_EMAIL,
                ]);
                $token->save(false);
                $this->mailer->sendReconfirmationMessage($this->user, $token);*/

                $this->mailer->sendGeneratedPassword($this->user, $this->new_password);
            }

            return $this->user->save();
        }

        return false;
    }
}
