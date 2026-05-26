var $ = window.$; 

var $fileUpload = $('#resumable-browse');
var $fileUploadDrop = $('#resumable-drop');
var $uploadList = $("#file-upload-list");
var $statusBox = $("#upload-status-box");
var $progressBar = $("#global-progress-bar");

if ($fileUpload.length > 0 && $fileUploadDrop.length > 0) {
    var resumable = new Resumable({
        chunkSize: 2 * 1024 * 1024, // 2MB
        simultaneousUploads: 3,
        testChunks: false,
        throttleProgressCallbacks: 1,
        target: $fileUpload.data('url'),
        query:{
            _token : $('input[name=_token]').val(),
            folderPath : 'api-upload'
        }
    });

    if (!resumable.support) {
        $('#resumable-error').show();
    } else {
        $fileUploadDrop.show();
        resumable.assignDrop($fileUploadDrop[0]); // Дроп-зона на весь блок
        resumable.assignBrowse($fileUpload[0]);

        // Событие добавления файла
        resumable.on('fileAdded', function (file) {
            $statusBox.show();
            $progressBar.css('width', '0%').addClass('progress-bar-animated');
            
            // Выводим имя файла с иконкой
            $uploadList.html(
                `<li class="resumable-file-${file.uniqueIdentifier} fw-medium text-dark d-flex justify-content-between">
                    <span><i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>${file.fileName}</span>
                    <span class="resumable-file-progress badge bg-primary">0%</span>
                </li>`
            );

            // Перехватываем имя папки строго в момент старта
            var currentFolder = $('#folder-input').val().trim();
            resumable.opts.query = {
                folderPath: currentFolder
            };
            
            resumable.upload();
        });

        // В процессе загрузки
        resumable.on('fileProgress', function (file) {
            var progressPercent = Math.floor(file.progress() * 100);
            
            // Обновляем циферки в бедже
            $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress').html(progressPercent + '%');
            // Двигаем Bootstrap прогресс-бар
            $progressBar.css('width', progressPercent + '%');
        });

        // Успешно загружено
        resumable.on('fileSuccess', function (file, message) {
            $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress')
                .removeClass('bg-primary').addClass('bg-success').html('Готово');
            $progressBar.removeClass('progress-bar-animated');
        });

        // Ошибка
        resumable.on('fileError', function (file, message) {
            $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress')
                .removeClass('bg-primary').addClass('bg-danger').html('Ошибка');
            $progressBar.removeClass('bg-success').addClass('bg-danger');
        });
    }
}