<div class="dropdown CRM_dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button"
            id="dropdownMenu1"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        {{ __('common.Select') }}
    </button>
    <div class="dropdown-menu dropdown-menu-right"
         aria-labelledby="dropdownMenu1">
        <a target="_blank" class="dropdown-item" href="{{route('invoice',$row->id)}}">{{__('common.View')}}</a>
    </div>
</div>
