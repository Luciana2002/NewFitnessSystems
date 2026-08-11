<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\SistemaModel;

class ReporteController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    public function index()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();

        $clientes = $clienteModel->getClientesConInfo();

        $total = count($clientes);
        $alDia = 0;
        $vencido = 0;
        $sinPagos = 0;
        $sinSuscripcion = 0;
        $sistemas = [];
        $vencidosPorSistema = [];

        foreach ($clientes as $cliente) {
            $sistemasCliente = $cliente['sistemas'] ?? [];
            $ultimoPago = $cliente['ultimo_pago'] ?? null;

            $sistemasActivos = array_filter($sistemasCliente, function ($s) {
                return ($s['estado_suscripcion'] ?? '') !== 'Cancelada';
            });

            foreach ($sistemasActivos as $sistema) {
                $nombreSistema = $sistema['nombre_sistema'] ?? 'Sin sistema';
                $sistemas[$nombreSistema] = ($sistemas[$nombreSistema] ?? 0) + 1;

                $tsVenc = !empty($sistema['fecha_vencimiento'])
                    ? strtotime((string) $sistema['fecha_vencimiento'])
                    : null;

                if ($tsVenc && $tsVenc < strtotime(date('Y-m-d'))) {
                    $vencidosPorSistema[$nombreSistema] = ($vencidosPorSistema[$nombreSistema] ?? 0) + 1;
                }
            }

            if (empty($sistemasActivos)) {
                $sinSuscripcion++;
            } elseif (empty($ultimoPago)) {
                $sinPagos++;
            } else {
                $hoyTs = strtotime(date('Y-m-d'));
                $vencidoCliente = false;

                foreach ($sistemasActivos as $sistema) {
                    $tsVenc = !empty($sistema['fecha_vencimiento'])
                        ? strtotime((string) $sistema['fecha_vencimiento'])
                        : null;

                    if ($tsVenc && $tsVenc < $hoyTs) {
                        $vencidoCliente = true;
                        break;
                    }
                }

                if ($vencidoCliente) {
                    $vencido++;
                } else {
                    $alDia++;
                }
            }
        }

        arsort($sistemas);

        // Recomendaciones de promos según la demanda
        $sistemaModel = new SistemaModel();
        $sistemasPrecios = [];

        foreach ($sistemaModel->getSistemasConPrecio(false) as $s) {
            $sistemasPrecios[$s['nombre_sistema']] = (float) ($s['precio'] ?? 0);
        }

        $recomendaciones = [];
        $topNombre = !empty($sistemas) ? array_key_first($sistemas) : null;

        if ($topNombre) {
            $recomendaciones[] = [
                'tipo'     => 'estrella',
                'sistema'  => $topNombre,
                'precio'   => $sistemasPrecios[$topNombre] ?? 0,
                'activos'  => $sistemas[$topNombre],
                'vencidos' => $vencidosPorSistema[$topNombre] ?? 0,
                'mensaje'  => "Es el sistema con más suscripciones activas. 
                            Aprovechalo para aplicar promos o descuentos 
                            antes que para subir el precio."
            ];
        }

        foreach ($sistemas as $nombre => $activos) {
            if ($nombre === $topNombre) continue;

            $vencidos = $vencidosPorSistema[$nombre] ?? 0;
            $precio   = $sistemasPrecios[$nombre] ?? 0;
            $ratio    = $activos > 0 ? $vencidos / $activos : 0;

            if ($ratio >= 0.4) {
                $recomendaciones[] = [
                    'tipo'     => 'retencion',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Alta proporción de cuotas vencidas.
                                Ofrecé una promo de 2x1 en la cuota del mes 
                                o 25% de descuento por 30 días para recuperar 
                                clientes."
                ];
            } elseif ($ratio <= 0.15 && $activos >= 5) {
                $recomendaciones[] = [
                    'tipo'     => 'subir-precio',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Alta demanda y cuotas al día: hay margen 
                                para subir el precio de $" . number_format($precio, 0, ',', '.') . " 
                                sin perder clientes."
                ];
            } elseif ($activos < 3) {
                $recomendaciones[] = [
                    'tipo'     => 'captacion',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Poca demanda. Lanzá una promo de captación: 
                                primer mes con 50% de descuento o una semana de 
                                prueba gratis."
                ];
            }

            if (count($recomendaciones) >= 6) break;
        }

        $data['total'] = $total;
        $data['alDia'] = $alDia;
        $data['vencido'] = $vencido;
        $data['sinPagos'] = $sinPagos;
        $data['sinSuscripcion'] = $sinSuscripcion;
        $data['sistemas'] = $sistemas;
        $data['recomendaciones'] = $recomendaciones;

        return view('front/header')
             . view('front/navbar')
             . view('administrador/reporte', $data)
             . view('front/footer');
    }
}
