<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
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

        return view('front/header')
             . view('front/navbar')
             . view('reportes/index')
             . view('front/footer');
    }

    public function cuotas()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $resumen = $this->resumenCuotas();

        $data['total']   = $resumen['total'];
        $data['alDia']   = $resumen['alDia'];
        $data['vencido'] = $resumen['vencido'];
        $data['sistemas'] = $resumen['sistemas'];

        return view('front/header')
             . view('front/navbar')
             . view('reportes/cuotas', $data)
             . view('front/footer');
    }

    public function liquidacion()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $pagoModel = new PagoModel();
        $data['liquidacionProfesores'] = $pagoModel->getLiquidacionProfesores(0.30);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/liquidacion', $data)
             . view('front/footer');
    }

    public function ingresos()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $pagoModel = new PagoModel();

        $data['ingresosPorSistema'] = $pagoModel->getIngresosPorSistema();

        $totalGanado = $pagoModel->getTotalGanado();
        $data['totalGanado'] = (float) ($totalGanado['total'] ?? 0);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/ingresos', $data)
             . view('front/footer');
    }

    public function ingresosData($meses = 6)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $meses = max(1, min((int) $meses, 60));

        $pagoModel = new PagoModel();
        $ingresosMensuales = $pagoModel->getIngresosPorMes($meses);

        $mesesCortos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $ingresosOrdenados = array_reverse($ingresosMensuales);
        $etiquetas = array_map(function ($r) use ($mesesCortos) {
            return $mesesCortos[$r['mes'] - 1] . ' ' . $r['anio'];
        }, $ingresosOrdenados);
        $datos = array_map(function ($r) {
            return (float) $r['total'];
        }, $ingresosOrdenados);

        return $this->response->setJSON([
            'etiquetas'    => $etiquetas,
            'datos'        => $datos,
            'totalPeriodo' => array_sum($datos)
        ]);
    }

    public function promos()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $resumen = $this->resumenCuotas();
        $data['recomendaciones'] = $this->recomendaciones($resumen['sistemas'], $resumen['vencidosPorSistema']);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/promos', $data)
             . view('front/footer');
    }

    private function resumenCuotas()
    {
        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->getClientesConInfo();

        $total = count($clientes);
        $alDia = 0;
        $vencido = 0;
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

            if (empty($sistemasActivos) || empty($ultimoPago)) {
                continue;
            }

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

        arsort($sistemas);

        return [
            'total'             => $total,
            'alDia'             => $alDia,
            'vencido'           => $vencido,
            'sistemas'          => $sistemas,
            'vencidosPorSistema' => $vencidosPorSistema
        ];
    }

    private function recomendaciones($sistemas, $vencidosPorSistema)
    {
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

        return $recomendaciones;
    }
}