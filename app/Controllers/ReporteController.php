<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\SistemaModel;
use App\Models\GastoModel;
use App\Models\BitacoraModel;
use App\Models\ConfiguracionModel;

class ReporteController extends BaseController
{
    private const PORCENTAJE_DEFAULT = 30;

    private function comisionPorcentaje(): int
    {
        $valor = (new ConfiguracionModel())->getValor('comision_profesor', self::PORCENTAJE_DEFAULT);
        $valor = (int) $valor;

        if ($valor < 0) {
            $valor = 0;
        }

        if ($valor > 100) {
            $valor = 100;
        }

        return $valor;
    }

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

        $fecha = (string) ($this->request->getGet('fecha') ?: date('Y-m-d'));
        $data = $this->cajaData($fecha);
        $data['totalCaja'] = $data['total'];
        unset($data['total']);

        $resumen = $this->resumenCuotas();
        $mesActual = date('Y-m');
        $data['mesGanancias'] = $mesActual;
        $data['total']    = $resumen['total'];
        $data['alDia']    = $resumen['alDia'];
        $data['vencido']  = $resumen['vencido'];
        $data['sistemas'] = $resumen['sistemas'];
        $data['ingresosPorSistema'] = (new PagoModel())->getIngresosPorSistema($mesActual);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/index', $data)
             . view('front/footer');
    }

    public function cuotas()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $resumen = $this->resumenCuotas();
        $mesActual = date('Y-m');
        $data['mesGanancias'] = $mesActual;

        $data['total']   = $resumen['total'];
        $data['alDia']   = $resumen['alDia'];
        $data['vencido'] = $resumen['vencido'];
        $data['sistemas'] = $resumen['sistemas'];
        $data['ingresosPorSistema'] = (new PagoModel())->getIngresosPorSistema($mesActual);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/cuotas', $data)
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

    /* ==========================================================
       CIERRE DE CAJA DIARIO
    ========================================================== */

    public function caja()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $fecha = (string) ($this->request->getGet('fecha') ?: date('Y-m-d'));
        $data = $this->cajaData($fecha);

        return view('front/header')
             . view('front/navbar')
             . view('reportes/caja', $data)
             . view('front/footer');
    }

    private function cajaData(string $fecha): array
    {
        $pagoModel = new PagoModel();
        $pagos = $pagoModel->getPagosPorFecha($fecha);

        $porMedio = [];
        $porCobrador = [];
        $total = 0.0;

        foreach ($pagos as $pago) {
            $monto = (float) $pago['monto'];
            $total += $monto;

            $medio = $pago['medio_pago'] ?: 'Sin especificar';
            $porMedio[$medio] = ($porMedio[$medio] ?? 0) + $monto;

            $cob = trim(($pago['cobrador_nombre'] ?? '') . ' ' . ($pago['cobrador_apellido'] ?? ''));
            if ($cob === '') {
                $cob = $pago['cobrador_usuario'] ?: 'Sin registrar';
            }
            $porCobrador[$cob] = ($porCobrador[$cob] ?? 0) + $monto;
        }

        arsort($porMedio);
        arsort($porCobrador);

        return [
            'fecha'       => $fecha,
            'pagos'       => $pagos,
            'porMedio'    => $porMedio,
            'porCobrador' => $porCobrador,
            'total'       => $total,
        ];
    }

    /* ==========================================================
       GASTOS Y BALANCE
    ========================================================== */

    public function balance()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $mes = (string) ($this->request->getGet('mes') ?: date('Y-m'));
        $data = $this->balanceData($mes);
        $data['porcentaje'] = $this->comisionPorcentaje();

        return view('front/header')
             . view('front/navbar')
             . view('reportes/balance', $data)
             . view('front/footer');
    }

    public function actualizarComision()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $porcentaje = (int) $this->request->getPost('porcentaje');
        $mes        = (string) ($this->request->getPost('mes') ?: date('Y-m'));

        if ($porcentaje < 0) {
            $porcentaje = 0;
        }

        if ($porcentaje > 100) {
            $porcentaje = 100;
        }

        (new ConfiguracionModel())->setValor('comision_profesor', (string) $porcentaje);

        return redirect()->to('/suscripciones/balance?mes=' . $mes)
            ->with('success', 'Porcentaje de comisión actualizado a ' . $porcentaje . '%.');
    }

    private function balanceData(string $mes): array
    {
        $pagoModel  = new PagoModel();
        $gastoModel = new GastoModel();

        $ingresos = $pagoModel->getTotalMes($mes);

        $liquidacion = $pagoModel->getLiquidacionProfesores($this->comisionPorcentaje() / 100, $mes);

        $sueldos = 0.0;
        foreach ($liquidacion as $prof) {
            $sueldos += (float) $prof['comision'];
        }

        $otrosGastos = $gastoModel->getTotalMes($mes, ['Sueldos']);

        return [
            'mes'                => $mes,
            'ingresos'           => $ingresos,
            'liquidacion'        => $liquidacion,
            'sueldos'            => $sueldos,
            'gastosPorCategoria' => $gastoModel->getTotalesPorCategoria($mes, ['Sueldos']),
            'otrosGastos'        => $otrosGastos,
            'totalGastos'        => $sueldos + $otrosGastos,
            'neto'               => $ingresos - $sueldos - $otrosGastos,
            'listaGastos'        => $gastoModel->getGastosMes($mes, ['Sueldos']),
            'categorias'         => $gastoModel->getCategorias(),
        ];
    }

    /* ==========================================================
       BITACORA / AUDITORIA
    ========================================================== */

    public function bitacora()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $data = $this->bitacoraData($this->bitacoraFiltros());

        return view('front/header')
             . view('front/navbar')
             . view('reportes/bitacora', $data)
             . view('front/footer');
    }

    private function bitacoraFiltros(): array
    {
        $hoy  = date('Y-m-d');
        $ayer = date('Y-m-d', strtotime('-1 day'));

        $desde = trim((string) $this->request->getGet('desde')) ?: $ayer;
        $hasta = trim((string) $this->request->getGet('hasta')) ?: $hoy;

        // Rango máximo de 1 año: evita cargar registros ilimitados.
        if (strtotime($desde) < strtotime($hasta . ' -1 year')) {
            $desde = date('Y-m-d', strtotime($hasta . ' -1 year'));
        }

        return [
            'entidad' => trim((string) $this->request->getGet('entidad')),
            'accion'  => trim((string) $this->request->getGet('accion')),
            'usuario' => trim((string) $this->request->getGet('usuario')),
            'desde'   => $desde,
            'hasta'   => $hasta,
            'buscar'  => trim((string) $this->request->getGet('buscar')),
        ];
    }

    private function bitacoraData(array $filtros): array
    {
        $bitacoraModel = new BitacoraModel();

        return [
            'registros' => $bitacoraModel->getRegistros($filtros, 400),
            'usuarios'  => $bitacoraModel->getUsuarios(),
            'entidades' => $bitacoraModel->getEntidades(),
            'filtros'   => $filtros,
        ];
    }

    /* ==========================================================
       EXPORTAR (CSV)
    ========================================================== */

    public function exportar($tipo = '')
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        switch (strtolower(trim((string) $tipo))) {
            case 'cuotas':
                $r = $this->resumenCuotas();
                $filas = [];
                foreach ($r['sistemas'] as $nombre => $activos) {
                    $filas[] = [
                        'sistema'  => $nombre,
                        'activos'  => $activos,
                        'vencidos' => $r['vencidosPorSistema'][$nombre] ?? 0,
                    ];
                }
                return nfs_csv('reporte_cuotas.csv', ['Sistema', 'Suscripciones activas', 'Cuotas vencidas'], $filas);

            case 'ingresos':
                $pagoModel = new PagoModel();
                $filas = [];
                foreach ($pagoModel->getIngresosPorSistema() as $s) {
                    $filas[] = [
                        'sistema' => $s['nombre_sistema'],
                        'total'   => number_format((float) $s['total'], 2, ',', '.'),
                    ];
                }
                $total = $pagoModel->getTotalGanado();
                $filas[] = ['sistema' => 'TOTAL', 'total' => number_format((float) ($total['total'] ?? 0), 2, ',', '.')];
                return nfs_csv('reporte_ingresos.csv', ['Sistema', 'Total'], $filas);

            case 'promos':
                $r = $this->resumenCuotas();
                $recs = $this->recomendaciones($r['sistemas'], $r['vencidosPorSistema']);
                $filas = [];
                foreach ($recs as $rec) {
                    $filas[] = [
                        'tipo'    => $rec['tipo'],
                        'sistema' => $rec['sistema'],
                        'precio'  => number_format((float) $rec['precio'], 2, ',', '.'),
                        'activos' => $rec['activos'],
                        'vencidos' => $rec['vencidos'],
                        'mensaje' => trim(preg_replace('/\s+/', ' ', $rec['mensaje'])),
                    ];
                }
                return nfs_csv('reporte_promos.csv', ['Tipo', 'Sistema', 'Precio', 'Activos', 'Vencidos', 'Recomendacion'], $filas);

            case 'caja':
                $fecha = (string) ($this->request->getGet('fecha') ?: date('Y-m-d'));
                $d = $this->cajaData($fecha);
                $filas = [];
                foreach ($d['pagos'] as $p) {
                    $filas[] = [
                        'fecha'    => $fecha,
                        'monto'    => number_format((float) $p['monto'], 2, ',', '.'),
                        'medio'    => $p['medio_pago'] ?: 'Sin especificar',
                        'cobrador' => trim(($p['cobrador_nombre'] ?? '') . ' ' . ($p['cobrador_apellido'] ?? '')) ?: ($p['cobrador_usuario'] ?: 'Sin registrar'),
                        'cliente'  => trim(($p['cliente_apellido'] ?? '') . ', ' . ($p['cliente_nombre'] ?? '')),
                    ];
                }
                $filas[] = ['fecha' => '', 'monto' => '', 'medio' => 'TOTAL', 'cobrador' => '', 'cliente' => number_format($d['total'], 2, ',', '.')];
                return nfs_csv('cierre_caja_' . $fecha . '.csv', ['Fecha', 'Monto', 'Medio de pago', 'Cobrado por', 'Cliente'], $filas);

            case 'balance':
                $mes = (string) ($this->request->getGet('mes') ?: date('Y-m'));
                $d = $this->balanceData($mes);
                $filas = [];
                foreach ($d['gastosPorCategoria'] as $g) {
                    $filas[] = ['concepto' => 'Gasto: ' . $g['categoria'], 'monto' => number_format((float) $g['total'], 2, ',', '.')];
                }
                $filas[] = ['concepto' => 'Sueldos (comisiones ' . $this->comisionPorcentaje() . '%)', 'monto' => number_format($d['sueldos'], 2, ',', '.')];
                $filas[] = ['concepto' => 'Ingresos', 'monto' => number_format($d['ingresos'], 2, ',', '.')];
                $filas[] = ['concepto' => 'Total gastos', 'monto' => number_format($d['totalGastos'], 2, ',', '.')];
                $filas[] = ['concepto' => 'Ganancia neta', 'monto' => number_format($d['neto'], 2, ',', '.')];
                return nfs_csv('balance_' . $mes . '.csv', ['Concepto', 'Monto'], $filas);

            case 'bitacora':
                $filtros = $this->bitacoraFiltros();
                $registros = (new BitacoraModel())->getRegistros($filtros, 5000);
                $filas = [];
                foreach ($registros as $r) {
                    $filas[] = [
                        'fecha'   => $r['fecha'],
                        'usuario' => $r['nombre_usuario'],
                        'accion'  => $r['accion'],
                        'entidad' => $r['entidad'],
                        'id'      => $r['id_entidad'],
                        'detalle' => $r['detalle'],
                    ];
                }
                return nfs_csv('bitacora.csv', ['Fecha', 'Usuario', 'Accion', 'Entidad', 'ID', 'Detalle'], $filas);

            default:
                return redirect()->to('/suscripciones');
        }
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