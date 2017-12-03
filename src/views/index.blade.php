@extends('excelify::layout.master')
@section('content')

@php

   $excelify_secret =  session('excelify_secret');

if (!request()->session()->has('tabnum')) {
    session(['tabnum' => '1']);
}

$error_message=[];
if(count($errors)) {
    foreach($errors->all() as $error){
        $temp  = explode('|', $error);
        $error_message[$temp[0]][]=$temp[1];
    }
}
$error_msg= function($input) use ( $error_message){
    if(array_key_exists($input ,$error_message)) {
        foreach($error_message[$input] as $error){
            return "$error";
        }
    }
};
function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

echo convert(memory_get_usage(true)); // 123 kb
//$showerrors('tablenaem');
@endphp
{{$excelify_secret or "NO"}}
@if(!empty(env("EXCELIFY_SECRET"))&&env("EXCELIFY_SECRET")!=$excelify_secret)
    @include("excelify::lock")
@endif

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="{{(session('tabnum')==1)?"active":""}}"><a href="#converter" aria-controls="converter" role="tab" data-toggle="tab">Converter</a></li>
    <li role="presentation" class="{{(session('tabnum')==2)?"active":""}}"><a href="#excelify" aria-controls="excelify" role="tab" data-toggle="tab">Excelify</a></li>
    <li role="presentation" class="{{(session('tabnum')==3)?"active":""}}"><a href="#api" aria-controls="excelify" role="tab" data-toggle="tab">API</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane {{(session('tabnum')==1)?"active":""}}" id="converter">
     <form action="/excel_reader" method="POST" role="form" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
            <div class=" col-md-7">
                {{-- 左方的表單 --}}
                @include('excelify::partial.table')
            </div>
            <div class="col-md-4 col-xs-9" id="app">
                {{-- 右方的表單 --}}
                @include('excelify::partial.columns')
            </div>
        </div>
    </form> 
</div>

<div role="tabpanel" class="tab-pane {{(session('tabnum')==2)?"active":""}}" id="excelify">
     <form action="/excelify" method="POST" role="form" enctype="multipart/form-data">
            {{csrf_field()}}
            @include('excelify::partial.excelify')
    </form> 
</div>
{{-- API說明 --}}
<div role="tabpanel" class="tab-pane {{(session('tabnum')==3)?"active":""}}" id="api">
<br/>
<table class="table table-hover">
    <thead>
        <tr>
            <th class="col-md-3">
                <label>
                    @lang("message.api_calls"):
                </label>
                <br>
                /api/excelify/[tablenum]?table=[URL]</th>
            <th>Url or HTML Content</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">
<a href="/api/excelify/2?table=https://zh.wikipedia.org/wiki/%E5%8A%A0%E6%8B%BF%E5%A4%A7%E5%9F%8E%E5%B8%82%E5%88%97%E8%A1%A8">
api/excelify/2?table=https://zh.wikipedia.org/wiki/%E5%8A%A0%E6%8B%BF%E5%A4%A7%E5%9F%8E%E5%B8%82%E5%88%97%E8%A1%A8
</a>
            </td>
        </tr>

        <tr>
            <td colsapne="2">
                <a href="/api/excelify?table=<table><tr><td>column1</td><td>test</td></tr></table>">
                    api/excelify?table=
                    @php
                    echo htmlentities("<table><tr><td>column1</td><td>test</td></tr></table>");
                    @endphp
                </a>
            </td>
        </tr>

        <tr>
            <td colsapne="2">
            <label>
            /api/excelify?name=[download_file_name]&table=[HTML TABLE]
            </label>
            <br/>
                <a href="/api/excelify?name=test&table=<table><tr><td>column1</td><td>test</td></tr></table>">
                    api/excelify?name=test&table=
                    @php
                    echo htmlentities("<table><tr><td>column1</td><td>test</td></tr></table>");
                    @endphp
                </a>
            </td>
        </tr>

        <tr>
            <td colsapne="2">
                <label>
                @lang('excelify::message.remove_excelfiles_in_temp'):
                </label>
                <br/>
                <a href="/api/clean_excel_files" target="_blank">
                    api/clean_excel_files
                </a>
            </td>
        </tr>
    </tbody>
</table>

</div>
</div>


<div class="result">
    {{-- 結果 --}}
    @include('excelify::partial.result')
</div>
@endsection

@section('scripts')
@parent
<script>
    var currentButton;

    var vm = new Vue({
        el:'#app',
        data:{
            mapData:""
        },
        methods: {
            addColumn: function(){
                divColumn = $(".columnslot div:eq(0)").clone();
                divColumn.removeClass('hide');
                $("div.columnslot").append(divColumn);
            },
            addMap: function(){
                $(currentButton).closest("[name='fieldKvMap[]']").hide();
                $(currentButton).parent().prev().val(this.mapData);
                $("#modal-arrayMap").modal('hide');
       // name="fieldKvMap[]"
   } 
},
});
    $("#app").on('click','button.btnDelete', function(){
        $(this).closest('div.form-inline').remove();
    });

    $("#modal-arrayMap").on('show.bs.modal', function(event){
        currentButton = event.relatedTarget;
        vm.mapData = $(currentButton).parent().prev().val();

    });
</script>
@endsection
