<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'movies';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'summary',
        'cover_image',
        'author',
        'imdb_rating',
        'user_id'
    ];

    /**
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genres');
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'movie_tags');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'movie_id');
    }

    public function relatedMovies(Movie $movie)
    {
        return Movie::where('imdb_rating', $movie->imdb_rating)
            ->where('id', '!=', $movie->id)
            ->where('author', $movie->author)
            ->where(function ($q) use ($movie) {
                foreach ($movie->genres as $genre)
                {
                    $q->whereHas('genres', function ($query) use ($genre) {
                        return $query->where('genre_id', $genre->id);
                    });
                }
            })
            ->where(function ($q) use ($movie) {
                foreach ($movie->tags as $tag)
                {
                    $q->whereHas('tags', function ($query) use ($tag) {
                        return $query->where('tag_id', $tag->id);
                    });
                }
            })
            ->take(7)->get();
    }
}
