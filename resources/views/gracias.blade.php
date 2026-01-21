<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kombiforms - Gracias</title>
    <style>
        :root {
            --verde-kombi: #05564f;
            --gris-suave: #f4f4f4;
            --gris-texto: #333;
            --blanco: #ffffff;
            --borde-suave: #e1e1e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: var(--gris-suave);
            color: var(--gris-texto);
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            background: var(--blanco);
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--borde-suave);
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .container h1 {
            font-size: 36px;
            color: var(--verde-kombi);
            margin-bottom: 10px;
        }

        .container h2 {
            font-size: 24px;
            margin-top: 10px;
        }

        .container p {
            margin-bottom: 25px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            background: var(--verde-kombi);
            color: var(--blanco);
            padding: 14px 26px;
            border-radius: 8px;
            font-size: 18px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn:hover {
            background: #044b46;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Kombitec</h1>
    <h2>¡Gracias por contestar la encuesta! 🙌</h2>

    <p>
        Tu respuesta ha sido registrada correctamente.<br>
        Agradecemos el tiempo que te tomaste para ayudarnos a mejorar.
    </p>
</div>

</body>
</html>
