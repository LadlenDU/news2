<?php

namespace app\controllers\user;

use yii\filters\AccessControl;
use app\models\user\Token;
use app\models\user\RegistrationForm;

use dektrium\user\controllers\RegistrationController as BaseRegistrationController;

class RegistrationController extends BaseRegistrationController
{
    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['register'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['confirm', 'resend', 'confirm_input_password'], 'roles' => ['?', '@']],
                ],
            ],
        ];
    }

    /**
     * Confirms user's account and allow to enter a password.
     * If confirmation was successful logs the user and shows success message and password field.
     * Otherwise shows error message.
     *
     * @param int $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm_input_password($id, $code)
    {
        $token = $this->finder->findToken([
            'user_id' => $id,
            'code' => $code,
            'type' => Token::TYPE_CONFIRM_NEW_ADMIN_EMAIL
        ])->one();

        if ($token === null || $token->isExpired || $token->user === null) {
            \Yii::$app->session->setFlash(
                'danger',
                \Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );
            return $this->render('/message', [
                'title' => \Yii::t('user', 'Invalid or expired link'),
                'module' => $this->module,
            ]);
        }

        $model = \Yii::createObject([
            'class' => RegistrationForm::className(),
            'scenario' => RegistrationForm::SCENARIO_SET_PASSWORD,
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->setPassword($token)) {

            $token->user->attemptConfirmation($code);

            return $this->render('/message', [
                'title' => \Yii::t('user', 'Password has been set'),
                'module' => $this->module,
            ]);
        }

        return $this->render('register_password', ['model' => $model,]);

    }

    /*public function validateToken($code, $type)
    {
        $success = false;

        $token = $this->finder->findTokenByParams($this->id, $code, $type);

        if ($token instanceof Token && !$token->isExpired) {
            //$token->delete();
            $success = true;
        } else {
            $message = \Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
        }

        \Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

        return $success;
    }*/
}
