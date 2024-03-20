<!DOCTYPE html>
<html>
<head>
    <!-- Your head content here -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ route('publishers.index') }}">Publishers</a></li>
            <li><a href="{{ route('writers.index') }}">Writers</a></li>
            <li><a href="{{ route('books.index') }}">Books</a></li>
        </ul>
    </nav>

    @yield('content')
    @yield('scripts')
    <!-- Your additional footer or script content here -->
</body>
</html>
