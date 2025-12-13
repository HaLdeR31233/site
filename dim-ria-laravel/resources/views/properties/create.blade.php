@extends('layouts.app')

@section('title', 'Додати нерухомість - DIM.RIA')

@section('content')
<div class="container">
    <h1>Додати нову нерухомість</h1>
    
    <form action="{{ route('properties.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="title" class="form-label">Назва *</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                   id="title" name="title" value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Опис *</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Ціна (грн) *</label>
                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                       id="price" name="price" value="{{ old('price') }}" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Тип *</label>
                <select class="form-select @error('type') is-invalid @enderror" 
                        id="type" name="type" required>
                    <option value="">Оберіть тип</option>
                    <option value="apartment" {{ old('type') === 'apartment' ? 'selected' : '' }}>Квартира</option>
                    <option value="house" {{ old('type') === 'house' ? 'selected' : '' }}>Будинок</option>
                    <option value="commercial" {{ old('type') === 'commercial' ? 'selected' : '' }}>Комерційна</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Адреса *</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                   id="address" name="address" value="{{ old('address') }}" required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="rooms" class="form-label">Кількість кімнат *</label>
                <input type="number" class="form-control @error('rooms') is-invalid @enderror" 
                       id="rooms" name="rooms" value="{{ old('rooms') }}" min="1" required>
                @error('rooms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="area" class="form-label">Площа (м²) *</label>
                <input type="number" step="0.01" class="form-control @error('area') is-invalid @enderror" 
                       id="area" name="area" value="{{ old('area') }}" min="0" required>
                @error('area')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Зберегти</button>
            <a href="{{ route('properties.index') }}" class="btn btn-secondary">Скасувати</a>
        </div>
    </form>
</div>
@endsection

