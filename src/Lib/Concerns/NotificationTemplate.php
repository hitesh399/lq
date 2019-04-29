<?php

namespace Singsys\LQ\Lib\Concerns;

use Cache;
use Illuminate\Support\Collection;

Trait NotificationTemplate {

    /**
     * To Get the Email Template
     */
    protected function getTemaplate($key, Array $data = []) {

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

        $template['subject'] = $this->replaceVeriables($template['subject'], $template['variables'], $data);
        $template['body'] = $this->replaceVeriables($template['body'], $template['variables'], $data);

        return $template;
    }

    /**
     * To Replace the veriable from html.
     */
    protected function replaceVeriables($html, Array $variables, $data) {

        $html = stripslashes(html_entity_decode($html));

        foreach ($variables as $var) {

            preg_match_all('/\{+'.$var.'+\}/i', $html, $matches);

            $val = array_get($data, $var);

            if(isset($matches[0]) && is_array($matches[0])) {

                $mt = $matches[0];

                foreach($mt as $mtk) {

                    $html = str_replace($mtk, $val, $html);
                }
            }
        }

        return $html;
    }

    protected function model() {

        return config('lq.notification_template_class');
    }
}
