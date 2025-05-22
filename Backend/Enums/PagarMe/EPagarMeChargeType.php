<?php

namespace Backend\Enums\PagarMe;

abstract class EPagarMeChargeType
{
    const ANTIFRAUD_APPROVED = 'charge.antifraud_approved';
    const ANTIFRAUD_MANUAL = 'charge.antifraud_manual';
    const ANTIFRAUD_PENDING = 'charge.antifraud_pending';
    const ANTIFRAUD_REPROVED = 'charge.antifraud_reproved';
    const CHARGEDBACK = 'charge.chargedback';
    const CREATED = 'charge.created';
    const OVERPAID = 'charge.overpaid';
    const PAID = 'charge.paid';
    const PARTIAL_CANCELED = 'charge.partial_canceled';
    const PAYMENT_FAILED = 'charge.payment_failed';
    const FAILED = 'charge.failed';
    const PENDING = 'charge.pending';
    const PROCESSING = 'charge.processing';
    const REFUNDED = 'charge.refunded';
    const UNDERPAID = 'charge.underpaid';
    const UPDATED = 'charge.updated';
};

// charge.antifraud_approvedEssa notificação é disparada quando uma cobrança é aprovada no antifraude.
// charge.antifraud_manual Essa notificação é disparada quando uma cobrança é direcionada para análise manual.
// charge.antifraud_pending Essa notificação é disparada quando uma cobrança está pendente no antifraude.
// charge.antifraud_reproved Essa notificação é disparada quando uma cobrança é reprovada no antifraude.
// charge.chargedback chargechargedback_ desc
// charge.created Essa notificação é disparada quando uma cobrança é criada.
// charge.overpaid Essa notificação é disparada quando uma cobrança foi paga a maior.
// charge.paid Essa notificação é disparada quando uma cobrança é paga.
// charge.partial_canceled Essa notificação é disparada quando uma cobrança foi cancelada parcialmente.
// charge.payment_failed Essa notificação é disparada quando houve uma falha no pagamento da cobrança.
// charge.pending Essa notificação é disparada quando uma cobrança está pendente.
// charge.processing Essa notificação é disparada quando uma cobrança está em processamento.
// charge.refunded Essa notificação é disparada quando uma cobrança é estornada.
// charge.underpaid Essa notificação é disparada quando uma cobrança foi paga a menor.
// charge.updated Essa notificação é disparada quando uma cobrança foi atualizada.