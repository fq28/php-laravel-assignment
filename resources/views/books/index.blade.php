@extends('layout')

@section('content')
    <div class="container">
        <h1>Books</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">Add Book</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>ISBN</th>
                    <th>Publication Year</th>
                    <th>Price</th>
                    <th>Genre</th>
                    <th>Subgenre</th>
                    <th>Writer</th>
                    <th>Publisher</th>
                    <th>Review</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($books as $book)
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->ISBN }}</td>
                        <td>{{ $book->publication_year }}</td>
                        <td>{{ $book->price }}</td>
                        <td>{{ $book->genre }}</td>
                        <td>{{ $book->subgenre }}</td>
                        <td>{{ $book->writer->name }}</td>
                        <td>{{ $book->publisher->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary get-score" data-book-id="{{ $book->id }}">
                                Get Review
                            </button>
                        </td>
                        <td>
                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@section('scripts')
<script>
document.querySelectorAll('.get-score').forEach(button => {
    button.addEventListener('click', function() {
        const bookId = this.getAttribute('data-book-id');
        fetch(`/books/${bookId}/score`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure CSRF token is included for POST requests
            },
            body: JSON.stringify({ bookId: bookId })
        })
        .then(response => response.json())
        .then(data => {
            // Replace the button with the score
            this.parentNode.innerHTML = data.score;
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
@endsection

@endsection
