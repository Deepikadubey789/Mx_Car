(function($) {
    'use strict';

    $.fn.locationAutocomplete = function() {
        return this.each(function() {
            const $container = $(this);
            const $input = $container.find('.location-autocomplete');
            const $suggestionsContainer = $container.find('[data-bb-toggle="data-suggestion"]');

            // Support both advanced search and filter forms
            let $cityIdHidden = $container.find('input[name="city_id"]');
            if (!$cityIdHidden.length) {
                $cityIdHidden = $('#city_id_hidden');
            }

            // Check if this is a filter form (has submit-form-filter class)
            const isFilterForm = $input.hasClass('submit-form-filter');

            if (!$input.length || !$suggestionsContainer.length) {
                return;
            }

            let searchTimeout;
            let currentIndex = -1;
            let isSubmitting = false;

            $input.on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();
                const url = $input.data('url');

                if (searchTerm.length < 1) {
                    $suggestionsContainer.hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { location: searchTerm },
                        dataType: 'json',
                        success: function(response) {
                            if (response.data && response.data.html) {
                                $suggestionsContainer.html(response.data.html);
                                $suggestionsContainer.show();
                                currentIndex = -1;

                                $suggestionsContainer.find('.search-suggestion-item').on('click', function(evt) {
                                    evt.preventDefault();
                                    evt.stopPropagation();

                                    if (isSubmitting) {
                                        return;
                                    }

                                    const cityId = $(this).data('value');
                                    const cityName = $(this).text().trim();

                                    $input.val(cityName);
                                    $cityIdHidden.val(cityId || '');
                                    $suggestionsContainer.hide();

                                    // If this is a filter form, trigger form submission (only once)
                                    if (isFilterForm && !isSubmitting) {
                                        isSubmitting = true;

                                        // Delay for Safari iOS compatibility - DOM needs time to update
                                        setTimeout(function() {
                                            const formId = $input.attr('form');
                                            if (formId) {
                                                const $form = $('#' + formId);
                                                if ($form.length) {
                                                    $form.trigger('submit');
                                                }
                                            }
                                            isSubmitting = false;
                                        }, 300);
                                    }
                                });
                            } else {
                                $suggestionsContainer.hide();
                            }
                        },
                        error: function() {
                            $suggestionsContainer.hide();
                        }
                    });
                }, 300);
            });

            $input.on('focus', function() {
                if ($suggestionsContainer.html().trim() && $(this).val().length >= 1) {
                    $suggestionsContainer.show();
                }
            });

            $input.on('keydown', function(e) {
                const $items = $suggestionsContainer.find('.search-suggestion-item');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (currentIndex < $items.length - 1) {
                        currentIndex++;
                        updateActiveItem($items, currentIndex);
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateActiveItem($items, currentIndex);
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const $activeItem = $items.filter('.active');
                    if ($activeItem.length) {
                        $activeItem.trigger('click');
                    }
                } else if (e.key === 'Escape') {
                    $suggestionsContainer.hide();
                    currentIndex = -1;
                }
            });

            function updateActiveItem($items, index) {
                $items.removeClass('active');
                if (index >= 0 && index < $items.length) {
                    $items.eq(index).addClass('active');
                }
            }
        });
    };

    $(document).on('click', function(e) {
        if (!$(e.target).closest('[data-bb-toggle="search-suggestion"]').length) {
            $('[data-bb-toggle="data-suggestion"]').hide();
        }
    });

    $(document).ready(function() {
        $('[data-bb-toggle="search-suggestion"]').locationAutocomplete();
    });

})(jQuery);
