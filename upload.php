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
    if ($_POST['nup'] === '2on1') {
        $nup = '1x2';
        $landscape = 'true';
    } else if ($_POST['nup'] === '4on1') {
        $nup = '2x2';
        $landscape = 'false';
    } else {
        array_push($errors, 'Invalid number of pages per sheet parameter');
    }
    if (!preg_match('/[01](\.[0-9]?)/', $_POST['vert_delta'])) {
        array_push($errors, 'Invalid vertical squish amount parameter');
    } else if ($_POST['vert_delta'] === '0') {
        $vert_delta = '0';
    } else {
        $vert_delta = '-' + $_POST['vert_delta'] + 'in';
    }
    if (!preg_match('/[01](\.[0-9]?)/', $_POST['horiz_delta'])) {
        array_push($errors, 'Invalid horizontal squish amount parameter');
    } else if ($_POST['horiz_delta'] === '0') {
        $horiz_delta = '0';
    } else {
        $horiz_delta = '-' + $_POST['horiz_delta'] + 'in';
    }
    $vert_delta = '0';
    $horiz_delta = '-1in';
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
        <p>How much to squish?</p>
        <input type="radio" name="nup" value="2on1">2 pages per sheet<br/>
        <input type="radio" name="nup" value="4on1">4 pages per sheet<br/>
        <p><input type="submit" value="submit"></p>
    </form>
    </body>
</html>

