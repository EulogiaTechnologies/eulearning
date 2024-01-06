<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\Cache;
use Modules\Setting\Model\Currency;

use Modules\Setting\Repositories\CurrencyRepositoryInterface;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function all()
    {
        return Currency::orderBy('name', 'asc')->get();
    }

    public function create(array $data)
    {
        $currency = new Currency();
        $currency->fill($data)->save();
        Cache::forget('currencyList_' . SaasDomain());
    }

    public function find($id)
    {
        return Currency::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        Cache::forget('currencyList_' . SaasDomain());
        return Currency::findOrFail($id)->update($data);

    }

    public function delete($id)
    {
        Cache::forget('currencyList_' . SaasDomain());
        return Currency::findOrFail($id)->delete();
    }
}
