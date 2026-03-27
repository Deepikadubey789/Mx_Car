'use strict'

$(document).ready(() => {
    $(document).on('click', '#confirm-verify-button', function(e) {
        e.preventDefault()

        const $button = $(this)
        const $form = $('#verify-vendor-modal form')
        const $modal = $('#verify-vendor-modal')

        $httpClient
            .make()
            .withButtonLoading($button)
            .post($form.attr('action'), $form.serialize())
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                $modal.modal('hide')
                setTimeout(() => {
                    window.location.reload()
                }, 1000)
            })
            .catch(({ data }) => {
                if (data && data.message) {
                    Botble.showError(data.message)
                } else {
                    Botble.handleError(data)
                }
            })
    })

    $(document).on('click', '#confirm-unverify-button', function(e) {
        e.preventDefault()

        const $button = $(this)
        const $form = $('#unverify-vendor-modal form')
        const $modal = $('#unverify-vendor-modal')

        $httpClient
            .make()
            .withButtonLoading($button)
            .post($form.attr('action'), $form.serialize())
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                $modal.modal('hide')
                setTimeout(() => {
                    window.location.reload()
                }, 1000)
            })
            .catch(({ data }) => {
                if (data && data.message) {
                    Botble.showError(data.message)
                } else {
                    Botble.handleError(data)
                }
            })
    })
})