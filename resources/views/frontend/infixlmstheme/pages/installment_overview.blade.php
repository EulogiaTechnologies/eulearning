@extends(theme('layouts.master'))
@section('title')
    {{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{_trans('installment.Installment Overview')}}
@endsection
@section('css')
    <link href="{{asset('public/frontend/infixlmstheme/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/checkout.css')}}" rel="stylesheet"/>
@endsection
@section('mainContent')
    <x-installment-overview-page-section :data="$data"/>

@endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/select2.min.js')}}"></script>
    <script src="{{asset('public/frontend/infixlmstheme/js/checkout.js')}}"></script>
    <script src="{{asset('public/frontend/infixlmstheme/js/city.js')}}"></script>
@endsection
