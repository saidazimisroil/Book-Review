<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function __construct(
        protected Request $request,
    ) { }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = $this->request->input('title');
        $filter = $this->request->input('filter');

        $books = Book::when(
            $title,
            fn ($query, $title) => $query->title($title)
        );
        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withAvgRating()->withReviewsCount(),
        };

        $cacheKey = 'books:' . $filter . ':' . $title;
        $books = Cache::remember($cacheKey, 3600, fn () => $books->get());

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey,
            3600, 
            fn () => Book::with([
                'reviews' => fn ($query) => $query->latest()
            ])->withAvgRating()->withReviewsCount()->findOrFail($id)
        );

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
