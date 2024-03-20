<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $writers = Writer::all();
        $publishers = Publisher::all();

        return view('books.create', compact('writers', 'publishers'));
    }

    /**
     * Store a newly created book in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'ISBN' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'genre' => 'required|string|max:255',
            'subgenre' => 'required|string|max:255',
            'writer_id' => 'required|exists:writers,id',
            'publisher_id' => 'required|exists:publishers,id',
        ]);

        Book::create([
            'title' => $request->input('title'),
            'ISBN' => $request->input('ISBN'),
            'publication_year' => $request->input('publication_year'),
            'price' => $request->input('price'),
            'genre' => $request->input('genre'),
            'subgenre' => $request->input('subgenre'),
            'writer_id' => $request->input('writer_id'),
            'publisher_id' => $request->input('publisher_id'),
        ]);

        return redirect()->route('books.index');
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\View\View
     */
    public function edit(Book $book)
    {
        $writers = Writer::all();
        $publishers = Publisher::all();

        return view('books.edit', compact('book', 'writers', 'publishers'));
    }

    /**
     * Update the specified book in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'ISBN' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'genre' => 'required|string|max:255',
            'subgenre' => 'required|string|max:255',
            'writer_id' => 'required|exists:writers,id',
            'publisher_id' => 'required|exists:publishers,id',
        ]);

        $book->update([
            'title' => $request->input('title'),
            'ISBN' => $request->input('ISBN'),
            'publication_year' => $request->input('publication_year'),
            'price' => $request->input('price'),
            'genre' => $request->input('genre'),
            'subgenre' => $request->input('subgenre'),
            'writer_id' => $request->input('writer_id'),
            'publisher_id' => $request->input('publisher_id'),
        ]);

        return redirect()->route('books.index');
    }

    public function score(Request $request, $id)
    {
        $book = Book::with(['writer', 'publisher'])->findOrFail($id);

        $bookInfo = "{$book->title}, ISBN: {$book->ISBN}, Published: {$book->publication_year}, Price: {$book->price}, Genre: {$book->genre}, Writer: {$book->writer->name}, Publisher: {$book->publisher->name}";

        $response = Http::post('localai:8080/v1/chat/completions', [
            "model" => "phi-2",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Give a score ranging from 1-10 on this book: " . $bookInfo,
                    "temperature" => 0.1
                ]
            ]
        ]);

        $score = $response->json()['choices'][0]['message']['content'] ?? 'No score available';

        return response()->json(['score' => $score]);
    }
}
