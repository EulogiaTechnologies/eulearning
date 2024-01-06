<?php

namespace Modules\FrontendManage\Http\Controllers;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FrontendManage\Repositories\TopicCommentRepository;
use Yajra\DataTables\Facades\DataTables;
use Modules\CourseSetting\Entities\Course;

class TopicCommentController extends Controller
{
    protected $topicCommentRepository;

    public function __construct(TopicCommentRepository $topicCommentRepository)
    {
        $this->middleware('auth');
        $this->middleware('RoutePermissionCheck:topics.comments.index', ['only' => ['index', 'datatable']]);
        $this->middleware('RoutePermissionCheck:topics.comments.reply', ['only' => ['reply']]);
        $this->middleware('RoutePermissionCheck:topics.comments.destroy', ['only' => ['destroy']]);
        $this->topicCommentRepository = $topicCommentRepository;
    }


    public function index()
    {
        try {
            $data['users'] = User::where('is_active', 1)->get();
            $data['courses'] = Course::where('status', 1)->get();
            return view('frontendmanage::topic_comments.index', $data);

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function reply(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'id' => 'required',
            'table' => 'required',
        ]);
        try {
            $msg = trans('blog.Replied') . ' ' . trans('lang.Successfully');
            $this->topicCommentRepository->reply($request->all());
            return response()->json(['msg' => $msg], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }

    public function datatable(Request $request)
    {

        try {

            $data = $this->topicCommentRepository->query($request->all());

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDate($row->created_at);
                })
                ->editColumn('course_title', function ($row) {
                    return view('frontendmanage::topic_comments.components._course', ['row' => $row]);
                })
                ->addColumn('action', function ($row) {
                    return view('frontendmanage::topic_comments.components._action', ['row' => $row]);
                })
                ->rawColumns(['action', 'course_title'])
                ->toJson();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Error!!');
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
        $request->validate([
            'id' => 'required',
            'source_table' => 'required'

        ]);

        try {
            $success = trans('lang.Deleted') . ' ' . trans('lang.Successfully');
            $this->topicCommentRepository->delete($request->id, $request->source_table);
            Toastr::success($success, 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
