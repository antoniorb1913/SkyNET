<?php
// Primero necesitas generar el XML con los mismos datos que el listado
require_once(__DIR__ . '/../../conectordb/conectordb.php');

// Obtener filtros desde la URL
$filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$filtro_categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$filtro_marca = isset($_GET['marca']) ? trim($_GET['marca']) : '';

// Consulta SQL similar a list_productos.php para obtener los nombres reales
$consulta = "SELECT pr.nombre as nombre, ca.nombre as categoria, m.nombre as marca 
             FROM productos pr
             LEFT JOIN categorias ca ON pr.categoria_id = ca.id
             LEFT JOIN marcas m ON m.id = pr.marca_id
             WHERE 1=1";

if (!empty($filtro_nombre)) {
    $consulta .= " AND pr.nombre LIKE '%".mysqli_real_escape_string($conexion, $filtro_nombre)."%'";
}
if (!empty($filtro_categoria) && is_numeric($filtro_categoria)) {
    $consulta .= " AND pr.categoria_id = ".(int)$filtro_categoria;
}
if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
    $consulta .= " AND pr.marca_id = ".(int)$filtro_marca;
}

$result = mysqli_query($conexion, $consulta);

// Crear XML
$xml = new DOMDocument('1.0', 'UTF-8');
$productos = $xml->createElement('productos');
$xml->appendChild($productos);

while ($row = mysqli_fetch_assoc($result)) {
    $producto = $xml->createElement('producto');
    
    $nombre = $xml->createElement('nombre', htmlspecialchars($row['nombre']));
    $producto->appendChild($nombre);
    
    $categoria = $xml->createElement('categoria', htmlspecialchars($row['categoria']));
    $producto->appendChild($categoria);
    
    $marca = $xml->createElement('marca', htmlspecialchars($row['marca']));
    $producto->appendChild($marca);
    
    $productos->appendChild($producto);
}

// Ahora puedes hacer la búsqueda XPath
$xpath = new DOMXPath($xml);

$consulta_xpath = "//producto";
$condiciones = [];

if (!empty($filtro_nombre)) {
    $condiciones[] = "contains(nombre, '".htmlspecialchars($filtro_nombre)."')";
}
if (!empty($filtro_categoria) && is_numeric($filtro_categoria)) {
    // Necesitas obtener el nombre de la categoría
    $cat_result = mysqli_query($conexion, "SELECT nombre FROM categorias WHERE id = ".(int)$filtro_categoria);
    if ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $condiciones[] = "categoria = '".htmlspecialchars($cat_row['nombre'])."'";
    }
}
if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
    // Necesitas obtener el nombre de la marca
    $marca_result = mysqli_query($conexion, "SELECT nombre FROM marcas WHERE id = ".(int)$filtro_marca);
    if ($marca_row = mysqli_fetch_assoc($marca_result)) {
        $condiciones[] = "marca = '".htmlspecialchars($marca_row['nombre'])."'";
    }
}

if (!empty($condiciones)) {
    $consulta_xpath .= "[".implode(" and ", $condiciones)."]";
}

$resultados = $xpath->query($consulta_xpath);

// Mostrar resultados
echo "<h2>Resultados de búsqueda con XPath</h2>";
if ($resultados->length > 0) {
    foreach ($resultados as $producto) {
        foreach ($producto->childNodes as $dato) {
            if ($dato->nodeType == XML_ELEMENT_NODE) {
                echo "<p><strong>".ucfirst($dato->nodeName).":</strong> ".$dato->nodeValue."</p>";
            }
        }
        echo "<hr>";
    }
} else {
    echo "<p>No se encontraron productos con los filtros aplicados.</p>";
}
?>