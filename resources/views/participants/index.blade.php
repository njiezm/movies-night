@extends('layouts.app')
@section('content')
<h1>Participants</h1>
<a href="{{ route('participants.create') }}">Ajouter Participant</a>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Films vus</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($participants as $p)
        <tr>
            <td>{{ $p->firstname }} {{ $p->lastname }}</td>
            <td>{{ $p->email }}</td>
            <td>{{ $p->telephone }}</td>
            <td>{{ $p->films->count() }}</td>
            <td>
                <a href="{{ route('participants.edit', $p) }}">Edit</a>
                <form method="POST" action="{{ route('participants.destroy', $p) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button>Supprimer</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
