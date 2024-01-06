<div>
    <div class="main_content_iner main_content_padding">
        <div class="dashboard_lg_card">
            <div class="container-fluid no-gutters">
                <div class="my_courses_wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="section__title3 margin-50">
                                <h3>
                                    {{ __("blog.My Blogs") }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 mb-4">
                        <div class="col d-flex justify-content-end">
                            <a class="link_value theme_btn small_btn4" href="{{ route('users.blog.create') }}">
                                {{ __('blog.Add Post') }}
                            </a>
                        </div>
                    </div>

                    <div class="row d-flex align-items-center mb-4 mb-lg-5">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table custom_table3">
                                    <thead>
                                    <tr>
                                        <th>{{__('common.SL')}}</th>
                                        <th>{{__('common.Title')}}</th>
                                        <th>{{__('common.Image')}}</th>
                                        <th>{{__('blog.Published Date')}}</th>
                                        <th>{{__('common.Status')}}</th>
                                        <th>{{__('common.Action')}}</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @if(count($blogs) > 0)
                                        @foreach($blogs as $key => $blog)
                                            <tr>
                                                <td>{{$key + 1}}</td>
                                                <td>{{ Str::limit($blog->title,50) }}</td>
                                                <td>
                                                    <img class="img-fluid blog-img"
                                                         src="{{ getBlogImage($blog->image) }}">
                                                </td>
                                                <td>
                                                    {{ $blog->authored_date }}
                                                </td>
                                                <td>
                                                    @if($blog->status == '1')
                                                        <span
                                                            class="badge badge-success">{{ __("blog.Published") }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-secondary">{{ __("blog.Pending") }}</span>
                                                    @endif

                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                            {{ __("blog.Action") }}
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item"
                                                               href="{{ route('users.blogs.edit',$blog->id) }}">{{__("common.Edit")}}</a>
                                                            <a class="dropdown-item"
                                                               href="{{ route('users.blogs.delete',$blog->id) }}">{{__("common.Delete")}}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan='6' class='text-center'>
                                                {{__('blog.Blog is not published yet')}}
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
