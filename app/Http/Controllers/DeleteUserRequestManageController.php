<?php

namespace App\Http\Controllers;


use App\Models\DeleteAccountRequest;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeleteUserRequestManageController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('RoutePermissionCheck:admin.user_delete_request.index', ['only' => ['index', 'datatable']]);
        $this->middleware('RoutePermissionCheck:admin.user_delete_request.destroy', ['only' => ['destroy']]);
        $this->middleware('RoutePermissionCheck:admin.user_delete_request.reject', ['only' => ['reject']]);

    }

    public function index()
    {
        try {
            return view('backend.delete_request.index');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function datatable(Request $request)
    {

        try {
            $query = DeleteAccountRequest::query();
            $query->with(['user'])->select('delete_account_requests.*');
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->addColumn('name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('action', function ($row) {
                    return view('backend.delete_request.components._action', ['row' => $row]);
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 503);
        }
    }

    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'id' => 'required'
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $success = trans('lang.Deleted') . ' ' . trans('lang.Successfully');
            $row = DeleteAccountRequest::where('id',$request->id)->first();
            if($row){
                User::where('id',$row->user_id)->delete();
                $row->delete();
            }
            Toastr::success($success, 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function reject(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'id' => 'required'
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $success = trans('common.Reject') . ' ' . trans('lang.Successfully');
            $row = DeleteAccountRequest::where('id',$request->id)->first();
            if($row){
                User::where('id',$row->user_id)->update(['status'=>1]);
                $row->delete();
            }
            Toastr::success($success, 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }
}
