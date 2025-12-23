@extends('layouts.admin')
@section('title','Dotations')
@section('content')
<h3 class="text-white text-center mb-4"><i class="fas fa-trophy"></i> Gestion des Dotations</h3>

<div class="text-center mb-4">
    <a href="{{ route('admin.dotations.create') }}" class="btn btn-danger">
        <i class="fas fa-plus"></i> Ajouter Dotation
    </a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Titre</th>
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
                    <a href="{{ route('admin.dotations.edit',$dot) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.dotations.delete',$dot) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection