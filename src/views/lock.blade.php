@extends('excelify::layout.master')

@section("content")
    <form action="/unlock" method="POST">
        {{csrf_field()}}
        <input type="password" name="secret" class="form-control" placeholder="secret">
        <button type="submit" class="btn btn-default">Ulock</button>
    </form>
@endsection
