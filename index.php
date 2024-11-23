<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connection = mysqli_connect('localhost', 'root', '', 'bookweb');

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}


$searchQuery = $_GET['search'] ?? '';
$apiUrl = empty($searchQuery)
    ? "https://openlibrary.org/search.json?q=popular&limit=20"
    : "https://openlibrary.org/search.json?q=" . urlencode($searchQuery) . "&limit=20";

$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Fainda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #121212;
            padding: 1em;
            color: white;
            text-align: center;
            border-radius: 8px;
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .search-container {
            margin: 20px auto;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .search-container input {
            padding: 10px;
            width: 50%;
            font-size: 16px;
        }
        .search-container button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .search-container .search-btn {
            background-color: #007bff;
            color: white;
        }
        .search-container .clear-btn {
            background-color: #f44336;
            padding:1em;
            margin:1em;
            color: white;
        }
        .book-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px;
        }
        .book-card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .book-card img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .book-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .book-card p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>El libro que usted está buscando aquí lo tenemo 9</h1>
        <img src="https://w0.peakpx.com/wallpaper/221/949/HD-wallpaper-bob-esponja-mafioso-bob-esponja-gangster-mafioso-meme.jpg" style="width:150px; height:150px; border-radius:8px; object-fit:cover" alt="bobesponja"/>
        <a href="savedbooks.php" style="color: white; text-decoration: none; background-color:green; padding:1em;">Mis Libros Guardados en MySQL</a>
    </div>
    <div class="search-container">
        <form method='GET' action=''>
            <input type='text' name='search' placeholder='Buscar un libro...' value='<?php echo htmlspecialchars($searchQuery); ?>'>
            <button type='submit' class='search-btn'>Buscar</button>
            <a href='/bookweb' class='clear-btn' style='text-decoration: none; display: inline-block;'>Limpiar</a>
        </form>
    </div>
    <div class="book-container">
        <?php
        
        if (!empty($data['docs'])) {
            foreach ($data['docs'] as $book) {
                $title = $book['title'] ?? 'Título Desconocido';
                $author = $book['author_name'][0] ?? 'Autor Desconocido';
                $pages = $book['number_of_pages_median'] ?? 'N/A';
                $coverId = $book['cover_i'] ?? null;
                $coverUrl = $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : 'placeholder.jpg';

                echo "
                <div class='book-card'>
                    <img src='{$coverUrl}' alt='{$title}'>
                    <h3>{$title}</h3>
                    <p><strong>Autor:</strong> {$author}</p>
                    <p><strong>Páginas:</strong> {$pages}</p>
                    <form method='POST' action='savedbooks.php?saved=true'>
                        <input type='hidden' name='name' value='" . htmlspecialchars($title) . "'>
                        <input type='hidden' name='author' value='" . htmlspecialchars($author) . "'>
                        <input type='hidden' name='pages' value='" . (is_numeric($pages) ? intval($pages) : 0) . "'>
                        <input type='hidden' name='cover' value='" . htmlspecialchars($coverUrl) . "'>
                        <button type='submit'>Guardar en db</button>
                    </form>
                </div>";
            }
        } else {
            echo "<p class='no-results'>No encontramos nada miops</p>";
        }
        ?>
    </div>
</body>
</html>
