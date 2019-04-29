<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

class BelongToMediaJson extends BelongsToJson {

    use Concerns\MediaFeature;
}
