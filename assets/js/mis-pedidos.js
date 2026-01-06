// Gestión de Pedidos para la página Mis Pedidos

document.addEventListener('DOMContentLoaded', function () {
    const editDeliveryModal = new bootstrap.Modal(document.getElementById('editDeliveryModal'));

    // Funcionalidad de volver a pedir
    document.querySelectorAll('.reorder-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const orderId = this.dataset.orderId;

            if (confirm('¿Añadir los productos de este pedido al carrito?')) {
                try {
                    const response = await fetch('controllers/order_actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'reorder',
                            order_id: orderId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('✅ Productos añadidos al carrito');
                        window.location.href = 'index.php?view=carrito';
                    } else {
                        alert('❌ Error: ' + (result.error || 'No se pudo procesar el pedido'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error al procesar la solicitud');
                }
            }
        });
    });

    // Funcionalidad de editar entrega
    document.querySelectorAll('.edit-delivery-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const orderId = this.dataset.orderId;
            const address = this.dataset.address;
            const phone = this.dataset.phone;
            const notes = this.dataset.notes;

            // Relleno el formulario
            document.getElementById('edit_order_id').value = orderId;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_notes').value = notes;

            editDeliveryModal.show();
        });
    });

    // Guardo los cambios de entrega
    document.getElementById('saveDeliveryBtn').addEventListener('click', async function () {
        const formData = new FormData(document.getElementById('editDeliveryForm'));
        const data = {
            action: 'update_delivery',
            order_id: formData.get('order_id'),
            delivery_address: formData.get('delivery_address'),
            delivery_phone: formData.get('delivery_phone'),
            delivery_notes: formData.get('delivery_notes')
        };

        try {
            const response = await fetch('controllers/order_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('✅ Información de entrega actualizada');
                editDeliveryModal.hide();
                location.reload();
            } else {
                alert('❌ Error: ' + (result.error || 'No se pudo actualizar'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error al procesar la solicitud');
        }
    });

    // Funcionalidad de cancelar pedido
    document.querySelectorAll('.cancel-order-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const orderId = this.dataset.orderId;

            if (confirm('⚠️ ¿Estás seguro de que quieres cancelar este pedido?')) {
                try {
                    const response = await fetch('controllers/order_actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'cancel_order',
                            order_id: orderId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('✅ Pedido cancelado');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + (result.error || 'No se pudo cancelar el pedido'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error al procesar la solicitud');
                }
            }
        });
    });
});
