@extends('layouts.layout')

@section('title', 'Hakkımızda Bölümü Düzenle')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="title">
                <h4>Hakkımızda Bölümü Düzenle - {{ $section->section_key }}</h4>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <form action="{{ route('admin.about.update', $section->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Başlık</label>
                        <input type="text" name="title" value="{{ old('title', $section->title) }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>İçerik</label>
                        <textarea name="content" class="form-control" rows="5">{{ old('content', $section->content) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Resim</label><br>
                        @if($section->image)
                            <img src="{{ asset('storage/' . $section->image) }}" alt="Image" width="100" class="mb-2"><br>
                        @endif
                        <input type="file" name="image" class="form-control-file">
                    </div>

                    <button type="submit" class="btn btn-success">Bölümü Güncelle</button>
                    <a href="{{ route('admin.about.index') }}" class="btn btn-secondary">Geri</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection