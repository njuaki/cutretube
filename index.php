<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CutreTube</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .content {
            flex: 1;
            padding-bottom: 50px; /* Espacio para el pie de página */
        }
        .centered-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh; /* Altura del viewport para centrar verticalmente */
        }

        .top-container {
            text-align: center;
            margin-top: 20px;
        }

        .hidden {
            display: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 600px;
            width: 100%;
            margin: 0 auto 40px;
        }

        form input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 4px 0 0 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

form button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    background-color: #FF0000;
    color: #fff;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-left: 10px; /* Añade un poco de separación */
}

        form button:hover {
            background-color: #cc0000;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
            background-color: #f1f1f1;
            border-top: 1px solid #ccc;
            width: 100%;
            position: fixed;
            bottom: 0;
        }
        
        .github-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            margin-left: 10px;
        }
        
        .github-link svg {
            width: 20px;
            height: 20px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div id="header" class="centered-container">
            <h1><span class="cutre">CUTRE</span><span class="tube">TUBE</span></h1>
            <h2>Ahora un poco menos cutre</h2>
            <form id="search-form" method="GET" action="index.php">
                <input type="text" name="query" placeholder="Introduce tu búsqueda" required>
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div id="results" class="hidden">
            <div id="modal" class="modal">
                <div class="modal-content">
                    <h2>Haciendo cosas...</h2>
                    <div class="loader"></div>
                    <p>No tardará mucho, o sí...</p>
                </div>
            </div>

            <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            function vaciarCarpeta($carpeta) {
                $ficheros = glob($carpeta . '/*');
                foreach ($ficheros as $fichero) {
                    if (is_file($fichero)) {
                        unlink($fichero);
                    }
                }
            }

            function descargarImagen($url, $rutaDestino) {
                $img = file_get_contents($url);
                file_put_contents($rutaDestino, $img);
            }

            function mostrarResultados($videos) {
                $ip = $_SERVER['REMOTE_ADDR'];
                if (count($videos) > 0) {
                    echo '<h2>Resultados de la búsqueda:</h2>';
                    echo '<div class="grid-container">';
                    foreach ($videos as $video) {
                        if (isset($video['videoRenderer'])) {
                            $title = htmlspecialchars($video['videoRenderer']['title']['runs'][0]['text']);
                            $thumbnailUrl = htmlspecialchars($video['videoRenderer']['thumbnail']['thumbnails'][0]['url']);
                            $videoId = $video['videoRenderer']['videoId'];
                            $downloadLink = "download.php?videoId=$videoId&ip=$ip";

                            $thumbnailPath = "tmpIma/$videoId.jpg";
                            if (!empty($thumbnailUrl)) {
                                descargarImagen($thumbnailUrl, $thumbnailPath);
                            } else {
                                echo "<p>Error: No se pudo obtener la URL de la miniatura para el video $videoId.</p>";
                            }

                            echo '<div class="grid-item">';
                            echo "<a href=\"$downloadLink\" class=\"download-link\" data-video-id=\"$videoId\" data-ip=\"$ip\"><img src=\"$thumbnailPath\" alt=\"Thumbnail\"></a>";
                            echo "<div class='grid-item-title'>$title</div>";
                            echo "<a class='ver-video download-link' href=\"$downloadLink\" data-video-id=\"$videoId\" data-ip=\"$ip\">Ver video</a>";
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                } else {
                    echo '<p>No se encontraron resultados. Verifica si la estructura JSON de YouTube ha cambiado.</p>';
                }
            }

            function guardarRecomendaciones($videos) {
                $recommendations = array();
                $count = 0;

                foreach ($videos as $video) {
                    if (isset($video['videoRenderer']) && $count < 5) {
                        $title = htmlspecialchars($video['videoRenderer']['title']['runs'][0]['text']);
                        $thumbnailUrl = htmlspecialchars($video['videoRenderer']['thumbnail']['thumbnails'][0]['url']);
                        $videoId = $video['videoRenderer']['videoId'];

                        $recommendations[] = array(
                            'title' => $title,
                            'thumbnail' => $thumbnailUrl,
                            'videoId' => $videoId
                        );

                        $count++;
                    }
                }

                file_put_contents('recommendations.json', json_encode($recommendations));
            }

            vaciarCarpeta('tmpIma');

            if (isset($_GET['query'])) {
                echo "<script>document.getElementById('header').classList.remove('centered-container');document.getElementById('header').classList.add('top-container');document.getElementById('results').classList.remove('hidden');</script>";
                $query = urlencode($_GET['query']);
                $url = "https://www.youtube.com/results?search_query=$query";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, como Gecko) Chrome/58.0.3029.110 Safari/537.3');
                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo '<p>Error fetching the page: ' . curl_error($ch) . '</p>';
                }

                curl_close($ch);

                if ($response !== false) {
                    if (preg_match('/ytInitialData = (.*?);<\/script>/', $response, $matches)) {
                        $jsonData = json_decode($matches[1], true);

                        $videos = $jsonData['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'][0]['itemSectionRenderer']['contents'];

                        guardarRecomendaciones($videos);

                        mostrarResultados($videos);
                    } else {
                        echo '<p>No se pudo encontrar la estructura JSON en la página de resultados.</p>';
                    }
                } else {
                    echo '<p>Error al obtener la página de resultados.</p>';
                }
            }
            ?>
        </div>
    </div>

    <footer>
      Juaki Garcia & GPT - Equal access for all
        <a href="https://github.com/njuaki/cutretube" class="github-link">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <title>GitHub</title>
                <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.387.6.113.82-.258.82-.577v-2.234c-3.338.724-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.757-1.333-1.757-1.089-.744.083-.729.083-.729 1.205.085 1.838 1.236 1.838 1.236 1.07 1.834 2.809 1.304 3.495.997.108-.775.418-1.305.762-1.605-2.665-.303-5.466-1.332-5.466-5.93 0-1.31.47-2.381 1.236-3.221-.124-.303-.536-1.523.116-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.398 3.004-.403 1.02.005 2.047.137 3.006.403 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.241 2.873.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.803 5.625-5.473 5.921.43.37.823 1.102.823 2.222v3.293c0 .322.216.694.825.576 4.765-1.589 8.199-6.084 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
        </a>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const downloadLinks = document.querySelectorAll('.download-link');
            downloadLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const videoId = this.getAttribute('data-video-id');
                    const ip = this.getAttribute('data-ip');
                    const modal = document.getElementById('modal');
                    modal.style.display = 'flex';

                    localStorage.setItem('downloadInProgress', videoId);

                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `download.php?videoId=${videoId}&ip=${ip}`, true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            localStorage.removeItem('downloadInProgress');
                            window.location.href = `play.php?videoId=${videoId}&ip=${ip}`;
                        }
                    };
                    xhr.send();
                });
            });

            const downloadInProgress = localStorage.getItem('downloadInProgress');
            if (downloadInProgress) {
                const modal = document.getElementById('modal');
                modal.style.display = 'flex';
            }
        });

        window.onpageshow = function(event) {
            if (event.persisted) {
                const modal = document.getElementById('modal');
                modal.style.display = 'none';
                localStorage.removeItem('downloadInProgress');
            }
        };
    </script>
</body>
</html>

