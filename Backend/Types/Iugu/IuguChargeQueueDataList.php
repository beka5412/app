<?php
declare(strict_types=1);

namespace Backend\Types\Iugu;

class IuguChargeQueueDataList
{
    /** @var array<IuguChargeQueueData> $data */
    public array $data;

    public int|string $order_id;

    /**
     * @param array<IuguChargeQueueData> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->order_id = ((array) $data)['meta']['order_id'] ?? '';
    }
}
