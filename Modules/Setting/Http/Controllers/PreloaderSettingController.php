<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PreloaderSettingController extends Controller
{
    use ImageStore;

    public function index()
    {
        return view('setting::preloader.index');
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'preloader_image' => 'nullable|image|mimes:jpeg,png,gif,jpg',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        UpdateGeneralSetting('preloader_status', $request->preloader_status);
        UpdateGeneralSetting('preloader_style', $request->preloader_style);
        UpdateGeneralSetting('preloader_type', $request->preloader_type);

        if ($request->hasFile('preloader_image')) {
            UpdateGeneralSetting('preloader_image', $this->saveImage($request->preloader_image));
        }

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }

}
