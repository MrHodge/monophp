<?php

use mono\Core;


/**
 * spanish.php Sep 29, 2015
 * Copyright (c) 2015 Venom Services
 *
 * LICENSE:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author VenomServices <contact@venomservices.com>
 * @copyright 2015 VenomServices
 * @license https://opensource.org/licenses/MIT
 * @link https://venomservices.com
 */
return [
    'validation' => [
        'required' => 'Se requiere %s.',
        'url' => '%s debe ser una URL válida.',
        'alphabet' => '%s debe contener sólo letras del alfabeto.',
        'numeric' => '%s debe contener sólo números.',
        'alphanumeric' => '%s debe contener sólo letras del alfabeto y números.',
        'min-text' => '%s debe haber un mínimo de %s caracteres.',
        'max-text' => '%s debe ser un máximo de %s caracteres.',
        'min-num' => '%s debe haber un mínimo de %s.',
        'max-num' => '%s debe ser un máximo de %s.',
        'email' => '%s debe ser un correo electrónico válido.',
        'password_doesnt_match' => 'Las contraseñas no coinciden.',
        'select' => '%s debe ser %s.',
    ],
    'log' => [
        'plugin_enable' => "Plugin de Habilitación %s v%s",
        'plugin_disable' => "Plugin de Desactivación de %s v%s",
        'loaded_controller' => "El controlador \"%s\" se cargó utilizando el método de \"%s\" con argumentos: %s",
        'csrfToken_generated' => "CSRF genera token. Valor simbólico: \"%s\"",
        'csrfToken_verified' => "CSRF token de verificación aprobada. Valor simbólico: \"%s\"",
        'csrfToken_unverified' => "CSRF falló verificación token. Valor simbólico: \"%s\", que se espera valor simbólico: \"%s\"",
        '404error' => "404 error tirado del archivo: \"%s\" en línea: %s.",
        'loaded' => "Acabado en %s segundo(s).",
    ],
    'errors' => [
        'config_error_0' => "No se encontró el archivo de configuración \"%s\".",
        'config_error_1' => "El camino configuración solicitada \"%s\" no es una cadena.",
        'config_error_2' => "El camino configuración solicitada \"%s\" no es un entero.",
        'config_error_3' => "El camino configuración solicitada \"%s\" no es un booleano.",
        'config_error_4' => "El camino configuración solicitada \"%s\" no es una matriz.",
        'database_error_0' => "Trató de utilizar la base de datos mientras está desactivado. File: \"%s\". Línea: %s",
        'database_error_1' => "Tipo no identificado de base de datos. Por favor, use uno de los siguientes: %s",
        'database_error_2' => "Esperando el parámetro \"%s\" pasó a \"%s\" para tener al menos %s.",
        'database_error_3' => "Esperando tener al menos una columna al crear la tabla \"%s\".",
        'controller_exists' => "El controlador \"%s\" ya existe.",
        'plugins_dir_notfound' => "Tél plugins no se encontró. No hay plugins serán cargados.",
        'controller_error_0' => "El controlador \"%s\" no se ha encontrado en el directorio: \"%s\"",
        'controller_error_1' => "La clase controlador \"%s\" no se encontró en el archivo PHP \"%s\"",
        'controller_error_2' => "El controlador \"%s\" no se carga como un controlador Mono.",
        'controller_error_3' => "El controlador \"%s\" no se extiende Controller.",
        'controller_error_4' => "El método %s no se encontró en el controlador de %s. Volviendo al método \"índice\" de forma predeterminada.",
        'controller_error_5' => "La vista de archivo \"%s\" no se ha encontrado para el controlador de \"%s\".",
        'controller_error_6' => "No hubo controladores por defecto cargados por la configuración de la aplicación. Si la página está en blanco, esta puede ser la razón.",
        'plugin_error_0' => "El plugin.json no se encontró para el plugin \"%s\".",
        'plugin_error_1' => "El plugin.json para el \"%s\" plug-in no es válido.",
        'plugin_error_2' => "El nombre del plugin \"%s\" ya está en uso. File: \"%s\".",
        'plugin_error_3' => "No se encontró la clase principal de \"%s\" para el plugin \"%s\".",
        'plugin_error_4' => "La clase principal de \"%s\" para el plugin \"%s\" no está extendiendo la clase Plugin.",
        'plugin_error_5' => "El plugin \"%s\" ya estaba activada.",
        'plugin_error_6' => "El plugin \"%s\" nunca fue habilitado.",
        'plugin_error_7' => "El plugin \"%s\" requiere una versión de PHP que es mayor que v%s, incapacitante.",
        'plugin_error_8' => "El plugin \"%s\" requiere una versión de PHP que es menor que v%s, incapacitante.",
        'plugin_error_9' => "El plugin \"%s\" requiere una versión de PHP que es igual a v%s, incapacitante.",
        'plugin_error_10' => "El plugin \"%s\" requiere una versión del plugin \"%s\" que es mayor que v%s, incapacitante.",
        'plugin_error_11' => "El plugin \"%s\" requiere una versión del plugin \"%s\" que es menor que v%s, incapacitante.",
        'plugin_error_12' => "El plugin \"%s\" requiere una versión del plugin \"%s\" que es igual a v%s, incapacitante.",
        'plugin_error_13' => "El plugin \"%s\" dependencia \"%s\" no se encontró, incapacitante.",
        'plugin_error_14' => "El plugin \"%s\" trató de cargar el plugin de \"%s\" primera pero no se ha encontrado.",
        'plugin_error_15' => "Archivo: \"%s\" en la línea:%s trataron de permitir a los plugins. Sólo Core puede permitir a los plugins.",
        'plugin_error_16' => "El plugin \"%s\" requiere una versión del plugin \"%s\" que es superior o igual a v%s, incapacitante.",
        'plugin_error_17' => "El plugin \"%s\" requiere una versión del plugin \"%s\" que es menor o igual a v%s, incapacitante.",
        'plugin_error_18' => "El plugin \"%s\" requiere una versión de PHP que es mayor o igual a v%s, incapacitante.",
        'plugin_error_19' => "El plugin \"%s\" requiere una versión de PHP que es menor o igual a v%s, incapacitante.",
        'plugin_error_20' => "El plugin \"%s\" depende del plugin de \"%s\", pero fue desactivado, no se puede permitir plugin.",
        'custom_regex' => "Regex personalizado para validador ignorado porque no regex fue suministrado. Archivo: \"%s\" en la línea:%s.",
    ]
];