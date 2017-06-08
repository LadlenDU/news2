<?php

namespace app\models\user;

use dektrium\user\models\Token;
use dektrium\user\models\User as BaseUser;

class User extends BaseUser
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $trans = parent::attributeLabels();
        $trans['last_login_at'] = \Yii::t('user_add', 'Last login');
        return $trans;
    }

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
            $authorRole = $auth->getRole('author');
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
}