<?php

namespace Singsys\LQ;

use Illuminate\Mail\MailServiceProvider;
use Illuminate\Mail\Mailer;

class LqMailServiceProvider extends MailServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $config = $this->app->make('config');
        $site_config = $this->app->make('site_config');

        $config->set('mail.driver', $site_config->get('MAIL_DRIVER', env('MAIL_DRIVER') ) );
        $config->set('mail.host', $site_config->get('MAIL_HOST', env('MAIL_HOST')) );
        $config->set('mail.port', $site_config->get('MAIL_PORT', env('MAIL_PORT')));
        $config->set('mail.form.address',  $site_config->get('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS') ));
        $config->set('mail.form.name',  $site_config->get('MAIL_FROM_NAME', env('MAIL_FROM_NAME') ));
        $config->set('mail.encryption', $site_config->get('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION')));
        $config->set('mail.username', $site_config->get('MAIL_USERNAME', env('MAIL_USERNAME')));
        $config->set('mail.password' , $site_config->get('MAIL_PASSWORD', env('MAIL_PASSWORD')));

        parent::register();
    }
}
