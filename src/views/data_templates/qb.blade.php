DB::table('{{$tablename}}')->insert(
[
@foreach($data as $item)
    [ @foreach($item as $key=>$value) "{{$key}}"=>"{{$value}}" @if(!$loop->last),@endif @endforeach ]  @if (!$loop->last),@endif  
@endforeach
]
);