<div class="tab-pane fade" id="identity_tab">
    <div class="row">
        <div class="col-12">

           <div class="d-flex justify-content-between align-items-center">
               <h3>{{__('profile.identity_and_documents')}}</h3>
           </div>
            <hr>
            <form action="{{route('users.document.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
               <div class="row">
                   <div class="col-lg-4">
                       <div class="primary_input mb-35">
                           <label class="primary_input_label"
                                  for="">{{__('profile.passport')}}</label>
                           <div class="primary_file_uploader">
                               <input class="primary-input" type="text" id="placeholderPassportName"
                                      placeholder="" readonly="">
                               <button class="primary_btn_2" type="button">
                                   <label class="primary_btn_2"
                                          for="passport">{{__('common.Browse')}} </label>
                                   <input type="file" class="d-none" name="passport" id="passport">
                               </button>
                           </div>
                       </div>
                   </div>
                   <div class="col-lg-4">
                       <img  id="passport_show" class="center image_preview p-1" src="{{$passport_document?showImage($passport_document->document): showImage()}}" alt="Passport">
                   </div>
               </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="primary_input mb-35">
                            <label class="primary_input_label"
                                   for="">{{__('profile.nid')}}</label>
                            <div class="primary_file_uploader">
                                <input class="primary-input" type="text" id="placeholderNidName"
                                       placeholder="" readonly="">
                                <button class="primary_btn_2" type="button">
                                    <label class="primary_btn_2"
                                           for="nid">{{__('common.Browse')}} </label>
                                    <input type="file" class="d-none" name="nid" id="nid">
                                </button>
                            </div>
                        </div>


                    </div>

                    <div class="col-lg-4">
                        <img  id="nid_show" class="center image_preview p-1" src="{{$nid_document?showImage($nid_document->document): showImage()}}" alt="NID">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 d-flex align-items-center">
                        <div class="">
                            <h4 class="text-nowrap">{{__('profile.certificates_or_other_documents')}}</h4>
                        </div>
                        <div class="custom-hr">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @foreach($others_documents as $document)
                        <div class="row">
                            <div class="col-lg-3">
                                <input type="hidden" name="ex_ids[]" value="{{$document->id}}" >
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="name"> {{__('profile.document_name')}}</label>
                                    <input value="{{$document->name}}" name="ex_document_name[{{$document->id}}]"  class="primary_input_field document_name"  placeholder="-" type="text">
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                           for="">{{__('profile.document')}}</label>
                                    <div class="primary_file_uploader">
                                        <input data-id="{{$document->id}}" class="primary-input ex_placeholder_field" id="exPlaceholderDocumentName_{{$document->id}}" type="text"
                                               placeholder="" readonly="">
                                        <button class="primary_btn_2" type="button">
                                            <label class="primary_btn_2 ex_label_id"
                                                   for="ex_document_{{$document->id}}">{{__('common.Browse')}} </label>
                                            <input  accept="image/*" data-id="{{$document->id}}" type="file" class="d-none ex_file_input_field" name="ex_document_{{$document->id}}" id="ex_document_{{$document->id}}">
                                        </button>
                                    </div>
                                </div>


                            </div>

                            <div class="col-lg-3">
                                <img data-id="{{$document->id}}"  id="ex_document_show_{{$document->id}}" class="center image_preview ex_document_show p-2" src="{{showImage($document->document)}}" alt="Document">
                            </div>


                            <div class="col-lg-1">
                                <div class="position-relative form-group ">
                                    <a  href="{{route('users.document.destroy',$document->id)}}" data-repeater-delete class="primary-btn small icon-only fix-gr-bg mt-35  delete_item  mt-repeater-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-xl-12 mt-repeater no-extra-space">
                        <div data-repeater-list="other_documents">
                            <div data-repeater-item class="mt-repeater-item document_items">
                                <div class="mt-repeater-row document_item">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="name"> {{__('profile.document_name')}}</label>
                                                <input name="document_name"  class="primary_input_field document_name"  placeholder="-" type="text">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{__('profile.document')}}</label>
                                                <div class="primary_file_uploader">
                                                    <input data-id="1" class="primary-input placeholder_field" id="placeholderDocumentName_1" type="text"
                                                           placeholder="" readonly="">
                                                    <button class="primary_btn_2" type="button">
                                                        <label class="primary_btn_2 label_id"
                                                               for="document_1">{{__('common.Browse')}} </label>
                                                        <input data-id="1" type="file" class="d-none file_input_field" name="document" id="document_1">
                                                    </button>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="col-lg-3">
                                            <img data-id="1"  id="document_show_1" class="center image_preview document_show p-2" src="{{showImage()}}" alt="Document">
                                        </div>


                                        <div class="col-lg-1">
                                            <div class="position-relative form-group ">
                                                <a  href="javascript:void(0);" data-repeater-delete class="primary-btn small icon-only fix-gr-bg mt-35   mt-repeater-delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                <a href="javascript:void(0);" data-repeater-create class="primary-btn radius_30px  fix-gr-bg mt-repeater-add document_add "><i class="fa fa-plus"></i>{{__('common.Add More')}}</a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-12 text-right">
                        <hr class="d-block">
                        <button class="primary-btn fix-gr-bg" type="submit"><i
                                class="ti-check"></i> {{__('common.Save')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
