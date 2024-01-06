<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendGeneralEmail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CourseSetting\Entities\CourseCanceled;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Cashback\Entities\UserCashback;
use Modules\Cashback\Entities\UserCashbackDetail;


class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('RoutePermissionCheck:refund.settings.create', ['only' => ['settings', 'settingsUpdate']]);
        $this->middleware('RoutePermissionCheck:refund.approved', ['only' => ['approved']]);
        $this->middleware('RoutePermissionCheck:refund.reject', ['only' => ['reject']]);
    }

    public function settings()
    {
        try {
            return view('backend.refund.settings');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function settingsUpdate(Request $request)
    {
        try {
            UpdateGeneralSetting('enable_refund_request', isset($request->enable_refund_request) ? 1 : 0);
            UpdateGeneralSetting('allow_refund_days', $request->allow_refund_days ?? 0);
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function approved($id)
    {
        try {
            DB::beginTransaction();
            $enroll = CourseCanceled::find($id);
            // dd($enroll);
            $course_enroll = CourseEnrolled::where('id', $enroll->enroll_id)->first();
            $student = $enroll->user;

            $refundable_amount = 0;
            $note = '';
            if (isModuleActive('Cashback')) {
                $product_type = '';

                switch ($enroll->course->type) {
                    case '1':
                        $product_type = 'course';
                        break;
                    case '2':
                        $product_type = 'quiz';
                        break;
                    case '3':
                        $product_type = 'live_class';
                        break;
                    default:

                        break;
                }

                if (isModuleActive('Subscription')) {
                    if ($course_enroll->subscription == 1) {
                        $product_type = 'subscription';
                    }
                }

                $user_cashback_details = UserCashbackDetail::whereHas('user_cashback', function ($q) use ($enroll) {
                    $q->where('user_id', $enroll->user_id);
                })
                    ->where('product_id', $enroll->course_id)
                    ->where('product_type', $product_type)
                    ->where('status', 'paid')
                    ->first();
                if (empty($user_cashback_details) && isModuleActive('Gift')) {
                    $user_cashback_details = UserCashbackDetail::where('product_id', $enroll->course_id)
                        ->where('product_type', $product_type)
                        ->where('status', 'paid')
                        ->where('is_gift', 1)
                        ->where('gifted_to', $enroll->user_id)
                        ->first();
                }
                if ($user_cashback_details) {
                    $refundable_amount = ($enroll->purchase_price - $user_cashback_details->cashback_amount);

                    $user_cashback_details->status = 'refunded';
                    $user_cashback_details->refunded_datetime = date('Y-m-d H:i:s');
                    $user_cashback_details->refunded_description = $enroll->reason;


                    $user_cashback_details->save();

                    $note = ' [NB: Cashback amount ' . $user_cashback_details->cashback_amount . ' has been deducted from the refundable amount.]';
                } else {
                    $refundable_amount = $enroll->purchase_price;
                }
            } else {
                $refundable_amount = $enroll->purchase_price;
            }
            // dd($user_cashback_details);
            $course_enroll->delete();
            $student->balance = $student->balance + $refundable_amount;
            $student->save();
            $act = 'Enroll_Refund';
            $enroll->status = 1;
            $enroll->approved_date = date('Y-m-d');
            $enroll->cancel_by = Auth::id();
            $enroll->save();
            DB::commit();
            if (UserEmailNotificationSetup($act, $enroll->user)) {
                if ($enroll->user) {
                    $reason = $enroll->reason ?? '';
                    $reason .= $note;
                    SendGeneralEmail::dispatch($enroll->user, $act, [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $reason
                    ]);
                }
            }

            if (UserBrowserNotificationSetup($act, $enroll->user)) {
                send_browser_notification(
                    $enroll->user,
                    $type = $act,
                    $shortcodes = [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $enroll->reason
                    ],
                    trans('common.View'), //actionText
                    courseDetailsUrl(@$enroll->course->id, @$enroll->course->type, @$enroll->course->slug), //actionUrl
                );
            }


            if (UserMobileNotificationSetup($act, $enroll->user) && !empty($enroll->user->device_token)) {
                send_mobile_notification($enroll->user, $act, [
                    'course' => $enroll->course->title,
                    'time' => now(),
                    'reason' => $enroll->reason
                ]);
            }


            if (UserEmailNotificationSetup($act, $enroll->course->user)) {
                if ($enroll->course->user) {
                    SendGeneralEmail::dispatch($enroll->course->user, $act, [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $enroll->reason
                    ]);
                }
            }

            if (UserBrowserNotificationSetup($act, $enroll->course->user)) {
                send_browser_notification(
                    $enroll->course->user,
                    $type = $act,
                    $shortcodes = [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $enroll->reason
                    ],
                    trans('common.View'), //actionText
                    courseDetailsUrl(@$enroll->course->id, @$enroll->course->type, @$enroll->course->slug), //actionUrl
                );
            }


            if (UserMobileNotificationSetup($act, $enroll->course->user) && !empty($enroll->course->user->device_token)) {
                send_mobile_notification($enroll->course->user, $act, [
                    'course' => $enroll->course->title,
                    'time' => now(),
                    'reason' => $enroll->reason
                ]);
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function reject(Request $request)
    {
        $request->validate([
            'reason' => 'required',
            'id' => 'required',
        ]);
        try {
            $msg = trans('common.Operation successful');

            DB::beginTransaction();
            $enroll = CourseCanceled::find($request->id);
            $act = 'REFUND_REJECT';
            $enroll->status = 2;
            $enroll->approved_date = date('Y-m-d');
            $enroll->cancel_reason = $request->reason;
            $enroll->cancel_by = Auth::id();
            $enroll->save();
            DB::commit();
            if (UserEmailNotificationSetup($act, $enroll->user)) {
                if ($enroll->user) {
                    SendGeneralEmail::dispatch($enroll->user, $act, [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $request->reason,
                        'name' => $enroll->user->name
                    ]);
                }
            }

            if (UserBrowserNotificationSetup($act, $enroll->user)) {
                send_browser_notification(
                    $enroll->user,
                    $type = $act,
                    $shortcodes = [
                        'course' => $enroll->course->title,
                        'time' => now(),
                        'reason' => $request->reason,
                        'name' => $enroll->user->name
                    ],
                    trans('common.View'), //actionText
                    courseDetailsUrl(@$enroll->course->id, @$enroll->course->type, @$enroll->course->slug), //actionUrl
                );
            }

            if (UserMobileNotificationSetup($act, $enroll->user) && !empty($enroll->user->device_token)) {
                send_mobile_notification($enroll->user, $act, [
                    'course' => $enroll->course->title,
                    'time' => now(),
                    'reason' => $request->reason,
                    'name' => $enroll->user->name
                ]);
            }
            return response()->json(['msg' => $msg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


}
