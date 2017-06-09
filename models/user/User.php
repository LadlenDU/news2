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

    protected function setDefaultRole()
    {
        $auth = \Yii::$app->authManager;
        $authorRole = $auth->getRole('reader');
        $auth->assign($authorRole, $this->getId());
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

            $this->setDefaultRole();

            /*$auth = \Yii::$app->authManager;
            $authorRole = $auth->getRole('reader');
            $auth->assign($authorRole, $this->getId());*/

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

            $this->setDefaultRole();

            //$this->confirm();

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

    /**
     * @inheritdoc
     */
    public function attemptConfirmation($code)
    {
        $token = $this->finder->findTokenByParams($this->id, $code, Token::TYPE_CONFIRM_NEW_ADMIN_EMAIL);

        if ($token instanceof Token && !$token->isExpired) {
            $token->delete();
            if (($success = $this->confirm())) {
                \Yii::$app->user->login($this, $this->module->rememberFor);
                $message = \Yii::t('user', 'Thank you, registration is now complete.');
            } else {
                $message = \Yii::t('user', 'Something went wrong and your account has not been confirmed.');
            }
        } else {
            $success = false;
            $message = \Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
        }

        \Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

        return $success;
    }
}