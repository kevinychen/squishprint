<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = substr(md5(microtime()), 0, 9);
    rename($_FILES['file']['tmp_name'], 'uploaded.pdf');
    if ($_POST['squishnum'] === '2on1') {
        $tex_file = 'squish2on1.tex';
    } else if ($_POST['squishnum'] === '4on1') {
        $tex_file = 'squish4on1.tex';
    }
    shell_exec('cp ' . $tex_file . ' ' . $code . '.tex');
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
        <p>How much to squish?</p>
        <input type="radio" name="squishnum" value="2on1">2 pages per sheet<br/>
        <input type="radio" name="squishnum" value="4on1">4 pages per sheet<br/>
        <p><input type="submit" value="submit"></p>
    </form>
    </body>
</html>

