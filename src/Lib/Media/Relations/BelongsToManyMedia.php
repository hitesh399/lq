<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToManyMedia extends BelongsToMany
{
    use Concerns\MediaFeature;
}
