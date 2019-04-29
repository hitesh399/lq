<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongToMedia extends BelongsTo {

    use Concerns\MediaFeature;
}
