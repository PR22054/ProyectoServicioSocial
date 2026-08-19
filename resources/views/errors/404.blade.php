<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 404</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff; border-radius: 8px;
            box-shadow: 0 2px 16px rgba(0,0,0,.1);
            padding: 3rem 4rem; text-align: center;
            max-width: 480px; width: 90%;
        }
        .code { font-size: 5rem; font-weight: 700; color: #fd7e14; line-height: 1; }
        h2 { color: #343a40; margin: .75rem 0 .5rem; font-size: 1.4rem; }
        p { color: #6c757d; margin-bottom: 2rem; font-size: .95rem; }
        .actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block; padding: .5rem 1.4rem;
            border-radius: 4px; text-decoration: none;
            font-size: .9rem; font-weight: 500; cursor: pointer;
        }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-secondary:hover { background: #545b62; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <h2>Pagina no encontrada</h2>
        <p>La pagina que buscas no existe o fue movida. Verifica la URL o regresa al panel.</p>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Volver atras</a>
            <a href="/admin/dashboard" class="btn btn-primary">Ir al panel</a>
        </div>
    </div>
</body>
</html>
