<?php

namespace App\Http\Controllers;

use App\BookReview;
use App\Book;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class BooksReviewController extends Controller
{
    public function __construct()
    {
    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $review = BookReview::create([
            'book_id' => $bookId,
            'user_id' => Auth::id(),
            'review' => $request->review,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'data' => new BookReviewResource($review)
        ], 201);
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $review = BookReview::where('book_id', $bookId)->find($reviewId);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $review->delete();

        return response()->json([], 204);
    }
}
