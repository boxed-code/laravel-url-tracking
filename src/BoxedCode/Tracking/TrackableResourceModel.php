<?php

namespace BoxedCode\Tracking;

use Illuminate\Database\Eloquent\Model;

class TrackableResourceModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trackable_resources';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'type', 'resource', 'meta'];

    /**
     * Set the meta data attribute.
     *
     * @param array $value
     */
    public function setMetaAttribute($value = [])
    {
        if (! is_null($value)) {
            $this->attributes['meta'] = serialize($value);
        } else {
            $this->attributes['meta'] = null;
        }
    }

    /**
     * Get the meta data attribute.
     *
     * @return mixed
     */
    public function getMetaAttribute()
    {
        if (isset($this->attributes['meta']) && ! empty($this->attributes['meta'])) {
            return unserialize($this->attributes['meta']);
        }
    }
}
