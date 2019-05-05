<?php

namespace Singsys\LQ\Lib;

use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Wcadena\StringBladeCompiler\Facades\StringView;

class MailCompiler {

    /**
     * To Replace the veriable from html.
     */
    public function replaceVeriables($html, Array $variables, $data) {

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

    public function bladeCom
}
