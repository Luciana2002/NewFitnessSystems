<?php

namespace App\Models;

use CodeIgniter\Model;

class DatosPersonalesModel extends Model
{
    protected $table = 'Datos_personales';
    protected $primaryKey = 'id_persona';

    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'dni'
    ];
}