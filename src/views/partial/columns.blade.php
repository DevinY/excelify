<br/>
<button type="button" class="btn btn-primary" @click="addColumn">@lang('excelify::message.add_column')</button>
@lang('excelify::message.mapping_excel_columns')
<p>
    <div class="columnslot">
        <div class="form-inline hide">
            <div class="row">
                <div class="col-xs-4">
                    <input type="text" class="form-control" name="fieldName[]" value="" placeholder="{{__('excelify::message.mapping_example_a')}}">
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control" name="fieldValue[]" value="" placeholder="{{__('excelify::message.mapping_example_b')}}">
                </div>
                <div class="col-xs-4 ">
                    <input type="text" class="hide" name="fieldKvMap[]" value="">
                    <div class="btn-group">
                        <a class="btn btn-primary" data-toggle="modal" href='#modal-arrayMap'>@lang('excelify::message.define')</a>
                        <button type="button" class="btn btn-danger btnDelete">@lang('excelify::message.delete')</button>
                    </div>
                </div>
            </div>
        </div>
        @if(request()->old('fieldName'))
        @foreach(request()->old('fieldName') as $row_index=>$field_name)
        @php
        if(is_null($field_name)) continue;
        @endphp
        <div class="form-inline">
            <div class="row">
                <div class="col-xs-4">
                    <input type="text" class="form-control" name="fieldName[]" value="{{$field_name}}" placeholder="{{__('excelify::message.mapping_example_a')}}">
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control" name="fieldValue[]" value="{{request()->old('fieldValue')[$row_index]}}" placeholder="{{__('excelify::message.mapping_example_b')}}">
                </div>
                <div class="col-xs-4">
                    <input type="text" class="hide" name="fieldKvMap[]" value="{{request()->old('fieldKvMap')[$row_index]}}">
                    <div class="btn-group">
                        <a class="btn btn-primary" data-toggle="modal" href='#modal-arrayMap'>@lang('excelify::message.define')</a>
                        <button type="button" class="btn btn-danger btnDelete">@lang('excelify::message.delete')</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
                    @if(isset($datatype)&&$datatype=='excel')
                    <a href="/download_excel" target="_blank">@lang('excelify::message.download')</a> 
                    @endif

    <div class="modal fade" id="modal-arrayMap">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">@lang('excelify::message.convert')</h4>
                </div>
                <div class="modal-body">
                <textarea name="" id="input" class="form-control" rows="4" placeholder='["Taipei"=>"1","Panchiao"=>"2"]' v-model="mapData"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('excelify::message.close')</button>
                    <button type="button" class="btn btn-primary" @click="addMap">@lang('excelify::message.confirm')</button>
                </div>
            </div>
        </div>
    </div>