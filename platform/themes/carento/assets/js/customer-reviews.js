(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle delete review button click
        $(document).on('click', '.delete-review-btn', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const reviewId = button.data('review-id');
            const carName = button.data('car-name');
            
            // Create confirmation modal HTML with theme-consistent styling
            const modalHtml = `
                <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center px-4 pb-4">
                                <div class="mb-4">
                                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                        ${window.reviewTranslations.trashIcon}
                                    </div>
                                </div>
                                <h5 class="heading-6 neutral-1000 mb-3">${window.reviewTranslations.deleteReview}</h5>
                                <p class="text-sm-medium neutral-400 mb-0">
                                    ${window.reviewTranslations.confirmDelete} <strong class="neutral-1000">${carName}</strong>?
                                </p>
                                <p class="text-sm neutral-400 mb-4">
                                    ${window.reviewTranslations.cannotUndo}
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                                        ${window.reviewTranslations.cancel}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" id="confirmDeleteBtn">
                                        ${window.reviewTranslations.trashIconSmall}
                                        ${window.reviewTranslations.deleteReview}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#deleteReviewModal').remove();
            
            // Append modal to body
            $('body').append(modalHtml);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('deleteReviewModal'));
            modal.show();
            
            // Handle confirm delete
            $('#confirmDeleteBtn').off('click').on('click', function() {
                const deleteBtn = $(this);
                deleteBtn.prop('disabled', true)
                    .html(`<span class="spinner-border spinner-border-sm me-1"></span>${window.reviewTranslations.deleting}`);
                
                $.ajax({
                    url: window.reviewTranslations.deleteUrl + '/' + reviewId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: window.reviewTranslations.csrfToken
                    },
                    success: function(response) {
                        if (response.error === false) {
                            // Hide modal first
                            modal.hide();
                            
                            // Show success message using Theme.showSuccess
                            if (typeof Theme !== 'undefined' && Theme.showSuccess) {
                                Theme.showSuccess(response.message || window.reviewTranslations.deleteSuccess);
                            } else if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                                Botble.showSuccess(response.message || window.reviewTranslations.deleteSuccess);
                            } else {
                                alert(response.message || window.reviewTranslations.deleteSuccess);
                            }
                            
                            // Remove the review card with animation
                            button.closest('.col-md-6').fadeOut(400, function() {
                                $(this).remove();
                                
                                // Check if no more reviews
                                if ($('.bb-customer-card').length === 0) {
                                    location.reload();
                                }
                            });
                        } else {
                            // Hide modal first
                            modal.hide();
                            
                            // Show error message
                            if (typeof Theme !== 'undefined' && Theme.showError) {
                                Theme.showError(response.message || window.reviewTranslations.deleteFailed);
                            } else if (typeof Botble !== 'undefined' && Botble.showError) {
                                Botble.showError(response.message || window.reviewTranslations.deleteFailed);
                            } else {
                                alert(response.message || window.reviewTranslations.deleteFailed);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Hide modal first
                        modal.hide();
                        
                        // Show error message
                        if (typeof Theme !== 'undefined' && Theme.showError) {
                            Theme.showError(window.reviewTranslations.deleteError);
                        } else if (typeof Botble !== 'undefined' && Botble.showError) {
                            Botble.showError(window.reviewTranslations.deleteError);
                        } else {
                            alert(window.reviewTranslations.deleteError);
                        }
                    }
                });
            });
            
            // Clean up modal on hide
            $('#deleteReviewModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        });
    });
})(jQuery);