<?php

namespace app\models\user;

use dektrium\user\traits\ModuleTrait;
use Yii;
//use yii\db\ActiveRecord;
use yii\helpers\Url;
use dektrium\user\models\Token as BaseToken;

class Token extends BaseToken
{
    const TYPE_CONFIRM_NEW_ADMIN_EMAIL = 4;

    /**
     * @return string
     */
    public function getUrl()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
                $route = '/user/registration/confirm';
                break;
            case self::TYPE_RECOVERY:
                $route = '/user/recovery/reset';
                break;
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
                $route = '/user/settings/confirm';
                break;
            case self::TYPE_CONFIRM_NEW_ADMIN_EMAIL:
                $route = '/user/settings/confirm';
                break;
            default:
                throw new \RuntimeException();
        }

        $url = $this->created_by
            ? Url::to([$route, 'id' => $this->user_id, 'code' => $this->code, 'created_by' => $this->created_by], true)
            : Url::to([$route, 'id' => $this->user_id, 'code' => $this->code], true);

        return $url;
    }

    /**
     * @return bool Whether token has expired.
     */
    public function getIsExpired()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
            case self::TYPE_CONFIRM_NEW_ADMIN_EMAIL:
                $expirationTime = $this->module->confirmWithin;
                break;
            case self::TYPE_RECOVERY:
                $expirationTime = $this->module->recoverWithin;
                break;
            default:
                throw new \RuntimeException();
        }

        return ($this->created_at + $expirationTime) < time();
    }
}
