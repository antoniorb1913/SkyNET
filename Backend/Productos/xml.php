<?php
    require_once(__DIR__ . '/../../conectordb/conectordb.php');

    $consulta_productos = "SELECT pr.id as id, pr.referencia as referencia, pr.nombre as nombre, pr.descripcion as descripcion, pr.precio as precio, 
    pr.stock as stock, pr.alto as alto, pr.ancho as ancho, pr.largo as largo, pr.peso as peso, 
    ca.nombre as categoria, m.nombre as marca, pr.updated_at as fecha_modificacion
    FROM productos pr
    LEFT JOIN categorias ca ON pr.categoria_id = ca.id
    LEFT JOIN marcas m ON m.id = pr.marca_id";

    $filtro_nombre='';
    $filtro_categoria='';
    $filtro_marca='';

    if(isset($_REQUEST) && count($_REQUEST)>0) {
        $condiciones = " where true";
        $filtro_nombre = $_REQUEST['nombre'];
        $filtro_categoria = $_REQUEST['categoria'];
        $filtro_marca = $_REQUEST['marca'];

    if ($filtro_nombre){
        $condiciones .= " AND pr.nombre LIKE '%".$filtro_nombre."%'";
    }

    if($filtro_categoria > 0){
        $condiciones .= " AND pr.categoria_id = ".$filtro_categoria;
    }
    if($filtro_marca > 0) {
        $condiciones .= " AND pr.marca_id = ".$filtro_marca;

    } 

    $consulta_productos .= $condiciones;

    }
    $consulta_productos .= " ORDER BY ca.nombre ASC";
    $listado_productos = mysqli_query( $conexion, $consulta_productos);
    $consulta_categoria= "SELECT * FROM categorias";
    $categorias = mysqli_query( $conexion, $consulta_categoria);
    $consulta_marca= "SELECT * FROM marcas";
    $marcas = mysqli_query( $conexion, $consulta_marca);  

    if (mysqli_num_rows($listado_productos) > 0) {
        $xml = new XMLWriter();
        $xml->openUri('listado_productos.xml');
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('producto');

    while ($fila = mysqli_fetch_array($listado_productos)) {
        $xml->startElement('producto');
        $xml->writeElement('referencia', $fila['referencia']);
        $xml->writeElement('nombre', $fila['nombre']);
        $xml->writeElement('descripcion', $fila['descripcion']);
        $xml->writeElement('precio', $fila['precio']);
        $xml->writeElement('stock', $fila['stock']);
        $xml->writeElement('alto', $fila['alto']);
        $xml->writeElement('ancho', $fila['ancho']);
        $xml->writeElement('largo', $fila['largo']);
        $xml->writeElement('peso', $fila['peso']);
        $xml->writeElement('categoria', $fila['categoria']);
        $xml->writeElement('marca', $fila['marca']);
        $xml->writeElement('fecha_modificacion', $fila['fecha_modificacion']);
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endDocument();

    echo "Archivo XML generado correctamente.";
    echo "<script>
        if (confirm('¿Quieres mostrar el archivo XML?')) {
            window.location.href = 'listado_productos.xml';
        } else {
            window.location.href = 'list_productos.php';
        }
    </script>";
} else {
    echo "No se encontraron resultados con los filtros aplicados.";
}





