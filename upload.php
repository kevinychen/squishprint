<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = substr(md5(microtime()), 0, 9);
    $errors = array();
    $format = '
\documentclass{article}
\usepackage{pdfpages}
\begin{document}
\includepdf[nup = %s, landscape=%s, delta=%s %s, pages={1-}]{uploaded.pdf}
\end{document}
';
    if (!preg_match('/[01](\.[02468]?)/', $_POST['delta'])) {
        array_push($errors, 'Invalid vertical squish amount parameter');
    } else {
        $delta = floatval($_POST['delta']);
    }
    if ($_POST['nup'] === '2on1') {
        $nup = '1x2';
        $landscape = 'true';
        $vert_delta = '0';
        $horiz_delta = '-' . strval($delta) . 'in';
    } else if ($_POST['nup'] === '4on1') {
        $nup = '2x2';
        $landscape = 'false';
        $vert_delta = '-' . strval($delta / 2) . 'in';
        $horiz_delta = '-' . strval($delta / 2) . 'in';
    }
    $latex = sprintf($format, $nup, $landscape, $vert_delta, $horiz_delta);
    file_put_contents($code . '.tex', $latex);

    rename($_FILES['file']['tmp_name'], 'uploaded.pdf');
    shell_exec('pdflatex ' . $code . '.tex');
    shell_exec('rm uploaded.pdf ' . $code . '.tex ' . $code . '.aux ' . $code . '.log');

    header('Location: /squishprint/' . $code . '.pdf');
}
?>
<html>
    <head>
    </head>
    <body>
    <h1>Squish Print</h1>
    <form method="post" action="/squishprint/upload.php" enctype="multipart/form-data">
        <p>Upload a PDF: <input type="file" name="file"></p>
        <input type="radio" name="nup" value="2on1" checked>2 pages per sheet<br/>
        <input type="radio" name="nup" value="4on1">4 pages per sheet<br/>
        Loose<input type="range" name="delta" min="0" max="1.99" step="0.2" value="1">Squished</br>
        <p><input type="submit" value="submit"></p>
    </form>
    </body>
</html>

