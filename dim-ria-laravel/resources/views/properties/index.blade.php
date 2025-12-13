@extends('layouts.app')

@section('title', 'Каталог нерухомості - DIM.RIA')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Каталог нерухомості</h1>
        <a href="{{ route('properties.create') }}" class="btn btn-primary">Додати нерухомість</a>
    </div>
    
    <div class="row">
        @forelse($properties as $property)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $property->title }}</h5>
                        <p class="card-text">{{ Str::limit($property->description, 100) }}</p>
                        <p class="card-text">
                            <strong>Ціна:</strong> {{ number_format($property->price, 0, ',', ' ') }} грн
                        </p>
                        <p class="card-text">
                            <strong>Адреса:</strong> {{ $property->address }}
                        </p>
                        <p class="card-text">
                            <strong>Кімнат:</strong> {{ $property->rooms }} | 
                            <strong>Площа:</strong> {{ $property->area }} м²
                        </p>
                        <p class="card-text">
                            <span class="badge bg-info">{{ $property->type }}</span>
                            <span class="badge bg-success">{{ $property->status }}</span>
                        </p>
                        <a href="{{ route('properties.show', $property) }}" class="btn btn-primary">
                            Детальніше
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Нерухомість не знайдена. <a href="{{ route('properties.create') }}">Додати першу нерухомість</a>
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $properties->links() }}
    </div>
</div>
@endsection

