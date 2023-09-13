<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Page</title>
</head>
<body>
    <h1>File Upload Page</h1>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label for="file">Select a file:</label>
        <input type="file" name="file" id="file" accept=".jpg, .png, .pdf">
        <br>
        <button type="submit">Upload File</button>
    </form>
</body>
</html>
