<?php

namespace Orchid\Foundation\Core\Models;

use Laravel\Scout\Searchable;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Orchid\Foundation\Facades\Dashboard;
use Orchid\Foundation\Exceptions\TypeException;

class Post extends Model
{
    use Searchable, TaggableTrait;

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var
     */
    protected $dataType = null;

    /**
     * @var array
     */
    protected $fillable = [
        'types_id',
        'users_id',
        'type',
        'section_id',
        'content',
        'slug',
        'publish',
        'created_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'page' => 'boolean',
        'type' => 'string',
        'slug' => 'string',
        'content' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'publish',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * @return mixed
     */
    public function whereType()
    {
        return $this->where('type', $this->dataType->slug);
    }

    /**
     * @return null
     */
    public function getTypeObject()
    {
        if (! is_null($this->dataType)) {
            return $this->dataType;
        } else {
            return $this->getType($this->getAttribute('type'))->dataType;
        }
    }

    /**
     * @param $getType
     * @return mixed
     * @throws TypeException
     */
    public function getType($getType)
    {
        $types = Dashboard::types(true);
        foreach ($types as $type) {
            if ($type->slug == $getType) {
                $this->dataType = $type;
                break;
            }
        }

        if (is_null($this->dataType)) {
            throw new TypeException('Type is not found');
        }

        return $this;
    }

    /**
     * @param $field
     * @param null $lang
     * @return mixed|null
     */
    public function getContent($field, $lang = null)
    {
        try {
            $lang = $lang ?: App::getLocale();
            if (! is_null($this->content) && ! in_array($field, $this->getFillable())) {
                return $this->content[$lang][$field];
            } elseif (in_array($field, $this->getFillable())) {
                return $this->$field;
            }
        } catch (\Exception $exception) {
        }
    }

    /**
     * Get the author's posts.
     * @return mixed
     */
    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id')->first();
    }

    /**
     * Get tags for post as string.
     * @return mixed
     */
    public function getStringTags()
    {
        return $this->tags->implode('name', $this->getTagsDelimiter());
    }

    /**
     * @return mixed
     */
    public function breadcrumb()
    {
        $section = $this->section()->first();

        return $section ? $section->breadcrumb() : [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Main image (First image).
     * @return mixed
     */
    public function hero()
    {
        $first = $this->attachment()->first();

        return $first ? $first->url() : null;
    }

    /**
     * @return mixed
     */
    public function attachment()
    {
        return $this->hasMany(File::class);
    }
}