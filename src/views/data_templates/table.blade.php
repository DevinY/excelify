<table class="table table-hover">
    <tbody>
        @foreach($data as $item)
        <tr> @foreach($item as $key=>$value) <td>{{$value}}</td> @endforeach </tr>  
        @endforeach
    </tbody>
</table>