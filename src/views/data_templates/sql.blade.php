INSERT INTO {{$tablename}}
(@foreach($data[0] as $key=>$item) `{{$key}}` @if(!$loop->last),@endif @endforeach)
VALUES
@foreach($data as $item)
    (@foreach($item as $key=>$value) "{{$value}}" @if(!$loop->last),@endif @endforeach) @if (!$loop->last),@endif  
@endforeach;