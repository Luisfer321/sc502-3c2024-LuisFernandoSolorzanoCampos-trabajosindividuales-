let productos = [];

function agregarProducto() {
    let nombre = document.getElementById('nombre').value;
    let precio = document.getElementById('precio').value;
    let categoria = document.getElementById('categoria').value;

    if (nombre && precio && categoria) {
        productos.push({ nombre, precio: parseFloat(precio), categoria });
        mostrarProductos();
        document.getElementById('productoForm').reset();
    } else {
        alert("Por favor, complete todos los campos.");
    }
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    mostrarProductos();
}

function filtrarPorCategoria() {
    let categoria = document.getElementById('filtroCategoria').value;
    mostrarProductos(categoria);
}

function mostrarProductos(categoria = 'todos') {
    let lista = document.getElementById('productosList');
    lista.innerHTML = '';

    let productosFiltrados = categoria === 'todos' ? productos : productos.filter(producto => producto.categoria === categoria);

    productosFiltrados.forEach((producto, index) => {
        let li = document.createElement('li');
        li.innerHTML = `${producto.nombre} - ₡${producto.precio} (${producto.categoria}) <button onclick="eliminarProducto(${index})">Eliminar</button>`;
        lista.appendChild(li);
    });
}