<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = substr(md5(microtime()), 0, 9);
    rename($_FILES['file']['tmp_name'], 'uploaded.pdf');
    shell_exec('cp squish2on1.tex ' . $code . '.tex');
    shell_exec('pdflatex ' . $code . '.tex');
    shell_exec('rm text.pdf ' . $code . '.tex ' . $code . '.aux ' . $code . '.log');

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
        <p><input type="submit" value="submit"></p>
    </form>
    </body>
</html>

