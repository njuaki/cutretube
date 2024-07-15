<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargando Video de YouTube</title>
</head>
<body>
    <h1>Descargando Video...</h1>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function vaciarCarpeta($carpeta) {
        $ficheros = glob($carpeta . '/*');
        foreach ($ficheros as $fichero) {
            if (is_file($fichero)) {
                unlink($fichero);
            }
        }
    }

    vaciarCarpeta('/var/www/html/videos');

    if (isset($_GET['videoId'])) {
        $videoId = htmlspecialchars($_GET['videoId']);
        $outputFile = "/var/www/html/videos/$videoId.mp4";

        $ytDlpPath = '/home/usuario/myenv/bin/yt-dlp';

        $command = "$ytDlpPath -f 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/mp4' --output '$outputFile' https://www.youtube.com/watch?v=$videoId 2>&1";

        echo "<pre>Comando a ejecutar: $command</pre>";

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $process = proc_open($command, $descriptorspec, $pipes, realpath('./'), []);

        if (is_resource($process)) {
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_value = proc_close($process);

            echo "<pre>Salida: $stdout</pre>";
            echo "<pre>Error: $stderr</pre>";
            echo "<pre>Código de retorno: $return_value</pre>";

            if (file_exists($outputFile)) {
                echo "<script>window.location.href='play.php?videoId=$videoId';</script>";
                exit();
            } else {
                echo "<p>Error al descargar el video.</p>";
                echo "<pre>$stderr</pre>";
            }
        } else {
            echo "<p>No se pudo iniciar el proceso para ejecutar el comando.</p>";
        }
    } else {
        echo "<p>No se proporcionó un ID de video.</p>";
    }
    ?>
</body>
</html>
