@extends('layouts.admin')
@section('title','Dotations')
@section('content')
<h1>Dotations</h1>
<a href="{{ route('admin.dotations.create') }}" class="btn">Ajouter Dotation</a>
<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Title</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($dotations as $dot)
        <tr>
            <td>{{ $dot->id }}</td>
            <td>{{ $dot->title }}</td>
            <td>{{ $dot->dotationdate }}</td>
            <td>
                <a href="{{ route('admin.dotations.edit',$dot) }}" class="btn">Edit</a>
                <form action="{{ route('admin.dotations.delete',$dot) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
