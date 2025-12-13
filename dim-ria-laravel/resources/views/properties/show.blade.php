@extends('layouts.app')

@section('title', $property->title . ' - DIM.RIA')

@section('content')
<div class="container">
    <h1>{{ $property->title }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Опис</h5>
            <p class="card-text">{{ $property->description }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Основна інформація</h5>
                    <p><strong>Ціна:</strong> {{ number_format($property->price, 0, ',', ' ') }} грн</p>
                    <p><strong>Адреса:</strong> {{ $property->address }}</p>
                    <p><strong>Кімнат:</strong> {{ $property->rooms }}</p>
                    <p><strong>Площа:</strong> {{ $property->area }} м²</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Додаткова інформація</h5>
                    <p><strong>Тип:</strong> 
                        <span class="badge bg-info">
                            @if($property->type === 'apartment') Квартира
                            @elseif($property->type === 'house') Будинок
                            @else Комерційна
                            @endif
                        </span>
                    </p>
                    <p><strong>Статус:</strong> 
                        <span class="badge bg-success">
                            @if($property->status === 'active') Активна
                            @elseif($property->status === 'sold') Продана
                            @else Орендована
                            @endif
                        </span>
                    </p>
                    <p><strong>Створено:</strong> {{ $property->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Оновлено:</strong> {{ $property->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('properties.index') }}" class="btn btn-secondary">
            Назад до списку
        </a>
        <a href="{{ route('properties.edit', $property) }}" class="btn btn-warning">
            Редагувати
        </a>
        <form action="{{ route('properties.destroy', $property) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Ви впевнені?')">
                Видалити
            </button>
        </form>
    </div>
</div>
@endsection

