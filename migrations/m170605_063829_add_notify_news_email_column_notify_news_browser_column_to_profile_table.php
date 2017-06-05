<?php

use yii\db\Migration;

/**
 * Handles adding notify_news_email_column_notify_news_browser to table `profile`.
 */
class m170605_063829_add_notify_news_email_column_notify_news_browser_column_to_profile_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('profile', 'notify_news_email', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('profile', 'notify_news_browser', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('profile', 'notify_news_email');
        $this->dropColumn('profile', 'notify_news_browser');
    }
}
