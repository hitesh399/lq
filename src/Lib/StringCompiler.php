<?php

namespace Singsys\LQ\Lib;
use Carbon\Carbon;

class StringCompiler {

    protected $timeVeriables = [];
    protected $inTimeZone = 'UTC';
    protected $outTimeZone = 'UTC';

    public function __construct(Array $timeVeriables = [], $inTimeZone = 'UTC', $outTimeZone = 'UTC') {
        $this->timeVeriables = $timeVeriables;
        $this->inTimeZone = $inTimeZone;
        $this->outTimeZone = $outTimeZone;
    }
    /**
     * To Replace the veriable from html.
     */
    public function replaceVeriables($html, $variables, $data)
    {
        $variables = $variables ? $variables : [];
        $html = stripslashes(html_entity_decode($html));
        foreach ($variables as $var) {
            $time_format = isset($this->timeVeriables[$var]) ? $this->timeVeriables[$var] : null;
            preg_match_all('/\{+'.$var.'+\}/i', $html, $matches);
            $val = array_get($data, $var);
            if ($time_format) {
                $val = Carbon::parse($val, $this->inTimeZone)->timezone($this->outTimeZone)->format($time_format);
            }
            if(isset($matches[0]) && is_array($matches[0])) {
                $mt = $matches[0];
                foreach($mt as $mtk) {
                    $html = str_replace($mtk, $val, $html);
                }
            }
        }
        return $html;
    }

    public function makePureString($string, $data) {
        return $string;
    }
}
