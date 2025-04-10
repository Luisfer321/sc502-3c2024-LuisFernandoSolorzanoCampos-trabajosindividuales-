<?php
//definir un arreglo de productos (inventario)
$productos = [
    ["id" => 1, "nombre" => "Producto1", "cantidad" => 10, "precio" => 20.5],
    ["id" => 2, "nombre" => "Producto2", "cantidad" => 5, "precio" => 15.0],
    ["id" => 3, "nombre" => "Producto3", "cantidad" => 8, "precio" => 7.75],
    ["id" => 4, "nombre" => "Producto4", "cantidad" => 12, "precio" => 9.99],
];

$carrito = [];

function buscarProductoPorId($id)
{
    global $productos;
    $longitud = count($productos);
    for ($i = 0; $i < $longitud; $i++) {
        if ($productos[$i]['id'] === $id) {
            return $productos[$i];
        }
    }
    return null;
}

function agregarAlCarrito($idProducto, $cantidad)
{
    global $carrito, $productos;
    //buscar el producto por id
    $producto = buscarProductoPorId($idProducto);

    //si no es nulo
    if ($producto) {
        // verificar la cantidad si hay suficiente agregar el producto al carrito
        $cantidadProductoDisponible = $producto["cantidad"];
        if ($cantidadProductoDisponible >= $cantidad) {
            //agregue el producto al carrito
            array_push($carrito, [
                "id" => $producto['id'],
                "nombre" => $producto["nombre"],
                "cantidad" => $cantidad,
                "precio" => $producto['precio']
            ]);
            //disminuir el inventario
            
            for($i=0; $i < count($productos); $i++){
            //paso 1, encuentre el producto en la variable global productos
                if($productos[$i]['id']=== $idProducto){
                    //paso 2, edite el dato cantidad restandole la cantidad que el usuario solicito
                    $productos[$i]['cantidad'] -= $cantidad;
                }
            }
            echo "Producto agregado exitosamente";
        } else {
            //si no hay suficientes unidades en el inventario mostrar un mensaje
            echo "No hay suficiente cantidad para el producto " . $producto["nombre"];
        }
    } else {
        echo "Producto no encontrado";
    }
}

function calcularTotalCarrito(){
    //recorrer el carrito de compras para calcular su total
    global $carrito;
    //crear variable para llevar el total
    $total = 0;
    //recorrer el arreglo y dejar en $item el dato que corresponde.
    foreach($carrito as $item){
        //sumar en total el resultado del precio por la cantidad.
        $total += $item['precio'] * $item['cantidad'];
    }
    return $total;
}

//simular una compra
agregarAlCarrito(1,3);// 3 unidades del producto 1
echo '<br>';
agregarAlCarrito(2,2);// 2 unidades del producto 2;
echo '<br>';
agregarAlCarrito(3,5);//5 unidades del producto 3
echo '<br>';
echo "El total del carrito es $" . calcularTotalCarrito();

?>