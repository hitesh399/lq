<?php

namespace Singsys\LQ\Lib\Media\Relations\Concerns;

use Singsys\LQ\Lib\Media\Relations\HasOneMedia;
use Singsys\LQ\Lib\Media\Relations\BelongToMedia;
//use Singsys\LQ\Lib\Media\Relations\BelongToMediaJson;
use Singsys\LQ\Lib\Media\Relations\MorphOneMedia;
use Singsys\LQ\Lib\Media\Relations\MorphManyMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasMediaRelationships {

    protected $mediaMorphType = null;
    public $mediaMorphRelation = null;
    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMedia($related, $foreignKey = null, $localKey = null)
    {

        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasOneMedia($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }
    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        if($this->mediaMorphType) {
            return $this->mediaMorphType;
        }

        $morphMap = Relation::morphMap();
        if (! empty($morphMap) && in_array(static::class, $morphMap)) {
            return array_search(static::class, $morphMap, true);
        }

        return static::class;
    }
    public function setMediaMorphType($mediaMorphType) {

        $this->mediaMorphType = $mediaMorphType;
        return $this;
    }
    /**
     * Define an inverse one-to-one or many JSON relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
     */
    // public function belongsToMediaJson($related, $foreignKey, $ownerKey = null, $relation = null)
    // {
    //     if (is_null($relation)) {
    //         $relation = $this->guessBelongsToRelation();
    //     }

    //     $instance = $this->newRelatedInstance($related);

    //     $ownerKey = $ownerKey ?: $instance->getKeyName();

    //     return $this->newBelongsToMediaJson(
    //         $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
    //     );
    // }
     /**
     * Define an inverse one-to-one or many JSON relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
     */
    public function belongsToMedia($related, $foreignKey, $ownerKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return $this->newBelongsToMedia(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }

    /**
     * Instantiate a new HasOne relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    protected function newHasOneMedia(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasOneMedia($query, $parent, $foreignKey, $localKey);
    }

     /**
     * Instantiate a new BelongsToJson relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $child
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
     */
    // protected function newBelongsToMediaJson(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    // {
    //     return new BelongToMediaJson($query, $child, $foreignKey, $ownerKey, $relation);
    // }
    /**
     * Instantiate a new BelongsToJson relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $child
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
     */
    protected function newBelongsToMedia(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongToMedia($query, $child, $foreignKey, $ownerKey, $relation);
    }

        /**
     * Define a polymorphic one-to-one relationship with media.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morphOneMedia($related, $name, $mediaMorphType = null, $relation = null, $type = null, $id = null, $localKey = null)
    {
        $this->mediaMorphType  = $mediaMorphType;
        $this->mediaMorphRelation = $relation;
        $instance = $this->newRelatedInstance($related);

        [$type, $id] = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newMorphOneMedia($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Instantiate a new MorphOne relationship with media.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    protected function newMorphOneMedia(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return new MorphOneMedia($query, $parent, $type, $id, $localKey);
    }
    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morphManyMedia($related, $name, $mediaMorphType = null, $relation = null,  $type = null, $id = null, $localKey = null)
    {
        $this->mediaMorphType  = $mediaMorphType;
        $this->mediaMorphRelation = $relation;

        $instance = $this->newRelatedInstance($related);

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        [$type, $id] = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newMorphManyMedia($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Instantiate a new MorphMany relationship with media.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    protected function newMorphManyMedia(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return new MorphManyMedia($query, $parent, $type, $id, $localKey);
    }
}
