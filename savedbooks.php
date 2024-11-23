<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$connection = mysqli_connect('localhost', 'root', '', 'bookweb');

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $author = mysqli_real_escape_string($connection, $_POST['author']);
    $pages = intval($_POST['pages']); 
    $cover = mysqli_real_escape_string($connection, $_POST['cover']);

    
    $query = "INSERT INTO saved_books (name, author, pages, cover) VALUES ('$name', '$author', $pages, '$cover')";
    if (mysqli_query($connection, $query)) {
        header('Location: savedbooks.php');
        exit;
    } else {
        echo "<p style='color: red; text-align: center;'>Error saving book: " . mysqli_error($connection) . "</p>";
    }
}


$query = 'SELECT * FROM saved_books';
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros guardados en MySQL</title>
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
        <h1>Libros en MySQL</h1>
        <a href="index.php" style="color: white; text-decoration: none;">Atrás</a>
    </div>
    <div class="book-container">
        <?php
        
        if (mysqli_num_rows($result) > 0) {
        
            while ($book = mysqli_fetch_assoc($result)) {
                echo "
                <div class='book-card'>
                    <img src='{$book['cover']}' alt='{$book['name']}'>
                    <h3>{$book['name']}</h3>
                    <p><strong>Autor:</strong> {$book['author']}</p>
                    <p><strong>Páginas:</strong> {$book['pages']}</p>
                </div>";
            }
        } else {
            echo "<p>No books saved yet.</p>";
        }
        ?>
    </div>
</body>
</html>
