<?php

namespace app\models\user;

use dektrium\user\models\Profile as BaseProfile;

/**
 * This is the model class for table "profile".
 *
 * @property integer $notify_news_email
 * @property integer $notify_news_browser
 *
 */
class Profile extends BaseProfile
{
    public function rules()
    {
        $attr = parent::rules();

        $attr['notifyFieldsSafe'] = [['notify_news_email', 'notify_news_browser'], 'safe'];

        return $attr;
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();

        $attr['notify_news_email'] = 'Уведомление по email';
        $attr['notify_news_browser'] = 'Уведомление в браузере';

        return $attr;
    }
}