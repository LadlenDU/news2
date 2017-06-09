<?php

namespace app\models\user;

use app\models\user\Token;
use app\modules\user\Mailer;
use dektrium\user\helpers\Password;
use dektrium\user\models\User as BaseUser;

class User extends BaseUser
{
    /**
     * @inheritdoc
     */
    public function getIsAdmin()
    {
        return \Yii::$app->user->can('admin');
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->confirmed_at = $this->module->enableConfirmation ? null : time();
            $this->password = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_REGISTER);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $auth = \Yii::$app->authManager;
            $authorRole = $auth->getRole('reader');
            $auth->assign($authorRole, $this->getId());

            if ($this->module->enableConfirmation) {
                /** @var Token $token */
                $token = \Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

            $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
            $this->trigger(self::AFTER_REGISTER);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            //$this->password = $this->password == null ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_CREATE);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $this->confirm();

            /** @var Token $token */
            /*$token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $this->id,
                'type' => Token::TYPE_CONFIRM_NEW_ADMIN_EMAIL,
                'created_by' => 'admin',
            ]);
            $token->link('user', $this);*/
            $token = \Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRM_NEW_ADMIN_EMAIL]);
            $token->link('user', $this);
            (new Mailer)->sendNewEmailByAdmin($this, $token);

            $this->trigger(self::AFTER_CREATE);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }
}