<!DOCTYPE html>
<html lang="pt_BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center"> {{ $title }} </h1>
        <form action="{{ route($route) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="row mb-3">
                <div class="col">
                    <label for="file" class="form-label">Escolha o arquivo</label>
                    <input type="file" class="form-control" id="pdfFile" name="file" required>
                </div>
                <div class="col">
                    <label for="tipo_documento" class="form-label">Tipo Documento</label>
                    <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                        @foreach ($tipo_documento as $documento)
                            <option value="{{ $documento }}">{{ $documento }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
