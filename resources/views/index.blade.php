@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0 fs-4">Файлы на Яндекс.Диске</h2>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    @forelse ($items as $item)
        @php $item = $item->toObject() @endphp
        <div class="col">
            <div class="card h-100 p-2 position-relative">
                <div class="ratio ratio-16x9 bg-light rounded overflow-hidden mb-2 d-flex align-items-center justify-content-center">
                    @if (isset($item->preview))
                        <img src="{{ $item->preview }}" class="object-fit-cover" alt="{{ $item->name }}">
                    @else
                        <div class="text-secondary fs-1">
                            <i class="bi {{ isset($item->media_type) && $item->media_type === 'video' ? 'bi-file-earmark-play' : 'bi-file-earmark-zip' }}"></i>
                        </div>
                    @endif
                </div>

                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <p class="card-title fw-semibold text-truncate mb-3" title="{{ $item->name }}">
                        {{ $item->name }}
                    </p>
                    
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ $item->docviewer }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Просмотр">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ $item->file }}" class="btn btn-sm btn-outline-primary" title="Скачать">
                            <i class="bi bi-download"></i>
                        </a>
                        <form action="/delete" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этот файл?');" class="d-inline">
                            @csrf
                            <input type="hidden" name="path" value="{{ $item->path }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted fs-2 mb-2"><i class="bi bi-folder-x"></i></div>
            <p class="text-muted">В этой папке пока нет файлов.</p>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5">
    {{ $items->links('pagination::simple-bootstrap-5') }}
</div>
@endsection