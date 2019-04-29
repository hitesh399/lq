<?php

namespace Singsys\LQ\Lib\Concerns;

trait MakeNew {

    /**
     * To get id or make new recoard
     */
    public static function getIdOrMakeNew($data, $other_data = []) {

        if(!$data || empty($data)) {
            return null;
        }

        $is_single = isset($data['0']) ? false : true;
        $data = $is_single ? [$data] : $data;

        $ids = [];
        $_this = new static;

        foreach($data as $attribute) {
            if(isset($attribute['new']) && $attribute['new']){
                $new = $_this->create( array_merge( $attribute, $other_data ) );
                $ids[] = $new->id;
            }
            else {
                $ids[] = $attribute['id'];
            }
        }

        return $is_single ? $ids[0]: $ids;
    }
}
