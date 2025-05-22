<?php

namespace Backend\Enums\PagarMe;

abstract class EPagarMeOrderStatus
{
    const CANCELED = 'canceled';
    const CLOSED = 'closed';
    const CREATED = 'created';
    const PAID = 'paid';
    const PAYMENT_FAILED = 'payment_failed';
    const UPDATED = 'updated';
}

// canceled Essa notificação é disparada quando um pedido é cancelado.
// closed Essa notificação é disparada quando um pedido é fechado.
// created Essa notificação é disparada quando um pedido é criado.
// paid Essa notificação é disparada quando um pedido é pago.
// payment_failed Essa notificação é disparada quando houve uma falha no pagamento do pedido.
// updated Essa notificação é disparada quando um pedido foi atualizado.