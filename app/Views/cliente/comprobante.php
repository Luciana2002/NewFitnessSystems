<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante <?= esc($nro) ?> - <?= esc($empresa) ?></title>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #e9e9e9;
            margin: 0;
            padding: 30px;
            color: #222;
        }

        .comprobante {
            background: #fff;
            width: 720px;
            max-width: 100%;
            margin: 0 auto;
            padding: 45px 50px;
            border-radius: 8px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.2);
        }

        .cabecera {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #0b8f70;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .marca h1 {
            margin: 0;
            font-size: 26px;
            color: #0b8f70;
            letter-spacing: 1px;
        }

        .marca p { margin: 4px 0 0; font-size: 13px; color: #666; }

        .nro { text-align: right; }
        .nro .titulo { font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .nro .valor { font-size: 20px; font-weight: 800; }

        h2 { font-size: 16px; color: #0b8f70; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px; }

        .datos { display: flex; gap: 40px; margin-bottom: 30px; }
        .datos > div { flex: 1; }

        .datos p { margin: 4px 0; font-size: 14px; }
        .datos p span { color: #666; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { text-align: left; background: #f2f2f2; padding: 12px; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
        table td { padding: 14px 12px; border-bottom: 1px solid #e2e2e2; font-size: 15px; }

        .total { text-align: right; font-size: 22px; font-weight: 800; margin-bottom: 30px; }
        .total span { color: #0b8f70; }

        .pie { border-top: 1px dashed #bbb; padding-top: 18px; font-size: 12px; color: #888; text-align: center; }

        .acciones { text-align: center; margin: 25px 0 0; }
        .acciones button {
            background: #0b8f70; color: #fff; border: none; padding: 14px 34px;
            font-size: 16px; font-weight: 700; border-radius: 30px; cursor: pointer;
            letter-spacing: .5px;
        }
        .acciones button:hover { background: #097a61; }

        @media print {
            body { background: #fff; padding: 0; }
            .comprobante { box-shadow: none; width: 100%; padding: 0; }
            .acciones { display: none; }
        }
    </style>
</head>
<body>

    <div class="comprobante">
        <div class="cabecera">
            <div class="marca">
                <h1><?= esc($empresa) ?></h1>
                <p>Córcega 4426 &mdash; WhatsApp 3794-268872</p>
                <p>newfitnesssystem@gmail.com</p>
            </div>
            <div class="nro">
                <div class="titulo">Comprobante N°</div>
                <div class="valor"><?= esc($nro) ?></div>
                <div style="font-size:13px; color:#666; margin-top:6px;"><?= formatear_fecha($pago['fecha_pago']) ?></div>
            </div>
        </div>

        <div class="datos">
            <div>
                <h2>Cliente</h2>
                <p><span>Nombre:</span> <?= esc($pago['nombre'] . ' ' . $pago['apellido']) ?></p>
                <p><span>DNI:</span> <?= esc($pago['dni']) ?></p>
                <p><span>Email:</span> <?= esc($pago['email'] ?? '-') ?></p>
                <p><span>Teléfono:</span> <?= esc($pago['telefono'] ?? '-') ?></p>
            </div>
            <div>
                <h2>Detalle</h2>
                <p><span>Sistema:</span> <?= esc($pago['nombre_sistema']) ?></p>
                <p><span>Medio de pago:</span> <?= esc($pago['medio_pago'] ?? '-') ?></p>
                <p><span>Estado cuota:</span> <?= esc($pago['estado_suscripcion'] ?? '-') ?></p>
                <p><span>Vence:</span> <?= formatear_fecha($pago['fecha_vencimiento']) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th style="text-align:right;">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cuota mensual &mdash; <?= esc($pago['nombre_sistema']) ?></td>
                    <td style="text-align:right;"><?= formatear_monto($pago['monto']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total">TOTAL: <span><?= formatear_monto($pago['monto']) ?></span></div>

        <div class="pie">
            Recibido por: <?= esc($pago['cobrado_por'] ?? '-') ?><br>
            Este comprobante es válido como constancia de pago. Gracias por entrenar con nosotros.
        </div>
    </div>

    <div class="acciones">
        <button type="button" onclick="window.print()">Descargar PDF / Imprimir</button>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 400);
        });
    </script>

</body>
</html>
