@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card p-4 p-md-5">
            <h3 class="fw-bold mb-4 text-center">Загрузка файлов</h3>

            <div id="resumable-error" class="alert alert-danger text-center" style="display: none">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Браузер не поддерживает загрузку чанками.
            </div>

            @csrf <div class="mb-4">
                <label for="folder-input" class="form-label fw-semibold text-muted small text-uppercase">Целевая папка на Диске</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-folder-plus"></i></span>
                    <input type="text" name="folder" value="api-upload" id="folder-input" class="form-control" placeholder="Например: folder/subfolder">
                </div>
                <div class="form-text">Если папки нет, она создастся автоматически.</div>
            </div>

            <div id="resumable-drop" class="border border-2 border-dashed border-primary-subtle rounded-3 p-5 text-center bg-light position-relative" style="display: none; cursor: pointer;">
                <div class="py-3">
                    <i class="bi bi-cloud-arrow-up text-primary fs-1 mb-2 d-block"></i>
                    <p class="fw-medium mb-1">Перетащите файл сюда или нажмите для выбора</p>
                    <button id="resumable-browse" data-url="{{ url('/upload') }}" class="btn btn-primary px-4 mt-2">
                        Выбрать файл
                    </button>
                </div>
            </div>

            <div id="upload-status-box" class="mt-4" style="display: none;">
                <hr class="text-muted my-4">
                <h5 class="fw-bold text-muted small text-uppercase mb-3">Статус загрузки</h5>
                
                <ul id="file-upload-list" class="list-unstyled mb-3">
                    </ul>

                <div class="progress" style="height: 10px; border-radius: 6px;">
                    <div id="global-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection