<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Blog\Entities\Blog;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Blog\Entities\BlogCategory;

class UserBlogController extends Controller
{
    use ImageStore;

    public function index()
    {
        try {
            return view(theme('pages.my_blogs'));
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function create()
    {
        try {
            $categories = BlogCategory::where('status', 1)->get();
            return view(theme('pages.my_blog_create'), compact('categories'));
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function store(Request $request)
    {
//        dd($request->all());

        if (saasPlanCheck('blog_post')) {
            Toastr::error('You have reached blog post limit', trans('common.Failed'));
            return redirect()->back();
        }
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            "title.en" => "required",
            "slug" => "required",
            "category" => "required",
            "description" => "required",
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = new Blog;
            foreach ($request->title as $key => $name) {
                $blog->setTranslation('title', $key, $name);
            }
            foreach ($request->description as $key => $description) {
                $blog->setTranslation('description', $key, $description);
            }
            $blog->slug = $request->slug;
            $blog->category_id = $request->category;
            $blog->tags = $request->tags;
            $blog->user_id = Auth::id();

            if (Settings('blog_auto_approval') == 1) {
                $status = 1;
            } else {
                $status = 0;
            }

            $blog->status = $status;

            $blog->authored_date = !empty($request->publish_date) ? Carbon::createFromFormat('Y-m-d', $request->publish_date)->format('m/d/Y') : date('m/d/y');
            $blog->authored_time = !empty($request->publish_time) ? $request->publish_time : date('H:i:s');

            if ($request->hasFile('image')) {
                $blog->image = $this->saveImage($request->image);
                $blog->thumbnail = $this->saveImage($request->image);
            } else {
                $blog->image = '';
                $blog->thumbnail = '';
            }
            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('users.blog.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function show($id)
    {
        return view('blog::show');
    }


    public function edit($id)
    {
        try {
            $categories = BlogCategory::where('status', 1)->get();
            $blog = Blog::where('id', $id)->where('user_id', Auth::id())->first();
            if ($blog) {
                return view(theme('pages.my_blog_edit'), compact('categories', 'blog'));
            } else {
                Toastr::error(trans('blog.Blog not Found'), trans('common.Error'));
                return back();
            }

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function update(Request $request, $id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            "title.en" => "required",
            "slug" => "required",
            "category" => "required",
            "description" => "required",
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = Blog::where('id', $id)->where('user_id', Auth::id())->first();
            if ($blog) {
                foreach ($request->title as $key => $name) {
                    $blog->setTranslation('title', $key, $name);
                }
                foreach ($request->description as $key => $description) {
                    $blog->setTranslation('description', $key, $description);
                }
                $blog->slug = $request->slug;
                $blog->category_id = $request->category;
                $blog->tags = $request->tags;
                $blog->user_id = Auth::id();
                if (Settings('blog_auto_approval') == 1) {
                    $status = 1;
                } else {
                    $status = 0;
                }
                $blog->status = $status;
                $blog->authored_date = !empty($request->publish_date) ? getPhpDateFormat($request->publish_date) : date('m/d/y');
                $blog->authored_time = !empty($request->publish_time) ? $request->publish_time : date('H:i:s');
                if ($request->hasFile('image')) {
                    $blog->image = $this->saveImage($request->image);
                    $blog->thumbnail = $this->saveImage($request->image);
                } else {
                    $blog->image = $request->old_image;
                    $blog->thumbnail = $request->old_image;
                }
                $blog->save();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('users.blog.index');
            } else {
                Toastr::error(trans('blog.Blog not Found'), trans('common.Error'));
                return back();
            }


        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function delete($id)
    {
        try {
            $blog = Blog::where('id', $id)->where('user_id', Auth::id())->first();
            if ($blog) {
                $blog->delete();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return back();
            }
            Toastr::error(trans('blog.Blog not Found'), trans('common.Error'));
            return redirect()->route('users.blog.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
