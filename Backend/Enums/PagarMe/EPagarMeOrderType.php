<?php

namespace Backend\Enums\PagarMe;

abstract class EPagarMeOrderType
{
    const CANCELED = 'order.canceled';
    const CLOSED = 'order.closed';
    const CREATED = 'order.created';
    const PAID = 'order.paid';
    const PAYMENT_FAILED = 'order.payment_failed';
    const UPDATED = 'order.updated';
}

// canceled Essa notificação é disparada quando um pedido é cancelado.
// closed Essa notificação é disparada quando um pedido é fechado.
// created Essa notificação é disparada quando um pedido é criado.
// paid Essa notificação é disparada quando um pedido é pago.
// payment_failed Essa notificação é disparada quando houve uma falha no pagamento do pedido.
// updated Essa notificação é disparada quando um pedido foi atualizado.