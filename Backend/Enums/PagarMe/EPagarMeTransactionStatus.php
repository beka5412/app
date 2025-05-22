<?php

namespace Backend\Enums\PagarMe;

abstract class EPagarMeTransactionStatus
{
    const CANCELED = 'canceled';
    const CLOSED = 'closed';
    const CREATED = 'created';
    const PAID = 'paid';
    const PAYMENT_FAILED = 'payment_failed';
    const UPDATED = 'updated';
}


// authorized_pending_capture	Autorizada pendente de captura
// not_authorized	Não autorizada
// captured	Capturada
// partial_capture	Capturada parcialmente
// waiting_capture	Aguardando captura
// refunded	Estornada
// voided	Cancelada
// partial_refunded	Estornada parcialmente
// partial_void	Cancelada parcialmente
// error_on_voiding	Erro no cancelamento
// error_on_refunding	Erro no estorno
// waiting_cancellation	Aguardando cancelamento
// with_error	Com erro
// failed	Falha