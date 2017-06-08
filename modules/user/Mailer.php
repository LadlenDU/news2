<?php

namespace app\modules\user;

use dektrium\user\models\Token;
use dektrium\user\models\User;
use Yii;
use dektrium\user\Mailer as BaseMailer;

class Mailer extends BaseMailer
{
    /** @var string */
    public $viewPath = '@app/views/mail';

    /** @var string */
    protected $newEmailByAdminSubject;

    /**
     * @return string
     */
    public function getNewEmailByAdminSubject()
    {
        if ($this->newEmailByAdminSubject == null) {
            $this->setNewEmailByAdminSubject(Yii::t('user', 'Email created for you on {0}', Yii::$app->name));
        }

        return $this->newEmailByAdminSubject;
    }

    /**
     * @param string $newEmailByAdminSubject
     */
    public function setNewEmailByAdminSubject($newEmailByAdminSubject)
    {
        $this->newEmailByAdminSubject = $newEmailByAdminSubject;
    }

    /**
     * @param User $user
     * @param Token $token
     *
     * @return bool
     */
    public function sendNewEmailByAdmin(User $user, Token $token)
    {
        /*return $this->sendMessage(
            $user->email,
            $this->getNewEmailByAdminSubject(),
            'new_email_by_admin',
            //['user' => $user, 'token' => $token, 'module' => $this->module]
            ['user' => $user, 'token' => $token]
        );*/
        return $this->sendMessage(
            $user->email,
            $this->getNewEmailByAdminSubject(),
            'new_email_by_admin',
            ['user' => $user, 'token' => $token, 'module' => $this->module]
        );
    }
}
