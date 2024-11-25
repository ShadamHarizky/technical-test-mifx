<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\RetreiveBookContentsJob;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
         $query = Book::with(['authors', 'bookContents', 'reviews']);

         if ($request->has('title')) {
             $query->where('title', 'like', '%' . $request->title . '%');
         }
 
         if ($request->has('authors')) {
             $authors = explode(',', $request->authors);
             $query->whereHas('authors', function($q) use ($authors) {
                 $q->whereIn('id', $authors);
             });
         }
 
         $sortColumn = $request->input('sortColumn');
         $sortDirection = $request->input('sortDirection','ASC');
         if (in_array($sortColumn, ['title', 'avg_review', 'published_year']) && in_array($sortDirection, ['ASC', 'DESC'])) {
             if ($sortColumn === 'avg_review') {
                $query->withAvg('reviews', 'review')
                      ->orderBy('reviews_avg_review', $sortDirection);
             } else {
                 $query->orderBy($sortColumn, $sortDirection);
             }
         }
 
         $books = $query->paginate($request->input('per_page', 15));
 
         return BookResource::collection($books);
    }

    public function store(PostBookRequest $request)
    {
        $book = Book::create([
            'isbn' => $request->isbn,
            'title' => $request->title,
            'description' => $request->description,
            'published_year' => $request->published_year,
            'price' => $request->price,
        ]);

        $book->authors()->sync($request->authors);

        RetreiveBookContentsJob::dispatch($book);

        return response()->json([
            'data' => new BookResource($book)
        ], 201);
    }
}
