<?php

namespace App\Http\Controllers;

use App\DepositRecord;
use App\Repositories\MyEnrollmentRepository;
use App\User;
use App\UserLogin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Certificate\Entities\CertificateRecord;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\CourseCanceled;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Payment\Entities\Checkout;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;
use Yajra\DataTables\Facades\DataTables;
use Modules\OrgInstructorPolicy\Entities\OrgPolicyCategory;

class MyPanelController extends Controller
{
    public $myEnrollmentRepo;

    public function __construct(MyEnrollmentRepository $myEnrollmentRepo)
    {
        $this->myEnrollmentRepo = $myEnrollmentRepo;
        $this->middleware('RoutePermissionCheck:users.my_refund.index', ['only' => ['myRefund', 'myRefundeDatatable']]);
        $this->middleware('RoutePermissionCheck:users.my_purchase.index', ['only' => ['myPurchase', 'myPurchaseDatatable']]);
        $this->middleware('RoutePermissionCheck:users.my_referral.index', ['only' => ['myReferral', 'myReferralDatatable']]);
        $this->middleware('RoutePermissionCheck:users.logged_in_devices.index', ['only' => ['loggedInDevices', 'loggedInDevicesDatatable']]);
        $this->middleware('RoutePermissionCheck:users.my_certificates.index', ['only' => ['myCertificates', 'myCertificatesDatatable']]);
        $this->middleware('RoutePermissionCheck:users.deposit.index', ['only' => ['deposit', 'depositDatatable']]);
        $this->middleware('RoutePermissionCheck:users.my_topics.index', ['only' => ['myTopics', 'myTopicsDatatable']]);

    }


    public function myRefund()
    {
        try {
            $flag = Settings('allow_refund_days') == 0 ? false : true;


            $ignore = CourseCanceled::where('user_id', auth()->id())
                ->where('status', 0)
                ->whereNotNull('enroll_id')->pluck('enroll_id')->toArray();
            $data['courses'] = CourseEnrolled::where('user_id', auth()->id())
                ->where('purchase_price', ">", 0)
                ->whereNotIn('id', $ignore)
                ->when($flag, function ($query) {
                    $today = Carbon::now();
                    $date = $today->subDays((int)Settings('allow_refund_days'))->format('Y-m-d');
                    return $query->where(DB::raw('DATE(created_at)'), '>=', $date);
                })
                ->with('course')
                ->get();

            return view('backend.my_panel.my_refund.index', $data);
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function myRefundeDatatable(Request $request)
    {
        try {
            $data = CourseCanceled::query()
                ->where('user_id', auth()->id())
                ->with('course');

            if ($request->f_date) {
                $data->whereBetween(DB::raw('DATE(created_at)'), formatDateRangeData($request->f_date));
            }


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->addColumn('course', function ($row) {
                    return $row->course->title;
                })
                ->editColumn('purchase_price', function ($row) {
                    return getPriceFormat($row->purchase_price);
                })
                ->editColumn('type', function ($row) {
                    return $row->refund == 1 ? 'Refund' : 'Cancel';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return 'Approved';
                    } elseif ($row->status == 0) {
                        return 'Pending';
                    } else {
                        return 'Reject';
                    }
                })
                ->addColumn('action', function ($row) {
                    return view('backend.my_panel.my_refund.components._action', ['row' => $row]);
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function myPurchase()
    {
        try {
            return view('backend.my_panel.my_purchase.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function myPurchaseDatatable(Request $request)
    {
        try {
            $data = Checkout::query()
                ->where('user_id', Auth::id())
                ->where('status', 1)
                ->with('coupon', 'courses');

            if ($request->f_date) {
                $data->whereBetween(DB::raw('DATE(updated_at)'), formatDateRangeData($request->f_date));

            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('updated_at', function ($row) {
                    return showDate($row->updated_at);
                })
                ->addColumn('total_courses', function ($row) {
                    if (count($row->courses) == 0) {
                        return 1;
                    } else {
                        return count($row->courses);
                    }
                })
                ->editColumn('purchase_price', function ($row) {
                    return getPriceFormat($row->purchase_price);
                })
                ->editColumn('discount', function ($row) {
                    return $row->discount != 0 ? getPriceFormat($row->discount) : '';
                })
                ->addColumn('tax', function ($row) {
                    if (hasTax() && $row->tax) {
                        return getPriceFormat($row->tax);
                    }
                })
                ->addColumn('invoice', function ($row) {
                    return view('backend.my_panel.my_purchase.components._invoice', ['row' => $row]);
                })
                ->rawColumns(['invoice'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function myReferral()
    {
        try {
            if (!Auth::user()->referral) {
                User::where('id', Auth::id())->update(['referral' => generateUniqueId()]);
            }

            return view('backend.my_panel.my_referral.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function myReferralDatatable(Request $request)
    {
        try {
            $data = UserWiseCoupon::query()->with(['invite_accept_byF'])->where('invite_by', Auth::user()->id)
                ->where('course_id', '!=', null);

            if ($request->f_date) {
                $data->whereHas('invite_accept_byF', function ($q) use ($request) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), formatDateRangeData($request->f_date));
                });

            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('bonus_amount', function ($row) {
                    return showPrice($row->bonus_amount);
                })
                ->addColumn('date', function ($row) {
                    return showDate($row->invite_accept_byF->created_at);
                })
                ->addColumn('user', function ($row) {
                    return view('backend.my_panel._user_td', ['row' => $row->invite_accept_byF]);
                })
                ->rawColumns(['user'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function myCertificates()
    {
        try {
            return view('backend.my_panel.my_certificates.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function myCertificatesDatatable(Request $request)
    {
        try {
            $data = CertificateRecord::query()->with(['course', 'student'])->when(isModuleActive('CPD'), function ($q) {
                $q->whereNotNull('course_id');
            })->where('student_id', Auth::user()->id);


            if ($request->f_date) {
                $data->whereBetween(DB::raw('DATE(created_at)'), formatDateRangeData($request->f_date));
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('course', function ($row) {
                    $course = $row->course;
                    return "<a href=" . courseDetailsUrl($course->id, $course->type, $course->slug) . " > $course->title </a>";
                })
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->addColumn('my_class', function ($row) {
                    return view('backend.my_panel.my_certificates.components._my_class', ['certificate' => $row]);
                })
                ->addColumn('invoice', function ($row) {
                    return view('backend.my_panel.my_certificates.components._invoice', ['certificate' => $row]);
                })
                ->addColumn('action', function ($row) {
                    return view('backend.my_panel.my_certificates.components._action', ['certificate' => $row]);
                })
                ->rawColumns(['action', 'course', 'my_class', 'invoice'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function loggedInDevices()
    {
        try {
            return view('backend.my_panel.logged_in_device.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function loggedInDevicesDatatable(Request $request)
    {
        try {
            $data = UserLogin::query()->where('user_id', auth()->id())->where('status', 1);


            if ($request->f_date) {
                $data->whereBetween(DB::raw('DATE(login_at)'), formatDateRangeData($request->f_date));
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('login_at', function ($row) {
                    return showDate($row->login_at);
                })
                ->editColumn('logout_at', function ($row) {
                    return $row->logout_at ? showDate($row->logout_at) : trans('common.Active');
                })
                ->addColumn('action', function ($row) {
                    return view('backend.my_panel.logged_in_device.components._action', ['login' => $row]);
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function deposit(Request $request)
    {
        try {
            $data['methods'] = PaymentMethod::where('active_status', 1)->where('module_status', 1)->where('method', '!=', 'Wallet')->where('method', '!=', 'Offline Payment')->get(['method', 'logo']);
            $data['amount'] = isset($request->deposit_amount) ? $request->deposit_amount : null;
            return view('backend.my_panel.deposit.index', $data);
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function depositDatatable(Request $request)
    {
        try {
            $data = DepositRecord::query()->where('user_id', Auth::id());
            if ($request->f_date) {
                $data->whereBetween(DB::raw('DATE(created_at)'), formatDateRangeData($request->f_date));
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->editColumn('amount', function ($row) {
                    return showPrice($row->amount);
                })
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


    public function myTopics()
    {
        try {
            $categories = Category::where('status', 1)->orderBy('position_order', 'ASC');
            if (isModuleActive('OrgInstructorPolicy') && \auth()->user()->role_id != 1) {
                $assign = OrgPolicyCategory::where('policy_id', \auth()->user()->policy_id)->pluck('category_id')->toArray();
                $categories->whereIn('id', $assign);
            }
            $data['categories'] = $categories->with('parent')->get();
            return view('backend.my_panel.my_topics.index', $data);
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myTopicsDatatable(Request $request)
    {
        try {
            $data = $this->myEnrollmentRepo->myTopics($request->all());
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('topic_title', function ($row) {
                    $course = $row->course;
                    return "<a href=" . courseDetailsUrl($course->id, $course->type, $course->slug) . " > $course->title </a>";
                })
                ->addColumn('topic_type', function ($row) {
                    return $row->course->courseType();
                })
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->addColumn('curriculum', function ($row) {
                    $result = '';
                    if ($row->course->type == 1) {
                        $result = count($row->course->lessons) . ' ' . trans('student.Lessons');
                    } elseif ($row->course->type == 2) {
                        $result = count($row->course->quiz->assign) . ' ' . trans('student.Question');
                    } elseif ($row->course->type == 3) {
                        $result = $row->course->class->total_class . ' ' . trans('student.Classes');
                    }
                    return $result;
                })
                ->addColumn('action', function ($row) {
                    return view('backend.my_panel.my_topics.components._action', ['course' => $row->course]);
                })
                ->rawColumns(['action', 'topic_title'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }


}
