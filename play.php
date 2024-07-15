<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'Reproduciendo Video'; ?></title>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            localStorage.removeItem('downloadInProgress');
        });
    </script>
</head>
<body>
    <div class="content">
        <h1><span class="cutre">CUTRE</span><span class="tube">TUBE</span></h1>
        <h2>Ahora un poco menos cutre</h2>
        <form method="GET" action="index.php">
            <input type="text" name="query" placeholder="Introduce tu búsqueda" required>
            <button type="submit">Buscar</button>
        </form>
        <div class="main-container">
            <div class="video-container">
                <?php
                if (isset($_GET['videoId']) && isset($_GET['ip'])) {
                    $videoId = htmlspecialchars($_GET['videoId']);
                    $ip = htmlspecialchars($_GET['ip']);
                    $videoPath = "/var/www/html/videos/$ip/{$videoId}.mp4";

                    if (file_exists($videoPath)) {
                        echo "<video controls>
                                <source src='/videos/$ip/{$videoId}.mp4' type='video/mp4'>
                                Tu navegador no soporta la reproducción de videos.
                              </video>";
                    } else {
                        echo "<p>El video aún no está disponible. Por favor, inténtelo de nuevo en unos minutos.</p>";
                    }
                } else {
                    echo "<p>No se proporcionó un ID de video o una IP.</p>";
                }
            ?>
            </div>
            <div class="recommendations-container">
                <h2>Recomendaciones</h2>
                <div class="grid-container">
                    <?php
                    $recommendationsFile = '/var/www/html/recommendations.json';
                    if (file_exists($recommendationsFile)) {
                        $recommendations = json_decode(file_get_contents($recommendationsFile), true);
                        foreach ($recommendations as $video) {
                            echo '<div class="grid-item">';
                            echo "<a href='download.php?videoId={$video['videoId']}'><img src='tmpIma/{$video['videoId']}.jpg' alt='Thumbnail'></a>";
                            echo "<div class='grid-item-title'>{$video['title']}</div>";
                            echo "<a class='ver-video' href='download.php?videoId={$video['videoId']}'>Ver video</a>";
                            echo '</div>';
                        }
                    } else {
                        echo "<p>No hay recomendaciones disponibles.</p>";
                    }
                    ?>
                </div>
            </div>
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
</body>
</html>

