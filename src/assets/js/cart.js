/**
 * Manejo del carrito de compras
 * Se encarga de enviar los productos al controlador PHP
 */
const Cart = {
    add: function (product) {
        // Creo un formulario dinámico para enviar los datos por POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'controllers/cart_controller.php';

        // Defino los campos necesarios para el controlador
        const fields = {
            action: 'add',
            product_id: product.id,
            name: product.name,
            price: product.price
        };

        // Añado los campos al formulario
        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        // Añado el formulario al body y lo envío
        document.body.appendChild(form);
        form.submit();
    }
};
