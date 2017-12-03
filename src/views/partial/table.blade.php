         <table class="table table-hover">
            <tbody>
                <tr>
                    <th colspan="2">
                    @lang('excelify::message.help')
                 </th>
             </tr>
             <tr>
                <td class="text-nowrap">
                    <label for="">@lang('excelify::message.select_excel_file')</label>
                </td>
                <td>
                    <input type="file" name="excelfile" value="">
                    <label class="text-danger"> {{$error_msg('excelfile')}} </label>
                </td>
            </tr>

            <tr>
                <td class="form-inline" colspan="2" >
                    Sheet:
                    @if(request()->has('sheetnum'))
                    <input type="number" class="form-control" name="sheetnum" placeholder="Sheet" value="{{request()->old("sheetnum")}}">
                    @else
                    <input type="number" class="form-control" name="sheetnum" placeholder="Sheet" value="1">
                    @endif
                    <label class="text-danger"> {{$error_msg('sheetnum')}} </label>
                </td>
            </tr>

            <tr class="text-nowrap">
                <td>
                    @lang('excelify::message.select_a_range')
                </td>
                <td class="form-inline">
                    @lang('excelify::message.start'):
                    @if(request()->has('start'))
                    <input type="text" class="form-control" name="start" placeholder="{{__('excelify::message.example_a')}}" value="{{request()->old("start")}}">
                    @else
                    <input type="text" class="form-control" name="start" placeholder="{{__('excelify::message.example_a')}}" value="a1">
                    @endif
                    @lang('excelify::message.end'):
                    <input type="text" class="form-control" name="end" placeholder="{{__('excelify::message.example_b')}}" value="{{request()->old('end')}}">
                    @if($error_msg('start'))
                    <Br/>
                    <label class="text-danger"> {{$error_msg('start')}} </label>
                    @endif
                </td>
            </tr>
            @include('excelify::partial.datatype')
            <tr>
                <td class="form-inline" >
                    @lang('excelify::message.table_name')
                </td>
                <td>
                    <input type="text" name="tablename" value="{{request()->old('tablename')}}" placeholder="tableName">
                    <button type="submit" class="btn btn-warning">
                        @lang('excelify::message.convert')
                    </button>
                    <br/>
                    <label class="text-danger">
                        {{$error_msg('tablename')}}
                    </label>
                </td>
            </tr>


        </tbody>
    </table> 