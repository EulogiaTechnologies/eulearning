<?php

namespace Modules\Setting\Http\Controllers;

use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\CookieSetting;
use Modules\Setting\Model\GeneralSetting;

class CookieSettingController extends Controller
{
    use ImageStore;

    public function index()
    {
        $setting = CookieSetting::getData();
        return view('setting::cookie_setting', compact('setting'));
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $cookie = CookieSetting::first();
            if ($cookie) {
                $cookie->allow = $request->allow;
                $cookie->btn_text = $request->btn_text;
                $cookie->bg_color = $request->bg_color;
                $cookie->text_color = $request->text_color;
                $cookie->details = $request->details;
                $cookie->gdpr_details = $request->gdpr_details;
                $cookie->customize_btn_text = $request->customize_btn_text;
                $cookie->gdpr_strictly = $request->gdpr_strictly;
                $cookie->gdpr_performance = $request->gdpr_performance;
                $cookie->gdpr_functional = $request->gdpr_functional;
                $cookie->gdpr_targeting = $request->gdpr_targeting;
                $cookie->gdpr_confirm_choice_btn_text = $request->gdpr_confirm_choice_btn_text;
                $cookie->gdpr_accept_all_btn_text = $request->gdpr_accept_all_btn_text;

                if ($request->image != null) {
                    $cookie->image = $this->saveImage($request->image);
                }
                $cookie->save();

                UpdateGeneralSetting('cookie_status', $request->allow ?? 0);
            }
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }


    }

}
