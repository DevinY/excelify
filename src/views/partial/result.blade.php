  @if(isset($data))
    @if(session('tabnum')==1)
    <form action="/download" method="post" target="_blank">
        {{csrf_field()}}
        <input type="hidden" name="filename" value="{{$tablename}}">
        <textarea class="form-control" name="data" style="width:100%;height:300px">
          {{-- array 格式 --}}
          @include("excelify::data_templates.$datatype")
      </textarea>
      <button type="submit" class="btn btn-default btn-sm">@lang('excelify::message.Download as text file')</button>
    </form>
    @endif
    
    @if(session('tabnum')==2)
      @include("excelify::data_templates.$datatype")
    @endif
  @endif