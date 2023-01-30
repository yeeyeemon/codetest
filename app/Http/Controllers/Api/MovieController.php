<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMovieRequest;
use App\Http\Requests\Api\UpdateMovieRequest;
use App\Http\Resources\MovieDetailResource;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Services\MovieService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\Movie\MovieRepositoryInterface;
class MovieController extends Controller
{
    /**
     * @param MovieRepositoryInterface $movieRepository
     * @param ImageUploadService $imageUploadService
     */
    public function __construct(
        private ImageUploadService $imageUploadService,
    )
    {}


    /**
     * @param StoreMovieRequest $request
     * @throws \App\Exceptions\Exception
     */
    public function store(StoreMovieRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $cover_image = $this->imageUploadService->upload($data['cover_image'],'Movie');

            $data['cover_image'] = $cover_image;
            $data['user_id'] = auth()->user()->id;
            $movie = Movie::create($data);
            $movie->genres()->attach($data['genres']);
            $movie->tags()->attach($data['tags']);

            DB::commit();
        }
        catch (\Exception $exception)
        {
            DB::rollBack();

            throw new Exception($exception->getMessage());
        }

        return response([ 'movie' => new 
        MovieResource($movie), 
        'message' => 'Success'], 200);
    }

    /**
     * @param Movie $movie
     * @return JsonResponse
     */
    public function show(Movie $movie)
    {
        $movie =$movie;
        return response([ 'movie' => 
        MovieDetailResource::collection($movie), 
        'message' => 'Successful'], 200);
    }

    /**
     * @param UpdateMovieFormRequest $request
     * @param Movie $movie
     * @return JsonResponse
     * @throws \App\Exceptions\CustomException
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        try {
            DB::beginTransaction();
           
            
            if ((int)$movie['user_id'] !== auth()->user()->id)
            {
                throw new Exception('Unauthorized!', 404);
            }

            $data = $request->validated();

            if (isset($data['cover_image']) && $data['cover_image'] !== null)
            {
                $this->imageUploadService->delete($movie['cover_image']);
                $cover_image = $this->imageUploadService->upload($data['cover_image'], $movie);
                $data['cover_image'] = $cover_image;
            }

            $movie = Movie::create($data);

            $movie->genres()->sync($data['genres']);

            $movie->genres()->sync($data['tags']);

            DB::commit();
        }
        catch (\Exception $exception)
        {
            DB::rollBack();

            throw new Exception($exception->getMessage());
        }

        return response([ 'movie' => new 
        MovieResource($movie), 
        'message' => 'Success'], 200);
    }

    /**
     * @param Movie $movie
     * @return JsonResponse
     */
    public function destroy(Movie $movie)
    {
        $movie->genres()->detach();
        $movie->tags()->detach();
        $movie->delete();

        return response(['message' => 'Movie deleted!']);
    }

    /**
     * @param Movie $movie
     * @return Response
     */
    public function downloadPdf(Movie $movie)
    {
        $pdf = Pdf::loadView('pdf.movie', [
            'movie' => $movie
        ]);

        return $pdf->download('movie.pdf');
    }

    /**
     * @return JsonResponse
     */
    public function list()
    {
        $movies = Movie::latest()->paginate(10);
        return response([ 'movies' => 
        MovieResource::collection($movies), 
        'message' => 'Successful'], 200);

    }
}
