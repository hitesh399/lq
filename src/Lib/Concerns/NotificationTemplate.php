<?php

namespace Singsys\LQ\Lib\Concerns;

use Cache;
use Singsys\LQ\Lib\StringCompiler;
use Illuminate\Mail\Mailable;

trait NotificationTemplate
{
    protected $timeVeriables = [];
    protected $inTimeZone = 'UTC';
    protected $outTimeZone = 'UTC';
    protected $emailBody = 'EMAIL_BODY';

    /**
     * To Get the Email Template
     */
    protected function getTemaplate($key, array $data = [])
    {
        $template = Cache::rememberForever('notification_template.'.$key, function () use ($key) {
            $model = $this->model();
            $data =  $model::where('name', $key)->first([
                'name', 'subject', 'options', 'body', 'type'
            ]);
            if (!$data) {
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
            $layout_body = $site_config->get($this->emailBody);
            if ($layout_body) {
                $body = $string->replaceVeriables(
                    $layout_body,
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
