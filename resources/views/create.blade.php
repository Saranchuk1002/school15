@extends('layouts.main')
@section('content')
    <div>
        <form action="{{route('object.store')}}" method="post">
            @csrf
<div class="mb-3 row">
    <label for="title" class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-10">
        <input type="text" name="title" class="form-control" id="title">
    </div>
</div>
<div class="mb-3 row">
    <label for="content" class="col-sm-2 col-form-label">Content</label>
    <div class="col-sm-10">
        <textarea  name="content" class="form-control" id="content"></textarea>
    </div>
</div>
<div class="mb-3 row">
    <label for="image" class="col-sm-2 col-form-label">Image</label>
    <div class="col-sm-10">
        <input type="text" name = "image" class="form-control" id="image">
    </div>
</div>
<div class="col-auto">
    <button type="submit"  class="btn btn-primary mb-3">Confirm</button>
</div>
        </form>
</div>
@endsection
