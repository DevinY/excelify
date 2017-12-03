         <table class="table table-hover">
            <tbody>
                <tr>
                    <th colspan="2">
                    @lang('excelify::message.Convert html table to excel')
                 </th>
             </tr>

             <tr>
                <td class="text-nowrap col-xs-2">
                    <label for="">
                     @lang('excelify::message.rendertron url')
                    </label>
                </td>
                <td>
                 <input type="text" class="form-control" name="rendertron_url" value="{{config('excelify.rendertron_url')}}"> 
                </td>
            </tr>
             <tr>
                <td class="text-nowrap col-xs-2">
                    <label for="">
                     @lang('excelify::message.input url')
                    </label>
                </td>
                <td>
                    @if(request()->has('url'))
                    <input type="text" name="url" class="form-control"  placeholder="Sheet" value="{{request()->old("url")}}">
                        @else
                    <input type="text" name="url" class="form-control" value="" placeholder="http://example.org or <table><tr><td>test</td></tr></table>">
                    @endif
                    <label class="text-danger"> {{$error_msg('url')}} </label>
                </td>
            </tr>

            <tr>
                <td class="form-inline" colspan="2" >
                    <label>
                        Table:
                    </label>
                    @if(request()->has('tablenum'))
                    <input type="number" class="form-control" name="tablenum"  value="{{request()->old("tablenum")}}">
                    @else
                    <input type="number" class="form-control" name="tablenum"  placeholder="zero is no limit">
                    @endif
                </td>
            </tr>
            <tr>
                <td class="form-inline" >
                    @lang('excelify::message.table_name')
                </td>
                <td>
                    <input type="text" name="tablename" value="{{request()->old('tablename')}}" placeholder="tableName">
                    <button type="submit" class="btn btn-warning">
                        @lang('excelify::message.convert')
                    </button>
                    @if(isset($data)&&count($data))
                    <a href="/download_temp" target="_blank">@lang('excelify::message.download')</a> 
                    @endif

                    @if(request()->old('url')!=""&&isset($data)&&count($data)==0)
                    @lang('excelify::message.no_data')
                    @endif
                    <br/>
                    <label class="text-danger">
                        {{$error_msg('tablename')}}
                    </label>
                </td>
            </tr>

        </tbody>
    </table> 