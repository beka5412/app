<?php 
declare(strict_types=1);

namespace Backend\Entities\Abstracts;

use Backend\Models\Balance;
use Backend\Models\BalanceHistory;
use Backend\Models\SellerCreditQueue as ModelsSellerCreditQueue;
use Backend\Types\SellerCredit\ESellerCreditQueueStatus;
use Backend\Types\SellerCredit\ESellerCredityBodyType;
use Backend\Types\SellerCredit\SellerCreditBody;
use Backend\Types\SellerCredit\SellerCreditBodyWhere;
use Backend\Types\SellerCredit\SellerCreditType;
use Backend\Types\SellerCredit\SellerQueueUpdateData;
use Backend\Types\SellerCredit\SellerQueueUpdateWhere;
use Illuminate\Database\Eloquent\Collection;

class SellerCreditQueue
{
    public static function push(string $data, string $date = ''): ModelsSellerCreditQueue
    {
        $entity = new ModelsSellerCreditQueue;
        $entity->data = $data;
        $entity->status = ESellerCreditQueueStatus::WAITING;
        $entity->scheduled_at = $date ? $date : today();
        $entity->save();
        return $entity;
    }

    public static function removeWhereData(SellerCreditBodyWhere $where, ?ESellerCreditQueueStatus $status = null): void
    {
        $where_array = (array) $where;
        if (!count($where_array)) return;


        $model = ModelsSellerCreditQueue::select();

        foreach ($where_array as $key => $value)
            if ($where->$key) $model = $model->whereJsonContains("data->$key", $value);

        if (!is_null($status)) $model = $model->where('status', $status);

        $model->delete();
    }

    public static function updateWhere(SellerQueueUpdateWhere $where, SellerQueueUpdateData $data): void
    {
        $where_array = (array) $where;
        if (!count($where_array)) return;

        $model = ModelsSellerCreditQueue::select();

        foreach ($where_array as $key => $value)
        {
            if (!in_array($key, ['data'])) $model = $model->where($key, $value);

            if ($key === 'data')
                foreach ((array) $where->data as $key2 => $value2)
                    if ($where->data->$key2) $model = $model->whereJsonContains("data->$key2", $value2);

        }

        $model->update((array) $data);
    }

    /**
     * @return Collection|SellerCreditQueue[]|null
     */
    public static function getWhere(SellerQueueUpdateWhere $where): ?Collection
    {
        $where_array = (array) $where;  
        if (!count($where_array)) return null;

        $model = ModelsSellerCreditQueue::select();

        foreach ($where_array as $key => $value)
        {
            if (!in_array($key, ['data'])) $model = $model->where($key, $value);

            if ($key === 'data')
                foreach ((array) $where->data as $key2 => $value2)
                    if ($where->data->$key2) $model = $model->whereJsonContains("data->$key2", $value2);
        }

        return $model->get();
    }

    public static function run(ModelsSellerCreditQueue $entity): ModelsSellerCreditQueue
    {
        return self::send([
            "data" => $entity->data,
            "entity" => $entity
        ]);
    }

    public static function send(SellerCreditType|array $object): ModelsSellerCreditQueue
    {
        $body = new SellerCreditBody(json_decode($object instanceof SellerCreditType ? $object->data : $object['data']));
        $entity = $object instanceof SellerCreditType ? $object->entity : $object['entity'];

        $user_id = $body->user_id;
        $amount = $body->amount;
        $type = $body->type ?? '';

        $entity->status = ESellerCreditQueueStatus::SENT;
        $entity->save();

        $balance = Balance::where('user_id', $user_id)->first();
        $balance->future_releases -= $amount;
        $balance->available += $amount;

        $balance_history = new BalanceHistory;
        $balance_history->user_id = $user_id;
        $balance_history->operation = 'D';
        $balance_history->type = 'future_release';
        $balance_history->amount = $amount;
        $balance_history->save();

        if ($type === ESellerCredityBodyType::RESERVED_AS_GUARANTEE)
        {
            $balance->reserved_as_guarantee -= $amount;

            $balance_history = new BalanceHistory;
            $balance_history->user_id = $user_id;
            $balance_history->operation = 'D';
            $balance_history->type = 'reserved_as_guarantee';
            $balance_history->amount = $amount;
            $balance_history->save();
        }

        $balance_history = new BalanceHistory;
        $balance_history->user_id = $user_id;
        $balance_history->operation = 'C';
        $balance_history->type = 'available';
        $balance_history->amount = $amount;
        $balance_history->save();

        $balance->save();

        return $entity;
    }
}
