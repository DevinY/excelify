
            <tr>
                <td class="form-inline" >
                    @lang('excelify::message.output_type')
                </td>
                <td>
                    <label>
                        QueryBuilder:
                        @if(request()->has('datatype'))
                        <input type="radio" name="datatype" value="qb" {{request()->old('datatype')=='qb'?'checked':''}}>
                        @else
                        <input type="radio" name="datatype" value="qb" checked>
                        @endif
                    </label>
                    <label>
                        Json:
                        <input type="radio" name="datatype" value="json" {{request()->old('datatype')=='json'?'checked':''}}>
                    </label>
                    <label>
                        Array:
                        <input type="radio" name="datatype" value="array" {{request()->old('datatype')=='array'?'checked':''}}>
                    </label>
                    <label>
                        SQL:
                        <input type="radio" name="datatype" value="sql" {{request()->old('datatype')=='sql'?'checked':''}}>
                    </label>
                    <label>
                        Excel:
                        <input type="radio" name="datatype" value="excel" {{request()->old('datatype')=='excel'?'checked':''}}>
                    </label>
                </td>
            </tr>