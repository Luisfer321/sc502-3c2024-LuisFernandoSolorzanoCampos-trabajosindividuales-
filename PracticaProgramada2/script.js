function calcularDeducciones() {
    let salarioBruto = parseFloat(document.getElementById("salario").value);
    
    if (isNaN(salarioBruto) || salarioBruto <= 0) {
        alert("Por favor, ingrese un salario válido.");
        return;
    }

    // Cálculo de cargas sociales (CCSS e INA)
    let cargasSociales = salarioBruto * 0.1049; // 10.49%

    // Cálculo del impuesto sobre la renta según tramos en Costa Rica
    let impuestoRenta = 0;
    if (salarioBruto > 941000) {
        impuestoRenta = (salarioBruto - 941000) * 0.15;
    }
    if (salarioBruto > 1383000) {
        impuestoRenta = ((salarioBruto - 1383000) * 0.20) + ((1383000 - 941000) * 0.15);
    }
    if (salarioBruto > 2422000) {
        impuestoRenta = ((salarioBruto - 2422000) * 0.25) + ((2422000 - 1383000) * 0.20) + ((1383000 - 941000) * 0.15);
    }

    let salarioNeto = salarioBruto - cargasSociales - impuestoRenta;

    // Mostrar resultados en la página
    document.getElementById("cargas-sociales").innerText = `₡${cargasSociales.toFixed(2)}`;
    document.getElementById("impuesto-renta").innerText = `₡${impuestoRenta.toFixed(2)}`;
    document.getElementById("salario-neto").innerText = `₡${salarioNeto.toFixed(2)}`;
}