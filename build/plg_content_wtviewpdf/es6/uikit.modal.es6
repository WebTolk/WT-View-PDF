document.addEventListener('DOMContentLoaded', () => {

    let wtviewpdfLastClickedButton = null;

    document.querySelectorAll('[uk-toggle="target: #modal-wtviewpdf"]').forEach(btn => {
        btn.addEventListener('mousedown', function() {
            wtviewpdfLastClickedButton = this;
        });
    });

    const project_pdf_modal = document.getElementById('modal-wtviewpdf');

    if (project_pdf_modal) {
        UIkit.util.on('#modal-wtviewpdf', 'show', function(event) {
            const button = wtviewpdfLastClickedButton;
            const file_url = button.getAttribute('data-file-url');

            const pdfContainer= project_pdf_modal.querySelector('.uk-modal-body > .pdf-container');
            let modalLoader= document.getElementById('modal-loader');
            showLoader(modalLoader);

            var {pdfjsLib} = globalThis;
            let url= window.location.origin + '/' + file_url;

            let scale = 2;

            async function loadPDF(pdfUrl) {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                const pdf = await loadingTask.promise;

                const canvases = [];

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({ scale: 2 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.setAttribute('data-page-num', i);
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    await page.render(renderContext).promise;
                    canvases.push(canvas); // Добавляем канвас в массив по порядку
                }

                return canvases;
            }

            // Использование функции
            loadPDF(url).then(canvases => {
                // canvases теперь содержит все страницы в правильном порядке
                canvases.forEach((canvas, index) => {
                    pdfContainer.appendChild(canvas);
                    console.log(`Page ${index + 1} appended.`);
                });
            }).catch(error => {
                console.error('Error loading PDF: ', error);
            }).then(() => {
                hideLoader(modalLoader);
                initProgressBar();
            });

        });// if wt quick lnks

        UIkit.util.on('#modal-wtviewpdf', 'show', function(event) {
            const pdfContainer = project_pdf_modal.querySelector('.uk-modal-body > .pdf-container');
            pdfContainer.innerHTML = '';
        });

        function showLoader(modalLoader) {
            console.log('showLoader');
            console.log(modalLoader);
            modalLoader.classList.remove('uk-hidden');
            modalLoader.classList.add('uk-flex');

        }

        function hideLoader(modalLoader) {
            console.log('hideLoader');
            modalLoader.classList.add('uk-hidden');
            modalLoader.classList.remove('uk-flex');

        }

        function initProgressBar() {
            const pdfContainer = project_pdf_modal.querySelector('.uk-modal-body > .pdf-container');
            const progressBar = project_pdf_modal.querySelector('.uk-progress');
            const modalBody = project_pdf_modal.querySelector('.uk-modal-body');
            let fulloffsetHeight = pdfContainer.offsetHeight - modalBody.offsetHeight;

            progressBar.setAttribute('value', 0);
            let currentProgress = 0;

            modalBody.addEventListener('scroll', () => {
                currentProgress = (modalBody.scrollTop * 100) / fulloffsetHeight;
                currentProgress = Math.min(100, Math.max(0, currentProgress)); // Ограничиваем значения 0-100

                progressBar.setAttribute('value', currentProgress.toFixed(1));
            });
        }
    }

});