<?php

namespace Acitjazz\UploadEndpoint\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaMysql extends Model
{
    use SoftDeletes;
    use Sluggable;

    protected $table = 'medias';
    protected $dates = ['deleted_at'];
    protected $fillable = ['name',
                           'slug',
                           'original_name',
                           'file_type',
                           'storage',
                           'public_id',
                           'extension',
                           'size',
                           'readable_size',
                           'height',
                           'width',
                           'url'
                        ];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ]
        ];
    }
}
