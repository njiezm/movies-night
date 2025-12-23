@extends('layouts.admin')
@section('title','{{ isset($dotation) ? 'Modifier' : 'Ajouter' }} une dotation')
@section('content')
<h3 class="text-white text-center mb-4"><i class="fas fa-trophy"></i> {{ isset($dotation) ? 'Modifier' : 'Ajouter' }} une dotation</h3>

<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ isset($dotation) ? route('admin.dotations.update',$dotation) : route('admin.dotations.store') }}" method="POST">
            @csrf
            @if(isset($dotation))
                @method('POST')
            @endif
            
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" class="form-control" name="title" value="{{ $dotation->title ?? '' }}" required>
            </div>
            
            <div class="form-group">
                <label for="dotationdate">Date</label>
                <input type="date" class="form-control" name="dotationdate" value="{{ $dotation->dotationdate ?? '' }}" required>
            </div>
            
            <div class="form-group text-center">
                <button type="submit" class="btn btn-danger">
                    {{ isset($dotation) ? 'Mettre à jour' : 'Ajouter' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection