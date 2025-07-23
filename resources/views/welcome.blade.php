<!DOCTYPE html>
<html>
<head>
    <title>Subir archivo a S3</title>
</head>
<body>
    <h1>Subir archivo a S3</h1>
    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Subir</button>
    </form>
</body>
</html>
