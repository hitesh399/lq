<?php

namespace Singsys\LQ\Lib;

use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class SiteConfigLib {

    private $siteConfigurations = [];

    /**
     * To Get the configuration key data
     * @param $key[String]
     */
    public function get($key, $default_val= null) {

        if(isset($this->siteConfigurations[$key]) ) {

            return $this->siteConfigurations[$key];
        }
        else {

            $attributes = Cache::rememberForever('site_config.'.$key, function () use ($key, $default_val) {
                $model = $this->model();
                $data =  $model::where('name', $key)->first([
                    'name', 'data', 'options'
                ]);

                $val = $data ? $data->data : null;

                return $val !== null ? $this->setAttributeCaste($val, $data->options, $key) :  $default_val;
            });



            $this->siteConfigurations[$key] =  $attributes;
            return $this->siteConfigurations[$key];
        }
    }

    protected function model() {

        return config('lq.site_config_class');
    }

    /**
     * To set the datatype.
     */
    protected function  setAttributeCaste($val, $options, $key) {

        $dataType = isset($options['dataType']) && $options['dataType'] ? isset($options['dataType']) : null;

        $data =  null;

        /**
         * DEcryp the val if attribute is secure.
         */
        if(isset($options['secure']) && $options['secure'] ) {

            $val =  Crypt::decrypt($val);
        }

        /**
         * Transform the data base on Attribute data type.
         */
        if($dataType == 'json') {

            $val =  json_decode($val);
        }
        return $val;

    }
}
