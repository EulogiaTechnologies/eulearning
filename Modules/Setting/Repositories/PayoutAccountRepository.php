<?php

namespace Modules\Setting\Repositories;

use App\Traits\ImageStore;
use Modules\Setting\Entities\PayoutAccount;
use Modules\Setting\Entities\PayoutAccountSpecification;

class PayoutAccountRepository
{
    use ImageStore;

    public function all()
    {
        return PayoutAccount::with(['specifications'])->withCount(['specifications'])->orderBy('title', 'asc')->get();
    }

    public function getActiveAll()
    {
        return PayoutAccount::with(['specifications'])->withCount(['specifications'])->where('status', 1)->orderBy('title', 'asc')->get();
    }

    public function create(array $data)
    {

        if (isset($data['logo']) && $data['logo']) {
            $logo_url = $this->saveImage($data['logo']);
        } else {
            $logo_url = null;
        }

        $payout_account = PayoutAccount::create([
            'title' => $data['title'],
            'logo' => $logo_url,
        ]);

        if (isset($data['specifications'])) {
            foreach ($data['specifications'] as $specification) {
                if ($specification['specification']) {
                    PayoutAccountSpecification::create([
                        'payout_accounts_id' => $payout_account->id,
                        'title' => $specification['specification'],
                    ]);
                }
            }
        }

        return $payout_account;

    }

    public function find($id)
    {
        return PayoutAccount::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $row = $this->find($id);
        if (isset($data['logo']) && $data['logo']) {
            $this->deleteImage($row->logo);
            $logo_url = $this->saveImage($data['logo']);
        } else {
            $logo_url = $row->logo;
        }

        $row->update([
            'title' => $data['title'],
            'logo' => $logo_url,
        ]);
        PayoutAccountSpecification::where('payout_accounts_id', $id)->delete();
        if (isset($data['specifications'])) {
            foreach ($data['specifications'] as $specification) {
                if ($specification['specification']) {
                    PayoutAccountSpecification::create([
                        'payout_accounts_id' => $id,
                        'title' => $specification['specification'],
                    ]);
                }

            }
        }

        return true;
    }

    public function delete($id)
    {
        $row = $this->find($id);
        if ($row->logo) {
            $this->deleteImage($row->logo);
        }
        return $row->delete();
    }
}
