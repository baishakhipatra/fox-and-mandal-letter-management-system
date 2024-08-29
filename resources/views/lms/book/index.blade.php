@extends('layouts.app')

@section('content')


<div class="container mt-2">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>Book
                            @can('create book')
                            <a href="{{ url('books/create') }}" class="btn btn-primary float-end">Add Book</a>
                            @endcan
                        </h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book UID</th>
                                    <th>Category</th>
                                    <th>Office</th>
                                    <th>Bookshelves</th>
                                    <th>Title</th>
                                    <th>Author Name</th>
                                    <th>Publisher Name</th>
                                    <th>Edition</th>
                                    <th>Quantity</th>
                                    <th>Page</th>
                                    <th width="40%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index=> $item)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->details }}</td>
                                    <td>
                                        @can('update book')
                                        <a href="{{ url('bookcategories/'.$item->id.'/edit') }}" class="btn btn-success">Edit</a>
                                        @endcan

                                        @can('delete book')
                                        <a href="{{ url('bookcategories/'.$item->id.'/delete') }}" class="btn btn-danger mx-2">Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $data->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection