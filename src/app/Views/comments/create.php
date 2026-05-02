<h2>Add new comment</h2>

<?= session()->getFlashdata('error') ?>
<?= validation_list_errors() ?>

<div id="form-errors" style="color: #b00020;"></div>

<form id="comment-form" action="/comments" method="post" class="mt-3" novalidate>
    <?= csrf_field() ?>

    <label for="author-email" class="form-label">Author email</label>
    <input type="email"
           class="form-control"
           id="author-email"
           name="name"
           value="<?= set_value('name') ?>"
           maxlength="128"
           autocomplete="email"
           inputmode="email"
           required>
    <br>

    <label for="comment-text" class="form-label">Text</label>
    <textarea id="comment-text"
              class="form-control"
              name="text"
              cols="45"
              rows="4"
              minlength="1"
              maxlength="5000"
              required><?= set_value('text') ?></textarea>
    <br>

    <input type="submit" name="submit" value="Create comment" class="btn btn-primary">
</form>

<div id="comments-loading-overlay"
     class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
     style="z-index: 2000; background-color: rgba(0, 0, 0, 0.45);"
     aria-hidden="true"
     role="presentation">
    <div class="bg-white rounded shadow p-4 text-center" style="min-width: 240px;">
        <div class="spinner-border text-primary" role="status" aria-label="Загрузка"></div>
        <p class="mt-3 mb-0 small text-secondary" id="comments-loading-message">Подождите…</p>
    </div>
</div>

<script>
$(function () {
    const $form = $('#comment-form');
    const $errorsBox = $('#form-errors');
    const $list = $('#comments-list');

    if ($form.length === 0 || $list.length === 0) {
        return;
    }

    const escapeHtml = (value) => $('<div>').text(String(value)).html();
    const csrfInputName = '<?= esc(csrf_token(), 'js') ?>';

    const $email = $('#author-email');
    const $text = $('#comment-text');
    const $overlay = $('#comments-loading-overlay');
    const $loadingMessage = $('#comments-loading-message');

    function showCommentsLoading(message) {
        $loadingMessage.text(message);
        $overlay.attr('aria-hidden', 'false').removeClass('d-none').addClass('d-flex');
    }

    function hideCommentsLoading() {
        $overlay.attr('aria-hidden', 'true').addClass('d-none').removeClass('d-flex');
    }

    function applyCsrfFromDoc($root) {
        const $remoteInput = $root.find('#comment-form input[name="' + csrfInputName + '"]');

        if (!$remoteInput.length) {
            return;
        }

        $form.find('input[name="' + csrfInputName + '"]').val($remoteInput.first().val());
    }

    function syncCommentsBlock() {
        const url = window.location.pathname + window.location.search;

        return $.get(url).done(function (html) {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const $parsed = $(doc.body);
            const $next = $parsed.find('#comments-block');
            const $cur = $('#comments-block');

            if ($next.length && $cur.length) {
                $cur.replaceWith($next.clone(true));
            }

            applyCsrfFromDoc($parsed);
        });
    }

    function validateAuthorEmail(value) {
        const trimmed = String(value).trim();

        if (trimmed === '') {
            return 'Author email is required.';
        }

        if (trimmed.length > 128) {
            return 'Email must be at most 128 characters.';
        }

        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (! re.test(trimmed)) {
            return 'Enter a valid email address.';
        }

        return null;
    }

    function validateCommentText(value) {
        const trimmed = String(value).trim();

        if (trimmed.length < 1) {
            return 'Text must be at least 1 character.';
        }

        if (trimmed.length > 5000) {
            return 'Text must be at most 5000 characters.';
        }

        return null;
    }

    $form.on('submit', function (event) {
        event.preventDefault();
        $errorsBox.empty();

        const emailError = validateAuthorEmail($email.val());
        const textError = validateCommentText($text.val());
        const clientErrors = [];

        if (emailError !== null) {
            clientErrors.push(emailError);
        }

        if (textError !== null) {
            clientErrors.push(textError);
        }

        if (clientErrors.length > 0) {
            const items = clientErrors.map((error) => `<li>${escapeHtml(error)}</li>`).join('');
            $errorsBox.html(`<ul>${items}</ul>`);
            return;
        }

        showCommentsLoading('Сохраняем комментарий…');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .done(function () {
                $loadingMessage.text('Обновляем список…');
                return syncCommentsBlock().done(function () {
                    $form[0].reset();
                });
            })
            .fail(function (xhr) {
                const payload = xhr.responseJSON || {};
                const errors = payload.errors || {};
                const items = Object.values(errors)
                    .map((error) => `<li>${escapeHtml(error)}</li>`)
                    .join('');

                $errorsBox.html(`<ul>${items}</ul>`);
            })
            .always(function () {
                hideCommentsLoading();
            });
    });

    $(document).on('submit', '.comment-delete-form', function (event) {
        event.preventDefault();

        const $deleteForm = $(this);

        showCommentsLoading('Удаляем комментарий…');

        $.ajax({
            url: $deleteForm.attr('action'),
            type: 'POST',
            data: $deleteForm.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .done(function () {
                $loadingMessage.text('Обновляем список…');
                return syncCommentsBlock();
            })
            .always(function () {
                hideCommentsLoading();
            });
    });
});
</script>