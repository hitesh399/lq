<?php

namespace Singsys\LQ\Lib\Concerns;

use Cache;
use Singsys\LQ\Lib\StringCompiler;
use Illuminate\Mail\Mailable;

Trait NotificationTemplate {

    protected $timeVeriables = [];
    protected $inTimeZone = 'UTC';
    protected $outTimeZone = 'UTC';
    protected $emailHeader = 'EMAIL_HEADER';
    protected $emailBody = 'EMAIL_BODY';
    protected $emailContainer = 'EMAIL_CONTAINER';
    protected $emailFooter = 'EMAIL_FOOTER';

    /**
     * To Get the Email Template
     */
    protected function getTemaplate($key, Array $data = [])
    {
        $template = Cache::rememberForever('notification_template.'.$key, function () use ($key) {
            $model = $this->model();
            $data =  $model::where('name', $key)->first([
                'name', 'subject', 'options', 'body', 'type'
            ]);
            if(!$data) {
                return null;
            }
            return [
                'subject' => $data->subject,
                'body' => $data->body,
                'variables' => $data->options['variables']
            ];
        });

        $subject = $template['subject'];
        $body = $template['body'];
        $string = new StringCompiler($this->timeVeriables, $this->inTimeZone, $this->outTimeZone);

        $subject = $string->makePureString($subject, $data);
        $subject = $string->replaceVeriables($subject, $template['variables'], $data);
        $body = $string->makePureString($body, $data);
        $body = $string->replaceVeriables($body, $template['variables'], $data);

        if ($this instanceof Mailable) {

            $site_config = app('site_config');
            $layout_header = $site_config->get($this->emailHeader);
            $layout_footer = $site_config->get($this->emailFooter);
            $layout_body = $site_config->get($this->emailBody);
            $layout_container = $site_config->get($this->emailContainer);
            if ($layout_body) {
                $body = $string->replaceVeriables(
                    $layout_body,
                    ['body'],
                    ['body' => $body]
                );
            }
            if ($layout_header) {
                $body = $layout_header . $body;
            }
            if ($layout_footer) {
                $body = $body . $layout_footer;
            }
            if ($layout_container) {
                $body = $string->replaceVeriables(
                    $layout_container,
                    ['body'],
                    ['body' => $body]
                );
            }
        }

        $template['subject'] = $subject;
        $template['body'] = $body;
        return $template;
    }

    /**
     * To get the Notification Template Model
     *
     * @return object Illuminate\Database\Eloquent\Model
     */
    protected function model()
    {
        return config('lq.notification_template_class');
    }
}
