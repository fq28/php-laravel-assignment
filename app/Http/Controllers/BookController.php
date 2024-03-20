<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Writer;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::orderBy('sort_order', 'desc')->get();
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
            'stock_amount' => 'required|integer|min:0',
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
            'sort_order' => -1,
            'stock_amount' => $request->input('stock_amount'),
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
            'stock_amount' => 'required|integer|min:0',
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
            'sort_order' => -1,
            'stock_amount' => $request->input('stock_amount'),
            'writer_id' => $request->input('writer_id'),
            'publisher_id' => $request->input('publisher_id'),
        ]);

        return redirect()->route('books.index');
    }


    /**
     * Recalculates the sort order of books based on a changed book and movement.
     *
     * @param Book $changedBook The book that has been changed.
     * @param int $movement The movement value to adjust the sort order.
     * @return void
     */
    public function recalculateSortOrder(Book $changedBook, $movement)
    {
        // retrieve all books except those with stock_amount 0, ordered by sort_order.
        $activeBooks = Book::where('stock_amount', '>', 0)
                            ->orderBy('sort_order', 'asc')
                            ->get();

        // find the index of the changed book in the collection.
        $currentIndex = $activeBooks->search(function ($item) use ($changedBook) {
            return $item->id === $changedBook->id;
        });

        // calculate new index by adding movement.
        $newIndex = $currentIndex + $movement;

        // ensure newIndex is within bounds.
        if ($newIndex < 0) 
            $newIndex = 0;

        else if ($newIndex >= $activeBooks->count()) 
            $newIndex = $activeBooks->count() - 1;

        // move the book to the new index
        $activeBooks->splice($currentIndex, 1);
        $activeBooks->splice($newIndex, 0, [$changedBook]);

        // Update sort_order for the active books
        $sortOrder = 1;
        foreach ($activeBooks as $book) {
            $book->sort_order = $sortOrder++;
            $book->save();
        }

        // ensure all books with stock_amount 0 have sort_order -1.
        Book::where('stock_amount', 0)->update(['sort_order' => -1]);
    }

    /**
     * Reorder the books.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reOrder(Request $request, Book $book)
    {
        // get up and down parameters from the request
        $up = intval($request->input('up')) ?? 0;
        $down = intval($request->input('down')) ?? 0;

        // ignore the action if both are 0 or if book is out of stock
        if (($up === 0 && $down === 0) || $book->stock_amount === 0)
            return redirect()->route('books.index');

        // calculate how much the book should move
        $movement = $up - $down;

        $this->recalculateSortOrder($book, $movement);

        return redirect()->route('books.index');
    }
}
