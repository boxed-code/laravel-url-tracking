<?php

namespace BoxedCode\Tracking;

use Illuminate\Database\Eloquent\Model;

class TrackableResourceModel extends Model
{
    protected $table = 'trackable_resources';

    public $incrementing = false;

    protected $fillable = ['id', 'type', 'resource', 'meta'];

    public function setMetaAttribute($value)
    {
        if (! is_null($value)) {
            $this->attributes['meta'] = serialize($value);
        } else {
            $this->attributes['meta'] = null;
        }
    }

    public function getMetaAttribute()
    {
        if (isset($this->attributes['meta']) && ! empty($this->attributes['meta'])) {
            return unserialize($this->attributes['meta']);
        }
    }
}
