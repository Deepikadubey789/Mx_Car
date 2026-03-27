'use strict'

$(document).ready(() => {
    $(document).on('click', '#confirm-verify-email', function(e) {
        e.preventDefault()

        const $button = $(this)
        const $modal = $('#verify-email-modal')
        const verifyUrl = $button.data('url')

        if (!verifyUrl) {
            Botble.showError('Verify URL not found')
            return
        }

        $httpClient
            .make()
            .withButtonLoading($button)
            .post(verifyUrl)
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                $modal.modal('hide')

                setTimeout(() => {
                    window.location.reload()
                }, 1500)
            })
            .catch(({ data }) => {
                if (data && data.message) {
                    Botble.showError(data.message)
                } else {
                    Botble.showError('An error occurred. Please try again.')
                }
                $modal.modal('hide')
            })
    })

    $(document).on('click', '#confirm-resend-confirmation', function(e) {
        e.preventDefault()

        const $button = $(this)
        const $modal = $('#resend-confirmation-modal')
        const resendUrl = $button.data('url')

        if (!resendUrl) {
            Botble.showError('Resend URL not found')
            return
        }

        $httpClient
            .make()
            .withButtonLoading($button)
            .post(resendUrl)
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                $modal.modal('hide')
            })
            .catch(({ data }) => {
                if (data && data.message) {
                    Botble.showError(data.message)
                } else {
                    Botble.showError('An error occurred. Please try again.')
                }
                $modal.modal('hide')
            })
    })

    $(document).on('click', '#confirm-upgrade-to-vendor', function(e) {
        e.preventDefault()

        const $button = $(this)
        const $modal = $('#upgrade-to-vendor-modal')
        const upgradeUrl = $button.data('url')

        if (!upgradeUrl) {
            Botble.showError('Upgrade URL not found')
            return
        }

        $httpClient
            .make()
            .withButtonLoading($button)
            .post(upgradeUrl)
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                $modal.modal('hide')

                if (data.data && data.data.next_url) {
                    setTimeout(() => {
                        window.location.href = data.data.next_url
                    }, 1000)
                } else {
                    setTimeout(() => {
                        window.location.reload()
                    }, 1500)
                }
            })
            .catch(({ data }) => {
                if (data && data.message) {
                    Botble.showError(data.message)
                } else {
                    Botble.showError('An error occurred. Please try again.')
                }
                $modal.modal('hide')
            })
    })
})